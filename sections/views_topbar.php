<header class="nxl-header">
  <div class="header-wrapper">
    <!--! [Start] Header Left !-->
    <div class="header-left d-flex align-items-center gap-4">
      <!--! [Start] nxl-head-mobile-toggler !-->
      <a href="javascript:void(0);" class="nxl-head-mobile-toggler" id="mobile-collapse">
        <div class="hamburger hamburger--arrowturn">
          <div class="hamburger-box">
            <div class="hamburger-inner"></div>
          </div>
        </div>
      </a>
      <!--! [Start] nxl-head-mobile-toggler !-->
      <!--! [Start] nxl-navigation-toggle !-->
      <div class="nxl-navigation-toggle">
        <a href="javascript:void(0);" id="menu-mini-button">
          <i class="feather-align-left"></i>
        </a>
        <a href="javascript:void(0);" id="menu-expend-button" style="display: none">
          <i class="feather-arrow-right"></i>
        </a>
      </div>
      <!--! [End] nxl-navigation-toggle !-->
    </div>
    <!--! [End] Header Left !-->
    <!--! [Start] Header Right !-->
    <div class="header-right ms-auto">
      <div class="d-flex align-items-center">
        <div class="nxl-h-item d-none d-sm-flex">
          <div class="full-screen-switcher">
            <a href="javascript:void(0);" class="nxl-head-link me-0" onclick="$('body').fullScreenHelper('toggle');">
              <i class="feather-maximize maximize"></i>
              <i class="feather-minimize minimize"></i>
            </a>
          </div>
        </div>
        <div class="nxl-h-item dark-light-theme">
          <a href="javascript:void(0);" class="nxl-head-link  me-3 dark-button">
            <i class="feather-moon"></i>
          </a>
          <a href="javascript:void(0);" class="nxl-head-link me-3 light-button" style="display: none">
            <i class="feather-sun"></i>
          </a>
        </div>
        <div class="dropdown nxl-h-item">
          <a href="javascript:void(0);" data-bs-toggle="dropdown" role="button" data-bs-auto-close="outside">
            <img src="<?= $baseURL?>assets/img/profil/<?= $image ?>" alt="user-image"
              class="img-fluid user-avtar me-0" />
          </a>
          <div class="dropdown-menu dropdown-menu-end nxl-h-dropdown nxl-user-dropdown">
            <div class="dropdown-header">
              <div class="d-flex align-items-center">
                <img src="<?= $baseURL?>assets/img/profil/<?= $image ?>" alt="user-image"
                  class="img-fluid user-avtar" />
                <div>
                  <h6 class="text-dark mb-0"><?= $name ?><span
                      class="badge bg-soft-success text-success ms-1"><?= $role ?></span></h6>
                  <span class="fs-12 fw-medium text-muted"><?= $email?></span>
                </div>
              </div>
            </div>
            <a href="<?= $baseURL?>views/profil" class="dropdown-item">
              <i class="feather-user"></i>
              <span>Detail Profil</span>
            </a>
            <?php if ($id_role <= 2) { ?>
            <a href="<?= $baseURL?>views/setting" class="dropdown-item">
              <i class="bi bi-gear"></i>
              <span>Setting</span>
            </a>
            <a href="<?= $baseURL?>views/utilities" class="dropdown-item">
              <i class="bi bi-bar-chart-steps"></i>
              <span>Utilitas</span>
            </a>
            <?php } if ($id_role == 1) { ?>
            <div class="dropdown">
              <a href="javascript:void(0);" class="dropdown-item" data-bs-toggle="dropdown">
                <span class="hstack">
                  <i class="bi bi-list me-2"></i>
                  <span>Menu Management</span>
                </span>
                <i class="feather-chevron-right ms-auto me-0"></i>
              </a>
              <div class="dropdown-menu">
                <a href="<?= $baseURL?>views/menu" class="dropdown-item">
                  <span class="hstack">
                    <i class="wd-5 ht-5 bg-gray-500 rounded-circle me-3"></i>
                    <span>Menu</span>
                  </span>
                </a>
                <a href="<?= $baseURL?>views/sub-menu" class="dropdown-item">
                  <span class="hstack">
                    <i class="wd-5 ht-5 bg-gray-500 rounded-circle me-3"></i>
                    <span>Sub Menu</span>
                  </span>
                </a>
                <a href="<?= $baseURL?>views/menu-access" class="dropdown-item">
                  <span class="hstack">
                    <i class="wd-5 ht-5 bg-gray-500 rounded-circle me-3"></i>
                    <span>Menu Access</span>
                  </span>
                </a>
                <a href="<?= $baseURL?>views/sub-menu-access" class="dropdown-item">
                  <span class="hstack">
                    <i class="wd-5 ht-5 bg-gray-500 rounded-circle me-3"></i>
                    <span>Sub Menu Access</span>
                  </span>
                </a>
              </div>
            </div>
            <?php }?>
            <div class="dropdown-divider"></div>
            <a href="<?= $baseURL?>auth/logout" class="dropdown-item">
              <i class="feather-log-out"></i>
              <span>Logout</span>
            </a>
          </div>
        </div>
      </div>
    </div>
    <!--! [End] Header Right !-->
  </div>
</header>