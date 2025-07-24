<?php

namespace App\Controllers;

class Dashboard extends BaseController
{
    public function index()
    {
        // Harus sudah login
        if (!session()->get('logged_in')) {
            return redirect()->to('/login');
        }

        // Cek role dan redirect ke dashboard yang sesuai
        $role = session()->get('role');

        if ($role === 'admin') {
            return redirect()->to('/admin/dashboard');
        } else {
            return redirect()->to('/dashboard/user');
        }
    }

    public function admin()
    {
        return view('dashboard/index'); // View untuk admin
    }

    public function user()
    {
        return view('dashboard/user'); // View untuk user/pelanggan
    }
}
