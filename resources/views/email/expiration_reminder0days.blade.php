<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Service Expires Soon</title>
   <style>
     body {
       font-family: 'Arial', sans-serif;
       line-height: 1.5;
     }
   </style>
</head>
<body>
<table width="100%" border="0" cellspacing="0" cellpadding="0">
   <tr>
      <td align="center">
         <table width="600" border="0" cellspacing="0" cellpadding="0" style="border-collapse: collapse;">
            <tr>
               <td align="center" bgcolor="#f5f5f5" style="padding: 40px 0 25px 0;" colspan="3">
                  <a href="https://wdesign.gr"><img src="https://subscriptions.wdesign.gr/images/WDesign%20logo.png" style="max-width: 180px;"></a>
                  <h2 style="color: #333333;"><span style="color:#aa0000">Important!</span> Your service has expired</h2>
               </td>
            </tr>
            <tr>
               <td width="20px" bgcolor="#f5f5f5"></td>
               <td bgcolor="#ffffff" style="padding: 30px 20px;">
                  <p>Αγαπητή/έ κ. {{ $customer_pronunciation }},</p>
                  <p></p>
                  <p>Eπικοινωνούμε μαζί σας για να σας ενημερώσουμε, ότι η υπηρεσία σας με τις παρακάτω πληροφορίες έληξε:</p>
                  <p style="padding-left: 30px;">
                     <b>Τύπος υπηρεσίας: </b> {{ $service_name }}<br/>
                     <b>Domain που αφορά: </b> {{ $domain }}<br/>
                     <b>Ημερομηνία Λήξης: </b> <span style="color:#aa0000">{{ $expired_date }}</span>
                  </p>
                  <p></p>
                  <p>Για να ανανεώσετε την υπηρεσία σας, παρακαλούμε επικοινωνήστε το συντομότερο δυνατό μαζί μας στο <a href="tel:+302661400437">+30 2661 400 437</a>
                     ή στο <a href="mailto:info@wdesign.gr">info@wdesign.gr</a> </p>
                  <p></p>
                  <p>Σας ευχαριστούμε πολύ!</p>
                  <p></p>
                  <p>Με εκτίμηση,</p>
                  <p>
                     Ομάδα Wdesign<br>
                  </p>
               </td>
               <td width="20px" bgcolor="#f5f5f5"></td>
            </tr>
            <tr>
               <td align="center" bgcolor="#f5f5f5" style="padding: 20px;" colspan="3">
                  <p style="color: #999999; font-size: 12px;"><a href="https://wdesign.gr">Wdesign.gr</a> - Web design & Digital marketing<br>
                     N. Λευτεριώτη 18, 49100 Κέρκυρα,
                     <a href="tel:+302661400437">+30 2661 400437</a>,
                     <a href="mailto:info@wdesign.gr">info@wdesign.gr</a>
                  </p>
               </td>
            </tr>
         </table>
      </td>
   </tr>
</table>
</body>
</html>
