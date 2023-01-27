<?php
echo view('aut/dashboard_common/dashboard_header_view');
echo view('aut/dashboard_common/dashboard_sidebar_view');
?>
<style>
    .admin-contain-main-div{
        padding: 30px;
    }
</style>
<div class="container-fluid p-0">
    <div class="bd-example">
        <h4>New Alerts:</h4>
        <div class="dropdown-divider"></div>
        <?php
        $i = 0;
        foreach ($alert_data as $value) {
            ?>
            <a class="dropdown-item-alert user_alert_list alert alert-<?= $value['class']; ?>" href="JavaScript:void(0)">
                <div class="dropdown-message small alert-<?= $value['class']; ?>"><?= $value['message']; ?></div>
                <p class="small alert-<?= $value['class']; ?> mb-0"><?= date('d F , Y h:i A', strtotime(convert_timezone($_SESSION['login_userid'], $value['time_stamp'], 'UTC'))); ?></p>
            </a>
            <?php
        }
        ?>
        <a class="dropdown-item small" href="#" style="display: <?= (count(getAllAlerts()) > 5 ? "" : "none"); ?>">View all alerts</a>
    </div>

</div>
<?php
echo view('aut/dashboard_common/dashboard_footer_view');
?>
