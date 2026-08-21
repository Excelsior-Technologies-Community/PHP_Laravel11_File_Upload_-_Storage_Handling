<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Tag;
use Illuminate\Http\Request;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Symfony\Component\HttpFoundation\StreamedResponse;

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
     * Show all active products.
     *
     * Features:
     * - Search
     * - Category filter
     * - Color filter
     * - Size filter
     * - Minimum price
     * - Maximum price
     * - Sorting
     * - Per page
     * - Pagination
     */
    public function index(Request $request)
    {
        $query = Product::query()
            ->whereNull('deleted_at')
            ->where('status', '!=', 'deleted');

        /*
        |--------------------------------------------------------------------------
        | SEARCH
        |--------------------------------------------------------------------------
        */

        if ($request->filled('keyword')) {

            $keyword = trim($request->keyword);

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


        /*
        |--------------------------------------------------------------------------
        | CATEGORY FILTER
        |--------------------------------------------------------------------------
        */

        if ($request->filled('category')) {

            $query->where(
                'category',
                $request->category
            );
        }


        /*
        |--------------------------------------------------------------------------
        | COLOR FILTER
        |--------------------------------------------------------------------------
        */

        if ($request->filled('color')) {

            $query->where(
                'color',
                $request->color
            );
        }


        /*
        |--------------------------------------------------------------------------
        | SIZE FILTER
        |--------------------------------------------------------------------------
        */

        if ($request->filled('size')) {

            $query->where(
                'size',
                $request->size
            );
        }


        /*
        |--------------------------------------------------------------------------
        | MINIMUM PRICE
        |--------------------------------------------------------------------------
        */

        if ($request->filled('min_price')) {

            $query->where(
                'price',
                '>=',
                (float) $request->min_price
            );
        }


        /*
        |--------------------------------------------------------------------------
        | MAXIMUM PRICE
        |--------------------------------------------------------------------------
        */

        if ($request->filled('max_price')) {

            $query->where(
                'price',
                '<=',
                (float) $request->max_price
            );
        }


        /*
        |--------------------------------------------------------------------------
        | SORTING
        |--------------------------------------------------------------------------
        */

        $allowedSorts = [
            'price-asc',
            'price-desc',
            'name-asc',
            'name-desc',
            'latest',
            'oldest',
        ];

        $sort = $request->get('sort', 'oldest');

        if (!in_array($sort, $allowedSorts)) {
            $sort = 'latest';
        }

        switch ($sort) {

            case 'price-asc':
                $query->orderBy('price', 'asc');
                break;

            case 'price-desc':
                $query->orderBy('price', 'desc');
                break;

            case 'name-asc':
                $query->orderBy('name', 'asc');
                break;

            case 'name-desc':
                $query->orderBy('name', 'desc');
                break;

            case 'oldest':
                $query->oldest();
                break;

            default:
                $query->latest();
                break;
        }


        /*
        |--------------------------------------------------------------------------
        | PER PAGE
        |--------------------------------------------------------------------------
        */

        $allowedPerPage = [
            5,
            10,
            25,
            50,
        ];

        $perPage = (int) $request->get('per_page', 5);

        if (!in_array($perPage, $allowedPerPage)) {
            $perPage = 5;
        }


        /*
        |--------------------------------------------------------------------------
        | PAGINATION
        |--------------------------------------------------------------------------
        */

        $products = $query
            ->paginate($perPage)
            ->withQueryString();


        /*
        |--------------------------------------------------------------------------
        | FILTER OPTIONS
        |--------------------------------------------------------------------------
        */

        $categories = Product::query()
            ->whereNull('deleted_at')
            ->where('status', '!=', 'deleted')
            ->whereNotNull('category')
            ->where('category', '!=', '')
            ->distinct()
            ->orderBy('category')
            ->pluck('category');

        $colors = Product::query()
            ->whereNull('deleted_at')
            ->where('status', '!=', 'deleted')
            ->whereNotNull('color')
            ->where('color', '!=', '')
            ->distinct()
            ->orderBy('color')
            ->pluck('color');

        $sizes = Product::query()
            ->whereNull('deleted_at')
            ->where('status', '!=', 'deleted')
            ->whereNotNull('size')
            ->where('size', '!=', '')
            ->distinct()
            ->orderBy('size')
            ->pluck('size');


        return view(
            'products.index',
            compact(
                'products',
                'categories',
                'colors',
                'sizes',
                'perPage'
            )
        );
    }


    /**
     * Export products to CSV.
     *
     * Uses the same filters as the product listing.
     */
    public function exportCsv(Request $request): StreamedResponse
    {
        $query = Product::query()
            ->whereNull('deleted_at')
            ->where('status', '!=', 'deleted');


        /*
        |--------------------------------------------------------------------------
        | SEARCH
        |--------------------------------------------------------------------------
        */

        if ($request->filled('keyword')) {

            $keyword = trim($request->keyword);

            if (is_numeric($keyword)) {

                $query->where(
                    'price',
                    (float) $keyword
                );
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


        /*
        |--------------------------------------------------------------------------
        | ADVANCED FILTERS
        |--------------------------------------------------------------------------
        */

        if ($request->filled('category')) {

            $query->where(
                'category',
                $request->category
            );
        }

        if ($request->filled('color')) {

            $query->where(
                'color',
                $request->color
            );
        }

        if ($request->filled('size')) {

            $query->where(
                'size',
                $request->size
            );
        }

        if ($request->filled('min_price')) {

            $query->where(
                'price',
                '>=',
                (float) $request->min_price
            );
        }

        if ($request->filled('max_price')) {

            $query->where(
                'price',
                '<=',
                (float) $request->max_price
            );
        }


        /*
        |--------------------------------------------------------------------------
        | SORT
        |--------------------------------------------------------------------------
        */

        $allowedSorts = [
            'price-asc',
            'price-desc',
            'name-asc',
            'name-desc',
            'latest',
            'oldest',
        ];

        $sort = $request->get(
            'sort',
            'latest'
        );

        if (!in_array($sort, $allowedSorts)) {
            $sort = 'latest';
        }

        switch ($sort) {

            case 'price-asc':
                $query->orderBy('price', 'asc');
                break;

            case 'price-desc':
                $query->orderBy('price', 'desc');
                break;

            case 'name-asc':
                $query->orderBy('name', 'asc');
                break;

            case 'name-desc':
                $query->orderBy('name', 'desc');
                break;

            case 'oldest':
                $query->oldest();
                break;

            default:
                $query->latest();
                break;
        }


        $products = $query->get();


        /*
        |--------------------------------------------------------------------------
        | CSV DOWNLOAD
        |--------------------------------------------------------------------------
        */

        $filename =
            'products_' .
            now()->format('Y-m-d_H-i-s') .
            '.csv';


        return response()->streamDownload(

            function () use ($products) {

                $handle = fopen(
                    'php://output',
                    'w'
                );

                /*
                | CSV Header
                */

                fputcsv($handle, [
                    'ID',
                    'Name',
                    'Details',
                    'Category',
                    'Color',
                    'Size',
                    'Price',
                    'Primary Image',
                    'Created At',
                ]);


                /*
                | CSV Rows
                */

                foreach ($products as $product) {

                    fputcsv($handle, [

                        $product->id,

                        $product->name,

                        $product->details,

                        $product->category,

                        $product->color,

                        $product->size,

                        $product->price,

                        $product->primary_image
                            ? basename(
                                $product->primary_image
                            )
                            : '',

                        $product->created_at
                            ? $product->created_at
                            ->format(
                                'Y-m-d H:i:s'
                            )
                            : '',

                    ]);
                }

                fclose($handle);
            },

            $filename,

            [
                'Content-Type' =>
                'text/csv; charset=UTF-8',
            ]
        );
    }


    /**
     * Recycle Bin.
     *
     * Show deleted products.
     */
    public function trash(Request $request)
    {
        $query = Product::onlyTrashed()
            ->where(function ($q) {

                $q->where('status', 'deleted')
                    ->orWhereNull('status');
            });


        /*
        |--------------------------------------------------------------------------
        | SEARCH DELETED PRODUCTS
        |--------------------------------------------------------------------------
        */

        if ($request->filled('keyword')) {

            $keyword = trim(
                $request->keyword
            );

            $query->where(function ($q) use ($keyword) {

                $q->where(
                    'name',
                    'like',
                    "%{$keyword}%"
                )
                    ->orWhere(
                        'category',
                        'like',
                        "%{$keyword}%"
                    )
                    ->orWhere(
                        'color',
                        'like',
                        "%{$keyword}%"
                    )
                    ->orWhere(
                        'size',
                        'like',
                        "%{$keyword}%"
                    )
                    ->orWhere(
                        'details',
                        'like',
                        "%{$keyword}%"
                    );
            });
        }


        $products = $query
            ->latest('deleted_at')
            ->paginate(10)
            ->withQueryString();


        return view(
            'products.trash',
            compact('products')
        );
    }


    /**
     * Restore deleted product.
     */
    public function restore($id)
    {
        $product = Product::onlyTrashed()
            ->findOrFail($id);

        $product->restore();

        $product->update([
            'status' => 'active',
        ]);

        return redirect()
            ->route('products.trash')
            ->with(
                'success',
                'Product restored successfully.'
            );
    }


    /**
     * Permanently delete product.
     */
    public function forceDelete($id)
    {
        $product = Product::onlyTrashed()
            ->findOrFail($id);


        /*
        |--------------------------------------------------------------------------
        | Delete Product Images
        |--------------------------------------------------------------------------
        */

        $images = $product->images ?? [];

        if (is_string($images)) {

            $images = json_decode(
                $images,
                true
            ) ?? [];
        }


        foreach ($images as $image) {

            $filename = basename($image);

            /*
            | Original
            */

            $originalPath =
                public_path(
                    'images/' . $filename
                );

            if (file_exists($originalPath)) {
                unlink($originalPath);
            }


            /*
            | Thumbnails
            */

            foreach (
                ['small', 'medium', 'large']
                as $size
            ) {

                $thumbnailPath =
                    public_path(
                        'images/thumbnails/' .
                            $size .
                            '/' .
                            $filename
                    );

                if (file_exists($thumbnailPath)) {
                    unlink($thumbnailPath);
                }
            }
        }


        /*
        |--------------------------------------------------------------------------
        | Force Delete Database Record
        |--------------------------------------------------------------------------
        */

        $product->forceDelete();


        return redirect()
            ->route('products.trash')
            ->with(
                'success',
                'Product permanently deleted.'
            );
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

            'name' =>
            'required',

            'details' =>
            'required',

            'size' =>
            'required',

            'color' =>
            'required',

            'category' =>
            'required',

            'price' =>
            'required|numeric',

            'images.*' =>
            'nullable|image|max:2048',

            'temp_images' =>
            'nullable|array',

            'tag_ids' =>
            'nullable|array',

            'primary_image_index' =>
            'nullable|integer|min:0',

        ]);


        $imagePaths = [];


        /*
        |--------------------------------------------------------------------------
        | Upload New Images
        |--------------------------------------------------------------------------
        */

        if ($request->hasFile('images')) {

            foreach (
                $request->file('images')
                as $image
            ) {

                $imageName =
                    time() .
                    '_' .
                    uniqid() .
                    '.' .
                    $image->getClientOriginalExtension();


                $imagePaths[] =
                    $this->processImage(
                        $image,
                        $imageName
                    );
            }
        }


        /*
        |--------------------------------------------------------------------------
        | Move Temporary Images
        |--------------------------------------------------------------------------
        */

        if ($request->filled('temp_images')) {

            $tempDir =
                public_path(
                    'images/temp'
                );

            $finalDir =
                public_path(
                    'images'
                );

            $thumbnailDir =
                public_path(
                    'images/thumbnails'
                );


            foreach (
                $request->temp_images
                as $tempPath
            ) {

                $filename =
                    basename($tempPath);

                $source =
                    $tempDir .
                    '/' .
                    $filename;

                $destination =
                    $finalDir .
                    '/' .
                    $filename;


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
                            $dir .
                                '/' .
                                $filename,
                            quality: 80
                        );
                    }


                    $imagePaths[] =
                        'images/' .
                        $filename;
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
            $request->filled(
                'primary_image_index'
            ) &&
            isset(
                $imagePaths[(int)
                    $request->primary_image_index]
            )
        ) {

            $primaryImage =
                $imagePaths[(int)
                    $request->primary_image_index];
        } elseif (!empty($imagePaths)) {

            $primaryImage =
                $imagePaths[0];
        }


        /*
        |--------------------------------------------------------------------------
        | Create Product
        |--------------------------------------------------------------------------
        */

        Product::create([

            'name' =>
            $request->name,

            'details' =>
            $request->details,

            'images' =>
            $imagePaths,

            'primary_image' =>
            $primaryImage,

            'size' =>
            $request->size,

            'color' =>
            $request->color,

            'category' =>
            $request->category,

            'price' =>
            $request->price,

            'tag_ids' =>
            $request->tag_ids,

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
            compact(
                'product',
                'tags'
            )
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

            'name' =>
            'required',

            'details' =>
            'required',

            'size' =>
            'required',

            'color' =>
            'required',

            'category' =>
            'required',

            'price' =>
            'required|numeric',

            'images.*' =>
            'nullable|image|max:2048',

            'temp_images' =>
            'nullable|array',

            'tag_ids' =>
            'nullable|array',

            'primary_image' =>
            'nullable|string',

            'primary_image_index' =>
            'nullable|integer|min:0',

        ]);


        $finalImages =
            $product->images ?? [];


        /*
        |--------------------------------------------------------------------------
        | Delete Existing Images
        |--------------------------------------------------------------------------
        */

        if ($request->has('delete_images')) {

            foreach (
                $request->delete_images
                as $delImg
            ) {

                if (
                    file_exists(
                        public_path($delImg)
                    )
                ) {

                    unlink(
                        public_path($delImg)
                    );
                }


                $filename =
                    basename($delImg);


                $thumbnailDir =
                    public_path(
                        'images/thumbnails'
                    );


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


                    if (
                        file_exists(
                            $thumbPath
                        )
                    ) {

                        unlink(
                            $thumbPath
                        );
                    }
                }


                $finalImages =
                    array_values(
                        array_filter(
                            $finalImages,
                            fn($img) =>
                            $img !== $delImg
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

            foreach (
                $request->file('images')
                as $image
            ) {

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


            $finalImages =
                array_merge(
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

            $tempDir =
                public_path(
                    'images/temp'
                );

            $finalDir =
                public_path(
                    'images'
                );

            $thumbnailDir =
                public_path(
                    'images/thumbnails'
                );


            foreach (
                $request->temp_images
                as $tempPath
            ) {

                $filename =
                    basename($tempPath);


                $source =
                    $tempDir .
                    '/' .
                    $filename;


                $destination =
                    $finalDir .
                    '/' .
                    $filename;


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
                            $dir .
                                '/' .
                                $filename,
                            quality: 80
                        );
                    }


                    $path =
                        'images/' .
                        $filename;


                    $finalImages[] =
                        $path;


                    $newImagePaths[] =
                        $path;
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


        if (
            $request->filled(
                'primary_image'
            ) &&
            in_array(
                $request->primary_image,
                $finalImages
            )
        ) {

            $primaryImage =
                $request->primary_image;
        } elseif (
            $request->filled(
                'primary_image_index'
            ) &&
            isset(
                $newImagePaths[(int)
                    $request->primary_image_index]
            )
        ) {

            $primaryImage =
                $newImagePaths[(int)
                    $request->primary_image_index];
        } elseif (
            empty($primaryImage) ||
            !in_array(
                $primaryImage,
                $finalImages
            )
        ) {

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

            'name' =>
            $request->name,

            'details' =>
            $request->details,

            'images' =>
            array_values(
                $finalImages
            ),

            'primary_image' =>
            $primaryImage,

            'size' =>
            $request->size,

            'color' =>
            $request->color,

            'category' =>
            $request->category,

            'price' =>
            $request->price,

            'tag_ids' =>
            $request->tag_ids,

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
                'Product moved to recycle bin.'
            );
    }
}
