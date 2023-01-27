<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    </head>
    <body>
        <div>
            <div style="width: 100%;max-width: 500px;font-size: 16px;padding: 10px;">
                <p style="margin-bottom: 0;margin-top: 0;">Here is the info you purchased.</p>
            </div>
            <div style="width: 100%;max-width: 500px;font-size: 16px;padding: 10px;">
                <p style="margin-bottom: 0;margin-top: 0;"><?= $info; ?></p>
            </div>
            <h3>Purchase Info</h3>
            <div style="background-color: lightgray;width: 100%;max-width: 500px;display: flex;align-items: center;">
                <div style="width: 100%;max-width: 500px;font-size: 16px;padding: 10px;display: flex;align-items: center;">
                    <p style="margin-bottom: 0;margin-top: 0;">Date:</p>
                </div>
                <div style="width: 100%;max-width: 500px;font-size: 16px;padding: 10px;">
                    <p style="margin-bottom: 0;margin-top: 0;"><?= date("m/d/Y", strtotime($date_charge)); ?></p>
                </div>
            </div>
            <div style="background-color: white;width: 100%;max-width: 500px;display: flex;align-items: center;">
                <div style="width: 100%;max-width: 500px;font-size: 16px;padding: 10px;display: flex;align-items: center;">
                    <p style="margin-bottom: 0;margin-top: 0;">Qty:</p>
                </div>
                <div style="width: 100%;max-width: 500px;font-size: 16px;padding: 10px;">
                    <p style="margin-bottom: 0;margin-top: 0;"><?= number_format($quantity, 2); ?></p>
                </div>
            </div>
            <div style="background-color: lightgray;width: 100%;max-width: 500px;display: flex;align-items: center;">
                <div style="width: 100%;max-width: 500px;font-size: 16px;padding: 10px;display: flex;align-items: center;">
                    <p style="margin-bottom: 0;margin-top: 0;">Description:</p>
                </div>
                <div style="width: 100%;max-width: 500px;font-size: 16px;padding: 10px;">
                    <p style="margin-bottom: 0;margin-top: 0;"><?= $description; ?></p>
                </div>
            </div>
            <div style="background-color: white;width: 100%;max-width: 500px;display: flex;align-items: center;">
                <div style="width: 100%;max-width: 500px;font-size: 16px;padding: 10px;display: flex;align-items: center;">
                    <p style="margin-bottom: 0;margin-top: 0;">Rate:</p>
                </div>
                <div style="width: 100%;max-width: 500px;font-size: 16px;padding: 10px;">
                    <p style="margin-bottom: 0;margin-top: 0;"><?= number_format($rate, 2); ?></p>
                </div>
            </div>
            <div style="background-color: lightgray;width: 100%;max-width: 500px;display: flex;align-items: center;">
                <div style="width: 100%;max-width: 500px;font-size: 16px;padding: 10px;display: flex;align-items: center;">
                    <p style="margin-bottom: 0;margin-top: 0;">Amount:</p>
                </div>
                <div style="width: 100%;max-width: 500px;font-size: 16px;padding: 10px;">
                    <p style="margin-bottom: 0;margin-top: 0;"><?= number_format($amount, 2); ?></p>
                </div>
            </div>
        </div>
    </body>
</html>