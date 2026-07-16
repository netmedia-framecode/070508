<?php require_once("../../controller/laporan.php");
$_SESSION["project_wisata_sumba_barat_daya"]["name_page"] = "Laporan Kunjungan";
require_once("../../templates/views_top.php"); ?>

<style>
  .visit-summary {
    border: 1px solid #edf0f5;
    border-radius: 8px;
    background: #fff;
    box-shadow: 0 10px 24px rgba(15, 23, 42, .05);
    overflow: hidden;
  }

  .visit-summary .summary-accent {
    width: 100%;
    height: 4px;
  }

  .visit-summary .summary-icon {
    width: 38px;
    height: 38px;
    border-radius: 50%;
    display: grid;
    place-items: center;
    background: #f5f7fb;
    flex: 0 0 38px;
  }

  .visit-builder-card {
    border: 1px solid #edf0f5;
    box-shadow: 0 10px 24px rgba(15, 23, 42, .05);
  }

  .visit-keyword-list,
  .visit-dropzone {
    min-height: 230px;
    border: 1px dashed #cfd6e4;
    border-radius: 8px;
    padding: 14px;
    background: #fbfcfe;
  }

  .visit-keyword-chip {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    border: 1px solid #dfe4ee;
    border-radius: 999px;
    padding: 8px 12px;
    margin: 0 8px 8px 0;
    background: #fff;
    color: #2d3748;
    cursor: grab;
    user-select: none;
  }

  .visit-keyword-chip:active {
    cursor: grabbing;
  }

  .visit-dropzone.is-over {
    border-color: #25b865;
    background: rgba(37, 184, 101, .08);
  }

  .visit-empty-state {
    color: #8a94a6;
    min-height: 190px;
    display: grid;
    place-items: center;
    text-align: center;
  }

  .visit-hidden {
    display: none !important;
  }

  .visit-result-table {
    min-width: 1000px;
  }
</style>

