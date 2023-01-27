<?php
echo view('aut/dashboard_common/dashboard_header_view');
echo view('aut/dashboard_common/dashboard_sidebar_view');
$credit_result = dbQueryRows('credit_detail', array('customer_id' => $customer_id, 'is_deleted' => 0));
$total_amount = 0.00;
foreach ($credit_result as $vaule) {
    $total_amount = $total_amount + $vaule['amount'];
}
?>
<div class="container-fluid">
    <input type="hidden" id="customer_id" value="<?= base64_encode(encrypt($customer_id)); ?>">
    <div class="row charges-details-main customer_register">
        <div class="table-heading">
            <div class="edittable-cols">

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
        <div class="customer_listing_register">
            <?php
            if (count($register_data) == 0) {
                ?>
                <div class="row1 blank_col_div">
                    <div class="table-col blank_col">
                        <p>No Customer Register Available</p>
                    </div>
                </div>

                <?php
            }
            ?>
            <?php
            $count = 0;
            $tbalance = 0.00;
            foreach ($register_data as $rvalue) {
                if (isset($rvalue['invoice_number']) && !empty($rvalue['invoice_number'])) {
                    $tbalance = $tbalance + floatval($rvalue['amount']);
                } else {
                    $tbalance = $tbalance - floatval($rvalue['total']);
                }
            }
            foreach ($register_data as $value) {
                ?>
                <div class="charge-tbl <?= ($count % 2 ? "bg-gray" : "bg-white1"); ?> action_click1 remove_invoice_<?= (isset($value['invoice_number']) ? $value['invoice_id'] : ""); ?>" data-count="<?= $count; ?>">
                    <div class="results-data ">
                        <div class="edittable-cols">
                            <?php
                            if (isset($value['invoice_number']) && !empty($value['invoice_number'])) {
                                ?>
                                <button type="button" class="edit_delete_btn invoice_undo no_mob" data-id="<?= $value['invoice_id']; ?>" data-inid="<?= base64_encode(encrypt($value['invoice_id'])); ?>" data-amount="<?= $value['amount']; ?>"><i class="fas fa-undo"></i></button>
                                <button type="button" class="edit_delete_btn invoice_file_btn no_mob" data-url="<?= 'https://billex.s3.amazonaws.com/invoice/' . encodeHashids(array(intval($_SESSION['biller_id']), intval($value['invoice_id']), intval($value['customer_id']))) . '.pdf' ?>"><i class="far fa-file-alt"></i></button>
                                <?php
                            } else {
                                ?>
                                <a href="<?= base_url() ?>aut/postpayment/edit/<?= base64_encode(encrypt($value['credit_id'])) ?>" class="edit_delete_btn credit_edit no_mob"><i class="fas fa-edit"></i></a>
                                <?php
                            }
                            ?>
                        </div>
                        <div class="table-cols datetable-cols">
                            <p class="no_desktop"> <?= date("m/d/y", strtotime($value['time_created'])); ?> </p>
                            <p class="no_mob"> <?= date("m/d/Y", strtotime($value['time_created'])); ?> </p>
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
                    <p class="no_mob no_desktop mob_des register_reference">
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
                    <div class="action_div" style="display: none;">
                        <div class="action-cols">
                            <?php
                            if (isset($value['invoice_number']) && !empty($value['invoice_number'])) {
                                ?>
                                <button type="button" class="edit_delete_btn invoice_undo" data-id="<?= $value['invoice_id']; ?>" data-inid="<?= base64_encode(encrypt($value['invoice_id'])); ?>" data-amount="<?= $value['amount']; ?>"><i class="fas fa-undo"></i></button>
                                <button type="button" class="edit_delete_btn invoice_file_btn" data-url="<?= 'https://billex.s3.amazonaws.com/invoice/' . encodeHashids(array(intval($_SESSION['biller_id']), intval($value['invoice_id']), intval($value['customer_id']))) . '.pdf' ?>"><i class="far fa-file-alt"></i></button>
                                <?php
                            } else {
                                ?>
                                <a href="<?= base_url() ?>aut/postpayment/edit/<?= base64_encode(encrypt($value['credit_id'])) ?>" class="edit_delete_btn credit_edit"><i class="fas fa-edit"></i></a>
                                <?php
                            }
                            ?>
                        </div>
                    </div>
                </div>
                <?php
                $count ++;
            }
            ?>
        </div>
    </div>
    <div class="btn-submitdiv pl-3 back_customer_btn">
        <button type="button" class="btn btn-adduser" id="back_customer_list">Back</button>
    </div>
</div>
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
                <div style="height: 80vh;">
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
</div>
<?php
echo view('aut/dashboard_common/dashboard_footer_view');
?>
<script src="<?php echo base_url(); ?>js/customer.js"></script>
