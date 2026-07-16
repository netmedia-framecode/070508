<?php require_once("../controller/dashboard.php");
$_SESSION["project_wisata_sumba_barat_daya"]["name_page"] = "Dashboard";

if (!function_exists('dashboardBadgeClass')) {
  function dashboardBadgeClass($status)
  {
    $status = strtolower((string) $status);
    if (in_array($status, ['paid', 'settlement', 'confirmed', 'active', 'used', 'aktif', 'terkonfirmasi'])) {
      return 'bg-soft-success text-success';
    }
    if (in_array($status, ['pending', 'unpaid', 'tertunda', 'diproses'])) {
      return 'bg-soft-warning text-warning';
    }
    if (in_array($status, ['expired', 'cancelled', 'failed', 'dibatalkan'])) {
      return 'bg-soft-danger text-danger';
    }
    return 'bg-soft-primary text-primary';
  }
}

require_once("../templates/views_top.php"); ?>

<style>
  .dashboard-stat-card {
    border: 1px solid #edf0f5;
    box-shadow: 0 10px 24px rgba(15, 23, 42, .05);
  }

  .dashboard-stat-icon {
    width: 44px;
    height: 44px;
    border-radius: 8px;
    display: grid;
    place-items: center;
    flex: 0 0 44px;
  }

  .quick-action-card {
    border: 1px solid #edf0f5;
    border-radius: 8px;
    background: #fff;
    min-height: 132px;
    transition: .2s ease;
  }

  .quick-action-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 12px 28px rgba(15, 23, 42, .08);
  }

  .quick-action-icon {
    width: 42px;
    height: 42px;
    border-radius: 8px;
    display: grid;
    place-items: center;
  }

  .dashboard-table {
    min-width: 960px;
  }

  .top-object-item {
    border: 1px dashed #dfe4ee;
    border-radius: 8px;
  }
</style>

