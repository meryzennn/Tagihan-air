<?= $this->extend('layouts/admin_layout') ?>
<?= $this->section('content') ?>

<div class="container">
  <h3 class="mb-4">Data Penggunaan Air</h3>

  <?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
  <?php endif; ?>

  <a href="/penggunaan-air/create" class="btn btn-primary mb-3"><i class="bi bi-plus-circle"></i> Tambah Penggunaan</a>

  <div class="table-responsive">
    <table class="table table-bordered table-striped w-100">
      <thead class="table-light">
        <tr>
          <th>No</th>
          <th>No Pelanggan</th>
          <th>Nama Pelanggan</th>
          <th>Bulan</th>
          <th>Meter Awal</th>
          <th>Meter Akhir</th>
          <th>Total Pemakaian (m³)</th>
        </tr>
      </thead>
      <tbody>
        <?php if (!empty($penggunaan)): ?>
          <?php foreach ($penggunaan as $i => $p): ?>
            <tr>
              <td><?= $i + 1 ?></td>
              <td><?= esc($p['no_pelanggan']) ?></td>
              <td><?= esc($p['nama_lengkap']) ?></td>
              <td><?= date('F Y', strtotime($p['tanggal_pencatatan'])) ?></td>
              <td><?= esc($p['meter_awal']) ?></td>
              <td><?= esc($p['meter_akhir']) ?></td>
              <td><?= $p['meter_akhir'] - $p['meter_awal'] ?> m³</td>
            </tr>
          <?php endforeach; ?>
        <?php else: ?>
          <tr>
            <td colspan="7" class="text-center">Belum ada data penggunaan air.</td>
          </tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<?= $this->endSection() ?>
