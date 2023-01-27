<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta content="width=device-width, initial-scale=1.0" name="viewport">
        <meta content="ie=edge" http-equiv="X-UA-Compatible">
        <title>Make Payment</title>
        <link rel="shortcut icon" type="image/ico" href="<?php echo base_url(); ?>images/favicon.ico"/>
        <!-- Css Links-->
        <link href="<?php echo base_url(); ?>css/style.css" rel="stylesheet">
        <link href="<?php echo base_url(); ?>css/bootstrap.min.css" rel="stylesheet">
        <link href="<?php echo base_url(); ?>css/dashboard.css" rel="stylesheet">
        <link href="<?php echo base_url(); ?>css/developer.css" rel="stylesheet">
        <?php
        if (isset($_COOKIE['compact']) && $_COOKIE['compact'] == 1) {
            ?>
            <link href="<?php echo base_url(); ?>css/compact_dashboard.css" rel="stylesheet">
            <?php
        }
        ?>
        <script>var BASE_URL = "<?= base_url(); ?>";</script>
        <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.2/css/all.css" integrity="sha384-oS3vJWv+0UjzBfQzYUhtDYW+Pj2yciDJxpsK1OYPAYjqT085Qq/1cq5FLXAZQ7Ay" crossorigin="anonymous">
        <style>
            .admin-contain-main-div{
                padding: 20px;
            }
            .main-section1 {
                display: flex;
                align-items: center;
                height: 100vh;
            }
            body{
                width: 100%;
                height: 100%;
                margin: 0;
                background-image: url(<?= base_url(); ?>images/donate_box.svg);
                background-size: cover;
                background-repeat: no-repeat;
                background-position: center center;
            }
            .make_payment_card{
                background-color: rgba(256,256,256,.8) !important;
            }
            .footer {
                position: fixed;
                bottom: 0;
                right: 0;
                left: 0;
                width: 100%;
            }
            .table-cols {
                width: 19%;
                font-size: 16px;
                padding: 10px;
            }
            @media only screen and (min-width: 320px) and (max-width: 575px) {
                .make_patyment_background {
                    margin: 30px 0px;
                }
                .footer {
                    position: relative;
                }
            }
            @media only screen and (min-width: 576px) and (max-width: 767px) {
                .make_patyment_background {
                    margin: 30px 0px;
                }
                .footer {
                    position: relative;
                }
            }
            @media only screen and (min-width: 768px) and (max-width: 991px) {
                .main-section1 {
                    height: 100vh;
                }
            }
        </style>
    </head>
    <body class="make_payment_body">
        <section class="make_patyment_background">
            <div class="main-section1">
                <div class="container-fluid">
                    <div class="row" style="justify-content: center;"> 
                        <div class="col-sm-12 col-md-8 col-lg-4">
                            <div class="card make_payment_card">
                                <div class="card-body">
                                    <div class="billers_info">
                                        <div class="biller_logo">
                                            <?php
                                            if ($biller_data['logo_seed'] == 0) {
                                                echo '<h2 style="font-size: 30px;margin-bottom: 0;">' . $biller_data['name'] . '</h2>';
                                            } else {
                                                $src = "https://billex.s3.amazonaws.com/biller_image/" . $biller_data['biller_id'] . "-" . base_convert($biller_data['biller_id'] + $biller_data['logo_seed'], 10, 32) . ".jpg";
                                                echo '<img src="' . $src . '" width="100px">';
                                            }
                                            ?>
                                            <p><?= $biller_data['address_pay']; ?><br><?= $biller_data['city_pay'] . ',' . $biller_data['state_pay'] . ' ' . $biller_data['zip_pay']; ?></p>
                                        </div>
                                        <div class="payment_customer_details">
                                            <p><?= $name; ?></p>
                                            <p class="payment_po_number"><?= ($po != "" ? "PO Number: " . $po : ""); ?></p>
                                        </div>
                                    </div>
                                    <div class="payment_info">
                                        <p>You have the following unpaid invoices. Check any you would like to pay.</p>
                                        <p class="payment_po_number">We use the <?= ($biller_data['processor'] == "stripe" ? "Stripe" : "Wepay"); ?> payment system for secure processing of payments.</p>
                                    </div>
                                    <div class="row customer_charge make_payment_form">
                                        <div class="table-heading">
                                            <div class="edittable-cols">

                                            </div> 
                                            <div class="table-cols pay_text_column">
                                                <p>Pay</p>
                                            </div> 
                                            <div class="table-cols datetable-cols no_mob">
                                                <p>Date</p>
                                            </div> 
                                            <div class="table-cols">
                                                <p>Invoice</p>
                                            </div> 
                                            <div class="table-cols">
                                                <p>Amount</p>
                                            </div>
                                            <div class="table-cols">
                                                <p>Balance</p>
                                            </div>
                                        </div>
                                        <div class="customer_listing_charge customer_payment" data-id="<?= $customer_id; ?>">
                                            <?php
                                            $blcount = 0;
                                            foreach ($invoice_data as $value) {
                                                ?>
                                                <div class="charge-tbl <?= ($blcount % 2 ? "bg-gray" : "bg-white1"); ?> action_click">
                                                    <div class="results-data ">
                                                        <div class="edittable-cols">
                                                            <div class="custom-control custom-checkbox" style="cursor: pointer;">
                                                                <input type="checkbox" name="customRadio" data-balance="<?= $value['invoice_balance']; ?>" class="custom-control-input payment_select_checkbox" data-type="total_curent" id="customCheck<?= $blcount; ?>" style="cursor: pointer;">
                                                                <label class="custom-control-label" for="customCheck<?= $blcount; ?>" style="cursor: pointer;"></label>
                                                            </div>
                                                        </div>
                                                        <div class="table-cols pay_text_column">
                                                            <input type="number" class="custom_textbox" data-id="<?= $value['invoice_id']; ?>" data-custid="<?= $customer_id; ?>" max="<?= $value['invoice_balance']; ?>">
                                                        </div>
                                                        <div class="table-cols datetable-cols no_mob">
                                                            <p><?= date("m/d/Y", strtotime($value['time_created'])); ?></p>
                                                        </div> 
                                                        <div class="table-cols">
                                                            <p><?= $value['invoice_number']; ?></p>
                                                        </div> 
                                                        <div class="table-cols">
                                                            <p><?= '$' . number_format($value['amount'], 2); ?></p>
                                                        </div>
                                                        <div class="table-cols">
                                                            <p><?= '$' . number_format($value['invoice_balance'], 2); ?></p>
                                                        </div>
                                                    </div>
                                                    <p class="no_desktop mob_des"><?= date("m/d/Y", strtotime($value['time_created'])); ?></p>
                                                </div>
                                                <?php
                                                $blcount++;
                                            }
                                            ?>
                                            <div class="charge-tbl <?= ($blcount % 2 ? "bg-gray" : "bg-white1"); ?> action_click">
                                                <div class="results-data ">
                                                    <div class="edittable-cols">
                                                        <div class="custom-control custom-checkbox" style="cursor: pointer;">
                                                            <input type="checkbox" name="customRadio" class="custom-control-input payment_select_checkbox" data-type="total_curent" id="customCheck<?= $blcount; ?>" style="cursor: pointer;">
                                                            <label class="custom-control-label" for="customCheck<?= $blcount; ?>" style="cursor: pointer;"></label>
                                                        </div>
                                                    </div>
                                                    <div class="table-cols">
                                                        <input type="number" class="custom_textbox" data-id="0" data-custid="<?= $customer_id; ?>" >
                                                    </div>
                                                    <div class="table-cols other_payment_col">
                                                        <p>Other Payment</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <?php
                                    if ($biller_data['processor'] == "stripe") {
                                        ?>
                                        <div class="form-group row mt-3 mb-0" style="justify-content: center;">
                                            <form action="<?= base_url() . 'pub/makepayment/payment' ?>" method="POST">
                                                <input type="hidden" name="stripeAmount" class="stripeAmount" value="">
                                                <input type="hidden" name="cardholdername" maxlength="70" value="<?= $name; ?>">
                                                <input type="hidden" name="stripeId" class="stripe_id">
                                                <input type="hidden" name="customer_id" value="<?= $customer_id; ?>">
                                                <?php
                                                if ($biller_data['logo_seed'] == 0) {
                                                    $src = "http://hitesh.dev.billex.net/images/Blogo.png";
                                                } else {
                                                    $src = "https://billex.s3.amazonaws.com/biller_image/" . $biller_data['biller_id'] . "-" . base_convert($biller_data['biller_id'] + $biller_data['logo_seed'], 10, 32) . ".jpg";
                                                }
                                                ?>
                                                <script
                                                    src="https://checkout.stripe.com/checkout.js" class="stripe-button"
                                                    data-key="<?= env('stripe.primary_key'); ?>"
                                                    data-image="<?= $src; ?>"
                                                    data-name="<?= $biller_data['name']; ?>"
                                                    data-description=""
                                                    data-amount="{{ORDER_AMOUNT}}"
                                                    data-label="Pay Now">
                                                </script>
                                            </form>
                                        </div>
                                        <?php
                                    } else {
                                        ?>
                                        <div class="form-group row mt-3 mb-0" style="justify-content: center;">
                                            <form action="<?= base_url() . 'pub/makepayment/payment' ?>" method="POST" id="wepay_form">
                                                <input type="hidden" name="stripeAmount" class="stripeAmount" value="">
                                                <input type="hidden" name="cardholdername" maxlength="70" value="<?= $name; ?>">
                                                <input type="hidden" name="stripeId" class="stripe_id">
                                                <input type="hidden" name="customer_id" value="<?= $customer_id; ?>">
                                                <button type="button" class="stripe-button-el wepay-el-button" style="visibility: visible;"><span style="display: block; min-height: 30px;">Pay Now</span></button>
                                            </form>
                                        </div>
                                        <?php
                                    }
                                    ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <?php
        echo view('pub/common/footer_view');
        if (isset($_REQUEST['status']) && $_REQUEST['status'] == 'success') {
            ?>
            <script>
                swal({title: "Your payment have successfully.!", text: "", icon: "success", timer: 3000}).then(function () {
                    window.location.href = BASE_URL;
                });
            </script>
            <?php
        }
        ?>
        <script type="text/javascript">
            function calculateSum() {
                var sum = 0;
                $(".custom_textbox").each(function () {
                    if (!isNaN(this.value) && this.value.length != 0) {
                        sum += parseFloat(this.value);
                    }
                });

                return sum;
            }
            $(".stripe-button-el").attr('disabled', true);
            $(".custom_textbox").blur(function () {
                if ($(this).val() != "") {
                    $(this).val(parseFloat($(this).val()).toFixed(2));
                }
            });
            $(document).on("change", "[name='customRadio']", function () {
                if ($(this).prop("checked") == true) {
                    $(".stripe-button-el").attr('disabled', true);
                    $(".stripeAmount").val(parseFloat('0.00').toFixed(2));
                    if ($(this).attr('data-type') == 'total_curent') {
                        $(this).parents('.results-data').find('.custom_textbox').val(parseFloat($(this).attr('data-balance')).toFixed(2));
                        $(".stripeAmount").val(parseFloat($(this).attr('data-price')).toFixed(2));
                        $(".stripe-button-el").attr('disabled', false);
                        total = calculateSum();
                        $(".stripeAmount").val(parseFloat(total).toFixed(2));
                        $(".stripe-button-el").find('span').text('Pay $' + parseFloat(total).toFixed(2) + ' Now');
                    }
                } else {
                    $(this).parents('.results-data').find('.custom_textbox').val('');
                    var checkedNum = $('[name="customRadio"]:checked').length;
                    if (!checkedNum) {
                        $(".stripeAmount").val(parseFloat('0.00').toFixed(2));
                        $(".stripe-button-el").attr('disabled', true);
                        $(".stripe-button-el").find('span').text('Pay Now');
                    } else {
                        total = calculateSum();
                        $(".stripeAmount").val(parseFloat(total).toFixed(2));
                        $(".stripe-button-el").find('span').text('Pay $' + parseFloat(total).toFixed(2) + ' Now');
                    }
                }
            });
            $(document).on("keyup", ".custom_textbox", function () {
                var max = parseFloat($(this).attr('max'));
                if ($(this).val() > max)
                {
                    $(this).val(max);
                }
                if ($(this).val() != "") {
                    total = calculateSum();
                    $(".stripeAmount").val(parseFloat(total).toFixed(2));
                    $(".stripe-button-el").attr('disabled', false);
                    $(".stripe-button-el").find('span').text('Pay $' + parseFloat(total).toFixed(2) + ' Now');
                    $(this).parents('.results-data').find('.custom-control-input').attr('checked', true);
                } else {
                    $(this).parents('.results-data').find('.custom-control-input').attr('checked', false);
                    var checkedNum = $('[name="customRadio"]:checked').length;
                    if (!checkedNum) {
                        $(".stripeAmount").val(parseFloat('0.00').toFixed(2));
                        $(".stripe-button-el").attr('disabled', true);
                        $(".stripe-button-el").find('span').text('Pay Now');
                    }
                }
            });
            $(document).on("click", ".stripe-button-el", function () {
                var customer_invoice = [];
                $(".customer_payment").each(function () {
                    $this = $(this);
                    $this.find('.custom_textbox').each(function () {
                        $input = $(this);
                        if ($input.val() != "") {
                            customer_invoice.push([$input.attr('data-id'), $input.attr('data-custid'), $input.val()]);
                        }
                    });
                });
<?php
if ($biller_data['processor'] == "wepay") {
    ?>
                    var url = BASE_URL + "pub/makepayment/add_wepay_invoice";
<?php } else {
    ?>
                    var url = BASE_URL + "pub/makepayment/add_payment_invoice";
<?php }
?>
                $.ajax({
                    url: url,
                    type: "POST",
                    data: {'invoice': customer_invoice},
                    dataType: "JSON",
                    success: function (data)
                    {
                        if (data.status) {
                            $(".stripe_id").val(data.id);
<?php
if ($biller_data['processor'] == "wepay") {
    ?>
                                $("#wepay_form").submit();
<?php } ?>
                        }
                    }
                });
            });

        </script>