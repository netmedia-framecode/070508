<?php require_once("../../controller/user-management.php");
$_SESSION["project_wisata_sumba_barat_daya"]["name_page"] = "Role";
require_once("../../templates/views_top.php"); ?>

<div class="nxl-content">

  <!-- [ page-header ] start -->
  <div class="page-header">
    <div class="page-header-left d-flex align-items-center">
      <div class="page-header-title">
        <h5 class="m-b-10"><?= $_SESSION['project_wisata_sumba_barat_daya']['name_page'] ?></h5>
      </div>
      <ul class="breadcrumb">
        <li class="breadcrumb-item">User Management</li>
        <li class="breadcrumb-item"><?= $_SESSION['project_wisata_sumba_barat_daya']['name_page'] ?></li>
      </ul>
    </div>
    <div class="page-header-right ms-auto">
      <div class="page-header-right-items">
        <div class="d-flex d-md-none">
          <a href="javascript:void(0)" class="page-header-right-close-toggle">
            <i class="feather-arrow-left me-2"></i>
            <span>Back</span>
          </a>
        </div>
        <div class="d-flex align-items-center gap-2 page-header-right-items-wrapper">
          <?php if (canAction('create')) { ?>
          <a href="add-role" class="btn btn-primary">
            <i class="feather-plus me-2"></i>
            <span>Tambah</span>
          </a>
          <?php } ?>
        </div>
      </div>
      <div class="d-md-none d-flex align-items-center">
        <a href="javascript:void(0)" class="page-header-right-open-toggle">
          <i class="feather-align-right fs-20"></i>
        </a>
      </div>
    </div>
  </div>
  <!-- [ page-header ] end -->

  <!-- [ Main Content ] start -->
  <div class="main-content">
    <div class="row">
      <div class="col-lg-12">
        <div class="card stretch stretch-full">
          <div class="card-body p-0">
            <div class="table-responsive">
              <table class="table table-hover" id="dataTable">
                <thead>
                  <tr>
                    <th class="text-center">#</th>
                    <th class="text-center">Role</th>
                    <th class="text-center">Aksi</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($views_user_role as $key => $data) { ?>
                  <tr class="single-item">
                    <td class="text-center"><?= $key + 1 ?></td>
                    <td><?= $data['role'] ?></td>
                    <td>
                      <div class="hstack gap-2 justify-content-center">
                        <?php if (canAction('edit')) { ?>
                        <a href="edit-role?p=<?= $data['id_role']?>" class="btn btn-warning btn-sm">
                          <i class="bi bi-pencil-square"></i>
                        </a>
                        <?php } ?>
                        <?php if (canAction('delete')) { ?>
                        <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#deleteRoleModal<?= $data['id_role'] ?>">
                          <i class="bi bi-trash"></i>
                        </button>

                        <div class="modal fade role-delete-modal" id="deleteRoleModal<?= $data['id_role'] ?>" tabindex="-1" aria-labelledby="deleteRoleModalLabel<?= $data['id_role'] ?>" aria-hidden="true">
                          <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                              <div class="modal-header">
                                <h5 class="modal-title" id="deleteRoleModalLabel<?= $data['id_role'] ?>">Konfirmasi Hapus Role</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                              </div>
                              <div class="modal-body text-start">
                                Apakah anda yakin ingin menghapus role <strong><?= htmlspecialchars($data['role']) ?></strong>?
                              </div>
                              <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                <form action="" method="post" class="d-inline">
                                  <input type="hidden" name="id_role" value="<?= $data['id_role'] ?>">
                                  <input type="hidden" name="role" value="<?= htmlspecialchars($data['role']) ?>">
                                  <button type="submit" name="delete_role" class="btn btn-danger">Hapus</button>
                                </form>
                              </div>
                            </div>
                          </div>
                        </div>
                        <?php } ?>
                      </div>
                    </td>
                  </tr>
                  <?php } ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- [ Main Content ] end -->

</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
  document.querySelectorAll('.role-delete-modal').forEach(function(modal) {
    document.body.appendChild(modal);
  });
});
</script>

<?php require_once("../../templates/views_bottom.php") ?>
