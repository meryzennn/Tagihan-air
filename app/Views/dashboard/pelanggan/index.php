<?= $this->extend('layouts/admin_layout') ?>

<?= $this->section('content') ?>
  <h4 class="mb-3">Data Pelanggan</h4>

  <a href="/pelanggan/create" class="btn btn-primary mb-3">+ Tambah Pelanggan</a>

  <table class="table table-bordered table-striped">
    <!-- Tabel pelanggan -->
  </table>
<?= $this->endSection() ?>
