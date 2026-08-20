<!-- resources/views/layouts/admin.blade.php -->

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>

    <meta charset="utf-8">

    <meta name="viewport" content="width=device-width, initial-scale=1">

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>
        {{ config('app.name', 'Laravel') }} - Admin
    </title>


    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">

    <link
        href="https://fonts.bunny.net/css?family=figtree:400,500,600,700"
        rel="stylesheet"
    />


    <!-- Bootstrap -->
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >


    <!-- Select2 -->
    <link
        href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css"
        rel="stylesheet"
    >


    <!-- Custom Admin CSS -->

    <style>

        /* =========================================
           GLOBAL
        ========================================= */

        body {

            font-family: 'Figtree', sans-serif;

            background:
                linear-gradient(
                    135deg,
                    #f8fafc 0%,
                    #eef2ff 100%
                );

            min-height: 100vh;

            color: #1f2937;

        }


        /* =========================================
           MAIN CONTENT
        ========================================= */

        .admin-content {

            min-height: calc(100vh - 70px);

            padding-top: 30px;
            padding-bottom: 50px;

        }


        /* =========================================
           PAGE CARD
        ========================================= */

        .admin-card {

            background: #ffffff;

            border: 1px solid #e5e7eb;

            border-radius: 16px;

            box-shadow:
                0 4px 20px rgba(15, 23, 42, 0.06);

        }


        /* =========================================
           PAGE HEADER
        ========================================= */

        .page-header {

            display: flex;

            align-items: center;

            justify-content: space-between;

            gap: 20px;

            margin-bottom: 25px;

        }


        .page-title {

            font-size: 28px;

            font-weight: 700;

            margin: 0;

            color: #111827;

        }


        .page-subtitle {

            color: #6b7280;

            margin-top: 5px;

            margin-bottom: 0;

            font-size: 14px;

        }


        /* =========================================
           FORM DESIGN
        ========================================= */

        .form-control,
        .form-select {

            border-radius: 10px;

            border: 1px solid #d1d5db;

            padding: 10px 13px;

            transition: all 0.2s ease;

        }


        .form-control:focus,
        .form-select:focus {

            border-color: #6366f1;

            box-shadow:
                0 0 0 3px rgba(99, 102, 241, 0.12);

        }


        .form-label {

            color: #374151;

            margin-bottom: 7px;

        }


        textarea.form-control {

            min-height: 120px;

            resize: vertical;

        }


        /* =========================================
           BUTTONS
        ========================================= */

        .btn {

            border-radius: 9px;

            font-weight: 600;

            transition: all 0.2s ease;

        }


        .btn-primary {

            background: #4f46e5;

            border-color: #4f46e5;

        }


        .btn-primary:hover {

            background: #4338ca;

            border-color: #4338ca;

            transform: translateY(-1px);

        }


        .btn-secondary {

            background: #6b7280;

            border-color: #6b7280;

        }


        /* =========================================
           UPLOAD BOX
        ========================================= */

        .upload-box {

            border: 2px dashed #c7d2fe !important;

            border-radius: 14px;

            cursor: pointer;

            background:
                linear-gradient(
                    135deg,
                    #f8faff,
                    #eef2ff
                );

            transition: all 0.25s ease;

        }


        .upload-box:hover {

            border-color: #6366f1 !important;

            background: #eef2ff;

            transform: translateY(-1px);

        }


        /* =========================================
           IMAGE PREVIEW
        ========================================= */

        .image-preview-item {

            position: relative;

            width: 150px;

            height: 175px;

            margin: 5px;

            border-radius: 12px;

            overflow: hidden;

            border: 2px solid #e5e7eb;

            background: #ffffff;

            box-shadow:
                0 3px 10px rgba(0, 0, 0, 0.06);

            transition: all 0.2s ease;

        }


        .image-preview-item:hover {

            transform: translateY(-3px);

            box-shadow:
                0 8px 20px rgba(0, 0, 0, 0.10);

        }


        .image-preview-item.primary {

            border: 3px solid #4f46e5;

            box-shadow:
                0 0 0 3px rgba(79, 70, 229, 0.12);

        }


        .image-preview-item img {

            width: 100%;

            height: 130px;

            object-fit: cover;

        }


        .primary-badge {

            position: absolute;

            top: 6px;

            left: 6px;

            background: #4f46e5;

            color: white;

            padding: 4px 8px;

            border-radius: 6px;

            font-size: 10px;

            font-weight: 700;

            z-index: 2;

        }


        .preview-actions {

            height: 42px;

            display: flex;

            align-items: center;

            justify-content: center;

            background: #ffffff;

        }


        .remove-btn {

            position: absolute;

            top: 6px;

            right: 6px;

            background: #dc3545;

            color: white;

            border: none;

            border-radius: 50%;

            width: 26px;

            height: 26px;

            cursor: pointer;

            z-index: 3;

            font-size: 16px;

            line-height: 20px;

        }


        .remove-btn:hover {

            background: #b02a37;

        }


        /* =========================================
           SELECT2
        ========================================= */

        .select2-container {

            width: 100% !important;

        }


        .select2-container--default
        .select2-selection--multiple {

            min-height: 44px;

            border: 1px solid #d1d5db;

            border-radius: 10px;

            padding: 4px 6px;

        }


        .select2-container--default
        .select2-selection--multiple
        .select2-selection__choice {

            background: #eef2ff;

            border: 1px solid #c7d2fe;

            color: #4338ca;

            border-radius: 6px;

            padding: 3px 8px;

        }


        .select2-container--default.select2-container--focus
        .select2-selection--multiple {

            border-color: #6366f1;

            box-shadow:
                0 0 0 3px rgba(99, 102, 241, 0.12);

        }


        /* =========================================
           TABLE
        ========================================= */

        .table {

            margin-bottom: 0;

        }


        .table thead th {

            font-size: 13px;

            text-transform: uppercase;

            letter-spacing: 0.03em;

        }


        .table tbody tr {

            transition: background 0.2s ease;

        }


        .table tbody tr:hover {

            background: #f8fafc;

        }


        /* =========================================
           ALERTS
        ========================================= */

        .alert {

            border-radius: 10px;

        }


        /* =========================================
           TOAST
        ========================================= */

        .toast-container {

            z-index: 9999;

        }


        .toast {

            border: none;

            border-radius: 12px;

            overflow: hidden;

            box-shadow:
                0 10px 30px rgba(0, 0, 0, 0.15);

        }


        .toast-header {

            border: none;

        }


        /* =========================================
           VALIDATION
        ========================================= */

        .invalid-feedback {

            font-size: 13px;

        }


        /* =========================================
           RESPONSIVE
        ========================================= */

        @media (max-width: 768px) {

            .admin-content {

                padding-top: 20px;

            }


            .page-header {

                flex-direction: column;

                align-items: flex-start;

            }


            .page-title {

                font-size: 23px;

            }


            .image-preview-item {

                width: 135px;

                height: 160px;

            }


            .image-preview-item img {

                height: 115px;

            }

        }

    </style>


    {{-- Page-specific styles --}}
    @yield('styles')

    @stack('styles')


    <!-- Vite -->
    @vite([
        'resources/css/app.css',
        'resources/js/app.js'
    ])

