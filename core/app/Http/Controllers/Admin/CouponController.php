<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Coupon;
use Illuminate\Http\Request;
use Validator;
use Session;

class CouponController extends Controller
{
    public function index(Request $request, $coupon_type)
    {
        $data['coupons'] = Coupon::orderBy('id', 'DESC')->where(['coupon_type'=>$coupon_type])->paginate(10);
        $data['coupon_type'] = $coupon_type;
        return view('admin.product.order.coupons.index', $data);
    }

    public function store(Request $request)
    {
        $rules = [
            'name' => 'required',
            'code' => 'required|unique:coupons',
            'type' => 'required',
            'value' => 'required',
            'minimum_spend' => 'nullable|numeric',
            'start_date' => 'required',
            'end_date' => 'required',
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            $errmsgs = $validator->getMessageBag()->add('error', 'true');
            return response()->json($validator->errors());
        }

        $input = $request->except('_token');

        Coupon::create($input);

        Session::flash('success', 'Coupon added successfully!');
        return "success";
    }

    public function edit($coupon_type, $id)
    {
        $data['coupon'] = Coupon::findOrFail($id);
        $data['coupon_type'] = $coupon_type;
        return view('admin.product.order.coupons.edit', $data);
    }

    public function update(Request $request)
    {
        $rules = [
            'name' => 'required',
            'code' => 'required|unique:coupons,code,' . $request->coupon_id,
            'type' => 'required',
            'value' => 'required',
            'minimum_spend' => 'nullable|numeric',
            'start_date' => 'required',
            'end_date' => 'required',
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            $errmsgs = $validator->getMessageBag()->add('error', 'true');
            return response()->json($validator->errors());
        }

        $input = $request->except('_token', 'coupon_id');

        $data = Coupon::find($request->coupon_id);
        $data->fill($input)->save();

        Session::flash('success', 'Coupon updated successfully!');
        return "success";
    }

    public function delete(Request $request) {
        $coupon = Coupon::find($request->coupon_id);
        $coupon->delete();

        $request->session()->flash('success', 'Coupon deleted successfully!');
        return back();
    }
}
