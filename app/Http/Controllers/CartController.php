<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CartController extends Controller
{
    protected $sessionKey = 'cart.items';

    public function show()
    {
        $items = session($this->sessionKey, []);
        $total = array_reduce($items, fn($s,$i) => $s + ($i['price'] * $i['qty']), 0.0);
        return view('cart.show', ['items' => $items, 'total' => $total]);
    }

    public function add(Request $r)
    {
        $payload = $r->validate([
            'country' => 'required|string',
            'company_id' => 'required',
            'report_id' => 'required',
            'name' => 'required|string',
            'price' => 'required|numeric',
            'qty' => 'sometimes|integer|min:1'
        ]);
        $items = session($this->sessionKey, []);
        // simple unique key
        $key = join(':', [$payload['country'],$payload['company_id'],$payload['report_id']]);
        if (isset($items[$key])) {
            $items[$key]['qty'] += $payload['qty'] ?? 1;
        } else {
            $items[$key] = [
                'id' => $key,
                'country' => $payload['country'],
                'company_id' => $payload['company_id'],
                'report_id' => $payload['report_id'],
                'name' => $payload['name'],
                'price' => (float) $payload['price'],
                'qty' => $payload['qty'] ?? 1,
            ];
        }
        session([$this->sessionKey => $items]);
        return redirect()->back()->with('success', 'Added to cart');
    }

    public function remove(Request $r)
    {
        $key = $r->input('key');
        $items = session($this->sessionKey, []);
        if (isset($items[$key])) {
            unset($items[$key]);
            session([$this->sessionKey => $items]);
        }
        return redirect()->back();
    }
}
