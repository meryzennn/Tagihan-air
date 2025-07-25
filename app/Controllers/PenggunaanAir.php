<?php

namespace App\Controllers;

use App\Models\PenggunaanAirModel;
use App\Models\PelangganModel;
use App\Models\TagihanModel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class PenggunaanAir extends BaseController
{
    public function index()
    {
        $model = new PenggunaanAirModel();
        $data['penggunaan'] = $model
            ->select('penggunaan_air.*, users.no_pelanggan, users.nama_lengkap')
            ->join('users', 'users.id_user = penggunaan_air.id_user')
            ->orderBy('tanggal_pencatatan', 'DESC')
            ->findAll();

        // Hitung total pemakaian dan tagihan
        foreach ($data['penggunaan'] as &$row) {
            $row['total_pemakaian'] = $row['meter_akhir'] - $row['meter_awal'];
            $row['tagihan'] = $row['total_pemakaian'] * 2500;
        }

        return view('admin/penggunaan_air/index', $data);
    }

    public function create()
    {
        $pelangganModel = new PelangganModel();
        $data['pelanggan'] = $pelangganModel
            ->where('role', 'pelanggan')
            ->findAll();

        return view('admin/penggunaan_air/create', $data);
    }

    public function store()
    {
        $no_pelanggan = $this->request->getPost('no_pelanggan');
        $pelangganModel = new \App\Models\PelangganModel();

        // Cari data user berdasarkan no_pelanggan
        $pelanggan = $pelangganModel->where('no_pelanggan', $no_pelanggan)->first();

        if (!$pelanggan) {
            return redirect()->back()->withInput()->with('error', 'Pelanggan tidak ditemukan.');
        }

        $id_user = $pelanggan['id_user'];

        $penggunaanModel = new \App\Models\PenggunaanAirModel();
        $tagihanModel    = new \App\Models\TagihanModel();

        // Simpan data penggunaan air
        $data = [
            'id_user'             => $id_user,
            'tanggal_pencatatan' => $this->request->getPost('tanggal_pencatatan'),
            'meter_awal'         => $this->request->getPost('meter_awal'),
            'meter_akhir'        => $this->request->getPost('meter_akhir'),
            'created_at'         => date('Y-m-d H:i:s'),
        ];

        $penggunaanModel->insert($data);
        $id_penggunaan = $penggunaanModel->getInsertID();

        // Hitung tagihan
        $total_pemakaian = $data['meter_akhir'] - $data['meter_awal'];
        $total_tagihan   = $total_pemakaian * 2500;

        // Simpan ke tabel tagihan
        $tagihanModel->insert([
            'id_penggunaan' => $id_penggunaan,
            'total_tagihan' => $total_tagihan,
            'status'        => 'Belum Dibayar',
            'created_at'    => date('Y-m-d H:i:s'),
        ]);

        return redirect()->to('/penggunaan-air')->with('success', 'Data penggunaan air & tagihan berhasil ditambahkan.');
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
        return redirect()->to('/penggunaan-air')->with('success', 'Data berhasil diperbarui.');
    }

    public function delete($id)
    {
        $model = new PenggunaanAirModel();
        $model->delete($id);

        return redirect()->to('/penggunaan-air')->with('success', 'Data berhasil dihapus.');
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

        // Header
        $sheet->fromArray([
            ['No', 'No Pelanggan', 'Nama', 'Tanggal', 'Meter Awal', 'Meter Akhir', 'Total Pemakaian (m³)', 'Tagihan']
        ], NULL, 'A1');

        $row = 2;
        foreach ($data as $i => $p) {
            $total = $p['meter_akhir'] - $p['meter_awal'];
            $tagihan = $total * 2500;

            $sheet->fromArray([
                $i + 1,
                $p['no_pelanggan'],
                $p['nama_lengkap'],
                $p['tanggal_pencatatan'],
                $p['meter_awal'],
                $p['meter_akhir'],
                $total,
                $tagihan
            ], NULL, 'A' . $row++);
        }

        $filename = 'data_penggunaan_air_' . date('Ymd_His') . '.xlsx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment;filename=\"$filename\"");
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }
}
