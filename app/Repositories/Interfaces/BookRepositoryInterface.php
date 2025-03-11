<?php

namespace App\Repositories\Interfaces;

interface BookRepositoryInterface
{
    public function getBooks($request);
    public function getBook($id);
    public function addBook($data);
    public function updateBook($data, $id);
    public function reserveBook($data);
    public function checkStatusAndBook($data);
    public function cancelReservation($data);
    public function borrowHistories($status, $borrow_date, $member_id, $book_id, $title);
    public function changeBorrowStatus($data, $status);
    public function changeReturnStatus($data, $status);

}