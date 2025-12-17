<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Inventory System</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            background-color: #e0e5ec;
        }
        .neumorphic {
            border-radius: 20px;
            background: #e0e5ec;
            box-shadow:  9px 9px 16px rgb(163,177,198,0.6), -9px -9px 16px rgba(255,255,255, 0.5);
        }
        .neumorphic-inset {
            border-radius: 10px;
            background: #e0e5ec;
            box-shadow: inset 6px 6px 10px 0 rgba(163,177,198, 0.7), inset -6px -6px 10px 0 rgba(255,255,255, 0.8);
        }
        .neumorphic-btn:active {
            box-shadow: inset 6px 6px 10px 0 rgba(163,177,198, 0.7), inset -6px -6px 10px 0 rgba(255,255,255, 0.8);
        }
    </style>
</head>
<body class="h-screen flex items-center justify-center font-sans">

    <div class="neumorphic p-10 w-full max-w-md mx-4">
        <h2 class="text-3xl font-bold text-gray-700 text-center mb-8">Login Inventory</h2>
        
        <form action="{{ route('login') }}" method="POST" class="space-y-6">
            @csrf
            
            <div>
                <label for="email" class="block text-gray-600 mb-2 pl-2 font-medium">Email Address</label>
                <input type="email" name="email" id="email" required 
                       class="neumorphic-inset w-full p-4 outline-none text-gray-700 focus:ring-2 focus:ring-blue-400/50 transition-all border-none"
                       placeholder="admin@inventory.com" value="{{ old('email') }}">
                @error('email')
                    <p class="text-red-500 text-sm mt-2 pl-2">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="password" class="block text-gray-600 mb-2 pl-2 font-medium">Password</label>
                <input type="password" name="password" id="password" required 
                       class="neumorphic-inset w-full p-4 outline-none text-gray-700 focus:ring-2 focus:ring-blue-400/50 transition-all border-none"
                       placeholder="••••••••">
            </div>

            <div class="pt-4">
                <button type="submit" 
                        class="neumorphic w-full py-4 text-blue-600 font-bold uppercase tracking-wider hover:text-blue-700 focus:outline-none neumorphic-btn transition-all">
                    Sign In
                </button>
            </div>
        </form>

        <p class="text-center text-gray-500 text-sm mt-8">
            Gunakan Role: Admin, Staff, atau Gudang
        </p>
    </div>

</body>
</html>
