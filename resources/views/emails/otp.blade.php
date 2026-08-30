<!DOCTYPE html>
<html lang="en" xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="x-apple-disable-message-reformatting">
    <title>Verification Code — ISU DSS Portal</title>
    <!-- Web Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&family=Sora:wght@700&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body, html { margin: 0 !important; padding: 0 !important; width: 100% !important; background-color: #f8faf9; }
        body {
            background-color: #f8faf9;
            -webkit-font-smoothing: antialiased;
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
        }
        .font-display { font-family: 'Sora', 'Plus Jakarta Sans', -apple-system, sans-serif; }
        @media only screen and (max-width: 600px) {
            .email-outer { padding: 20px 12px !important; }
            .email-card { padding: 28px 20px !important; }
            .otp-code { font-size: 32px !important; letter-spacing: 10px !important; padding: 16px 12px !important; }
        }
    </style>
</head>
<body style="margin:0;padding:0;background-color:#f8faf9;">

<table width="100%" cellpadding="0" cellspacing="0" border="0" role="presentation" style="background-color:#f8faf9;">
    <tr>
        <td align="center" class="email-outer" style="padding:48px 16px;">
            <table width="100%" cellpadding="0" cellspacing="0" border="0" role="presentation" style="max-width:480px;width:100%;">
                
                <!-- Card Container -->
                <tr>
                    <td class="email-card" style="background-color:#ffffff;border:1px solid #e2e8f0;border-radius:6px;padding:36px 40px;box-shadow:0 1px 3px rgba(0,0,0,0.03);">
                        
                        <!-- Header / Brand -->
                        <table width="100%" cellpadding="0" cellspacing="0" border="0" role="presentation" style="margin-bottom:24px;">
                            <tr>
                                <td>
                                    <p class="font-display" style="font-family:'Sora','Plus Jakarta Sans',sans-serif;font-size:15px;font-weight:700;color:#166534;margin:0;letter-spacing:-0.01em;">
                                        ISU Cauayan DSS
                                    </p>
                                </td>
                            </tr>
                        </table>

                        <!-- Heading & Copy -->
                        <h1 class="font-display" style="font-family:'Sora','Plus Jakarta Sans',sans-serif;font-size:20px;font-weight:700;color:#0f172a;margin:0 0 8px;line-height:1.3;letter-spacing:-0.02em;">
                            Verify your email
                        </h1>
                        <p style="font-size:14px;color:#475569;margin:0 0 24px;line-height:1.55;font-weight:400;">
                            Hi {{ $studentName }}, use this code to complete your registration for the Canteen Evaluation Portal:
                        </p>

                        <!-- Code Display Box -->
                        <table width="100%" cellpadding="0" cellspacing="0" border="0" role="presentation" style="margin-bottom:20px;">
                            <tr>
                                <td align="center">
                                    <div class="otp-code" style="background-color:#f0fdf4;border:1px solid #bbf7d0;border-radius:4px;padding:18px 24px;text-align:center;font-size:34px;font-weight:700;letter-spacing:10px;color:#15803d;font-family:'Sora',ui-monospace,SFMono-Regular,Consolas,monospace;user-select:all;-webkit-user-select:all;cursor:pointer;">{{ $otp }}</div>
                                </td>
                            </tr>
                        </table>

                        <!-- Minimal Instruction -->
                        <p style="font-size:12px;color:#64748b;margin:0 0 24px;text-align:center;">
                            Click to select &amp; copy code. Valid for 15 minutes.
                        </p>

                        <!-- Subtle Divider -->
                        <div style="border-top:1px solid #f1f5f9;margin-bottom:20px;"></div>

                        <!-- Footer / Security Note -->
                        <p style="font-size:12px;color:#94a3b8;margin:0;line-height:1.5;">
                            If you didn't create an account, you can safely ignore this email.
                        </p>

                    </td>
                </tr>

                <!-- Micro Footer -->
                <tr>
                    <td style="padding:16px 8px 0;text-align:center;">
                        <p style="font-size:11px;color:#94a3b8;margin:0;">
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
