<?php

namespace App\Http\Controllers;

use App\Models\Guest;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Str;
use Yajra\DataTables\Facades\DataTables;

class GuestController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');   
    }

    public function index()
    {
        return view('guest.index');
    }

    public function list()
    {
        $table = DB::table('users')->join('guests', 'guests.user_id', '=', 'users.id')
            ->select(['guests.id', 'guests.name', 'guests.position', 'guests.rsvp_count', 'guests.qr_code', 'guests.seat', 'guests.attended_at' ])
            ->where('users.id', auth()->user()->id);

        return Datatables::of($table)
            ->make(true);
    }

    public function create()
    {
        return view('guest.create');
    }

    public function check()
    {
        $result = array();
        $counter_all = DB::table('users')->join('guests', 'guests.user_id', '=', 'users.id')
            ->select(DB::raw('count(guests.id) as guests_all, 
                                sum(guests.rsvp_count) as attendances_all
                                '))
            ->where('users.id', auth()->user()->id)
            ->get();
        $result['guests_all'] = $counter_all[0]->guests_all;
        $result['attendances_all'] = ($counter_all[0]->attendances_all) ?
                                $counter_all[0]->attendances_all : 0;
        
        $counter_attend = DB::table('users')->join('guests', 'guests.user_id', '=', 'users.id')
            ->select(DB::raw('count(guests.id) as guests_count, 
                                sum(guests.rsvp_count) as attendances_count
                                '))
            ->where('users.id', auth()->user()->id)
            ->whereRaw('guests.attended_at IS NOT NULL')
            ->get();
        $result['guests_count'] = $counter_attend[0]->guests_count;
        $result['attendances_count'] = ($counter_attend[0]->attendances_count) ?
                                $counter_attend[0]->attendances_count : 0;

        $last_attended = DB::table('users')->join('guests', 'guests.user_id', '=', 'users.id')
            ->select('guests.name', 'guests.rsvp_count', 'guests.seat')
            ->where('users.id', auth()->user()->id)
            ->whereRaw('guests.attended_at IS NOT NULL')
            ->orderByDesc('guests.attended_at')
            ->limit(1)
            ->get();
        $is_there_any_guests = count($last_attended);
        $result['name'] = $is_there_any_guests ? $last_attended[0]->name : "";
        $result['rsvp_count'] = $is_there_any_guests ? $last_attended[0]->rsvp_count : "";
        $result['seat'] = $is_there_any_guests ? $last_attended[0]->seat : "";

        echo json_encode($result);
    }

    public function store()
    {
        $data = request()->validate([
            'name' => ['required', 'string', 'max:255'],
            'position' => ['required', 'string', 'max:255'],
            'rsvp_count' => ['required', 'digits_between:1,4'],
            'seat' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:13'],
        ]);

        $data['qr_code'] = (string) Str::uuid();

        $data['user_id'] = auth()->user()->id;

        QrCode::size(500)
          ->format('png')
          ->generate($data['qr_code'], public_path('temp/'. $data['qr_code'] . '.png'));
        $guest = Guest::create($data);

        return redirect('/guests/create?success&message='.$data['name']);
    }

    public function edit(Guest $guest)
    {
        return view('guest.edit', compact('guest'));
    }

    public function update(Guest $guest, Request $request)
    {
        $data = request()->validate([
            'name' => ['required', 'string', 'max:255'],
            'position' => ['required', 'string', 'max:255'],
            'rsvp_count' => ['required', 'digits_between:1,4'],
            'seat' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:13'],
        ]);

        $guest->update($data);
        return redirect('/guests/' . $guest->id . '/edit?success&message='.$data['name']);
    }

    public function destroy(Guest $guest)
    {
        $guest->delete();
        return redirect('/guests?success&message='.$guest->name);
    }

}
