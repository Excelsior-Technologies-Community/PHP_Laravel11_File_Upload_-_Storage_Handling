<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Tag;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    // Show all products
    public function index(Request $request)
    {
        $query = Product::where('status', '!=', 'deleted');

        if ($request->filled('keyword')) {
            $keyword = $request->keyword;

            if (is_numeric($keyword)) {
                $query->where('price', (float)$keyword);
            } else {
                $query->where(function ($q) use ($keyword) {
                    $q->where('name', 'like', "%{$keyword}%")
                      ->orWhere('category', 'like', "%{$keyword}%")
                      ->orWhere('color', 'like', "%{$keyword}%")
                      ->orWhere('size', 'like', "%{$keyword}%")
                      ->orWhere('details', 'like', "%{$keyword}%")
                      ->orWhere('price', 'like', "%{$keyword}%");
                });
            }
        }

        if ($request->filled('sort') && in_array($request->sort, ['price-asc', 'price-desc'])) {
            $query->orderBy('price', $request->sort === 'price-asc' ? 'asc' : 'desc');
        } else {
            $query->latest();
        }

        $products = $query->paginate(2);

        return view('products.index', compact('products'));
    }

    // Show create form
    public function create()
    {
        $tags = Tag::all();
        return view('products.create', compact('tags'));
    }

    // Store product data
    public function store(Request $request)
    {
        $request->validate([
            'name'      => 'required',
            'details'   => 'required',
            'size'      => 'required',
            'color'     => 'required',
            'category'  => 'required',
            'price'     => 'required|numeric',
            'images.*'  => 'nullable|image|max:2048',
            'tag_ids'   => 'nullable|array',
        ]);

        $imagePaths = [];

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {

                $imageName = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
                $image->move(public_path('images'), $imageName);

                $imagePaths[] = 'images/' . $imageName;
            }
        }

        Product::create([
            'name'      => $request->name,
            'details'   => $request->details,
            'images'    => $imagePaths,         // ← NO JSON ENCODE
            'size'      => $request->size,
            'color'     => $request->color,
            'category'  => $request->category,
            'price'     => $request->price,
            'tag_ids'   => $request->tag_ids,   // ← NO JSON ENCODE
        ]);

        return redirect()->route('products.index')->with('success', 'Product created successfully.');
    }

    // Edit form
    public function edit(Product $product)
    {
        $tags = Tag::all();
        return view('products.edit', compact('product', 'tags'));
    }

    // Update product data
    public function update(Request $request, Product $product)
    {
        $request->validate([
            'name'      => 'required',
            'details'   => 'required',
            'size'      => 'required',
            'color'     => 'required',
            'category'  => 'required',
            'price'     => 'required|numeric',
            'images.*'  => 'nullable|image|max:2048',
            'tag_ids'   => 'nullable|array',
        ]);

        $finalImages = $product->images ?? [];

        // DELETE OLD SELECTED IMAGES
        if ($request->has('delete_images')) {
            foreach ($request->delete_images as $delImg) {

                if (file_exists(public_path($delImg))) {
                    unlink(public_path($delImg));
                }

                $finalImages = array_values(array_filter($finalImages, fn($img) => $img !== $delImg));
            }
        }

        // UPLOAD NEW IMAGES
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {

                $imageName = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
                $image->move(public_path('images'), $imageName);

                $finalImages[] = 'images/' . $imageName;
            }
        }

        // UPDATE PRODUCT
        $product->update([
            'name'      => $request->name,
            'details'   => $request->details,
            'images'    => $finalImages,        // ← array only
            'size'      => $request->size,
            'color'     => $request->color,
            'category'  => $request->category,
            'price'     => $request->price,
            'tag_ids'   => $request->tag_ids,   // ← array only
        ]);

        return redirect()->route('products.index')->with('success', 'Product updated successfully.');
    }

    // Soft delete product
    public function destroy(Product $product)
    {
        $product->update(['status' => 'deleted']);
        $product->delete();

        return redirect()->route('products.index')->with('success', 'Product deleted successfully.');
    }
}