</head>


<body>


    {{-- =========================================
         TOP NAVIGATION
    ========================================== --}}

    @include('layouts.navigation')


    {{-- =========================================
         MAIN ADMIN CONTENT
    ========================================== --}}

    <main class="admin-content">

        <div class="container">

            @yield('content')

        </div>

    </main>


    {{-- =========================================
         SUCCESS TOAST
    ========================================== --}}

    <div
        class="toast-container position-fixed top-0 end-0 p-3"
    >

        @if(session('success'))

            <div
                class="toast show"
                role="alert"
                aria-live="assertive"
                aria-atomic="true"
            >

                <div
                    class="toast-header bg-success text-white"
                >

                    <strong class="me-auto">

                        ✓ Success

                    </strong>

                    <button
                        type="button"
                        class="btn-close btn-close-white"
                        data-bs-dismiss="toast"
                    ></button>

                </div>

                <div class="toast-body">

                    {{ session('success') }}

                </div>

            </div>

        @endif


        {{-- ERROR TOAST --}}

        @if(session('error'))

            <div
                class="toast show"
                role="alert"
                aria-live="assertive"
                aria-atomic="true"
            >

                <div
                    class="toast-header bg-danger text-white"
                >

                    <strong class="me-auto">

                        ✕ Error

                    </strong>

                    <button
                        type="button"
                        class="btn-close btn-close-white"
                        data-bs-dismiss="toast"
                    ></button>

                </div>

                <div class="toast-body">

                    {{ session('error') }}

                </div>

            </div>

        @endif

    </div>


    {{-- =========================================
         VALIDATION ERRORS
    ========================================== --}}

    @if($errors->any())

        <div
            class="position-fixed bottom-0 end-0 p-3"
            style="z-index: 9999;"
        >

            <div
                class="alert alert-danger shadow-lg mb-0"
                style="max-width: 400px;"
            >

                <div class="fw-bold mb-2">

                    Please fix the following errors:

                </div>

                <ul class="mb-0 ps-3">

                    @foreach($errors->all() as $error)

                        <li>

                            {{ $error }}

                        </li>

                    @endforeach

                </ul>

            </div>

        </div>

    @endif


    {{-- =========================================
         JAVASCRIPT
    ========================================== --}}

    <script
        src="https://code.jquery.com/jquery-3.6.0.min.js"
    ></script>


    <script
        src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
    ></script>


    <!-- Select2 -->

    <script
        src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"
    ></script>


    {{-- Page-specific scripts --}}

    @stack('scripts')


    {{-- =========================================
         AUTO HIDE TOAST
    ========================================== --}}

    <script>

        document.addEventListener(
            'DOMContentLoaded',
            function () {

                const toastElements =
                    document.querySelectorAll('.toast');

                toastElements.forEach(function (toastElement) {

                    const toast =
                        bootstrap.Toast.getOrCreateInstance(
                            toastElement,
                            {
                                delay: 4000
                            }
                        );

                    toast.show();

                });

            }
        );

    </script>


</body>

</html>