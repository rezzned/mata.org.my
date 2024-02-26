<?php

namespace App\Http\Controllers\User;

use App\Attendance;
use App\BasicExtra;
use App\EventDetail;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class EventController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $bex = BasicExtra::first();
        if ($bex->is_event == 0) {
            return back();
        }

        $events = EventDetail::where('user_id', Auth::user()->id)->orderBy('id', 'DESC')->get();

        return view('user.events', compact('events'));
    }

    public function eventdetails($id)
    {
        $bex = BasicExtra::first();
        if ($bex->is_event == 0) {
            return back();
        }

        $data = EventDetail::findOrFail($id);

        return view('user.event_details', compact('data'));
    }


    public function attendance()
    {
        $bex = BasicExtra::first();
        if ($bex->is_event == 0) {
            return back();
        }

        $events = EventDetail::With('attendances')->where('user_id', Auth::user()->id)->orderBy('id', 'DESC')->get();

        return view('user.appointment', compact('events'));
    }

    public function attendanceSave($id)
    {
        // dd($id);
        $event = EventDetail::findOrFail($id);
        $attendCheck = Attendance::Where('event_detail_id', $id)->first();

        $data = array(
            'event_detail_id' => $id,
            'user_id' => auth()->user()->id,
            'status' => 'Attend'
        );

        if ($event) {
            try {
                $attend = new Attendance;
                $attend->create($data);
                return back()->with('success', "Your attending as update successfully");
            } catch (\Exception $e) {
                // Exception handling
                dd($e->getMessage());
                echo "Error: " . $e->getMessage();
            }
        } else {
            return back()->with('danger', "Your action not valid!");
        }
    }
}
