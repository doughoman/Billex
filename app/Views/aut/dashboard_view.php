<?php
echo view('aut/dashboard_common/dashboard_header_view');
echo view('aut/dashboard_common/dashboard_sidebar_view');
?>

<div class="container-fluid">
    <div class="row mt-4 mb-4">
        <div class="col-lg-6">
            <div class="main-box dashboard_main_box">
                <div class="info-box">
                    <span class="info-box-icon bg-aqua"><i class="fas fa-dollar-sign"></i></span>
                    <div class="info-box-content">
                        <div class="data-div"> 
                            <strong><span>Outstanding Balance</span></strong>
                            <?php
                            $customer_result = dbQueryRows('customer', array('biller_id' => $_SESSION['biller_id'], 'is_deleted' => 0));
                            $obalance = 0;
                            foreach ($customer_result as $value) {
                                $obalance = $obalance + floatval($value['balance']);
                            }
                            ?>
                            <strong><p>$<?= number_format($obalance, 2); ?></p></strong>
                        </div>
                    </div>
                    <!-- /.info-box-content -->
                </div>
                <div class="info-box dashboard_redirect_div" onclick='window.location.href = "<?php echo base_url(); ?>aut/billcharges"'>
                    <span class="info-box-icon bg-aqua"><i class="fas fa-file-invoice-dollar"></i></span>
                    <div class="info-box-content">
                        <div class="data-div"> 
                            <strong><span>Unbilled Charges</span></strong>
                            <strong><p>$<?= number_format($unbill_charges['amount'], 2); ?></p></strong>
                        </div>
                    </div>
                    <!-- /.info-box-content -->
                </div>
                <div class="info-box dashboard_redirect_div" onclick='window.location.href = "<?php echo base_url(); ?>aut/printdeposit"'>
                    <span class="info-box-icon bg-aqua"><i class="fas fa-clipboard-list"></i></span>
                    <div class="info-box-content">
                        <div class="data-div"> 
                            <strong><span>Undeposited Items</span></strong>
                            <strong><p><?= count($undeposited_items); ?></p></strong>
                        </div>
                    </div>
                    <!-- /.info-box-content -->
                </div>
                <div class="info-box dashboard_redirect_div" onclick='window.location.href = "<?php echo base_url(); ?>aut/printdeposit"'>
                    <span class="info-box-icon bg-aqua"><i class="fas fa-money-check-alt"></i></span>
                    <div class="info-box-content">
                        <div class="data-div"> 
                            <strong><span>Undeposited Total</span></strong>
                            <?php
                            $undeposited_total = 0;
                            foreach ($undeposited_items as $value) {
                                $undeposited_total = $undeposited_total + floatval($value['amount']);
                            }
                            ?>
                            <strong><p>$<?= number_format($undeposited_total, 2); ?></p></strong>
                        </div>
                    </div>
                    <!-- /.info-box-content -->
                </div>
                <div class="info-box dashboard_redirect_div" onclick='window.location.href = "<?php echo base_url(); ?>aut/customer"'>
                    <span class="info-box-icon bg-aqua"><i class="fas fa-users"></i></span>
                    <div class="info-box-content">
                        <div class="data-div"> 
                            <strong><span>Customer Count</span></strong>
                            <?php $customer_count = dbQueryRows('customer', array('biller_id' => $_SESSION['biller_id'], 'is_deleted' => 0, 'status' => 'active', 'user_id_created !=' => 0)); ?>
                            <strong><p><?= count($customer_count); ?></p></strong>
                        </div>
                    </div>
                    <!-- /.info-box-content -->
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="img-chart">
                <div id="container_line_chart" style="min-width: 310px; height: 400px; margin: 0 auto"></div>
                <?php
                $amount = '';
                for ($i = 1; $i <= 12; $i++) {
                    $amount .=$invoice_data[0][$i] . ',';
                }
                $amount = rtrim($amount, ',');
                $credit = '';
                for ($i = 1; $i <= 12; $i++) {
                    $credit .=$credit_data[0][$i] . ',';
                }
                $credit = rtrim($credit, ',');
                ?>  
            </div>
        </div>
    </div>
</div>
<?php
echo view('aut/dashboard_common/dashboard_footer_view');
?>
<script>
    $(document).ready(function () {
        Highcharts.chart('container_line_chart', {
            chart: {
                type: 'column'
            },
            title: {
                text: 'Monthly Totals'
            },
            subtitle: {
                text: ''
            },
            xAxis: {
                categories: [
                    'Jan',
                    'Feb',
                    'Mar',
                    'Apr',
                    'May',
                    'Jun',
                    'Jul',
                    'Aug',
                    'Sep',
                    'Oct',
                    'Nov',
                    'Dec'
                ],
                crosshair: true
            },
            yAxis: {
                min: 0,
                title: {
                    text: 'Amount'
                }
            },
            tooltip: {
                headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
                pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +
                        '<td style="padding:0"><b>{point.y:.1f} $</b></td></tr>',
                footerFormat: '</table>',
                shared: true,
                useHTML: true
            },
            plotOptions: {
                column: {
                    pointPadding: 0.2,
                    borderWidth: 0
                },
                series: {
                    animation: false
                }
            },
            responsive: {
                rules: [{
                        condition: {
                            maxWidth: 676
                        },
                        chartOptions: {
                            legend: {
                                enabled: true
                            }
                        }
                    }]
            },
            series: [{
                    name: 'Total Billed',
                    data: [<?php echo $amount; ?>],
                    color: "darkorange"

                }, {
                    name: 'Total Received',
                    data: [<?php echo $credit; ?>],
                    color: "#327052"

                }]
        });
    });
</script>