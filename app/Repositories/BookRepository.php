<?php

namespace App\Repositories;

use App\Models\Book;
use App\Models\Category;
use App\Repositories\Interfaces\BookRepositoryInterface;
use Exception;
use App\Core\Constants;
use App\Models\BorrowBook;
use App\Models\User;

class BookRepository implements BookRepositoryInterface
{
    public function getBooks($request)
    {
        $books = Book::filter($request)->paginate(Constants::PAGINATION);
        return $books;
    }

    public function getBook($id)
    {
        $book = Book::find($id);
        return $book;
    }

    public function addBook($data)
    {
        $book = Book::create([
            'title' => $data['title'],
            'author' => $data['author'],
            'description' => $data['description'],
            'category_id' => $data['category_id'],
            'quantity' => $data['quantity'],
            'available' => $data['available'],
            'publisher' => $data['publisher'],
            'status' => $data['status']
        ]);

        return $book;
    }

    public function updateBook($data, $id)
    {
        $book = $this->getBook($id);

        if(!$book) {
            throw new Exception('Book not found');
        }

        $book->update([
            'title' => $data['title'],
            'author' => $data['author'],
            'description' => $data['description'],
            'category_id' => $data['category_id'],
            'quantity' => $data['quantity'],
            'available' => $data['available'],
            'publisher' => $data['publisher'],
            'status' => $data['status']
        ]);

        return $book;
    }

    public function reserveBook($data)
    {
        $reserveBook = BorrowBook::create([
            'user_id' => $data['user_id'],
            'book_id' => $data['book_id'],
            'reserve_date' => now(),
            'status' => Constants::RESERVED
        ]);

        return $reserveBook;
    }

    public function checkStatusAndBook($data)
    {
        $statusAndBook = BorrowBook::where('user_id', $data['user_id'])
            ->where('book_id', $data['book_id'])
            ->where('status', Constants::RESERVED)
            ->whereNull('cancel_reserve_date')
            ->first();

        return $statusAndBook;
    }

    public function cancelReservation($data)
    {
        $check = $this->checkStatusAndBook($data);

        if(!$check) {
            throw new Exception('Reservation not found');
        }

        $check->update([
            'cancel_reserve_date' => now(),
            'status' => Constants::CANCEL_RESERVED
        ]);

        return $check;
    }

    public function borrowHistories($status, $borrowDate, $memberId, $bookId, $title)
    {
        $query = Book::with([
            'borrowInfo' => function ($query) use ($status, $borrowDate, $memberId, $bookId) {
                $query->with(['user' => fn($q) => $q->select('id', 'name', 'email', 'phone', 'address')])
                    ->when(!is_null($status), function ($q) use ($status) {
                        $q->where('status', $status)
                        ->when($status === Constants::RESERVED, fn($q) => 
                            $q->whereNull('cancel_reserve_date')
                        );
                    })
                    ->when(!is_null($borrowDate), fn($q) => 
                        $q->whereDate('borrow_date', $borrowDate)
                    )
                    ->when(!is_null($memberId), fn($q) => 
                        $q->where('user_id', $memberId)
                    )
                    ->when(!is_null($bookId), fn($q) => 
                        $q->where('book_id', $bookId)
                    );
            },
            'category' => fn($q) => $q->select('id', 'name')
        ])
        ->whereHas('borrowInfo', function ($query) use ($status, $borrowDate, $memberId, $bookId) {
            $query->when(!is_null($status), function ($q) use ($status) {
                $q->where('status', $status)
                ->when($status === Constants::RESERVED, fn($q) => 
                    $q->whereNull('cancel_reserve_date')
                );
            })
            ->when(!is_null($borrowDate), fn($q) => 
                $q->whereDate('borrow_date', $borrowDate)
            )
            ->when(!is_null($memberId), fn($q) => 
                $q->where('user_id', $memberId)
            )
            ->when(!is_null($bookId), fn($q) => 
                $q->where('book_id', $bookId)
            );
        })
        ->select('id', 'title', 'author', 'quantity', 'category_id',  'available', 'publisher', 'status')
        ->where('title', 'like', "%{$title}%")
        ->paginate(Constants::PAGINATION);

        return $query;
    }

    public function changeBorrowStatus($data, $status)
    {
        $book = BorrowBook::where('book_id', $data['book_id'])
            ->where('user_id', $data['user_id'])
            ->whereIn('status', [Constants::RESERVED, Constants::BORROWED])
            ->first();

        if ($book) {
            if ($book->status == Constants::BORROWED && is_null($book->return_date)) {
                // If already borrowed, return false
                return "already_borrowed";
            }
            if ($book->status == Constants::RESERVED) {
                // Update reserved book to borrowed
                $book->update([
                    'status' => $status,
                    'borrow_date' => now(),
                    'due_date' => now()->addDays(5)
                ]);
                return "borrowed";
            }
        } else {
            BorrowBook::create([
                'user_id' => $data['user_id'],
                'book_id' => $data['book_id'],
                'status' => $status,
                'borrow_date' => now(),
                'due_date' => now()->addDays(5)
            ]);
            return "borrowed";
        }

        return "failed";


    }

    public function changeReturnStatus($data, $status)
    {
        $book = BorrowBook::where('book_id', $data['book_id'])
            ->where('user_id', $data['user_id'])
            ->where('status', Constants::BORROWED)
            ->whereNull('return_date')
            ->whereDate('borrow_date', $data['borrow_date'])
            ->first();

        if($book)
        {
            $book->update([
                'status' => $status,
                'return_date' => now()  
            ]);
            return true;
        }

        return false;
    }

}
