<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Container\Attributes\Auth;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class ProductController extends Controller
{
        public function index(Request $request)
        {
        
                $products = Product::with('category')
                            ->paginate(8); 

            return response()->json(['products' => $products]);
        
            
        }
        public function getByAdmin(Request $request)
        {
        
                $products = Product::with('category')
                            ->paginate(8); 

            return response()->json(['products' => $products]);
        
            
        }


        public function getByCat($id)
        {
            $category = Category::findOrFail($id); 
            
        $products = Product::where('category_id', $category->id)
                        ->with('category')
                        ->paginate(12);

            return response()->json([
                'products' => $products,
                'category' => $category
            ]);
        }


        public function show($id)
        {
            $product = Product::with('category')->findOrFail($id);
            return response()->json(['product' => $product]);
        }

public function store(Request $request)
{
    $validated = $request->validate([
        'name' => 'required|string',
        'images.*' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        'price' => 'required|numeric',
        'old_price' => 'nullable|numeric',
        'rating' => 'nullable|numeric|min:1|max:5',
        'reviews' => 'nullable|integer|min:0',
        'description' => 'nullable|string',
        'category_id' => 'required|exists:categories,id',
    ]);

    $images = [];

    if ($request->hasFile('images')) {
         foreach ($request->file('images') as $image) {
        $path = $image->store('products', 'public'); // ✅ هنا disk اسمه "public"
        $images[] = $path;
    }
    }

    if (!empty($images)) {
        $validated['images'] = $images;
    }

    $product = Product::create($validated);

    return response()->json([
        'message' => 'Product created successfully',
        'product' => $product,
    ], 201);
}

public function update(Request $request, $id)
{
    $product = Product::findOrFail($id);

    $validated = $request->validate([
        'name' => 'sometimes|string',
        'images.*' => 'sometimes|image|mimes:jpg,jpeg,png,webp|max:2048',
        'price' => 'sometimes|numeric',
        'old_price' => 'nullable|numeric',
        'rating' => 'nullable|numeric|min:1|max:5',
        'reviews' => 'nullable|integer|min:0',
        'description' => 'nullable|string',
        'category_id' => 'sometimes|exists:categories,id',
    ]);

  

    // تحديث الصور المتعددة (اختياري حسب مشروعك)
    if ($request->hasFile('images')) {

         if ($product->images) {
        foreach ($product->images as $oldImage) {
            \Storage::disk('public')->delete('products/' . $oldImage);
        }
        }
        $images = [];
       foreach ($request->file('images') as $image) {
        $path = $image->store('products', 'public'); // ✅ هنا disk اسمه "public"
        $images[] = $path;
       }

        $validated['images'] = $images; // لو عندك عمود images في الجدول
    }

    $product->update($validated);

    return response()->json([
        'message' => 'Product updated successfully',
        'product' => $product,
    ]);
}




        public function destroy($id)
        {
            $product = Product::findOrFail($id);
            $product->delete();

            return response()->json(['message' => 'Product deleted']);
        }
}
