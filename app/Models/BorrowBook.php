<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\User;
use App\Models\Book;

class BorrowBook extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'user_id',
        'book_id',
        'borrow_date',
        'return_date',
        'due_date',
        'reserve_date',
        'cancel_reserve_date',
        'status'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }


    public function book()
    {
        return $this->belongsTo(Book::class, 'book_id');
    }

}

