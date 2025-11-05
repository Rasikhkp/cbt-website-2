<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description"
        content="Online exam system with question banks, automated grading, and role-based access for committees and examinees.">

    <title>CBT Website</title>

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans antialiased">
    <div class="min-h-screen bg-gray-100">
        @include('layouts.navigation')

        <!-- Page Heading -->
        @isset($header)
            <header class="bg-white shadow">
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    {{ $header }}
                </div>
            </header>
        @endisset

        <!-- Page Content -->
        <main>
            {{ $slot }}
        </main>

        <div id="customConfirmModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center hidden z-50">
          <div id="customConfirmCard" class="bg-white rounded-lg shadow-lg w-[28rem] p-6 modal-enter">
            <div class="text-left">
              <h3 id="customConfirmTitle" class="text-lg font-semibold text-gray-900">
                Are you absolutely sure?
              </h3>
              <p id="customConfirmMessage" class="text-sm text-gray-500 mt-2">
                This action cannot be undone. This will permanently delete your account and remove your data from our servers.
              </p>
            </div>
            <div class="flex justify-end gap-3 mt-6">
              <button id="customConfirmCancelBtn"
                      class="px-4 py-2 bg-white border border-gray-300 text-gray-700 text-sm font-medium rounded-md shadow-sm hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-200">
                Cancel
              </button>
              <button id="customConfirmOkBtn"
                      class="px-4 py-2 bg-blue-500 text-white text-sm font-medium rounded-md shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-300">
                Continue
              </button>
            </div>
          </div>
        </div>
    </div>
</body>

</html>
