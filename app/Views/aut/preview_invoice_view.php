<div class="modal fade" id="invoice_preview" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered invoice_preview_modal" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalCenterTitle">Invoice Preview</h5>
                <?php
                if (count($charges_invoice_data) >= 1) {
                    ?>
                    <div class="col-md-4">
                        <div class="type_select_option">
                            <select class="form-control selectpicker" data-width="100%" title="Choose one of the email invoice" id="send_email_type">
                                <option value="Send from Server" selected>Send from Server</option>
                                <option value="Send from My Email">Send from My Email</option>
                            </select>
                        </div>
                    </div>
                <?php } ?>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="margin-left: 0px;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body invoice_table" id="invoice_main_table">
                <?php
                $download = $billable_labor = $service = $product = $reimbursement = $unbillable_labor = $unbillable_other = array();
                $product_total_bill = $qty_product = $billable_total_bill = $qty_billable = $service_total_bill = $qty_service = $blcount = $scount = $pcount = $rcount = $ulcount = $uocount = 0;
                $unbillable_other_total_bill = $qty_unbillable_other = $unbillable_labor_total_bill = $qty_unbillable_labor = $reimbursement_total_bill = $qty_reimbursement = 0;
                $dcount = $download_total_bill = 0;
                foreach ($charges_invoice_data as $charges_vaule) {
                    if ($charges_vaule["ct_id"] == "1") {
                        $billable_labor[] = $charges_vaule;

                        $billable_total_bill = $billable_total_bill + ($charges_vaule['quantity'] * $charges_vaule['rate']);
                    }
                    if ($charges_vaule["ct_id"] == "2") {
                        $service[] = $charges_vaule;

                        $service_total_bill = $service_total_bill + ($charges_vaule['quantity'] * $charges_vaule['rate']);
                    }
                    if ($charges_vaule["ct_id"] == "3") {
                        $product[] = $charges_vaule;

                        $product_total_bill = $product_total_bill + ($charges_vaule['quantity'] * $charges_vaule['rate']);
                    }
                    if ($charges_vaule["ct_id"] == "4") {
                        $reimbursement[] = $charges_vaule;

                        $reimbursement_total_bill = $reimbursement_total_bill + ($charges_vaule['quantity'] * $charges_vaule['rate']);
                    }
                    if ($charges_vaule["ct_id"] == "5") {
                        $unbillable_labor[] = $charges_vaule;

                        $unbillable_labor_total_bill = $unbillable_labor_total_bill + ($charges_vaule['quantity'] * $charges_vaule['rate']);
                    }
                    if ($charges_vaule["ct_id"] == "6") {
                        $unbillable_other[] = $charges_vaule;

                        $unbillable_other_total_bill = $unbillable_other_total_bill + ($charges_vaule['quantity'] * $charges_vaule['rate']);
                    }
                    if ($charges_vaule["ct_id"] == "7") {
                        $download[] = $charges_vaule;
                        $download_total_bill = $download_total_bill + $charges_vaule['amount'];
                    }
                }
                $total_current_chrges = $billable_total_bill + $service_total_bill + $download_total_bill + $product_total_bill + $reimbursement_total_bill + $unbillable_labor_total_bill + $unbillable_other_total_bill;
                if (isset($preview) && $preview == 1) {
                    if ($email_to_list == "") {
                        echo '<div class="position-relative">
                        <p class="bg_text_one" style="-webkit-transform: rotate(330deg);-moz-transform: rotate(330deg);-o-transform: rotate(330deg);transform: rotate(330deg);font-size: 40px;color: rgba(255, 5, 5, 0.30);position: absolute;padding-left: 10%;top: 185px;left: 125px;font-weight: 900;font-family: fantasy;margin-bottom: 0;">PREVIEW</p></div>';
                    } else {
                        echo '<div class="position-relative">
                        <p class="bg_text_one" style="-webkit-transform: rotate(330deg);-moz-transform: rotate(330deg);-o-transform: rotate(330deg);transform: rotate(330deg);font-size: 40px;color: rgba(255, 5, 5, 0.30);position: absolute;padding-left: 10%;top: 185px;left: 125px;font-weight: 900;font-family: fantasy;margin-bottom: 0;">PREVIEW</p>
                        <p class="bg_text_two" style="-webkit-transform: rotate(330deg);-moz-transform: rotate(330deg);-o-transform: rotate(330deg);transform: rotate(330deg);font-size: 16px;color: rgba(255, 5, 5, 0.20);position: absolute;padding-left: 10%;top: 231px;left: 163px;margin-bottom: 0;font-weight: bold;">Will Email to</p>
                        <p class="bg_text_three" style="-webkit-transform: rotate(330deg);-moz-transform: rotate(330deg);-o-transform: rotate(330deg);transform: rotate(330deg);font-size: 16px;color: rgba(255, 5, 5, 0.20);position: absolute;padding-left: 10%;top: 246px;left: 124px;margin-bottom: 0;font-weight: bold;">' . $email_to_list . '</p>
                    </div>';
                    }
                }
                ?>
                <table class="invoice_main_table" cellpadding="0" cellspacing="0" style="width: 860px;margin: 0 auto;text-align: left;background-color: #ffffff;color: #555;" >
                    <tr>
                        <td colspan="2" style="width: 50%;padding-top:5px;padding-right:5px;padding-left:5px;vertical-align: top;padding-bottom:5px;" >
                            <table style="width:100%;text-align:left;" >
                                <tr>
                                    <td class="title" style="padding-top:5px;padding-right:5px;padding-left:0px;vertical-align:top;padding-bottom:5px;color:#333;" >
                                        <?php
                                        if ($biller_data['logo_seed'] != 0) {
                                            $src = "https://billex.s3.amazonaws.com/biller_image/" . $biller_data['biller_id'] . "-" . base_convert($biller_data['biller_id'] + $biller_data['logo_seed'], 10, 32) . ".jpg";
                                            echo '<img src="' . $src . '" style="width:200px;padding-top:5px;max-width:100px;" >';
                                        } else {
                                            echo '<p style="font-size: 30px;margin-bottom: 0;">' . $biller_data['name'] . '</p>';
                                        }
                                        ?>

                                        <table style="width:100%;" >
                                            <tr>
                                                <td style="font-size: 12px;padding-top:-10px;padding-right:5px;padding-left:0px;vertical-align:top;padding-bottom:5px;text-align:left;font-weight:bold;color:#000000;" >
                                                    <?= $biller_data['address']; ?><br>
                                                    <?= $biller_data['city'] . ', ' . $biller_data['state'] . ' ' . $biller_data['zip']; ?>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="font-size: 12px;padding-top:5px;padding-right:5px;padding-left:0px;vertical-align:top;padding-bottom:5px;text-align:left;" >
                                                    <?php
                                                    echo ($biller_data['phone_support'] != "" || $biller_data['email_support'] != "" ? "<i><span>Questions?</span>" : "");
                                                    echo ($biller_data['phone_support'] != "" ? ' Call ' . $biller_data['phone_support'] : '');
                                                    echo ($biller_data['email_support'] != "" ? '<p class="email_support" style="padding-left: 70px;padding-top: 0px;margin-top: 0;margin-bottom: 0;">Email ' . $biller_data['email_support'] . '</p></i>' : "");
                                                    ?>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td colspan="2" style="padding-top:5px;padding-bottom:5px;padding-right:5px;padding-left:0px;vertical-align:top;" >
                                                    <table style="width:100%;" >
                                                        <tr>
                                                            <td style="padding-top:5px;padding-right:5px;padding-left:0px;vertical-align:top;padding-bottom:5px;text-align:left;font-size: 12.5pt;" >
                                                                <?= (isset($name) && $name != "" ? $name : ""); ?>
                                                                <?= (isset($address_mail_attention) && $address_mail_attention != "<br>" . $address_mail_attention ? "" : ""); ?>
                                                                <?= (isset($address_mail_street) && $address_mail_street != "" ? "<br>" . $address_mail_street : ""); ?><br>
                                                                <?= (isset($address_mail_city) && $address_mail_city != '' ? "\n" . $address_mail_city : "") . (isset($address_mail_state) && $address_mail_state != '' ? ", " . $address_mail_state : "") . (isset($address_mail_zip5) && $address_mail_zip5 != 0 ? " " . $address_mail_zip5 : "") ?>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td style="font-size: 12px;padding-top:5px;padding-left:-2px;padding-right:5px;vertical-align:top;padding-bottom:5px;text-align:left;font-weight:bold;color:#000000;" >
                                                                <?= (isset($po) && $po != "PO Number: " . $po ? "" : ""); ?>
                                                            </td>
                                                        </tr>
                                                    </table>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                    <td style="width: 50%;padding-top:5px;padding-right:5px;padding-left:5px;vertical-align:top;padding-bottom:5px;text-align:right;" >
                                        <table style="width:100%;" cellspacing="0">

                                            <tr>
                                                <td style="text-align:right;padding-top:5px;padding-right:5px;padding-left:5px;vertical-align:top;font-weight:bold;color:#000000;font-size:16px;" >
                                                    Invoice Date:<br>

                                                </td>
                                                <td style="text-align:right;padding-top:5px;padding-right:5px;padding-left:5px;vertical-align:top;font-weight:bold;color:#000000;font-size:16px;" >
                                                    <?= date('m/d/Y'); ?>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="text-align:right;padding-right: 5px;padding-top:-5px;padding-left:5px;font-weight:bold;color:#000000;font-size:12px;" >
                                                    Invoice Number:
                                                </td>
                                                <td style="text-align:right;padding-top:-5px;padding-right: 5px;padding-left:5px;font-weight:bold;color:#000000;font-size:12px;" class="invoice_number">
                                                    <span class="s_n_preview_old">Preview</span>
                                                    <span class="s_n_preview_new" style="display: none;"><?= $biller_data['invoice_number'] + 1; ?></span>
                                                </td>
                                            </tr>
                                            <tr><td></td></tr>
                                            <tr><td></td></tr>
                                            <tr>
                                                <td style="text-align:right;padding-top:-5px;padding-left:5px;padding-right: 5px;font-size:12px;" >
                                                    Current Charges:
                                                </td>
                                                <td style="text-align:right;padding-top:-5px;padding-left:5px;padding-right: 5px;font-size:12px;" >
                                                    +$<?= number_format($total_current_chrges, 2); ?>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="text-align:right;padding-top:5px;padding-right:5px;padding-left:5px;vertical-align:top;font-size:12px;" >
                                                    Previous Balance:<br>

                                                </td>
                                                <td style="text-align:right;padding-top:5px;padding-right:5px;padding-left:5px;vertical-align:top;font-size:12px;" >
                                                    <?= '$' . number_format($balance, 2); ?>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="text-align:right;padding:7px;border-width: 1px; border-style: solid; border-right-style: none;color: #000000;font-weight: bold;">Total Amount Due:</td>
                                                <td style="text-align:right;padding:7px;border-width: 1px; border-style: solid; border-left-style: none;color: #000000;font-weight: bold;">&nbsp;$<?= number_format($total_current_chrges, 2); ?></td>
                                            </tr>
                                            <tr>
                                                <td style="text-align:right;padding-left:5px;padding-right: 5px;font-size:12px;">Due date for current charges:</td>
                                                <td style="text-align:right;padding-left:5px;padding-right: 5px;font-size:12px;"><?= date('m/d/Y', strtotime("+15 day", strtotime(date('Y-m-d')))); ?></td>
                                            </tr>
                                            <tr><td></td></tr>
                                            <tr><td></td></tr>
                                            <tr>
                                                <td style="text-align: center;font-size: 12px;"><?= (isset($identifier) && $identifier != "Please include your account ID<br>with payment and remit to:" ? "" : ""); ?></td>
                                                <td colspan="2" style="padding-bottom: 10px;">
                                                    <?php
                                                    $result = dbQueryRows('stripe', array('biller_id' => $_SESSION['biller_id']));
                                                    if (count($result) != 0) {
                                                        ?>
                                                        <a href="<?php echo base_url() . "pay/" . (encodeHashids(decrypt(base64_decode($customer_id)))); ?>" class="make_paytment_link" style="float: right;color: #fff;background-color: #28a745;border-color: #28a745;text-decoration: underline;padding-left: 5px;padding-right: 5px;padding-top: 5px;padding-bottom: 5px;font-size: 13pt;border-radius: 4px;"><button class="make_paytment_link" style="border: none;background: no-repeat;text-decoration: underline;color: #fff !important;">Make Payment</button></a>
                                                        <?php
                                                    }
                                                    ?>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="text-align: center;padding-top:5px;padding-right:5px;padding-left:5px;vertical-align:top;padding-bottom:5px;font-weight: bold;color: #000000;font-size: 12px;" >
                                                    <?= $biller_data['name']; ?><br>
                                                    <?= ($biller_data['recipient_pay'] != "" ? $biller_data['recipient_pay'] . '<br>' : ""); ?>
                                                    <?= $biller_data['address_pay']; ?><br>
                                                    <?= $biller_data['city_pay'] . ', ' . $biller_data['state_pay'] . ' ' . $biller_data['zip_pay']; ?>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="text-align: center;padding-top:5px;padding-right:5px;vertical-align:top;padding-bottom:5px;font-weight:bold;color:#000000;" >
                                                    <?= (isset($identifier) && $identifier != "Account ID: " . $identifier ? "" : ""); ?>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr style="display: <?= (count($charges_invoice_data) >= 1 ? "" : "none"); ?>">
                        <td colspan="2" style="padding-left:5px;vertical-align: middle;">
                            <table style="width: 100%;" cellspacing="0">
                                <tbody>
                                    <tr>
                                        <td style="font-weight: bolder;color: #000000;font-size: 15px;vertical-align: middle;">Current Charges</td>
                                    </tr>
                                </tbody>
                            </table>
                        </td>
                    </tr>
                    <?php
                    if (count($charges_invoice_data) >= 1) {
                        ?>
                        <tr>
                            <td colspan="2" style="padding-top:5px;padding-bottom:5px;padding-right:5px;padding-left:5px;vertical-align: middle;">
                                <table style="width: 100%;" cellspacing="0">
                                    <tbody>
                                        <?php
                                        foreach ($billable_labor as $value) {
                                            if ($blcount == 0) {
                                                ?>
                                                <tr>
                                                    <td style="width: 15%;font-weight: bold;color: #000000;font-size: 12px;vertical-align: middle;"><?= $value['name']; ?></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                </tr>
                                                <tr>
                                                    <td style="padding-top:5px;padding-bottom:5px;padding-right:5px;padding-left:5px;vertical-align: middle;background-color:#eee;font-weight:bold;color: #000000;font-size: 12px;" >
                                                        Date
                                                    </td>
                                                    <td style="width: 100%;padding-top:5px;padding-bottom:5px;padding-right:5px;padding-left:5px;vertical-align: middle;background-color:#eee;font-weight:bold;color: #000000;font-size: 12px;" >
                                                        Description
                                                    </td>
                                                    <td style="padding-top:5px;padding-bottom:5px;padding-right:5px;padding-left:5px;vertical-align: middle;background-color:#eee;font-weight:bold;color: #000000;font-size: 12px;text-align: right;" >
                                                        Qty
                                                    </td>
                                                    <td style="padding-top:5px;padding-bottom:5px;padding-right:5px;padding-left:5px;vertical-align: middle;background-color:#eee;font-weight:bold;color: #000000;font-size: 12px;text-align: right;" >
                                                        Rate
                                                    </td>
                                                    <td style="padding-top:5px;padding-bottom:5px;padding-right:5px;padding-left:5px;vertical-align: middle;background-color:#eee;font-weight:bold;color: #000000;font-size: 12px;text-align: right;" >
                                                        Total
                                                    </td>
                                                </tr>
                                                <?php
                                            }
                                            ?>
                                            <tr>
                                                <td style="padding-top:5px;padding-bottom:5px;padding-right:5px;padding-left:5px;vertical-align: middle;font-size: 12px;border-style: solid;border-right-style: none;border-left-style: none;border-top-style: none;border-width: 1px;border-color: #eee;">
                                                    <?= date("m/d/Y", strtotime($value['date_charge'])); ?>
                                                </td>
                                                <td style="width: 100%;padding-top:5px;padding-bottom:5px;padding-right:5px;padding-left:5px;vertical-align: middle;font-size: 12px;border-style: solid;border-right-style: none;border-left-style: none;border-top-style: none;border-width: 1px;border-color: #eee;">
                                                    <?= $value['description']; ?>
                                                </td>
                                                <td style="padding-top:5px;padding-bottom:5px;padding-right:5px;padding-left:5px;vertical-align: middle;font-size: 12px;border-style: solid;border-right-style: none;border-left-style: none;text-align: right;border-top-style: none;border-width: 1px;border-color: #eee;">
                                                    <?= number_format($value['quantity'], 2); ?>
                                                </td>
                                                <td style="padding-top:5px;padding-bottom:5px;padding-right:5px;padding-left:5px;vertical-align: middle;font-size: 12px;border-style: solid;border-right-style: none;border-left-style: none;text-align: right;border-top-style: none;border-width: 1px;border-color: #eee;">
                                                    <?= '$' . number_format($value['rate'], 2); ?>
                                                </td>
                                                <td style="padding-top:5px;padding-bottom:5px;padding-right:5px;padding-left:5px;vertical-align: middle;font-size: 12px;border-style: solid;border-right-style: none;border-left-style: none;text-align: right;border-top-style: none;border-width: 1px;border-color: #eee;">
                                                    <?= '$' . number_format($value['quantity'] * $value['rate'], 2); ?>
                                                </td>
                                            </tr>
                                            <?php
                                            $blcount++;
                                            if (count($billable_labor) == $blcount) {
                                                ?>
                                                <tr>
                                                    <td></td>

                                                    <td style="font-weight: bold;padding: 7px;color: #000000;text-align: right;font-size: 12px;vertical-align: middle;">Total <?= $value['name']; ?></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td style="font-weight: bold; padding: 7px;color: #000000;text-align: right;font-size: 12px;vertical-align: middle;">$<?= number_format($billable_total_bill, 2); ?></td>
                                                </tr>
                                                <?php
                                            }
                                        }
                                        foreach ($service as $value) {
                                            if ($scount == 0) {
                                                ?>
                                                <tr>
                                                    <td style="width: 15%;font-weight: bold;color: #000000;font-size: 12px;vertical-align: middle;"><?= $value['name']; ?></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                </tr>
                                                <tr>
                                                    <td style="padding-top:5px;padding-bottom:5px;padding-right:5px;padding-left:5px;vertical-align: middle;background-color:#eee;font-weight:bold;color: #000000;font-size: 12px;" >
                                                        Date
                                                    </td>
                                                    <td style="width: 100%;padding-top:5px;padding-bottom:5px;padding-right:5px;padding-left:5px;vertical-align: middle;background-color:#eee;font-weight:bold;color: #000000;font-size: 12px;" >
                                                        Description
                                                    </td>
                                                    <td style="padding-top:5px;padding-bottom:5px;padding-right:5px;padding-left:5px;vertical-align: middle;background-color:#eee;font-weight:bold;color: #000000;text-align: right;font-size: 12px;" >
                                                        Qty
                                                    </td>
                                                    <td style="padding-top:5px;padding-bottom:5px;padding-right:5px;padding-left:5px;vertical-align: middle;background-color:#eee;font-weight:bold;color: #000000;text-align: right;font-size: 12px;" >
                                                        Rate
                                                    </td>
                                                    <td style="padding-top:5px;padding-bottom:5px;padding-right:5px;padding-left:5px;vertical-align: middle;background-color:#eee;font-weight:bold;color: #000000;text-align: right;font-size: 12px;" >
                                                        Total
                                                    </td>
                                                </tr>
                                                <?php
                                            }
                                            ?>
                                            <tr>
                                                <td style="padding-top:5px;padding-bottom:5px;padding-right:5px;padding-left:5px;vertical-align: middle;border-style: solid;border-right-style: none;font-size: 12px;border-left-style: none;border-top-style: none;border-width: 1px;border-color: #eee;">
                                                    <?= date("m/d/Y", strtotime($value['date_charge'])); ?>
                                                </td>
                                                <td style="width: 100%;padding-top:5px;padding-bottom:5px;padding-right:5px;padding-left:5px;vertical-align: middle;border-style: solid;border-right-style: none;font-size: 12px;border-left-style: none;border-top-style: none;border-width: 1px;border-color: #eee;">
                                                    <?= $value['description']; ?>
                                                </td>
                                                <td style="padding-top:5px;padding-bottom:5px;padding-right:5px;padding-left:5px;vertical-align: middle;border-style: solid;border-right-style: none;font-size: 12px;border-left-style: none;text-align: right;border-top-style: none;border-width: 1px;border-color: #eee;">
                                                    <?= number_format($value['quantity'], 2); ?>
                                                </td>
                                                <td style="padding-top:5px;padding-bottom:5px;padding-right:5px;padding-left:5px;vertical-align: middle;border-style: solid;border-right-style: none;font-size: 12px;border-left-style: none;text-align: right;border-top-style: none;border-width: 1px;border-color: #eee;">
                                                    <?= '$' . number_format($value['rate'], 2); ?>
                                                </td>
                                                <td style="padding-top:5px;padding-bottom:5px;padding-right:5px;padding-left:5px;vertical-align: middle;border-style: solid;border-right-style: none;font-size: 12px;border-left-style: none;text-align: right;border-top-style: none;border-width: 1px;border-color: #eee;">
                                                    <?= '$' . number_format($value['quantity'] * $value['rate'], 2); ?>
                                                </td>
                                            </tr>
                                            <?php
                                            $scount++;
                                            if (count($service) == $scount) {
                                                ?>
                                                <tr>
                                                    <td></td>

                                                    <td style="font-weight: bold;padding: 7px;color: #000000;text-align: right;font-size: 12px;vertical-align: middle;">Total <?= $value['name']; ?></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td style="font-weight: bold; padding: 7px;color: #000000;text-align: right;font-size: 12px;vertical-align: middle;">$<?= number_format($service_total_bill, 2); ?></td>
                                                </tr>
                                                <?php
                                            }
                                        }
                                        foreach ($product as $value) {
                                            if ($pcount == 0) {
                                                ?>
                                                <tr>
                                                    <td style="width: 15%;font-weight: bold;color: #000000;font-size: 12px;vertical-align: middle;"><?= $value['name']; ?></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                </tr>
                                                <tr>
                                                    <td style="padding-top:5px;padding-bottom:5px;padding-right:5px;padding-left:5px;vertical-align: middle;background-color:#eee;font-weight:bold;color: #000000;font-size: 12px;" >
                                                        Date
                                                    </td>
                                                    <td style="width: 100%;padding-top:5px;padding-bottom:5px;padding-right:5px;padding-left:5px;vertical-align: middle;background-color:#eee;font-weight:bold;color: #000000;font-size: 12px;" >
                                                        Description
                                                    </td>
                                                    <td style="padding-top:5px;padding-bottom:5px;padding-right:5px;padding-left:5px;vertical-align: middle;background-color:#eee;font-weight:bold;color: #000000;text-align: right;font-size: 12px;" >
                                                        Qty
                                                    </td>
                                                    <td style="padding-top:5px;padding-bottom:5px;padding-right:5px;padding-left:5px;vertical-align: middle;background-color:#eee;font-weight:bold;color: #000000;text-align: right;font-size: 12px;" >
                                                        Rate
                                                    </td>
                                                    <td style="padding-top:5px;padding-bottom:5px;padding-right:5px;padding-left:5px;vertical-align: middle;background-color:#eee;font-weight:bold;color: #000000;text-align: right;font-size: 12px;" >
                                                        Total
                                                    </td>
                                                </tr>
                                                <?php
                                            }
                                            ?>
                                            <tr>
                                                <td style="padding-top:5px;padding-bottom:5px;padding-right:5px;padding-left:5px;vertical-align: middle;border-style: solid;border-right-style: none;font-size: 12px;border-left-style: none;border-top-style: none;border-width: 1px;border-color: #eee;">
                                                    <?= date("m/d/Y", strtotime($value['date_charge'])); ?>
                                                </td>
                                                <td style="width: 100%;padding-top:5px;padding-bottom:5px;padding-right:5px;padding-left:5px;vertical-align: middle;border-style: solid;border-right-style: none;font-size: 12px;border-left-style: none;border-top-style: none;border-width: 1px;border-color: #eee;">
                                                    <?= $value['description']; ?>
                                                </td>
                                                <td style="padding-top:5px;padding-bottom:5px;padding-right:5px;padding-left:5px;vertical-align: middle;border-style: solid;border-right-style: none;font-size: 12px;border-left-style: none;text-align: right;border-top-style: none;border-width: 1px;border-color: #eee;">
                                                    <?= number_format($value['quantity'], 2); ?>
                                                </td>
                                                <td style="padding-top:5px;padding-bottom:5px;padding-right:5px;padding-left:5px;vertical-align: middle;border-style: solid;border-right-style: none;font-size: 12px;border-left-style: none;text-align: right;border-top-style: none;border-width: 1px;border-color: #eee;">
                                                    <?= '$' . number_format($value['rate'], 2); ?>
                                                </td>
                                                <td style="padding-top:5px;padding-bottom:5px;padding-right:5px;padding-left:5px;vertical-align: middle;border-style: solid;border-right-style: none;font-size: 12px;border-left-style: none;text-align: right;border-top-style: none;border-width: 1px;border-color: #eee;">
                                                    <?= '$' . number_format($value['quantity'] * $value['rate'], 2); ?>
                                                </td>
                                            </tr>
                                            <?php
                                            $pcount++;
                                            if (count($product) == $pcount) {
                                                ?>
                                                <tr>
                                                    <td></td>

                                                    <td style="font-weight: bold;padding: 7px;color: #000000;text-align: right;font-size: 12px;vertical-align: middle;">Total <?= $value['name']; ?></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td style="font-weight: bold; padding: 7px;color: #000000;text-align: right;font-size: 12px;vertical-align: middle;">$<?= number_format($product_total_bill, 2); ?></td>
                                                </tr>
                                                <?php
                                            }
                                        }
                                        foreach ($download as $value) {
                                            if ($dcount == 0) {
                                                ?>
                                                <tr>
                                                    <td style="width: 15%;font-weight: bold;color: #000000;font-size: 12px;vertical-align: middle;"><?= $value['name']; ?></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                </tr>
                                                <tr>
                                                    <td style="padding-top:5px;padding-bottom:5px;padding-right:5px;padding-left:5px;vertical-align: middle;background-color:#eee;font-weight:bold;color: #000000;font-size: 12px;" >
                                                        Date
                                                    </td>
                                                    <td style="width: 100%;padding-top:5px;padding-bottom:5px;padding-right:5px;padding-left:5px;vertical-align: middle;background-color:#eee;font-weight:bold;color: #000000;font-size: 12px;" >
                                                        Description
                                                    </td>
                                                    <td style="padding-top:5px;padding-bottom:5px;padding-right:5px;padding-left:5px;vertical-align: middle;background-color:#eee;font-weight:bold;color: #000000;text-align: right;font-size: 12px;" >
                                                        Qty
                                                    </td>
                                                    <td style="padding-top:5px;padding-bottom:5px;padding-right:5px;padding-left:5px;vertical-align: middle;background-color:#eee;font-weight:bold;color: #000000;text-align: right;font-size: 12px;" >
                                                        Rate
                                                    </td>
                                                    <td style="padding-top:5px;padding-bottom:5px;padding-right:5px;padding-left:5px;vertical-align: middle;background-color:#eee;font-weight:bold;color: #000000;text-align: right;font-size: 12px;" >
                                                        Total
                                                    </td>
                                                </tr>
                                                <?php
                                            }
                                            ?>
                                            <tr>
                                                <td style="padding-top:5px;padding-bottom:5px;padding-right:5px;padding-left:5px;vertical-align: middle;border-style: solid;border-right-style: none;font-size: 12px;border-left-style: none;border-top-style: none;border-width: 1px;border-color: #eee;">
                                                    <?= date("m/d/Y", strtotime($value['date_charge'])); ?>
                                                </td>
                                                <td style="width: 100%;padding-top:5px;padding-bottom:5px;padding-right:5px;padding-left:5px;vertical-align: middle;border-style: solid;border-right-style: none;font-size: 12px;border-left-style: none;border-top-style: none;border-width: 1px;border-color: #eee;">
                                                    <?= $value['description']; ?>
                                                </td>
                                                <td style="padding-top:5px;padding-bottom:5px;padding-right:5px;padding-left:5px;vertical-align: middle;border-style: solid;border-right-style: none;font-size: 12px;border-left-style: none;text-align: right;border-top-style: none;border-width: 1px;border-color: #eee;">
                                                    <?= number_format($value['quantity'], 2); ?>
                                                </td>
                                                <td style="padding-top:5px;padding-bottom:5px;padding-right:5px;padding-left:5px;vertical-align: middle;border-style: solid;border-right-style: none;font-size: 12px;border-left-style: none;text-align: right;border-top-style: none;border-width: 1px;border-color: #eee;">
                                                    <?= '$' . number_format($value['rate'], 2); ?>
                                                </td>
                                                <td style="padding-top:5px;padding-bottom:5px;padding-right:5px;padding-left:5px;vertical-align: middle;border-style: solid;border-right-style: none;font-size: 12px;border-left-style: none;text-align: right;border-top-style: none;border-width: 1px;border-color: #eee;">
                                                    <?= '$' . number_format($value['quantity'] * $value['rate'], 2); ?>
                                                </td>
                                            </tr>
                                            <?php
                                            $dcount++;
                                            if (count($download) == $dcount) {
                                                ?>
                                                <tr>
                                                    <td></td>
                                                    <td style="font-weight: bold;padding: 7px;color: #000000;text-align: right;font-size: 12px;vertical-align: middle;">Total <?= $value['name']; ?></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td style="font-weight: bold; padding: 7px;color: #000000;text-align: right;font-size: 12px;vertical-align: middle;">$<?= number_format($download_total_bill, 2); ?></td>
                                                </tr>
                                                <?php
                                            }
                                        }
                                        foreach ($reimbursement as $value) {
                                            if ($rcount == 0) {
                                                ?>
                                                <tr>
                                                    <td style="width: 15%;font-weight: bold;color: #000000;font-size: 12px;vertical-align: middle;"><?= $value['name']; ?></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                </tr>
                                                <tr>
                                                    <td style="padding-top:5px;padding-bottom:5px;padding-right:5px;padding-left:5px;vertical-align: middle;background-color:#eee;font-weight:bold;color: #000000;font-size: 12px;" >
                                                        Date
                                                    </td>
                                                    <td style="width: 100%;padding-top:5px;padding-bottom:5px;padding-right:5px;padding-left:5px;vertical-align: middle;background-color:#eee;font-weight:bold;color: #000000;font-size: 12px;" >
                                                        Description
                                                    </td>
                                                    <td style="padding-top:5px;padding-bottom:5px;padding-right:5px;padding-left:5px;vertical-align: middle;background-color:#eee;font-weight:bold;color: #000000;text-align: right;font-size: 12px;" >
                                                        Qty
                                                    </td>
                                                    <td style="padding-top:5px;padding-bottom:5px;padding-right:5px;padding-left:5px;vertical-align: middle;background-color:#eee;font-weight:bold;color: #000000;text-align: right;font-size: 12px;" >
                                                        Rate
                                                    </td>
                                                    <td style="padding-top:5px;padding-bottom:5px;padding-right:5px;padding-left:5px;vertical-align: middle;background-color:#eee;font-weight:bold;color: #000000;text-align: right;font-size: 12px;" >
                                                        Total
                                                    </td>
                                                </tr>
                                                <?php
                                            }
                                            ?>
                                            <tr>
                                                <td style="padding-top:5px;padding-bottom:5px;padding-right:5px;padding-left:5px;vertical-align: middle;border-style: solid;border-right-style: none;border-left-style: none;font-size: 12px;border-top-style: none;border-width: 1px;border-color: #eee;">
                                                    <?= date("m/d/Y", strtotime($value['date_charge'])); ?>
                                                </td>
                                                <td style="width: 100%;padding-top:5px;padding-bottom:5px;padding-right:5px;padding-left:5px;vertical-align: middle;border-style: solid;border-right-style: none;border-left-style: none;font-size: 12px;border-top-style: none;border-width: 1px;border-color: #eee;">
                                                    <?= $value['description']; ?>
                                                </td>
                                                <td style="padding-top:5px;padding-bottom:5px;padding-right:5px;padding-left:5px;vertical-align: middle;border-style: solid;border-right-style: none;border-left-style: none;font-size: 12px;text-align: right;border-top-style: none;border-width: 1px;border-color: #eee;">
                                                    <?= number_format($value['quantity'], 2); ?>
                                                </td>
                                                <td style="padding-top:5px;padding-bottom:5px;padding-right:5px;padding-left:5px;vertical-align: middle;border-style: solid;border-right-style: none;border-left-style: none;font-size: 12px;text-align: right;border-top-style: none;border-width: 1px;border-color: #eee;">
                                                    <?= '$' . number_format($value['rate'], 2); ?>
                                                </td>
                                                <td style="padding-top:5px;padding-bottom:5px;padding-right:5px;padding-left:5px;vertical-align: middle;border-style: solid;border-right-style: none;border-left-style: none;font-size: 12px;text-align: right;border-top-style: none;border-width: 1px;border-color: #eee;">
                                                    <?= '$' . number_format($value['quantity'] * $value['rate'], 2); ?>
                                                </td>
                                            </tr>
                                            <?php
                                            $rcount++;
                                            if (count($reimbursement) == $rcount) {
                                                ?>
                                                <tr>
                                                    <td></td>

                                                    <td style="font-weight: bold;padding: 7px;color: #000000;text-align: right;font-size: 12px;vertical-align: middle;">Total <?= $value['name']; ?></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td style="font-weight: bold; padding: 7px;color: #000000;text-align: right;font-size: 12px;vertical-align: middle;">$<?= number_format($reimbursement_total_bill, 2); ?></td>
                                                </tr>
                                                <?php
                                            }
                                        }
                                        foreach ($unbillable_labor as $value) {
                                            if ($ulcount == 0) {
                                                ?>
                                                <tr>
                                                    <td style="width: 15%;font-weight: bold;color: #000000;font-size: 12px;vertical-align: middle;"><?= $value['name']; ?></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                </tr>
                                                <tr>
                                                    <td style="padding-top:5px;padding-bottom:5px;padding-right:5px;padding-left:5px;vertical-align: middle;background-color:#eee;font-weight:bold;color: #000000;font-size: 12px;" >
                                                        Date
                                                    </td>
                                                    <td style="width: 100%;padding-top:5px;padding-bottom:5px;padding-right:5px;padding-left:5px;vertical-align: middle;background-color:#eee;font-weight:bold;color: #000000;font-size: 12px;" >
                                                        Description
                                                    </td>
                                                    <td style="padding-top:5px;padding-bottom:5px;padding-right:5px;padding-left:5px;vertical-align: middle;background-color:#eee;font-weight:bold;color: #000000;text-align: right;font-size: 12px;" >
                                                        Qty
                                                    </td>
                                                    <td style="padding-top:5px;padding-bottom:5px;padding-right:5px;padding-left:5px;vertical-align: middle;background-color:#eee;font-weight:bold;color: #000000;text-align: right;font-size: 12px;" >
                                                        Rate
                                                    </td>
                                                    <td style="padding-top:5px;padding-bottom:5px;padding-right:5px;padding-left:5px;vertical-align: middle;background-color:#eee;font-weight:bold;color: #000000;text-align: right;font-size: 12px;" >
                                                        Total
                                                    </td>
                                                </tr>
                                                <?php
                                            }
                                            ?>
                                            <tr>
                                                <td style="padding-top:5px;padding-bottom:5px;padding-right:5px;padding-left:5px;vertical-align: middle;border-style: solid;border-right-style: none;border-left-style: none;font-size: 12px;border-top-style: none;border-width: 1px;border-color: #eee;">
                                                    <?= date("m/d/Y", strtotime($value['date_charge'])); ?>
                                                </td>
                                                <td style="width: 100%;padding-top:5px;padding-bottom:5px;padding-right:5px;padding-left:5px;vertical-align: middle;border-style: solid;border-right-style: none;border-left-style: none;font-size: 12px;border-top-style: none;border-width: 1px;border-color: #eee;">
                                                    <?= $value['description']; ?>
                                                </td>
                                                <td style="padding-top:5px;padding-bottom:5px;padding-right:5px;padding-left:5px;vertical-align: middle;border-style: solid;border-right-style: none;border-left-style: none;font-size: 12px;text-align: right;border-top-style: none;border-width: 1px;border-color: #eee;">
                                                    <?= number_format($value['quantity'], 2); ?>
                                                </td>
                                                <td style="padding-top:5px;padding-bottom:5px;padding-right:5px;padding-left:5px;vertical-align: middle;border-style: solid;border-right-style: none;border-left-style: none;font-size: 12px;text-align: right;border-top-style: none;border-width: 1px;border-color: #eee;">
                                                    <?= '$' . number_format($value['rate'], 2); ?>
                                                </td>
                                                <td style="padding-top:5px;padding-bottom:5px;padding-right:5px;padding-left:5px;vertical-align: middle;border-style: solid;border-right-style: none;border-left-style: none;font-size: 12px;text-align: right;border-top-style: none;border-width: 1px;border-color: #eee;">
                                                    <?= '$' . number_format($value['quantity'] * $value['rate'], 2); ?>
                                                </td>
                                            </tr>
                                            <?php
                                            $ulcount++;
                                            if (count($unbillable_labor) == $ulcount) {
                                                ?>
                                                <tr>
                                                    <td></td>

                                                    <td style="font-weight: bold;padding: 7px;color: #000000;text-align: right;font-size: 12px;vertical-align: middle;">Total <?= $value['name']; ?></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td style="font-weight: bold; padding: 7px;color: #000000;text-align: right;font-size: 12px;vertical-align: middle;">$<?= number_format($unbillable_labor_total_bill, 2); ?></td>
                                                </tr>
                                                <?php
                                            }
                                        }
                                        foreach ($unbillable_other as $value) {
                                            if ($uocount == 0) {
                                                ?>
                                                <tr>
                                                    <td style="width: 15%;font-weight: bold;color: #000000;font-size: 12px;vertical-align: middle;"><?= $value['name']; ?></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                </tr>
                                                <tr>
                                                    <td style="padding-top:5px;padding-bottom:5px;padding-right:5px;padding-left:5px;vertical-align: middle;background-color:#eee;font-weight:bold;color: #000000;font-size: 12px;" >
                                                        Date
                                                    </td>
                                                    <td style="width: 100%;padding-top:5px;padding-bottom:5px;padding-right:5px;padding-left:5px;vertical-align: middle;background-color:#eee;font-weight:bold;color: #000000;font-size: 12px;" >
                                                        Description
                                                    </td>
                                                    <td style="padding-top:5px;padding-bottom:5px;padding-right:5px;padding-left:5px;vertical-align: middle;background-color:#eee;font-weight:bold;color: #000000;text-align: right;font-size: 12px;" >
                                                        Qty
                                                    </td>
                                                    <td style="padding-top:5px;padding-bottom:5px;padding-right:5px;padding-left:5px;vertical-align: middle;background-color:#eee;font-weight:bold;color: #000000;text-align: right;font-size: 12px;" >
                                                        Rate
                                                    </td>
                                                    <td style="padding-top:5px;padding-bottom:5px;padding-right:5px;padding-left:5px;vertical-align: middle;background-color:#eee;font-weight:bold;color: #000000;text-align: right;font-size: 12px;" >
                                                        Total
                                                    </td>
                                                </tr>
                                                <?php
                                            }
                                            ?>
                                            <tr>
                                                <td style="padding-top:5px;padding-bottom:5px;padding-right:5px;padding-left:5px;vertical-align: middle;border-style: solid;border-right-style: none;font-size: 12px;border-left-style: none;border-top-style: none;border-width: 1px;border-color: #eee;">
                                                    <?= date("m/d/Y", strtotime($value['date_charge'])); ?>
                                                </td>
                                                <td style="width: 100%;padding-top:5px;padding-bottom:5px;padding-right:5px;padding-left:5px;vertical-align: middle;border-style: solid;border-right-style: none;font-size: 12px;border-left-style: none;border-top-style: none;border-width: 1px;border-color: #eee;">
                                                    <?= $value['description']; ?>
                                                </td>
                                                <td style="padding-top:5px;padding-bottom:5px;padding-right:5px;padding-left:5px;vertical-align: middle;border-style: solid;border-right-style: none;font-size: 12px;border-left-style: none;text-align: right;border-top-style: none;border-width: 1px;border-color: #eee;">
                                                    <?= number_format($value['quantity'], 2); ?>
                                                </td>
                                                <td style="padding-top:5px;padding-bottom:5px;padding-right:5px;padding-left:5px;vertical-align: middle;border-style: solid;border-right-style: none;font-size: 12px;border-left-style: none;text-align: right;border-top-style: none;border-width: 1px;border-color: #eee;">
                                                    <?= '$' . number_format($value['rate'], 2); ?>
                                                </td>
                                                <td style="padding-top:5px;padding-bottom:5px;padding-right:5px;padding-left:5px;vertical-align: middle;border-style: solid;border-right-style: none;font-size: 12px;border-left-style: none;text-align: right;border-top-style: none;border-width: 1px;border-color: #eee;">
                                                    <?= '$' . number_format($value['quantity'] * $value['rate'], 2); ?>
                                                </td>
                                            </tr>
                                            <?php
                                            $uocount++;
                                            if (count($unbillable_other) == $uocount) {
                                                ?>
                                                <tr>
                                                    <td></td>

                                                    <td style="font-weight: bold;padding: 7px;color: #000000;text-align: right;font-size: 12px;vertical-align: middle;">Total <?= $value['name']; ?></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td style="font-weight: bold; padding: 7px;color: #000000;text-align: right;font-size: 12px;vertical-align: middle;">$<?= number_format($unbillable_other_total_bill, 2); ?></td>
                                                </tr>
                                                <?php
                                            }
                                        }
                                        ?>
                                        <tr>
                                            <td></td>
                                            <td style="font-weight: bold;padding: 7px;text-align: right;color: #000000;font-size: 12px;vertical-align: middle;">TOTAL CURRENT CHARGES</td>
                                            <td></td>
                                            <td></td>
                                            <td style="font-weight: bold; padding: 7px;text-align: right;color: #000000;font-size: 12px;vertical-align: middle;">$<?= number_format($total_current_chrges, 2); ?></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </td>
                        </tr>
                    <?php } ?>
                    <tr>
                        <td style="<?= (count($recent_charges) == 0 ? 'border-width: 1px; border-style: solid; border-right-style: none;border-color: #40926a;' : 'width:45%;border-right: 1px solid #40926a;border-width: 1px;border-style: solid;border-color: #40926a;'); ?>padding-top:5px;padding-bottom:5px;padding-left:5px;vertical-align: middle;">
                            <table style="width: 100%;" cellspacing="0">
                                <tbody>
                                    <tr>
                                        <td colspan="5" style="padding-top:0px;padding-bottom:5px;padding-right:5px;padding-left:5px;vertical-align: middle;<?= (count($recent_charges) == 0 ? 'padding:0px !important;' : ''); ?>">
                                            <table style="width: 100%;" cellspacing="0">
                                                <tbody>
                                                    <tr>
                                                        <td style="width: 17%;font-weight: bold;color: #000000;font-size: 12px;vertical-align: middle;"><?= (count($recent_charges) == 0 ? "<span style='color: grey;'>No Recent Payments</span>" : "Recent Payments Received"); ?></td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </td>
                                    </tr>
                                    <?php
                                    $rc = 0;
                                    foreach ($recent_charges as $value) {
                                        if ($rc == 0) {
                                            ?>
                                            <tr>
                                                <td style="padding-top:5px;padding-bottom:5px;padding-right:5px;padding-left:5px;vertical-align: middle;background-color:#eee;font-weight:bold;color: #000000;font-size: 12px;" >
                                                    Date
                                                </td>
                                                <td style="padding-top:5px;padding-bottom:5px;padding-right:5px;padding-left:5px;vertical-align: middle;background-color:#eee;font-weight:bold;color: #000000;font-size: 12px;" >
                                                    Type
                                                </td>
                                                <td style="padding-top:5px;padding-bottom:5px;padding-right:5px;padding-left:5px;vertical-align: middle;background-color:#eee;font-weight:bold;color: #000000;font-size: 12px;" >
                                                    Reference
                                                </td>
                                                <td style="width: 100%;padding-top:5px;padding-bottom:5px;padding-right:5px;padding-left:5px;vertical-align: middle;background-color:#eee;font-weight:bold;color: #000000;font-size: 12px;" >
                                                    Description
                                                </td>
                                                <td style="padding-top:5px;padding-bottom:5px;padding-right:5px;padding-left:5px;vertical-align: middle;background-color:#eee;font-weight:bold;color: #000000;font-size: 12px;text-align: right;" >
                                                    Amount
                                                </td>
                                            </tr>
                                            <?php
                                        }
                                        ?>
                                        <tr>
                                            <td style="padding-top:5px;padding-bottom:5px;padding-right:5px;padding-left:5px;vertical-align: middle;font-size: 12px;border-style: solid;border-right-style: none;border-left-style: none;border-top-style: none;border-width: 1px;border-color: #eee;">
                                                <?= date("m/d/Y", strtotime($value['date_credit'])); ?>
                                            </td>
                                            <td style="padding-top:5px;padding-bottom:5px;padding-right:5px;padding-left:5px;vertical-align: middle;font-size: 12px;border-style: solid;border-right-style: none;border-left-style: none;border-top-style: none;border-width: 1px;border-color: #eee;">
                                                <?= $value['type']; ?>
                                            </td>
                                            <td style="padding-top:5px;padding-bottom:5px;padding-right:5px;padding-left:5px;vertical-align: middle;font-size: 12px;border-style: solid;border-right-style: none;border-left-style: none;border-top-style: none;border-width: 1px;border-color: #eee;">
                                                <?= $value['reference']; ?>
                                            </td>
                                            <td style="padding-top:5px;padding-bottom:5px;padding-right:5px;padding-left:5px;vertical-align: middle;font-size: 12px;border-style: solid;border-right-style: none;border-left-style: none;border-top-style: none;border-width: 1px;border-color: #eee;width: 100%;">
                                                <?= $value['description']; ?>
                                            </td>
                                            <td style="padding-top:5px;padding-bottom:5px;padding-right:5px;padding-left:5px;vertical-align: middle;font-size: 12px;border-style: solid;border-right-style: none;border-left-style: none;text-align: right;border-top-style: none;border-width: 1px;border-color: #eee;">
                                                <?= '$' . number_format($value['amount'], 2); ?>
                                            </td>
                                        </tr>
                                        <?php
                                        $rc++;
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </td>
                        <td style="width:15%;padding-top:5px;padding-bottom:5px;padding-right:5px;padding-left:5px;vertical-align: middle;<?= (count($recent_charges) == 0 ? 'border-width: 1px; border-style: solid; border-left-style: none;border-color: #40926a;' : 'border-bottom: 1px solid;border-width: 1px;border-style: solid;border-color: #40926a;'); ?>">
                            <span <?= (count($recent_charges) == 0 ? 'style="display:none;"' : ''); ?>>View All History or visit billex.net/history and enter code <?= $invoice_code; ?>.</span>
                        </td>
                    </tr>
                    <tr><td colspan="2"><br></td></tr>
                    <tr>
                        <td style="padding-right: 5px;padding-top:5px;padding-left:5px;padding-bottom: 5px;font-weight:bold;font-size:9px;font-family: Arial, Helvetica, sans-serif;" >
                            <i class="s_n_preview_old">S/N: Preview</i>
                            <i class="s_n_preview_new" style="display: none;">S/N: <?= $invoice_data['invoice_id']; ?></i>
                        </td>
                    </tr>
                </table>
            </div>
            <?php
            $recent_payment = '';
            $mrecent_payment = '';
            if (count($recent_charges) != 0) {
                $rc = 0;
                foreach ($recent_charges as $value) {
                    if ($rc == 0) {
                        $recent_payment .= 'Date           Amount           Type
';
                        $mrecent_payment .='Date      Amount       Type
';
                    }
                    $amount = '$' . number_format($value['amount'], 2);
                    if (strlen($amount) < 10) {
                        $dif = 6 - intval(strlen($amount));
                        $add = '';
                        for ($i = 0; $i < $dif; $i++) {
                            $add.=' ';
                        }
                        $amount = $add . $amount;
                    }
                    $recent_payment .=date("m/d/y", strtotime($value['date_credit'])) . '    ' . $amount . '      ' . $value['type'] . '%0D';
                    $mrecent_payment .=date("m/d/y", strtotime($value['date_credit'])) . '         ' . $amount . '      ' . $value['type'] . '%0D';
                    $rc++;
                }
            }
            $body = '
VIEW COMPLETE INVOICE HERE
{link}

' . $biller_data['address'] . '
' . $biller_data['city'] . ', ' . $biller_data['state'] . ' ' . $biller_data['zip'] . '

Invoice Date:' . date('m/d/Y') . '
Invoice Number:' . ($biller_data['invoice_number'] + 1) . '
                    
Total Amount Due:$' . number_format($total_current_chrges, 2) . '
                    
Previous Balance:$' . number_format($balance, 2) . '
Current Charges:+$' . number_format($total_current_chrges, 2) . '

Recent Payments
' . $recent_payment . '
Please remit payment to:
' . (isset($name) && $name != "" ? $name : "") . (isset($address_mail_attention) && $address_mail_attention != "" . $address_mail_attention ? "" : "") . '
' . (isset($address_mail_street) && $address_mail_street != "" ? "" . $address_mail_street : "") . '
' . (isset($address_mail_city) && $address_mail_city != '' ? "" . $address_mail_city : "") . (isset($address_mail_state) && $address_mail_state != '' ? ", " . $address_mail_state : "") . (isset($address_mail_zip5) && $address_mail_zip5 != 0 ? " " . $address_mail_zip5 : "") . '
  
' . ($biller_data["phone_support"] != "" || $biller_data['email_support'] != "" ? "Questions?" : "") . ($biller_data['email_support'] != "" ? ' Email ' . $biller_data['email_support'] : "") . '
' . ($biller_data['phone_support'] != "" ? 'or Call ' . $biller_data['phone_support'] : '') . '';

            $mbody = "
VIEW COMPLETE INVOICE HERE
{link}

" . $biller_data['address'] . "
" . $biller_data['city'] . ", " . $biller_data['state'] . " " . $biller_data['zip'] . "

Invoice Date:" . date('m/d/Y') . "
Invoice Number:" . ($biller_data['invoice_number'] + 1) . "
                    
Total Amount Due:$" . number_format($total_current_chrges, 2) . "
                    
Previous Balance:$" . number_format($balance, 2) . "
Current Charges:+$" . number_format($total_current_chrges, 2) . "

Recent Payments
" . $recent_payment . "
    
Please remit payment to:
" . (isset($name) && $name != "" ? $name : "") . (isset($address_mail_attention) && $address_mail_attention != "" . $address_mail_attention ? "" : "") . "
" . (isset($address_mail_street) && $address_mail_street != "" ? "" . $address_mail_street : "") . "
" . (isset($address_mail_city) && $address_mail_city != '' ? "" . $address_mail_city : "") . (isset($address_mail_state) && $address_mail_state != '' ? ", " . $address_mail_state : "") . (isset($address_mail_zip5) && $address_mail_zip5 != 0 ? " " . $address_mail_zip5 : "") . "
    
" . ($biller_data["phone_support"] != "" || $biller_data['email_support'] != "" ? "Questions?" : "") . ($biller_data['email_support'] != "" ? ' Email ' . $biller_data['email_support'] : "") . "
" . ($biller_data['phone_support'] != "" ? 'or Call ' . $biller_data['phone_support'] : '') . "";
            $body = urlencode($body);
            $mbody = rawurlencode($mbody);
            $mbody = str_replace('%0D%0A', '%3C%2Fbr%3E', $mbody);
            $mbody = str_replace('%250D', '%3C%2Fbr%3E', $mbody);
//            $mbody = str_replace('%20', '%2d', $mbody);
            $body = str_replace('%250D', '%0D', $body);
            $body = str_replace('%7Blink%7D', '{link}', $body);
            $mbody = str_replace(array('%7Blink%7D'), array('{link}'), $mbody);
            ?>
            <a id="thickboxId" href="mailto:<?= $email_to_list; ?>?subject=Invoice from <?= $_SESSION['display_name']; ?>&body=<?= $mbody; ?>" class="thickbox" style="display: none;"></a>
            <input type="hidden" class="memail_body" value="mailto:<?= $email_to_list; ?>?subject=Invoice from <?= $_SESSION['display_name']; ?>&body=<?= $mbody; ?>">
            <input type="hidden" class="email_body" value="mailto:<?= $email_to_list; ?>?subject=Invoice from <?= $_SESSION['display_name']; ?>&body=<?= $body; ?>">
            <div class="modal-footer flex-wrap">
                <input type="hidden" value="<?= count($charges_invoice_data); ?>" id="charge_count">
                <?php
                if ($email_to_list != "") {
                    ?>
                    <p class="mb-0">
                        <input type="hidden" class="form-control tooltipped" id="send_invoice_email" value="<?= $email_to_list; ?>">
                        Email to: <?= $email_to_list; ?>
                    <div class="" id="btn_send_invoice_div" role="group" aria-label="Basic example">
                        <button type="button" class="btn btn-send_invoice" id="send_invoice" data-toggle="tooltip" data-placement="top" title="Send Invoice From Server"><i class="fas fa-at"></i><i class="fas fa-spinner fa-spin edit_customer_loder" style="display: none;"></i></button>
                        <a href="JavaScript:void(0);" class="btn btn-send_invoice" id="send_invoice_my_email" data-toggle="tooltip" data-placement="top" title="Send Invoice From My Email" style="display: none;"><i class="fas fa-at"></i><i class="fas fa-spinner fa-spin edit_customer_loder" style="display: none;"></i></a>
                        <button type="button" class="btn btn-send_invoice" id="print_invoice" onclick="printTrigger('invoice_main_table');" data-toggle="tooltip" data-placement="top" title="Print Invoice"><i class="fas fa-print"></i><i class="fas fa-spinner fa-spin edit_customer_loder" style="display: none;"></i></button>
                        <button type="button" class="btn btn-send_invoice" id="send_print_invoice" data-toggle="tooltip" data-placement="top" title="Print & Send Invoice" style="display: <?= (!isset($preview) ? "" : "none;") ?>"><i class="fas fa-print"></i> & <i class="fas fa-at"></i><i class="fas fa-spinner fa-spin edit_customer_loder" style="display: none;"></i></button>
                        <button type="button" class="btn btn-send_invoice" id="mail_usps" data-toggle="tooltip" data-placement="top" title="Mail via USPS"><i class="fas fa-paper-plane"></i><i class="fas fa-spinner fa-spin edit_customer_loder" style="display: none;"></i></button>
                    </div>
                    </p>
                    <?php
                } else {
                    ?>
                    <p>
                    <div class="row mr-0 ml-0">
                        <label class="col-sm-4 mb-0 pr-0" style="margin-top: 7px;font-size: 16px;">Email to</label>
                        <div class="col-sm-8 pl-0">
                            <input type="text" class="form-control tooltipped" id="<?= (count($charges_invoice_data) == 0 ? "send_invoice_email_blank" : "send_invoice_email") ?>">
                        </div>
                    </div>
                    <div class="" id="btn_send_invoice_div" role="group" aria-label="Basic example">
                        <button type="button" class="btn btn-send_invoice" id="print_invoice" onclick="printTrigger('invoice_main_table');" data-toggle="tooltip" data-placement="top" title="Print Invoice"><i class="fas fa-print"></i><i class="fas fa-spinner fa-spin edit_customer_loder" style="display: none;"></i></button>
                        <button type="button" class="btn btn-send_invoice" disabled="" id="send_invoice" data-toggle="tooltip" data-placement="top" title="Send Invoice From Server"><i class="fas fa-at"></i><i class="fas fa-spinner fa-spin edit_customer_loder" style="display: none;"></i></button>
                        <a href="JavaScript:void(0);" disabled="" class="btn btn-send_invoice" id="send_invoice_my_email" data-toggle="tooltip" data-placement="top" title="Send Invoice From My Email" style="display: none;"><i class="fas fa-at"></i><i class="fas fa-spinner fa-spin edit_customer_loder" style="display: none;"></i></a>
                        <button type="button" class="btn btn-send_invoice" disabled="" id="send_print_invoice" data-toggle="tooltip" data-placement="top" title="Print & Send Invoice" style="display: <?= (!isset($preview) ? "" : "none;") ?>"><i class="fas fa-print"></i> & <i class="fas fa-at"></i><i class="fas fa-spinner fa-spin edit_customer_loder" style="display: none;"></i></button>
                        <button type="button" class="btn btn-send_invoice" id="mail_usps" data-toggle="tooltip" data-placement="top" title="Mail via USPS"><i class="fas fa-paper-plane"></i><i class="fas fa-spinner fa-spin edit_customer_loder" style="display: none;"></i></button>
                    </div>
                    </p>
                    <?php
                }
                ?>
            </div>
        </div>
    </div>
</div>