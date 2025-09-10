@extends('layouts.app')
@section('content')

<div class="bg-white shadow-md rounded-lg p-4">

  <form method="get" action="{{ route('search.index') }}" class="flex items-center w-full space-x-2">
    
    <div class="relative flex-grow">
      <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
        <!-- Search icon -->
        <svg xmlns="http://www.w3.org/2000/svg" 
             class="h-5 w-5" 
             fill="none" 
             viewBox="0 0 24 24" 
             stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                d="M21 21l-4.35-4.35m0 0A7.5 7.5 0 1010.5 18a7.5 7.5 0 006.15-1.35z" />
        </svg>
      </span>
      <input 
        id="search-input"
        name="q"
        value="{{ $q }}"
        autocomplete="off"
        placeholder="Search your Property here..."
        class="border rounded-md p-3 pl-10 w-full focus:outline-none focus:ring-2 focus:ring-blue-500"
      >
    </div>
    
    <button class="bg-gray-800 hover:bg-gray-900 text-white font-semibold px-6 py-3 rounded-md">
      SEARCH
    </button>
  </form>

  <!-- Autocomplete Dropdown -->
  <ul id="autocomplete-results" class="border bg-white shadow-lg border border-gray-300 rounded-sm mt-2 w-full hidden">
    <!-- JS will fill -->
  </ul>
</div>

<div class="text-xl font-semibold p-4 pb-0 pt-5">
    Search Results
</div>

<div class="p-5">
  @if($results->isEmpty())
    <div>No results</div>
  @else
    <table class="min-w-full border border-gray-200 rounded-lg overflow-hidden shadow-sm">
      <thead class="bg-gray-50">
        <tr>
          <th class="text-left px-4 py-3 text-sm font-semibold text-gray-600">Company Name</th>
          <th class="text-left px-4 py-3 text-sm font-semibold text-gray-600">Country</th>
          <th class="text-left px-4 py-3 text-sm font-semibold text-gray-600">Registration</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-gray-100">
        @foreach($results as $c)
        <tr class=" hover:bg-gray-50 cursor-pointer" data-id="${s.id}" data-country="${s.country}" onclick="window.location='{{ url('company/'.$c->country.'/'.$c->id) }}'">
          <td class="px-4 py-3 flex max-w-[300px] items-center space-x-3">
     
            <span class="text-sm text-gray-800 leading-tight">{{$c->name}}</span>
          </td>
          <td class="px-4 py-3 text-gray-600">{{ $c->country }}</td>
          <td class="px-4 py-3 text-gray-500">{{ $c->registration_number }}</td>
        </tr>
        @endforeach
      </tbody>
    </table>
    @php
      $lastPage = ceil($total / $perPage);
    @endphp
    @if($lastPage > 1)
      <div class="mt-4 flex justify-center space-x-2">
        @if($page > 1)
          <a href="{{ route('search.index', ['q' => $q, 'page' => $page - 1]) }}"
            class="px-4 py-2 bg-gray-200 rounded hover:bg-gray-300">Previous</a>
        @endif

        <span class="px-4 py-2">Page {{ $page }} of {{ $lastPage }}</span>

        @if($page < $lastPage)
          <a href="{{ route('search.index', ['q' => $q, 'page' => $page + 1]) }}"
            class="px-4 py-2 bg-gray-200 rounded hover:bg-gray-300">Next</a>
        @endif
      </div>
    @endif
  @endif
</div>

<script>
const input = document.getElementById('search-input');
const resultsBox = document.getElementById('autocomplete-results');
const autocompleteUrl = "{{ url('search/autocomplete') }}";
const baseUrl = "{{ url('') }}";

let timer;
input.addEventListener('input', function() {

    clearTimeout(timer);

    const query = this.value;
    if (!query) {
        resultsBox.innerHTML = '';
        resultsBox.classList.add('hidden');
        return;
    }

    timer = setTimeout(async () => {
        const res = await fetch(`${autocompleteUrl}?q=${encodeURIComponent(query)}`);

        const suggestions = await res.json();

        if (!suggestions.length) {
            resultsBox.innerHTML = '';
            resultsBox.classList.add('hidden');
            return;
        }


        resultsBox.innerHTML = suggestions.map(s => `
          <li class="flex items-center p-3 hover:bg-gray-50 cursor-pointer border-b border-gray-300 last:border-none" 
              data-id="${s.id}" data-country="${s.country}">
              <span class="text-gray-500 mr-3">
                <!-- Property icon -->
                <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24" class="w-5 h-5">
                  <path d="M3 21V3a1 1 0 0 1 1-1h5a1 1 0 0 1 1 1v3h4V3a1 1 0 0 1 1-1h5a1 1 0 0 1 1 1v18h-6v-6h-6v6H3Zm6-2v-4H5v4h4Zm10 0v-4h-4v4h4ZM9 9H5v4h4V9Zm10 0h-4v4h4V9Z"/>
                </svg>

              </span>
              <div>
                <div class="text-sm text-gray-800">${s.name}</div>
                <div class="text-sm text-gray-500">${s.country}</div>
              </div>
          </li>
        `).join('');

        resultsBox.classList.remove('hidden');
    }, 200); 
});

// Handle click on suggestion
resultsBox.addEventListener('click', function(e) {
    const li = e.target.closest('li');
    if (!li) return;
    input.value = li.textContent.trim();
    resultsBox.innerHTML = '';
    resultsBox.classList.add('hidden');
    // redirectt to
    window.location.href = `${baseUrl}/company/${li.dataset.country}/${li.dataset.id}`;
});
</script>

@endsection
