<?php

namespace App\Http\Controllers;

use App\Models\Routine;
use App\Models\RoutineItem;
use App\Models\UserProduct;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RoutineController extends Controller
{
    /**
     * Display the routines (Morning & Night).
     */
    public function index(Request $request)
    {
        $user = $request->user();

        // Get or create Morning Routine
        $morningRoutine = $user->routines()->firstOrCreate(
            ['routine_type' => 'morning', 'active' => true],
            ['name' => 'Rutinitas Pagi', 'target_time' => '07:00:00']
        );

        // Get or create Night Routine
        $nightRoutine = $user->routines()->firstOrCreate(
            ['routine_type' => 'night', 'active' => true],
            ['name' => 'Rutinitas Malam', 'target_time' => '20:00:00']
        );

        $morningRoutine->load(['items.userProduct.product.category']);
        $nightRoutine->load(['items.userProduct.product.category']);

        return view('routines.index', compact('morningRoutine', 'nightRoutine'));
    }

    /**
     * Mark a routine as completed for today.
     */
    public function complete(Request $request, Routine $routine)
    {
        abort_if($routine->user_id !== $request->user()->id, 403);

        // We can create a simple tracker in a real app. For now, we simulate success.
        $points = $routine->routine_type === 'morning' ? 5 : 10;
        
        app(\App\Services\RewardService::class)->awardPoints(
            $request->user(), 
            $points, 
            ucfirst($routine->routine_type) . ' routine completed'
        );

        return back()->with('success', "Yeay! Rutinitas " . ($routine->routine_type === 'morning' ? 'pagi' : 'malam') . " selesai. Kamu dapat +$points Points!");
    }

    /**
     * Show form to edit a routine.
     */
    public function edit(Routine $routine)
    {
        abort_if($routine->user_id !== auth()->id(), 403);

        $routine->load(['items.userProduct.product.category']);
        
        // Get user's products that aren't in this routine yet
        $existingProductIds = $routine->items->pluck('user_product_id');
        $availableProducts = auth()->user()->products()
            ->where('is_active', true)
            ->whereNotIn('id', $existingProductIds)
            ->with('product.category')
            ->get();

        return view('routines.edit', compact('routine', 'availableProducts'));
    }

    /**
     * Add an item to the routine.
     */
    public function addItem(Request $request, Routine $routine)
    {
        abort_if($routine->user_id !== $request->user()->id, 403);

        $validated = $request->validate([
            'user_product_id' => 'required|exists:user_products,id',
        ]);

        // Ensure user owns this user_product
        $userProduct = UserProduct::where('id', $validated['user_product_id'])
            ->where('user_id', $request->user()->id)
            ->firstOrFail();

        // Calculate next order number
        $nextOrder = $routine->items()->max('order_number') + 1;

        RoutineItem::create([
            'routine_id' => $routine->id,
            'user_product_id' => $userProduct->id,
            'order_number' => $nextOrder,
        ]);

        return back()->with('success', 'Produk berhasil ditambahkan ke rutinitas.');
    }

    /**
     * Remove an item from the routine.
     */
    public function removeItem(Routine $routine, RoutineItem $item)
    {
        abort_if($routine->user_id !== auth()->id() || $item->routine_id !== $routine->id, 403);

        $item->delete();

        return back()->with('success', 'Produk dihapus dari rutinitas.');
    }
}
