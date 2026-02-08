<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
<meta name="keywords" content="<?= $data_utilities['keyword']?>">
<meta name="description" content="<?= $data_utilities['description']?>">
<meta name="author" content="<?= $data_utilities['author']?>">
<link rel="icon" href="<?= $baseURL?>assets/img/<?= $data_utilities['logo']?>" type="image/png">
<title><?= $data_utilities['name_web']?> <?php if (isset($_SESSION['project_wisata_sumba_barat_daya']['name_page'])) {
                          echo " - " . $_SESSION['project_wisata_sumba_barat_daya']['name_page'];
                        } ?>
</title>
<link href="<?= $baseURL ?>assets/vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
<link
  href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
  rel="stylesheet">
<link rel="stylesheet" type="text/css" href="<?= $baseURL ?>assets/css/bootstrap.min.css">
<link rel="stylesheet" type="text/css" href="<?= $baseURL ?>assets/vendor/css/vendors.min.css">
<link rel="stylesheet" type="text/css" href="<?= $baseURL ?>assets/css/theme.min.css">
<script src="<?= $baseURL ?>assets/sweetalert/dist/sweetalert2.all.min.js"></script>
