<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Dashboard')</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 text-gray-900 font-sans antialiased">
    <div class="min-h-screen flex flex-col">
        <nav class="bg-indigo-600 text-white shadow-lg">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between h-16">
                    <div class="flex items-center">
                        <a href="/" class="font-bold text-xl tracking-tight">Admin System</a>
                        <div class="ml-10 flex items-baseline space-x-4">
                            <a href="/users" class="hover:bg-indigo-500 px-3 py-2 rounded-md text-sm font-medium transition-colors">Pengguna</a>
                            <a href="/ruangan" class="hover:bg-indigo-500 px-3 py-2 rounded-md text-sm font-medium transition-colors">Ruangan</a>
                        </div>
                    </div>
                    <div class="flex items-center">
                        @if(Session::has('user'))
                            <span class="mr-4 text-sm">{{ Session::get('user')['nama'] ?? 'User' }}</span>
                            <form action="{{ route('logout') }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="bg-red-500 hover:bg-red-600 px-3 py-2 rounded-md text-sm font-medium transition-colors">Logout</button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        </nav>
        
        <main class="flex-grow max-w-7xl mx-auto py-6 sm:px-6 lg:px-8 w-full">
            @if(session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            @if($errors->any())
                <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
                    <ul class="list-disc pl-5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @yield('content')
        </main>
    </div>
</body>
</html>
