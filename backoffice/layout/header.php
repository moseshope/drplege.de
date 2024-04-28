<?php
$id = $_SESSION['staff_id'];

$sql = "select * from user where id='$id' and deleted_at IS NULL";
$result = $connect->query($sql);
$row = $result->fetch_assoc();
$role = $row['role'];

$currentURL = $_SERVER['REQUEST_URI'];
// $fileName = substr(strrchr($currentURL, '/'), 1);
$fileName = basename(parse_url($currentURL, PHP_URL_PATH));

if ($role == 1) {
    // Allow access for role 1
    $allowedUrls = array(
        "index",
        "doctors",
        "employees",
        "patients",
        "time_slot",
        "servicesdata",
        "nurse",
        "profile",
    );
    } elseif ($role == 2) {
    $allowedUrls = array(
        "index",
        "patients",
        "time",
        "services",
        "profile",
    );
    } else {
    $allowedUrls = array(
        "doctors",
        "employees",
        "patients",
    );
    }

// $currentUrl = $_SERVER['REQUEST_URI'];
if ($role == 1 || $role == 2) {

    if (!in_array($fileName, $allowedUrls)) {
        header("Location: index");
        }
    } else {
    if (!in_array($fileName, $allowedUrls)) {
        header("Location: doctors");
        }
    }
?>
<!DOCTYPE html>
<html lang="des">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <!-- Bootstrap -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">

  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.3.0/font/bootstrap-icons.css">

  <!-- FontAwesome -->
  <link rel="stylesheet" type="text/css" href="https://site-assets.fontawesome.com/releases/v6.2.1/css/all.css">
  <!-- Custom Css -->
  <?php if ($role == 1) { ?>
  <link rel="stylesheet" href="asset/css/index.css">
  <title>Dr. Pleger - Admin</title>
  <?php } elseif ($role == 3) { ?>
  <link rel="stylesheet" href="asset/css/index2.css">
  <title>Dr. Pleger - Mitarbeiter</title>
  <?php } else { ?>
  <title>Dr. Pleger - Doktoren</title>
  <link rel="stylesheet" href="asset/css/index2.css">
  <link rel="icon" href="data:;base64,iVBORw0KGgo=">
  <?php } ?>
</head>

<body>

  <!-- Navbar -->
  <nav class="w-100 custom-navbar">
    <div class="d-flex justify-content-around">
      <div class="menu-button align-items-center">
        <i class="bi bi-list" onclick="handleSidebar()"></i>
      </div>
      <div class="nav-logo cursor-pointer" style="margin-left:54px !important;">
        <a href="https://drpleger.de/">
          <img class="w-100 h-100" src="../images/logo.png" alt="logo-image">
        </a>
      </div>

      <!-- <ul>
                <li class="cursor-pointer">Startseite</li>
                <li class="cursor-pointer">Über uns</li>
                <li class="cursor-pointer">Dienstleistungen </li>
                <li class="cursor-pointer">Ernennung</li>
                <li class="cursor-pointer">Kontakt</li>
                <li class="cursor-pointer">Profil</li>
            </ul> -->
      <div class="flex-grow-1"></div>
      <button class="logout-btn cursor-pointer" onclick="window.location = './controller/logout.php'">
        Logout
      </button>
    </div>
  </nav>
</body>