<div class="nxl-content">
  <!-- [ page-header ] start -->
  <div class="page-header">
    <div class="page-header-left d-flex align-items-center">
      <div class="page-header-title">
        <h5 class="m-b-10">Dashboard</h5>
      </div>
      <ul class="breadcrumb">
        <li class="breadcrumb-item">Home</li>
        <li class="breadcrumb-item">Dashboard</li>
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
          <a href="laporan/transaksi" class="btn btn-light-brand">
            <i class="bi bi-file-earmark-bar-graph me-2"></i>
            <span>Laporan Transaksi</span>
          </a>
          <a href="manajemen-kunjungan/scan-qr-wisatawan" class="btn btn-primary">
            <i class="bi bi-qr-code-scan me-2"></i>
            <span>Scan QR</span>
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
        <div class="card dashboard-stat-card stretch stretch-full">
          <div class="card-body">
            <div class="d-flex align-items-center justify-content-between gap-3">
              <div>
                <span class="text-muted d-block">Objek Wisata</span>
                <h4 class="mb-1"><?= number_format($dashboard_total_objek_wisata, 0, ',', '.') ?></h4>
                <small><?= number_format($dashboard_total_galeri, 0, ',', '.') ?> galeri tersedia</small>
              </div>
              <div class="dashboard-stat-icon bg-soft-primary text-primary">
                <i class="bi bi-geo-alt fs-4"></i>
              </div>
            </div>
            <div class="progress mt-4 ht-3">
              <div class="progress-bar bg-primary" style="width: <?= min(100, $dashboard_total_objek_wisata * 10) ?>%"></div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-xxl-3 col-md-6">
        <div class="card dashboard-stat-card stretch stretch-full">
          <div class="card-body">
            <div class="d-flex align-items-center justify-content-between gap-3">
              <div>
                <span class="text-muted d-block">Wisatawan</span>
                <h4 class="mb-1"><?= number_format($dashboard_total_wisatawan, 0, ',', '.') ?></h4>
                <small><?= number_format($dashboard_total_users, 0, ',', '.') ?> total pengguna</small>
              </div>
              <div class="dashboard-stat-icon bg-soft-success text-success">
                <i class="bi bi-people fs-4"></i>
              </div>
            </div>
            <div class="progress mt-4 ht-3">
              <div class="progress-bar bg-success" style="width: <?= $dashboard_total_users > 0 ? min(100, ($dashboard_total_wisatawan / $dashboard_total_users) * 100) : 0 ?>%"></div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-xxl-3 col-md-6">
        <div class="card dashboard-stat-card stretch stretch-full">
          <div class="card-body">
            <div class="d-flex align-items-center justify-content-between gap-3">
              <div>
                <span class="text-muted d-block">Pendapatan</span>
                <h4 class="mb-1">Rp <?= number_format($dashboard_pendapatan, 0, ',', '.') ?></h4>
                <small><?= number_format($dashboard_total_pembayaran, 0, ',', '.') ?> pembayaran</small>
              </div>
              <div class="dashboard-stat-icon bg-soft-warning text-warning">
                <i class="bi bi-cash-coin fs-4"></i>
              </div>
            </div>
            <div id="total-sales-color-graph"></div>
          </div>
        </div>
      </div>
      <div class="col-xxl-3 col-md-6">
        <div class="card dashboard-stat-card stretch stretch-full">
          <div class="card-body">
            <div class="d-flex align-items-center justify-content-between gap-3">
              <div>
                <span class="text-muted d-block">Kunjungan</span>
                <h4 class="mb-1"><?= number_format($dashboard_total_kunjungan, 0, ',', '.') ?></h4>
                <small><?= number_format($dashboard_active_tiket, 0, ',', '.') ?> tiket aktif</small>
              </div>
              <div class="dashboard-stat-icon bg-soft-info text-info">
                <i class="bi bi-qr-code-scan fs-4"></i>
              </div>
            </div>
            <div id="new-tasks-area-chart"></div>
          </div>
        </div>
      </div>
    </div>

    <div class="row g-4 mt-1">
      <div class="col-xl-3 col-md-6">
        <a href="manajemen-kunjungan/scan-qr-wisatawan" class="quick-action-card d-block p-4 text-decoration-none">
          <div class="quick-action-icon bg-soft-primary text-primary mb-3">
            <i class="bi bi-qr-code-scan fs-4"></i>
          </div>
          <h6 class="mb-1">Scan QR Wisatawan</h6>
          <span class="text-muted">Catat kunjungan dari e-tiket</span>
        </a>
      </div>
      <div class="col-xl-3 col-md-6">
        <a href="manajemen-transaksi/add-pemesanan-tiket" class="quick-action-card d-block p-4 text-decoration-none">
          <div class="quick-action-icon bg-soft-success text-success mb-3">
            <i class="bi bi-ticket-perforated fs-4"></i>
          </div>
          <h6 class="mb-1">Tambah Pemesanan</h6>
          <span class="text-muted">Input transaksi tiket baru</span>
        </a>
      </div>
      <div class="col-xl-3 col-md-6">
        <a href="manajemen-transaksi/add-konfirmasi-pembayaran" class="quick-action-card d-block p-4 text-decoration-none">
          <div class="quick-action-icon bg-soft-warning text-warning mb-3">
            <i class="bi bi-credit-card fs-4"></i>
          </div>
          <h6 class="mb-1">Konfirmasi Bayar</h6>
          <span class="text-muted">Proses pembayaran tiket</span>
        </a>
      </div>
      <div class="col-xl-3 col-md-6">
        <a href="master-data/add-objek-wisata" class="quick-action-card d-block p-4 text-decoration-none">
          <div class="quick-action-icon bg-soft-info text-info mb-3">
            <i class="bi bi-map fs-4"></i>
          </div>
          <h6 class="mb-1">Tambah Objek Wisata</h6>
          <span class="text-muted">Lengkapi master destinasi</span>
        </a>
      </div>
    </div>

    <div class="row g-4 mt-1">
      <div class="col-xxl-8">
        <div class="card stretch stretch-full">
          <div class="card-header">
            <h5 class="card-title">Grafik Operasional Bulanan</h5>
            <span class="text-muted"><?= date('Y') ?></span>
          </div>
          <div class="card-body custom-card-action p-0">
            <div id="payment-records-chart"></div>
          </div>
          <div class="card-footer">
            <div class="row g-4">
              <div class="col-lg-3 col-6">
                <div class="p-3 border border-dashed rounded">
                  <div class="fs-12 text-muted mb-1">Pemesanan</div>
                  <h6 class="fw-bold text-dark"><?= number_format($dashboard_total_pemesanan, 0, ',', '.') ?></h6>
                </div>
              </div>
              <div class="col-lg-3 col-6">
                <div class="p-3 border border-dashed rounded">
                  <div class="fs-12 text-muted mb-1">Pending</div>
                  <h6 class="fw-bold text-dark"><?= number_format($dashboard_pending_pemesanan, 0, ',', '.') ?></h6>
                </div>
              </div>
              <div class="col-lg-3 col-6">
                <div class="p-3 border border-dashed rounded">
                  <div class="fs-12 text-muted mb-1">E-Tiket</div>
                  <h6 class="fw-bold text-dark"><?= number_format($dashboard_total_e_tiket, 0, ',', '.') ?></h6>
                </div>
              </div>
              <div class="col-lg-3 col-6">
                <div class="p-3 border border-dashed rounded">
                  <div class="fs-12 text-muted mb-1">Tiket Terpakai</div>
                  <h6 class="fw-bold text-dark"><?= number_format($dashboard_used_tiket, 0, ',', '.') ?></h6>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-xxl-4">
        <div class="card stretch stretch-full">
          <div class="card-header">
            <h5 class="card-title">Komposisi Fitur</h5>
          </div>
          <div class="card-body custom-card-action">
            <div id="leads-overview-donut"></div>
            <div class="row g-2">
              <?php foreach (array_slice($dashboard_feature_summary, 0, 6) as $index => $feature): ?>
                <div class="col-6">
                  <a href="<?= htmlspecialchars($feature['link']) ?>" class="p-2 hstack gap-2 rounded border border-dashed border-gray-5 text-decoration-none">
                    <span class="wd-7 ht-7 rounded-circle d-inline-block" style="background-color: <?= ['#3454d1', '#25b865', '#e49e3d', '#17a2b8', '#d13b4c', '#64748b'][$index] ?>"></span>
                    <span class="text-truncate"><?= htmlspecialchars($feature['fitur']) ?><span class="fs-10 text-muted ms-1">(<?= number_format($feature['jumlah'], 0, ',', '.') ?>)</span></span>
                  </a>
                </div>
              <?php endforeach; ?>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="row g-4 mt-1">
      <div class="col-lg-4">
        <div class="card mb-4 stretch stretch-full">
          <div class="card-header d-flex align-items-center justify-content-between">
            <div class="d-flex gap-3 align-items-center">
              <div class="avatar-text"><i class="bi bi-ticket-perforated"></i></div>
              <div>
                <div class="fw-semibold text-dark">Pemesanan Tiket</div>
                <div class="fs-12 text-muted">Total transaksi pemesanan</div>
              </div>
            </div>
            <div class="fs-4 fw-bold text-dark"><?= number_format($dashboard_total_pemesanan, 0, ',', '.') ?></div>
          </div>
          <div class="card-body d-flex align-items-center justify-content-between gap-4">
            <div id="task-completed-area-chart"></div>
            <a href="manajemen-transaksi/pemesanan-tiket" class="btn btn-light btn-sm">Lihat</a>
          </div>
        </div>
      </div>
      <div class="col-lg-4">
        <div class="card mb-4 stretch stretch-full">
          <div class="card-header d-flex align-items-center justify-content-between">
            <div class="d-flex gap-3 align-items-center">
              <div class="avatar-text"><i class="bi bi-qr-code"></i></div>
              <div>
                <div class="fw-semibold text-dark">E-Tiket</div>
                <div class="fs-12 text-muted">Tiket aktif dan terpakai</div>
              </div>
            </div>
            <div class="fs-4 fw-bold text-dark"><?= number_format($dashboard_total_e_tiket, 0, ',', '.') ?></div>
          </div>
          <div class="card-body d-flex align-items-center justify-content-between gap-4">
            <div id="project-done-area-chart"></div>
            <a href="manajemen-transaksi/e-tiket" class="btn btn-light btn-sm">Lihat</a>
          </div>
        </div>
      </div>
      <div class="col-lg-4">
        <div class="card mb-4 stretch stretch-full">
          <div class="card-header d-flex align-items-center justify-content-between">
            <div class="d-flex gap-3 align-items-center">
              <div class="avatar-text"><i class="bi bi-basket"></i></div>
              <div>
                <div class="fw-semibold text-dark">Keranjang</div>
                <div class="fs-12 text-muted">Pantauan keranjang tertunda</div>
              </div>
            </div>
            <div class="fs-4 fw-bold text-dark"><?= number_format($dashboard_total_keranjang, 0, ',', '.') ?></div>
          </div>
          <div class="card-body d-flex align-items-center justify-content-between gap-4">
            <div class="fs-12 text-muted">Data dari wisatawan yang belum menjadi pemesanan tiket.</div>
            <a href="manajemen-transaksi/data-keranjang" class="btn btn-light btn-sm">Lihat</a>
          </div>
        </div>
      </div>
    </div>

    <div class="row g-4 mt-1">
      <div class="col-xxl-8">
        <div class="card stretch stretch-full">
          <div class="card-header">
            <h5 class="card-title">Ringkasan Fitur Sistem</h5>
          </div>
          <div class="card-body">
            <div class="table-responsive">
              <table class="table table-hover dashboard-table" id="dashboardFeatureTable">
                <thead>
                  <tr>
                    <th>#</th>
                    <th>Fitur</th>
                    <th>Kategori</th>
                    <th>Jumlah Data</th>
                    <th>Status</th>
                    <th>Aksi</th>
                  </tr>
                </thead>
                <tbody>
                  <?php $no = 1; foreach ($dashboard_feature_summary as $feature): ?>
                    <tr>
                      <td><?= $no++ ?></td>
                      <td><?= htmlspecialchars($feature['fitur']) ?></td>
                      <td><?= htmlspecialchars($feature['kategori']) ?></td>
                      <td><?= number_format($feature['jumlah'], 0, ',', '.') ?></td>
                      <td><span class="badge <?= dashboardBadgeClass($feature['status']) ?>"><?= htmlspecialchars($feature['status']) ?></span></td>
                      <td>
                        <a href="<?= htmlspecialchars($feature['link']) ?>" class="btn btn-light btn-sm">
                          <i class="bi bi-box-arrow-up-right me-1"></i>Buka
                        </a>
                      </td>
                    </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
      <div class="col-xxl-4">
        <div class="card stretch stretch-full">
          <div class="card-header">
            <h5 class="card-title">Objek Wisata Terlaris</h5>
          </div>
          <div class="card-body">
            <?php if (count($dashboard_top_objek) > 0): ?>
              <?php foreach ($dashboard_top_objek as $objek): ?>
                <div class="top-object-item p-3 mb-3">
                  <div class="d-flex align-items-start justify-content-between gap-3">
                    <div>
                      <h6 class="mb-1"><?= htmlspecialchars($objek['nama_wisata'] ?: '-') ?></h6>
                      <span class="text-muted fs-12">Harga tiket Rp <?= number_format($objek['harga_tiket'], 0, ',', '.') ?></span>
                    </div>
                    <span class="badge bg-soft-primary text-primary"><?= number_format($objek['jumlah_pemesanan'], 0, ',', '.') ?> pesan</span>
                  </div>
                  <div class="mt-2 fs-12 text-muted">Nilai pemesanan Rp <?= number_format($objek['total_tagihan'], 0, ',', '.') ?></div>
                </div>
              <?php endforeach; ?>
            <?php else: ?>
              <div class="text-center text-muted py-4">Belum ada data objek wisata.</div>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </div>

    <div class="row g-4 mt-1">
      <div class="col-12">
        <div class="card stretch stretch-full">
          <div class="card-header">
            <h5 class="card-title">Aktivitas Terbaru</h5>
          </div>
          <div class="card-body">
            <div class="table-responsive">
              <table class="table table-hover dashboard-table" id="dashboardActivityTable">
                <thead>
                  <tr>
                    <th>Jenis</th>
                    <th>Kode</th>
                    <th>Wisatawan</th>
                    <th>Objek Wisata</th>
                    <th>Nominal</th>
                    <th>Tanggal</th>
                    <th>Status</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($dashboard_recent_activity as $activity): ?>
                    <tr>
                      <td><?= htmlspecialchars($activity['jenis'] ?: '-') ?></td>
                      <td><?= htmlspecialchars($activity['kode'] ?: '-') ?></td>
                      <td><?= htmlspecialchars($activity['nama'] ?: '-') ?></td>
                      <td><?= htmlspecialchars($activity['nama_wisata'] ?: '-') ?></td>
                      <td><?= (float) $activity['nominal'] > 0 ? 'Rp ' . number_format($activity['nominal'], 0, ',', '.') : '-' ?></td>
                      <td><?= $activity['tanggal'] ? date('d/m/Y H:i', strtotime($activity['tanggal'])) : '-' ?></td>
                      <td><span class="badge <?= dashboardBadgeClass($activity['status']) ?>"><?= htmlspecialchars($activity['status'] ?: '-') ?></span></td>
                    </tr>
                  <?php endforeach; ?>
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
  window.addEventListener('load', function() {
    const monthLabels = <?= json_encode(array_values($dashboard_chart_months)) ?>;
    const monthlyOrders = <?= json_encode(array_values($dashboard_chart_pemesanan)) ?>;
    const monthlyPayments = <?= json_encode(array_values($dashboard_chart_pembayaran)) ?>;
    const monthlyVisits = <?= json_encode(array_values($dashboard_chart_kunjungan)) ?>;
    const monthlyRevenue = <?= json_encode(array_values($dashboard_chart_pendapatan)) ?>;
    const featureLabels = <?= json_encode($dashboard_feature_chart_labels) ?>;
    const featureSeries = <?= json_encode($dashboard_feature_chart_data) ?>;
    const totalTickets = <?= (int) $dashboard_total_e_tiket ?>;
    const activeTickets = <?= (int) $dashboard_active_tiket ?>;
    const usedTickets = <?= (int) $dashboard_used_tiket ?>;

    const formatRupiah = value => new Intl.NumberFormat('id-ID', {
      style: 'currency',
      currency: 'IDR',
      minimumFractionDigits: 0
    }).format(Number(value || 0));

    function resetChartTarget(selector) {
      const target = document.querySelector(selector);
      if (target) {
        target.innerHTML = '';
      }
      return target;
    }

    new ApexCharts(resetChartTarget('#payment-records-chart'), {
      chart: {
        height: 380,
        stacked: false,
        toolbar: {
          show: false
        }
      },
      stroke: {
        width: [0, 3, 3],
        curve: 'smooth'
      },
      plotOptions: {
        bar: {
          borderRadius: 4,
          columnWidth: '35%'
        }
      },
      colors: ['#3454d1', '#25b865', '#e49e3d'],
      series: [{
          name: 'Pemesanan',
          type: 'bar',
          data: monthlyOrders
        },
        {
          name: 'Pembayaran',
          type: 'line',
          data: monthlyPayments
        },
        {
          name: 'Kunjungan',
          type: 'line',
          data: monthlyVisits
        }
      ],
      xaxis: {
        categories: monthLabels
      },
      yaxis: {
        labels: {
          formatter: value => new Intl.NumberFormat('id-ID').format(value) + ' data'
        }
      },
      dataLabels: {
        enabled: false
      },
      legend: {
        position: 'top'
      }
    }).render();

    new ApexCharts(resetChartTarget('#leads-overview-donut'), {
      chart: {
        width: 328,
        type: 'donut'
      },
      dataLabels: {
        enabled: false
      },
      labels: featureSeries.some(value => Number(value) > 0) ? featureLabels : ['Belum ada data'],
      series: featureSeries.some(value => Number(value) > 0) ? featureSeries : [1],
      colors: ['#3454d1', '#25b865', '#e49e3d', '#17a2b8', '#d13b4c', '#64748b', '#8b5cf6', '#14b8a6', '#f97316', '#0ea5e9'],
      stroke: {
        width: 0
      },
      legend: {
        show: false
      },
      plotOptions: {
        pie: {
          donut: {
            size: '78%'
          }
        }
      }
    }).render();

    new ApexCharts(resetChartTarget('#total-sales-color-graph'), {
      chart: {
        type: 'area',
        height: 76,
        sparkline: {
          enabled: true
        }
      },
      series: [{
        name: 'Pendapatan',
        data: monthlyRevenue
      }],
      colors: ['#e49e3d'],
      stroke: {
        curve: 'smooth',
        width: 2
      },
      fill: {
        type: 'gradient',
        gradient: {
          opacityFrom: .35,
          opacityTo: .05
        }
      },
      tooltip: {
        y: {
          formatter: value => formatRupiah(value)
        }
      }
    }).render();

    new ApexCharts(resetChartTarget('#new-tasks-area-chart'), {
      chart: {
        type: 'area',
        height: 76,
        sparkline: {
          enabled: true
        }
      },
      series: [{
        name: 'Kunjungan',
        data: monthlyVisits
      }],
      colors: ['#17a2b8'],
      stroke: {
        curve: 'smooth',
        width: 2
      },
      fill: {
        type: 'gradient',
        gradient: {
          opacityFrom: .3,
          opacityTo: .04
        }
      }
    }).render();

    new ApexCharts(resetChartTarget('#task-completed-area-chart'), {
      chart: {
        type: 'area',
        height: 100,
        sparkline: {
          enabled: true
        }
      },
      series: [{
        name: 'Pemesanan',
        data: monthlyOrders
      }],
      colors: ['#3454d1'],
      stroke: {
        curve: 'smooth',
        width: 2
      },
      fill: {
        type: 'gradient',
        gradient: {
          opacityFrom: .25,
          opacityTo: .04
        }
      }
    }).render();

    new ApexCharts(resetChartTarget('#project-done-area-chart'), {
      chart: {
        type: 'bar',
        height: 100,
        sparkline: {
          enabled: true
        }
      },
      series: [{
        name: 'E-Tiket',
        data: [activeTickets, usedTickets, Math.max(totalTickets - activeTickets - usedTickets, 0)]
      }],
      colors: ['#25b865'],
      plotOptions: {
        bar: {
          borderRadius: 3,
          columnWidth: '45%'
        }
      },
      tooltip: {
        x: {
          formatter: function(value, opts) {
            return ['Active', 'Used', 'Lainnya'][opts.dataPointIndex] || 'E-Tiket';
          }
        }
      }
    }).render();

    if (window.jQuery && window.jQuery.fn.DataTable) {
      window.jQuery('#dashboardFeatureTable').DataTable({
        pageLength: 10,
        lengthMenu: [10, 20, 50, 100]
      });
      window.jQuery('#dashboardActivityTable').DataTable({
        pageLength: 10,
        lengthMenu: [10, 20, 50, 100],
        order: []
      });
    }
  });
</script>

<?php require_once("../templates/views_bottom.php") ?>
