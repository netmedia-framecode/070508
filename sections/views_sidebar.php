<nav class="nxl-navigation">
  <div class="navbar-wrapper">
    <div class="m-header">
      <a href="./" class="b-brand">
        <!-- ========   change your logo hear   ============ -->
        <h4 class="logo logo-lg"><?= $data_utilities['name_web']?></h4>
        <img src="<?= $baseURL?>assets/img/<?= $data_utilities['logo']?>" alt="" class="logo logo-sm" />
      </a>
    </div>
    <div class="navbar-content">
      <ul class="nxl-navbar">
        <li class="nxl-item nxl-caption">
          <label>Navigation</label>
        </li>
        <li class="nxl-item nxl-hasmenu">
          <a href="<?= $baseURL?>views/" class="nxl-link">
            <span class="nxl-micon"><i class="feather-airplay"></i></span>
            <span class="nxl-mtext">Dashboards</span>
          </a>
        </li>
        <?php
        $queryMenu = "SELECT user_menu.id_menu, user_menu.icon, user_menu.menu
                        FROM user_menu JOIN user_access_menu
                        ON user_menu.id_menu = user_access_menu.id_menu
                        WHERE user_access_menu.id_role = '$id_role'
                        ORDER BY user_access_menu.id_menu ASC
                      ";
        $menu = mysqli_query($conn, $queryMenu);
        foreach ($menu as $m) :
        ?>
        <li class="nxl-item nxl-hasmenu">
          <a href="javascript:void(0);" class="nxl-link">
            <span class="nxl-micon"><i class="<?= $m['icon']?>"></i></span>
            <span class="nxl-mtext"><?= $m['menu']?></span><span class="nxl-arrow"><i
                class="feather-chevron-right"></i></span>
          </a>
          <ul class="nxl-submenu">
            <?php
            $menuId = $m['id_menu'];
            $querySubMenu = "SELECT * FROM user_sub_menu 
                              JOIN user_menu ON user_sub_menu.id_menu = user_menu.id_menu
                              JOIN user_access_sub_menu ON user_sub_menu.id_sub_menu=user_access_sub_menu.id_sub_menu
                              WHERE user_sub_menu.id_menu = $menuId
                              AND user_sub_menu.id_active = 1
                              AND user_access_sub_menu.id_role = '$id_role'
                            ";
            $subMenu = mysqli_query($conn, $querySubMenu);
            foreach ($subMenu as $sm) : ?>
            <li class="nxl-item"><a class="nxl-link" href="<?= $baseURL.'views/'.$sm['url']?>"><?= $sm['title']?></a></li>
            <?php endforeach; ?>
          </ul>
        </li>
        <?php endforeach; ?>
      </ul>
    </div>
  </div>
</nav>