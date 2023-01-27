<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta content="width=device-width, initial-scale=1.0" name="viewport">
        <meta content="ie=edge" http-equiv="X-UA-Compatible">
        <title>Transactions History</title>
        <link rel="shortcut icon" type="image/ico" href="<?php echo base_url(); ?>images/favicon.ico"/>
        <link href="<?php echo base_url(); ?>css/bootstrap.min.css" rel="stylesheet">
        <link href="<?php echo base_url(); ?>css/mobile_menu.css" rel="stylesheet">
        <link href="<?php echo base_url(); ?>css/sb-admin.css" rel="stylesheet">
        <link href="<?php echo base_url(); ?>css/bootstrap-select.min.css" rel="stylesheet">
        <link href="<?php echo base_url(); ?>css/style.css" rel="stylesheet">
        <link href="<?php echo base_url(); ?>css/dashboard.css" rel="stylesheet">
        <link href="<?php echo base_url(); ?>css/datepicker.css" rel="stylesheet">
        <link href="<?php echo base_url(); ?>css/bootstrap-datetimepicker.min.css" rel="stylesheet">
        <script>var BASE_URL = "<?= base_url(); ?>";</script>
        <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.2/css/all.css" integrity="sha384-oS3vJWv+0UjzBfQzYUhtDYW+Pj2yciDJxpsK1OYPAYjqT085Qq/1cq5FLXAZQ7Ay" crossorigin="anonymous">
        <style>
            .admin-contain-main-div{
                padding: 20px;
            }
            .main-section1 {
                display: flex;
                margin-top: 80px;
                height: 90vh;
            }
            body{
                width: 100%;
                height: 100%;
                margin: 0;
                background-image: url(<?= base_url(); ?>images/transaction.svg);
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
            .footer_links_div {
                display: flex;
                justify-content: center;
                align-items: center;
                flex-wrap: wrap;
            }
            .footer {
                background-color: #327052;
            }
            .footer_links_div a {
                padding-right: 5px;
            }
            .transction_history_main{
                margin-left: 5%;
            }
            .customer_invoice_name{
                font-size: 20px;
            }
            .customer_invoice_details p{
                margin-bottom: 0;
            }
            .customer_invoice_details {
                margin-bottom: 20px;
            }
            .customer_register .dtable-cols {
                width: 26%;
            }
            .customer_charge .table-cols {
                width: 10%;
            }
            .customer_charge .tbl_refernce {
                width: 29%;
            }
            @media only screen and (min-width: 320px) and (max-width: 575px) {
                .make_patyment_background {
                    margin: 0px;
                }
                .footer {
                    position: relative;
                }
                body{
                    background-image:linear-gradient(to bottom, rgb(33, 87, 49), rgba(50, 112, 82, 0.05)),  url(<?= base_url(); ?>images/transaction.svg);
                    background-position: right;
                }
                .transction_history_main{
                    margin-left: -15px;
                    justify-content: center;
                }
                .customer_register .dtable-cols {
                    width: 78%;
                    text-align: right;
                }
                .customer_charge .table-cols {
                    width: 21%;
                }
                .ml1{
                    margin-left: 4px;
                }
            }
            @media only screen and (min-width: 576px) and (max-width: 767px) {
                .make_patyment_background {
                    margin: 0px;
                }
                .footer {
                    position: relative;
                }
                body{
                    background-image:linear-gradient(to bottom, rgb(33, 87, 49), rgba(50, 112, 82, 0.05)),  url(<?= base_url(); ?>images/transaction.svg);
                    background-position: right;
                }
                .transction_history_main{
                    margin-left: -15px;
                    justify-content: center;
                }
                .customer_register .dtable-cols {
                    width: 78%;
                    text-align: right;
                }
                .customer_charge .table-cols {
                    width: 21%;
                }
                .ml1{
                    margin-left: 4px;
                }
            }
            @media only screen and (min-width: 768px) and (max-width: 991px) {
                .main-section1 {
                    height: 100vh;
                }
                body{
                    background-position: right;
                }
                .transction_history_main{
                    margin-left: -15px;
                    justify-content: center;
                }
                .customer_charge .tbl_refernce {
                    display: none;
                }
                .ipad_ref{
                    display: block;
                    margin-left: 45px;
                    font-size: 16px;
                    padding: 10px;
                    margin-bottom: 0;
                }
                .customer_charge .table-cols {
                    width: 15%;
                }
                .customer_register .dtable-cols {
                    width: 35%;
                }
            }
            @media only screen and (min-width: 992px) and (max-width: 1199px) {
                body{
                    background-position: right;
                }
                .transction_history_main{
                    margin-left: -15px;
                    justify-content: center;
                }
                .customer_charge .tbl_refernce {
                    display: none;
                }
                .ipad_ref{
                    display: block;
                    margin-left: 45px;
                    font-size: 16px;
                    padding: 10px;
                    margin-bottom: 0;
                }
                .customer_charge .table-cols {
                    width: 15%;
                }
                .customer_register .dtable-cols {
                    width: 35%;
                }
            }
            @media only screen and (min-width: 1200px) and (max-width: 1440px) {
                .customer_register .dtable-cols {
                    width: 28%;
                }
            }
        </style>
    </head>
    <body>
        <section class="make_patyment_background">
            <div class="main-section1">
                <div class="container-fluid">
                    <div class="row transction_history_main">
                        <div class="col-sm-12 col-md-11 col-lg-10 col-xl-8">
                            <?php if (isset($history_data) && count($history_data) > 0) {
                                ?>
                                <div class="row ml1">
                                    <div style="color: white;" class="customer_invoice_details">
                                        <strong class="customer_invoice_name" id="add_charges_heading"><?= $customer_data['name']; ?></strong>
                                        <?= (isset($customer_data['identifier']) && $customer_data['identifier'] != '' ? "<p>Account ID: " . $customer_data['identifier'] . "</p>" : ""); ?>
                                        <?= (isset($customer_data['po']) && $customer_data['po'] != '' ? "<p>PO Number: " . $customer_data['po'] . "</p>" : ""); ?>
                                    </div>
                                </div>
                                <div class="row ml1">
                                    <div style="color: white;">
                                        <h5 class="" id="add_charges_heading">Transactions History</h5>
                                        <p>Invoice Id: <?= $invoice_id; ?></p>
                                    </div>
                                </div>
                                <div class="row invoice_history_main customer_charge customer_register">
                                    <div class="table-heading">
                                        <div class="iedittable-cols">

                                        </div> 
                                        <div class="table-cols datetable-cols">
                                            <p>Date</p>
                                        </div> 
                                        <div class="table-cols">
                                            <p>Type</p>
                                        </div> 
                                        <div class="table-cols tbl_refernce no_mob">
                                            <p>Reference</p>
                                        </div> 
                                        <div class="dtable-cols no_mob">
                                            <p>Description</p>
                                        </div> 
                                        <div class="table-cols text-right">
                                            <p>Amount</p>
                                        </div> 
                                        <div class="table-cols text-right">
                                            <p>Balance</p>
                                        </div> 

                                    </div>
                                    <?php
                                    $tbalance = 0.00;
                                    $credit_id = '';
                                    foreach ($history_data as $rvalue) {
                                        if (isset($rvalue['invoice_number']) && !empty($rvalue['invoice_number'])) {
                                            $tbalance = $tbalance + floatval($rvalue['amount']);
                                        } else {
                                            $tbalance = $tbalance - floatval($rvalue['total']);
                                            $credit_id.=$rvalue['credit_id'] . ',';
                                        }
                                    }
                                    $credit_id = rtrim($credit_id, ',');
                                    $count = 0;
                                    foreach ($history_data as $key => $value) {
                                        ?>
                                        <div class="charge-tbl <?= ($count % 2 ? "bg-gray" : "bg-white1"); ?>">
                                            <div class="results-data ">
                                                <div class="iedittable-cols">
                                                    <?php
                                                    if (isset($value['invoice_number']) && !empty($value['invoice_number'])) {
                                                        ?>
                                                        <button type="button" class="edit_delete_btn invoice_file_btn" data-url="<?= 'https://billex.s3.amazonaws.com/invoice/' . encodeHashids(array(intval($value['biller_id']), intval($value['invoice_id']), intval($value['customer_id']))) . '.pdf' ?>"><i class="far fa-file-alt"></i></button>
                                                        <?php
                                                    } else {
                                                        if ($value['type'] == "Cash" || $value['type'] == "Check") {
                                                            ?>
                                                            <button type="button" class="edit_delete_btn deposit_pdf" data-id="<?= $credit_id; ?>" style="color: #17a2b8;"><i class="far fa-file-alt"></i></button>
                                                            <?php
                                                        }
                                                    }
                                                    ?>
                                                </div> 
                                                <div class="table-cols datetable-cols">
                                                    <p class="no_mob"> <?= date("m/d/Y", strtotime($value['time_created'])); ?> </p>
                                                    <p class="no_desktop"> <?= date("m/d/y", strtotime($value['time_created'])); ?> </p>
                                                </div> 
                                                <div class="table-cols">
                                                    <?php
                                                    if (isset($value['invoice_number']) && !empty($value['invoice_number'])) {
                                                        echo '<p>Invoice</p>';
                                                    } else {
                                                        echo '<p>' . $value['type'] . '</p>';
                                                    }
                                                    ?>
                                                </div> 
                                                <div class="table-cols tbl_refernce no_mob">
                                                    <?php
                                                    if (isset($value['invoice_number']) && !empty($value['invoice_number'])) {
                                                        echo '<p>' . $value['invoice_number'] . '</p>';
                                                    } else {
                                                        echo '<p>' . $value['reference'] . '</p>';
                                                    }
                                                    ?>
                                                </div> 
                                                <div class="dtable-cols  no_mob">
                                                    <?php
                                                    if (isset($value['invoice_number']) && !empty($value['invoice_number'])) {
                                                        echo '<p></p>';
                                                    } else {
                                                        echo '<p>' . $value['description'] . '</p>';
                                                    }
                                                    ?>
                                                </div> 
                                                <div class="table-cols text-right">
                                                    <?php
                                                    if (isset($value['invoice_number']) && !empty($value['invoice_number'])) {
                                                        echo '<p>$' . number_format($value['amount'], 2) . '</p>';
                                                    } else {
                                                        echo '<p class="text-muted">($' . number_format($value['total'], 2) . ')</p>';
                                                    }
                                                    ?>
                                                </div> 
                                                <div class="table-cols text-right">
                                                    <p>
                                                        <?php
                                                        if ($tbalance < 0) {
                                                            echo '-$' . number_format(abs($tbalance), 2);
                                                        } else {
                                                            echo '$' . number_format(abs($tbalance), 2);
                                                        }
                                                        if (isset($value['invoice_number']) && !empty($value['invoice_number'])) {
                                                            $tbalance = $tbalance - floatval($value['amount']);
                                                        } else {
                                                            $tbalance = $tbalance + floatval($value['total']);
                                                        }
                                                        ?>  
                                                    </p>
                                                </div> 

                                            </div>
                                            <p class="no_desktop no_mob ipad_ref">
                                                <?php
                                                if (isset($value['invoice_number']) && !empty($value['invoice_number'])) {
                                                    echo $value['invoice_number'];
                                                } else {
                                                    echo $value['reference'];
                                                }
                                                ?>
                                            </p>
                                            <p class="no_desktop mob_des"><?php
                                                if (isset($value['invoice_number']) && !empty($value['invoice_number'])) {
                                                    echo '';
                                                } else {
                                                    echo $value['description'];
                                                }
                                                ?>
                                            </p>
                                        </div>
                                        <?php
                                        $count ++;
                                    }
                                    ?>
                                    <div class="charge-tbl <?= ($count % 2 ? "bg-gray" : "bg-white1"); ?>">
                                        <div class="results-data ">
                                            <div class="edittable-cols no_mob">

                                            </div> 
                                            <div class="table-cols datetable-cols no_mob">

                                            </div> 
                                            <div class="table-cols no_mob">

                                            </div> 
                                            <div class="table-cols tbl_refernce no_mob">

                                            </div> 
                                            <div class="dtable-cols">
                                                <p>Beginning Balance</p>
                                            </div> 
                                            <div class="table-cols text-right no_mob">

                                            </div> 
                                            <div class="table-cols text-right">
                                                <p>
                                                    $0.00
                                                </p>
                                            </div> 

                                        </div>
                                    </div>
                                    <?php
                                } else {
                                    ?>
                                    <!-- Modal -->
                                    <div class="modal fade" id="exampleModalCenter" tabindex="-1" role="dialog" aria-hidden="true">
                                        <div class="modal-dialog" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="exampleModalLongTitle">Invoice History</h5>
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick='window.location.href = "<?php echo base_url(); ?>"'>
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="form-group">
                                                        <label for="invoice_code" style="font-size:16px;">Enter code to view history</label>
                                                        <input type="text" class="form-control" id="invoice_code" autocomplete="off" autofocus placeholder="Enter Code" style="border: <?= (isset($error) && $error == 1 ? "1px solid red;" : "1px solid #ced4da;"); ?>" value="<?= (isset($code) ? $code : ""); ?>">
                                                        <p style="display: <?= (isset($error) && $error == 1 ? "" : "none"); ?>" class="email_error_message">Invalid Code.</p>
                                                    </div>
                                                    <button type="button" class="btn btn-primary" id="btn_invoice_code">Submit</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <?php
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <div class="modal fade" id="invoice_file_preview" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered invoice_preview_modal" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalCenterTitle">Invoice Preview</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div style="height: 80vh;" class="no_desktop">
                            <object data="" class="invoice_object_tag" frameborder="0" width="100%" height="100%" type="application/pdf">
                                <iframe class="preview_invoice_iframe" src="" frameborder="0" width="100%" height="100%"></iframe>
                            </object>
                        </div>
                        <div style="height: 80vh;" class="no_mob">
                            <iframe class="preview_invoice_iframe" src="" frameborder="0" width="100%" height="100%"></iframe>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <footer class="footer">
            <div class="footer_links_div">
                <a href="#" class="">Terms & Conditions</a>
                <p>&copy; <?php echo date("Y"); ?> EMRI Corporation</p>
            </div>
        </footer>
        <script src="<?php echo base_url(); ?>js/jquery.min.js"></script>
        <script src="<?php echo base_url(); ?>js/popper.min.js"></script>
        <script src="<?php echo base_url(); ?>js/bootstrap.min.js"></script>
        <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/bootbox.js/4.4.0/bootbox.min.js"></script>
        <script src="https://s3-us-west-2.amazonaws.com/s.cdpn.io/3/jquery.inputmask.bundle.js"></script>
        <script src="<?php echo base_url(); ?>js/moment-with-locales.min.js"></script>
        <script src="<?php echo base_url(); ?>js/bootstrap-datetimepicker.min.js"></script>
        <script src="<?php echo base_url(); ?>js/charge.js"></script>
        <script src="<?php echo base_url(); ?>js/bootstrap-datepicker.js"></script>
        <script src="<?php echo base_url(); ?>js/bootstrap-select.min.js"></script>
        <script src="<?php echo base_url(); ?>js/papaparse.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/bootbox.js/4.4.0/bootbox.min.js"></script>
        <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
        <script type="text/javascript">
<?php if (isset($error) && $error == 1) {
    ?>
                                                        $('#invoice_code').val(function (index, value) {
                                                            return value.replace(/\D/g, "").replace(/\B(?=(\d{3})+(?!\d))/g, "-");
                                                        });
    <?php
}
?>
                                                    $('#exampleModalCenter').modal({backdrop: 'static', keyboard: false});
                                                    $('#exampleModalCenter').modal('show');
                                                    $("#invoice_code").focusin();
                                                    $('#invoice_code').keyup(function (event) {
                                                        if (event.which >= 37 && event.which <= 40)
                                                            return;
                                                        $(this).val(function (index, value) {
                                                            return value.replace(/\D/g, "").replace(/\B(?=(\d{3})+(?!\d))/g, "-");
                                                        });
                                                    });
        </script>
    </body>
</html>