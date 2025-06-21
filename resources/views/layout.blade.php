<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Budget Bee')</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">
    <link rel="icon" href="{{ asset('images/logo.png') }}" type="">
    <style>
        body {
            font-family: 'Poppins';
            background-color: #ffcd3f
        }
    </style>
</head>

<body>
    {{-- @include('include.header') --}}
    @yield('content')
    
    <script src="https://cdn.tailwindcss.com"></script>
</body>

</html>