<?php

namespace App\Http\Controllers;

use App\Models\Guest;
use DateTime;
use DateTimeZone;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Str;
use Yajra\DataTables\Facades\DataTables;
use App\Exports\GuestsExport;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\IOFactory;

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
            ->select('guests.name', 'guests.rsvp_count', 'guests.seat', 'guests.attended_at')
            ->where('users.id', auth()->user()->id)
            ->whereRaw('guests.attended_at IS NOT NULL')
            ->orderByDesc('guests.attended_at')
            ->limit(1)
            ->get();
        $is_there_any_guests = count($last_attended);
        $result['name'] = $is_there_any_guests ? $last_attended[0]->name : "";
        $result['rsvp_count'] = $is_there_any_guests ? $last_attended[0]->rsvp_count : "";
        $result['seat'] = $is_there_any_guests ? $last_attended[0]->seat : "";
        $result['attended_at'] = $is_there_any_guests ? $last_attended[0]->attended_at : "";

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

        $data['rsvp_count'] = (int) $data['rsvp_count'];

        $data['qr_code'] = (string) Str::uuid();

        $data['user_id'] = auth()->user()->id;

        QrCode::size(500)
          ->format('png')
          ->generate($data['qr_code'], public_path('temp/'. $data['qr_code'] . '.png'));
        $guest = Guest::create($data);

        return redirect('/guests/create?success&message='.$data['name']);
    }

    public function store_excel()
    {
        $data = request()->validate([
            'guest_list' => ['required', 'file:xlsx'],
        ]);

        $filename = $data['guest_list']->getClientOriginalName();

        $reader = IOFactory::createReader('Xlsx');
        $spreadsheet = $reader->load($data['guest_list']);
        $worksheet = $spreadsheet->getActiveSheet();
        $rows = $worksheet->toArray();

        foreach($rows as $key => $value) {
            $table = DB::table('users')->join('guests', 'guests.user_id', '=', 'users.id')
                ->select(['guests.id'])
                ->where('users.id', auth()->user()->id)
                ->where('guests.name', $value[0])
                ->where('guests.position', $value[1])
                ->get();
            if (count($table) == 1) {
                Guest::where('id', $table[0]->id)
                    ->update([
                        'rsvp_count' => $value[2],
                        'seat' => $value[3],
                        'email' => $value[4],
                        'phone' => $value[5],
                    ]);
                continue;
            }

            if ($key == 0) continue;
            $data = [
                'name' => $value[0],
                'position' => $value[1],
                'rsvp_count' => $value[2],
                'seat' => $value[3],
                'email' => $value[4],
                'phone' => $value[5],
            ];

            $data['rsvp_count'] = (int) $data['rsvp_count'];

            $data['qr_code'] = (string) Str::uuid();

            $data['user_id'] = auth()->user()->id;

            QrCode::size(500)
            ->format('png')
            ->generate($data['qr_code'], public_path('temp/'. $data['qr_code'] . '.png'));
            $guest = Guest::create($data);
        };

        return redirect('/guests/create?success&message='.$filename);
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

    public function toggle(Guest $guest, $command, Request $request)
    {
        if ($command == "false") {
            $data['attended_at'] = NULL;
            $guest->update($data);
        }
        else if ($command == "true"){
            $date = new DateTime("now", new DateTimeZone('Asia/Singapore') );
            $data['attended_at'] = $date->format('Y-m-d H:i:s');
            $guest->update($data);
        }
        else {
            return redirect('/guests?unknown_command&message='.$guest->name);
        }

        return redirect('/guests?success&message='.$guest->name);
    }

    public function destroy(Guest $guest)
    {
        $guest->delete();
        return redirect('/guests?success&message='.$guest->name);
    }

    public function export()
    {
        return Excel::download(new GuestsExport, 'guests.xlsx');
    }
}
