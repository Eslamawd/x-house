<?php

namespace App\Http\Controllers;

use App\Models\Buyer;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    //

     public function getRevenue()
    {
        $revenue = Order::sum('total_price');
        return response()->json(['count' => $revenue]);
    }


        public function count()
    {
        $count = Order::count();
        return response()->json(['count' => $count]);
    }

    public function orders()
    {
        $orders = Order::with( ['buyer','items.product'])->paginate(6);
        return response()->json(['orders' => $orders]);
    }


public function store(Request $request)
{
    $validated = $request->validate([
        'username' => 'required|string',
        'phone' => 'required|string',
        'location' => 'nullable|string',
        'total' => 'nullable|numeric',
        'cart' => 'required|array',
        'cart.*.product_id' => 'required|integer|exists:products,id',
        'cart.*.quantity' => 'required|integer|min:1',
    ]);

    // 🔍 التأكد من وجود المستخدم أو إنشاءه
    $user = Buyer::firstOrCreate(
        [ 'phone' => $validated['phone']],
        [
            'username' => $validated['username']
        ]
    );

        $totalPrice = 0;

    foreach ($validated['cart'] as $item) {
        $product = Product::findOrFail($item['product_id']);
        $totalPrice += $product->price * $item['quantity'];
    }

    // ✅ إنشاء الطلب الرئيسي
    $order = $user->orders()->create([
        'total_price' => $totalPrice,
        'location' => $validated['location'] ?? null,
    ]);

    // ✅ إنشاء العناصر المرتبطة بالطلب
    foreach ($validated['cart'] as $item) {
        $order->items()->create([
            'product_id' => $item['product_id'],
            'quantity' => $item['quantity'],
        ]);
    }

    return response()->json(['message' => 'Created New Order'], 200);
}

public function update (Request $request, $id) {
    $valitated = $request->validate([
        'status' => 'required|string'
    ]);
    $order = Order::findOrFail($id);
    $order->update($valitated);
    response()->json(['message' => 'updated status completed']);

}


}