<div class="nxl-content">

  <!-- [ page-header ] start -->
  <div class="page-header">
    <div class="page-header-left d-flex align-items-center">
      <div class="page-header-title">
        <h5 class="m-b-10"><?= $_SESSION["project_wisata_sumba_barat_daya"]["name_page"] ?></h5>
      </div>
      <ul class="breadcrumb">
        <li class="breadcrumb-item">Laporan</li>
        <li class="breadcrumb-item"><?= $_SESSION["project_wisata_sumba_barat_daya"]["name_page"] ?></li>
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
          <a href="kunjungan?export_kunjungan_excel=1" class="btn btn-success">
            <i class="bi bi-file-earmark-excel me-2"></i>
            <span>Export Excel</span>
          </a>
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
    <div class="row g-4">
      <div class="col-xxl-3 col-md-6">
        <div class="visit-summary stretch stretch-full">
          <div class="summary-accent bg-success"></div>
          <div class="p-4 d-flex align-items-center justify-content-between gap-3">
            <div>
              <span class="text-muted d-block">Total Kunjungan</span>
              <h4 class="mb-0"><?= number_format($jumlah_laporan_kunjungan, 0, ',', '.') ?></h4>
              <small>data scan masuk</small>
            </div>
            <div class="summary-icon text-success"><i class="bi bi-qr-code-scan"></i></div>
          </div>
        </div>
      </div>
      <div class="col-xxl-3 col-md-6">
        <div class="visit-summary stretch stretch-full">
          <div class="summary-accent bg-primary"></div>
          <div class="p-4 d-flex align-items-center justify-content-between gap-3">
            <div>
              <span class="text-muted d-block">Kunjungan Hari Ini</span>
              <h4 class="mb-0"><?= number_format($jumlah_kunjungan_hari_ini, 0, ',', '.') ?></h4>
              <small><?= date('d/m/Y') ?></small>
            </div>
            <div class="summary-icon text-primary"><i class="bi bi-calendar-check"></i></div>
          </div>
        </div>
      </div>
      <div class="col-xxl-3 col-md-6">
        <div class="visit-summary stretch stretch-full">
          <div class="summary-accent bg-warning"></div>
          <div class="p-4 d-flex align-items-center justify-content-between gap-3">
            <div>
              <span class="text-muted d-block">Wisatawan Terdata</span>
              <h4 class="mb-0"><?= number_format($jumlah_wisatawan_kunjungan, 0, ',', '.') ?></h4>
              <small>wisatawan unik</small>
            </div>
            <div class="summary-icon text-warning"><i class="bi bi-person-check"></i></div>
          </div>
        </div>
      </div>
      <div class="col-xxl-3 col-md-6">
        <div class="visit-summary stretch stretch-full">
          <div class="summary-accent bg-info"></div>
          <div class="p-4 d-flex align-items-center justify-content-between gap-3">
            <div>
              <span class="text-muted d-block">Objek Dikunjungi</span>
              <h4 class="mb-0"><?= number_format($jumlah_objek_kunjungan, 0, ',', '.') ?></h4>
              <small>objek wisata unik</small>
            </div>
            <div class="summary-icon text-info"><i class="bi bi-geo-alt"></i></div>
          </div>
        </div>
      </div>
    </div>

    <div class="row g-4 mt-1">
      <div class="col-xl-8">
        <div class="card stretch stretch-full">
          <div class="card-body">
            <div class="d-flex align-items-center justify-content-between mb-3">
              <h5 class="mb-0">Tren Kunjungan Bulanan</h5>
              <span class="text-muted"><?= date('Y') ?></span>
            </div>
            <div id="visitAreaChart"></div>
          </div>
        </div>
      </div>
      <div class="col-xl-4">
        <div class="card stretch stretch-full">
          <div class="card-body">
            <h5 class="mb-3">Objek Wisata Teratas</h5>
            <div id="visitObjectChart"></div>
          </div>
        </div>
      </div>
    </div>

    <div id="visitBuilderSection" class="row g-4 mt-1">
      <div class="col-xl-5">
        <div class="card visit-builder-card stretch stretch-full">
          <div class="card-body">
            <h5 class="mb-3">Keyword Laporan</h5>
            <div class="mb-3">
              <label for="visitKeywordSearch" class="form-label">Cari Keyword</label>
              <input type="text" id="visitKeywordSearch" class="form-control" placeholder="Cari QR, wisatawan, petugas...">
            </div>
            <small class="text-muted d-block mb-2">Rekomendasi Keyword</small>
            <div id="visitKeywordList" class="visit-keyword-list"></div>
          </div>
        </div>
      </div>
      <div class="col-xl-7">
        <div class="card visit-builder-card stretch stretch-full">
          <div class="card-body">
            <div class="d-flex align-items-center justify-content-between gap-3 mb-3">
              <div>
                <h5 class="mb-1">Susunan Laporan</h5>
                <span class="text-muted">Drag keyword kunjungan ke area ini</span>
              </div>
              <button type="button" id="resetVisitBuilder" class="btn btn-light">Reset</button>
            </div>
            <form id="visitBuilderForm">
              <div id="visitDropzone" class="visit-dropzone">
                <div id="visitEmptyState" class="visit-empty-state">Belum ada keyword dipilih</div>
              </div>
              <div class="mt-3 hstack gap-2 justify-content-end">
                <button type="button" id="generateVisitReport" class="btn btn-primary">
                  <i class="bi bi-file-earmark-bar-graph me-2"></i>Generate Laporan
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>

    <div id="visitResultSection" class="card stretch stretch-full mt-4 visit-hidden">
      <div class="card-body">
        <div class="d-flex align-items-center justify-content-between gap-3 mb-3">
          <div>
            <h5 class="mb-1">Hasil Laporan Kunjungan</h5>
            <span class="text-muted">Data hasil scan QR wisatawan</span>
          </div>
          <button type="button" id="backToVisitBuilder" class="btn btn-light">
            <i class="bi bi-arrow-left me-2"></i>Kembali
          </button>
        </div>
        <div class="table-responsive">
          <table class="table table-hover visit-result-table" id="visitReportTable">
            <thead>
              <tr id="visitReportTableHead"></tr>
            </thead>
            <tbody id="visitReportTableBody"></tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
  <!-- [ Main Content ] end -->

