<?php require_once("../controller/menu-management.php");
$_SESSION["project_wisata_sumba_barat_daya"]["name_page"] = "Konfigurasi Hak Akses";
require_once("../templates/views_top.php"); ?>

<style>
  /* Kustomisasi styling untuk tabel */
  .access-table th {
    text-transform: uppercase;
    font-size: 12px;
    letter-spacing: 0.5px;
  }

  .menu-header {
    background-color: #f8f9fa;
    border-top: 2px solid #e9ecef;
  }

  .menu-header td {
    font-weight: 600;
    color: #343a40;
    font-size: 14px;
  }

  .sub-menu-row:hover {
    background-color: #f1f5f9;
    transition: 0.2s;
  }

  /* Memperbesar ukuran toggle switch */
  .large-switch {
    transform: scale(1.3);
    cursor: pointer;
    margin-left: -1rem;
  }

  .form-switch {
    min-height: 1.5rem;
    display: flex;
    justify-content: center;
    align-items: center;
  }

  /* Kustomisasi Warna Toggle Switch saat ON (Checked) */
  .switch-view:checked {
    background-color: #0dcaf0 !important;
    border-color: #0dcaf0 !important;
  }

  /* Info / Biru Muda */
  .switch-create:checked {
    background-color: #198754 !important;
    border-color: #198754 !important;
  }

  /* Success / Hijau */
  .switch-edit:checked {
    background-color: #ffc107 !important;
    border-color: #ffc107 !important;
  }

  /* Warning / Kuning */
  .switch-delete:checked {
    background-color: #dc3545 !important;
    border-color: #dc3545 !important;
  }

  /* Danger / Merah */

  /* Desain Tombol Check All */
  .btn-check-all {
    font-size: 12px;
    padding: 5px 12px;
    border-radius: 6px;
    font-weight: 500;
    transition: all 0.2s ease-in-out;
  }
</style>

