
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description"
        content="Online exam system with question banks, automated grading, and role-based access for committees and examinees.">

    <title>Login - BRC CBT System</title>

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        .card-lift {
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.05), 0 4px 6px -2px rgba(0, 0, 0, 0.03);
            transition: all 0.3s ease-in-out;
        }
        .card-lift:hover {
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            transform: translateY(-2px);
        }
        /* Custom color based on the logo's deep red/maroon */
        .bg-primary {
            background-color: #8B0000; /* Deep Red/Maroon for an elegant touch */
        }
        .text-primary {
            color: #8B0000;
        }
        .border-primary {
            border-color: #8B0000;
        }
    </style>
</head>

<body class="bg-gray-50 flex flex-col min-h-screen items-center justify-center p-4">

    <div class="w-full max-w-md">
        <div class="flex flex-col items-center mb-8">
            <img src="{{ asset('images/logo-with-text.png') }}" alt="Beasiswa Cakrawala Logo" class="h-32 mb-0">
        </div>

        <div class="bg-white p-8 rounded-xl card-lift w-full">

            <h2 class="text-2xl font-bold text-gray-700 mb-1">Welcome!</h2>
            <p class="text-gray-700 mb-6 font-medium text-lg">
                Please login to continue
            </p>

            <form method="POST" action="{{ route('login') }}">
                @csrf
                <div class="mb-4">
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary transition duration-150 ease-in-out"
                           placeholder="you@example.com" required>
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </div>

                <div class="mb-6">
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                    <input type="password" id="password" name="password"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary transition duration-150 ease-in-out"
                           placeholder="••••••••" required>
                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                </div>

                <div class="flex items-center justify-between mb-6">
                    <div class="flex items-center">
                        <input id="remember-me" name="remember-me" type="checkbox"
                               class="h-4 w-4 text-primary rounded border-gray-300 focus:ring-primary">
                        <label for="remember-me" class="ml-2 block text-sm text-gray-600">
                            Remember me
                        </label>
                    </div>
                </div>

                <button type="submit"
                        class="w-full bg-primary text-white font-semibold py-2.5 rounded-lg hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2 transition duration-150 ease-in-out shadow-md hover:shadow-lg">
                    LOG IN
                </button>
            </form>

            @if (\App\Models\Setting::where('key', 'registration_enabled')->value('value') === '1')
                <div class="mt-4 text-center">
                    <p class="text-sm text-gray-600">
                        Don't have an account?
                        <a href="{{ route('register') }}" class="font-medium text-primary hover:text-red-700">
                            Register
                        </a>
                    </p>
                </div>
            @endif
        </div>
    </div>

    <footer class="mt-8 text-center text-sm text-gray-500">
        <p>&copy; <span id="year"></span> Bintan Resorts • All rights reserved.</p>
    </footer>

</body>
</html>
