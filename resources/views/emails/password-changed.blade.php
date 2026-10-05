<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="x-apple-disable-message-reformatting">
    <title>Password Changed — ISU DSS Portal</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&family=Sora:wght@700;800&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body, html { margin: 0 !important; padding: 0 !important; width: 100% !important; background-color: #f8faf9; }
        body {
            background-color: #f8faf9;
            -webkit-font-smoothing: antialiased;
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
        }
        @media only screen and (max-width: 600px) {
            .email-outer { padding: 20px 12px !important; }
            .email-card { padding: 28px 20px !important; }
        }
    </style>
</head>
<body style="margin:0;padding:0;background-color:#f8faf9;">

<table width="100%" cellpadding="0" cellspacing="0" border="0" role="presentation" style="background-color:#f8faf9;">
    <tr>
        <td align="center" class="email-outer" style="padding:48px 16px;">
            <table width="100%" cellpadding="0" cellspacing="0" border="0" role="presentation" style="max-width:480px;width:100%;">
                <tr>
                    <td class="email-card" style="background-color:#ffffff;border:1px solid #e2e8f0;border-radius:6px;padding:36px 40px;box-shadow:0 1px 3px rgba(0,0,0,0.03);">

                        <p style="font-family:'Sora','Plus Jakarta Sans',sans-serif;font-size:15px;font-weight:700;color:#166534;margin:0 0 24px;letter-spacing:-0.01em;">
                            ISU Cauayan DSS Portal
                        </p>

                        <h1 style="font-family:'Sora','Plus Jakarta Sans',sans-serif;font-size:20px;font-weight:700;color:#0f172a;margin:0 0 8px;line-height:1.3;letter-spacing:-0.02em;">
                            Your password was changed
                        </h1>
                        <p style="font-size:14px;color:#475569;margin:0 0 20px;line-height:1.55;">
                            Hi {{ $studentName }}, the password for your canteen evaluation account was changed on {{ $changedAtLabel }} (Philippine time).
                        </p>

                        <p style="font-size:14px;color:#475569;margin:0 0 24px;line-height:1.55;">
                            If you made this change, you don't need to do anything.
                        </p>

                        <div style="border-top:1px solid #f1f5f9;margin-bottom:20px;"></div>

                        <p style="font-size:12px;color:#64748b;margin:0;line-height:1.5;">
                            If you did not change your password, contact the canteen system administrator right away so they can secure your account.
                        </p>

                    </td>
                </tr>
                <tr>
                    <td style="padding:16px 8px 0;text-align:center;">
                        <p style="font-size:11px;color:#64748b;margin:0;">
                            ISU Cauayan Canteen Evaluation System &middot; Automated Email
                        </p>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>

</body>
</html>
