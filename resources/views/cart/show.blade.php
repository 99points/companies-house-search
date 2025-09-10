@extends('layouts.app')
@section('content')
<div class="bg-white shadow-md rounded-lg p-4">

  <h1 class="text-2xl font-bold">Cart</h1>
  @if(empty($items))
    <div>Your cart is empty</div>
  @else
    <table class="w-full mt-4">
      <thead><tr align="left"><th>Item</th><th>Qty</th><th>Price</th><th>Total</th><th></th></tr></thead>
      <tbody>
        @foreach($items as $k => $i)
          <tr class="border-t">
            <td class="py-3 pb-2 text-sm">{{ $i['name'] }} <span class="text-sm text-gray-500">({{ $i['country'] }})</span></td>
            <td class="text-sm">{{ $i['qty'] }}</td>
            <td class="text-sm">{{ number_format($i['price'],2) }}</td>
            <td class="text-sm">{{ number_format($i['price'] * $i['qty'],2) }}</td>
            <td class="text-sm ">
              <form method="post" action="{{ route('cart.remove') }}">
                @csrf
                <input type="hidden" name="key" value="{{ $k }}">
                <button class="text-red-600 cursor-pointer">Remove</button>
              </form>
            </td>
          </tr>
        @endforeach
      </tbody>
    </table>

    <div class="mt-4 font-bold">Total: {{ number_format($total,2) }}</div>
  @endif
</div>
@endsection
