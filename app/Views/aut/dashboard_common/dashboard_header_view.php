<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta content="width=device-width, initial-scale=1.0" name="viewport">
        <meta content="ie=edge" http-equiv="X-UA-Compatible">
        <title>Billex</title>
        <!-- Css Links-->

        <link href="<?php echo base_url(); ?>css/bootstrap.min.css" rel="stylesheet">
        <link href="<?php echo base_url(); ?>css/mobile_menu.css" rel="stylesheet">
        <link href="<?php echo base_url(); ?>css/sb-admin.css" rel="stylesheet">
        <link href="<?php echo base_url(); ?>css/bootstrap-select.min.css" rel="stylesheet">
        <link href="<?php echo base_url(); ?>css/style.css" rel="stylesheet">
        <link href="<?php echo base_url(); ?>css/dashboard.css" rel="stylesheet">
        <link href="<?php echo base_url(); ?>css/jquery.rotaryswitch.css" rel="stylesheet">
        <?php
        if (isset($_COOKIE['compact']) && $_COOKIE['compact'] == 1) {
            echo '<link href="' . base_url() . 'css/compact_dashboard.css" rel="stylesheet">';
        }
        if (isset($_COOKIE['compact']) && $_COOKIE['compact'] == 1) {
            if (isset($_COOKIE['sidenav_toggle'])) {
                $class = 'sidenav-toggled';
            } else {
                $class = '';
            }
        } else {
            if (isset($_COOKIE['sidenav_toggle']) && $_COOKIE['sidenav_toggle'] == 1) {
                $class = 'sidenav-toggled';
            }
        }
        ?>
        <link href="<?php echo base_url(); ?>css/datepicker.css" rel="stylesheet">
        <link href="<?php echo base_url(); ?>css/bootstrap-datetimepicker.min.css" rel="stylesheet">
        <script>var BASE_URL = "<?= base_url(); ?>";</script>
        <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.2/css/all.css" integrity="sha384-oS3vJWv+0UjzBfQzYUhtDYW+Pj2yciDJxpsK1OYPAYjqT085Qq/1cq5FLXAZQ7Ay" crossorigin="anonymous">
        <style>
            #mobnav .navbar-sidenav .nav-link-collapse.collapsed:after {
                content: '\f054';
            }
            #mobnav .navbar-sidenav .nav-link-collapse:after {
                float: right;
                content: '\f078';
                font-style: normal;
                font-weight: 600;
                text-decoration: inherit;
                font-size: 13px;
                font-family: "Font Awesome 5 Free";
            }
        </style>
    </head>
    <body class="fixed-nav sticky-footer <?= (isset($class) ? $class : ""); ?>" id="page-top">
        <div class="no_desktop_menu main_title_div ">

            <div>
                <ul class="navbar-nav ml-auto no_allform">
                    <li class="nav-item dropdown ipad_name_display">
                        <a class="nav-link nv dropdown-toggle mr-lg-2 login_user_name" id="messagesDropdown" href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <span class="login_user_name"><?= (isset($_SESSION["display_name"]) ? $_SESSION["display_name"] : ""); ?></span>
                        </a>
                        <div class="dropdown-menu dm dm_user_name" aria-labelledby="messagesDropdown">
                            <a class="dropdown-item" href="#">
                                Change Biller
                            </a>
                        </div>

                    </li>

                </ul>
            </div>
        </div>