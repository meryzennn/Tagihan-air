<?php

namespace App\Controllers;

use App\Models\PenggunaanAirModel;
use App\Models\TagihanModel;

class User extends BaseController
{
    public function index()
    {
        $id_user = session('id_user');

        $penggunaanModel = new PenggunaanAirModel();
        $tagihanModel = new TagihanModel();

        // Ambil penggunaan terakhir
        $last = $penggunaanModel->where('id_user', $id_user)->orderBy('tanggal_pencatatan', 'DESC')->first();
        $pemakaian = $last ? $last['meter_akhir'] - $last['meter_awal'] : 0;

        // Tagihan belum dibayar
        $belum = $tagihanModel
            ->select('SUM(total_tagihan) AS total')
            ->join('penggunaan_air', 'penggunaan_air.id_penggunaan = tagihan.id_penggunaan')
            ->where('penggunaan_air.id_user', $id_user)
            ->where('status', 'Belum Dibayar')
            ->first();

        // Riwayat
        $riwayat = $tagihanModel
            ->select('penggunaan_air.*, tagihan.total_tagihan, tagihan.status')
            ->join('penggunaan_air', 'penggunaan_air.id_penggunaan = tagihan.id_penggunaan')
            ->where('penggunaan_air.id_user', $id_user)
            ->orderBy('penggunaan_air.tanggal_pencatatan', 'DESC')
            ->findAll();

        return view('user/dashboard/index', [
            'pemakaian' => $pemakaian,
            'tagihan_belum' => $belum['total'] ?? 0,
            'riwayat' => $riwayat
        ]);
    }
}
