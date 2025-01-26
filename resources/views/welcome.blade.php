<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>CRUD</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
        <!-- 
            <script src="https://unpkg.com/@tailwindcss/browser@4"></script>
        -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>

    <body>
        <div id="app"></div>
    </body>
</html>
