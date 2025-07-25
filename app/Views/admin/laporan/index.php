<?= $this->extend('layouts/admin_layout') ?>
<?= $this->section('content') ?>

<div class="container">
  <h3 class="mb-4">Laporan Tagihan Air</h3>

  <!-- Filter -->
  <form method="get" action="<?= base_url('laporan') ?>" class="row g-3 mb-3">
    <div class="col-md-4">
      <label for="from" class="form-label">Dari Tanggal</label>
      <input type="date" name="from" id="from" class="form-control" value="<?= esc($from ?? '') ?>">
    </div>
    <div class="col-md-4">
      <label for="to" class="form-label">Sampai Tanggal</label>
      <input type="date" name="to" id="to" class="form-control" value="<?= esc($to ?? '') ?>">
    </div>
    <div class="col-md-4 d-flex align-items-end">
      <button type="submit" class="btn btn-primary me-2">Filter</button>
      <a href="<?= base_url('/admin/laporan/export/excel?from=' . ($from ?? '') . '&to=' . ($to ?? '')) ?>" class="btn btn-success me-2">
        <i class="bi bi-file-earmark-excel"></i> Excel
      </a>
      <a href="<?= base_url('/admin/laporan/export/pdf?from=' . ($from ?? '') . '&to=' . ($to ?? '')) ?>" class="btn btn-danger">
        <i class="bi bi-file-earmark-pdf"></i> PDF
      </a>
    </div>
  </form>

  <!-- Table -->
  <div class="table-responsive">
    <table class="table table-bordered table-striped">
      <thead class="table-light">
        <tr>
          <th>No</th>
          <th>No Pelanggan</th>
          <th>Nama</th>
          <th>Tanggal</th>
          <th>Pemakaian (m³)</th>
          <th>Total Tagihan</th>
          <th>Status</th>
        </tr>
      </thead>
      <tbody>
        <?php if (!empty($laporan)): ?>
          <?php foreach ($laporan as $i => $row): ?>
            <tr>
              <td><?= $i + 1 ?></td>
              <td><?= esc($row['no_pelanggan']) ?></td>
              <td><?= esc($row['nama_lengkap']) ?></td>
              <td><?= date('d-m-Y', strtotime($row['tanggal_pencatatan'])) ?></td>
              <td><?= esc($row['meter_akhir'] - $row['meter_awal']) ?> m³</td>
              <td>Rp <?= number_format($row['total_tagihan'], 0, ',', '.') ?></td>
              <td>
                <?php if ($row['status'] === 'Lunas'): ?>
                  <span class="badge bg-success">Lunas</span>
                <?php else: ?>
                  <span class="badge bg-danger">Belum Dibayar</span>
                <?php endif; ?>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php else: ?>
          <tr><td colspan="7" class="text-center">Tidak ada data.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<?= $this->endSection() ?>
