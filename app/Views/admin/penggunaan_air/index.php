<?= $this->extend('layouts/admin_layout') ?>

<?= $this->section('content') ?>
<div class="container">
  <h3 class="mb-4">Data Penggunaan Air</h3>

  <?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
  <?php endif; ?>

  <a href="<?= base_url('/penggunaan-air/create') ?>" class="btn btn-primary mb-3">
    <i class="bi bi-plus-circle"></i> Tambah Penggunaan
  </a>

  <div class="table-responsive">
    <table class="table table-bordered table-striped">
      <thead class="table-light">
        <tr>
          <th>No</th>
          <th>Nama Pelanggan</th>
          <th>No. Pelanggan</th>
          <th>Bulan</th>
          <th>Tahun</th>
          <th>Meter Awal</th>
          <th>Meter Akhir</th>
          <th>Pemakaian (m³)</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody>
        <?php if (!empty($penggunaan)): ?>
          <?php foreach ($penggunaan as $i => $p): ?>
            <tr>
              <td><?= $i + 1 ?></td>
              <td><?= esc($p['nama_lengkap']) ?></td>
              <td><?= esc($p['no_pelanggan']) ?></td>
              <td><?= esc($p['bulan']) ?></td>
              <td><?= esc($p['tahun']) ?></td>
              <td><?= esc($p['meter_awal']) ?></td>
              <td><?= esc($p['meter_akhir']) ?></td>
              <td><?= esc($p['meter_akhir'] - $p['meter_awal']) ?></td>
              <td>
                <a href="<?= base_url('/penggunaan-air/edit/' . $p['id']) ?>" class="btn btn-sm btn-warning">
                  <i class="bi bi-pencil-square"></i>
                </a>
                <a href="<?= base_url('/penggunaan-air/delete/' . $p['id']) ?>" class="btn btn-sm btn-danger" onclick="return confirm('Yakin ingin menghapus data ini?')">
                  <i class="bi bi-trash"></i>
                </a>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php else: ?>
          <tr>
            <td colspan="9" class="text-center">Belum ada data penggunaan air.</td>
          </tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>
<?= $this->endSection() ?>
