<?php require_once("../controller/auth.php");
if (!isset($_GET["en"])) {
  header("Location: ./");
  exit;
} else {
  if (empty($_GET["en"])) {
    header("Location: ./");
    exit;
  } else {
    $en = valid($conn, $_GET["en"]);
    $selectUser = "SELECT * FROM users WHERE en_user='$en'";
    $selectUser = mysqli_query($conn, $selectUser);
    $data_account = mysqli_fetch_assoc($selectUser);
  }
}
$_SESSION["project_wisata_sumba_barat_daya"]["name_page"] = "Reset Password";
require_once("../templates/auth_top.php");

if($data_auth['model']==1){?>
<main class="auth-creative-wrapper">
  <div class="auth-creative-inner">
    <div class="creative-card-wrapper">
      <div class="card my-4 overflow-hidden" style="z-index: 1">
        <div class="row flex-1 g-0">
          <div class="col-lg-6 h-100 my-auto order-1 order-lg-0">
            <div
              class="wd-50 bg-white p-2 rounded-circle shadow-lg position-absolute translate-middle top-50 start-50 d-none d-lg-block">
              <img src="<?= $baseURL?>assets/img/<?= $data_utilities['logo']?>" alt="" class="img-fluid">
            </div>
            <div class="creative-card-body card-body p-sm-5">
              <h2 class="fs-20 fw-bolder mb-4">Reset</h2>
              <h4 class="fs-13 fw-bold mb-2">Atur ulang password anda</h4>
              <form action="" method="post" class="w-100 mt-4 pt-2">
                <div class="mb-4">
                  <input type="email" name="email" value="<?= $data_account['email'] ?>"
                    class="form-control form-control-user" id="email" placeholder="Email" readonly>
                </div>
                <div class="mb-4">
                  <input type="password" name="password" class="form-control form-control-user" id="password"
                    placeholder="Password" required>
                </div>
                <div class="mb-4">
                  <input type="password" name="re_password" class="form-control form-control-user" id="re_password"
                    placeholder="Konfirmasi Password" required>
                </div>
                <div class="mt-3">
                  <button type="submit" name="new_password" class="btn btn-lg btn-primary w-100">Atur Ulang</button>
                </div>
              </form>
              <div class="mt-5 text-muted">
                <span> Sudah punya akun?</span>
                <a href="./" class="fw-bold">Login</a>
              </div>
            </div>
          </div>
          <div class="col-lg-6 bg-primary order-0 order-lg-1">
            <div class="h-100 d-flex align-items-center justify-content-center">
              <img src="<?= $baseURL?>assets/img/auth/<?= $data_auth['image']?>" alt="" class="img-fluid" class="img-fluid" style="width: 100%; height: 100%; object-fit: cover;">
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</main>
<?php }else if($data_auth['model']==2){?>
<main class="auth-minimal-wrapper">
  <div class="auth-minimal-inner">
    <div class="minimal-card-wrapper">
      <div class="card mb-4 mt-5 mx-4 mx-sm-0 position-relative">
        <div class="wd-50 bg-white p-2 rounded-circle shadow-lg position-absolute translate-middle top-0 start-50">
          <img src="<?= $baseURL?>assets/img/<?= $data_utilities['logo']?>" alt="" class="img-fluid">
        </div>
        <div class="card-body p-sm-5">
          <h2 class="fs-20 fw-bolder mb-4">Reset</h2>
          <h4 class="fs-13 fw-bold mb-2">Atur ulang password anda</h4>
          <form action="" method="post" class="w-100 mt-4 pt-2">
            <div class="mb-4">
              <input type="email" name="email" value="<?= $data_account['email'] ?>"
                class="form-control form-control-user" id="email" placeholder="Email" readonly>
            </div>
            <div class="mb-4">
              <input type="password" name="password" class="form-control form-control-user" id="password"
                placeholder="Password" required>
            </div>
            <div class="mb-4">
              <input type="password" name="re_password" class="form-control form-control-user" id="re_password"
                placeholder="Konfirmasi Password" required>
            </div>
            <div class="mt-3">
              <button type="submit" name="new_password" class="btn btn-lg btn-primary w-100">Atur Ulang</button>
            </div>
          </form>
          <div class="mt-5 text-muted">
            <span> Sudah punya akun?</span>
            <a href="./" class="fw-bold">Login</a>
          </div>
        </div>
      </div>
    </div>
  </div>
</main>
<?php }
  require_once("../templates/auth_bottom.php") ?>
