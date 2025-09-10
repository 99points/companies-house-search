@extends('layouts.app')
@section('content')
<div class="bg-white shadow-md rounded-lg p-4">

@if(session('success')) <div class="bg-green-100 p-2 mb-2">{{ session('success') }}</div>@endif

  <h1 class="text-2xl font-bold">{{ $company->name }} <span class="text-sm text-gray-500">({{ $company->country }})</span></h1>
  <div class="space-y-8 mt-8">
    <div class="">
      <div class="flex justify-between items-center">
        <div>
          <h2 class="text-1xl font-bold leading-6 text-gray-900">Company information</h2>
          <p class="mt-1 max-w-2xl text-sm text-gray-700">General information about {{ $company->name }}</p>
        </div>
      </div>
      <div class="mt-5 border-t border-gray-200">
        <dl class="sm:divide-y sm:divide-gray-200">
          <div class="py-2 sm:grid sm:grid-cols-3 sm:gap-4 sm:py-5">
            <dt class="text-sm font-medium text-gray-500">
              <h3>Registered name</h3>
            </dt>
            <dd class="mt-1 text-sm text-gray-900 sm:col-span-2 sm:mt-0"> {{ $company->name }} </dd>
          </div>
          <div class="py-2 sm:grid sm:grid-cols-3 sm:gap-4 sm:py-5">
            <dt class="text-sm font-medium text-gray-500">
              <h3>Slug</h3>
            </dt>
            <dd class="mt-1 text-sm text-gray-900 sm:col-span-2 sm:mt-0"> {{ $company->slug }} </dd>
          </div>
          <div class="py-2 sm:grid sm:grid-cols-3 sm:gap-4 sm:py-5">
            <dt class="text-sm font-medium text-gray-500">
              <h3>Registration No.</h3>
            </dt>
            <dd class="mt-1 text-sm text-gray-900 sm:col-span-2 sm:mt-0"> {{ $company->registration_number }} </dd>
          </div>
          <div class="py-2 sm:grid sm:grid-cols-3 sm:gap-4 sm:py-5">
            <dt class="text-sm font-medium text-gray-500">
              <h3>Address</h3>
            </dt>
            <dd class="mt-1 text-sm text-gray-900 sm:col-span-2 sm:mt-0"> {{ $company->address }} </dd>
          </div>
        </dl>
      </div>
    </div>

  </div>

  <h2 class="mt-6 text-xl font-bold">Available Reports</h2>
  <table class="w-full mt-2">
    <thead><tr align="left"><th>Name</th><th>Price</th><th></th></tr></thead>
    <tbody>
      @foreach($reports as $r)
        <tr class="border-t">
          <td>{{ $r['name'] }}</td>
          <td>{{ number_format($r['price'],2) }}</td>
          <td align="right" class="p-2">
            <form method="post" action="{{ route('cart.add') }}">
              @csrf
              <input type="hidden" name="country" value="{{ $company->country }}">
              <input type="hidden" name="company_id" value="{{ $company->id }}">
              <input type="hidden" name="report_id" value="{{ $r['report_id'] }}">
              <input type="hidden" name="name" value="{{ $r['name'] }}">
              <input type="hidden" name="price" value="{{ $r['price'] }}">
              <button class="w-full max-w-[160px] px-2 py-1 pt-3 text-base font-medium text-white
                      bg-blue-600 border border-transparent rounded-md shadow-sm
                      hover:bg-blue-700 focus:outline-none focus:ring-2
                      focus:ring-offset-2 focus:ring-blue-500
                      disabled:bg-gray-400 disabled:text-gray-200
                      disabled:cursor-not-allowed transition-all duration-200 ease-in-out">

                  <div class="inline-flex items-center justify-center">
                      <svg class="w-5 h-5 mr-3 -ml-1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                          <path d="M10 19.5c0 .829-.672 1.5-1.5 1.5s-1.5-.671-1.5-1.5c0-.828.672-1.5 1.5-1.5s1.5.672 1.5 1.5zm3.5-1.5c-.828 0-1.5.671-1.5 1.5s.672 1.5 1.5 1.5 1.5-.671 1.5-1.5c0-.828-.672-1.5-1.5-1.5zm1.336-5l1.977-7h-16.813l2.938 7h11.898zm4.969-10l-3.432 12h-12.597l.839 2h13.239l3.474-12h1.929l.743-2h-4.195z">
                          </path>
                      </svg>
                      <span>Add to cart</span>
                  </div>
              </button>
            </form>
          </td>
        </tr>
      @endforeach
    </tbody>
  </table>
</div>
@endsection
