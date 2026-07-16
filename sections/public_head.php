<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="keywords" content="<?= htmlspecialchars($data_utilities['keyword'] ?? 'wisata sumba barat daya') ?>">
<meta name="description" content="<?= htmlspecialchars($data_utilities['description'] ?? 'Temukan dan pesan tiket wisata Sumba Barat Daya') ?>">
<meta name="author" content="<?= htmlspecialchars($data_utilities['author'] ?? 'Wisata Sumba Barat Daya') ?>">
<link rel="icon" href="<?= $baseURL ?>assets/img/<?= htmlspecialchars($data_utilities['logo'] ?? '199380593.png') ?>" type="image/png">
<title><?= htmlspecialchars($data_utilities['name_web'] ?? 'Wisata Sumba Barat Daya') ?></title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<script src="https://cdn.tailwindcss.com"></script>
<script>
  tailwind.config = {
    theme: {
      extend: {
        fontFamily: {
          sans: ['Inter', 'ui-sans-serif', 'system-ui']
        },
        colors: {
          travel: {
            blue: '#0194f3',
            navy: '#102a43',
            sky: '#eaf7ff',
            orange: '#ff6d00'
          }
        },
        boxShadow: {
          soft: '0 18px 45px rgba(15, 23, 42, 0.12)'
        }
      }
    }
  }
</script>
<style>
  .bg-travel-blue {
    background-color: #0194f3;
  }

  .bg-travel-orange {
    background-color: #ff6d00;
  }

  .bg-travel-sky {
    background-color: #eaf7ff;
  }

  .text-travel-blue {
    color: #0194f3;
  }

  .text-travel-orange {
    color: #ff6d00;
  }

  .shadow-soft {
    box-shadow: 0 18px 45px rgba(15, 23, 42, 0.12);
  }

  .line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
  }

  .line-clamp-4 {
    display: -webkit-box;
    -webkit-line-clamp: 4;
    -webkit-box-orient: vertical;
    overflow: hidden;
  }

  .profile-dropdown summary::-webkit-details-marker {
    display: none;
  }

  .profile-dropdown[open] .profile-caret {
    transform: rotate(180deg);
  }

  .destination-dropdown summary::-webkit-details-marker {
    display: none;
  }

  .destination-dropdown[open] .destination-caret {
    transform: rotate(180deg);
  }
</style>
