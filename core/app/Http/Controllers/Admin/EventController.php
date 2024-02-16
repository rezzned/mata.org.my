<?php

namespace App\Http\Controllers\Admin;

use App\BasicExtra;
use App\Event;
use App\EventCategory;
use App\EventCertificate;
use App\EventDetail;
use App\EventTicket;
use App\Exports\EventBookingExport;
use App\Language;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Front\EventController as FrontEventController;
use App\Jobs\EventUserEmailJob;
use App\Jobs\SendEventAttendCertificate;
use App\Megamenu;
use App\Notifications\EventAttendNotify;
use App\Notifications\TrainingStatusNotify;
use App\OfflineGateway;
use App\PaymentGateway;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Helpers\EventHelper;
use PDF;

class EventController extends Controller
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
        $data['abx'] = $lang->basic_extra;
        $data['events'] = Event::with('eventTicket')->where('lang_id', $lang_id)->orderBy('id', 'DESC')->get();
        $data['event_categories'] = EventCategory::where('lang_id', $lang_id)->where('status', '1')->get();
        $eticket_prices = [];
        $data['events']->each(function ($event) {
            foreach ($event->eventTicket?->unique('type') as $ticket) {
                $eticket_prices[] = undash_str($ticket->type) . ": <b>" . currency_format($ticket->cost) . "</b>";
            }
            $eticket_prices = join('<br>', $eticket_prices);
            $event->pricing = $eticket_prices;
        });

        return view('admin.event.event.index', $data);
    }


    public function update_status(Event $event)
    {
        $data = $this->validate(request(), [
            'status' => 'required|in:' . Event::STATUS_ACTIVE . ',' . Event::STATUS_DEACTIVE
        ]);

        $event->status = $data['status'];
        $event->save();

        Session::flash('success', 'Event status updated successfully!');

        return back();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $slug = make_slug($request->title);

        $sliders = !empty($request->slider) ? explode(',', $request->slider) : [];
        $allowedExts = array('jpg', 'png', 'jpeg');

        $rules = [
            'title' => [
                'required',
                'max:255',
                function ($attribute, $value, $fail) use ($slug) {
                    $events = Event::all();
                    foreach ($events as $key => $event) {
                        if (strtolower($slug) == strtolower($event->slug)) {
                            $fail('The title field must be unique.');
                        }
                    }
                }
            ],
            'date' => 'required',
            'time' => 'required',
            'datetime2' => 'nullable',
            'ticket_type' => ['required', 'array', 'min:1'],
            'organizer' => 'required',
            'venue' => 'required',
            'lang_id' => 'required',
            'cat_id' => 'required',
            'slider' => 'required',
            'cpd_points' => 'required',
            'short_form' => 'required'
        ];


        if (request('available_tickets')) {
            $available_tickets = request('available_tickets');
            $rules['available_tickets'] = [
                function ($attribute, $value, $fail) use ($available_tickets) {
                    if (!array_filter($available_tickets)) {
                        return $fail("Available ticket minimum one required");
                    }
                }
            ];
        }
        if (request('cost')) {
            $cost = request('cost');
            $rules['cost'] = [
                function ($attribute, $value, $fail) use ($cost) {
                    if (!array_filter($cost)) {
                        return $fail("Cost minimum one required");
                    }
                }
            ];
        }

        if ($request->filled('slider')) {
            $rules['slider'] = [
                function ($attribute, $value, $fail) use ($sliders, $allowedExts) {
                    foreach ($sliders as $key => $slider) {
                        $extSlider = pathinfo($slider, PATHINFO_EXTENSION);
                        if (!in_array($extSlider, $allowedExts)) {
                            return $fail("Only png, jpg, jpeg images are allowed");
                        }
                    }
                }
            ];
        }

        $messages = [
            'title.required' => 'The title field is required',
            'date.required' => 'The date field is required',
            'time.required' => 'The time field is required',
            'cost.required' => 'The cost field is required',
            'available_tickets.required' => 'Number of tickets field is required',
            'organizer.required' => 'The organizer name field is required',
            'venue.required' => 'The venue field is required',
            'lang_id.required' => 'The language field is required',
            'cat_id.required' => 'The category field is required'
        ];

        $validator = Validator::make($request->all(), $rules, $messages);
        if ($validator->fails()) {
            $errmsgs = $validator->getMessageBag()->add('error', 'true');
            return response()->json($validator->errors());
        }

        $images = [];
        foreach ($sliders as $key => $slider) {
            $extSlider = pathinfo($slider, PATHINFO_EXTENSION);
            $filename = uniqid() . '.' . $extSlider;

            $directory = 'assets/front/img/events/sliders/';
            @mkdir($directory, 0775, true);

            @copy($slider, $directory . $filename);
            $images[] = $filename;
        }

        DB::beginTransaction();

        $event = Event::create($request->except('image', 'video', 'content') + [
            'status' => request('status', 1),
            'slug' => $slug,
            'image' => json_encode($images),
            'content' => str_replace(url('/') . '/assets/front/img/', "{base_url}/assets/front/img/", $request->post("content")),
            // 'video' => $videoFile
        ]);

        foreach (request('ticket_type') as $key => $value) {
            if (request('cost')[$key] && request('available_tickets')[$key]) {
                EventTicket::updateOrCreate([
                    'event_id' => $event->id,
                    'type' => $value
                ], [
                    'cost' => request('cost')[$key],
                    'available' => request('available_tickets')[$key]
                ]);
            }
        }

        DB::commit();
        Session::flash('success', 'Event added successfully!');
        return "success";
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $data['event'] = Event::findOrFail($id);
        $data['event_categories'] = EventCategory::where('lang_id', $data['event']->lang_id)->where('status', '1')->get();
        $data['abx'] = BasicExtra::select('base_currency_text')->where('language_id', $data['event']->lang_id)->first();
        return view('admin.event.event.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        $slug = make_slug($request->title);
        $eventId = $request->event_id;

        $sliders = !empty($request->slider) ? explode(',', $request->slider) : [];
        $allowedExts = array('jpg', 'png', 'jpeg');

        $rules = [
            'slider' => 'required',
            'title' => [
                'required',
                'max:255',
                function ($attribute, $value, $fail) use ($slug, $eventId) {
                    $events = Event::all();
                    foreach ($events as $key => $event) {
                        if ($event->id != $eventId && strtolower($slug) == strtolower($event->slug)) {
                            $fail('The title field must be unique.');
                        }
                    }
                }
            ],
            'date' => 'required',
            'time' => 'required',
            'datetime2' => 'nullable',
            'ticket_type' => ['required', 'array', 'min:1'],
            'organizer' => 'required',
            'venue' => 'required',
            'cat_id' => 'required',
            'cpd_points' => 'required',
            'short_form' => 'required'
        ];


        if (request('available_tickets')) {
            $available_tickets = request('available_tickets');
            $rules['available_tickets'] = [
                function ($attribute, $value, $fail) use ($available_tickets) {
                    if (!array_filter($available_tickets)) {
                        return $fail("Available ticket minimum one required");
                    }
                }
            ];
        }
        if (request('cost')) {
            $cost = request('cost');
            $rules['cost'] = [
                function ($attribute, $value, $fail) use ($cost) {
                    if (!array_filter($cost)) {
                        return $fail("Cost minimum one required");
                    }
                }
            ];
        }


        if ($request->filled('slider')) {
            $rules['slider'] = [
                function ($attribute, $value, $fail) use ($sliders, $allowedExts) {
                    foreach ($sliders as $key => $slider) {
                        $extSlider = pathinfo($slider, PATHINFO_EXTENSION);
                        if (!in_array($extSlider, $allowedExts)) {
                            return $fail("Only png, jpg, jpeg images are allowed.");
                        }
                    }
                }
            ];
        }

        $messages = [
            'title.required' => 'The title field is required',
            'date.required' => 'The date field is required',
            'time.required' => 'The time field is required',
            'cost.required' => 'The cost field is required',
            'available_tickets.required' => 'Number of tickets field is required',
            'organizer.required' => 'The organizer name field is required',
            'venue.required' => 'The venue field is required',
            'cat_id.required' => 'The category field is required'
        ];

        $validator = Validator::make($request->all(), $rules, $messages);
        if ($validator->fails()) {
            $errmsgs = $validator->getMessageBag()->add('error', 'true');
            return response()->json($validator->errors());
        }

        $event = Event::findOrFail($request->event_id);

        $event->update($request->except('image', 'video', 'content') + [
            'slug' => $slug,
            'content' => str_replace(url('/') . '/assets/front/img/', "{base_url}/assets/front/img/", $request->post("content")),
        ]);

        // copy the sliders first
        $fileNames = [];
        foreach ($sliders as $key => $slider) {
            $extSlider = pathinfo($slider, PATHINFO_EXTENSION);
            $filename = uniqid() . '.' . $extSlider;
            @copy($slider, 'assets/front/img/events/sliders/' . $filename);
            $fileNames[] = $filename;
        }

        // delete & unlink previous slider images
        $preImages = json_decode($event->image, true);
        foreach ($preImages as $key => $pi) {
            @unlink('assets/front/img/events/sliders/' . $pi);
        }

        $event->status = request('status');
        $event->image = json_encode($fileNames);
        $event->save();

        foreach (request('ticket_type') as $key => $value) {
            if (request('cost')[$key] && request('available_tickets')[$key]) {
                EventTicket::updateOrCreate([
                    'event_id' => $event->id,
                    'type' => $value
                ], [
                    'cost' => request('cost')[$key],
                    'available' => request('available_tickets')[$key]
                ]);
            }
        }

        Session::flash('success', 'Event updated successfully!');
        return "success";
    }

    public function uploadUpdate(Request $request, $id)
    {
        $rules = [
            'file' => 'required | mimes:jpeg,jpg,png',
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            $validator->getMessageBag()->add('error', 'true');
            return response()->json(['errors' => $validator->errors(), 'id' => 'blog']);
        }
        $img = $request->file('file');
        $event = Event::findOrFail($id);
        if ($request->hasFile('file')) {
            $filename = time() . '.' . $img->getClientOriginalExtension();
            $request->file('file')->move('assets/front/img/events/', $filename);
            @unlink('assets/front/img/events/' . $event->image);
            $event->image = $filename;
            $event->save();
        }

        return response()->json(['status' => "success", "image" => "Event image", 'event' => $event]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        //
    }

    public function getCategories($lang_id)
    {
        return EventCategory::where('lang_id', $lang_id)->where('status', '1')->get();
    }

    public function upload(Request $request)
    {
        $rules = ['upload_video' => 'mimes:mp4|required'];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            $validator->getMessageBag()->add('error', 'true');
            return response()->json(['errors' => $validator->errors(), 'id' => 'blog']);
        }
        $img = $request->file('upload_video');
        $filename = uniqid("event-") . '.' . $img->getClientOriginalExtension();
        //if directory not exist than create directory with permission
        $directory = "assets/front/img/events/videos/";
        if (!file_exists($directory)) mkdir($directory, 0777, true);
        $img->move($directory, $filename);
        return response()->json(['filename' => $filename, 'status' => 200]);
    }

    public function sliderRemove(Request $request)
    {
        $event = Event::findOrFail($request->id);
        $images = json_decode($event->image, true);
        @unlink('assets/front/img/events/sliders/' . $images["$request->key"]);
        unset($images["$request->key"]);
        $newImages = array_values($images);
        $event->image = json_encode($newImages);
        $event->save();
        return response()->json(['status' => 200, 'message' => 'success']);
    }

    public function deleteFromMegaMenu($event)
    {
        // unset service from megamenu for service_category = 1
        $megamenu = Megamenu::where('language_id', $event->lang_id)->where('category', 1)->where('type', 'events');
        if ($megamenu->count() > 0) {
            $megamenu = $megamenu->first();
            $menus = json_decode($megamenu->menus, true);
            $catId = $event->eventCategories->id;
            if (is_array($menus) && array_key_exists("$catId", $menus)) {
                if (in_array($event->id, $menus["$catId"])) {
                    $index = array_search($event->id, $menus["$catId"]);
                    unset($menus["$catId"]["$index"]);
                    $menus["$catId"] = array_values($menus["$catId"]);
                    if (count($menus["$catId"]) == 0) {
                        unset($menus["$catId"]);
                    }
                    $megamenu->menus = json_encode($menus);
                    $megamenu->save();
                }
            }
        }
    }

    public function delete(Request $request)
    {
        $event = Event::findOrFail($request->event_id);
        $images = json_decode($event->image, true);
        if (count($images) > 0) {
            foreach ($images as $image) {
                $directory = 'assets/front/img/events/sliders/' . $image;
                if (file_exists($directory)) {
                    @unlink($directory);
                }
            }
        }
        if (!is_null($event->video)) {
            $directory = "assets/front/img/events/videos/" . $event->video;
            if (file_exists($directory)) {
                @unlink($directory);
            }
        }
        $event_details = EventDetail::query()->where('event_id', $event->id)->get();
        foreach ($event_details as $event_detail) {
            if (!is_null($event_detail->receipt)) {
                $directory = "assets/front/img/events/receipt/" . $event_detail->receipt;
                if (file_exists($directory)) {
                    @unlink($directory);
                }
            }
            $event_detail->delete();
        }

        $this->deleteFromMegaMenu($event);
        $event->delete();

        Session::flash('success', 'Event deleted successfully!');
        return back();
    }

    public function bulkDelete(Request $request)
    {
        return DB::transaction(function () use ($request) {
            $ids = $request->ids;
            foreach ($ids as $id) {
                $event = Event::findOrFail($id);
                $images = json_decode($event->image, true);
                if (count($images) > 0) {
                    foreach ($images as $image) {
                        $directory = 'assets/front/img/events/sliders/' . $image;
                        if (file_exists($directory)) {
                            @unlink($directory);
                        }
                    }
                }
                if (!is_null($event->video)) {
                    $directory = "assets/front/img/events/videos/" . $event->video;
                    if (file_exists($directory)) {
                        @unlink($directory);
                    }
                }
                $event_details = EventDetail::where('event_id', $event->id)->get();
                foreach ($event_details as $event_detail) {
                    if (!is_null($event_detail->receipt)) {
                        $directory = "assets/front/img/events/receipt/" . $event_detail->receipt;
                        if (file_exists($directory)) {
                            @unlink($directory);
                        }
                    }
                    $event_detail->delete();
                }

                $this->deleteFromMegaMenu($event);
                $event->delete();
            }
            Session::flash('success', 'Events deleted successfully!');
            return "success";
        });
    }

    public function paymentLog(Request $request)
    {
        $data['fileClass'] = File::class;
        $data['pdf_path'] = function ($fileName) {
            return realpath(dirname(base_path())) . '/assets/front/invoices/' . $fileName;
        };
        $search = $request->search;
        $data['events'] = EventDetail::when($search, function ($query, $search) {
            return $query->where('transaction_id', $search);
        })->orderBy('id', 'DESC')->paginate(10);

        return view('admin.event.payment.index', $data);
    }

    public function regenerateTicket(Request $request, $id, $trxId)
    {
        $fileName = $file = '';
        $pdfPath = realpath(dirname(base_path())) . '/assets/front/invoices/';
        $event_details = EventDetail::findOrFail($id);
        // return view('pdf.custom.training', ['ticket' => $event_details]);
        if ($event_details) {
            if ($event_details->invoice) {
                $fileName = $event_details->invoice;
                $file = $pdfPath . $fileName;
            }
            $event = new FrontEventController;
            if ($event_details->invoice) {
                $fileName = $event->makeInvoice($event_details, $event_details->invoice);
            } else {
                $fileName = $event->makeInvoice($event_details, $event_details->invoice);
                $event_details->invoice = $fileName;
                $event_details->save();
            }
        }
        if ($fileName != '' && File::exists($file)) {
            return response()->download($file, $fileName);
        }
        return back();
    }

    public function generateTicket(Request $request, $id, $trxId)
    {
        $fileName = $file = '';
        $pdfPath = realpath(dirname(base_path())) . '/assets/front/invoices/';
        $event_details = EventDetail::findOrFail($id);
        if ($event_details) {
            if ($event_details->invoice) {
                $fileName = $event_details->invoice;
                $file = $pdfPath . $fileName;
            }
            if (!File::exists($file)) {
                $event = new FrontEventController;
                if ($event_details->invoice) {
                    $fileName = $event->makeInvoice($event_details, $event_details->invoice);
                } else {
                    $fileName = $event->makeInvoice($event_details, $event_details->invoice);
                    $event_details->invoice = $fileName;
                    $event_details->save();
                }
            }
        }
        if ($fileName != '' && File::exists($file)) {
            return response()->download($file, $fileName);
        }
        return back();
    }

    public function paymentLogDelete(Request $request)
    {
        $payment = EventDetail::findOrFail($request->payment_id);
        $filePath = root_path('assets/front/img/events/receipt/' . $payment->receipt);
        if (file_exists($filePath)) {
            @unlink($filePath);
        }
        $payment->delete();

        $request->session()->flash('success', 'Payment deleted successfully!');
        return back();
    }


    public function paymentLogBulkDelete(Request $request)
    {
        $ids = $request->ids;

        foreach ($ids as $id) {
            $payment = EventDetail::findOrFail($id);
            $filePath = root_path('assets/front/img/events/receipt/' . $payment->receipt);
            if (file_exists($filePath)) {
                @unlink($filePath);
            }
            $payment->delete();
        }

        Session::flash('success', 'Payments deleted successfully!');
        return "success";
    }

    public function paymentLogUpdate(Request $request)
    {
        try {
            $currentLang = session()->has('lang') ?
                (Language::where('code', session()->get('lang'))->first())
                : (Language::where('is_default', 1)->first());
            $be = $currentLang->basic_extended;

            $event_details = EventDetail::findOrFail($request->id);

            if ($request->status == "success") {
                if ($event_details->status == "Rejected") {
                    EventTicket::where('event_id', $event_details->event_id)->decrement('available', $event_details->quantity);

                    // $event = Event::findOrFail($event_details->event_id);
                    // $event->available_tickets = $event->available_tickets - $event_details->quantity;
                    // $event->save();
                }
                $event_details->status = "Success";
                $event_details->save();
                // updateCpdPoint($event_details->user_id, $event_details->cpd_points * $event_details->quantity);
                $event = new FrontEventController;
                $fileName = $event->makeInvoice($event_details);
                $request['name'] = $event_details->name;
                $request['email'] = $event_details->email;
                $event->sendMailPHPMailer($request, $fileName, $be);
                Session::flash('success', 'Event payment updated successfully!');
            } elseif ($request->status == "rejected") {
                $event_details->status = "Rejected";
                $event_details->rejected_note = $request->rejected_note;
                $event_details->save();

                EventTicket::where('event_id', $event_details->event_id)->increment('available', $event_details->quantity);
                Session::flash('success', 'Event payment rejected successfully!');
            } else {
                $event_details->status = 'Pending';
                $event_details->save();

                Session::flash('success', 'Event payment to pending successfully!');
            }

            if ($event_details->user) {
                $title = __('Your booking is ') . $request->status . ($event_details->rejected_note ? '. Reason:' . $event_details->rejected_note : '');
                $event_details->user->notify(new TrainingStatusNotify($title, $event_details));
            }

            if (!empty($event_details->email)) {
                // Send Mail to Customer
                EventUserEmailJob::dispatch($be, $event_details, $request->status)->delay(now()->addMinutes(1));
            }
            return redirect()->route('admin.event.payment.log');
        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function settings()
    {
        $data['abex'] = BasicExtra::first();
        return view('admin.event.settings', $data);
    }

    public function updateSettings(Request $request)
    {
        $bexs = BasicExtra::all();
        foreach ($bexs as $bex) {
            $bex->event_guest_checkout = $request->event_guest_checkout;
            $bex->is_event = $request->is_event;
            $bex->save();
        }

        $request->session()->flash('success', 'Settings updated successfully!');
        return back();
    }

    public function images($eventid)
    {
        $event = Event::find($eventid);
        $images = json_decode($event->image, true);
        $convImages = [];

        foreach ($images as $key => $image) {
            $convImages[] = url("assets/front/img/events/sliders/$image");
        }

        return $convImages;
    }

    public function report(Request $request)
    {
        $fromDate = $request->from_date;
        $toDate = $request->to_date;
        $status = $request->status;
        $paymentMethod = $request->payment_method;

        $eventIds = EventDetail::select('event_id')->distinct();
        $data['events'] = Event::query()
            ->select(['id', 'title'])
            ->whereIn('id', $eventIds)
            ->get();

        $bookings = EventDetail::query()
            ->with(['event',])
            ->when($fromDate, function ($query, $fromDate) {
                return $query->whereDate('created_at', '>=', Carbon::parse($fromDate));
            })
            ->when($toDate, function ($query, $toDate) {
                return $query->whereDate('created_at', '<=', Carbon::parse($toDate));
            })
            ->when($paymentMethod, function ($query, $paymentMethod) {
                return $query->where('payment_method', $paymentMethod);
            })
            ->when($status, function ($query, $status) {
                return $query->where('status', $status);
            })
            ->when($request->event, function ($query, $event) {
                return $query->where('event_id', $event);
            })
            ->select([
                'transaction_id',
                'transaction_details',
                'event_id',
                'id',
                'name',
                'email',
                'phone',
                'amount',
                'quantity',
                'payment_method',
                'status',
                'created_at',
                'attendance',
            ])
            ->orderBy('id', 'DESC');

        Session::put('event_booking_report', $bookings->get());
        $data['bookings'] = $bookings->paginate(10);

        $data['onPms'] = PaymentGateway::where('status', 1)->get();
        $data['offPms'] = OfflineGateway::where('event_checkout_status', 1)->get();


        return view('admin.event.report', $data);
    }

    public function eventReportAttendance(Request $request)
    {
        $request->validate([
            'attendance' => ['required'],
            'id' => ['required']
        ]);

        $eventDetail = EventDetail::findorfail(request('id'))->load(['event']);

        if (!$eventDetail->event->short_form) {
            Session::flash('error', 'Short form not updated yet! Please update short form first.');
            return back();
        }

        $eventDetail->update(['attendance' => request('attendance'), 'refund_note' => request('refund_note')]);

        if (request('attendance') == 'attend') {
            updateCpdPoint($eventDetail->user_id, $eventDetail->cpd_points);
            $notifTitle = __('You attended this event and your CPD points increasing ') . $eventDetail->cpd_points;

            if ($eventDetail->user) {
                $eventDetail->user->notify(new EventAttendNotify($notifTitle, $eventDetail));
            }

            $certificate = $this->createCertificate($eventDetail);

            $data = $this->certificateDataArray($eventDetail, $certificate);

            EventHelper::makeCertificate($data);

            dispatch(new SendEventAttendCertificate($data));
        } elseif (request('attendance') == 'not_attend') {
            $notifTitle = __('You not attended this event and your CPD points are not increase.') . request('refund_note');
            if ($eventDetail->user) {
                $eventDetail->user->notify(new EventAttendNotify($notifTitle, $eventDetail));
            }
        }

        Session::flash('success', 'Event attendance status change successfully!');
        return back();
    }

    public function eventReportCertificateDownload($id)
    {
        $eventDetail = EventDetail::find($id);

        $data = $this->getCertificateData($eventDetail);

        //return view('pdf.custom.training-attend-cert', [ 'data' => (object) $data ]);

        $file = root_path('assets/front/certificate/'.$eventDetail->certificate->certificate_file);

        if (!file_exists($file)) {
            EventHelper::makeCertificate($data);
        }

        return response()->download($file);
    }

    public function eventReportCertificateRegenerate($id)
    {
        $eventDetail = EventDetail::find($id);

        $data = $this->getCertificateData($eventDetail);

        $file = root_path('assets/front/certificate/'.$eventDetail->certificate->certificate_file);

        EventHelper::makeCertificate($data);

        return response()->download($file);
    }

    public function exportReport()
    {
        $bookings = Session::get('event_booking_report');
        if (empty($bookings) || count($bookings) == 0) {
            Session::flash('warning', 'There are no bookings to export');
            return back();
        }
        return Excel::download(new EventBookingExport($bookings), 'event-bookings.csv');
    }

    private function getCertificateData($eventDetail)
    {
        $certificate = $eventDetail->certificate;

        if (!$certificate) {
            $certificate = $this->createCertificate($eventDetail);
        }

        if (!$certificate->certificate_file) {
            $certificate->update(['certificate_file'   => 'cert_' . $eventDetail->transaction_id . '.pdf']);
        }

        return $this->certificateDataArray($eventDetail, $certificate);
    }

    private function createCertificate($eventDetail)
    {
        $cert_number = EventCertificate::whereEventId($eventDetail->event_id)->max('certificate_number');

        return EventCertificate::create([
            'event_id' => $eventDetail->event_id,
            'event_detail_id' => $eventDetail->id,
            'event_name' => $eventDetail->event->title,
            'event_date' => $eventDetail->event->date,
            'event_venue' => $eventDetail->event->venue,
            'participant_name' => $eventDetail->name,
            'ic_number' => $eventDetail->ic_number,
            'cpd_point' => $eventDetail->cpd_points,
            'certificate_number' => $cert_number + 1,
            'certificate_date' => now()->format('Y-m-d'),
            'short_form' => $eventDetail->event->short_form,
            'certificate_file' => 'cert_' . $eventDetail->transaction_id . '.pdf'
        ]);
    }

    private function certificateDataArray($eventDetail, $certificate)
    {
        return [
            'cert_id'           => $certificate->certificate_number,
            'certificate_file'  => $certificate->certificate_file,
            'event_title'       => $eventDetail->event->title,
            'short_form'        => $eventDetail->event->short_form,
            'venue'             => $eventDetail->event->venue,
            'date'              => $eventDetail->event->date,
            'date2'              => $eventDetail->event->datetime2,
            'name'              => $eventDetail->name,
            'ic_number'         => $eventDetail->ic_number,
            'company_name'      => $eventDetail->company_name,
            'phone'             => $eventDetail->phone,
            'email'             => $eventDetail->email,
            'cpd_points'        => $eventDetail->cpd_points,
        ];
    }
}
