<?php require_once("../../controller/laporan.php");
$_SESSION["project_wisata_sumba_barat_daya"]["name_page"] = "Laporan Transaksi";
require_once("../../templates/views_top.php"); ?>

<style>
  .report-stat {
    border: 1px solid #edf0f5;
    box-shadow: 0 10px 24px rgba(15, 23, 42, .05);
  }

  .report-stat .icon-box {
    width: 42px;
    height: 42px;
    border-radius: 8px;
    display: grid;
    place-items: center;
    color: #fff;
    flex: 0 0 42px;
  }

  .report-builder-card {
    border: 1px solid #edf0f5;
    box-shadow: 0 10px 24px rgba(15, 23, 42, .05);
  }

  .keyword-list,
  .report-dropzone {
    min-height: 230px;
    border: 1px dashed #cfd6e4;
    border-radius: 8px;
    padding: 14px;
    background: #fbfcfe;
  }

  .keyword-chip {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    border: 1px solid #dfe4ee;
    border-radius: 8px;
    padding: 8px 10px;
    margin: 0 8px 8px 0;
    background: #fff;
    color: #2d3748;
    cursor: grab;
    user-select: none;
  }

  .keyword-chip:active {
    cursor: grabbing;
  }

  .report-dropzone.is-over {
    border-color: #3454d1;
    background: rgba(52, 84, 209, .06);
  }

  .report-empty-state {
    color: #8a94a6;
    min-height: 190px;
    display: grid;
    place-items: center;
    text-align: center;
  }

  .report-hidden {
    display: none !important;
  }

  .report-result-table {
    min-width: 1100px;
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
          <a href="transaksi?export_transaksi_excel=1" class="btn btn-success">
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
        <div class="card report-stat stretch stretch-full">
          <div class="card-body d-flex align-items-center gap-3">
            <div class="icon-box bg-primary"><i class="bi bi-ticket-perforated"></i></div>
            <div>
              <span class="text-muted d-block">Total Pemesanan</span>
              <h5 class="mb-0">Rp <?= number_format($total_pemesanan, 0, ',', '.') ?></h5>
              <small><?= number_format($jumlah_pemesanan, 0, ',', '.') ?> pemesanan</small>
            </div>
          </div>
        </div>
      </div>
      <div class="col-xxl-3 col-md-6">
        <div class="card report-stat stretch stretch-full">
          <div class="card-body d-flex align-items-center gap-3">
            <div class="icon-box bg-success"><i class="bi bi-credit-card"></i></div>
            <div>
              <span class="text-muted d-block">Total Pembayaran</span>
              <h5 class="mb-0">Rp <?= number_format($total_pembayaran, 0, ',', '.') ?></h5>
              <small><?= number_format($jumlah_pembayaran, 0, ',', '.') ?> pembayaran</small>
            </div>
          </div>
        </div>
      </div>
      <div class="col-xxl-3 col-md-6">
        <div class="card report-stat stretch stretch-full">
          <div class="card-body d-flex align-items-center gap-3">
            <div class="icon-box bg-warning"><i class="bi bi-qr-code"></i></div>
            <div>
              <span class="text-muted d-block">Total E-Tiket</span>
              <h5 class="mb-0"><?= number_format($jumlah_e_tiket, 0, ',', '.') ?></h5>
              <small>tiket elektronik</small>
            </div>
          </div>
        </div>
      </div>
      <div class="col-xxl-3 col-md-6">
        <div class="card report-stat stretch stretch-full">
          <div class="card-body d-flex align-items-center gap-3">
            <div class="icon-box bg-info"><i class="bi bi-people"></i></div>
            <div>
              <span class="text-muted d-block">Total Kunjungan</span>
              <h5 class="mb-0"><?= number_format($jumlah_kunjungan, 0, ',', '.') ?></h5>
              <small><?= number_format($jumlah_transaksi_wisata, 0, ',', '.') ?> data transaksi</small>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="row g-4 mt-1">
      <div class="col-xl-8">
        <div class="card stretch stretch-full">
          <div class="card-body">
            <div class="d-flex align-items-center justify-content-between mb-3">
              <h5 class="mb-0">Grafik Transaksi Bulanan</h5>
              <span class="text-muted"><?= date('Y') ?></span>
            </div>
            <div id="transactionBarChart"></div>
          </div>
        </div>
      </div>
      <div class="col-xl-4">
        <div class="card stretch stretch-full">
          <div class="card-body">
            <h5 class="mb-3">Komposisi Transaksi</h5>
            <div id="transactionPieChart"></div>
          </div>
        </div>
      </div>
    </div>

    <div id="reportBuilderSection" class="row g-4 mt-1">
      <div class="col-xl-5">
        <div class="card report-builder-card stretch stretch-full">
          <div class="card-body">
            <h5 class="mb-3">Keyword Laporan</h5>
            <div class="mb-3">
              <label for="keywordSearch" class="form-label">Cari Keyword</label>
              <input type="text" id="keywordSearch" class="form-control" placeholder="Cari wisatawan, booking, status...">
            </div>
            <small class="text-muted d-block mb-2">Rekomendasi Keyword</small>
            <div id="keywordList" class="keyword-list"></div>
          </div>
        </div>
      </div>
      <div class="col-xl-7">
        <div class="card report-builder-card stretch stretch-full">
          <div class="card-body">
            <div class="d-flex align-items-center justify-content-between gap-3 mb-3">
              <div>
                <h5 class="mb-1">Susunan Laporan</h5>
                <span class="text-muted">Drag keyword ke area ini</span>
              </div>
              <button type="button" id="resetReportBuilder" class="btn btn-light">Reset</button>
            </div>
            <form id="reportBuilderForm">
              <div id="reportDropzone" class="report-dropzone">
                <div id="reportEmptyState" class="report-empty-state">Belum ada keyword dipilih</div>
              </div>
              <div class="mt-3 hstack gap-2 justify-content-end">
                <button type="button" id="generateReport" class="btn btn-primary">
                  <i class="bi bi-file-earmark-bar-graph me-2"></i>Generate Laporan
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>

    <div id="reportResultSection" class="card stretch stretch-full mt-4 report-hidden">
      <div class="card-body">
        <div class="d-flex align-items-center justify-content-between gap-3 mb-3">
          <div>
            <h5 class="mb-1">Hasil Laporan Transaksi</h5>
            <span class="text-muted">Data gabungan pemesanan, pembayaran, e-tiket, dan kunjungan</span>
          </div>
          <button type="button" id="backToReportBuilder" class="btn btn-light">
            <i class="bi bi-arrow-left me-2"></i>Kembali
          </button>
        </div>
        <div class="table-responsive">
          <table class="table table-hover report-result-table" id="reportTable">
            <thead>
              <tr id="reportTableHead"></tr>
            </thead>
            <tbody id="reportTableBody"></tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
  <!-- [ Main Content ] end -->

</div>

<script>
  window.addEventListener('load', function() {
    const laporanData = <?= json_encode($laporan_transaksi) ?>;
    const chartBulan = <?= json_encode(array_values($chart_bulan)) ?>;
    const chartPemesanan = <?= json_encode(array_values($chart_pemesanan)) ?>;
    const chartPembayaran = <?= json_encode(array_values($chart_pembayaran)) ?>;
    const chartKunjungan = <?= json_encode(array_values($chart_kunjungan)) ?>;
    const pieSeries = [<?= $jumlah_pemesanan ?>, <?= $jumlah_pembayaran ?>, <?= $jumlah_e_tiket ?>, <?= $jumlah_kunjungan ?>];
    let keywordOptions = [{
        key: 'sumber',
        label: 'Sumber Transaksi'
      },
      {
        key: 'jenis_transaksi',
        label: 'Jenis Transaksi'
      },
      {
        key: 'kode_booking',
        label: 'Kode Booking'
      },
      {
        key: 'order_id',
        label: 'Order ID'
      },
      {
        key: 'kode_qr',
        label: 'Kode QR'
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
        key: 'nominal',
        label: 'Nominal'
      },
      {
        key: 'jumlah_tiket',
        label: 'Jumlah Tiket'
      },
      {
        key: 'tanggal',
        label: 'Tanggal'
      },
      {
        key: 'status',
        label: 'Status'
      },
      {
        key: 'keterangan',
        label: 'Keterangan'
      }
    ];
    let selectedKeywords = [];
    let draggedKeyword = null;
    let reportDataTable = null;

    const formatRupiah = value => new Intl.NumberFormat('id-ID', {
      style: 'currency',
      currency: 'IDR',
      minimumFractionDigits: 0
    }).format(Number(value || 0));

    const slugifyKeyword = value => 'custom_' + value.toLowerCase().replace(/[^a-z0-9]+/g, '_').replace(/^_|_$/g, '');

    new ApexCharts(document.querySelector('#transactionBarChart'), {
      chart: {
        type: 'bar',
        height: 320,
        toolbar: {
          show: false
        }
      },
      series: [{
          name: 'Pemesanan',
          data: chartPemesanan
        },
        {
          name: 'Pembayaran',
          data: chartPembayaran
        },
        {
          name: 'Kunjungan',
          data: chartKunjungan
        }
      ],
      xaxis: {
        categories: chartBulan
      },
      yaxis: {
        labels: {
          formatter: value => new Intl.NumberFormat('id-ID').format(value) + ' data'
        }
      },
      dataLabels: {
        enabled: false
      },
      plotOptions: {
        bar: {
          borderRadius: 4,
          columnWidth: '45%'
        }
      }
    }).render();

    new ApexCharts(document.querySelector('#transactionPieChart'), {
      chart: {
        type: 'pie',
        height: 320
      },
      labels: ['Pemesanan', 'Pembayaran', 'E-Tiket', 'Kunjungan'],
      series: pieSeries,
      legend: {
        position: 'bottom'
      }
    }).render();

    const keywordList = document.getElementById('keywordList');
    const keywordSearch = document.getElementById('keywordSearch');
    const dropzone = document.getElementById('reportDropzone');
    const emptyState = document.getElementById('reportEmptyState');

    function renderKeywords(filter = '') {
      keywordList.innerHTML = '';
      const normalizedFilter = filter.toLowerCase();
      keywordOptions
        .filter(keyword => keyword.label.toLowerCase().includes(normalizedFilter))
        .forEach(keyword => {
          const chip = document.createElement('span');
          chip.className = 'keyword-chip';
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
      dropzone.querySelectorAll('.keyword-chip').forEach(chip => chip.remove());
      emptyState.classList.toggle('report-hidden', selectedKeywords.length > 0);
      selectedKeywords.forEach(keyword => {
        const chip = document.createElement('span');
        chip.className = 'keyword-chip';
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

    document.getElementById('resetReportBuilder').addEventListener('click', function() {
      selectedKeywords = [];
      renderSelectedKeywords();
    });

    function generateReportTable() {
      const columns = selectedKeywords.length > 0 ? selectedKeywords : keywordOptions.slice(0, 8);
      const head = document.getElementById('reportTableHead');
      const body = document.getElementById('reportTableBody');
      if (reportDataTable) {
        reportDataTable.destroy();
        reportDataTable = null;
      }
      head.innerHTML = '';
      body.innerHTML = '';

      columns.forEach(column => {
        const th = document.createElement('th');
        th.textContent = column.label;
        head.appendChild(th);
      });

      laporanData.forEach(row => {
        const tr = document.createElement('tr');
        columns.forEach(column => {
          const td = document.createElement('td');
          if (column.key === 'nominal') {
            td.textContent = formatRupiah(row[column.key]);
          } else if (row[column.key] || row[column.key] === 0) {
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
        reportDataTable = window.jQuery('#reportTable').DataTable();
      }

      document.getElementById('reportBuilderSection').classList.add('report-hidden');
      document.getElementById('reportResultSection').classList.remove('report-hidden');
    }

    document.getElementById('generateReport').addEventListener('click', generateReportTable);
    document.getElementById('backToReportBuilder').addEventListener('click', function() {
      document.getElementById('reportResultSection').classList.add('report-hidden');
      document.getElementById('reportBuilderSection').classList.remove('report-hidden');
    });

    renderKeywords();
    renderSelectedKeywords();
  });
</script>

<?php require_once("../../templates/views_bottom.php") ?>
