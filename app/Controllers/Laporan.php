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
        // Ambil data rekap bulanan (misal query group by month)
        $db = \Config\Database::connect();
        $query = $db->query("
            SELECT 
                MONTH(penggunaan_air.tanggal_pencatatan) AS bulan,
                COUNT(DISTINCT penggunaan_air.id_user) AS total_pelanggan,
                SUM(penggunaan_air.meter_akhir - penggunaan_air.meter_awal) AS total_pemakaian,
                SUM(tagihan.total_tagihan) AS total_tagihan,
                SUM(CASE WHEN tagihan.status = 'Lunas' THEN 1 ELSE 0 END) AS jumlah_lunas,
                SUM(CASE WHEN tagihan.status = 'Belum Dibayar' THEN 1 ELSE 0 END) AS jumlah_belum
            FROM tagihan
            JOIN penggunaan_air ON tagihan.id_penggunaan = penggunaan_air.id_penggunaan
            GROUP BY MONTH(penggunaan_air.tanggal_pencatatan)
            ORDER BY bulan ASC
        ");
        $data['rekap'] = $query->getResultArray();
        
        return view('admin/laporan/index', $data);
    }

    // Export to Excel
    public function exportExcel()
    {
        $model = new TagihanModel();
        $rekap = $model->getRekapBulanan();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->fromArray(['Bulan', 'Jumlah Pelanggan', 'Total Pemakaian', 'Total Tagihan', 'Tagihan Lunas', 'Belum Dibayar'], NULL, 'A1');

        $row = 2;
        foreach ($rekap as $r) {
            $sheet->setCellValue("A$row", date('F', mktime(0, 0, 0, $r['bulan'], 10)));
            $sheet->setCellValue("B$row", $r['total_pelanggan']);
            $sheet->setCellValue("C$row", $r['total_pemakaian']);
            $sheet->setCellValue("D$row", $r['total_tagihan']);
            $sheet->setCellValue("E$row", $r['jumlah_lunas']);
            $sheet->setCellValue("F$row", $r['jumlah_belum']);
            $row++;
        }

        $filename = 'laporan_bulanan.xlsx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment; filename=\"$filename\"");
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }
    // Export to PDF
    // This function generates a PDF report of the monthly usage and billing summary
    public function exportPDF()
    {
        $db = \Config\Database::connect();
        $query = $db->query("
            SELECT 
                MONTH(penggunaan_air.tanggal_pencatatan) AS bulan,
                COUNT(DISTINCT penggunaan_air.id_user) AS total_pelanggan,
                SUM(penggunaan_air.meter_akhir - penggunaan_air.meter_awal) AS total_pemakaian,
                SUM(tagihan.total_tagihan) AS total_tagihan,
                SUM(CASE WHEN tagihan.status = 'Lunas' THEN 1 ELSE 0 END) AS jumlah_lunas,
                SUM(CASE WHEN tagihan.status = 'Belum Dibayar' THEN 1 ELSE 0 END) AS jumlah_belum
            FROM tagihan
            JOIN penggunaan_air ON tagihan.id_penggunaan = penggunaan_air.id_penggunaan
            GROUP BY MONTH(penggunaan_air.tanggal_pencatatan)
            ORDER BY bulan ASC
        ");
        $rekap = $query->getResultArray();

        // Buat HTML untuk ditampilkan di PDF
        $html = '<h3 style="text-align:center;">Laporan Bulanan</h3>';
        $html .= '<table border="1" cellpadding="6" cellspacing="0" width="100%">
                    <thead>
                    <tr style="background-color:#f2f2f2;">
                        <th>Bulan</th>
                        <th>Jumlah Pelanggan</th>
                        <th>Total Pemakaian (m³)</th>
                        <th>Total Tagihan</th>
                        <th>Tagihan Lunas</th>
                        <th>Belum Dibayar</th>
                    </tr>
                    </thead>
                    <tbody>';

        foreach ($rekap as $r) {
            $bulan = date('F', mktime(0, 0, 0, $r['bulan'], 10));
            $html .= "<tr>
                        <td>{$bulan}</td>
                        <td>{$r['total_pelanggan']}</td>
                        <td>{$r['total_pemakaian']} m³</td>
                        <td>Rp " . number_format($r['total_tagihan'], 0, ',', '.') . "</td>
                        <td>{$r['jumlah_lunas']}</td>
                        <td>{$r['jumlah_belum']}</td>
                    </tr>";
        }

        $html .= '</tbody></table>';

        // Setup Dompdf
        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();

        // Outputkan ke browser
        $dompdf->stream('laporan_bulanan_' . date('Ymd_His') . '.pdf', ['Attachment' => false]);
    }


}
