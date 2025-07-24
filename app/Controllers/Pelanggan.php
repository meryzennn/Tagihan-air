<?php

namespace App\Controllers;

use App\Models\PelangganModel;
use CodeIgniter\Controller;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class Pelanggan extends Controller
{
    public function index()
    {
        $model = new \App\Models\PelangganModel();
        $data['pelanggan'] = $model->where('role', 'pelanggan')->findAll();

        return view('admin/pelanggan/index', $data);
    }


    public function create()
    {
        $data['title'] = 'Tambah Pelanggan';
        return view('admin/pelanggan/create', $data);
    }

    public function store()
    {
    $model = new PelangganModel();

    // Generate no_pelanggan otomatis, contoh: PLG20250723001
    $no_pelanggan = 'PLG' . date('Ymd') . rand(100, 999);

    $password = $this->request->getPost('password');
    $username = $this->request->getPost('username');

    // Validasi sederhana
    if ($model->where('username', $username)->first()) {
        return redirect()->back()->with('error', 'Username sudah digunakan!');
    }

    $data = [
        'username'      => $username,
        'password'      => password_hash($password, PASSWORD_DEFAULT),
        'role'          => 'pelanggan',
        'nama_lengkap'  => $this->request->getPost('nama_lengkap'),
        'alamat'        => $this->request->getPost('alamat'),
        'no_hp'         => $this->request->getPost('no_hp'),
        'no_pelanggan'  => $no_pelanggan,
        'created_at'    => date('Y-m-d H:i:s')
    ];

    $model->insert($data);

    return redirect()->to('/pelanggan')->with('success', 'Pelanggan berhasil ditambahkan!');
}

    public function delete($id)
    {
        $model = new PelangganModel();
        $model->delete($id);

        return redirect()->to('/pelanggan')->with('success', 'Data pelanggan berhasil dihapus.');
    }
    public function edit($id)
    {
        $model = new \App\Models\PelangganModel();
        $data['pelanggan'] = $model->find($id);
        $data['title'] = 'Edit Data Pelanggan';

        if (!$data['pelanggan']) {
            return redirect()->to('/pelanggan')->with('error', 'Data pelanggan tidak ditemukan.');
        }

        return view('admin/pelanggan/edit', $data);
    }

    public function update($id)
    {
        $model = new \App\Models\PelangganModel();

        $data = [
            'no_pelanggan' => $this->request->getPost('no_pelanggan'),
            'nama_lengkap' => $this->request->getPost('nama_lengkap'),
            'alamat'       => $this->request->getPost('alamat'),
            'no_hp'        => $this->request->getPost('no_hp'),
        ];

        $model->update($id, $data);

        return redirect()->to('/pelanggan')->with('success', 'Data pelanggan berhasil diperbarui.');
    }

    // spreadsheet export function
    public function export()
    {
        $model = new \App\Models\PelangganModel();
        $data = $model->where('role', 'pelanggan')->findAll();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Header kolom
        $sheet->setCellValue('A1', 'No');
        $sheet->setCellValue('B1', 'Nama Lengkap');
        $sheet->setCellValue('C1', 'No Pelanggan');
        $sheet->setCellValue('D1', 'Alamat');
        $sheet->setCellValue('E1', 'No HP');
        $sheet->setCellValue('F1', 'Tanggal Daftar');

        // Isi data
        $row = 2;
        foreach ($data as $index => $p) {
            $sheet->setCellValue('A' . $row, $index + 1);
            $sheet->setCellValue('B' . $row, $p['nama_lengkap']);
            $sheet->setCellValue('C' . $row, $p['no_pelanggan']);
            $sheet->setCellValue('D' . $row, $p['alamat']);
            $sheet->setCellValue('E' . $row, $p['no_hp']);
            $sheet->setCellValue('F' . $row, $p['created_at']);
            $row++;
        }

        // Kirim ke browser
        $filename = 'data_pelanggan_' . date('YmdHis') . '.xlsx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment; filename=\"$filename\"");
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }



}
