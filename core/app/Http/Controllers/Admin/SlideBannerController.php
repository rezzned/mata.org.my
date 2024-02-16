<?php

namespace App\Http\Controllers\Admin;

use App\Language;
use App\SlideBanner;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;
use Intervention\Image\Facades\Image;

class SlideBannerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return
     */
    public function index(Request $request)
    {
        $lang = Language::where('code', $request->language)->first();
        $lang_id = $lang->id;
        $data['lang_id'] = $lang_id;
        $data['banners'] = SlideBanner::where('lang_id', $lang_id)->orderBy('id', 'DESC')->paginate(10);
        return view('admin.banner.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function createorUpdate($request, $item = null)
    {
        $data = [
            'title' => $request['title'],
            'status' => $request['status'],
        ];


        if (!$item) {
            $data['lang_id'] = $request['lang_id'];
        }

        if ($request['image']) {
            $directory = 'assets/front/img/banner/';

            if (!is_dir($directory)) {
                @mkdir($directory, 0775, true);
            }
            if ($item) {
                @unlink($directory . $item->image);
            }

            $filename = uniqid() . '.' . $request['image']->extension();

            $img = Image::make($request['image']);
            $img->fit(1280, 350)->save($directory . '/' . $filename);
            $data['image'] = $filename;
        }

        return $data;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        request()->validate([
            'title' => ['required'],
            'image' => ['required', 'mimes:jpg,gif,png,jpeg']
        ]);

        SlideBanner::create($this->createorUpdate($request->all()));

        Session::flash('success', 'Banner  added successfully!');
        return "success";
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        request()->validate([
            'title' => ['required'],
            'image' => ['nullable', 'mimes:jpg,gif,png,jpeg']
        ]);

        $item = SlideBanner::findorfail(request('item_id'));
        $item->update($this->createorUpdate($request->all(), $item));

        Session::flash('success', 'Banner update successfully!');
        return "success";
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function delete(Request $request)
    {
        $ecat = SlideBanner::findOrFail($request->event_category_id);
        @unlink('assets/front/img/banner/' . $ecat->image);
        $ecat->delete();
        Session::flash('success', 'Banner deleted successfully!');
        return back();
    }
}
