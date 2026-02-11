<!DOCTYPE html>
<html lang="en" class="h-full bg-gray-50">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Install - BxCode CMS</title>
    <link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>🚀</text></svg>">
    
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');

        body {
            font-family: 'Inter', sans-serif;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-slide-up {
            animation: slideUp 0.6s ease-out;
        }

        .stagger-1 { animation-delay: 0.1s; }
        .stagger-2 { animation-delay: 0.2s; }
        .stagger-3 { animation-delay: 0.3s; }
    </style>
</head>

<body class="h-full flex flex-col justify-center py-12 sm:px-6 lg:px-8">
    <div class="sm:mx-auto sm:w-full sm:max-w-md">
        <!-- Logo -->
        <div class="flex justify-center animate-slide-up">
            <div class="h-16 w-16 bg-gradient-to-br from-indigo-600 to-purple-600 rounded-2xl flex items-center justify-center text-white font-bold text-3xl shadow-lg transform hover:scale-105 transition-transform duration-200">
                B
            </div>
        </div>

        <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900 tracking-tight animate-slide-up stagger-1">
            Welcome to BxCode CMS
        </h2>
        <p class="mt-2 text-center text-sm text-gray-600 animate-slide-up stagger-2">
            Let's get your CMS set up in just a few steps
        </p>

        <!-- Progress Indicator -->
        <div class="mt-6 flex justify-center space-x-2 animate-slide-up stagger-2">
            <div class="h-1.5 w-12 bg-indigo-600 rounded-full"></div>
            <div class="h-1.5 w-12 bg-gray-200 rounded-full"></div>
            <div class="h-1.5 w-12 bg-gray-200 rounded-full"></div>
        </div>
    </div>

    <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
        <div class="bg-white py-8 px-4 shadow-lg sm:rounded-xl sm:px-10 border border-gray-100 animate-slide-up stagger-3">

            @if ($errors->any())
                <div class="mb-6 bg-red-50 border-l-4 border-red-400 p-4 rounded-md">
                    <div class="flex">
                        <div class="shrink-0">
                            <svg class="h-5 w-5 text-red-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                                fill="currentColor">
                                <path fill-rule="evenodd"
                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                    clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-red-800">Installation Error</h3>
                            <div class="mt-2 text-sm text-red-700">
                                <ul class="list-disc pl-5 space-y-1">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <form action="{{ route('install.store') }}" method="POST" class="space-y-6">
                @csrf

                <!-- Database Section -->
                <div class="bg-gray-50 p-5 rounded-lg border border-gray-200">
                    <div class="flex items-center mb-4">
                        <div class="flex-shrink-0">
                            <svg class="h-6 w-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4"></path>
                            </svg>
                        </div>
                        <h3 class="ml-3 text-lg font-semibold text-gray-900">Database Configuration</h3>
                    </div>

                    <div class="space-y-4">
                        <div>
                            <label for="db_name" class="block text-sm font-medium text-gray-700">
                                Database Name
                            </label>
                            <div class="mt-1">
                                <input id="db_name" type="text" name="db_name" required
                                    value="{{ old('db_name') }}"
                                    class="appearance-none block w-full bg-white p-3 border border-gray-300 rounded-md placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm transition duration-150 ease-in-out"
                                    placeholder="e.g. laravel_cms">
                            </div>
                        </div>

                        <div>
                            <label for="db_username" class="block text-sm font-medium text-gray-700">
                                Database Username
                            </label>
                            <div class="mt-1">
                                <input id="db_username" type="text" name="db_username" required
                                    value="{{ old('db_username', 'root') }}"
                                    class="appearance-none block w-full bg-white p-3 border border-gray-300 rounded-md placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm transition duration-150 ease-in-out"
                                    placeholder="root">
                            </div>
                        </div>

                        <div>
                            <label for="db_password" class="block text-sm font-medium text-gray-700">
                                Database Password
                            </label>
                            <div class="mt-1">
                                <input id="db_password" type="password" name="db_password"
                                    class="appearance-none block w-full bg-white p-3 border border-gray-300 rounded-md placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm transition duration-150 ease-in-out"
                                    placeholder="Leave blank if no password">
                            </div>
                            <p class="mt-1 text-xs text-gray-500">Leave empty if your database has no password</p>
                        </div>
                    </div>
                </div>

                <!-- Admin Section -->
                <div class="bg-gradient-to-br from-indigo-50 to-purple-50 p-5 rounded-lg border border-indigo-100">
                    <div class="flex items-center mb-4">
                        <div class="flex-shrink-0">
                            <svg class="h-6 w-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                        </div>
                        <h3 class="ml-3 text-lg font-semibold text-gray-900">Admin Account</h3>
                    </div>

                    <div class="space-y-4">
                        <div>
                            <label for="admin_email" class="block text-sm font-medium text-gray-700">
                                Admin Email
                            </label>
                            <div class="mt-1">
                                <input id="admin_email" type="email" name="admin_email" required
                                    value="{{ old('admin_email') }}"
                                    class="appearance-none block w-full bg-white p-3 border border-gray-300 rounded-md placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm transition duration-150 ease-in-out"
                                    placeholder="admin@example.com">
                            </div>
                        </div>

                        <div x-data="{ show: false }">
                            <label for="admin_password" class="block text-sm font-medium text-gray-700">
                                Admin Password
                            </label>
                            <div class="mt-1 relative">
                                <input id="admin_password" :type="show ? 'text' : 'password'" name="admin_password" required minlength="8"
                                    class="appearance-none block w-full bg-white p-3 border border-gray-300 rounded-md placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm transition duration-150 ease-in-out pr-10"
                                    placeholder="Minimum 8 characters">
                                <button type="button" @click="show = !show"
                                    class="absolute inset-y-0 right-0 px-3 flex items-center cursor-pointer text-gray-400 hover:text-gray-600 focus:outline-none">
                                    <svg x-show="!show" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20"
                                        fill="currentColor">
                                        <path d="M10 12a2 2 0 100-4 2 2 0 000 4z" />
                                        <path fill-rule="evenodd"
                                            d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z"
                                            clip-rule="evenodd" />
                                    </svg>
                                    <svg x-show="show" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20"
                                        fill="currentColor" style="display: none;">
                                        <path fill-rule="evenodd"
                                            d="M3.707 2.293a1 1 0 00-1.414 1.414l14 14a1 1 0 001.414-1.414l-1.473-1.473A10.014 10.014 0 0019.542 10C18.268 5.943 14.478 3 10 3a9.958 9.958 0 00-4.512 1.074l-1.78-1.781zm4.261 4.26l1.514 1.515a2.003 2.003 0 012.45 2.45l1.514 1.514a4 4 0 00-5.478-5.478z"
                                            clip-rule="evenodd" />
                                        <path
                                            d="M12.454 16.697L9.75 13.992a4 4 0 01-3.742-3.742L2.335 6.578A9.98 9.98 0 00.458 10c1.274 4.057 5.064 7 9.542 7 .847 0 1.669-.105 2.454-.303z" />
                                    </svg>
                                </button>
                            </div>
                            <p class="mt-1 text-xs text-gray-500">Choose a strong password with at least 8 characters</p>
                        </div>
                    </div>
                </div>

                <div>
                    <button type="submit"
                        class="group relative w-full flex justify-center py-3 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all duration-150 ease-in-out transform hover:scale-[1.02]">
                        <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                            <svg class="h-5 w-5 text-indigo-300 group-hover:text-indigo-200" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-8.707l-3-3a1 1 0 00-1.414 0l-3 3a1 1 0 001.414 1.414L9 9.414V13a1 1 0 102 0V9.414l1.293 1.293a1 1 0 001.414-1.414z" clip-rule="evenodd" />
                            </svg>
                        </span>
                        Install & Create Admin Account
                    </button>
                </div>
            </form>
        </div>

        <!-- Footer -->
        <p class="mt-8 text-center text-xs text-gray-500">
            © {{ date('Y') }} BxCode CMS. All rights reserved.
        </p>
    </div>
</body>

</html>