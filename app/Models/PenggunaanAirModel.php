<?php

namespace App\Models;

use CodeIgniter\Model;

class PenggunaanAirModel extends Model
{
    protected $table = 'penggunaan_air';
    protected $primaryKey = 'id_penggunaan';
    protected $allowedFields = ['id_user', 'bulan', 'tahun', 'meter_awal', 'meter_akhir', 'created_at'];
    protected $useTimestamps = false;
}
