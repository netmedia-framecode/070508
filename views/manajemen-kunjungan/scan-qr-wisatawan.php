<?php require_once("../../controller/manajemen-kunjungan.php");
$_SESSION["project_wisata_sumba_barat_daya"]["name_page"] = "Scan QR Wisatawan";
require_once("../../templates/views_top.php"); ?>

<div class="nxl-content">

  <!-- [ page-header ] start -->
  <div class="page-header">
    <div class="page-header-left d-flex align-items-center">
      <div class="page-header-title">
        <h5 class="m-b-10"><?= $_SESSION["project_wisata_sumba_barat_daya"]["name_page"] ?></h5>
      </div>
      <ul class="breadcrumb">
        <li class="breadcrumb-item">Manajemen Kunjungan</li>
        <li class="breadcrumb-item"><?= $_SESSION["project_wisata_sumba_barat_daya"]["name_page"] ?></li>
      </ul>
    </div>
  </div>
  <!-- [ page-header ] end -->

  <!-- [ Main Content ] start -->
  <div class="main-content">
    <div class="row">
      <div class="col-lg-8">
        <div class="card stretch stretch-full">
          <div class="card-body">
            <div class="mb-3">
              <video id="qr-video" class="w-100 rounded border bg-dark" style="max-height: 420px; object-fit: cover;" autoplay muted playsinline></video>
            </div>
            <form action="" method="post" id="scan-form">
              <input type="hidden" name="scan_qr_wisatawan" value="1">
              <div class="mb-3">
                <label for="kode_qr" class="form-label">Kode QR</label>
                <input type="text" name="kode_qr" class="form-control" id="kode_qr" autocomplete="off" required>
              </div>
              <div class="mb-3">
                <label for="keterangan" class="form-label">Keterangan</label>
                <textarea name="keterangan" class="form-control" id="keterangan" rows="3">Scan QR wisatawan</textarea>
              </div>
              <div class="mb-3 hstack gap-2 justify-content-left">
                <a href="data-kunjungan" class="btn btn-success">Kembali</a>
                <button type="button" class="btn btn-secondary" id="start-camera">Mulai Kamera</button>
                <button type="submit" class="btn btn-primary">Simpan Kunjungan</button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- [ Main Content ] end -->

</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
  const video = document.getElementById('qr-video');
  const input = document.getElementById('kode_qr');
  const form = document.getElementById('scan-form');
  const startButton = document.getElementById('start-camera');
  let stream = null;
  let scanning = false;

  const stopCamera = function() {
    if (stream) {
      stream.getTracks().forEach(function(track) {
        track.stop();
      });
    }
    scanning = false;
  };

  const scanLoop = async function(detector) {
    if (!scanning) {
      return;
    }

    try {
      const codes = await detector.detect(video);
      if (codes.length > 0) {
        input.value = codes[0].rawValue;
        stopCamera();
        form.submit();
        return;
      }
    } catch (error) {
      scanning = false;
    }

    requestAnimationFrame(function() {
      scanLoop(detector);
    });
  };

  const startCamera = async function() {
    if (!('BarcodeDetector' in window)) {
      input.focus();
      return;
    }

    try {
      stream = await navigator.mediaDevices.getUserMedia({
        video: {
          facingMode: 'environment'
        }
      });
      video.srcObject = stream;
      const detector = new BarcodeDetector({
        formats: ['qr_code']
      });
      scanning = true;
      scanLoop(detector);
    } catch (error) {
      input.focus();
    }
  };

  startButton.addEventListener('click', startCamera);
});
</script>

<?php require_once("../../templates/views_bottom.php") ?>
