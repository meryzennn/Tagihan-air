<?php

namespace App\Controllers;

use App\Models\PenggunaanAirModel;
use App\Models\PelangganModel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class PenggunaanAir extends BaseController
{
    public function index()
    {
        $model = new \App\Models\PenggunaanAirModel();

        $data['penggunaan'] = $model
            ->select('penggunaan_air.*, users.no_pelanggan, users.nama_lengkap')
            ->join('users', 'users.id_user = penggunaan_air.id_user')
            ->orderBy('tanggal_pencatatan', 'DESC')
            ->findAll();

        // Hitung total pemakaian (meter_akhir - meter_awal)
        foreach ($data['penggunaan'] as &$row) {
            $row['total_pemakaian'] = $row['meter_akhir'] - $row['meter_awal'];
            $row['tagihan'] = $row['total_pemakaian'] * 2500; // atau harga per m³ sesuai kebijakan

        }

        return view('admin/penggunaan_air/index', $data);
    }



    public function create()
    {
        $pelangganModel = new \App\Models\PelangganModel();
        $data['pelanggan'] = $pelangganModel
            ->select('id_user, no_pelanggan, nama_lengkap')
            ->where('role', 'pelanggan')
            ->findAll();

        return view('admin/penggunaan_air/create', $data);
    }


    public function store()
    {
        $noPelanggan = $this->request->getPost('no_pelanggan');

        // Cari ID user berdasarkan no_pelanggan
        $userModel = new PelangganModel();
        $user = $userModel->where('no_pelanggan', $noPelanggan)->first();

        if (!$user) {
            return redirect()->back()->with('error', 'Nomor pelanggan tidak ditemukan.');
        }

        // Validasi meter akhir harus lebih besar atau sama dengan meter awal
        $meterAwal = (int)$this->request->getPost('meter_awal');
        $meterAkhir = (int)$this->request->getPost('meter_akhir');

        if ($meterAkhir < $meterAwal) {
            return redirect()->back()->with('error', 'Meter akhir tidak boleh lebih kecil dari meter awal.');
        }

        $model = new PenggunaanAirModel();
        $data = [
            'id_user'            => $user['id_user'],
            'tanggal_pencatatan' => $this->request->getPost('tanggal_pencatatan'),
            'meter_awal'         => $meterAwal,
            'meter_akhir'        => $meterAkhir,
            'created_at'         => date('Y-m-d H:i:s'),
        ];

        $model->insert($data);

        return redirect()->to('/penggunaan-air')->with('success', 'Data penggunaan air berhasil ditambahkan.');
    }


    public function edit($id)
    {
        $model = new PenggunaanAirModel();
        $pelangganModel = new PelangganModel();

        $data['penggunaan'] = $model->find($id);
        $data['pelanggan'] = $pelangganModel->where('role', 'pelanggan')->findAll();

        return view('admin/penggunaan_air/edit', $data);
    }

    public function update($id)
    {
        $model = new PenggunaanAirModel();
        $data = [
            'id_user'             => $this->request->getPost('id_user'),
            'tanggal_pencatatan' => $this->request->getPost('tanggal_pencatatan'),
            'meter_awal'         => $this->request->getPost('meter_awal'),
            'meter_akhir'        => $this->request->getPost('meter_akhir'),
        ];

        $model->update($id, $data);
        return redirect()->to('/penggunaan-air')->with('success', 'Data penggunaan air berhasil diperbarui.');
    }

    public function delete($id)
    {
        $model = new \App\Models\PenggunaanAirModel();
        $model->delete($id);

        return redirect()->to('/penggunaan-air')->with('success', 'Data penggunaan air berhasil dihapus.');
    }

    public function export()
    {
        $model = new PenggunaanAirModel();
        $data = $model
            ->select('penggunaan_air.*, users.no_pelanggan, users.nama_lengkap')
            ->join('users', 'users.id_user = penggunaan_air.id_user')
            ->orderBy('tanggal_pencatatan', 'DESC')
            ->findAll();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Header kolom
        $sheet->setCellValue('A1', 'No');
        $sheet->setCellValue('B1', 'No Pelanggan');
        $sheet->setCellValue('C1', 'Nama');
        $sheet->setCellValue('D1', 'Tanggal');
        $sheet->setCellValue('E1', 'Meter Awal');
        $sheet->setCellValue('F1', 'Meter Akhir');
        $sheet->setCellValue('G1', 'Total Pemakaian (m³)');
        $sheet->setCellValue('H1', 'Tagihan');

        $row = 2;
        foreach ($data as $i => $p) {
            $total = $p['meter_akhir'] - $p['meter_awal'];
            $tagihan = $total * 2500;

            $sheet->setCellValue('A' . $row, $i + 1);
            $sheet->setCellValue('B' . $row, $p['no_pelanggan']);
            $sheet->setCellValue('C' . $row, $p['nama_lengkap']);
            $sheet->setCellValue('D' . $row, $p['tanggal_pencatatan']);
            $sheet->setCellValue('E' . $row, $p['meter_awal']);
            $sheet->setCellValue('F' . $row, $p['meter_akhir']);
            $sheet->setCellValue('G' . $row, $total);
            $sheet->setCellValue('H' . $row, $tagihan);
            $row++;
        }

        // Download file
        $filename = 'data_penggunaan_air_' . date('Ymd_His') . '.xlsx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment;filename=\"$filename\"");
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }





}
