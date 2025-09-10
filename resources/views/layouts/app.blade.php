<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Companies</title>
  @vite(['resources/css/app.css', 'resources/js/app.js'])

</head>
<body class=" bg-[#0d2453]">
  <div class="min-h-full bg-gray-100">
  <nav class="bg-white border-b-2" x-data="{ isUserMenuOpen: false }">
      <div class="px-6 md:px-12 xl:px-8 xl:container mx-auto">
          <div class="relative flex items-center justify-between h-24">
              <div class="flex items-center px-2 lg:px-0">
                  <div class="flex-shrink-0">
                      <a href="{{ route('search.index') }}"> <img class="w-auto max-h-10 lg:max-h-12" src="https://companieshouse.sg/assets/images/logo-black.png" alt="Singapore Company Search - Companies House Singapore">
                      </a>
                  </div>
              </div>
              <div class="block lg:ml-4">
                  <div class="flex items-center">
                      <a href="{{ route('cart.show') }}" class="float-right">Cart</a>
                  </div>
              </div>
          </div>
      </div>
  </nav>
  </div>
  <div class="container mx-auto bg-white mt-8 border rounded">
    @yield('content')
  </div>

    <div class="container p-4 mx-auto text-center text-gray-500 text-sm my-4">
     Developed with ❤️ by Zeeshan Rasool. {{ date('Y') }}
    </div>
</body>
</html>
