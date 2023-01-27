<?php
echo view('aut/dashboard_common/dashboard_header_view');
echo view('aut/dashboard_common/dashboard_sidebar_view');
?>
<style>
    .admin-contain-main-div{
        padding: 30px;
    }
</style>
<div class="container-fluid">
    <div class="row">
        <div class="main-table-div user_listing">
            <div class="add_customer text-right mb-3 no_desktop">
                <a href="<?php echo base_url(); ?>aut/administration/add_biller_user" class="add_ico no_desktop"><div class="add-ico"><i class="fas fa-plus"></i></div></a>
            </div>
            <div class="table-heading no_mob">
                <div class="table-col_name">
                    <p>Name / Email Address / User Status</p>
                </div>

                <div class="table-col add_user_div">
                    <a href="<?php echo base_url(); ?>aut/administration/add_biller_user" class="btn btn-adduser"><i class="fas fa-user-plus"></i> &nbsp;Add</a>
                </div>
            </div>
            <div class="mainrow-div">
                <?php
                foreach ($biller_user as $key => $value) {
                    $status = '';
                    $date1 = date("Y-m-d", strtotime($value['time_last_access']));
                    $date2 = date("Y-m-d");
                    $diff = abs(strtotime($date2) - strtotime($date1));
                    $days = round($diff / (60 * 60 * 24));

                    if ($value['time_last_access'] == "0000-00-00 00:00:00") {
                        $status = '<p class="text-muted"><i class="fas fa-spinner"></i> Never</p>';
                    } else {
                        if ($days == 0 || $days == 1) {
                            $status = '<p class="text-success"><i class="fas fa-spinner"></i> Today</p>';
                        } else if ($days < 30) {
                            $status = '<p class="text-success"><i class="fas fa-spinner"></i> ' . $days . ' days ago</p>';
                        } else {
                            $status = '<p class="text-muted"><i class="fas fa-spinner"></i> ' . $days . ' days ago</p>';
                        }
                    }

                    $edate1 = date("Y-m-d", strtotime($value['expiration']));
                    $diff = strtotime($edate1) - strtotime($date2);
                    $edays = round($diff / (60 * 60 * 24));
                    ?>
                    <div class="user_listing_main">
                        <div class="user_main_div">
                            <div class="user_profile_image">
                                <div class="table-col_image">
                                    <?php
                                    if ($value['avatar_seed'] == 0) {
                                        $src = "https://billex.s3.amazonaws.com/profile/profile-placeholder.png";
                                    } else {
                                        $src = "https://billex.s3.amazonaws.com/profile/" . $value['user_id'] . "-" . base_convert($value['user_id'] + $value['avatar_seed'], 10, 32) . ".jpg";
                                    }
                                    ?>
                                    <img src="<?= $src; ?>" class="biller_profile_image">
                                </div>
                            </div>
                            <div class="user_information">
                                <div class="user_name_main">
                                    <div class="user_name_sub">
                                        <strong><?= $value['name_display']; ?></strong>
                                    </div>
                                    <div class="user_status_sub">
                                        <div class="onoffswitch2 no_mob">
                                            <input type="checkbox" name="onoffswitch" class="onoffswitch2-checkbox user_disable" id="user_disable_<?= $value['user_id']; ?>" <?= ($edays <= 0 || $edays == 1) ? "" : "checked" ?> data-id="<?= $value['user_id']; ?>">
                                            <label class="onoffswitch2-label" for="user_disable_<?= $value['user_id']; ?>">
                                                <span class="onoffswitch2-inner"></span>
                                                <span class="onoffswitch2-switch"></span>
                                            </label>
                                        </div>
                                        <div class="onoffswitch1 no_desktop">
                                            <input type="checkbox" name="onoffswitch1" class="onoffswitch1-checkbox user_disable" id="user_disable_mob_<?= $value['user_id']; ?>" <?= ($edays <= 0 || $edays == 1) ? "" : "checked" ?> data-id="<?= $value['user_id']; ?>">
                                            <label class="onoffswitch1-label" for="user_disable_mob_<?= $value['user_id']; ?>"></label>
                                        </div>
                                    </div>
                                </div>
                                <div class="user_email_phone">
                                    <?php
                                    $email = '';
                                    $phone_numer = '';
                                    $result = dbQueryRows('user_auth', array('user_id' => $value['user_id']));
                                    if (count($result) > 1) {
                                        foreach ($result as $mvalue) {
                                            if ($mvalue['type'] == "email") {
                                                $email = $mvalue['value'];
                                                break;
                                            } else if ($mvalue['type'] == "phone") {
                                                if (preg_match('/^(\d{3})(\d{3})(\d{4})$/', $rvalue['value'], $matches)) {
                                                    $phone_result = $matches[1] . '-' . $matches[2] . '-' . $matches[3];
                                                    $phone_numer = $phone_result;
                                                }
                                                break;
                                            }
                                        }
                                    } else {
                                        foreach ($result as $rvalue) {
                                            if ($rvalue['type'] == "email") {
                                                $email = $rvalue['value'];
                                            } else {
                                                if (preg_match('/^(\d{3})(\d{3})(\d{4})$/', $rvalue['value'], $matches)) {
                                                    $phone_result = $matches[1] . '-' . $matches[2] . '-' . $matches[3];
                                                    $phone_numer = $phone_result;
                                                }
                                            }
                                        }
                                    }
                                    ?>
                                    <p><?= ($email == "" ? '<i class="fas fa-mobile-alt"></i>' . $phone_numer : '<i class="far fa-envelope"></i>' . $email); ?></p>
                                </div>
                                <div class="user_activity_status">
                                    <div class="user_activity_status_sub">
                                        <?= $status; ?>
                                    </div>
                                    <div class="user_activity_btn">
                                        <button type="button" class="btn btn-default biller_activity"><i class="fas fa-history"></i> Activity</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php
                }
                ?>

            </div>
            <div class="customer_filter no_mob" style="display: <?= ($key >= 2 ? "" : "none"); ?>">
                <div class="add_customer text-right my-3">
                    <a href="<?php echo base_url(); ?>aut/administration/add_biller_user" class="btn btn-submit"><i class="fas fa-user-plus"></i> &nbsp;Add</a>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
echo view('aut/dashboard_common/dashboard_footer_view');
?>
<script src="<?php echo base_url(); ?>js/administration.js"></script>