<?= $this->extend('layouts/admin_layout') ?>

<?= $this->section('content') ?>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<div class="container">
  <h3 class="mb-4">Data Pelanggan</h3>

  <?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
  <?php endif; ?>

  <!-- <a href="/pelanggan/create" class="btn btn-primary mb-3"><i class="bi bi-plus-circle"></i> Tambah Pelanggan</a> -->

  <div class="table-responsive">
    <a href="<?= base_url('pelanggan/export') ?>" class="btn btn-success mb-3">
    <i class="bi bi-file-earmark-excel"></i> Export Excel
    </a>

    <table class="table table-bordered table-striped w-100">
      <thead class="table-light">
        <tr>
          <th>No</th>
          <th>Nama</th>
          <th>No Pelanggan</th>
          <th>Alamat</th>
          <th>No HP</th>
          <th>Waktu Daftar</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody>
        <?php if (!empty($pelanggan)): ?>
          <?php foreach ($pelanggan as $i => $p): ?>
            <tr>
              <td><?= $i + 1 ?></td>
              <td><?= esc($p['nama_lengkap']) ?></td>
              <td><?= esc($p['no_pelanggan']) ?></td>
              <td><?= esc($p['alamat']) ?></td>
              <td><?= esc($p['no_hp']) ?></td>
              <td>
                <?= isset($p['created_at']) ? date('d M Y', strtotime($p['created_at'])) : '-' ?>
              </td>
              <td>
                <a href="/pelanggan/edit/<?= $p['id_user'] ?>" class="btn btn-sm btn-warning">
                  <i class="bi bi-pencil-square"></i>
                </a>
                <a href="#" class="btn btn-sm btn-danger btn-delete" 
                   data-id="<?= $p['id_user'] ?>" 
                   data-nama="<?= esc($p['nama_lengkap']) ?>">
                  <i class="bi bi-trash"></i>
                </a>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php else: ?>
          <tr>
            <td colspan="7" class="text-center">Belum ada data pelanggan.</td>
          </tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<!-- SweetAlert Delete Handler -->
<script>
  document.querySelectorAll('.btn-delete').forEach(button => {
    button.addEventListener('click', function () {
      const id = this.getAttribute('data-id');
      const nama = this.getAttribute('data-nama');

      Swal.fire({
        title: 'Yakin ingin menghapus?',
        text: `Data pelanggan "${nama}" akan dihapus!`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal'
      }).then((result) => {
        if (result.isConfirmed) {
          window.location.href = `/pelanggan/delete/${id}`;
        }
      });
    });
  });
</script>

<?php if (session()->getFlashdata('success')): ?>
<script>
  Swal.fire({
    icon: 'success',
    title: 'Sukses!',
    text: '<?= session()->getFlashdata('success') ?>',
    timer: 2000,
    showConfirmButton: false
  });
</script>
<?php endif; ?>

<?= $this->endSection() ?>
