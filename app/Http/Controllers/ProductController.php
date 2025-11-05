<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Illuminate\Validation\ValidationException;
use League\CommonMark\Extension\CommonMark\Node\Inline\Strong;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $products = [
            ['id' => 1 , 'name' => 'matooke', 'origin' => 'uganda'],
            ['id' => 2 , 'name' => 'rice', 'origin' => 'india'],
            ['id' => 3 , 'name' => 'beans', 'origin' => 'burundi'],
        ];
        return response()->json(['products' => $products], 200);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): \Inertia\Response
    {
        return inertia::render('product/AddProductForm');
    }

    /***
     * Upload image
     */
    public function upload_image(Request $request)
    {
        $request->validate([
            'image' => 'mimes:jpg,png,jpeg|max:3000'
        ]);

        $file = $request->file('image');

        $mime = $file->getMimeType();

        if ( !str_starts_with($mime , 'image/')) {
            throw ValidationException::withMessages([
                'image' => 'invalid image type'
            ]);
        }

        $file_name = str::uuid(). '.' . $file->getClientOriginalExtension();

        $file_path = $file->storeAs('uploads' , $file_name , 'public');

        $url = Storage::path($file_path);

        return response()->json([
            'url' => $url,
            'message' => 'Image uploaded',
            'file_path' => $file_path
        ]);
    }
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'=> 'required',
            'origin' => 'required'
        ]);

        $product = new Product();
        $product->name = $request->name;
        $product->origin = $request->origin;
        $product->save();

        return response()->json(['product' => $product, 'message' => 'Product added.'],200);

    }

    public function add_product_photo(Request $request)
    {
        $request->validate([
           'image' => 'mimes:jpg, png, jpeg | max 2500'
        ]);
        $file = $request->file('image');

        $mime = $file->getMimeType();
        if ( !str_starts_with($mime, 'image/'))
            throw ValidationException::withMessages([
               'image' => 'invalid image'
            ]);

        $file_name = str::uuid(). '.' .$file->getClientOriginalExtension();
        $file_path = $file->storeAs('uploads' , $file_name , 'public');
        $url = Storage::path($file_path);

        return response()->json([
            'url' => $url,
            'file_path' => $file_path,
            'message' => 'Image Uploaded'
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $product = Product::findorfail($id);
        return response()->json(['product' => $product], 200);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        return inertia::render('product/EditProductForm', ['id' => $id]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'name' => 'required',
            'origin' => 'required'
        ]);

        $product = Product::where('id',$id)->first();
        if ($product) {
            $product->update([
                'name' => $request->name,
                'origin' => $request->origin
            ]);
            return response()->json(['product' => $product ,'message' => 'Updated'], 200);
        } else {
            return  response()->json(['message'=> 'Product not found.'], 400);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $product = Product::findorfail($id);
        if ($product){
            $product->delete();
            return response()->json(['message' => 'Product deleted'], 200);
        } else {
            return  response()->json(['message'=> 'Product not found.'], 400);
        }
    }
}
