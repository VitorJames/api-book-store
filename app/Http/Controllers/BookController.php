<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BookController extends Controller
{
    protected $user;

    public function __construct()
    {
        $this->user = auth()->user();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query = Book::query()->orderBy('id', 'asc');

        if ($request->has('search') && $request->search != "") {
            $query->where(function ($query) use ($request) {
                $query->where('name', 'LIKE' ,"%{$request->search}%")
                    ->orWhere('isbn', 'LIKE' ,"%{$request->search}%");
            });
        }

        if ($request->has('paginate')) {
            $data = $query->paginate($request->per_page);
        } else {
            $data = $query->get();
        }
        
        return response()->json($data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = $request->all();
        
        $data['isbn'] = $this->formatIsbn($data['isbn']);
        
        $validator = $this->validation($data);

        if (!$validator->fails()) {
            $book = Book::create([
                'name' => $data['name'],
                'isbn' => $data['isbn'],
                'value' => $data['value'],
                'user_id' => $this->user->id,
            ]);

            if ($book) {
                return response()->json([
                    "message" => "Book registered successfully."
                ], 200);
            } else {
                return response()->json([
                    "message" => "Error registering the book."
                ], 500);
            }
            
        } else {
            return response()->json($validator->errors());
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Book  $book
     * @return \Illuminate\Http\Response
     */
    public function show(Book $book)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Book  $book
     * @return \Illuminate\Http\Response
     */
    public function edit(Book $book)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Book  $book
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Book $book)
    {
        $data = $request->all();
        
        $data['isbn'] = $this->formatIsbn($data['isbn']);
        
        $validator = $this->validation($data);

        if (!$validator->fails()) {
            $book->update([
                'name' => $data['name'],
                'isbn' => $data['isbn'],
                'value' => $data['value'],
                'user_id' => $this->user->id,
            ]);

            if ($book) {
                return response()->json([
                    "message" => "Book updated successfully."
                ], 200);
            } else {
                return response()->json([
                    "message" => "Error updating the book."
                ], 500);
            }
            
        } else {
            return response()->json($validator->errors());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Book  $book
     * @return \Illuminate\Http\Response
     */
    public function destroy(Book $book)
    {
        $deleted = $book->delete();

        if ($deleted) {
            return response()->json([
                "message" => "Book deleted successfully."
            ], 200);
        }
        // return response()->json($book, 200);
    }

    public function formatIsbn($string)
    {
        $formated = str_replace('-', "", $string);

        $isbn = intval($formated);

        return $isbn;
    }

    private function validation($data) {
        return Validator::make($data, [
            'name' => 'required|max:255|string',
            'isbn' => 'required|numeric|digits:13|unique:books,isbn,'.$this->user->id.',user_id',
            'value' => 'required|numeric|between:0,99999',
        ]);
    }
}
