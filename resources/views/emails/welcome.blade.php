<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Welcome to SALTIII</title>
</head>
<body style="font-family: Arial, sans-serif; background-color:#f4f4f4; margin:0; padding:0;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0">
        <tr>
            <td align="center" style="padding: 40px 0;">
                <table role="presentation" width="600" cellspacing="0" cellpadding="0" border="0" style="background:#ffffff; border-radius:8px; box-shadow:0 2px 6px rgba(0,0,0,0.1); padding:40px;">
                    <tr>
                        <td style="text-align: left;">
                            <h2 style="color:#007BFF; text-align:center; margin-bottom:10px;">Welcome to SALTIII</h2>
                            <p style="font-size:16px; color:#333;">Hi {{ $user->name }},</p>

                            <p style="font-size:15px; line-height:1.6; color:#555;">
                                We’re excited to have you with us!<br>
                                Your account has been verified and is ready to go.<br>
                                You can log in anytime here:
                            </p>

                            <!-- Centered button only -->
                            <div style="text-align:center; margin: 30px 0;">
                                <a href="{{ url('/login') }}" style="background-color:#007BFF; color:white; padding:12px 25px; text-decoration:none; border-radius:5px; display:inline-block;">
                                    Login to Your Account
                                </a>
                            </div>

                            <p style="font-size:15px; color:#555;">
                                Once inside, you’ll be able to:
                            </p>

                            <ul style="font-size:15px; color:#555; line-height:1.6; margin-left:20px;">
                                <li>Access your dashboard and begin using the all-in-one workflow platform right away.</li>
                                <li>Track your activity and progress as you get familiar with the tools.</li>
                                <li>Find quick answers or tutorials in our Help Center if you ever need assistance.</li>
                            </ul>

                            <p style="font-size:15px; color:#555; line-height:1.6;">
                                If you have any questions, feel free to reach out to our team at 
                                <a href="mailto:support@saltiii.com" style="color:#007BFF;">support@saltiii.com</a>.
                                We’re happy to help.
                            </p>

                            <hr style="border:none; border-top:1px solid #ddd; margin:30px 0;">

                            <p style="font-size:14px; color:#777; text-align:center;">
                                Warm regards,<br>
                                <strong>The SALTIII Team</strong><br>
                                <a href="https://www.saltiii.com" style="color:#007BFF; text-decoration:none;">www.saltiii.com</a> | support@saltiii.com
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
