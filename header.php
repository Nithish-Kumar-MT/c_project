<!-- This is main configuration File -->
<?php
ob_start();
session_start();
include("payment\admin\inc\config.php");
include("payment\admin\inc\CSRF_Protect.php");
include("payment/admin/inc/functions.php");
$csrf = new CSRF_Protect();
$error_message = '';
$success_message = '';
$error_message1 = '';
$success_message1 = '';

// Getting all language variables into array as global variable
$i = 1;
$statement = $pdo->prepare("SELECT * FROM tbl_language");
$statement->execute();
$result = $statement->fetchAll(PDO::FETCH_ASSOC);
foreach ($result as $row) {
  define('LANG_VALUE_' . $i, $row['lang_value']);
  $i++;
}

$statement = $pdo->prepare("SELECT * FROM tbl_settings WHERE id=1");
$statement->execute();
$result = $statement->fetchAll(PDO::FETCH_ASSOC);
foreach ($result as $row) {
  $logo = $row['logo'];
  $favicon = $row['favicon'];
  $contact_email = $row['contact_email'];
  $contact_phone = $row['contact_phone'];
  $meta_title_home = $row['meta_title_home'];
  $meta_keyword_home = $row['meta_keyword_home'];
  $meta_description_home = $row['meta_description_home'];
  $before_head = $row['before_head'];
  $after_body = $row['after_body'];
}

