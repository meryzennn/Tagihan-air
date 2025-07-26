<?php

namespace App\Controllers;

use App\Models\PenggunaanAirModel;
use App\Models\TagihanModel;
use App\Models\TarifAirModel;

class User extends BaseController
{
    public function index()
    {
        $id_user = session('id_user');

        $penggunaanModel = new PenggunaanAirModel();
        $tagihanModel = new TagihanModel();
        $tarifModel = new TarifAirModel();

        // Ambil penggunaan terakhir
        $last = $penggunaanModel
            ->where('id_user', $id_user)
            ->orderBy('tanggal_pencatatan', 'DESC')
            ->first();

        $pemakaian = $last ? ($last['meter_akhir'] - $last['meter_awal']) : 0;

        // Ambil semua tagihan belum dibayar
        $belum_dibayar = $tagihanModel
            ->select('penggunaan_air.*')
            ->join('penggunaan_air', 'penggunaan_air.id_penggunaan = tagihan.id_penggunaan')
            ->where('penggunaan_air.id_user', $id_user)
            ->where('tagihan.status', 'Belum Dibayar')
            ->findAll();

        $totalBelum = 0;
        foreach ($belum_dibayar as $b) {
            $tarif = $tarifModel
                ->where('berlaku_mulai <=', $b['tanggal_pencatatan'])
                ->orderBy('berlaku_mulai', 'DESC')
                ->first();

            $harga = $tarif ? $tarif['harga_per_m3'] : 2500;
            $pemakaianTagihan = $b['meter_akhir'] - $b['meter_awal'];
            $totalBelum += $pemakaianTagihan * $harga;
        }

        // Ambil riwayat semua tagihan user
        $riwayatData = $tagihanModel
            ->select('penggunaan_air.*, tagihan.status')
            ->join('penggunaan_air', 'penggunaan_air.id_penggunaan = tagihan.id_penggunaan')
            ->where('penggunaan_air.id_user', $id_user)
            ->orderBy('penggunaan_air.tanggal_pencatatan', 'ASC')
            ->findAll();

        $riwayat = [];
        foreach ($riwayatData as $r) {
            $tarif = $tarifModel
                ->where('berlaku_mulai <=', $r['tanggal_pencatatan'])
                ->orderBy('berlaku_mulai', 'DESC')
                ->first();

            $harga = $tarif ? $tarif['harga_per_m3'] : 2500;
            $pemakaianItem = $r['meter_akhir'] - $r['meter_awal'];

            $riwayat[] = [
                'tanggal_pencatatan' => $r['tanggal_pencatatan'],
                'meter_awal'         => $r['meter_awal'],
                'meter_akhir'        => $r['meter_akhir'],
                'pemakaian'          => $pemakaianItem,
                'harga_per_m3'       => $harga,
                'total_tagihan'      => $pemakaianItem * $harga,
                'status'             => $r['status']
            ];
        }

        return view('user/dashboard/index', [
            'pemakaian'     => $pemakaian,
            'tagihan_belum' => $totalBelum,
            'riwayat'       => $riwayat
        ]);
    }
}
