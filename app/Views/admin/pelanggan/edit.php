<?= $this->extend('layouts/admin_layout') ?>
<?= $this->section('content') ?>

<div class="container">
  <h3 class="mb-4">Edit Data Pelanggan</h3>

  <form action="<?= base_url('/pelanggan/update/' . $pelanggan['id']) ?>" method="post">
    <div class="mb-3">
      <label for="no_pelanggan">Nomor Pelanggan</label>
      <input type="text" name="no_pelanggan" id="no_pelanggan" value="<?= $pelanggan['no_pelanggan'] ?>" class="form-control" required>
    </div>

    <div class="mb-3">
      <label for="nama">Nama</label>
      <input type="text" name="nama" id="nama" value="<?= $pelanggan['nama'] ?>" class="form-control" required>
    </div>

    <div class="mb-3">
      <label for="alamat">Alamat</label>
      <textarea name="alamat" id="alamat" class="form-control" required><?= $pelanggan['alamat'] ?></textarea>
    </div>

    <div class="mb-3">
      <label for="no_hp">Nomor HP</label>
      <input type="text" name="no_hp" id="no_hp" value="<?= $pelanggan['no_hp'] ?>" class="form-control" required>
    </div>

    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
    <a href="<?= base_url('/pelanggan') ?>" class="btn btn-secondary">Batal</a>
  </form>
</div>

<?= $this->endSection() ?>
