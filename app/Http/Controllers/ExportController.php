<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Incident;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Color;

class ExportController extends Controller
{
    public function export(Request $request)
    {
        // A. Ambil Data (Copy logika filter dari index agar hasil export sama dengan tampilan)
        // Tambahkan filter waktu down diatas 1 menit (>= 60 detik)
        $query = Incident::with('monitor')->orderBy('down_at', 'desc')
            ->whereRaw('TIMESTAMPDIFF(SECOND, down_at, COALESCE(up_at, NOW())) >= 60');

        if ($request->filled('status')) {
            if ($request->status == 'resolved') {
                $query->whereNotNull('up_at');
            } elseif ($request->status == 'ongoing') {
                $query->whereNull('up_at');
            }
        }
        if ($request->filled('start_date')) {
            $query->whereDate('down_at', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('down_at', '<=', $request->end_date);
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('monitor', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('ip_address', 'like', "%{$search}%")
                  ->orWhere('location', 'like', "%{$search}%");
            });
        }
        
        $incidents = $query->get(); // Ambil semua data (bukan paginate)

        // B. Buat Spreadsheet Baru
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // C. Buat Header Kolom
        $headers = ['No', 'Perangkat', 'IP Address', 'Lokasi', 'Waktu Down', 'Waktu Up', 'Durasi', 'Status'];
        $col = 'A';
        foreach ($headers as $header) {
            $cell = $col . '1';
            $sheet->setCellValue($cell, $header);
            
            // Styling Header (Bold, Tengah, Auto Width)
            $sheet->getStyle($cell)->getFont()->setBold(true);
            $sheet->getStyle($cell)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getColumnDimension($col)->setAutoSize(true);
            
            $col++;
        }

        // D. Isi Data
        $row = 2; // Mulai baris ke-2
        $no = 1;
        foreach ($incidents as $incident) {
            // Hitung Durasi (Logika sama seperti di Blade)
            $duration = '-';
            if ($incident->up_at) {
                $diff = $incident->down_at->diff($incident->up_at);
                $duration = $diff->format('%Hj %Im %Sd');
            } else {
                $diff = $incident->down_at->diff(now());
                $duration = $diff->format('%Hj %Im %Sd') . ' (Running)';
            }

            $statusText = $incident->up_at ? 'Resolved' : 'Gangguan';

            // Masukkan Data ke Cell
            $sheet->setCellValue('A' . $row, $no++);
            $sheet->setCellValue('B' . $row, $incident->monitor->name ?? 'N/A');
            $sheet->setCellValue('C' . $row, $incident->monitor->ip_address ?? 'N/A');
            $sheet->setCellValue('D' . $row, $incident->monitor->location ?? '-');
            $sheet->setCellValue('E' . $row, $incident->down_at->format('Y-m-d H:i:s'));
            $sheet->setCellValue('F' . $row, $incident->up_at ? $incident->up_at->format('Y-m-d H:i:s') : 'Sedang Perbaikan...');
            $sheet->setCellValue('G' . $row, $duration);
            $sheet->setCellValue('H' . $row, $statusText);

            // Mewarnai Text Status (Merah jika Gangguan)
            if (!$incident->up_at) {
                $sheet->getStyle('H' . $row)->getFont()->setColor(new Color(Color::COLOR_RED));
            }
            
            // Tengahkan Kolom Status
            $sheet->getStyle('H' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            $row++;
        }

        // E. Proses Download
        $fileName = 'History_DAOP3_' . date('Y-m-d_H-i-s') . '.xlsx';
        
        // Header agar browser tahu ini file Excel
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="'. urlencode($fileName) .'"');
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }
}
