<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Events\AfterSheet;

class GuestsExport implements FromCollection, WithHeadings, ShouldAutoSize, WithEvents
{
    public function collection()
    {
        $table = 
        DB::table('users')->join('guests', 'guests.user_id', '=', 'users.id')
          ->select('guests.id', 
                  'guests.name',  
                  'guests.position',
                  'guests.rsvp_count',
                  'guests.seat', 
                  'guests.email', 
                  'guests.phone', 
                  'guests.attended_at')
          ->where('users.id', auth()->user()->id)
          ->get();
        return $table;
    }

    public function headings(): array
    {
        return [
            "ID", 
            "Name", 
            "Position", 
            "# of Guests", 
            "Seat",
            "Email", 
            "Phone", 
            "Attended At", 
          ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class    => function(AfterSheet $event) {
                $cellRange = 'A1:H1'; // All headers
                $styleArray = [
                  'font' => array(
                      'name'      =>  'Calibri',
                      'size'      =>  12,
                      'bold'      =>  true
                  ),
                  'fill' => array(
                    'type'  => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'color' => array('rgb' => '0000FF')
                  ),
                  'borders' => [
                      'outline' => [
                          'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THICK,
                          'color' => ['rgb' => '000000'],
                      ],
                  ],
                ];
                $event->sheet->getDelegate()->getStyle($cellRange)->applyFromArray($styleArray);
            },
        ];
    }
}