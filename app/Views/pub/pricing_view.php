
<?php
$page_title = "Pricing";
echo view('pub/common/header_view');
?>
<section>
    <div class="main-bg Main_bg_div" id="main_backgrond_image">
        <div class="bannerblackShadow">
            <div class="container d-flex h-100 align-items-center">

                <div class="banner-text mx-auto">
                    <h1>Pricing</h1>
                    <h2>$1/customer/year</h2>
                    <ul>
                        <li>No added fees on customer bill payments<br><small>(3rd party processor fees still apply, typically 2.9% + 30¢ per payment)</small></li>
                        <li>$1/customer per year<br><small>(Sold in blocks of 20)</small></li>
                        <li>Invoice mailing service $1/invoice<br>
                        <small>We print, stuff, stamp, and mail your invoices</small></li>
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
