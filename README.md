# PHP_Laravel11_File_Upload_-_Storage_Handling

This documentation explains how to build a complete CRUD system in Laravel 11  
with **image upload, update and delete**, and storing files in the **public/images** folder.

No search, no pagination, no sorting — only pure CRUD + file handling.

---

# Step 1: Create Laravel Project

Open terminal and run:

```
composer create-project laravel/laravel product-crud
```

Enter project folder:

```
cd product-crud
```

Run server:

```
php artisan serve
```

---

# Step 2: Configure Database

Open `.env` and set MySQL credentials:

```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_db_name
DB_USERNAME=root
DB_PASSWORD=root
```

---

# Step 3: Create Products Migration

```
php artisan make:migration create_products_table --create=products
```

Open migration file and add:

```php
Schema::create('products', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->text('details');
    $table->string('image')->nullable(); // store image path
    $table->decimal('price', 8, 2);
    $table->timestamps();
});
```

Run migration:

```
php artisan migrate
```

---

# Step 4: Create Model

```
php artisan make:model Product
```

Open model: `app/Models/Product.php`

```php
class Product extends Model
{
    protected $fillable = [
        'name','details','image','price'
    ];
}
```

---

# Step 5: Add CRUD Routes

Open `routes/web.php`:

```php
use App\Http\Controllers\ProductController;

Route::resource('products', ProductController::class);
```

---

# Step 6: Create Controller

```
php artisan make:controller ProductController --resource --model=Product
```

---

# Step 7: Implement Full CRUD + Image Upload

Below is complete logic for:

 Create  
 Read  
 Update  
 Delete  
 Image upload → public/images  
 Remove old image on update  
 Remove image on delete  

---

##  STORE (Create Product + Upload Image)

```php
public function store(Request $request)
{
    $request->validate([
        'name' => 'required',
        'details' => 'required',
        'price' => 'required|numeric',
        'image' => 'nullable|image|max:2048'
    ]);

    $imagePath = null;

    if ($request->hasFile('image')) {

        $image = $request->file('image');

        $imageName = time().'_'.$image->getClientOriginalName();

        // Save to /public/images
        $image->move(public_path('images'), $imageName);

        // Save path to DB
        $imagePath = 'images/' . $imageName;
    }

    Product::create([
        'name' => $request->name,
        'details' => $request->details,
        'price' => $request->price,
        'image' => $imagePath,
    ]);

    return redirect()->route('products.index')->with('success', 'Product created!');
}
```

###  How image uploading works?
- User selects a file  
- Laravel gets it via `$request->file('image')`  
- `move(public_path('images'), $imageName)` stores image in **public/images**  
- Only **path** (`images/filename.jpg`) is saved in database  

---

##  INDEX (Show All Products)

```php
public function index()
{
    $products = Product::all(); // No pagination
    return view('products.index', compact('products'));
}
```

---

## EDIT (Load Edit Form)

```php
public function edit(Product $product)
{
    return view('products.edit', compact('product'));
}
```

---

##  UPDATE (Replace Image + Delete Old One)

```php
public function update(Request $request, Product $product)
{
    $request->validate([
        'name' => 'required',
        'details' => 'required',
        'price' => 'required|numeric',
        'image' => 'nullable|image|max:2048',
    ]);

    $imagePath = $product->image;

    // If new image uploaded
    if ($request->hasFile('image')) {

        // Delete old image
        if ($product->image && file_exists(public_path($product->image))) {
            unlink(public_path($product->image));
        }

        $image = $request->file('image');
        $imageName = time().'_'.$image->getClientOriginalName();
        $image->move(public_path('images'), $imageName);

        $imagePath = 'images/' . $imageName;
    }

    $product->update([
        'name' => $request->name,
        'details' => $request->details,
        'price' => $request->price,
        'image' => $imagePath,
    ]);

    return redirect()->route('products.index')->with('success', 'Product updated!');
}
```

---

## DELETE (Remove Product + Delete Image)

```php
public function destroy(Product $product)
{
    if ($product->image && file_exists(public_path($product->image))) {
        unlink(public_path($product->image));
    }

    $product->delete();

    return redirect()->route('products.index')->with('success', 'Product deleted!');
}
```

---

# Step 8: Blade Views

## Create / Edit Forms

```blade
<form action="{{ isset($product) ? route('products.update', $product) : route('products.store') }}" 
      method="POST" enctype="multipart/form-data">

    @csrf
    @if(isset($product)) @method('PUT') @endif

    <input type="text" name="name" value="{{ $product->name ?? '' }}" required>
    <textarea name="details">{{ $product->details ?? '' }}</textarea>
    <input type="number" name="price" value="{{ $product->price ?? '' }}" step="0.01" required>

    <input type="file" name="image">

    @if(isset($product) && $product->image)
        <img src="{{ asset($product->image) }}" width="100">
    @endif

    <button type="submit">Save</button>
</form>
```

---

## Index Page

```blade
@foreach($products as $product)
<tr>
    <td>{{ $product->name }}</td>
    <td>{{ $product->details }}</td>
    <td>{{ $product->price }}</td>
    <td>
        @if($product->image)
            <img src="{{ asset($product->image) }}" width="80">
        @endif
    </td>
    <td>
        <a href="{{ route('products.edit', $product) }}">Edit</a>

        <form action="{{ route('products.destroy', $product) }}" method="POST">
            @csrf
            @method('DELETE')
            <button>Delete</button>
        </form>
    </td>
</tr>
@endforeach
```

 Where Images Are Stored?

Images are uploaded to:

```
public/images
```

Access using:

```php
asset('images/filename.jpg')
```

---<img width="676" height="213" alt="image" src="https://github.com/user-attachments/assets/08ec545b-7897-445d-a5b2-8a2796d894e3" />
