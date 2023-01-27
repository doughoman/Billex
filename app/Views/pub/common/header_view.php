<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta content="width=device-width, initial-scale=1.0" name="viewport">
        <meta content="ie=edge" http-equiv="X-UA-Compatible">
        <meta name="google-site-verification" content="FKbhzxiZ-SeX44xcIY2Jde2pDYw9MR9NqVfL-FSCL44" />
        <title><?= (isset($page_title) ? $page_title : "BillX"); ?></title>
        <link rel="shortcut icon" type="image/ico" href="<?php echo base_url(); ?>images/favicon.ico"/>
        <!-- Css Links-->
        <link href="<?php echo base_url(); ?>css/style.css" rel="stylesheet">
        <link href="<?php echo base_url(); ?>css/bootstrap.min.css" rel="stylesheet">
        <link href="<?php echo base_url(); ?>css/dashboard.css" rel="stylesheet">
        <link href="<?php echo base_url(); ?>css/developer.css" rel="stylesheet">
        <script>var BASE_URL = "<?= base_url(); ?>";</script>
        <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.2/css/all.css" integrity="sha384-oS3vJWv+0UjzBfQzYUhtDYW+Pj2yciDJxpsK1OYPAYjqT085Qq/1cq5FLXAZQ7Ay" crossorigin="anonymous">
    </head>
    <body>
        <!-- Header Start Here-->
        <div class="top-bar">
            <div class="container">
                <div class="row signup_signin_header">
                    <div class="col-md-6 laptop_last">
                        <div class="contact-info ">
                            <span><a href="tel:888-987-6557"><i class="fas fa-phone"></i>&nbsp; 888-987-6557 &nbsp;|&nbsp;</a></span> 
                            <span><a href="mailto:support@billx.app"><i class="far fa-envelope"></i>&nbsp; support@billx.app</a></span>
                        </div>
                    </div>
                    <?php
                    $hide_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
                    if (strpos(explode("?", $hide_url)[0], "login") || strpos(explode("?", $hide_url)[0], "signup")) {
                        $hide = 1;
                    } else {
                        $hide = 0;
                    }
                    ?>
                    <div class="col-md-6 setup_page_hide laptop_first">
                        <div style="display: <?= ($hide == 1 ? "" : "none") ?>">
                            <a class="dashboard_billex_logo" href="<?php echo base_url(); ?>"><img src="<?php echo base_url(); ?>images/bx-150x65.png" class="img-fluid mob-logo" width="60"></a>
                        </div>
                        <div class="contact-info Nomob text-right" style="display: <?= ($hide == 1 ? "none" : "") ?>">
                            <?php if (isset($_SESSION["login_userid"]) && !empty($_SESSION["login_userid"])) {
                                ?>
                                <span class="mob-login mr-2"><a href="<?php echo base_url(); ?>aut/dashboard"><i class="fas fa-tachometer-alt"></i>&nbsp; Dashboard</a></span>
                                <span class="mob-login no_mob"><a href="<?php echo base_url(); ?>logout"><i class="fas fa-sign-in-alt"></i>&nbsp; Sign Out</a></span>
                                <?php
                            } else {
                                ?>
                                <span class="mob-login"><a href="<?php echo base_url(); ?>login"><i class="fas fa-sign-in-alt"></i>&nbsp; Sign In</a></span>
                                <?php
                            }
                            ?>

                        </div>
                    </div>
                </div>
            </div>
        </div>
        <nav class="navbar navbar-expand-lg navbar-light main-navbar setup_page_hide" style="display: <?= ($hide == 1 ? "none" : "") ?>">
            <div class="container">
                <a class="navbar-brand" href="<?php echo base_url(); ?>"><img src="<?php echo base_url(); ?>images/bx-150x65.png" class="img-fluid mob-logo" width="138"></a>
                <button class="navbar-toggler navbar-style navbar-border" type="button" data-toggle="collapse" data-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse cust-colleps" id="navbarNavDropdown">
                    <ul class="navbar-nav margin-auto">
                        <li class="nav-item active">
                            <a class="nav-link cust-link navbar-text" href="<?= base_url(); ?>features">Features <span class="sr-only">(current)</span></a>
                        </li>
                        <li class="nav-item ">
                            <a class="nav-link cust-link navbar-text" href="<?= base_url(); ?>pricing">Pricing </a>
                        </li>
                        <li class="nav-item ">
                            <a class="nav-link cust-link navbar-text" href="<?= base_url(); ?>howto">How-To</a>
                        </li>
                        <?php if (isset($_SESSION["login_userid"]) && !empty($_SESSION["login_userid"])) {
                            ?>
                            <li class="nav-item no_desktop">
                                <a class="nav-link cust-link navbar-text" href="<?php echo base_url(); ?>logout"><i class="fas fa-sign-in-alt"></i>&nbsp; Sign Out</a>
                            </li>
                        <?php }
                        ?>
                    </ul>
                </div>
            </div>
        </nav>
        <!-- End Header-->
