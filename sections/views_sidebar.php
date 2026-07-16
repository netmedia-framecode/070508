<style>
  /* Modifikasi Area Sidebar */
  .modern-sidebar {
    background-color: #ffffff;
    /* Warna dasar bersih */
    box-shadow: 4px 0 24px rgba(0, 0, 0, 0.03);
    /* Shadow super lembut di kanan */
    border-right: 1px solid rgba(231, 234, 243, 0.7);
  }

  /* Modifikasi Link Utama */
  .modern-sidebar .nxl-link.modern-link {
    margin: 4px 16px;
    padding: 12px 16px;
    border-radius: 12px;
    /* Membuat sudut membulat */
    transition: all 0.3s ease;
    color: #4b5563;
    /* Warna teks abu-abu elegan */
  }

  /* Efek Hover Menu Utama */
  .modern-sidebar .nxl-item:not(.active)>.nxl-link.modern-link:hover {
    background-color: #f3f4f6;
    color: #2563eb;
    /* Warna primer (biru) saat dihover */
    transform: translateX(4px);
    /* Efek bergeser sedikit ke kanan */
  }

  /* Efek Aktif Menu Utama */
  .modern-sidebar .nxl-item.active>.nxl-link.modern-link,
  .modern-sidebar .nxl-item.nxl-trigger>.nxl-link.modern-link {
    background: linear-gradient(118deg, #2563eb, rgba(37, 99, 235, 0.85));
    color: #ffffff !important;
    box-shadow: 0 4px 12px rgba(37, 99, 235, 0.25);
  }

  /* Icon Styling */
  .modern-sidebar .nxl-micon {
    margin-right: 12px;
    font-size: 1.1rem;
    transition: all 0.3s ease;
  }

  /* Submenu Link */
  .modern-sidebar .nxl-submenu {
    padding-left: 10px;
  }

  .modern-sidebar .nxl-link.modern-sublink {
    margin: 2px 16px 2px 32px;
    padding: 10px 14px;
    border-radius: 8px;
    color: #6b7280;
    font-size: 0.9rem;
    position: relative;
    transition: all 0.2s ease;
  }

  /* Titik Indikator di Submenu */
  .modern-sidebar .sub-dot {
    width: 6px;
    height: 6px;
    background-color: #d1d5db;
    border-radius: 50%;
    display: inline-block;
    margin-right: 8px;
    transition: all 0.3s ease;
  }

  /* Submenu Hover & Active */
  .modern-sidebar .modern-sublink:hover {
    color: #2563eb;
    background-color: rgba(37, 99, 235, 0.05);
  }

  .modern-sidebar .modern-sublink:hover .sub-dot {
    background-color: #2563eb;
    transform: scale(1.5);
  }

  .modern-sidebar .modern-sublink.active-sub {
    color: #2563eb;
    font-weight: 600;
    background-color: rgba(37, 99, 235, 0.08);
  }

  .modern-sidebar .modern-sublink.active-sub .sub-dot {
    background-color: #2563eb;
    box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.2);
  }
</style>

<nav class="nxl-navigation modern-sidebar">
  <div class="navbar-wrapper">
    <div class="m-header p-3 mb-2 d-flex align-items-center justify-content-center">
      <a href="<?= $baseURL ?>views/" class="b-brand text-decoration-none d-flex align-items-center gap-2">
        <img src="<?= $baseURL ?>assets/img/<?= $data_utilities['logo'] ?>" alt="Logo" class="logo rounded-circle shadow-sm" style="width: 40px; height: 40px; object-fit: cover;" />
        <h4 class="logo logo-lg fw-bolder mb-0 text-primary" style="letter-spacing: 0.5px;"><?= $data_utilities['name_web'] ?></h4>
      </a>
    </div>

    <div class="navbar-content">
      <ul class="nxl-navbar">
        <li class="nxl-item nxl-caption mt-2 mb-1 px-4">
          <label class="text-uppercase text-muted fw-bold fs-11" style="letter-spacing: 1px;">Navigation</label>
        </li>

        <li class="nxl-item nxl-hasmenu <?= ($_SESSION["project_wisata_sumba_barat_daya"]["name_page"] == 'Dashboard') ? 'active' : '' ?>">
          <a href="<?= $baseURL ?>views/" class="nxl-link modern-link">
            <span class="nxl-micon"><i class="feather-airplay"></i></span>
            <span class="nxl-mtext fw-medium">Dashboards</span>
          </a>
        </li>

        <?php
        $queryMenu = "SELECT DISTINCT um.id_menu, um.icon, um.menu
                      FROM user_menu um
                      JOIN user_sub_menu usm ON um.id_menu = usm.id_menu
                      JOIN permissions p ON usm.id_sub_menu = p.id_sub_menu
                      WHERE p.id_role = '$id_role' 
                        AND p.view = 1 
                        AND usm.id_active = 1
                      ORDER BY um.id_menu ASC";

        $menu = mysqli_query($conn, $queryMenu);
        foreach ($menu as $m) :

          // Cek apakah ada submenu di dalam menu ini yang sedang aktif
          $menuId = $m['id_menu'];
          $isMenuOpen = '';

          $querySubMenu = "SELECT usm.* FROM user_sub_menu usm
                             JOIN permissions p ON usm.id_sub_menu = p.id_sub_menu
                             WHERE usm.id_menu = '$menuId'
                               AND usm.id_active = 1
                               AND p.id_role = '$id_role'
                               AND p.view = 1
                             ORDER BY usm.id_sub_menu ASC";
          $subMenu = mysqli_query($conn, $querySubMenu);

          // Deteksi menu induk aktif
          foreach ($subMenu as $smCheck) {
            if ($_SESSION["project_wisata_sumba_barat_daya"]["name_page"] == $smCheck['title']) {
              $isMenuOpen = 'active nxl-trigger'; // Class trigger untuk membuka dropdown otomatis
              break;
            }
          }
        ?>
          <li class="nxl-item nxl-hasmenu <?= $isMenuOpen ?>">
            <a href="javascript:void(0);" class="nxl-link modern-link">
              <span class="nxl-micon"><i class="<?= $m['icon'] ?>"></i></span>
              <span class="nxl-mtext fw-medium"><?= $m['menu'] ?></span>
              <span class="nxl-arrow"><i class="feather-chevron-right"></i></span>
            </a>
            <ul class="nxl-submenu">
              <?php foreach ($subMenu as $sm) :
                $isActiveSub = ($_SESSION["project_wisata_sumba_barat_daya"]["name_page"] == $sm['title']) ? 'active-sub' : '';
              ?>
                <li class="nxl-item">
                  <a class="nxl-link modern-sublink <?= $isActiveSub ?>" href="<?= $baseURL . 'views/' . $sm['url'] ?>">
                    <span class="sub-dot"></span> <?= $sm['title'] ?>
                  </a>
                </li>
              <?php endforeach; ?>
            </ul>
          </li>
        <?php endforeach; ?>
      </ul>
    </div>
  </div>
</nav>