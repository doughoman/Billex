<?php
echo view('aut/dashboard_common/dashboard_header_view');
echo view('aut/dashboard_common/dashboard_sidebar_view');
?>
<!-- Modal -->
<div class="modal fade" id="exampleModalCenter" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLongTitle">Export</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="go_back();">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="text-center">
                    <p style="font-size:16px;">Click on the button, which you want to like to export data.</p>
                    <p><button type="button" class="btn btn-primary" id="btn_customer" onclick="window.location.href = '<?php echo base_url(); ?>aut/administration/export/customer'"><i class="far fa-handshake fa-fw"></i><span class="nav-link-text">&nbsp;&nbsp;Customers</span></button></p>
                    <p class="mb-0"><a class="btn btn-primary btn_item_import" id="btn_item" href='<?php echo base_url(); ?>aut/administration/export/item'><i class="fas fa-list fa-fw"></i><span class="nav-link-text">&nbsp;&nbsp;Items</span></a></p>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
echo view('aut/dashboard_common/dashboard_footer_view');
?>
<script type="text/javascript">
    $('#exampleModalCenter').modal({backdrop: 'static', keyboard: false});
    $('#exampleModalCenter').modal('show');
    function go_back() {
        if (document.referrer == BASE_URL + 'aut/administration/export/customer' || document.referrer == BASE_URL + 'aut/administration/export/item') {
            window.location.href = BASE_URL + 'dashboard';
        } else {
            window.history.back();
        }
    }
</script>