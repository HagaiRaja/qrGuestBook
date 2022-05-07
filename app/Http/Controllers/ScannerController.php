<?php

namespace App\Http\Controllers;

use DateTime;
use DateTimeZone;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ScannerController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');   
    }

    public function index()
    {
        $scanner = auth()->user()->scanner;
        return view('scanner.index', compact('scanner'));
    }

    public function show()
    {
        $scanner = auth()->user()->scanner;
        return view('scanner.scanner', compact('scanner'));
    }

    public function update(Request $request)
    {
        $data = request()->validate([
            'background_img' => ['image'],
        ]);

        $imageArray = [];
        if (request('background_img')) {
            $imagePath = request('background_img')->store('background_img', 'public');
            $imageArray['background_img']= $imagePath;
        }

        auth()->user()->scanner->update(array_merge(
            $data,
            $imageArray ?? []
        ));

        return redirect('/scanners?success');
    }

    public function check($qr_code, Request $request) {
        $table = DB::table('users')->join('guests', 'guests.user_id', '=', 'users.id')
            ->select(['guests.id', 'guests.name', 'guests.position', 'guests.rsvp_count', 'guests.qr_code', 'guests.seat', 'guests.attended_at' ])
            ->where('users.id', auth()->user()->id)
            ->where('guests.qr_code', $qr_code)
            ->get();
        if (count($table) == 1) {
            $date = new DateTime("now", new DateTimeZone('Asia/Singapore') );
            DB::table('guests')
              ->where('id', $table[0]->id)
              ->update(['attended_at' => $date->format('Y-m-d H:i:s')]);
        }
        echo json_encode($table);
    }
}
