 Laravel 11 – Simple Image Upload & Public Storage (Basic Product CRUD)
 
![Laravel](https://img.shields.io/badge/Laravel-11-orange)
![PHP](https://img.shields.io/badge/PHP-8.2-blue)
![Bootstrap](https://img.shields.io/badge/Bootstrap-5-purple)
![MySQL](https://img.shields.io/badge/Database-MySQL-yellow)

This guide explains **only** how a single image is uploaded in Laravel 11, how it is saved inside the **public folder**, and how a basic Product CRUD works using the fields:

✔ name  
✔ details  
✔ image  
✔ size  
✔ color  
✔ category  
✔ price  

---

 Features
- Upload a **single image**
- Save image directly in **public/images**
- Store path in database
- Display image in product listing
- Simple CRUD (Create, Edit, Delete)

---

 1. Migration (products table)

```php
$table->string('name');
$table->text('details');
$table->string('image')->nullable();
$table->string('size');
$table->string('color');
$table->string('category');
$table->decimal('price', 8, 2);
```

---

 2. Product Model

```php
protected $fillable = [
    'name','details','image','size','color','category','price'
];
```

---

 3. Image Upload Logic (Store)

```php
public function store(Request $request)
{
    $request->validate([
        'name' => 'required',
        'details' => 'required',
        'size' => 'required',
        'color' => 'required',
        'category' => 'required',
        'price' => 'required|numeric',
        'image' => 'nullable|image|max:2048'
    ]);

    $imagePath = null;

    if ($request->hasFile('image')) {

        $image = $request->file('image');
        $imageName = time() . '_' . $image->getClientOriginalName();

        // Upload image to public folder
        $image->move(public_path('images'), $imageName);

        $imagePath = 'images/' . $imageName;
    }

    Product::create([
        'name' => $request->name,
        'details' => $request->details,
        'image' => $imagePath,
        'size' => $request->size,
        'color' => $request->color,
        'category' => $request->category,
        'price' => $request->price,
    ]);

    return redirect()->route('products.index');
}
```

---

 4. Update Logic (Replace Image)

```php
public function update(Request $request, Product $product)
{
    $imagePath = $product->image;

    if ($request->hasFile('image')) {

        if ($product->image && file_exists(public_path($product->image))) {
            unlink(public_path($product->image));
        }

        $image = $request->file('image');
        $imageName = time().'_'.$image->getClientOriginalName();
        $image->move(public_path('images'), $imageName);

        $imagePath = 'images/'.$imageName;
    }

    $product->update([
        'name' => $request->name,
        'details' => $request->details,
        'image' => $imagePath,
        'size' => $request->size,
        'color' => $request->color,
        'category' => $request->category,
        'price' => $request->price,
    ]);

    return redirect()->route('products.index');
}
```

---

 5. Delete Image + Product

```php
if ($product->image && file_exists(public_path($product->image))) {
    unlink(public_path($product->image));
}

$product->delete();
```

---

 6. Show Image in Blade

```html
@if($product->image)
    <img src="{{ asset($product->image) }}" width="80">
@endif
```

---

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
