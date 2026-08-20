<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Tag;
use Illuminate\Http\Request;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class ProductController extends Controller
{
    /**
     * Process uploaded image.
     *
     * Creates:
     * - Original image
     * - Small thumbnail
     * - Medium thumbnail
     * - Large thumbnail
     */
private function processImage($image, $filename)
{
    $originalPath = public_path('images/' . $filename);
    $thumbnailDir = public_path('images/thumbnails');

    $manager = new ImageManager(new Driver());

    // Intervention Image v3
    $img = $manager->read($image->getRealPath());

    // Limit original image to maximum 1200px
    if ($img->width() > 1200 || $img->height() > 1200) {
        $img->scaleDown(width: 1200, height: 1200);
    }

    // Save original image
    $img->save($originalPath, quality: 80);

    // Create thumbnails
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

        $thumb = $manager->read($originalPath);

        $thumb->scaleDown(width: $width);

        $thumb->save(
            $dir . '/' . $filename,
            quality: 80
        );
    }

    return 'images/' . $filename;
}

    /**
     * Show all products.
     */
    public function index(Request $request)
    {
        $query = Product::where('status', '!=', 'deleted');

        if ($request->filled('keyword')) {

            $keyword = $request->keyword;

            if (is_numeric($keyword)) {

                $query->where('price', (float) $keyword);

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

        if (
            $request->filled('sort') &&
            in_array($request->sort, ['price-asc', 'price-desc'])
        ) {

            $query->orderBy(
                'price',
                $request->sort === 'price-asc'
                    ? 'asc'
                    : 'desc'
            );

        } else {

            $query->latest();
        }

        $products = $query->paginate(2);

        return view('products.index', compact('products'));
    }

    /**
     * Show create form.
     */
    public function create()
    {
        $tags = Tag::all();

        return view(
            'products.create',
            compact('tags')
        );
    }

    /**
     * Store product.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'details' => 'required',
            'size' => 'required',
            'color' => 'required',
            'category' => 'required',
            'price' => 'required|numeric',

            'images.*' => 'nullable|image|max:2048',

            'temp_images' => 'nullable|array',

            'tag_ids' => 'nullable|array',

            // New functionality
            'primary_image_index' => 'nullable|integer|min:0',
        ]);

        $imagePaths = [];

        /*
        |--------------------------------------------------------------------------
        | Upload new images
        |--------------------------------------------------------------------------
        */

        if ($request->hasFile('images')) {

            foreach ($request->file('images') as $image) {

                $imageName =
                    time() .
                    '_' .
                    uniqid() .
                    '.' .
                    $image->getClientOriginalExtension();

                $imagePaths[] = $this->processImage(
                    $image,
                    $imageName
                );
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Move temporary images to final storage
        |--------------------------------------------------------------------------
        */

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

                    $manager = new ImageManager(
                        new Driver()
                    );

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

                        $thumb = $manager->decodePath(
                            $destination
                        );

                        $thumb->scaleDown(
                            width: $width
                        );

                        $thumb->save(
                            $dir . '/' . $filename,
                            quality: 80
                        );
                    }

                    $imagePaths[] = 'images/' . $filename;
                }
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Primary Image
        |--------------------------------------------------------------------------
        */

        $primaryImage = null;

        if (
            $request->filled('primary_image_index') &&
            isset(
                $imagePaths[
                    (int) $request->primary_image_index
                ]
            )
        ) {

            $primaryImage =
                $imagePaths[
                    (int) $request->primary_image_index
                ];

        } elseif (!empty($imagePaths)) {

            // Automatically use first image if no image was selected.
            $primaryImage = $imagePaths[0];
        }

        /*
        |--------------------------------------------------------------------------
        | Create Product
        |--------------------------------------------------------------------------
        */

        Product::create([
            'name' => $request->name,
            'details' => $request->details,

            'images' => $imagePaths,

            // New functionality
            'primary_image' => $primaryImage,

            'size' => $request->size,
            'color' => $request->color,
            'category' => $request->category,
            'price' => $request->price,
            'tag_ids' => $request->tag_ids,
        ]);

        return redirect()
            ->route('products.index')
            ->with(
                'success',
                'Product created successfully.'
            );
    }

    /**
     * Edit form.
     */
    public function edit(Product $product)
    {
        $tags = Tag::all();

        return view(
            'products.edit',
            compact('product', 'tags')
        );
    }

    /**
     * Update product.
     */
    public function update(
        Request $request,
        Product $product
    ) {

        $request->validate([
            'name' => 'required',
            'details' => 'required',
            'size' => 'required',
            'color' => 'required',
            'category' => 'required',
            'price' => 'required|numeric',

            'images.*' => 'nullable|image|max:2048',

            'temp_images' => 'nullable|array',

            'tag_ids' => 'nullable|array',

            // New functionality
            'primary_image' => 'nullable|string',
            'primary_image_index' => 'nullable|integer|min:0',
        ]);

        $finalImages = $product->images ?? [];

        /*
        |--------------------------------------------------------------------------
        | Delete Existing Images
        |--------------------------------------------------------------------------
        */

        if ($request->has('delete_images')) {

            foreach ($request->delete_images as $delImg) {

                if (file_exists(public_path($delImg))) {
                    unlink(public_path($delImg));
                }

                $filename = basename($delImg);

                $thumbnailDir =
                    public_path('images/thumbnails');

                foreach (
                    ['small', 'medium', 'large']
                    as $size
                ) {

                    $thumbPath =
                        $thumbnailDir .
                        '/' .
                        $size .
                        '/' .
                        $filename;

                    if (file_exists($thumbPath)) {
                        unlink($thumbPath);
                    }
                }

                $finalImages = array_values(
                    array_filter(
                        $finalImages,
                        fn ($img) => $img !== $delImg
                    )
                );
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Upload New Images
        |--------------------------------------------------------------------------
        */

        $newImagePaths = [];

        if ($request->hasFile('images')) {

            foreach ($request->file('images') as $image) {

                $imageName =
                    time() .
                    '_' .
                    uniqid() .
                    '.' .
                    $image->getClientOriginalExtension();

                $newImagePaths[] =
                    $this->processImage(
                        $image,
                        $imageName
                    );
            }

            $finalImages = array_merge(
                $finalImages,
                $newImagePaths
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Move Temporary Images
        |--------------------------------------------------------------------------
        */

        if ($request->filled('temp_images')) {

            $tempDir = public_path('images/temp');
            $finalDir = public_path('images');
            $thumbnailDir = public_path('images/thumbnails');

            foreach ($request->temp_images as $tempPath) {

                $filename = basename($tempPath);

                $source =
                    $tempDir . '/' . $filename;

                $destination =
                    $finalDir . '/' . $filename;

                if (file_exists($source)) {

                    rename(
                        $source,
                        $destination
                    );

                    $manager =
                        new ImageManager(
                            new Driver()
                        );

                    $sizes = [
                        'small' => 150,
                        'medium' => 400,
                        'large' => 800,
                    ];

                    foreach (
                        $sizes as $size => $width
                    ) {

                        $dir =
                            $thumbnailDir .
                            '/' .
                            $size;

                        if (!file_exists($dir)) {
                            mkdir(
                                $dir,
                                0755,
                                true
                            );
                        }

                        $thumb =
                            $manager->decodePath(
                                $destination
                            );

                        $thumb->scaleDown(
                            width: $width
                        );

                        $thumb->save(
                            $dir . '/' . $filename,
                            quality: 80
                        );
                    }

                    $path =
                        'images/' . $filename;

                    $finalImages[] = $path;
                    $newImagePaths[] = $path;
                }
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Determine Primary Image
        |--------------------------------------------------------------------------
        */

        $primaryImage =
            $product->primary_image;

        // If selected existing image is still available.
        if (
            $request->filled('primary_image') &&
            in_array(
                $request->primary_image,
                $finalImages
            )
        ) {

            $primaryImage =
                $request->primary_image;

        } elseif (
            $request->filled('primary_image_index') &&
            isset(
                $newImagePaths[
                    (int) $request->primary_image_index
                ]
            )
        ) {

            // Select newly uploaded image as primary.
            $primaryImage =
                $newImagePaths[
                    (int) $request->primary_image_index
                ];

        } elseif (
            empty($primaryImage) ||
            !in_array(
                $primaryImage,
                $finalImages
            )
        ) {

            // If current primary was deleted,
            // automatically select first remaining image.
            $primaryImage =
                !empty($finalImages)
                    ? $finalImages[0]
                    : null;
        }

        /*
        |--------------------------------------------------------------------------
        | Update Product
        |--------------------------------------------------------------------------
        */

        $product->update([
            'name' => $request->name,
            'details' => $request->details,

            'images' => array_values(
                $finalImages
            ),

            // New functionality
            'primary_image' => $primaryImage,

            'size' => $request->size,
            'color' => $request->color,
            'category' => $request->category,
            'price' => $request->price,
            'tag_ids' => $request->tag_ids,
        ]);

        return redirect()
            ->route('products.index')
            ->with(
                'success',
                'Product updated successfully.'
            );
    }

    /**
     * Soft delete product.
     */
    public function destroy(Product $product)
    {
        $product->update([
            'status' => 'deleted',
        ]);

        $product->delete();

        return redirect()
            ->route('products.index')
            ->with(
                'success',
                'Product deleted successfully.'
            );
    }
}