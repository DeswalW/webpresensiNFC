<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin - Sistem Presensi</title>
    
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-white flex items-center justify-center p-4">
    <div class="w-full max-w-md">
        <div class="bg-white rounded-2xl shadow-2xl overflow-hidden">
            <div class="bg-gradient-to-br from-primary-green to-green-800 text-white p-8 text-center">
                <div class="mb-6">
                    <img src="{{ asset('img/logosmk.png') }}" alt="Logo SMK" class="w-20 h-20 mx-auto">
                </div>
                <h3 class="text-2xl font-bold mb-2">Sistem Presensi</h3>
                <p class="text-green-200">Admin Panel</p>
            </div>
            
            <div class="p-8">
                @if($errors->any())
                    <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg">
                        <ul class="list-disc list-inside">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                
                <form method="POST" action="{{ route('admin.login') }}" class="space-y-6">
                    @csrf
                    
                    <div>
                        <label for="username" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-user mr-2"></i>Username
                        </label>
                        <input type="text" 
                               class="form-input @error('username') border-red-500 @enderror" 
                               id="username" 
                               name="username" 
                               value="{{ old('username') }}" 
                               required 
                               autofocus
                               placeholder="Masukkan username">
                        @error('username')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-lock mr-2"></i>Password
                        </label>
                        <input type="password" 
                               class="form-input @error('password') border-red-500 @enderror" 
                               id="password" 
                               name="password" 
                               required
                               placeholder="Masukkan password">
                        @error('password')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div class="flex items-center">
                        <input type="checkbox" 
                               class="h-4 w-4 text-primary-green focus:ring-primary-green border-gray-300 rounded" 
                               id="remember" 
                               name="remember">
                        <label for="remember" class="ml-2 block text-sm text-gray-700">
                            Ingat saya
                        </label>
                    </div>
                    
                    <div>
                        <button type="submit" class="w-full bg-gradient-to-r from-primary-green to-green-700 hover:from-green-700 hover:to-primary-green text-white font-semibold py-3 px-4 rounded-lg transition-all duration-200 transform hover:-translate-y-1 hover:shadow-lg">
                            <i class="fas fa-sign-in-alt mr-2"></i>Login
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
