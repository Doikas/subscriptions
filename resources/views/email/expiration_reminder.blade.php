<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Service Expiration Reminder</title>
</head>
<body>
    <div>
        <p>Dear {{ $customer_pronunciation }},</p>
        <p>Your service for {{ $service_name }} is expiring soon. Please take note of the following details:</p>
        <ul>
            <li>Service Name: {{ $service_name }}</li>
            <li>Expired Date: {{ $expired_date }}</li>
        </ul>
        <p>Please contact us for payment options to renew your service.</p>
        <p>Thank you!</p>
    </div>
</body>
</html>