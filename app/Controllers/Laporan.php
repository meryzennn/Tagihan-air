<?php

namespace App\Controllers;

use App\Models\TagihanModel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Dompdf\Dompdf;
use Dompdf\Options;

class Laporan extends BaseController
{   

    public function index()
    {
        $tagihanModel = new TagihanModel();

        $data['tagihan'] = $tagihanModel
            ->select('
                tagihan.*, 
                penggunaan_air.tanggal_pencatatan, penggunaan_air.meter_awal, penggunaan_air.meter_akhir,
                users.no_pelanggan, users.nama_lengkap
            ')
            ->join('penggunaan_air', 'penggunaan_air.id_penggunaan = tagihan.id_penggunaan')
            ->join('users', 'users.id_user = penggunaan_air.id_user')
            ->findAll();

        return view('admin/laporan/index', $data);
    }

    public function exportPDF()
    {
        $tagihanModel = new TagihanModel();

        $data['tagihan'] = $tagihanModel
            ->select('
                tagihan.*, 
                penggunaan_air.tanggal_pencatatan, penggunaan_air.meter_awal, penggunaan_air.meter_akhir,
                users.no_pelanggan, users.nama_lengkap
            ')
            ->join('penggunaan_air', 'penggunaan_air.id_penggunaan = tagihan.id_penggunaan')
            ->join('users', 'users.id_user = penggunaan_air.id_user')
            ->findAll();

        // Load HTML dari view
        $html = view('admin/laporan/pdf', $data);

        // Konfigurasi Dompdf
        $options = new Options();
        $options->set('defaultFont', 'Arial');
        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();
        $dompdf->stream('laporan_tagihan.pdf', ['Attachment' => true]);
    }


    public function exportExcel()
    {
        $tagihanModel = new TagihanModel();

        $data = $tagihanModel
            ->select('
                tagihan.*, 
                penggunaan_air.tanggal_pencatatan, penggunaan_air.meter_awal, penggunaan_air.meter_akhir,
                users.no_pelanggan, users.nama_lengkap
            ')
            ->join('penggunaan_air', 'penggunaan_air.id_penggunaan = tagihan.id_penggunaan')
            ->join('users', 'users.id_user = penggunaan_air.id_user')
            ->findAll();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Header
        $sheet->setCellValue('A1', 'No');
        $sheet->setCellValue('B1', 'No Pelanggan');
        $sheet->setCellValue('C1', 'Nama');
        $sheet->setCellValue('D1', 'Tanggal');
        $sheet->setCellValue('E1', 'Pemakaian (m³)');
        $sheet->setCellValue('F1', 'Total Tagihan');
        $sheet->setCellValue('G1', 'Status');

        // Data
        $row = 2;
        foreach ($data as $i => $t) {
            $pemakaian = $t['meter_akhir'] - $t['meter_awal'];
            $sheet->setCellValue("A$row", $i + 1);
            $sheet->setCellValue("B$row", $t['no_pelanggan']);
            $sheet->setCellValue("C$row", $t['nama_lengkap']);
            $sheet->setCellValue("D$row", $t['tanggal_pencatatan']);
            $sheet->setCellValue("E$row", $pemakaian);
            $sheet->setCellValue("F$row", $t['total_tagihan']);
            $sheet->setCellValue("G$row", $t['status']);
            $row++;
        }

        $filename = 'laporan_tagihan_' . date('Ymd_His') . '.xlsx';

        // Output ke browser
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment; filename=\"$filename\"");
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }
}
