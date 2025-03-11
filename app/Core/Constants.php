<?php
namespace App\Core;

class Constants
{
    // book status
    const AVAILABLE = 1;
    const NOT_AVAILABLE = 2;

    // borrow status
    const BORROWED = 1;
    const RETURNED = 2;
    const RESERVED = 3;
    const CANCEL_RESERVED = 4;
    const OVERDUE = 5;

    // pagination
    const PAGINATION = 10;
}