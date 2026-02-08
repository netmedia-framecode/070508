<script src="<?= $baseURL ?>assets/vendor/js/vendors.min.js"></script>
<script src="<?= $baseURL ?>assets/js/common-init.min.js"></script>
<script src="<?= $baseURL ?>assets/js/theme-customizer-init.min.js"></script>
<script src="<?= $baseURL ?>assets/js/jquery-3.5.1.min.js"></script>
<script src="<?= $baseURL ?>assets/vendor/js/lslstrength.min.js"></script>

<script>
const showMessage = (type, title, message) => {
  if (message) {
    Swal.fire({
      icon: type,
      title: title,
      text: message,
    });
  }
};

showMessage("success", "Berhasil Terkirim", $(".message-success").data("message-success"));
showMessage("info", "For your information", $(".message-info").data("message-info"));
showMessage("warning", "Peringatan!!", $(".message-warning").data("message-warning"));
showMessage("error", "Kesalahan", $(".message-danger").data("message-danger"));
</script>