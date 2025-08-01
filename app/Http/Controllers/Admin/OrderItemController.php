<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\OrderItem;
use App\Events\TableStatusUpdated;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Events\OrderStatusUpdated;

class OrderItemController extends Controller
{
    public function deliver(OrderItem $orderItem)
    {
        try {
            if ($orderItem->quantity_delivered < $orderItem->quantity) {
                $orderItem->increment('quantity_delivered');
            }

            $order = $orderItem->order->load('orderItems');

            $allItemsDelivered = $order->orderItems->every(fn($item) => $item->quantity <= $item->quantity_delivered);

            if ($allItemsDelivered) {
                $order->update(['status' => 'completed']);
            }
            
            OrderStatusUpdated::dispatch($order);
            
            if ($order->diningTable) {
                TableStatusUpdated::dispatch($order->dining_table_id); 
            }

            return response()->json(['success' => true, 'message' => 'Status pengantaran diperbarui.']);

        } catch (\Exception $e) {
            Log::error("Gagal update status antar: " . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan di server.'], 500);
        }
    }
}