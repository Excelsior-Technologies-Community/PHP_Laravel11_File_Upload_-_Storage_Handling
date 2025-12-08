<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Customer Area</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background: #f5f6f7;
        }
        .product-img {
            height: 220px;
            width: 100%;
            object-fit: cover;
            border-bottom: 1px solid #ddd;
        }
    </style>
</head>
<body>

    <!-- SIMPLE CUSTOMER HEADER -->
    <nav class="navbar navbar-light bg-white shadow-sm mb-4">
        <div class="container">
            <h4 class="m-0">Customer Products</h4>
        </div>
    </nav>

    <div class="container">
        @yield('content')
    </div>

</body>
</html>
