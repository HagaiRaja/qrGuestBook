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
        $table = DB::table('guests')
            ->select(['id', 'name', 'position', 'rsvp_count', 'qr_code', 'seat', 'attended_at' ]);

        return Datatables::of($table)
            ->make(true);
    }

    public function create()
    {
        return view('guest.create');
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
