<!-- /.container-fluid-->
<footer class="sticky-footer">
    <div class="footer-section">
        <div class="footer-left">  
            <p class="mb-0">
                <a href="mailto:support@billex.net"><i class="fas fa-envelope"></i>&nbsp; support@billex.net</a>
            </p>

            <a href="tel:888-987-6557"><i class="fas fa-phone"></i>&nbsp; 888-987-6557 &nbsp;&nbsp;</a>

        </div>
        <div class="footer-center no_mob">  
            <a href="#"><i class="fas fa-bullhorn"></i>&nbsp; FeedBack</a>
            <a class="dashboard_billex_logo" href="<?php echo base_url(); ?>"><img src="<?php echo base_url(); ?>images/billex.png" class="img-fluid mob-logo" width="69"></a>
        </div>
        <div class="footer-right">  
            <div class="coyright text-right">
                <a href="#" class="no_desktop"><i class="fas fa-bullhorn"></i>&nbsp; FeedBack</a>
                <a href="#" class="no_mob">Terms &amp; Conditions</a>
                <p class="mb-0">&copy; 2019 EMRI <span class="no_mob">Corporation</span></p>
            </div>
        </div>
    </div>

</footer>
<!-- Scroll to Top Button-->
<!-- <a class="scroll-to-top rounded" href="#page-top">
    <i class="fa fa-angle-up"></i>
</a> -->
</div> <!-- content-wrapper End -->
</body>
<?php
$url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
if (strpos(explode("?", $url)[0], "alert")) {
    ?>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
    <?php
} else {
    ?>

    <script src="<?php echo base_url(); ?>js/jquery.min.js"></script>
    <?php
}
?>

<script src="<?php echo base_url(); ?>js/moment-with-locales.min.js"></script>
<script src="<?php echo base_url(); ?>js/bootstrap-datetimepicker.min.js"></script>
<script src="<?php echo base_url(); ?>js/popper.min.js"></script>
<script src="<?php echo base_url(); ?>js/bootstrap.min.js"></script>

<?php
if (isset($_COOKIE['compact']) && $_COOKIE['compact'] == 1) {
    if (isset($_COOKIE['sidenav_toggle'])) {
        ?>
        <script>
            if ($(window).width() > 767) {
                $('.navbar-sidenav [data-toggle="tooltip"]').tooltip({
                    template: '<div class="tooltip navbar-sidenav-tooltip" role="tooltip" style="pointer-events: none;"><div class="arrow"></div><div class="tooltip-inner"></div></div>'
                });
                $(".navbar-sidenav .nav-link-collapse").addClass("collapsed");
                $(".navbar-sidenav .sidenav-second-level, .navbar-sidenav .sidenav-third-level").removeClass("show");
            }
        </script>
        <?php
    }
} else {
    if (isset($_COOKIE['sidenav_toggle']) && $_COOKIE['sidenav_toggle'] == 1) {
        ?>
        <script>
            if ($(window).width() > 767) {
                $('.navbar-sidenav [data-toggle="tooltip"]').tooltip({
                    template: '<div class="tooltip navbar-sidenav-tooltip" role="tooltip" style="pointer-events: none;"><div class="arrow"></div><div class="tooltip-inner"></div></div>'
                });
                $('.administration_link').tooltip({
                    template: '<div class="tooltip navbar-sidenav-tooltip" role="tooltip" style="pointer-events: none;"><div class="arrow"></div><div class="tooltip-inner"></div></div>'
                });
                $(".navbar-sidenav .nav-link-collapse").addClass("collapsed");
                $(".navbar-sidenav .sidenav-second-level, .navbar-sidenav .sidenav-third-level").removeClass("show");
            }

        </script>
        <?php
    }
}
?>

