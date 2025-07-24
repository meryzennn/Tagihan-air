<?= $this->extend('layouts/user_layout') ?>

<?= $this->section('content') ?>
  <h2>Dashboard Pelanggan</h2>
  <p>Hai, <?= session('username') ?>! Cek tagihan dan pemakaianmu di sini.</p>
<?= $this->endSection() ?>
