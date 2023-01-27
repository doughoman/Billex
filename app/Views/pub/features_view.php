
<?php
$page_title = "Features";
echo view('pub/common/header_view');
?>
<section>
    <div class="main-bg Main_bg_div" id="main_backgrond_image">
        <div class="bannerblackShadow">
            <div class="container d-flex h-100 align-items-center">
                <div class="banner-text" style="margin: 0 auto;">
                    <h1>Features</h1>
                    <h2>Bill Payment</h2>
                    <h4>
                        Give customers the easiest way to pay via:
                    </h4>
                    <ul>
                        <li>Auto-payments</li>
                        <li>Online payment via Card</li>
                        <li>Online payment via E-Check</li>
                        <li>Payment via paper check</li>
                    </ul>
                    <h2>
                        Bill Creation
                    </h2>
                    <h4>
                        Bill your customers easily via:
                    </h4>
                    <ul>
                        <li>Recurring item entry</li>
                        <li>Emailed PDF and HTML generated bills</li>
                        <li>Payment history included on bills</li>
                        <li>Minimum retainer calculations</li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="get-started-main">
            <div class="get-started-btn">
                <div class="dropdown text-right">
                    <a href="<?php echo base_url(); ?>pub/start/signup" class="btn getstarted" id="getStartedBtn" >
                        <span>Get Started</span><br>
                        No payment info required
                    </a>
                </div>  
            </div>
        </div>

    </div>

</section>

<?php echo view('pub/common/footer_view'); ?>

<script type="text/javascript">
<?php
if (isset($_REQUEST['checkout_id'])) {
    ?>
        swal({title: "Your payment have successfully.!", text: "", icon: "success", timer: 3000}).then(function () {
            var new_url = BASE_URL;
            window.history.pushState("data", "Title", new_url);
        });
    <?php
}
?>
    var images = ['banner-1.jpeg', 'banner-2.jpeg', 'banner-3.jpeg', 'banner-4.jpg', 'banner-5.jpeg'];
    $("#main_backgrond_image").css("background-image", "url(<?php echo base_url(); ?>images/" + images[Math.floor(Math.random() * images.length)] + ")");
</script>
