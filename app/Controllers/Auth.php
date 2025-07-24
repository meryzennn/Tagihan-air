<?php

namespace App\Controllers;

use App\Models\UserModel;
use CodeIgniter\Controller;

class Auth extends Controller
{
    public function index()
    {
        return view('auth/login');
    }

    public function login()
    {
        $session = session();
        $model = new UserModel();

        $username = $this->request->getPost('username');
        $password = $this->request->getPost('password');

        $user = $model->where('username', $username)->first();

        if ($user && password_verify($password, $user['password'])) {
            $session->set([
                'id_user'   => $user['id_user'],
                'username'  => $user['username'],
                'role'      => $user['role'],
                'logged_in' => true
            ]);

            // Redirect sesuai role
            if ($user['role'] === 'admin') {
                return redirect()->to('/dashboard/admin');
            } else {
                return redirect()->to('/dashboard/user');
            }
        } else {
            return redirect()->back()->with('error', 'Username atau Password salah.');
        }
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to('/login?logout=success');
    }

    public function registerForm()
    {
        return view('auth/register');
    }

    public function register()
    {
        helper('text'); // Untuk fungsi random_string
        $model = new UserModel();

        $username = $this->request->getPost('username');
        $password = $this->request->getPost('password');
        $confirm  = $this->request->getPost('password_confirm');

        $nama   = $this->request->getPost('nama_lengkap');
        $alamat = $this->request->getPost('alamat');
        $no_hp  = $this->request->getPost('no_hp');

        // Validasi
        if ($password !== $confirm) {
            return redirect()->back()->with('error', 'Konfirmasi password tidak cocok');
        }

        if ($model->where('username', $username)->first()) {
            return redirect()->back()->with('error', 'Username sudah digunakan');
        }

        // Generate No Pelanggan
        $noPelanggan = 'PLG' . strtoupper(random_string('alnum', 6));

        $model->save([
            'username'      => $username,
            'password'      => password_hash($password, PASSWORD_DEFAULT),
            'role'          => 'pelanggan',
            'nama_lengkap'  => $nama,
            'alamat'        => $alamat,
            'no_hp'         => $no_hp,
            'no_pelanggan'  => $noPelanggan
        ]);

        return redirect()->to('/login')->with('success', 'Akun berhasil dibuat. Silakan login.');
    }
}
