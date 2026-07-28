<?php require_once("../../controller/manajemen-kunjungan.php");
$_SESSION["project_wisata_sumba_barat_daya"]["name_page"] = "Scan QR Wisatawan";
require_once("../../templates/views_top.php"); ?>

<div class="nxl-content">
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

  <div class="main-content">
    <div class="row g-4">
      <div class="col-xl-8">
        <div class="card stretch stretch-full">
          <div class="card-header d-flex flex-wrap align-items-center justify-content-between gap-2">
            <div>
              <h5 class="card-title mb-1">Pemindai Kamera</h5>
              <p class="text-muted fs-12 mb-0">Kamera belakang digunakan di HP, webcam default digunakan di laptop.</p>
            </div>
            <span class="badge bg-soft-secondary text-secondary" id="device-mode">Mendeteksi perangkat...</span>
          </div>
          <div class="card-body">
            <div class="position-relative overflow-hidden rounded border bg-dark mb-3" id="camera-container" style="min-height: 320px;">
              <video id="qr-video" class="d-block w-100" style="height: 420px; object-fit: cover;" autoplay muted playsinline></video>
              <canvas id="qr-canvas" class="d-none"></canvas>
              <div class="position-absolute top-50 start-50 translate-middle text-center text-white px-3" id="camera-placeholder">
                <i class="feather-camera fs-1 d-block mb-2"></i>
                <span>Kamera belum aktif</span>
              </div>
              <div class="position-absolute top-50 start-50 translate-middle border border-2 border-white rounded opacity-75 d-none" id="scan-frame" style="width: 230px; height: 230px; pointer-events: none;"></div>
            </div>

            <div class="alert alert-info py-2 mb-3" id="scanner-status" role="status">
              Menyiapkan pemindai. Scanner fisik dapat langsung digunakan pada kolom kode QR.
            </div>

            <div class="row g-3 mb-3">
              <div class="col-md-7">
                <label for="camera-select" class="form-label">Pilih Kamera</label>
                <select class="form-select" id="camera-select" disabled>
                  <option value="">Kamera otomatis</option>
                </select>
              </div>
              <div class="col-md-5 d-flex align-items-end gap-2">
                <button type="button" class="btn btn-primary flex-fill" id="start-camera">
                  <i class="feather-camera me-2"></i>Mulai
                </button>
                <button type="button" class="btn btn-outline-danger flex-fill" id="stop-camera" disabled>
                  <i class="feather-camera-off me-2"></i>Stop
                </button>
              </div>
            </div>

            <form action="" method="post" id="scan-form">
              <input type="hidden" name="scan_qr_wisatawan" value="1">
              <div class="mb-3">
                <label for="kode_qr" class="form-label">Kode QR / Scanner Fisik</label>
                <input type="text" name="kode_qr" class="form-control form-control-lg" id="kode_qr"
                  placeholder="Scan dengan alat atau masukkan kode QR" autocomplete="off" spellcheck="false" autofocus required>
                <small class="form-text text-muted">Scanner USB/Bluetooth akan terbaca seperti keyboard dan diproses otomatis.</small>
              </div>
              <div class="mb-3">
                <label for="keterangan" class="form-label">Keterangan</label>
                <textarea name="keterangan" class="form-control" id="keterangan" rows="3">Scan QR wisatawan</textarea>
              </div>
              <div class="hstack flex-wrap gap-2">
                <a href="data-kunjungan" class="btn btn-success">Kembali</a>
                <button type="submit" class="btn btn-primary" id="submit-scan">
                  <i class="feather-check-circle me-2"></i>Verifikasi & Simpan
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>

      <div class="col-xl-4">
        <div class="card stretch stretch-full">
          <div class="card-header">
            <h5 class="card-title mb-0">Mode Pemindaian</h5>
          </div>
          <div class="card-body">
            <div class="d-flex gap-3 mb-4">
              <span class="avatar-text bg-soft-primary text-primary"><i class="feather-smartphone"></i></span>
              <div>
                <h6 class="mb-1">HP / Tablet</h6>
                <p class="text-muted fs-12 mb-0">Sistem mengutamakan kamera belakang agar QR mudah diarahkan.</p>
              </div>
            </div>
            <div class="d-flex gap-3 mb-4">
              <span class="avatar-text bg-soft-success text-success"><i class="feather-monitor"></i></span>
              <div>
                <h6 class="mb-1">Laptop / Komputer</h6>
                <p class="text-muted fs-12 mb-0">Sistem memakai webcam default dan kamera lain dapat dipilih.</p>
              </div>
            </div>
            <div class="d-flex gap-3">
              <span class="avatar-text bg-soft-warning text-warning"><i class="feather-maximize"></i></span>
              <div>
                <h6 class="mb-1">Scanner QR Fisik</h6>
                <p class="text-muted fs-12 mb-0">Arahkan scanner ke QR. Kode diproses otomatis saat scanner mengirim Enter.</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/jsqr@1.4.0/dist/jsQR.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
  const video = document.getElementById('qr-video');
  const canvas = document.getElementById('qr-canvas');
  const canvasContext = canvas.getContext('2d', { willReadFrequently: true });
  const input = document.getElementById('kode_qr');
  const form = document.getElementById('scan-form');
  const submitButton = document.getElementById('submit-scan');
  const startButton = document.getElementById('start-camera');
  const stopButton = document.getElementById('stop-camera');
  const cameraSelect = document.getElementById('camera-select');
  const statusBox = document.getElementById('scanner-status');
  const deviceMode = document.getElementById('device-mode');
  const placeholder = document.getElementById('camera-placeholder');
  const scanFrame = document.getElementById('scan-frame');

  const isMobile = /Android|iPhone|iPad|iPod|Mobile/i.test(navigator.userAgent)
    || window.matchMedia('(pointer: coarse)').matches;
  let stream = null;
  let detector = null;
  let scanning = false;
  let submitting = false;
  let scanTimer = null;
  let lastInputAt = 0;
  let rapidInputCount = 0;
  let hardwareBuffer = '';
  let hardwareLastKeyAt = 0;
  let hardwareTimer = null;

  deviceMode.textContent = isMobile ? 'HP / Tablet — kamera belakang' : 'Laptop / Komputer — webcam';
  deviceMode.className = isMobile
    ? 'badge bg-soft-primary text-primary'
    : 'badge bg-soft-success text-success';

  function setStatus(message, type) {
    statusBox.textContent = message;
    statusBox.className = 'alert py-2 mb-3 alert-' + (type || 'info');
  }

  function submitCode(code, source) {
    const normalizedCode = String(code || '').trim();
    if (!normalizedCode || submitting) {
      return;
    }

    submitting = true;
    input.value = normalizedCode;
    input.readOnly = true;
    submitButton.disabled = true;
    setStatus('QR terbaca melalui ' + source + '. Memverifikasi tiket...', 'success');
    stopCamera();
    form.requestSubmit();
  }

  async function loadCameraOptions() {
    if (!navigator.mediaDevices || !navigator.mediaDevices.enumerateDevices) {
      return;
    }

    const devices = await navigator.mediaDevices.enumerateDevices();
    const cameras = devices.filter(function(device) {
      return device.kind === 'videoinput';
    });
    const previousValue = cameraSelect.value;
    cameraSelect.innerHTML = '<option value="">Kamera otomatis</option>';

    cameras.forEach(function(camera, index) {
      const option = document.createElement('option');
      option.value = camera.deviceId;
      option.textContent = camera.label || ('Kamera ' + (index + 1));
      cameraSelect.appendChild(option);
    });

    cameraSelect.disabled = cameras.length < 2;
    if (previousValue && cameras.some(function(camera) { return camera.deviceId === previousValue; })) {
      cameraSelect.value = previousValue;
    }
  }

  function stopCamera() {
    scanning = false;
    if (stream) {
      stream.getTracks().forEach(function(track) {
        track.stop();
      });
      stream = null;
    }
    video.srcObject = null;
    placeholder.classList.remove('d-none');
    scanFrame.classList.add('d-none');
    startButton.disabled = false;
    stopButton.disabled = true;
  }

  async function detectFrame() {
    if (!scanning || video.readyState < HTMLMediaElement.HAVE_CURRENT_DATA) {
      if (scanning) {
        requestAnimationFrame(detectFrame);
      }
      return;
    }

    try {
      if (detector) {
        const codes = await detector.detect(video);
        if (codes.length > 0) {
          submitCode(codes[0].rawValue, 'kamera');
          return;
        }
      } else if (window.jsQR) {
        const width = video.videoWidth;
        const height = video.videoHeight;
        if (width && height) {
          canvas.width = width;
          canvas.height = height;
          canvasContext.drawImage(video, 0, 0, width, height);
          const imageData = canvasContext.getImageData(0, 0, width, height);
          const result = window.jsQR(imageData.data, width, height, {
            inversionAttempts: 'dontInvert'
          });
          if (result && result.data) {
            submitCode(result.data, 'kamera');
            return;
          }
        }
      }
    } catch (error) {
      setStatus('Pemindaian kamera terganggu. Coba pilih kamera lain atau gunakan scanner fisik.', 'warning');
    }

    if (scanning) {
      requestAnimationFrame(detectFrame);
    }
  }

  async function startCamera() {
    if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
      setStatus('Browser tidak mendukung akses kamera. Gunakan scanner fisik atau masukkan kode secara manual.', 'warning');
      input.focus();
      return;
    }

    stopCamera();
    startButton.disabled = true;
    setStatus('Meminta izin dan menyiapkan kamera...', 'info');

    const selectedDevice = cameraSelect.value;
    const videoConstraints = selectedDevice
      ? { deviceId: { exact: selectedDevice } }
      : { facingMode: { ideal: isMobile ? 'environment' : 'user' } };

    try {
      stream = await navigator.mediaDevices.getUserMedia({
        video: videoConstraints,
        audio: false
      });
      video.srcObject = stream;
      await video.play();
      await loadCameraOptions();

      detector = null;
      if ('BarcodeDetector' in window) {
        try {
          detector = new BarcodeDetector({ formats: ['qr_code'] });
        } catch (error) {
          detector = null;
        }
      }

      if (!detector && !window.jsQR) {
        throw new Error('QR decoder tidak tersedia');
      }

      scanning = true;
      placeholder.classList.add('d-none');
      scanFrame.classList.remove('d-none');
      stopButton.disabled = false;
      setStatus('Kamera aktif. Arahkan kode QR ke dalam kotak pemindaian.', 'success');
      requestAnimationFrame(detectFrame);
    } catch (error) {
      stopCamera();
      const insecure = location.protocol !== 'https:' && location.hostname !== 'localhost' && location.hostname !== '127.0.0.1';
      setStatus(
        insecure
          ? 'Kamera browser memerlukan HTTPS. Gunakan HTTPS atau scanner fisik.'
          : 'Kamera tidak dapat dibuka. Periksa izin kamera, lalu coba kembali.',
        'danger'
      );
      input.focus();
    }
  }

  startButton.addEventListener('click', startCamera);
  stopButton.addEventListener('click', function() {
    stopCamera();
    setStatus('Kamera dihentikan. Scanner fisik tetap siap digunakan.', 'secondary');
    input.focus();
  });
  cameraSelect.addEventListener('change', startCamera);

  input.addEventListener('keydown', function(event) {
    if (event.key === 'Enter') {
      event.preventDefault();
      submitCode(input.value, 'scanner fisik/manual');
    }
  });

  input.addEventListener('input', function(event) {
    const now = performance.now();
    if (now - lastInputAt < 80) {
      rapidInputCount++;
    } else {
      rapidInputCount = 1;
    }
    lastInputAt = now;
    clearTimeout(scanTimer);

    if (event.inputType === 'insertFromPaste') {
      scanTimer = setTimeout(function() {
        submitCode(input.value, 'scanner fisik');
      }, 80);
      return;
    }

    scanTimer = setTimeout(function() {
      if (input.value.trim().length >= 6 && rapidInputCount >= Math.max(4, input.value.trim().length - 2)) {
        submitCode(input.value, 'scanner fisik');
      }
    }, 140);
  });

  document.addEventListener('keydown', function(event) {
    if (event.target === input || event.ctrlKey || event.altKey || event.metaKey) {
      return;
    }

    const targetTag = event.target.tagName ? event.target.tagName.toLowerCase() : '';
    if (targetTag === 'textarea' || targetTag === 'select' || event.target.isContentEditable) {
      return;
    }

    const now = performance.now();
    if (now - hardwareLastKeyAt > 100) {
      hardwareBuffer = '';
    }
    hardwareLastKeyAt = now;

    if (event.key === 'Enter') {
      if (hardwareBuffer.length >= 6) {
        event.preventDefault();
        submitCode(hardwareBuffer, 'scanner fisik');
      }
      hardwareBuffer = '';
      return;
    }

    if (event.key.length === 1) {
      hardwareBuffer += event.key;
      clearTimeout(hardwareTimer);
      hardwareTimer = setTimeout(function() {
        if (hardwareBuffer.length >= 6) {
          submitCode(hardwareBuffer, 'scanner fisik');
        }
        hardwareBuffer = '';
      }, 140);
    }
  });

  form.addEventListener('submit', function(event) {
    if (!input.value.trim()) {
      event.preventDefault();
      submitting = false;
      input.readOnly = false;
      submitButton.disabled = false;
      input.focus();
    } else {
      submitting = true;
      submitButton.disabled = true;
    }
  });

  window.addEventListener('pagehide', stopCamera);
  document.addEventListener('visibilitychange', function() {
    if (document.hidden && scanning) {
      stopCamera();
    }
  });

  loadCameraOptions();
  input.focus();
});
</script>

<?php require_once("../../templates/views_bottom.php") ?>
