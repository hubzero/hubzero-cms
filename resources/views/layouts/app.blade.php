<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Hubzero')</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen flex flex-col bg-gray-50 text-gray-900">
    <header class="bg-white border-b border-gray-200">
        <div class="max-w-5xl mx-auto px-6 py-4 flex items-center justify-between">
            <a href="/" class="text-xl font-semibold text-gray-900">Hubzero</a>
            <nav class="flex gap-4 text-sm text-gray-600">
                <a href="/status" class="hover:text-gray-900">Status</a>
            </nav>
        </div>
    </header>

    <main class="flex-1 max-w-5xl mx-auto w-full px-6 py-8">
        @yield('content')
    </main>

    <footer class="bg-white border-t border-gray-200">
        <div class="max-w-5xl mx-auto px-6 py-4 text-sm text-gray-500">
            &copy; {{ date('Y') }} Purdue University. All Rights Reserved.
        </div>
    </footer>
</body>
</html>
