<?php
$total = 0;
$count = 0;
foreach ($customer_data as $value) {
    $total = $total + floatval($value['amount']);
    $count++;
}
?>
<div style="display: flex;">	
    <div style="width: 30%;">	
        <div>
            <h3></h3>
            <div style="height: 95px;"></div>
            <div style="margin-left: 110px;">
                <label><?= date('m/d/Y'); ?></label>
            </div>
            <div style="height: 25px;"></div>
            <div>
                <label></label>
            </div>
        </div>
    </div>
    <div style="width: 70%;padding-top: 30px;">
        <div style="display: flex;" >
            <?php
            $dc = 0;
            $dp = 0;
            foreach ($customer_data as $key => $value) {
                $dp++;
                echo ($key % 6 == 0 ? '<div style = "width: 33.33%;padding-top: 5px;padding-right: 15px;min-height:190px;">' : "");
                ?>
                <div style="display: flex;justify-content: space-between;">
                    <div style="padding-bottom: 5px;padding-top: 5px;text-align: left;"><?= $value['reference']; ?></div>
                    <div style="padding-bottom: 5px;padding-top: 5px;text-align: right;"><?= $value['amount']; ?></div>
                </div>
                <?php
                echo ($dp % 6 == 0 ? '</div>' : '');
                if ($dp % 6 == 0) {
                    $dc = 0;
                } else {
                    $dc++;
                }
            }
            ?>
        </div>
    </div>
    <div style="display: flex;">
        <div style="width: 49%;padding-top: 5px;">
            <div style="display: flex;justify-content: flex-end;">
                <div style="padding-bottom: 5px;padding-top: 5px;font-size: 10pt;"><span style="font-weight: bold;"><?= $count; ?></span></div>
            </div>
        </div>
        <div style="width: 49%;padding-top: 5px;text-align: right;">
            <div style="padding-bottom: 5px;padding-top: 5px;font-size: 10pt;font-weight: bold;"><span><?= '$ ' . number_format($total, 2); ?></span></div>
        </div>
    </div>
</div>
</div>
<div>
    <div style="display: flex;margin-top: 50px;align-items: center;">
        <div style="width: 60%;text-align: right;">
            <h2 style="margin: 0;">Deposit Summary</h2>
        </div>
        <div style="width: 38%;text-align: right;">
            <p style="margin: 0;"><?= date('m/d/Y'); ?></p>
        </div>
    </div>
    <table style="width: 100%;">
        <tr>
            <td colspan="2" style="padding-top:5px;padding-bottom:5px;padding-right:5px;padding-left:5px;vertical-align: middle;">
                <table style="width: 100%;" cellspacing="0">
                    <tbody>
                        <tr>
                            <td style="padding-top:5px;padding-bottom:5px;padding-right:5px;padding-left:5px;vertical-align: middle;background-color:#eee;font-weight:bold;color: #000000;font-size: 12px;" >
                                Ref
                            </td>
                            <td style="padding-top:5px;padding-bottom:5px;padding-right:5px;padding-left:5px;vertical-align: middle;background-color:#eee;font-weight:bold;color: #000000;text-align: right;font-size: 12px;" >
                                PmtMethod
                            </td>
                            <td style="width: 80%;padding-top:5px;padding-bottom:5px;padding-right:5px;padding-left:5px;vertical-align: middle;background-color:#eee;font-weight:bold;color: #000000;font-size: 12px;" >
                                Customer
                            </td>
                            <td style="width: 15%;padding-top:5px;padding-bottom:5px;padding-right:5px;padding-left:5px;vertical-align: middle;background-color:#eee;font-weight:bold;color: #000000;text-align: right;font-size: 12px;" >
                                Amount
                            </td>
                        </tr>
                        <?php
                        foreach ($customer_data as $value) {
                            ?>
                            <tr>
                                <td style="padding-top:5px;padding-bottom:5px;padding-right:5px;padding-left:5px;vertical-align: middle;border-style: solid;border-right-style: none;font-size: 12px;border-left-style: none;border-top-style: none;border-width: 1px;border-color: #eee;">
                                    <?= $value['reference']; ?>
                                </td>
                                <td style="padding-top:5px;padding-bottom:5px;padding-right:5px;padding-left:5px;vertical-align: middle;border-style: solid;border-right-style: none;font-size: 12px;border-left-style: none;border-top-style: none;border-width: 1px;border-color: #eee;">
                                    <?= $value['type']; ?>
                                </td>
                                <td style="width: 80%;padding-top:5px;padding-bottom:5px;padding-right:5px;padding-left:5px;vertical-align: middle;border-style: solid;border-right-style: none;font-size: 12px;border-left-style: none;border-top-style: none;border-width: 1px;border-color: #eee;">
                                    <?= $value['customers']; ?>
                                </td>
                                <td style="width: 15%;padding-top:5px;padding-bottom:5px;padding-right:5px;padding-left:5px;vertical-align: middle;border-style: solid;border-right-style: none;font-size: 12px;border-left-style: none;text-align: right;border-top-style: none;border-width: 1px;border-color: #eee;">
                                    <?= $value['amount']; ?>
                                </td>
                            </tr>
                            <?php
                        }
                        ?>
                        <tr>
                            <td></td>
                            <td></td>
                            <td style="font-weight: bold;padding: 7px;text-align: right;color: #000000;font-size: 12px;vertical-align: middle;">Diposit Total:</td>
                            <td style="width: 15%;font-weight: bold; padding: 7px;text-align: right;color: #000000;font-size: 12px;vertical-align: middle;"><?= "$ " . number_format($total, 2); ?></td>
                        </tr>
                    </tbody>
                </table>
            </td>
        </tr>
    </table>
</div>