</div>

<script>
  window.addEventListener('load', function() {
    const laporanKunjungan = <?= json_encode($laporan_kunjungan) ?>;
    const chartBulanKunjungan = <?= json_encode(array_values($chart_bulan_kunjungan)) ?>;
    const chartTotalKunjungan = <?= json_encode(array_values($chart_total_kunjungan)) ?>;
    const objekKunjunganLabel = <?= json_encode($chart_objek_kunjungan_label) ?>;
    const objekKunjunganData = <?= json_encode($chart_objek_kunjungan_data) ?>;
    let keywordOptions = [{
        key: 'kode_qr',
        label: 'Kode QR'
      },
      {
        key: 'kode_booking',
        label: 'Kode Booking'
      },
      {
        key: 'nama_wisatawan',
        label: 'Nama Wisatawan'
      },
      {
        key: 'email',
        label: 'Email'
      },
      {
        key: 'nama_wisata',
        label: 'Objek Wisata'
      },
      {
        key: 'tgl_kunjungan',
        label: 'Tanggal Kunjungan'
      },
      {
        key: 'nama_petugas',
        label: 'Petugas'
      },
      {
        key: 'waktu_kunjungan',
        label: 'Waktu Scan'
      },
      {
        key: 'status_tiket',
        label: 'Status Tiket'
      },
      {
        key: 'keterangan',
        label: 'Keterangan'
      }
    ];
    let selectedKeywords = [];
    let draggedKeyword = null;
    let visitDataTable = null;

    const slugifyKeyword = value => 'custom_' + value.toLowerCase().replace(/[^a-z0-9]+/g, '_').replace(/^_|_$/g, '');

    new ApexCharts(document.querySelector('#visitAreaChart'), {
      chart: {
        type: 'area',
        height: 320,
        toolbar: {
          show: false
        }
      },
      series: [{
        name: 'Kunjungan',
        data: chartTotalKunjungan
      }],
      xaxis: {
        categories: chartBulanKunjungan
      },
      yaxis: {
        labels: {
          formatter: value => new Intl.NumberFormat('id-ID').format(value) + ' scan'
        }
      },
      stroke: {
        curve: 'smooth',
        width: 3
      },
      colors: ['#25b865'],
      dataLabels: {
        enabled: false
      },
      fill: {
        type: 'gradient',
        gradient: {
          opacityFrom: .35,
          opacityTo: .05
        }
      }
    }).render();

    new ApexCharts(document.querySelector('#visitObjectChart'), {
      chart: {
        type: 'donut',
        height: 320
      },
      labels: objekKunjunganLabel.length ? objekKunjunganLabel : ['Belum ada data'],
      series: objekKunjunganData.length ? objekKunjunganData : [0],
      legend: {
        position: 'bottom'
      },
      colors: ['#25b865', '#3454d1', '#e49e3d', '#17a2b8', '#d13b4c', '#64748b']
    }).render();

    const keywordList = document.getElementById('visitKeywordList');
    const keywordSearch = document.getElementById('visitKeywordSearch');
    const dropzone = document.getElementById('visitDropzone');
    const emptyState = document.getElementById('visitEmptyState');

    function renderKeywords(filter = '') {
      keywordList.innerHTML = '';
      const normalizedFilter = filter.toLowerCase();
      keywordOptions
        .filter(keyword => keyword.label.toLowerCase().includes(normalizedFilter))
        .forEach(keyword => {
          const chip = document.createElement('span');
          chip.className = 'visit-keyword-chip';
          chip.draggable = true;
          chip.dataset.key = keyword.key;
          chip.innerHTML = '<i class="bi bi-grip-vertical"></i>' + keyword.label;
          chip.addEventListener('dragstart', function() {
            draggedKeyword = keyword;
          });
          keywordList.appendChild(chip);
        });
    }

    function addCustomKeyword(value) {
      const label = value.trim();
      if (!label) {
        return;
      }

      const key = slugifyKeyword(label);
      if (!keywordOptions.some(keyword => keyword.key === key || keyword.label.toLowerCase() === label.toLowerCase())) {
        keywordOptions.push({
          key: key,
          label: label,
          custom: true
        });
      }

      keywordSearch.value = '';
      renderKeywords();
    }

    function renderSelectedKeywords() {
      dropzone.querySelectorAll('.visit-keyword-chip').forEach(chip => chip.remove());
      emptyState.classList.toggle('visit-hidden', selectedKeywords.length > 0);
      selectedKeywords.forEach(keyword => {
        const chip = document.createElement('span');
        chip.className = 'visit-keyword-chip';
        chip.dataset.key = keyword.key;
        chip.innerHTML = keyword.label + ' <button type="button" class="btn-close ms-1" aria-label="Hapus"></button>';
        chip.querySelector('button').addEventListener('click', function() {
          selectedKeywords = selectedKeywords.filter(item => item.key !== keyword.key);
          renderSelectedKeywords();
        });
        dropzone.appendChild(chip);
      });
    }

    keywordSearch.addEventListener('input', function() {
      renderKeywords(this.value);
    });

    keywordSearch.addEventListener('keydown', function(event) {
      if (event.key === 'Enter') {
        event.preventDefault();
        addCustomKeyword(this.value);
      }
    });

    dropzone.addEventListener('dragover', function(event) {
      event.preventDefault();
      dropzone.classList.add('is-over');
    });

    dropzone.addEventListener('dragleave', function() {
      dropzone.classList.remove('is-over');
    });

    dropzone.addEventListener('drop', function(event) {
      event.preventDefault();
      dropzone.classList.remove('is-over');
      if (!draggedKeyword) {
        return;
      }
      if (!selectedKeywords.some(keyword => keyword.key === draggedKeyword.key)) {
        selectedKeywords.push(draggedKeyword);
        renderSelectedKeywords();
      }
      draggedKeyword = null;
    });

    document.getElementById('resetVisitBuilder').addEventListener('click', function() {
      selectedKeywords = [];
      renderSelectedKeywords();
    });

    function generateVisitTable() {
      const columns = selectedKeywords.length > 0 ? selectedKeywords : keywordOptions.slice(0, 8);
      const head = document.getElementById('visitReportTableHead');
      const body = document.getElementById('visitReportTableBody');
      if (visitDataTable) {
        visitDataTable.destroy();
        visitDataTable = null;
      }
      head.innerHTML = '';
      body.innerHTML = '';

      columns.forEach(column => {
        const th = document.createElement('th');
        th.textContent = column.label;
        head.appendChild(th);
      });

      laporanKunjungan.forEach(row => {
        const tr = document.createElement('tr');
        columns.forEach(column => {
          const td = document.createElement('td');
          if (row[column.key] || row[column.key] === 0) {
            td.textContent = row[column.key];
          } else if (column.custom) {
            const foundValue = Object.values(row).find(value => String(value).toLowerCase().includes(column.label.toLowerCase()));
            td.textContent = foundValue || '-';
          } else {
            td.textContent = '-';
          }
          tr.appendChild(td);
        });
        body.appendChild(tr);
      });

      if (window.jQuery && window.jQuery.fn.DataTable) {
        visitDataTable = window.jQuery('#visitReportTable').DataTable();
      }

      document.getElementById('visitBuilderSection').classList.add('visit-hidden');
      document.getElementById('visitResultSection').classList.remove('visit-hidden');
    }

    document.getElementById('generateVisitReport').addEventListener('click', generateVisitTable);
    document.getElementById('backToVisitBuilder').addEventListener('click', function() {
      document.getElementById('visitResultSection').classList.add('visit-hidden');
      document.getElementById('visitBuilderSection').classList.remove('visit-hidden');
    });

    renderKeywords();
  });
</script>

<?php require_once("../../templates/views_bottom.php") ?>
