<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title><?= $title ?? 'Dashboard Pelanggan' ?></title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <style>
    body { min-height: 100vh; display: flex; background-color: #f5f6fa; font-family: 'Segoe UI', sans-serif; }
    .sidebar { width: 220px; background: #dfe4ea; padding: 20px; box-shadow: 2px 0 5px rgba(0,0,0,0.05); }
    .sidebar a { display: block; padding: 10px 15px; text-decoration: none; color: #333; border-radius: 8px; margin-bottom: 8px; }
    .sidebar a:hover { background-color: #ced6e0; }
    .main-content { flex-grow: 1; padding: 40px; }
  </style>
</head>
<body>

<div class="sidebar">
  <h5 class="mb-4">Pelanggan</h5>
  <a href="<?= base_url('/dashboard/user') ?>"><i class="bi bi-house-door"></i> Dashboard</a>
  <a href="#"><i class="bi bi-droplet-half"></i> Cek Pemakaian</a>
  <a href="#"><i class="bi bi-cash"></i> Riwayat Tagihan</a>
  <a href="#"><i class="bi bi-person"></i> Profil</a>
  <a href="<?= base_url('/logout') ?>"><i class="bi bi-box-arrow-right"></i> Logout</a>
</div>

<div class="main-content">
  <?= $this->renderSection('content') ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
