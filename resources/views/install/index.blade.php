<!DOCTYPE html>
<html lang="en" class="h-full bg-gray-50">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Install BxCode CMS</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap');

        body {
            font-family: 'Inter', sans-serif;
        }
    </style>
</head>

<body class="h-full flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8 bg-gray-50">
    <div class="max-w-lg w-full space-y-8">
        <div>
            <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
                BxCode CMS Installation
            </h2>
            <p class="mt-2 text-center text-sm text-gray-600">
                Please fill in the details below to install.
            </p>
        </div>

        <div class="bg-white py-8 px-4 shadow sm:rounded-lg sm:px-10">
            @if ($errors->any())
                <div class="mb-4 bg-red-50 border border-red-200 text-red-600 px-4 py-3 rounded relative" role="alert">
                    <strong class="font-bold">Error!</strong>
                    <span class="block sm:inline">
                        @foreach ($errors->all() as $error)
                            {{ $error }}<br>
                        @endforeach
                    </span>
                </div>
            @endif

            <form class="space-y-6" action="{{ route('install.store') }}" method="POST">
                @csrf

                <div>
                    <h3 class="text-lg leading-6 font-medium text-gray-900 border-b border-gray-200 pb-2 mb-4">Database
                        Connection</h3>
                    <div class="space-y-4">
                        <div>
                            <label for="db_name" class="block text-sm font-medium text-gray-700">Database Name</label>
                            <div class="mt-1">
                                <input id="db_name" name="db_name" type="text" required value="{{ old('db_name') }}"
                                    class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            </div>
                        </div>

                        <div>
                            <label for="db_username" class="block text-sm font-medium text-gray-700">Database
                                Username</label>
                            <div class="mt-1">
                                <input id="db_username" name="db_username" type="text" required
                                    value="{{ old('db_username', 'root') }}"
                                    class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            </div>
                        </div>

                        <div>
                            <label for="db_password" class="block text-sm font-medium text-gray-700">Database
                                Password</label>
                            <div class="mt-1">
                                <input id="db_password" name="db_password" type="password"
                                    class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="pt-2">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 border-b border-gray-200 pb-2 mb-4">Admin
                        Account</h3>
                    <div class="space-y-4">
                        <div>
                            <label for="admin_email" class="block text-sm font-medium text-gray-700">Admin Email</label>
                            <div class="mt-1">
                                <input id="admin_email" name="admin_email" type="email" required
                                    value="{{ old('admin_email') }}"
                                    class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            </div>
                        </div>

                        <div>
                            <label for="admin_password" class="block text-sm font-medium text-gray-700">Admin
                                Password</label>
                            <div class="mt-1">
                                <input id="admin_password" name="admin_password" type="password" required minlength="8"
                                    class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="pt-4">
                    <button type="submit"
                        class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Install CMS
                    </button>
                </div>
            </form>
        </div>
    </div>
    <!-- Loading Overlay -->
    <div id="loading-overlay"
        class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden flex items-center justify-center z-50">
        <div class="bg-white p-5 rounded-lg shadow-xl flex flex-col items-center">
            <svg class="animate-spin h-10 w-10 text-indigo-600 mb-3" xmlns="http://www.w3.org/2000/svg" fill="none"
                viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
                </circle>
                <path class="opacity-75" fill="currentColor"
                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                </path>
            </svg>
            <p class="text-gray-900 font-medium">Installing BxCode CMS...</p>
            <p class="text-gray-500 text-sm mt-1">Please wait, this may take a moment.</p>
        </div>
    </div>

    <script>
        document.querySelector('form').addEventListener('submit', function (e) {
            // Show loader
            document.getElementById('loading-overlay').classList.remove('hidden');

            // Disable button and change text
            const btn = this.querySelector('button[type="submit"]');
            btn.disabled = true;
            btn.classList.add('opacity-75', 'cursor-not-allowed');
            btn.innerText = 'Installing...';
        });
    </script>
</body>

</html>