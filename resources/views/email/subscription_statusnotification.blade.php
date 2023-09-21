<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Service Expiration Reminder</title>
</head>
<body>
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr>
            <td align="center">
                <table width="600" border="0" cellspacing="0" cellpadding="0" style="border-collapse: collapse;">
                    <tr>
                        <td align="center" bgcolor="#f5f5f5" style="padding: 40px 0;">
                            <img src="https://subscriptions.wdesign.gr/images/WDesign%20logo.png" alt="wdesignLogo" style="max-width: 150px;">
                            <h1 style="color: #333333;">Service Expiration Reminder 0 Days</h1>
                        </td>
                    </tr>
                    <tr>
                        <td bgcolor="#ffffff" style="padding: 20px;">
                            <p>Dear {{ $customer_pronunciation }},</p>
                            <p>Your service {{ $service_name }} for the {{ $domain }} is set to expire on {{ $expired_date }}.</p>
                            <p>Please contact us for payment details to renew your service.</p>
                        </td>
                    </tr>
                    <tr>
                        <td align="center" bgcolor="#f5f5f5" style="padding: 20px;">
                            <p style="color: #999999; font-size: 12px;">This is an automated message. Please do not reply.</p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
