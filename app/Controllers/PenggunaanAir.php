<?php

namespace App\Controllers;

use App\Models\PenggunaanAirModel;
use App\Models\PelangganModel;

class PenggunaanAir extends BaseController
{
    public function index()
    {
        $model = new PenggunaanAirModel();
        $data['penggunaan'] = $model->join('users', 'users.id_user = penggunaan_air.id_user')
                                    ->select('penggunaan_air.*, users.nama_lengkap')
                                    ->orderBy('tahun', 'desc')
                                    ->orderBy('bulan', 'desc')
                                    ->findAll();

        return view('admin/penggunaan_air/index', $data);
    }

    public function create()
    {
        $pelangganModel = new PelangganModel();
        $data['pelanggan'] = $pelangganModel->where('role', 'pelanggan')->findAll();
        return view('admin/penggunaan_air/create', $data);
    }

    public function store()
    {
        $model = new PenggunaanAirModel();
        $data = [
            'id_user'    => $this->request->getPost('id_user'),
            'bulan'      => $this->request->getPost('bulan'),
            'tahun'      => $this->request->getPost('tahun'),
            'meter_awal' => $this->request->getPost('meter_awal'),
            'meter_akhir'=> $this->request->getPost('meter_akhir'),
            'created_at' => date('Y-m-d H:i:s'),
        ];

        $model->insert($data);

        return redirect()->to('/penggunaan-air')->with('success', 'Data penggunaan air berhasil ditambahkan.');
    }
}
