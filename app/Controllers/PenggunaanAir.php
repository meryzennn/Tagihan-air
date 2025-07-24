<?php

namespace App\Controllers;

use App\Models\PenggunaanAirModel;
use App\Models\PelangganModel;

class PenggunaanAir extends BaseController
{
    public function index()
    {
        $model = new \App\Models\PenggunaanAirModel();

        $data['penggunaan'] = $model
        ->select('penggunaan_air.*, users.no_pelanggan, users.nama_lengkap')
        ->join('users', 'users.id_user = penggunaan_air.id_user')
        ->orderBy('tanggal_pencatatan', 'DESC') // ganti ini
        ->findAll();


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
        $model = new PenggunaanAirModel();
        $data = [
            'id_user'    => $this->request->getPost('id_user'),
            'tanggal_pencatatan' => $this->request->getPost('tanggal_pencatatan'),
            'meter_awal' => $this->request->getPost('meter_awal'),
            'meter_akhir'=> $this->request->getPost('meter_akhir'),
            'created_at' => date('Y-m-d H:i:s'),
        ];

        $model->insert($data);

        return redirect()->to('/penggunaan-air')->with('success', 'Data penggunaan air berhasil ditambahkan.');
    }
}
