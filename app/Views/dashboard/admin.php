<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Dashboard Admin - Tagihan Air</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <!-- Bootstrap 5 & Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

  <style>
    body {
      min-height: 100vh;
      display: flex;
      font-family: 'Segoe UI', sans-serif;
      background-color: #f5f6fa;
    }
    .sidebar {
      width: 250px;
      background-color: #e0e0e0;
      padding: 25px 15px;
      box-shadow: 2px 0 5px rgba(0,0,0,0.05);
    }
    .sidebar h5 {
      font-weight: bold;
    }
    .sidebar a {
      display: block;
      padding: 10px 15px;
      color: #333;
      text-decoration: none;
      border-radius: 8px;
      margin-bottom: 8px;
      transition: all 0.2s;
    }
    .sidebar a:hover {
      background-color: #ced4da;
    }
    .main-content {
      flex-grow: 1;
      padding: 40px;
    }
    .card {
      border: none;
      border-radius: 16px;
    }
    .card .card-body {
      padding: 25px;
    }
    .stat-card {
      box-shadow: 0 0 15px rgba(0,0,0,0.05);
      transition: all 0.2s ease;
    }
    .stat-card:hover {
      transform: translateY(-3px);
    }
  </style>
</head>
<body>

  <!-- Sidebar -->
  <div class="sidebar">
    <h5 class="mb-4">Admin Panel</h5>
    <a href="#"><i class="bi bi-house-door"></i> Dashboard</a>
    <a href="<?= base_url('pelanggan') ?>"><i class="bi bi-people-fill"></i> Data Pelanggan</a>
    <a href="#"><i class="bi bi-droplet-half"></i> Penggunaan Air</a>
    <a href="#"><i class="bi bi-cash-stack"></i> Tagihan</a>
    <a href="#"><i class="bi bi-bar-chart-line"></i> Laporan</a>
    <a href="#"><i class="bi bi-gear"></i> Tarif Air</a>
    <a href="/logout"><i class="bi bi-box-arrow-right"></i> Logout</a>
  </div>

  <!-- Main Content -->
  <div class="main-content">
    <h2 class="fw-bold mb-3">Selamat Datang, <?= session('username') ?></h2>
    <p class="text-muted">Pantau dan kelola sistem tagihan air Anda dari sini.</p>

    <!-- Stat Cards -->
    <div class="row mt-4">
      <div class="col-md-4 mb-3">
        <div class="card stat-card text-bg-primary text-white">
          <div class="card-body">
            <h6>Total Pelanggan</h6>
            <h3 class="fw-bold">124</h3>
            <p class="mb-0"><i class="bi bi-people-fill"></i> Aktif & terdaftar</p>
          </div>
        </div>
      </div>
      <div class="col-md-4 mb-3">
        <div class="card stat-card text-bg-warning text-dark">
          <div class="card-body">
            <h6>Tagihan Belum Dibayar</h6>
            <h3 class="fw-bold">42</h3>
            <p class="mb-0"><i class="bi bi-exclamation-triangle-fill"></i> Menunggu pembayaran</p>
          </div>
        </div>
      </div>
      <div class="col-md-4 mb-3">
        <div class="card stat-card text-bg-success text-white">
          <div class="card-body">
            <h6>Tagihan Lunas</h6>
            <h3 class="fw-bold">82</h3>
            <p class="mb-0"><i class="bi bi-check-circle-fill"></i> Pembayaran berhasil</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Grafik Placeholder -->
    <div class="card mt-4">
      <div class="card-body">
        <h5 class="card-title mb-3">Grafik Penggunaan Air</h5>
        <canvas id="waterChart" height="100"></canvas>
      </div>
    </div>
  </div>

  <!-- Bootstrap JS + Chart.js -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

  <script>
    // Contoh grafik dummy
    const ctx = document.getElementById('waterChart');
    new Chart(ctx, {
      type: 'line',
      data: {
        labels: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun'],
        datasets: [{
          label: 'Pemakaian Air (m³)',
          data: [820, 710, 950, 1050, 880, 930],
          borderColor: '#0d6efd',
          backgroundColor: 'rgba(13,110,253,0.1)',
          tension: 0.4
        }]
      },
      options: {
        responsive: true,
        plugins: {
          legend: { display: false }
        },
        scales: {
          y: { beginAtZero: true }
        }
      }
    });
  </script>

</body>
</html>