// Checking the order table and removing the pending transaction that are 24 hours+ old. Very important
$current_date_time = date('Y-m-d H:i:s');
$statement = $pdo->prepare("SELECT * FROM tbl_payment WHERE payment_status=?");
$statement->execute(array('Pending'));
$result = $statement->fetchAll(PDO::FETCH_ASSOC);
foreach ($result as $row) {
  $ts1 = strtotime($row['payment_date']);
  $ts2 = strtotime($current_date_time);
  $diff = $ts2 - $ts1;
  $time = $diff / (3600);
  if ($time > 24) {

    // Return back the stock amount
    $statement1 = $pdo->prepare("SELECT * FROM tbl_order WHERE payment_id=?");
    $statement1->execute(array($row['payment_id']));
    $result1 = $statement1->fetchAll(PDO::FETCH_ASSOC);
    foreach ($result1 as $row1) {
      $statement2 = $pdo->prepare("SELECT * FROM tbl_product WHERE p_id=?");
      $statement2->execute(array($row1['product_id']));
      $result2 = $statement2->fetchAll(PDO::FETCH_ASSOC);
      foreach ($result2 as $row2) {
        $p_qty = $row2['p_qty'];
      }
      $final = $p_qty + $row1['quantity'];

      $statement = $pdo->prepare("UPDATE tbl_product SET p_qty=? WHERE p_id=?");
      $statement->execute(array($final, $row1['product_id']));
    }

    // Deleting data from table
    $statement1 = $pdo->prepare("DELETE FROM tbl_order WHERE payment_id=?");
    $statement1->execute(array($row['payment_id']));

    $statement1 = $pdo->prepare("DELETE FROM tbl_payment WHERE id=?");
    $statement1->execute(array($row['id']));
  }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>

  <!-- Meta Tags -->
  <meta name="viewport" content="width=device-width,initial-scale=1.0" />
  <meta http-equiv="content-type" content="text/html; charset=UTF-8" />

  <!-- Favicon -->
  <link rel="icon" type="image/png" href="assets/uploads/<?php echo $favicon; ?>">

  <!-- Stylesheets -->


  <link rel="icon" href="./icons/dead stock.png">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css">
  <link rel="stylesheet" href="index.css">

  <link rel="stylesheet" href="index.css">
  <?php

  $statement = $pdo->prepare("SELECT * FROM tbl_page WHERE id=1");
  $statement->execute();
  $result = $statement->fetchAll(PDO::FETCH_ASSOC);
  foreach ($result as $row) {
    $about_meta_title = $row['about_meta_title'];
    $about_meta_keyword = $row['about_meta_keyword'];
    $about_meta_description = $row['about_meta_description'];
    $faq_meta_title = $row['faq_meta_title'];
    $faq_meta_keyword = $row['faq_meta_keyword'];
    $faq_meta_description = $row['faq_meta_description'];
    $blog_meta_title = $row['blog_meta_title'];
    $blog_meta_keyword = $row['blog_meta_keyword'];
    $blog_meta_description = $row['blog_meta_description'];
    $contact_meta_title = $row['contact_meta_title'];
    $contact_meta_keyword = $row['contact_meta_keyword'];
    $contact_meta_description = $row['contact_meta_description'];
    $pgallery_meta_title = $row['pgallery_meta_title'];
    $pgallery_meta_keyword = $row['pgallery_meta_keyword'];
    $pgallery_meta_description = $row['pgallery_meta_description'];
    $vgallery_meta_title = $row['vgallery_meta_title'];
    $vgallery_meta_keyword = $row['vgallery_meta_keyword'];
    $vgallery_meta_description = $row['vgallery_meta_description'];
  }

  $cur_page = substr($_SERVER["SCRIPT_NAME"], strrpos($_SERVER["SCRIPT_NAME"], "/") + 1);

  if ($cur_page == 'index.php' || $cur_page == 'login.php' || $cur_page == 'registration.php' || $cur_page == 'cart.php' || $cur_page == 'checkout.php' || $cur_page == 'forget-password.php' || $cur_page == 'reset-password.php' || $cur_page == 'product-category.php' || $cur_page == 'product.php') {
    ?>
    <title><?php echo $meta_title_home; ?></title>
    <meta name="keywords" content="<?php echo $meta_keyword_home; ?>">
    <meta name="description" content="<?php echo $meta_description_home; ?>">
    <?php
  }

  if ($cur_page == 'about.php') {
    ?>
    <title><?php echo $about_meta_title; ?></title>
    <meta name="keywords" content="<?php echo $about_meta_keyword; ?>">
    <meta name="description" content="<?php echo $about_meta_description; ?>">
    <?php
  }
  if ($cur_page == 'faq.php') {
    ?>
    <title><?php echo $faq_meta_title; ?></title>
    <meta name="keywords" content="<?php echo $faq_meta_keyword; ?>">
    <meta name="description" content="<?php echo $faq_meta_description; ?>">
    <?php
  }
  if ($cur_page == 'contact.php') {
    ?>
    <title><?php echo $contact_meta_title; ?></title>
    <meta name="keywords" content="<?php echo $contact_meta_keyword; ?>">
    <meta name="description" content="<?php echo $contact_meta_description; ?>">
    <?php
  }
  if ($cur_page == 'product.php') {
    $statement = $pdo->prepare("SELECT * FROM tbl_product WHERE p_id=?");
    $statement->execute(array($_REQUEST['id']));
    $result = $statement->fetchAll(PDO::FETCH_ASSOC);
    foreach ($result as $row) {
      $og_photo = $row['p_featured_photo'];
      $og_title = $row['p_name'];
      $og_slug = 'product.php?id=' . $_REQUEST['id'];
      $og_description = substr(strip_tags($row['p_description']), 0, 200) . '...';
    }
  }

  if ($cur_page == 'dashboard.php') {
    ?>
    <title>Dashboard - <?php echo $meta_title_home; ?></title>
    <meta name="keywords" content="<?php echo $meta_keyword_home; ?>">
    <meta name="description" content="<?php echo $meta_description_home; ?>">
    <?php
  }
  if ($cur_page == 'customer-profile-update.php') {
    ?>
    <title>Update Profile - <?php echo $meta_title_home; ?></title>
    <meta name="keywords" content="<?php echo $meta_keyword_home; ?>">
    <meta name="description" content="<?php echo $meta_description_home; ?>">
    <?php
  }
  if ($cur_page == 'customer-billing-shipping-update.php') {
    ?>
    <title>Update Billing and Shipping Info - <?php echo $meta_title_home; ?></title>
    <meta name="keywords" content="<?php echo $meta_keyword_home; ?>">
    <meta name="description" content="<?php echo $meta_description_home; ?>">
    <?php
  }
  if ($cur_page == 'customer-password-update.php') {
    ?>
    <title>Update Password - <?php echo $meta_title_home; ?></title>
    <meta name="keywords" content="<?php echo $meta_keyword_home; ?>">
    <meta name="description" content="<?php echo $meta_description_home; ?>">
    <?php
  }
  if ($cur_page == 'customer-order.php') {
    ?>
    <title>Orders - <?php echo $meta_title_home; ?></title>
    <meta name="keywords" content="<?php echo $meta_keyword_home; ?>">
    <meta name="description" content="<?php echo $meta_description_home; ?>">
    <?php
  }
  ?>

  <?php if ($cur_page == 'blog-single.php'): ?>
    <meta property="og:title" content="<?php echo $og_title; ?>">
    <meta property="og:type" content="website">
    <meta property="og:url" content="<?php echo BASE_URL . $og_slug; ?>">
    <meta property="og:description" content="<?php echo $og_description; ?>">
    <meta property="og:image" content="assets/uploads/<?php echo $og_photo; ?>">
  <?php endif; ?>

  <?php if ($cur_page == 'product.php'): ?>
    <meta property="og:title" content="<?php echo $og_title; ?>">
    <meta property="og:type" content="website">
    <meta property="og:url" content="<?php echo BASE_URL . $og_slug; ?>">
    <meta property="og:description" content="<?php echo $og_description; ?>">
    <meta property="og:image" content="assets/uploads/<?php echo $og_photo; ?>">
  <?php endif; ?>

  <script src="https://cdnjs.cloudflare.com/ajax/libs/modernizr/2.8.3/modernizr.min.js"></script>

  <!-- <script type="text/javascript" src="//platform-api.sharethis.com/js/sharethis.js#property=5993ef01e2587a001253a261&product=inline-share-buttons"></script> -->

  <?php echo $before_head; ?>

</head>

<body>

  <?php echo $after_body; ?>
  <!--
<div id="preloader">
  <div id="status"></div>
</div>-->


  <!DOCTYPE html>
  <html lang="en">

  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dead Stock</title>
    <link rel="icon" href="./icons/dead stock.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
      integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css">
    <link rel="stylesheet" href="index.css">
  </head>

  <body>

    <!-- header -->
    <div class="header">
      <nav id="nav-bar-head" class="navbar navbar-expand-lg bg-body-tertiary fixed-top">
        <div class="container-fluid">
          <a class="navbar-brand" href="#">
            <img src="assets/uploads/<?php echo $logo; ?>" alt="Logo" width="30" height="30">
            Dead Stock
          </a>
          <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
            data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false"
            aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
          </button>
          <div class="collapse navbar-collapse page-head" id="navbarSupportedContent">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
              <li class="nav-item search-form" id="navbarSupportedContent">
                <form class="d-flex" role="search">
                  <div class="search-container">
                    <ion-icon class="search-outline" name="search-outline" type="submit" size="small"
                      style="padding: 5px;"></ion-icon>
                    <input id="form-control-me-2" class="form-control me-2" type="search" placeholder="Search"
                      aria-label="Search">
                  </div>
                </form>



              </li>
              <?php
              if (isset($_SESSION['customer'])) {
                ?>
                <li><i class="fa fa-user"></i> <?php echo LANG_VALUE_13; ?>
                  <?php echo $_SESSION['customer']['cust_name']; ?>
                </li>
                <li><a href="dashboard.php"><i class="fa fa-home"></i> <?php echo LANG_VALUE_89; ?></a></li>
                <?php
              } else {
                ?>

                <li class="nav-item btns">
                  <button id="seller-btn" type="button" class="seller-btn btn btn-outline-secondary">
                    Sell here!
                  </button>
                  <button type="button" id="login-btn" class="login-btn btn btn-outline-secondary" data-bs-toggle="modal"
                    data-bs-target="#staticBackdrop">
                    Login
                  </button>
                </li>
                <?php
              }
              ?>
            </ul>
          </div>
        </div>
      </nav>

      <!-- runningtxt -->
      <div class="runningtxt">
        <marquee id="marquee" onmouseover="this.stop();" onmouseout="this.start();">
        </marquee>
      </div>

      <!-- menu bar -->
      <nav class="navbar  menu-bar">
        <?php
        $statement = $pdo->prepare("SELECT * FROM tbl_top_category WHERE show_on_menu=1");
        $statement->execute();
        $result = $statement->fetchAll(PDO::FETCH_ASSOC);
        foreach ($result as $row) {
          ?>
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" data-bs-toggle="dropdown"
              href="product-category.php?id=<?php echo $row['tcat_id']; ?>&type=top-category"><?php echo $row['tcat_name']; ?></a>
            <ul class="dropdown-menu">
              <?php
              $statement1 = $pdo->prepare("SELECT * FROM tbl_mid_category WHERE tcat_id=?");
              $statement1->execute(array($row['tcat_id']));
              $result1 = $statement1->fetchAll(PDO::FETCH_ASSOC);
              foreach ($result1 as $row1) {
                ?>
                <li><a class="dropdown-item"
                    href="product-category.php?id=<?php echo $row1['mcat_id']; ?>&type=mid-category"><?php echo $row1['mcat_name']; ?></a>
                </li>
                <?php
              }
              ?>
            </ul>
          </li>
          <?php
        }
        ?>
      </nav>

    </div>
    <!-- End header -->

    <!-- login modal -->
    <div class="modal fade" id="staticBackdrop" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
      aria-labelledby="staticBackdropLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header">
            <h1 class="modal-title fs-5" id="staticBackdropLabel">Dead Stock</h1>
            <button type="button" id="btn-close" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div id="modal-body" class="modal-body">
            <!-- Sign In Form -->
            <form id="signin-form" method="POST" action="login.php">
              <h1 class="modal-title fs-5" id="box-header">Login</h1>
              <div class="input-box">
                <input type="email" id="mail" class="input-field" placeholder="Email" name="email" autocomplete="off"
                  required>
                <p id="email-error"></p>
              </div>
              <div class="input-box">
                <input type="password" class="input-field" placeholder="Password" name="password" autocomplete="off"
                  required>

              </div>
              <div class="forgot" id="forgot-password-link">
                <!-- <section>
                <input type="checkbox" id="check">
                <label for="check">Remember me</label>
              </section> -->
                <section>
                  <a href="#">Forgot password ?</a>
                </section>
              </div>
              <div class="input-submit">
                <button class="submit-btn" id="signin-btn" name="login">
                  <label for="submit">Sign In</label>
                </button>
              </div>
              <div class="sign-up-link">
                <p>Don't have an account? <a href="#" id="signup-link">Sign Up</a></p>
              </div>
            </form>

            <!-- Sign Up Form -->
            <form id="signup-form" method="POST" action="registration.php" style="display: none;">
              <h1 class="modal-title fs-5" id="box-header">SignUp</h1>
              <div class="input-box">
                <input type="text" class="input-field" placeholder="Username" name="username" autocomplete="off"
                  required>
              </div>
              <div class="input-box">
                <input type="tel" id="phone-number" class="input-field" placeholder="Phone" name="phone_number"
                  autocomplete="off" required pattern="[0-9]{10}">
                <p id="error-message"></p>
              </div>
              <div class="input-box">
                <input type="email" id="email" class="input-field" placeholder="Email" name="email" autocomplete="off"
                  required>
                <p id="email-error-message"></p>
              </div>
              <div class="input-box">
                <input id="password" type="password" class="input-field" placeholder="Password" name="password"
                  autocomplete="off" required>
                <p id="password-error"></p>
              </div>
              <div class="input-submit">
                <button class="submit-btn" id="signup-btn" name="register">
                  <label for="submit">Sign Up</label>
                </button>
              </div>
              <div class="sign-in-link">
                <p>Already have an account? <a href="#" id="signin-link">Sign In</a></p>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>


    <!-- seller modal -->
    <div class="modal fade" id="seller-registration-modal" data-bs-backdrop="static" data-bs-keyboard="false"
      tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header">
            <h1 class="modal-title fs-5" id="staticBackdropLabel">Dead Stock</h1>
            <button type="button" id="btn-close" class="seller-close btn-close" data-bs-dismiss="modal"
              aria-label="Close"></button>
          </div>
          <div id="modal-body" class="modal-body">
            <!-- Seller registration form -->
            <form id="seller-form" method="POST" action="seller_registration.php">


              <h1 class="modal-title fs-5" id="box-header">Seller Registration</h1>
              <div class="input-box">
                <input type="text" id="seller-name" class="input-field" placeholder="Seller Name" name="seller_name"
                  required value="<?php if (isset($_POST['seller_name'])) {
                    echo $_POST['seller_name'];
                  } ?>">
              </div>

              <div class="input-box">
                <input type="text" id="address" class="input-field" placeholder="Address" name="seller_address" required
                  value="<?php if (isset($_POST['seller_address'])) {
                    echo $_POST['seller_address'];
                  } ?>">
              </div>

              <div class="input-box">
                <input type="tel" id="seller-phone" class="input-field" placeholder="Phone Number" name="seller_phone"
                  required value="<?php if (isset($_POST['seller_phone'])) {
                    echo $_POST['seller_phone'];
                  } ?>">
                <p id="sellerphoneError"></p>
              </div>

              <div class="input-box">
                <input type="email" id="seller-email" class="input-field" placeholder="Email" name="seller_email"
                  required value="<?php if (isset($_POST['seller_email'])) {
                    echo $_POST['seller_email'];
                  } ?>">
                <p id="sellerEmailError"></p>
              </div>

              <div class="input-box">
                <input type="password" id="seller-password" class="input-field" placeholder="Password"
                  name="seller_password" required>
                <p id="seller-password-error"></p>
              </div>

              <div class="input-submit">
                <button class="submit-btn" id="seller-register" value="<?php echo LANG_VALUE_15; ?>" name="form1">
                  <label for="seller-register">Register</label>
                </button>
              </div>
              <div class="sign-in-link">
                <p>Already a Seller?<a href="#" id="loginlink" data-bs-target="#staticBackdrop" data-bs-toggle="modal">
                    Sign In</a></p>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>

    <!-- quote container -->
    <div class="quote-container">
      <p class="quote"><span class="quote-bold">Buy</span> at your Desired bidding price</p>
      <img src="assets/uploads/<?php echo $logo; ?>" alt="Logo" class="logo">
    </div>


    <!-- Product categories -->

    <!-- <div class="product-categories">
    <div class="product-category">
      <a href="#" class="link-body-emphasis link-underline-opacity-0">
        <div class="img-category">
      <img src="./icons/index milling.png">
    </div>
    <p class="category-txt">Indexable Milling Tools</p>
    </a>
    </div>
    <div class="product-category">
      <a href="#" class="link-body-emphasis link-underline-opacity-0">
        <div class="img-category">
      <img src="./icons/endmill.png">
    </div>
    <p class="category-txt">Solid Carbide Endmills</p>
      </a>
    </div>
    <div class="product-category">
      <a href="#" class="link-body-emphasis link-underline-opacity-0">
        <div class="img-category">
      <img src="./icons/turning.png">
    </div>
    <p class="category-txt ">Turning Tools</p>
      </a>
    </div>
    <div class="product-category">
      <a href="#" class="link-body-emphasis link-underline-opacity-0">
        <div class="img-category">
          <img src="./icons/hole.png">
        </div>
        <p class="category-txt">Holemaking Tools</p>
      </a>
    </div>
    <div class="product-category">
      <a href="#" class="link-body-emphasis link-underline-opacity-0">
        <div class="img-category">
      <img src="./icons/Threading tools.png">
    </div>
    <p class="category-txt">Threading Tools</p>
      </a>
    </div>
    <div class="product-category">
      <a href="./products.html" class="link-body-emphasis link-underline-opacity-0">
        <div class="img-category">
          <img src="./icons/others.png">
    </div>
    <p class="category-txt">Others</p>
      </a>
    </div>
