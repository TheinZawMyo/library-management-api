<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\BookService;
use Illuminate\Http\JsonResponse;
use Exception;
use Illuminate\Support\Facades\DB;
use App\Models\Book;

class BookController extends Controller
{
    public function __construct(private BookService $bookService)
    {}

    //======= get all books by category || author || title =======// pagination
    public function index(Request $request): JsonResponse
    {
        try {
            $books = $this->bookService->getBooks($request->all());
    
            return response()->json($books, 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    //======= get book by id =======//
    public function show($id): JsonResponse
    {
        try {
            $book = $this->bookService->getBook($id);
    
            return response()->json($book, 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => $e->getMessage()
            ], 500);
        }

    }


    //======== add book ========//
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'author' => 'required|string|max:255',
            'description' => 'required|string',
            'category_id' => 'required|integer|exists:categories,id',
            'quantity' => 'required|integer',
            'available' => 'required|integer|lte:quantity',
            'publisher' => 'required|string|max:255',
            'status' => 'required|integer|in:1,2'
        ]);

        try {
            DB::beginTransaction();
            $result = $this->bookService->addBook($request->all());
            DB::commit();

            return response()->json([
                'status' => 201,
                'message' => 'Book added successfully',
            ], 201);

        } catch (Exception $e) {

            DB::rollBack();
            return response()->json([
                'status' => 500,
                'message' => $e->getMessage()
            ], 500);
        }

    }

    //======= update book =======//
    public function update(Request $request, $id): JsonResponse
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'author' => 'required|string|max:255',
            'description' => 'required|string',
            'category_id' => 'required|integer|exists:categories,id',
            'quantity' => 'required|integer',
            'available' => 'required|integer|lte:quantity',
            'publisher' => 'required|string|max:255',
            'status' => 'required|integer|in:1,2'
        ]);

        try {
            DB::beginTransaction();
            $result = $this->bookService->updateBook($request->all(), $id);
            DB::commit();

            return response()->json([
                'status' => 200,
                'message' => 'Book updated successfully',
            ], 200);

        } catch (Exception $e) {

            DB::rollBack();
            return response()->json([
                'status' => 500,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    //======= delete book =======//
    public function destroy($id): JsonResponse
    {
        $book = $this->bookService->getBook($id);

        if (!$book) {
            return response()->json([
                'status' => 404,
                'message' => 'Book not found'
            ], 404);
        }

        $book->delete();

        return response()->json([
            'status' => 200,
            'message' => 'Book deleted successfully'
        ], 200);
    }


    // ============== Reserve Book =========//
    // ============== Reserve Book By Members =========//
    public function reserveBook(Request $request): JsonResponse
    {
        $request->validate([
            'user_id' => 'required|integer|exists:users,id',
            'book_id' => 'required|integer|exists:books,id',
        ]);

        try {
            DB::beginTransaction();
            $result = $this->bookService->reserveBook($request->all());
            DB::commit();

            if($result['status'] === 400) {
                return response()->json([
                    'status' => 400,
                    'message' => 'Book is not available'
                ], 400);
            }else {
                return response()->json([
                    'status' => 200,
                    'message' => 'Book reserved successfully',
                ], 200);
            }


        } catch (Exception $e) {

            DB::rollBack();
            return response()->json([
                'status' => 500,
                'message' => $e->getMessage()
            ], 500);

        }
    }


    // ============== Cancel Reservation =========//
    public function cancelReservation(Request $request): JsonResponse
    {
        $request->validate([
            'user_id' => 'required|integer|exists:users,id',
            'book_id' => 'required|integer|exists:books,id',
        ]);

        try {
            DB::beginTransaction();
            $result = $this->bookService->cancelReservation($request->all());
            DB::commit();

            $httpStatus = $result['status'] === 200 ? 200 : 400;
            return response()->json($result, $httpStatus);

        } catch (Exception $e) {

            DB::rollBack();
            return response()->json([
                'status' => 500,
                'message' => $e->getMessage()
            ], 500);

        }

    }

    // ============== Browse Books Histories =========//
    public function borrowHistories(Request $request): JsonResponse
    {
        try {
            $status = $request->status ?? null;
            $borrow_date = $request->borrow_date ?? null;
            $user_id = $request->user_id ?? null;
            $book_id = $request->book_id ?? null;
            $title = $request->title ?? null;
            $books = $this->bookService->borrowHistories($status, $borrow_date, $user_id, $book_id, $title);
            return response()->json([
                'status' => 200,
                'message' => 'Histories by books',
                'data' => $books
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    // ========= User borrow histories ====//
    public function userBorrowHistories(Request $request): JsonResponse
    {
        try {
            $status = $request->status ?? null;
            $borrow_date = $request->borrow_date ?? null;
            $user_id = auth()->user()->id;
            $book_id = $request->book_id ?? null;
            $title = $request->title ?? null;

            $books = $this->bookService->borrowHistories($status, $borrow_date, $user_id, $book_id, $title);

            return response()->json([
                'status' => 200,
                'message' => 'Histories for user',
                'data' => $books
            ], 200);

        } catch (Exception $e) {

            return response()->json([
                'status' => 500,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    // ============== Change Borrow Status =========//
    public function changeBorrowStatus(Request $request, $status): JsonResponse
    {
        $request->validate([
            'user_id' => 'required|integer|exists:users,id',
            'book_id' => 'required|integer|exists:books,id',
            'borrow_date' => 'required_if:status,2|date',
        ]);

        try {
            DB::beginTransaction();
            $result = $this->bookService->changeBorrowStatus($request->all(), $status);
            DB::commit();

            $httpStatus = $result['status'] === 200 ? 200 : 400;
            return response()->json($result, $httpStatus);

        } catch (Exception $e) {

            DB::rollBack(); 
            return response()->json([
                'status' => 500,
                'message' => $e->getMessage()
            ], 500);

        }

    }

    // ============== Update Book Status =========//
    public function updateBookStatus(Request $request, $id): JsonResponse
    {
        $request->validate([
            'status' => 'required|integer|in:1,2',
        ]);

        try {
            $book = Book::find($id);

            if(!$book) {
                return response()->json([
                    'status' => 404,
                    'message' => 'Book not found'
                ], 404);
            }

            $book->update([
                'status' => $request->status
            ]);

            return response()->json([
                'status' => 200,
                'message' => 'Book status updated successfully'
            ], 200);

        } catch (Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => $e->getMessage()
            ], 500);

        }

        
    }
}
