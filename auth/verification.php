<?php require_once("../controller/auth.php");
if (isset($_SESSION["data_auth"]["en_user"])) {
  $enValue = $_GET["en"];
  $checkEN = "SELECT * FROM users WHERE en_user='$enValue'";
  $checkEN = mysqli_query($conn, $checkEN);
  if (mysqli_num_rows($checkEN) == 0) {
    $_SESSION["project_wisata_sumba_barat_daya"] = [
      "message-warning" => "Maaf, sepertinya ada kesalahan saat mendaftar.",
      "time-message" => time()
    ];
    header("Location: register");
    exit;
  } else if (mysqli_num_rows($checkEN) > 0) {
    if (!isset($_SESSION["en"]) || $_SESSION["en"] !== $enValue) {
      $_SESSION["en"] = $enValue;
      unset($_SESSION["countdown_started"]);
    }
  }
}
$_SESSION["project_wisata_sumba_barat_daya"]["name_page"] = "Verifikasi";
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
              <h2 class="fs-20 fw-bolder mb-4">Verification</h2>
              <h4 class="fs-13 fw-bold mb-2">Verifikasi akun anda</h4>
              <p class="fs-12 fw-medium text-muted">Masukan kode token yang kami kirim di email untuk memverifikasi akun
                disini!</p>
              <form action="" method="post" class="w-100 mt-4 pt-2">
                <div class="mb-4">
                  <input type="hidden" name="en_user" value="<?= $_GET["en"] ?>">
                  <input type="number" name="token" class="form-control form-control-user" id="token"
                    placeholder="Token" required>
                </div>
                <div class="mt-3">
                  <button type="submit" name="verifikasi" class="btn btn-lg btn-primary w-100">Verifikasi</button>
                </div>
              </form>
              <div class="mt-5 text-muted">
                <span id="countdown" class="text-center text-dark"></span>
              </div>
              <form id="data-message" style="display:none;" class="mt-4" method="post">
                <input type="hidden" name="en_user" value="<?= $_GET["en"] ?>">
                <span id="message" class="text-center text-danger"></span>
                <button type="submit" name="re_verifikasi" class="btn btn-link m-0 p-0 text-danger">Kirim ulang
                  token</button>
              </form>
              <div class="mt-3 text-muted">
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
          <h2 class="fs-20 fw-bolder mb-4">Verification</h2>
          <h4 class="fs-13 fw-bold mb-2">Verifikasi akun anda</h4>
          <p class="fs-12 fw-medium text-muted">Masukan kode token yang kami kirim di email untuk memverifikasi akun
            disini!</p>
          <form action="" method="post" class="w-100 mt-4 pt-2">
            <div class="mb-4">
              <input type="hidden" name="en_user" value="<?= $_GET["en"] ?>">
              <input type="number" name="token" class="form-control form-control-user" id="token" placeholder="Token"
                required>
            </div>
            <div class="mt-3">
              <button type="submit" name="verifikasi" class="btn btn-lg btn-primary w-100">Verifikasi</button>
            </div>
          </form>
          <div class="mt-5 text-muted">
            <span id="countdown" class="text-center text-dark"></span>
          </div>
          <form id="data-message" style="display:none;" class="mt-4" method="post">
            <input type="hidden" name="en_user" value="<?= $_GET["en"] ?>">
            <span id="message" class="text-center text-danger"></span>
            <button type="submit" name="re_verifikasi" class="btn btn-link m-0 p-0 text-danger">Kirim ulang
              token</button>
          </form>
          <div class="mt-3 text-muted">
            <span> Sudah punya akun?</span>
            <a href="./" class="fw-bold">Login</a>
          </div>
        </div>
      </div>
    </div>
  </div>
</main>
<?php }?>
<script>
// Fungsi untuk mengirimrim permintaan Ajax ke server PHP untuk memperbarui sesi
function updateSession() {
  var xhr = new XMLHttpRequest();
  xhr.open("GET", "verify_session.php", true);
  xhr.onreadystatechange = function() {
    if (xhr.readyState == 4 && xhr.status == 200) {
      var response = JSON.parse(xhr.responseText);
      if (response.message) {
        document.getElementById("message").textContent = response.message;
        document.getElementById("data-message").style.display = "block";
      }
    }
  };
  xhr.send();
}

// Fungsi untuk menghitung mundur
function startCountdown(duration, display) {
  var timer = duration,
    minutes, seconds;

  function updateTimer() {
    minutes = parseInt(timer / 60, 10);
    seconds = parseInt(timer % 60, 10);

    minutes = minutes < 10 ? "0" + minutes : minutes;
    seconds = seconds < 10 ? "0" + seconds : seconds;

    display.textContent = "Waktu untuk verifikasi " + minutes + ":" + seconds;

    if (--timer < 0) {
      clearInterval(intervalId); // Hentikan interval setelah waktu hitung mundur selesai
      updateSession(); // Panggil fungsi untuk memperbarui sesi PHP

      // Sembunyikan elemen dengan ID "countdown"
      display.style.display = "none";
    }
  }

  // Periksa apakah waktu hitung mundur sudah dimulai di sesi
  var sessionStarted = <?php echo isset($_SESSION['countdown_started']) ? 'true' : 'false'; ?>;

  if (!sessionStarted) {
    // Jika belum dimulai, mulai waktu hitung mundur
    updateTimer();
    var intervalId = setInterval(updateTimer, 1000);

    // Tandai bahwa waktu hitung mundur telah dimulai di sesi
    <?php $_SESSION['countdown_started'] = true; ?>
  }
}

document.addEventListener("DOMContentLoaded", function() {
  var oneMinute = 60 * 5, // satu menit
    display = document.getElementById("countdown");
  startCountdown(oneMinute, display);
});
</script>
<?php require_once("../templates/auth_bottom.php") ?>
