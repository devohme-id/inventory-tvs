<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use App\Models\PickingTask;
use App\Models\Product;
use App\Models\SalesOrder;
use App\Models\SoItem;
use App\Models\StorageBin; // Import SoItem
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PickingTaskController extends Controller
{
    /**
     * Halaman List Antrian PICKING (Dengan Tab Riwayat)
     */
    public function index(Request $request)
    {
        $tab = $request->get('tab', 'list'); // Default: list

        if ($tab === 'history') {
            // Riwayat: SO yang sudah melewati tahap Picking (Picked/Packed/Shipped)
            $pickingOrders = SalesOrder::whereIn('status', ['Picked', 'Packed', 'Shipped'])
                ->with(['items.product', 'items.pickingTask.storagebins'])
                ->orderBy('updated_at', 'desc') // Terakhir selesai paling atas
                ->paginate(10);
        } else {
            // List: Antrian Picking (Pending)
            $pickingOrders = SalesOrder::where('status', 'Pending')
                ->with(['items.product', 'items.pickingTask.storagebins'])
                ->orderBy('order_date', 'asc') // FIFO: Order lama duluan
                ->paginate(10); 
        }

        return view('picking.index', compact('pickingOrders', 'tab'));
    }

    public function processSO($soId)
    {
        $salesOrder = SalesOrder::findOrFail($soId);
        $tasks = PickingTask::with(['soitems.product', 'storagebins'])
            ->whereHas('soitems', function($q) use ($soId) {
                $q->where('so_id', $soId);
            })
            ->orderBy('status', 'asc')
            ->get();

        $total = $tasks->count();
        $done = $tasks->where('status', 'Picked')->count();
        $progress = $total > 0 ? round(($done / $total) * 100) : 0;

        // PASTIKAN INI: 'picking.process', BUKAN 'picking_tasks.process'
        return view('picking.process', compact('salesOrder', 'tasks', 'progress'));
    }

    // ... (Method scanItem, create, store, edit, update, destroy tetap sama) ...
    
    public function scanItem(Request $request, $soId)
    {
        $request->validate(['barcode' => 'required|string']);
        $barcode = $request->barcode;
        $product = Product::where('part_number', $barcode)->first();
        if (!$product) return redirect()->back()->with('error', 'Barcode tidak valid.');

        $task = PickingTask::whereHas('soitems', function($q) use ($soId, $product) {
                $q->where('so_id', $soId)->where('product_id', $product->id);
            })->where('status', 'Pending')->first();

        if (!$task) return redirect()->back()->with('error', 'Item tidak ada di daftar picking atau sudah diambil.');

        $task->update(['status' => 'Picked', 'operator_id' => Auth::id(), 'picked_at' => Carbon::now()]);
        $task->soitems->increment('quantity_picked', $task->quantity_to_pick);

        $pendingCount = PickingTask::whereHas('soitems', function($q) use ($soId) {
            $q->where('so_id', $soId);
        })->where('status', 'Pending')->count();

        if ($pendingCount == 0) {
            SalesOrder::where('id', $soId)->update(['status' => 'Picked']);
            return redirect()->route('picking.index')->with('success', 'SEMUA BARANG DIAMBIL! Sales Order status: PICKED.');
        }

        return redirect()->route('picking.process', $soId)->with('success', "Berhasil ambil {$product->part_number}.");
    }

    public function create()
    {
        return view('picking.create', ['soItems' => [], 'bins' => [], 'operators' => []]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'so_item_id' => 'required|exists:so_items,id',
            'bind_id' => 'required|exists:storage_bins,id',
            'quantity_to_pick' => 'required|integer|min:1',
            'operator_id' => 'nullable|exists:users,id',
        ]);
        $validated['status'] = 'Pending';
        PickingTask::create($validated);

        return redirect()->route('picking.index')->with('success', 'Tugas picking berhasil dibuat.');
    }

    public function edit(PickingTask $pickingTask)
    {
        $pickingTask->load(['soitems.product', 'soitems.salesorder']);
        $operators = User::where('role', 'Operator')->get();
        $bins = StorageBin::where('is_empty', false)->get();

        return view('picking.edit', compact('pickingTask', 'operators', 'bins'));
    }

    public function update(Request $request, PickingTask $pickingTask)
    {
        $validated = $request->validate([
            'operator_id' => 'required|exists:users,id',
            'status' => 'required|in:Pending,Picked',
            'bind_id' => 'required|exists:storage_bins,id',
        ]);

        if ($validated['status'] === 'Picked' && $pickingTask->status !== 'Picked') {
            $validated['picked_at'] = Carbon::now();
            $pickingTask->soitems->increment('quantity_picked', $pickingTask->quantity_to_pick);

            $so = $pickingTask->soitems->salesOrder;
            if ($so) {
                $totalOrdered = $so->items->sum('quantity_ordered');
                $totalPicked = $so->items->sum('quantity_picked') + $pickingTask->quantity_to_pick;

                if ($totalPicked >= $totalOrdered) {
                    $so->update(['status' => 'Packed']);
                } elseif ($so->status == 'Pending') {
                    $so->update(['status' => 'Picked']);
                }
            }
        }
        $pickingTask->update($validated);

        return redirect()->route('picking.index')->with('success', 'Status picking diperbarui.');
    }

    public function destroy(PickingTask $pickingTask)
    {
        if ($pickingTask->status === 'Picked') {
            $pickingTask->soitems->decrement('quantity_picked', $pickingTask->quantity_to_pick);

            $so = $pickingTask->soitems->salesOrder;
            if ($so && $so->status == 'Packed') {
                $so->update(['status' => 'Picked']);
            }
        }
        $pickingTask->delete();

        return redirect()->route('picking.index')->with('success', 'Tugas picking dihapus.');
    }
}