<div class="nxl-content">
  <div class="page-header">
    <div class="page-header-left d-flex align-items-center">
      <div class="page-header-title">
        <h5 class="m-b-10"><?= $_SESSION['project_wisata_sumba_barat_daya']['name_page'] ?></h5>
      </div>
    </div>
  </div>
  <div class="main-content">
    <div class="row">
      <div class="col-12">
        <div class="card shadow-sm border-0">
          <div class="card-header bg-white d-flex align-items-center justify-content-between p-4">
            <div>
              <h5 class="mb-1 fw-bold text-primary">Kelola Hak Akses Role</h5>
              <p class="text-muted mb-0" style="font-size: 13px;">Atur izin tampilan dan aksi (Create, Edit, Delete) untuk role yang dipilih.</p>
            </div>
            <form action="" method="POST" class="d-flex align-items-center bg-light p-2 rounded border">
              <i class="ti ti-users text-muted m-r-10 ms-2" style="font-size: 20px;"></i>
              <label class="mb-0 me-2 fw-medium">Pilih Role:</label>
              <select name="role_permission" class="form-select form-select-sm border-0 shadow-none bg-light" style="width: 200px; cursor: pointer; font-weight: 600;" onchange="this.form.submit()">
                <?php while ($role = mysqli_fetch_assoc($views_user_role)) : ?>
                  <option value="<?= $role['id_role'] ?>" <?= ($id_role_permission == $role['id_role']) ? 'selected' : '' ?>>
                    <?= strtoupper($role['role']) ?>
                  </option>
                <?php endwhile; ?>
              </select>
            </form>
          </div>
          <form action="" method="POST" id="formPermission">
            <input type="hidden" name="id_role" value="<?= $id_role_permission ?>">
            <div class="card-body p-0">
              <div class="table-responsive">
                <table class="table access-table mb-0 align-middle">
                  <thead class="bg-white text-muted">
                    <tr>
                      <th style="width: 35%; padding-left: 1.5rem;">Modul / Halaman</th>
                      <th class="text-center"><i class="ti ti-eye text-info m-r-5"></i> View</th>
                      <th class="text-center"><i class="ti ti-plus text-success m-r-5"></i> Create</th>
                      <th class="text-center"><i class="ti ti-edit text-warning m-r-5"></i> Edit</th>
                      <th class="text-center"><i class="ti ti-trash text-danger m-r-5"></i> Delete</th>
                      <th class="text-center text-muted"><i class="ti ti-settings m-r-5"></i> Aksi Cepat</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php
                    $current_menu = null;
                    if ($views_permissions instanceof mysqli_result) {
                      mysqli_data_seek($views_permissions, 0);
                      while ($row = mysqli_fetch_assoc($views_permissions)) :
                      if ($current_menu !== $row['id_menu']) :
                        $current_menu = $row['id_menu'];
                    ?>
                        <tr class="menu-header">
                          <td colspan="6" style="padding-left: 1.5rem;">
                            <i class="<?= $row['icon'] ?> text-primary m-r-10 fs-5 align-middle"></i>
                            <?= strtoupper($row['menu']) ?>
                          </td>
                        </tr>
                      <?php endif;
                      if ($row['id_sub_menu']) : ?><tr class="sub-menu-row">
                          <td style="padding-left: 3rem;">
                            <div class="d-flex align-items-center">
                              <i class="ti ti-corner-down-right text-muted m-r-10"></i>
                              <span class="fw-medium text-dark"><?= $row['title'] ?></span>
                            </div>
                          </td>
                          <td class="text-center">
                            <div class="form-check form-switch m-0">
                              <input class="form-check-input large-switch switch-view sub-<?= $row['id_sub_menu'] ?>" type="checkbox" name="access[<?= $row['id_sub_menu'] ?>][view]" value="1" <?= ($row['view'] == 1) ? 'checked' : '' ?>>
                            </div>
                          </td>
                          <td class="text-center">
                            <div class="form-check form-switch m-0">
                              <input class="form-check-input large-switch switch-create sub-<?= $row['id_sub_menu'] ?>" type="checkbox" name="access[<?= $row['id_sub_menu'] ?>][create]" value="1" <?= ($row['create'] == 1) ? 'checked' : '' ?>>
                            </div>
                          </td>
                          <td class="text-center">
                            <div class="form-check form-switch m-0">
                              <input class="form-check-input large-switch switch-edit sub-<?= $row['id_sub_menu'] ?>" type="checkbox" name="access[<?= $row['id_sub_menu'] ?>][edit]" value="1" <?= ($row['edit'] == 1) ? 'checked' : '' ?>>
                            </div>
                          </td>
                          <td class="text-center">
                            <div class="form-check form-switch m-0">
                              <input class="form-check-input large-switch switch-delete sub-<?= $row['id_sub_menu'] ?>" type="checkbox" name="access[<?= $row['id_sub_menu'] ?>][delete]" value="1" <?= ($row['delete'] == 1) ? 'checked' : '' ?>>
                            </div>
                          </td>
                          <td class="text-center">
                            <button type="button" class="btn d-inline-flex align-items-center justify-content-center gap-2 btn-outline-secondary btn-check-all" onclick="toggleRow(<?= $row['id_sub_menu'] ?>, this)">
                              <i class="bi bi-check2-all"></i> <span>Check All</span>
                            </button>
                          </td>
                        </tr>
                    <?php endif;
                      endwhile;
                    } ?>
                  </tbody>
                </table>
              </div>
            </div>
            <div class="card-footer bg-white border-top p-4 d-flex justify-content-between align-items-center">
              <span class="text-muted" style="font-size: 13px;">
                <i class="ti ti-info-circle text-info m-r-5"></i> Pastikan menekan tombol simpan sebelum memilih role lain.
              </span>
              <button type="submit" name="update_permission" class="btn btn-primary px-4 py-3 shadow-sm">
                <i class="bi bi-check2-all m-r-5"></i> Simpan Hak Akses
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
  function toggleRow(idSubMenu, btnElement) {
    const checkboxes = document.querySelectorAll('.sub-' + idSubMenu);
    let allChecked = true;

    // Cek apakah semua sudah tercentang
    checkboxes.forEach(cb => {
      if (!cb.checked) allChecked = false;
    });

    // Toggle status
    checkboxes.forEach(cb => {
      cb.checked = !allChecked;
    });

    // Ubah visual tombol dengan efek transisi warna
    const icon = btnElement.querySelector('i');
    const text = btnElement.querySelector('span');

    if (!allChecked) {
      btnElement.classList.remove('btn-outline-secondary');
      btnElement.classList.add('btn-primary', 'text-white');
      icon.className = 'bi bi-check2-all fs-6';
      text.innerText = 'Uncheck All';
    } else {
      btnElement.classList.remove('btn-primary', 'text-white');
      btnElement.classList.add('btn-outline-secondary');
      icon.className = 'bi bi-check2-circle fs-6';
      text.innerText = 'Check All';
    }
  }
</script>

<?php require_once("../templates/views_bottom.php") ?>
