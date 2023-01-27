<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    </head>
    <body onafterprint="parent.location.reload();">
        <?php
        foreach ($customer_data as $key => $value) {
            $download = $billable_labor = $service = $product = $reimbursement = $unbillable_labor = $unbillable_other = array();
            $product_total_bill = $qty_product = $billable_total_bill = $qty_billable = $service_total_bill = $qty_service = $blcount = $scount = $pcount = $rcount = $ulcount = $uocount = 0;
            $unbillable_other_total_bill = $qty_unbillable_other = $unbillable_labor_total_bill = $qty_unbillable_labor = $reimbursement_total_bill = $qty_reimbursement = 0;
            $dcount = $download_total_bill = 0;
            foreach ($value['charges'] as $charges_vaule) {
                if ($charges_vaule["ct_id"] == "1") {
                    $billable_labor[] = $charges_vaule;
                    $billable_total_bill = $billable_total_bill + $charges_vaule['amount'];
                }
                if ($charges_vaule["ct_id"] == "2") {
                    $service[] = $charges_vaule;
                    $service_total_bill = $service_total_bill + $charges_vaule['amount'];
                }
                if ($charges_vaule["ct_id"] == "3") {
                    $product[] = $charges_vaule;
                    $product_total_bill = $product_total_bill + $charges_vaule['amount'];
                }
                if ($charges_vaule["ct_id"] == "4") {
                    $reimbursement[] = $charges_vaule;
                    $reimbursement_total_bill = $reimbursement_total_bill + $charges_vaule['amount'];
                }
                if ($charges_vaule["ct_id"] == "5") {
                    $unbillable_labor[] = $charges_vaule;
                    $unbillable_labor_total_bill = $unbillable_labor_total_bill + $charges_vaule['amount'];
                }
                if ($charges_vaule["ct_id"] == "6") {
                    $unbillable_other[] = $charges_vaule;
                    $unbillable_other_total_bill = $unbillable_other_total_bill + $charges_vaule['amount'];
                }
                if ($charges_vaule["ct_id"] == "7") {
                    $download[] = $charges_vaule;
                    $download_total_bill = $download_total_bill + $charges_vaule['amount'];
                }
            }
            $total_current_chrges = $billable_total_bill + $service_total_bill + $product_total_bill + $download_total_bill + $reimbursement_total_bill + $unbillable_labor_total_bill + $unbillable_other_total_bill;
            ?>
            <table class="invoice_main_table table_border" cellpadding="0" cellspacing="0" style="width: 100%;text-align: left;background-color: #ffffff;color: #555;" >
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
                                                echo ($biller_data['email_support'] != "" ? '<p style="padding-left: 60px;padding-top: 0px;margin-top: 0;margin-bottom: 0;">Email ' . $biller_data['email_support'] . '</p></i>' : "");
                                                ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="2" style="padding-top:5px;padding-bottom:5px;padding-right:5px;padding-left:0px;vertical-align:top;" >
                                                <table style="width:100%;" >
                                                    <tr>
                                                        <td style="padding-top:5px;padding-right:5px;padding-left:0px;vertical-align:top;padding-bottom:5px;text-align:left;font-size: 12.5pt;" >
                                                            <?= (isset($value['name']) && $value['name'] != "" ? $value['name'] : ""); ?>
                                                            <?= (isset($value['address_mail_attention']) && $value['address_mail_attention'] != "<br>" . $value['address_mail_attention'] ? "" : ""); ?>
                                                            <?= (isset($value['address_mail_street']) && $value['address_mail_street'] != "" ? "<br>" . $value['address_mail_street'] : ""); ?><br>
                                                            <?= (isset($value['address_mail_city']) && $value['address_mail_city'] != '' ? "\n" . $value['address_mail_city'] : "") . (isset($value['address_mail_state']) && $value['address_mail_state'] != '' ? ", " . $value['address_mail_state'] : "") . (isset($value['address_mail_zip5']) && $value['address_mail_zip5'] != 0 ? " " . $value['address_mail_zip5'] : "") ?>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td style="font-size: 12px;padding-top:5px;padding-left:-2px;padding-right:5px;vertical-align:top;padding-bottom:5px;text-align:left;font-weight:bold;color:#000000;" >
                                                            <?= (isset($value['po']) && $value['po'] != "PO Number: " . $value['po'] ? "" : ""); ?>
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
                                            <td style="text-align:right;padding-right: 5px;padding-top:5px;padding-left:5px;font-weight:bold;color:#000000;font-size:12px;" >
                                                Invoice Number:
                                            </td>
                                            <td style="text-align:right;padding-top:5px;padding-right: 5px;padding-left:5px;font-weight:bold;color:#000000;font-size:12px;" class="invoice_number">
                                                <?= $biller_data['invoice_number']; ?>
                                            </td>
                                        </tr>
                                        <tr><td></td></tr>
                                        <tr><td></td></tr>
                                        <tr>
                                            <td style="text-align:right;padding-top:2px;padding-left:5px;padding-right: 5px;font-size:12px;" >
                                                Current Charges:
                                            </td>
                                            <td style="text-align:right;padding-top:2px;padding-left:5px;padding-right: 5px;font-size:12px;" >
                                                +$<?= number_format($total_current_chrges, 2); ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="text-align:right;padding-top:5px;padding-right:5px;padding-left:5px;vertical-align:top;font-size:12px;" >
                                                Previous Balance:<br>
                                            </td>
                                            <td style="text-align:right;padding-top:5px;padding-right:5px;padding-left:5px;vertical-align:top;font-size:12px;" >
                                                <?= '$' . number_format($value['balance'], 2); ?>
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
                                            <td style="text-align: center;font-size: 12px;"><?= (isset($value['identifier']) && $value['identifier'] != "Please include your account ID<br>with payment and remit to:" ? "" : ""); ?></td>
                                            <td style="padding-bottom: 5px;">
                                                <?php
                                                $result = array();
                                                $result = dbQueryRows('stripe', array('biller_id' => $_SESSION['biller_id']));
                                                if (count($result) != 0) {
                                                    ?>
                                                    <a href="<?php echo base_url() . "pay/" . (encodeHashids($value['customer_id'])); ?>" class="make_paytment_link" style="float: right;color: #fff;background-color: #28a745;border-color: #28a745;text-decoration: underline;padding-left: 5px;padding-right: 5px;padding-top: 5px;padding-bottom: 5px;font-size: 13pt;border-radius: 4px;">Make Payment</a>
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
                                                <?= (isset($value['identifier']) && $value['identifier'] != "Account ID: " . $value['identifier'] ? "" : ""); ?>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr style="display: <?= (count($value['charges']) >= 1 ? "" : "none"); ?>">
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
                if (count($value['charges']) >= 1) {
                    ?>
                    <tr>
                        <td colspan="2" style="padding-top:5px;padding-bottom:5px;padding-right:5px;padding-left:5px;vertical-align: middle;">
                            <table style="width: 100%;" cellspacing="0">
                                <tbody>
                                    <?php
                                    foreach ($billable_labor as $cvalue) {
                                        if ($blcount == 0) {
                                            ?>
                                            <tr>
                                                <td style="width: 15%;font-weight: bold;color: #000000;font-size: 12px;vertical-align: middle;"><?= $cvalue['name']; ?></td>
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
                                                <?= date("m/d/Y", strtotime($cvalue['date_charge'])); ?>
                                            </td>
                                            <td style="width: 100%;padding-top:5px;padding-bottom:5px;padding-right:5px;padding-left:5px;vertical-align: middle;font-size: 12px;border-style: solid;border-right-style: none;border-left-style: none;border-top-style: none;border-width: 1px;border-color: #eee;">
                                                <?= $cvalue['description']; ?>
                                            </td>
                                            <td style="padding-top:5px;padding-bottom:5px;padding-right:5px;padding-left:5px;vertical-align: middle;font-size: 12px;border-style: solid;border-right-style: none;border-left-style: none;text-align: right;border-top-style: none;border-width: 1px;border-color: #eee;">
                                                <?= number_format($cvalue['quantity'], 2); ?>
                                            </td>
                                            <td style="padding-top:5px;padding-bottom:5px;padding-right:5px;padding-left:5px;vertical-align: middle;font-size: 12px;border-style: solid;border-right-style: none;border-left-style: none;text-align: right;border-top-style: none;border-width: 1px;border-color: #eee;">
                                                <?= '$' . number_format($cvalue['rate'], 2); ?>
                                            </td>
                                            <td style="padding-top:5px;padding-bottom:5px;padding-right:5px;padding-left:5px;vertical-align: middle;font-size: 12px;border-style: solid;border-right-style: none;border-left-style: none;text-align: right;border-top-style: none;border-width: 1px;border-color: #eee;">
                                                <?= '$' . number_format($cvalue['amount'], 2); ?>
                                            </td>
                                        </tr>
                                        <?php
                                        $blcount++;
                                        if (count($billable_labor) == $blcount) {
                                            ?>
                                            <tr>
                                                <td></td>

                                                <td style="font-weight: bold;padding: 7px;color: #000000;text-align: right;font-size: 12px;vertical-align: middle;">Total <?= $cvalue['name']; ?></td>
                                                <td></td>
                                                <td></td>
                                                <td style="font-weight: bold; padding: 7px;color: #000000;text-align: right;font-size: 12px;vertical-align: middle;">$<?= number_format($billable_total_bill, 2); ?></td>
                                            </tr>
                                            <?php
                                        }
                                    }
                                    foreach ($service as $cvalue) {
                                        if ($scount == 0) {
                                            ?>
                                            <tr>
                                                <td style="width: 15%;font-weight: bold;color: #000000;font-size: 12px;vertical-align: middle;"><?= $cvalue['name']; ?></td>
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
                                                <?= date("m/d/Y", strtotime($cvalue['date_charge'])); ?>
                                            </td>
                                            <td style="width: 100%;padding-top:5px;padding-bottom:5px;padding-right:5px;padding-left:5px;vertical-align: middle;border-style: solid;border-right-style: none;font-size: 12px;border-left-style: none;border-top-style: none;border-width: 1px;border-color: #eee;">
                                                <?= $cvalue['description']; ?>
                                            </td>
                                            <td style="padding-top:5px;padding-bottom:5px;padding-right:5px;padding-left:5px;vertical-align: middle;border-style: solid;border-right-style: none;font-size: 12px;border-left-style: none;text-align: right;border-top-style: none;border-width: 1px;border-color: #eee;">
                                                <?= number_format($cvalue['quantity'], 2); ?>
                                            </td>
                                            <td style="padding-top:5px;padding-bottom:5px;padding-right:5px;padding-left:5px;vertical-align: middle;border-style: solid;border-right-style: none;font-size: 12px;border-left-style: none;text-align: right;border-top-style: none;border-width: 1px;border-color: #eee;">
                                                <?= '$' . number_format($cvalue['rate'], 2); ?>
                                            </td>
                                            <td style="padding-top:5px;padding-bottom:5px;padding-right:5px;padding-left:5px;vertical-align: middle;border-style: solid;border-right-style: none;font-size: 12px;border-left-style: none;text-align: right;border-top-style: none;border-width: 1px;border-color: #eee;">
                                                <?= '$' . number_format($cvalue['amount'], 2); ?>
                                            </td>
                                        </tr>
                                        <?php
                                        $scount++;
                                        if (count($service) == $scount) {
                                            ?>
                                            <tr>
                                                <td></td>

                                                <td style="font-weight: bold;padding: 7px;color: #000000;text-align: right;font-size: 12px;vertical-align: middle;">Total <?= $cvalue['name']; ?></td>
                                                <td></td>
                                                <td></td>
                                                <td style="font-weight: bold; padding: 7px;color: #000000;text-align: right;font-size: 12px;vertical-align: middle;">$<?= number_format($service_total_bill, 2); ?></td>
                                            </tr>
                                            <?php
                                        }
                                    }
                                    foreach ($product as $cvalue) {
                                        if ($pcount == 0) {
                                            ?>
                                            <tr>
                                                <td style="width: 15%;font-weight: bold;color: #000000;font-size: 12px;vertical-align: middle;"><?= $cvalue['name']; ?></td>
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
                                                <?= date("m/d/Y", strtotime($cvalue['date_charge'])); ?>
                                            </td>
                                            <td style="width: 100%;padding-top:5px;padding-bottom:5px;padding-right:5px;padding-left:5px;vertical-align: middle;border-style: solid;border-right-style: none;font-size: 12px;border-left-style: none;border-top-style: none;border-width: 1px;border-color: #eee;">
                                                <?= $cvalue['description']; ?>
                                            </td>
                                            <td style="padding-top:5px;padding-bottom:5px;padding-right:5px;padding-left:5px;vertical-align: middle;border-style: solid;border-right-style: none;font-size: 12px;border-left-style: none;text-align: right;border-top-style: none;border-width: 1px;border-color: #eee;">
                                                <?= number_format($cvalue['quantity'], 2); ?>
                                            </td>
                                            <td style="padding-top:5px;padding-bottom:5px;padding-right:5px;padding-left:5px;vertical-align: middle;border-style: solid;border-right-style: none;font-size: 12px;border-left-style: none;text-align: right;border-top-style: none;border-width: 1px;border-color: #eee;">
                                                <?= '$' . number_format($cvalue['rate'], 2); ?>
                                            </td>
                                            <td style="padding-top:5px;padding-bottom:5px;padding-right:5px;padding-left:5px;vertical-align: middle;border-style: solid;border-right-style: none;font-size: 12px;border-left-style: none;text-align: right;border-top-style: none;border-width: 1px;border-color: #eee;">
                                                <?= '$' . number_format($cvalue['amount'], 2); ?>
                                            </td>
                                        </tr>
                                        <?php
                                        $pcount++;
                                        if (count($product) == $pcount) {
                                            ?>
                                            <tr>
                                                <td></td>

                                                <td style="font-weight: bold;padding: 7px;color: #000000;text-align: right;font-size: 12px;vertical-align: middle;">Total <?= $cvalue['name']; ?></td>
                                                <td></td>
                                                <td></td>
                                                <td style="font-weight: bold; padding: 7px;color: #000000;text-align: right;font-size: 12px;vertical-align: middle;">$<?= number_format($product_total_bill, 2); ?></td>
                                            </tr>
                                            <?php
                                        }
                                    }
                                    foreach ($download as $cvalue) {
                                        if ($dcount == 0) {
                                            ?>
                                            <tr>
                                                <td style="width: 15%;font-weight: bold;color: #000000;font-size: 12px;vertical-align: middle;"><?= $cvalue['name']; ?></td>
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
                                                <?= date("m/d/Y", strtotime($cvalue['date_charge'])); ?>
                                            </td>
                                            <td style="width: 100%;padding-top:5px;padding-bottom:5px;padding-right:5px;padding-left:5px;vertical-align: middle;border-style: solid;border-right-style: none;font-size: 12px;border-left-style: none;border-top-style: none;border-width: 1px;border-color: #eee;">
                                                <?= $cvalue['description']; ?>
                                            </td>
                                            <td style="padding-top:5px;padding-bottom:5px;padding-right:5px;padding-left:5px;vertical-align: middle;border-style: solid;border-right-style: none;font-size: 12px;border-left-style: none;text-align: right;border-top-style: none;border-width: 1px;border-color: #eee;">
                                                <?= number_format($cvalue['quantity'], 2); ?>
                                            </td>
                                            <td style="padding-top:5px;padding-bottom:5px;padding-right:5px;padding-left:5px;vertical-align: middle;border-style: solid;border-right-style: none;font-size: 12px;border-left-style: none;text-align: right;border-top-style: none;border-width: 1px;border-color: #eee;">
                                                <?= '$' . number_format($cvalue['rate'], 2); ?>
                                            </td>
                                            <td style="padding-top:5px;padding-bottom:5px;padding-right:5px;padding-left:5px;vertical-align: middle;border-style: solid;border-right-style: none;font-size: 12px;border-left-style: none;text-align: right;border-top-style: none;border-width: 1px;border-color: #eee;">
                                                <?= '$' . number_format($cvalue['quantity'] * $cvalue['rate'], 2); ?>
                                            </td>
                                        </tr>
                                        <?php
                                        $dcount++;
                                        if (count($download) == $dcount) {
                                            ?>
                                            <tr>
                                                <td></td>
                                                <td style="font-weight: bold;padding: 7px;color: #000000;text-align: right;font-size: 12px;vertical-align: middle;">Total <?= $cvalue['name']; ?></td>
                                                <td></td>
                                                <td></td>
                                                <td style="font-weight: bold; padding: 7px;color: #000000;text-align: right;font-size: 12px;vertical-align: middle;">$<?= number_format($download_total_bill, 2); ?></td>
                                            </tr>
                                            <?php
                                        }
                                    }
                                    foreach ($reimbursement as $cvalue) {
                                        if ($rcount == 0) {
                                            ?>
                                            <tr>
                                                <td style="width: 15%;font-weight: bold;color: #000000;font-size: 12px;vertical-align: middle;"><?= $cvalue['name']; ?></td>
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
                                                <?= date("m/d/Y", strtotime($cvalue['date_charge'])); ?>
                                            </td>
                                            <td style="width: 100%;padding-top:5px;padding-bottom:5px;padding-right:5px;padding-left:5px;vertical-align: middle;border-style: solid;border-right-style: none;border-left-style: none;font-size: 12px;border-top-style: none;border-width: 1px;border-color: #eee;">
                                                <?= $cvalue['description']; ?>
                                            </td>
                                            <td style="padding-top:5px;padding-bottom:5px;padding-right:5px;padding-left:5px;vertical-align: middle;border-style: solid;border-right-style: none;border-left-style: none;font-size: 12px;text-align: right;border-top-style: none;border-width: 1px;border-color: #eee;">
                                                <?= number_format($cvalue['quantity'], 2); ?>
                                            </td>
                                            <td style="padding-top:5px;padding-bottom:5px;padding-right:5px;padding-left:5px;vertical-align: middle;border-style: solid;border-right-style: none;border-left-style: none;font-size: 12px;text-align: right;border-top-style: none;border-width: 1px;border-color: #eee;">
                                                <?= '$' . number_format($cvalue['rate'], 2); ?>
                                            </td>
                                            <td style="padding-top:5px;padding-bottom:5px;padding-right:5px;padding-left:5px;vertical-align: middle;border-style: solid;border-right-style: none;border-left-style: none;font-size: 12px;text-align: right;border-top-style: none;border-width: 1px;border-color: #eee;">
                                                <?= '$' . number_format($cvalue['amount'], 2); ?>
                                            </td>
                                        </tr>
                                        <?php
                                        $rcount++;
                                        if (count($reimbursement) == $rcount) {
                                            ?>
                                            <tr>
                                                <td></td>

                                                <td style="font-weight: bold;padding: 7px;color: #000000;text-align: right;font-size: 12px;vertical-align: middle;">Total <?= $cvalue['name']; ?></td>
                                                <td></td>
                                                <td></td>
                                                <td style="font-weight: bold; padding: 7px;color: #000000;text-align: right;font-size: 12px;vertical-align: middle;">$<?= number_format($reimbursement_total_bill, 2); ?></td>
                                            </tr>
                                            <?php
                                        }
                                    }
                                    foreach ($unbillable_labor as $cvalue) {
                                        if ($ulcount == 0) {
                                            ?>
                                            <tr>
                                                <td style="width: 15%;font-weight: bold;color: #000000;font-size: 12px;vertical-align: middle;"><?= $cvalue['name']; ?></td>
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
                                                <?= date("m/d/Y", strtotime($cvalue['date_charge'])); ?>
                                            </td>
                                            <td style="width: 100%;padding-top:5px;padding-bottom:5px;padding-right:5px;padding-left:5px;vertical-align: middle;border-style: solid;border-right-style: none;border-left-style: none;font-size: 12px;border-top-style: none;border-width: 1px;border-color: #eee;">
                                                <?= $cvalue['description']; ?>
                                            </td>
                                            <td style="padding-top:5px;padding-bottom:5px;padding-right:5px;padding-left:5px;vertical-align: middle;border-style: solid;border-right-style: none;border-left-style: none;font-size: 12px;text-align: right;border-top-style: none;border-width: 1px;border-color: #eee;">
                                                <?= number_format($cvalue['quantity'], 2); ?>
                                            </td>
                                            <td style="padding-top:5px;padding-bottom:5px;padding-right:5px;padding-left:5px;vertical-align: middle;border-style: solid;border-right-style: none;border-left-style: none;font-size: 12px;text-align: right;border-top-style: none;border-width: 1px;border-color: #eee;">
                                                <?= '$' . number_format($cvalue['rate'], 2); ?>
                                            </td>
                                            <td style="padding-top:5px;padding-bottom:5px;padding-right:5px;padding-left:5px;vertical-align: middle;border-style: solid;border-right-style: none;border-left-style: none;font-size: 12px;text-align: right;border-top-style: none;border-width: 1px;border-color: #eee;">
                                                <?= '$' . number_format($cvalue['amount'], 2); ?>
                                            </td>
                                        </tr>
                                        <?php
                                        $ulcount++;
                                        if (count($unbillable_labor) == $ulcount) {
                                            ?>
                                            <tr>
                                                <td></td>

                                                <td style="font-weight: bold;padding: 7px;color: #000000;text-align: right;font-size: 12px;vertical-align: middle;">Total <?= $cvalue['name']; ?></td>
                                                <td></td>
                                                <td></td>
                                                <td style="font-weight: bold; padding: 7px;color: #000000;text-align: right;font-size: 12px;vertical-align: middle;">$<?= number_format($unbillable_labor_total_bill, 2); ?></td>
                                            </tr>
                                            <?php
                                        }
                                    }
                                    foreach ($unbillable_other as $cvalue) {
                                        if ($uocount == 0) {
                                            ?>
                                            <tr>
                                                <td style="width: 15%;font-weight: bold;color: #000000;font-size: 12px;vertical-align: middle;"><?= $cvalue['name']; ?></td>
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
                                                <?= date("m/d/Y", strtotime($cvalue['date_charge'])); ?>
                                            </td>
                                            <td style="width: 100%;padding-top:5px;padding-bottom:5px;padding-right:5px;padding-left:5px;vertical-align: middle;border-style: solid;border-right-style: none;font-size: 12px;border-left-style: none;border-top-style: none;border-width: 1px;border-color: #eee;">
                                                <?= $cvalue['description']; ?>
                                            </td>
                                            <td style="padding-top:5px;padding-bottom:5px;padding-right:5px;padding-left:5px;vertical-align: middle;border-style: solid;border-right-style: none;font-size: 12px;border-left-style: none;text-align: right;border-top-style: none;border-width: 1px;border-color: #eee;">
                                                <?= number_format($cvalue['quantity'], 2); ?>
                                            </td>
                                            <td style="padding-top:5px;padding-bottom:5px;padding-right:5px;padding-left:5px;vertical-align: middle;border-style: solid;border-right-style: none;font-size: 12px;border-left-style: none;text-align: right;border-top-style: none;border-width: 1px;border-color: #eee;">
                                                <?= '$' . number_format($cvalue['rate'], 2); ?>
                                            </td>
                                            <td style="padding-top:5px;padding-bottom:5px;padding-right:5px;padding-left:5px;vertical-align: middle;border-style: solid;border-right-style: none;font-size: 12px;border-left-style: none;text-align: right;border-top-style: none;border-width: 1px;border-color: #eee;">
                                                <?= '$' . number_format($cvalue['amount'], 2); ?>
                                            </td>
                                        </tr>
                                        <?php
                                        $uocount++;
                                        if (count($unbillable_other) == $uocount) {
                                            ?>
                                            <tr>
                                                <td></td>

                                                <td style="font-weight: bold;padding: 7px;color: #000000;text-align: right;font-size: 12px;vertical-align: middle;">Total <?= $cvalue['name']; ?></td>
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
                    <td style="<?= (count($value['recent_charges']) == 0 ? 'border-width: 1px; border-style: solid; border-right-style: none;border-color: #40926a;' : 'width:40%;border-right: 1px solid #40926a;border-width: 1px;border-style: solid;border-color: #40926a;'); ?>padding-top:5px;padding-bottom:5px;padding-left:5px;vertical-align: middle;">
                        <table style="width: 80%;" cellspacing="0">
                            <tbody>
                                <tr>
                                    <td colspan="5" style="padding-top:0px;padding-bottom:5px;padding-right:5px;padding-left:5px;vertical-align: middle;">
                                        <table style="width: 100%;" cellspacing="0">
                                            <tbody>
                                                <tr>
                                                    <td style="width: 17%;font-weight: bold;color: #000000;font-size: 12px;vertical-align: middle;"><?= (count($value['recent_charges']) == 0 ? "<span style='color: grey;'>No Recent Payments</span>" : "Recent Payments Received"); ?></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </td>
                                </tr>
                                <?php
                                $rc = 0;
                                foreach ($value['recent_charges'] as $cvalue) {
                                    if ($rc == 0) {
                                        ?>
                                        <tr>
                                            <td style="padding-top:5px;padding-bottom:5px;padding-right:5px;padding-left:5px;vertical-align: middle;background-color:#eee;font-weight:bold;color: #000000;font-size: 12px;">
                                                Date
                                            </td>
                                            <td style="padding-top:5px;padding-bottom:5px;padding-right:5px;padding-left:5px;vertical-align: middle;background-color:#eee;font-weight:bold;color: #000000;font-size: 12px;">
                                                Type
                                            </td>
                                            <td style="padding-top:5px;padding-bottom:5px;padding-right:5px;padding-left:5px;vertical-align: middle;background-color:#eee;font-weight:bold;color: #000000;font-size: 12px;">
                                                Reference
                                            </td>
                                            <td style="width: 100%;padding-top:5px;padding-bottom:5px;padding-right:5px;padding-left:5px;vertical-align: middle;background-color:#eee;font-weight:bold;color: #000000;font-size: 12px;">
                                                Description
                                            </td>
                                            <td style="padding-top:5px;padding-bottom:5px;padding-right:5px;padding-left:5px;vertical-align: middle;background-color:#eee;font-weight:bold;color: #000000;font-size: 12px;text-align: right;">
                                                Amount
                                            </td>
                                        </tr>
                                        <?php
                                    }
                                    ?>
                                    <tr>
                                        <td style="padding-top:5px;padding-bottom:5px;padding-right:5px;padding-left:5px;vertical-align: middle;font-size: 12px;border-style: solid;border-right-style: none;border-left-style: none;border-top-style: none;border-width: 1px;border-color: #eee;">
                                            <?= date("m/d/Y", strtotime($cvalue['date_credit'])); ?>
                                        </td>
                                        <td style="padding-top:5px;padding-bottom:5px;padding-right:5px;padding-left:5px;vertical-align: middle;font-size: 12px;border-style: solid;border-right-style: none;border-left-style: none;border-top-style: none;border-width: 1px;border-color: #eee;">
                                            <?= $cvalue['type']; ?>
                                        </td>
                                        <td style="padding-top:5px;padding-bottom:5px;padding-right:5px;padding-left:5px;vertical-align: middle;font-size: 12px;border-style: solid;border-right-style: none;border-left-style: none;border-top-style: none;border-width: 1px;border-color: #eee;">
                                            <?= $cvalue['reference']; ?>
                                        </td>
                                        <td style="padding-top:5px;padding-bottom:5px;padding-right:5px;padding-left:5px;vertical-align: middle;font-size: 12px;border-style: solid;border-right-style: none;border-left-style: none;border-top-style: none;border-width: 1px;border-color: #eee;width: 100%;">
                                            <?= $cvalue['description']; ?>
                                        </td>
                                        <td style="padding-top:5px;padding-bottom:5px;padding-right:5px;padding-left:5px;vertical-align: middle;font-size: 12px;border-style: solid;border-right-style: none;border-left-style: none;text-align: right;border-top-style: none;border-width: 1px;border-color: #eee;">
                                            <?= '$' . number_format($cvalue['amount'], 2); ?>
                                        </td>
                                    </tr>
                                    <?php
                                    $rc++;
                                }
                                ?>
                            </tbody>
                        </table>
                    </td>

                    <td colspan="2" style="width: 5%;padding-top:5px;padding-bottom:5px;padding-right:5px;padding-left:5px;vertical-align: middle;<?= (count($value['recent_charges']) == 0 ? 'border-width: 1px; border-style: solid; border-left-style: none;border-color: #40926a;' : 'border-bottom: 1px solid;border-width: 1px;border-style: solid;border-color: #40926a;'); ?>">
                        <span <?= (count($value['recent_charges']) == 0 ? 'style="display:none;"' : ''); ?>><a href="<?= base_url(); ?>history/<?= str_replace(["-", "_"], '', $invoice_code); ?>">View All History</a> or visit <a rel="nofollow" style='text-decoration:none; color:#333'>billex.net/history</a> and enter code <?= $invoice_code; ?>.</span>
                    </td>

                </tr>
            <?php } ?>
        </table>
        <?= (count($customer_data) != $key + 1 ? '<div class="breakNow" style="page-break-after:always;"></div>' : ''); ?>

        <table>
            <tbody>
                <tr><td><br></td></tr>
                <tr>
                    <td style="padding-right: 5px;padding-top:5px;padding-left:5px;padding-bottom: 5px;font-weight:bold;font-size:9px;font-family: Arial, Helvetica, sans-serif;" >
                        <i>S/N: <?= $invoice_data['invoice_id']; ?></i>
                    </td>
                </tr> 
            </tbody>
        </table>
    </body>
</html>