<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Tag;
use Illuminate\Http\Request;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class ProductController extends Controller
{
    private function processImage($image, $filename)
    {
        $originalPath = public_path('images/' . $filename);
        $thumbnailDir = public_path('images/thumbnails');
        
        $manager = new ImageManager(new Driver());
        $img = $manager->decode($image->getRealPath());
        
        if ($img->width() > 1200 || $img->height() > 1200) {
            $img->scaleDown(width: 1200, height: 1200);
        }
        
        $img->save($originalPath, quality: 80);
        
        $sizes = [
            'small' => 150,
            'medium' => 400,
            'large' => 800,
        ];
        
        foreach ($sizes as $size => $width) {
            $dir = $thumbnailDir . '/' . $size;
            if (!file_exists($dir)) {
                mkdir($dir, 0755, true);
            }
            
            $thumb = $manager->decodePath($originalPath);
            $thumb->scaleDown(width: $width);
            $thumb->save($dir . '/' . $filename, quality: 80);
        }
        
        return 'images/' . $filename;
    }

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
            'temp_images' => 'nullable|array',
            'tag_ids'   => 'nullable|array',
        ]);

        $imagePaths = [];

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $imageName = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
                $imagePaths[] = $this->processImage($image, $imageName);
            }
        }

        if ($request->filled('temp_images')) {
            $tempDir = public_path('images/temp');
            $finalDir = public_path('images');
            $thumbnailDir = public_path('images/thumbnails');
            
            foreach ($request->temp_images as $tempPath) {
                $filename = basename($tempPath);
                $source = $tempDir . '/' . $filename;
                $destination = $finalDir . '/' . $filename;
                
                if (file_exists($source)) {
                    rename($source, $destination);
                    
                    $manager = new ImageManager(new Driver());
                    $sizes = ['small' => 150, 'medium' => 400, 'large' => 800];
                    foreach ($sizes as $size => $width) {
                        $dir = $thumbnailDir . '/' . $size;
                        if (!file_exists($dir)) {
                            mkdir($dir, 0755, true);
                        }
                        
                        $thumb = $manager->decodePath($destination);
                        $thumb->scaleDown(width: $width);
                        $thumb->save($dir . '/' . $filename, quality: 80);
                    }
                    
                    $imagePaths[] = 'images/' . $filename;
                }
            }
        }

        Product::create([
            'name'      => $request->name,
            'details'   => $request->details,
            'images'    => $imagePaths,
            'size'      => $request->size,
            'color'     => $request->color,
            'category'  => $request->category,
            'price'     => $request->price,
            'tag_ids'   => $request->tag_ids,
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
            'temp_images' => 'nullable|array',
            'tag_ids'   => 'nullable|array',
        ]);

        $finalImages = $product->images ?? [];

        if ($request->has('delete_images')) {
            foreach ($request->delete_images as $delImg) {
                if (file_exists(public_path($delImg))) {
                    unlink(public_path($delImg));
                }
                
                $filename = basename($delImg);
                $thumbnailDir = public_path('images/thumbnails');
                foreach (['small', 'medium', 'large'] as $size) {
                    $thumbPath = $thumbnailDir . '/' . $size . '/' . $filename;
                    if (file_exists($thumbPath)) {
                        unlink($thumbPath);
                    }
                }

                $finalImages = array_values(array_filter($finalImages, fn($img) => $img !== $delImg));
            }
        }

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $imageName = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
                $finalImages[] = $this->processImage($image, $imageName);
            }
        }

        if ($request->filled('temp_images')) {
            $tempDir = public_path('images/temp');
            $finalDir = public_path('images');
            $thumbnailDir = public_path('images/thumbnails');
            
            foreach ($request->temp_images as $tempPath) {
                $filename = basename($tempPath);
                $source = $tempDir . '/' . $filename;
                $destination = $finalDir . '/' . $filename;
                
                if (file_exists($source)) {
                    rename($source, $destination);
                    
                    $manager = new ImageManager(new Driver());
                    $sizes = ['small' => 150, 'medium' => 400, 'large' => 800];
                    foreach ($sizes as $size => $width) {
                        $dir = $thumbnailDir . '/' . $size;
                        if (!file_exists($dir)) {
                            mkdir($dir, 0755, true);
                        }
                        
                        $thumb = $manager->decodePath($destination);
                        $thumb->scaleDown(width: $width);
                        $thumb->save($dir . '/' . $filename, quality: 80);
                    }
                    
                    $finalImages[] = 'images/' . $filename;
                }
            }
        }

        $product->update([
            'name'      => $request->name,
            'details'   => $request->details,
            'images'    => $finalImages,
            'size'      => $request->size,
            'color'     => $request->color,
            'category'  => $request->category,
            'price'     => $request->price,
            'tag_ids'   => $request->tag_ids,
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