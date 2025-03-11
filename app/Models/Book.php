<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\BorrowBook;

class Book extends Model
{
    use SoftDeletes, HasFactory;
    protected $fillable = [
        'title',
        'author',
        'description',
        'publisher',
        'category_id',
        'quantity',
        'available',
        'status'
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function scopeFilter($query, array $filters)
    {
        //=== filter by category ===//
        if(isset($filters['category_id']) && $filters['category_id'] != '') {
            $query->has('category', function($q) use ($filters) {
                $q->where('id', $filters['category_id']);
            });
        }
        
        //=== filter by status ===//
        if(isset($filters['status']) && $filters['status'] != '') {
            $query->where('status', $filters['status']);
        }

        //=== filter by title ===//
        if(isset($filters['title']) && $filters['title'] != '') {
            $query->where('title', 'like', '%'.$filters['title'].'%');
        }

        //=== filter by author ===//
        if(isset($filters['author']) && $filters['author'] != '') {
            $query->where('author', 'like', '%'.$filters['author'].'%');
        }
    }

    public function borrowInfo()
    {
        return $this->hasMany(BorrowBook::class, 'book_id');
    }

}
