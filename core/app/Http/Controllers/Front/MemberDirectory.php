<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Language;
use App\User;

class MemberDirectory extends Controller
{

    public function index()
    {
        if (session()->has('lang')) {
            $currentLang = Language::where('code', session()->get('lang'))->first();
        } else {
            $currentLang = Language::where('is_default', 1)->first();
        }
        $be = $currentLang->basic_extended;
        $version = $be->theme_version;

        if ($version == 'dark') {
            $version = 'default';
        }

        $sort = [
            'a-z' => 'ASC',
            'z-a' => 'DESC'
        ];

        $order = $sort[request('sort', 'a-z') ?? 'a-z'];

        $data['version'] = $version;

        $data['state'] = request('state') ? request('state') : 'all';
        $users = User::whereHas('subscription', function ($q) {
            $q->whereHas('current_package', function ($q) {
                $q->whereIn('type', ['associate_member', 'standard_member']);
            });
        });
        if (request('state') && request('state') != 'all') {
            $users = $users->where('state', request('state'));
        }
        $users = $users->orderBy('fname', $order)->paginate(10);

        $data['users'] = $users;

        return view('front.member-directory', $data);
    }
}