<script src="<?php echo base_url(); ?>js/bootstrap-datepicker.js"></script>
<script src="<?php echo base_url(); ?>js/bootstrap-select.min.js"></script>
<script src="<?php echo base_url(); ?>js/papaparse.min.js"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/bootbox.js/4.4.0/bootbox.min.js"></script>
<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.inputmask/3.3.4/jquery.inputmask.bundle.min.js"></script>
<script src="https://code.highcharts.com/highcharts.js"></script>
<script src="https://code.highcharts.com/modules/exporting.js"></script> 
<script type="text/javascript">
    function getUrlVars()
    {
        var vars = [], hash;
        var hashes = window.location.href.slice(window.location.href.indexOf('?') + 1).split('&');
        for (var i = 0; i < hashes.length; i++)
        {
            hash = hashes[i].split('=');
            vars.push(hash[0]);
            vars[hash[0]] = hash[1];
        }
        return vars;
    }
    $(document).on("click", ".nav-link-collapse", function () {
        $(".nav-link-collapse").removeClass("active_menu");
        $(this).addClass("active_menu");
    });
    $(document).ready(function () {
        var expression = window.location.href.split("/")[4];
        switch (expression) {
            case 'dashboard':
                $("nav-link-collapse").removeClass("active_menu");
                $(".dashboard_link").addClass("active_menu");
                break;
            case 'charges':
                $("nav-link-collapse").removeClass("active_menu");
                $(".charges_link").addClass("active_menu");
                break;
            case 'charges?bill=1':
                $("nav-link-collapse").removeClass("active_menu");
                $(".charges_link").addClass("active_menu");
                break;
            case 'billcharges':
                $("nav-link-collapse").removeClass("active_menu");
                $(".bill_link").addClass("active_menu");
                break;
            case 'billcharges?q=initialize':
                $("nav-link-collapse").removeClass("active_menu");
                $(".bill_link").addClass("active_menu");
                break;
            case 'postpayment':
                $("nav-link-collapse").removeClass("active_menu");
                $(".postpayment_link").addClass("active_menu");
                break;
            case 'printdeposit':
                $("nav-link-collapse").removeClass("active_menu");
                $(".deposit_link").addClass("active_menu");
                break;
            case 'customer':
                $("nav-link-collapse").removeClass("active_menu");
                $(".customers_link").addClass("active_menu");
<?php
if (isset($_COOKIE['compact']) && $_COOKIE['compact'] == 1) {
    ?>
                    if ($(window).width() > 767) {
                        $(document).on("click", "#sidenavToggler", function () {
                            $(".actions-icon").toggleClass("customer_action_icon");
                        });
                    }
    <?php
}
if (!isset($_COOKIE['sidenav_toggle'])) {
    ?>
                    if ($(window).width() > 767) {
                        $(".actions-icon").addClass("customer_action_icon");
                    }
    <?php
}
?>

                break;
            case 'customer?q=initialize':
                $("nav-link-collapse").removeClass("active_menu");
                $(".customers_link").addClass("active_menu");
<?php
if (isset($_COOKIE['compact']) && $_COOKIE['compact'] == 1) {
    ?>
                    if ($(window).width() > 767) {
                        $(document).on("click", "#sidenavToggler", function () {
                            $(".actions-icon").toggleClass("customer_action_icon");
                        });
                    }
    <?php
}
if (!isset($_COOKIE['sidenav_toggle'])) {
    ?>
                    if ($(window).width() > 767) {
                        $(".actions-icon").addClass("customer_action_icon");
                    }
    <?php
}
?>
                break;
            case 'alert':
                var type = "";
                if (window.location.href.split("/")[4].indexOf("alert") != -1) {
                    type = "alert";
                }
                $("nav-link-collapse").removeClass("active_menu");
                $(".superadmin_" + type).parent('li').css("background-color", "#40926a");
                $(".superadmin_link").trigger("click");
                break;
            case 'administration':
                var type = "";
                if (window.location.href.split("/")[5].indexOf("item") != -1) {
                    type = "item";
                }
                if (window.location.href.split("/")[5].indexOf("import") != -1) {
                    type = "import";
                }
                if (window.location.href.split("/")[5].indexOf("export") != -1) {
                    type = "export";
                }
                if (window.location.href.split("/")[5].indexOf("settings") != -1) {
                    type = "settings";
                }
                if (window.location.href.split("/")[5].indexOf("users") != -1 || window.location.href.split("/")[5].indexOf("add_biller_user") != -1) {
                    type = "users";
                }
                if (window.location.href.split("/")[5].indexOf("subscription") != -1) {
                    type = "subscription";
                }
                $("nav-link-collapse").removeClass("active_menu");
                $(".administration_" + type).parent('li').css("background-color", "#40926a");
                $(".administration_link").trigger("click");
                $("#administration").show();
                break;
            default:

        }
        $(document).on("click", ".chav-left-menu-click", function () {
            window.history.back();
        });
        $("#sidenavToggler").click(function (o) {
            o.preventDefault();
            $("body").toggleClass("sidenav-toggled");
            $(".navbar-sidenav .sidenav-second-level, .navbar-sidenav .sidenav-third-level").removeClass("show");
        });
<?php ?>
        $(document).on('click', '#comapct_view', function () {
            $.ajax({
                url: BASE_URL + "aut/dashboard/set_compact",
                type: "POST",
                data: '',
                success: function (data)
                {
                    location.reload();
                }
            });
        });
        $(document).on('click', '#sidenavToggler', function () {
            $.ajax({
                url: BASE_URL + "aut/dashboard/sidenav_toggle",
                type: "POST",
                data: '',
                success: function (data)
                {

                }
            });
        });
    });
    $(function () {
        $('.btn-send_invoice').tooltip();
    });
    function dateAdd(date, interval, units) {
        if (!(date instanceof Date))
            return undefined;
        var ret = new Date(date); //don't change original date
        var checkRollover = function () {
            if (ret.getDate() != date.getDate())
                ret.setDate(0);
        };
        switch (String(interval).toLowerCase()) {
            case 'day'    :
                ret.setDate(ret.getDate() + units);
                break;
            case 'hour'   :
                ret.setTime(ret.getTime() + units * 3600000);
                break;
            case 'minute' :
                ret.setTime(ret.getTime() + units * 60000);
                break;
            default       :
                ret = undefined;
                break;
        }
        return ret;
    }
    function getCookie(name) {
        // Split cookie string and get all individual name=value pairs in an array
        var cookieArr = document.cookie.split(";");

        // Loop through the array elements
        for (var i = 0; i < cookieArr.length; i++) {
            var cookiePair = cookieArr[i].split("=");

            /* Removing whitespace at the beginning of the cookie name
             and compare it with the given string */
            if (name == cookiePair[0].trim()) {
                // Decode the cookie value and return
                return decodeURIComponent(cookiePair[1]);
            }
        }

        // Return null if not found
        return null;
    }
</script>
</html>