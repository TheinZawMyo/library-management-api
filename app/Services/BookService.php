<?php

namespace App\Services;
use App\Repositories\Interfaces\BookRepositoryInterface;
use Exception;
use App\Core\Constants;

class BookService
{
    public function __construct(private BookRepositoryInterface $bookRepository)
    {}


    public function getBooks($request)
    {
        return $this->bookRepository->getBooks($request);
    }

    public function getBook($id)
    {
        return $this->bookRepository->getBook($id);
    }

    public function addBook($data)
    {
        return $this->bookRepository->addBook($data);
    }

    public function updateBook($data, $id)
    {
        return $this->bookRepository->updateBook($data, $id);
    }

    public function reserveBook($data)
    {
        $book = $this->bookRepository->getBook($data['book_id']);

        if(!$book) {
            throw new Exception('Book not found');
        }

        if($book->available < 1 || $book->status == Constants::NOT_AVAILABLE) {
            return [
                'status' => 400,
                'message' => 'Book is not available'
            ];
        }

        $reserveBook = $this->bookRepository->reserveBook($data);

        if(!$reserveBook) {
            throw new Exception('Failed to reserve book');
        }

        $book->update([
            'available' => $book->available - 1
        ]);

        if($book->available < 1) {
            $book->update([
                'status' => Constants::NOT_AVAILABLE
            ]);
        } else {
            $book->update([
                'status' => Constants::AVAILABLE
            ]);
        }

        return [
            'status' => 200,
            'message' => 'Book reserved successfully'
        ];
    }


    public function cancelReservation($data)
    {
        $book = $this->bookRepository->getBook($data['book_id']);

        if(!$book) {
            throw new Exception('Book not found');
        }

        $checkStatusAndBook = $this->bookRepository->checkStatusAndBook($data);

        if(!$checkStatusAndBook) {
            return [
                'status' => 400,
                'message' => 'Book is not reserved'
            ];
        }

        $cancelReservation = $this->bookRepository->cancelReservation($data);

        if(!$cancelReservation) {
            throw new Exception('Failed to cancel reservation');
        }

        if($book->available < $book->quantity) {
            $book->update([
                'available' => $book->available + 1
            ]);
        }

        if($book->available < 1) {
            $book->update([
                'status' => Constants::NOT_AVAILABLE
            ]);
        } else {
            $book->update([
                'status' => Constants::AVAILABLE
            ]);
        }

        return [
            'status' => 200,
            'message' => 'Reservation canceled successfully'
        ];
    }

    public function borrowHistories($status, $borrow_date, $member_id, $book_id, $title)
    {
        return $this->bookRepository->borrowHistories($status, $borrow_date, $member_id, $book_id, $title);
    }

    public function changeBorrowStatus($data, $status)
    {
        $book = $this->bookRepository->getBook($data['book_id']);

        if(!$book) {
            throw new Exception('Book not found');
        }

        if($status == Constants::BORROWED) 
        {
            if($book->available < 1 || $book->status == Constants::NOT_AVAILABLE) {
                return [
                    'status' => 400,
                    'message' => 'Book is not available'
                ];
            }

            $changeStatus = $this->bookRepository->changeBorrowStatus($data, $status); // if reserve or not_borrow => change borrow status

            if($changeStatus == 'already_borrowed') {
                return [
                    'status' => 400,
                    'message' => 'Already borrowed'
                ];

            }else if($changeStatus == 'failed') {
                return [
                    'status' => 400,
                    'message' => 'Failed to change status'
                ];
            }

            $book->update([
                'available' => $book->available - 1
            ]);
            
        } else if($status == Constants::RETURNED) {

            $changeStatus = $this->bookRepository->changeReturnStatus($data, $status); // if borrow => change return status

            if(!$changeStatus) {
                return [
                    'status' => 400,
                    'message' => 'Already Returned'
                ];
            }
            if($book->available < $book->quantity) {

                $book->update([
                    'available' => $book->available + 1
                ]);
            }
        } else if($status == Constants::OVERDUE) {

            $changeStatus = $this->bookRepository->changeReturnStatus($data, $status); // if borrow => change return status but overdue

            if(!$changeStatus) {
                return [
                    'status' => 400,
                    'message' => 'Failed to change status'
                ];
            }

            if($book->available < $book->quantity) {

                $book->update([
                    'available' => $book->available + 1
                ]);
            }
        }

        if($book->available < 1) {
            $book->update([
                'status' => Constants::NOT_AVAILABLE
            ]);
        } else {
            $book->update([
                'status' => Constants::AVAILABLE
            ]);
        }

        return [
            'status' => 200,
            'message' => 'Status changed successfully'
        ];

    }

}