</div> -->

<div class="product-categories">
    <?php
    // $query = "SELECT CATEGORY_NAME, CATEGORY_IMAGE FROM category";
    // $result = $connection->query($query);
    $statement = $pdo->prepare("SELECT * FROM category");
    $statement->execute();
    $result = $statement->fetchAll(PDO::FETCH_ASSOC);
    foreach ($result as $row) {
            ?>
            <div class="product-category">
                <a href="#" class="link-body-emphasis link-underline-opacity-0">
                    <div class="img-category">
                        <img src="<?php echo $row['CATEGORY_IMAGE']; ?>">
                    </div>
                    <p class="category-txt"><?php echo $row['CATEGORY_NAME']; ?></p>
                </a>
            </div>
            <?php
        }
  
    ?>
</div>











    <!-- end product category -->

    <!-- banner -->
    <div class="banner">
      <div id="carouselExampleAutoplaying" class="carousel slide" data-bs-ride="carousel">
        <div class="carousel-inner">
          <div class="carousel-item active">
            <img class="img" src="https://rukminim2.flixcart.com/fk-p-flap/1600/270/image/b35a105fe8bc8cbb.png?q=20"
              class="d-block w-100" alt="...">
          </div>
          <div class="carousel-item">
            <img class="img" src="https://rukminim2.flixcart.com/fk-p-flap/1600/270/image/3322c8a97f524397.jpeg?q=20"
              class="d-block w-100" alt="...">
          </div>
          <div class="carousel-item">
            <img class="img" src="https://rukminim2.flixcart.com/fk-p-flap/1600/270/image/42a2afb08834f823.jpeg?q=20"
              class="d-block w-100" alt="...">
          </div>
        </div>
        <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleAutoplaying"
          data-bs-slide="prev">
          <span class="carousel-control-prev-icon" aria-hidden="true"></span>
          <span class="visually-hidden">Previous</span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleAutoplaying"
          data-bs-slide="next">
          <span class="carousel-control-next-icon" aria-hidden="true"></span>
          <span class="visually-hidden">Next</span>
        </button>
      </div>
    </div>
    <!-- end banner  -->

    <!-- Live Bidding -->
    <div class="container swiper">
      <p class="swiper-title">Live Bidding</p>
      <div class="slider-wrapper">
        <div class="card-list swiper-wrapper">
          <div class="card-item swiper-slide">
            <div class="card-img">
              <img alt="Card-img" src="./icons/index milling.png">
            </div>
            <div class="live-bidding-details">
              <div class="live-bidding-product-title">
                <h2>Product title</h2>
              </div>
              <div class="price-section">
                <p class="actual-price">₹1,000.00</p>
                <p class="price-strikethrough"> ₹2,500.00</p>
              </div>
              <div class="live-bidding-price">
                <p>Live Bidding Price: <span class="live-bidding-price-text">₹ 100.00</span></p>
              </div>
              <div class="available-stock">
                <p>Available Stock: <span class="available-stock-text">10</span> units</p>
              </div>
              <div class="bid-ends-in">
                <p>Bid Ends In: <span class="bid-ends-in-text" id="bid-ends-in-time"></span></p>
              </div>
            </div>
          </div>

          <div class="card-item swiper-slide">
            <div class="card-img">
              <img alt="Card-img" src="./icons/index milling.png">
            </div>
            <div class="live-bidding-details">
              <div class="live-bidding-product-title">
                <h2>Product title</h2>
              </div>
              <div class="price-section">
                <p class="actual-price">₹1,000.00</p>
                <p class="price-strikethrough"> ₹2,500.00</p>
              </div>
              <div class="live-bidding-price">
                <p>Live Bidding Price: <span class="live-bidding-price-text">₹ 100.00</span></p>
              </div>
              <div class="available-stock">
                <p>Available Stock: <span class="available-stock-text">20</span> units</p>
              </div>
              <div class="bid-ends-in">
                <p>Bid Ends In: <span class="bid-ends-in-text" id="bid-ends-in-time"></span></p>
              </div>
            </div>
          </div>

          <div class="card-item swiper-slide">
            <div class="card-img">
              <img alt="Card-img" src="./icons/index milling.png">
            </div>
            <div class="live-bidding-details">
              <div class="live-bidding-product-title">
                <h2>Product title</h2>
              </div>
              <div class="price-section">
                <p class="actual-price">₹1,000.00</p>
                <p class="price-strikethrough"> ₹2,500.00</p>
              </div>
              <div class="live-bidding-price">
                <p>Live Bidding Price: <span class="live-bidding-price-text">₹ 100.00</span></p>
              </div>
              <div class="available-stock">
                <p>Available Stock: <span class="available-stock-text">30</span> units</p>
              </div>
              <div class="bid-ends-in">
                <p>Bid Ends In: <span class="bid-ends-in-text" id="bid-ends-in-time"></span></p>
              </div>
            </div>
          </div>

          <div class="card-item swiper-slide">
            <div class="card-img">
              <img alt="Card-img" src="./icons/index milling.png">
            </div>
            <div class="live-bidding-details">
              <div class="live-bidding-product-title">
                <h2>Product title</h2>
              </div>
              <div class="price-section">
                <p class="actual-price">₹1,000.00</p>
                <p class="price-strikethrough"> ₹2,500.00</p>
              </div>
              <div class="live-bidding-price">
                <p>Live Bidding Price: <span class="live-bidding-price-text">₹ 100.00</span></p>
              </div>
              <div class="available-stock">
                <p>Available Stock: <span class="available-stock-text">40</span> units</p>
              </div>
              <div class="bid-ends-in">
                <p>Bid Ends In: <span class="bid-ends-in-text" id="bid-ends-in-time"></span></p>
              </div>
            </div>
          </div>

          <div class="card-item swiper-slide">
            <div class="card-img">
              <img alt="Card-img" src="./icons/index milling.png">
            </div>
            <div class="live-bidding-details">
              <div class="live-bidding-product-title">
                <h2>Product title</h2>
              </div>
              <div class="price-section">
                <p class="actual-price">₹1,000.00</p>
                <p class="price-strikethrough"> ₹2,500.00</p>
              </div>
              <div class="live-bidding-price">
                <p>Live Bidding Price: <span class="live-bidding-price-text">₹ 100.00</span></p>
              </div>
              <div class="available-stock">
                <p>Available Stock: <span class="available-stock-text">50</span> units</p>
              </div>
              <div class="bid-ends-in">
                <p>Bid Ends In: <span class="bid-ends-in-text" id="bid-ends-in-time"></span></p>
              </div>
            </div>
          </div>

          <div class="card-item swiper-slide">
            <div class="card-img">
              <img alt="Card-img" src="./icons/index milling.png">
            </div>
            <div class="live-bidding-details">
              <div class="live-bidding-product-title">
                <h2>Product title</h2>
              </div>
              <div class="price-section">
                <p class="actual-price">₹1,000.00</p>
                <p class="price-strikethrough"> ₹2,500.00</p>
              </div>
              <div class="live-bidding-price">
                <p>Live Bidding Price: <span class="live-bidding-price-text">₹ 100.00</span></p>
              </div>
              <div class="available-stock">
                <p>Available Stock: <span class="available-stock-text">60</span> units</p>
              </div>
              <div class="bid-ends-in">
                <p>Bid Ends In: <span class="bid-ends-in-text" id="bid-ends-in-time"></span></p>
              </div>
            </div>
          </div>
        </div>

        <div class="swiper-pagination"></div>
        <div class="swiper-slide-button swiper-button-prev"></div>
        <div class="swiper-slide-button swiper-button-next"></div>
      </div>
    </div>


    <!-- footer -->
    <div class="footer">
      <footer class="text-center text-lg-start bg-body-tertiary text-muted">
        <!-- Section: Links  -->
        <section class="">
          <div class="container text-center text-md-start mt-5">
            <!-- Grid row -->
            <div class="row mt-3">
              <!-- Grid column -->
              <div class="col-md-3 col-lg-4 col-xl-3 mx-auto mb-4">
                <!-- Content -->
                <h6 class="text-uppercase fw-bold mb-4">
                  <i class="fas fa-gem me-3"></i>Dead Stock
                </h6>
                <p>
                  Here you can use rows and columns to organize your footer content. Lorem ipsum
                  dolor sit amet, consectetur adipisicing elit.
                </p>
              </div>
              <!-- Grid column -->

              <!-- Grid column -->
              <div class="col-md-2 col-lg-2 col-xl-2 mx-auto mb-4">
                <!-- Links -->
                <h6 class="text-uppercase fw-bold mb-4">
                  Products
                </h6>
                <p>
                  <a href="#!" class="text-reset">Product 1</a>
                </p>
                <p>
                  <a href="#!" class="text-reset">Product 2</a>
                </p>
                <p>
                  <a href="#!" class="text-reset">Product 3</a>
                </p>
                <p>
                  <a href="#!" class="text-reset">Product 4</a>
                </p>
              </div>
              <!-- Grid column -->

              <!-- Grid column -->
              <div class="col-md-3 col-lg-2 col-xl-2 mx-auto mb-4">
                <!-- Links -->
                <h6 class="text-uppercase fw-bold mb-4">
                  Useful links
                </h6>
                <p>
                  <a href="#!" class="text-reset">Trending Products</a>
                </p>
                <p>
                  <a href="#!" class="text-reset">My Account</a>
                </p>
                <p>
                  <a href="#!" class="text-reset">Orders</a>
                </p>
                <p>
                  <a href="#!" class="text-reset">Help</a>
                </p>
              </div>
              <!-- Grid column -->

              <!-- Grid column -->
              <div class="col-md-4 col-lg-3 col-xl-3 mx-auto mb-md-0 mb-4">
                <!-- Links -->
                <h6 class="text-uppercase fw-bold mb-4 contact">Contact</h6>
                <p><i class="fas fa-home me-3"></i> MKCE, Karur - 639113</p>
                <p>
                  <i class="fas fa-envelope me-3"></i>
                  example@mkce.ac.in
                </p>
                <p><i class="fas fa-phone me-3"></i> +91 99988 xxxxx</p>
                <p><i class="fas fa-print me-3"></i> +91 99988 xxxxx</p>
              </div>
              <!-- Grid column -->
            </div>
            <!-- Grid row -->
          </div>
        </section>
        <!-- Section: Links  -->

        <!-- Copyright -->
        <div class="text-center p-4" style="background-color: rgba(0, 0, 0, 0.05);">
          © 2024 Copyright:
          <a class="text-reset fw-bold" href="#">Dead Stock</a>
        </div>
        <!-- Copyright -->
      </footer>
    </div>




    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
      integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
      crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
    <script src="index.js"></script>

  </body>

  </html>