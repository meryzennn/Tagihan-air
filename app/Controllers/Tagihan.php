<?php

namespace App\Controllers;

use App\Models\TagihanModel;
use App\Models\PenggunaanAirModel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class Tagihan extends BaseController
{
    protected $db;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
    }

    public function index()
    {
        $status = $this->request->getGet('status');
        $tagihanModel = new \App\Models\TagihanModel();

        $builder = $tagihanModel
            ->select('
                tagihan.id_tagihan,
                tagihan.status,
                tagihan.total_tagihan,
                tagihan.created_at,
                penggunaan_air.tanggal_pencatatan,
                penggunaan_air.meter_awal,
                penggunaan_air.meter_akhir,
                users.no_pelanggan,
                users.nama_lengkap
            ')
            ->join('penggunaan_air', 'penggunaan_air.id_penggunaan = tagihan.id_penggunaan')
            ->join('users', 'users.id_user = penggunaan_air.id_user');

        if ($status && in_array($status, ['Lunas', 'Belum Dibayar'])) {
            $builder->where('tagihan.status', $status);
        }

        $perPage = 5;
        $data['tagihan']     = $builder->paginate($perPage, 'tagihan');
        $data['pager']       = $tagihanModel->pager;
        $data['filter_status'] = $status;
        $data['currentPage'] = $tagihanModel->pager->getCurrentPage('tagihan');
        $data['perPage']     = $perPage;

        return view('admin/tagihan/index', $data);
    }



    public function generate()
    {
        $penggunaanModel = new PenggunaanAirModel();
        $tagihanModel = new TagihanModel();

        $penggunaan = $penggunaanModel->findAll();

        foreach ($penggunaan as $p) {
            // Hindari duplikasi
            $existing = $tagihanModel->where('id_penggunaan', $p['id_penggunaan'])->first();
            if ($existing) continue;

            $total_pemakaian = $p['meter_akhir'] - $p['meter_awal'];
            $total_tagihan   = $total_pemakaian * 2500;

            $tagihanModel->insert([
                'id_penggunaan' => $p['id_penggunaan'],
                'total_tagihan' => $total_tagihan,
                'status'        => 'Belum Dibayar',
                'created_at'    => date('Y-m-d H:i:s')
            ]);
        }

        return redirect()->to('/tagihan')->with('success', 'Tagihan berhasil digenerate.');
    }
    // Export to Excel
    // This function exports the tagihan data to an Excel file
    public function export()
    {
        $builder = $this->db->table('tagihan');
        $builder->select('
            tagihan.*, 
            penggunaan_air.tanggal_pencatatan, penggunaan_air.meter_awal, penggunaan_air.meter_akhir,
            users.no_pelanggan, users.nama_lengkap
        ');
        $builder->join('penggunaan_air', 'penggunaan_air.id_penggunaan = tagihan.id_penggunaan');
        $builder->join('users', 'users.id_user = penggunaan_air.id_user');
        $data = $builder->get()->getResultArray();

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

        $filename = 'data_tagihan_' . date('Ymd_His') . '.xlsx';

        // Output ke browser
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment; filename=\"$filename\"");
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }

}
