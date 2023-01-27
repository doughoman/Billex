<?php
echo view('aut/dashboard_common/dashboard_header_view');
echo view('aut/dashboard_common/dashboard_sidebar_view');
?>
<div class="container box-content">
    <div class="row">
        <div class="col-sm-12 col-md-6 col-lg-6 col-xl-4">
            <div class="sub-box container-fluid">

                <div class="sub-box-title">
                    <h1>Small</h1>
                    <div class="kt-pricing-1__hexagon1">
                        <div class="pricing_icon text-center">
                            <img src="<?= base_url() ?>images/rocket.svg" class="img-fuild">
                        </div>
                    </div>
                    <h2><sup>$</sup><strong>8</strong><sup>25</sup><sub>/month</sub></h2>
                    <h4>$99 Billed Annually</h4>
                </div>

                <div class="sub-box-item">
                    <p>5 Channels</p>
                    <ul>
                        <li>Unlimited App Users</li>
                        <li>Web Dhashboard</li>
                        <li>No Ads</li>
                        <li>Dedicated Dial-In Number</li>
                        <li>API and Embedded Feeds</li>
                    </ul>
                    <p>Include Annually :</p>
                </div> 

                <div class="pay-btn">
                    <button class="btn btn-submit">Buy Now</button>
                </div>

            </div>
        </div>

        <div class="col-sm-12 col-md-6 col-lg-6 col-xl-4">
            <div class="sub-box">

                <div class="sub-box-title">
                    <h1>Medium</h1>
                    <div class="kt-pricing-1__hexagon1">
                        <div class="pricing_icon text-center">
                            <img src="<?= base_url() ?>images/piggy-bank.svg" class="img-fuild">
                        </div>
                    </div>
                    <h2><sup>$</sup><strong>32</strong><sup>42</sup><sub>/month</sub></h2>
                    <h4>$389 Billed Annually</h4>
                </div>

                <div class="sub-box-item">
                    <p>20 Channels</p>
                    <ul>
                        <li>Unlimited App Users</li>
                        <li>Web Dhashboard</li>
                        <li>No Ads</li>
                        <li>Dedicated Dial-In Number</li>
                        <li>API and Embedded Feeds</li>
                        <li>Custom Web Address</li>
                    </ul>
                </div> 

                <div class="pay-btn">
                    <button class="btn btn-submit">Buy Now</button>
                </div>

            </div>
        </div>

        <div class="col-sm-12 col-md-6 col-lg-6 col-xl-4">
            <div class="sub-box">

                <div class="sub-box-title">
                    <h1>Large</h1>
                    <div class="kt-pricing-1__hexagon1">
                        <div class="pricing_icon text-center">
                            <img src="<?= base_url() ?>images/gift.svg" class="img-fuild">
                        </div>
                    </div>
                    <h2><sup>$</sup><strong>62</strong><sup>42</sup><sub>/month</sub></h2>
                    <h4>$749 Billed Annually</h4>
                </div>

                <div class="sub-box-item">
                    <p>99 Channels</p>
                    <ul>
                        <li>Unlimited App Users</li>
                        <li>Web Dhashboard</li>
                        <li>No Ads</li>
                        <li>Dedicated Dial-In Number</li>
                        <li>API and Embedded Feeds</li>
                        <li>Custom Web Address</li>
                    </ul>
                </div> 

                <div class="pay-btn">
                    <button class="btn btn-submit">Buy Now</button>
                </div>

            </div>
        </div>

    </div>
</div>

<?php
echo view('aut/dashboard_common/dashboard_footer_view');
?>