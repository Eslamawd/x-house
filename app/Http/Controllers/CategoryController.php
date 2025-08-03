<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
       public function index()
    {
          $categories = Category::parentsOnly()->paginate(6);

    return response()->json(['categories' => $categories]);
    }

    
       public function getByAdmin()
    {
          $categories = Category::with('children')->paginate(10);


    return response()->json(['categories' => $categories]);
    }


       public function getAll()
            {
                $categories = Category::whereNull('parent_id')
                ->with('children') // جلب الأبناء مباشرة
                ->get();

            return response()->json(['categories' => $categories]);
            }
        public function store(Request $request)
        {
            $request->validate([
                'name'     => 'required|string',
                'image'       => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
                'parent_id'   => 'nullable|exists:categories,id',
            ]);

            // رفع الصورة لو موجودة
            $imagePath = null;
            if ($request->hasFile('image')) {
                $imagePath = $request->file('image')->store('categories', 'public');
            }

            $category = Category::create([
                'name'     => $request->name,
                'image'       => $imagePath, // المسار الجديد
                'parent_id'   => $request->parent_id,
            ]);

            return response()->json(['category' => $category], 201);
        }


        public function show(Request $request,$id)
        {
            $perPage = 8;
            $category = Category::findOrFail($id);

            // الأطفال paginated
            $childrenQuery = Category::where('parent_id', $category->id);
            $children = $childrenQuery->paginate($perPage);

            // المنتجات برضو paginated
            $products = $category->products()->paginate($perPage);


            
            return response()->json([
                'category' => $category,
                'children' => $children,
                'products' => $products,
            ]);
        }


    public function update(Request $request,  $category)
    {
        $category = Category::findOrFail($category);

         $request->validate([
                'name'     => 'required|string',
                'image'       => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
                'parent_id'   => 'nullable|exists:categories,id',
            ]);

         $imagePath = null;
            if ($request->hasFile('image')) {
                $imagePath = $request->file('image')->store('categories', 'public');
            }

            $category = Category::update([
                'name'     => $request->name,
                'image'       => $imagePath, // المسار الجديد
                'parent_id'   => $request->parent_id,
            ]);
      
        return response()->json(['category'=> $category]);
    }

    public function destroy($category)
    {
        $category = Category::findOrFail($category);
        $category->delete();
        return response()->json(['message' => 'Deleted']);
    }

}
