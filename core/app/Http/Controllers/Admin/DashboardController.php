<?php

namespace App\Http\Controllers\Admin;

use App\EventDetail;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Language;
use App\Quote;
use App\Product;
use App\ProductOrder;
use App\Subscription;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function dashboard()
    {
        $data['quotes'] = Quote::orderBy('id', 'DESC')->limit(10)->get();
        $porders = ProductOrder::select(
            DB::raw('"publication_pending" as type'),
            DB::raw('"Publication" as name'),
            'id',
            'total as amount',
            'invoice_number',
            'created_at'
        )
            ->where(['payment_status' => 'Pending'])
            ->orderBy('id', 'DESC')
            ->get();

        $events = EventDetail::select(
            DB::raw('"event_pending" as type'),
            DB::raw('"Event" as name'),
            'id',
            'amount','invoice as invoice_number','created_at'
        )
            ->where(function ($q) {
                $q->where('status', 'Pending')
                    ->orWhere('transaction_details', 'offline');
            })
            ->orderBy('id', 'DESC')
            ->get();

        $subscriptions = Subscription::join('packages', 'subscriptions.pending_package_id', '=', 'packages.id')
            ->select(
                DB::raw('"subscription_pending" as type'),
                DB::raw('"Subscription" as name'),
                'subscriptions.id',
                DB::raw('(packages.price + packages.entrance_fee) as amount'),
                'subscriptions.invoice as invoice_number',
                'subscriptions.created_at'
            )
            ->whereNotNull('subscriptions.pending_package_id')
            ->orderBy('subscriptions.id', 'DESC')->get();

        $data['pending_items'] = array_merge($porders->toArray(), $events->toArray(), $subscriptions->toArray());

        // dd($data);

        $data['default'] = Language::where('is_default', 1)->first();

        return view('admin.dashboard', $data);
    }
}
