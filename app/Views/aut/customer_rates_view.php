<?php
echo view('aut/dashboard_common/dashboard_header_view');
echo view('aut/dashboard_common/dashboard_sidebar_view');
?>
<div class="container-fluid">
    <div class="row mb-3">   
        <div class="main-table-div">
            <div class="add_header_line">
                <div><h5 class="no_mob" id="add_charges_heading">Enter Rates</h5></div>
                <div>
                    <?php
                    $result = dbQueryRows('customer_item', array('customer_id' => $customer_id));
                    ?>
                    <input type="hidden" value="<?= count($result); ?>" id="customer_rate">
                    <div class="onoffswitch1" style="left: 0;display: <?= (count($result) >= 1 ? "" : "none") ?>">
                        <input type="checkbox" name="onoffswitch1" class="onoffswitch1-checkbox user_disable" id="user_disable" checked="">
                        <label class="onoffswitch1-label" for="user_disable"></label>
                    </div>
                </div>
            </div>
            <div class="mainrow-div">
                <div class="row1 chargeclick set_row">
                    <div class="table-col search_text_div">
                        <strong class="customer_name"><?= $name; ?></strong>
                        <p><span class="customer_identifier"><?= $identifier; ?></span> <?= (($identifier == '' && $po == '') || ($identifier == '' || $po == '') ? "" : "/") ?> <span><?= $po; ?></span></p>
                    </div>
                    <div class="table-col recordhide">
                        <p class="customer_attention"><?= $address_mail_attention; ?></p>
                        <p class="customer_address" style="display: <?= ($address_mail_street == "" ? "none" : "") ?>"><?= $address_mail_street; ?><br><?= $address_mail_city; ?>, <?= $address_mail_state; ?> <?= $address_mail_zip5; ?></p>
                    </div>
                    <div class="table-col recordhide">
                        <p><?= $contact_name; ?></p>
                        <p><?php echo '<a href="mailto:' . $contact_email . '" target="_blank" class="customer_mail_phone">' . $contact_email . '</a>'; ?></p>
                        <p><?php
                            if (preg_match('/^(\d{3})(\d{3})(\d{4})$/', $contact_phone, $matches)) {
                                $phone_result = $matches[1] . '-' . $matches[2] . '-' . $matches[3];
                                echo '<a href="tel:' . $phone_result . '" target="_blank" class="customer_mail_phone">' . $phone_result . '</a>';
                            }
                            ?></p>
                    </div>
                    <div class="angle-righticon no_desktop">
                        <i class="fas fa-chevron-right icon-rotate"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">   
        <div class="main-table-div customer_rates">
            <div class="table-heading">
                <div class="name_table_cols dtable-cols">
                    <p>Name</p>
                </div> 
                <div class="dtable-cols no_mob">
                    <p>Description</p>
                </div> 
                <div class="table-cols">
                    <p>Default Rate</p>
                </div> 
                <div class="customer_rates_text">
                    <p>Customer Rate</p>
                </div> 
            </div>
            <div class="mainrow-div item_listing">
                <?php
                $dcount = $scount = $blcount = $pcount = $rcount = $ulcount = $uocount = 0;
                $download = $billable_labor = $service = $product = $reimbursement = $unbillable_labor = $unbillable_other = array();

                foreach ($rates_data as $item_value) {
                    if ($item_value["ct_id"] == "1") {
                        $billable_labor[] = $item_value;
                    }
                    if ($item_value["ct_id"] == "2") {
                        $service[] = $item_value;
                    }
                    if ($item_value["ct_id"] == "3") {
                        $product[] = $item_value;
                    }
                    if ($item_value["ct_id"] == "4") {
                        $reimbursement[] = $item_value;
                    }
                    if ($item_value["ct_id"] == "5") {
                        $unbillable_labor[] = $item_value;
                    }
                    if ($item_value["ct_id"] == "6") {
                        $unbillable_other[] = $item_value;
                    }
                    if ($item_value["ct_id"] == "7") {
                        $download[] = $item_value;
                    }
                }

                foreach ($billable_labor as $value) {
                    echo ($blcount == 0 ? '<div class="item_list_1"><h4 class="header_text">Billable Labor</h4>' : "");
                    ?>
                    <div class="charge-tbl <?= ($blcount % 2 ? "bg-gray" : "bg-white1"); ?> <?= ($value['ci_rate'] == "" ? "un_set" : "set"); ?>" style="display: <?= (count($result) >= 1 && $value['ci_rate'] == "" ? "none" : ""); ?>">
                        <div class="results-data ">
                            <div class="name_table_cols dtable-cols">
                                <strong><?= $value['name']; ?></strong>
                            </div> 
                            <div class="dtable-cols no_mob">
                                <p><?= $value['description']; ?></p>
                            </div> 
                            <div class="table-cols">
                                <p><?= '$' . number_format($value['rate'], 2, '.', ''); ?></p>
                            </div> 
                            <div class="customer_rates_text position-relative">
                                <input type="text" class="form-control customer_rates" min="0" data-toggle="tooltip" data-placement="top" id="item_rate_<?= $value['item'] ?>" data-rateid="<?= ($value['ci_rate'] != "" ? $value['id'] : 0); ?>" data-itemid="<?= $value['item'] ?>" data-custid="<?= $customer_id ?>" value="<?= $value['ci_rate']; ?>">
                                <a href="#" class="sucess_tick text-success" style="display: none;"><i class="fas fa-check"></i></a>
                            </div> 
                        </div>
                        <p class="no_desktop mob_des"><?= $value['description']; ?></p>
                    </div>
                    <?php
                    $blcount++;
                    echo (count($billable_labor) == $blcount ? "</div>" : "");
                }
                foreach ($service as $value) {
                    echo ($scount == 0 ? "<div class='item_list_2'><h4 class='header_text'>Service</h4>" : "");
                    ?>
                    <div class="charge-tbl <?= ($scount % 2 ? "bg-gray" : "bg-white1"); ?> <?= ($value['ci_rate'] == "" ? "un_set" : "set"); ?>" style="display: <?= (count($result) >= 1 && $value['ci_rate'] == "" ? "none" : ""); ?>">
                        <div class="results-data ">
                            <div class="name_table_cols dtable-cols">
                                <strong><?= $value['name']; ?></strong>
                            </div> 
                            <div class="dtable-cols no_mob">
                                <p><?= $value['description']; ?></p>
                            </div> 
                            <div class="table-cols">
                                <p><?= '$' . number_format($value['rate'], 2, '.', ''); ?></p>
                            </div> 
                            <div class="customer_rates_text position-relative">
                                <input type="text" class="form-control customer_rates" min="0" data-toggle="tooltip" data-placement="top" id="item_rate_<?= $value['item'] ?>" data-rateid="<?= ($value['ci_rate'] != "" ? $value['id'] : 0); ?>" data-itemid="<?= $value['item'] ?>" data-custid="<?= $customer_id ?>" value="<?= $value['ci_rate']; ?>">
                                <a href="#" class="sucess_tick text-success" style="display: none;"><i class="fas fa-check"></i></a>
                            </div>
                        </div>
                        <p class="no_desktop mob_des"><?= $value['description']; ?></p>
                    </div>
                    <?php
                    $scount++;
                    echo (count($service) == $scount ? "</div>" : "");
                }
                foreach ($product as $value) {
                    echo ($pcount == 0 ? "<div class='item_list_3'><h4 class='header_text'>Product</h4>" : "");
                    ?>
                    <div class="charge-tbl <?= ($pcount % 2 ? "bg-gray" : "bg-white1"); ?> <?= ($value['ci_rate'] == "" ? "un_set" : "set"); ?>" style="display: <?= (count($result) >= 1 && $value['ci_rate'] == "" ? "none" : ""); ?>">
                        <div class="results-data ">
                            <div class="name_table_cols dtable-cols">
                                <strong><?= $value['name']; ?></strong>
                            </div> 
                            <div class="dtable-cols no_mob">
                                <p><?= $value['description']; ?></p>
                            </div> 
                            <div class="table-cols">
                                <p><?= '$' . number_format($value['rate'], 2, '.', ''); ?></p>
                            </div> 
                            <div class="customer_rates_text position-relative">
                                <input type="text" class="form-control customer_rates" min="0" data-toggle="tooltip" data-placement="top" id="item_rate_<?= $value['item'] ?>" data-rateid="<?= ($value['ci_rate'] != "" ? $value['id'] : 0); ?>" data-itemid="<?= $value['item'] ?>" data-custid="<?= $customer_id ?>" value="<?= $value['ci_rate']; ?>">
                                <a href="#" class="sucess_tick text-success" style="display: none;"><i class="fas fa-check"></i></a>
                            </div> 
                        </div>
                        <p class="no_desktop mob_des"><?= $value['description']; ?></p>
                    </div>
                    <?php
                    $pcount++;
                    echo (count($product) == $pcount ? "</div>" : "");
                }
                foreach ($download as $value) {
                    echo ($dcount == 0 ? "<div class='item_list_3'><h4 class='header_text'>Download</h4>" : "");
                    ?>
                    <div class="charge-tbl <?= ($dcount % 2 ? "bg-gray" : "bg-white1"); ?> <?= ($value['ci_rate'] == "" ? "un_set" : "set"); ?>" style="display: <?= (count($result) >= 1 && $value['ci_rate'] == "" ? "none" : ""); ?>">
                        <div class="results-data ">
                            <div class="name_table_cols dtable-cols">
                                <strong><?= $value['name']; ?></strong>
                            </div> 
                            <div class="dtable-cols no_mob">
                                <p><?= $value['description']; ?></p>
                            </div> 
                            <div class="table-cols">
                                <p><?= '$' . number_format($value['rate'], 2, '.', ''); ?></p>
                            </div> 
                            <div class="customer_rates_text position-relative">
                                <input type="text" class="form-control customer_rates" min="0" data-toggle="tooltip" data-placement="top" id="item_rate_<?= $value['item'] ?>" data-rateid="<?= ($value['ci_rate'] != "" ? $value['id'] : 0); ?>" data-itemid="<?= $value['item'] ?>" data-custid="<?= $customer_id ?>" value="<?= $value['ci_rate']; ?>">
                                <a href="#" class="sucess_tick text-success" style="display: none;"><i class="fas fa-check"></i></a>
                            </div> 
                        </div>
                        <p class="no_desktop mob_des"><?= $value['description']; ?></p>
                    </div>
                    <?php
                    $dcount++;
                    echo (count($download) == $dcount ? "</div>" : "");
                }
                foreach ($reimbursement as $value) {
                    echo ($rcount == 0 ? "<div class='item_list_4'><h4 class='header_text'>Reimbursement</h4>" : "");
                    ?>
                    <div class="charge-tbl <?= ($rcount % 2 ? "bg-gray" : "bg-white1"); ?> <?= ($value['ci_rate'] == "" ? "un_set" : "set"); ?>" style="display: <?= (count($result) >= 1 && $value['ci_rate'] == "" ? "none" : ""); ?>">
                        <div class="results-data ">
                            <div class="name_table_cols dtable-cols">
                                <strong><?= $value['name']; ?></strong>
                            </div> 
                            <div class="dtable-cols no_mob">
                                <p><?= $value['description']; ?></p>
                            </div> 
                            <div class="table-cols">
                                <p><?= '$' . number_format($value['rate'], 2, '.', ''); ?></p>
                            </div> 
                            <div class="customer_rates_text position-relative">
                                <input type="text" class="form-control customer_rates" min="0" data-toggle="tooltip" data-placement="top" id="item_rate_<?= $value['item'] ?>" data-rateid="<?= ($value['ci_rate'] != "" ? $value['id'] : 0); ?>" data-itemid="<?= $value['item'] ?>" data-custid="<?= $customer_id ?>" value="<?= $value['ci_rate']; ?>">
                                <a href="#" class="sucess_tick text-success" style="display: none;"><i class="fas fa-check"></i></a>
                            </div>
                        </div>
                        <p class="no_desktop mob_des"><?= $value['description']; ?></p>
                    </div>
                    <?php
                    $rcount++;
                    echo (count($reimbursement) == $rcount ? "</div>" : "");
                }
                foreach ($unbillable_labor as $value) {
                    echo ($ulcount == 0 ? "<div class='item_list_5'><h4 class='header_text'>Unbillable Labor</h4>" : "");
                    ?>
                    <div class="charge-tbl <?= ($ulcount % 2 ? "bg-gray" : "bg-white1"); ?> <?= ($value['ci_rate'] == "" ? "un_set" : "set"); ?>" style="display: <?= (count($result) >= 1 && $value['ci_rate'] == "" ? "none" : ""); ?>">
                        <div class="results-data ">
                            <div class="name_table_cols dtable-cols">
                                <strong><?= $value['name']; ?></strong>
                            </div> 
                            <div class="dtable-cols no_mob">
                                <p><?= $value['description']; ?></p>
                            </div> 
                            <div class="table-cols">
                                <p><?= '$' . number_format($value['rate'], 2, '.', ''); ?></p>
                            </div> 
                            <div class="customer_rates_text position-relative">
                                <input type="text" class="form-control customer_rates" min="0" data-toggle="tooltip" data-placement="top" id="item_rate_<?= $value['item'] ?>" data-rateid="<?= ($value['ci_rate'] != "" ? $value['id'] : 0); ?>" data-itemid="<?= $value['item'] ?>" data-custid="<?= $customer_id ?>" value="<?= $value['ci_rate']; ?>">
                                <a href="#" class="sucess_tick text-success" style="display: none;"><i class="fas fa-check"></i></a>
                            </div>
                        </div>
                        <p class="no_desktop mob_des"><?= $value['description']; ?></p>
                    </div>
                    <?php
                    $ulcount++;
                    echo (count($unbillable_labor) == $ulcount ? "</div>" : "");
                }
                foreach ($unbillable_other as $value) {
                    echo ($uocount == 0 ? "<div class='item_list_6'><h4 class='header_text'>Unbillable Other</h4>" : "");
                    ?>
                    <div class="charge-tbl <?= ($uocount % 2 ? "bg-gray" : "bg-white1"); ?> <?= ($value['ci_rate'] == "" ? "un_set" : "set"); ?>" style="display: <?= (count($result) >= 1 && $value['ci_rate'] == "" ? "none" : ""); ?>">
                        <div class="results-data ">
                            <div class="name_table_cols dtable-cols">
                                <strong><?= $value['name']; ?></strong>
                            </div> 
                            <div class="dtable-cols no_mob">
                                <p><?= $value['description']; ?></p>
                            </div> 
                            <div class="table-cols">
                                <p><?= '$' . number_format($value['rate'], 2, '.', ''); ?></p>
                            </div> 
                            <div class="customer_rates_text position-relative">
                                <input type="text" class="form-control customer_rates" min="0" data-toggle="tooltip" data-placement="top" id="item_rate_<?= $value['item'] ?>" data-rateid="<?= ($value['ci_rate'] != "" ? $value['id'] : 0); ?>" data-itemid="<?= $value['item'] ?>" data-custid="<?= $customer_id ?>" value="<?= $value['ci_rate']; ?>">
                                <a href="#" class="sucess_tick text-success" style="display: none;"><i class="fas fa-check"></i></a>
                            </div>
                        </div>
                        <p class="no_desktop mob_des"><?= $value['description']; ?></p>
                    </div>
                    <?php
                    $uocount++;
                    echo (count($unbillable_other) == $uocount ? "</div>" : "");
                }
                ?>
            </div>
        </div>
    </div>
    <div class="btn-submitdiv pl-3 back_customer_btn">
        <button type="button" class="btn btn-adduser" id="back_customer_list">Back</button>
    </div>
</div>
<?php
echo view('aut/dashboard_common/dashboard_footer_view');
?>
<script src="<?php echo base_url(); ?>js/customer.js"></script>