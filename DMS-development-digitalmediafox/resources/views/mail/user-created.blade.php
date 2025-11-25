<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome Email</title>
</head>

<body style="margin:0; padding:0; background:#f5f7fa; font-family:Arial, sans-serif;">

    <table width="100%" cellpadding="0" cellspacing="0" style="background:#f5f7fa; padding:40px 0;">
        <tr>
            <td align="center">

                <!-- Main Card -->
                <table width="600" cellpadding="0" cellspacing="0" style="background:white; border-radius:10px; box-shadow:0 2px 10px rgba(0,0,0,0.08); overflow:hidden;">

                    <!-- Header -->
                    <tr>
                        <td style="background:#722c81; padding:25px; text-align:center;">
                            <h1 style="color:white; margin:0; font-size:24px;">Welcome to Our Portal</h1>
                        </td>
                    </tr>

                    <!-- Body -->
                    <tr>
                        <td style="padding:30px; color:#333; font-size:15px; line-height:1.6;">

                            <p style="font-size:18px; font-weight:bold; color:#222; margin-bottom:10px;">
                                Hello, {{ $employee->name }}
                            </p>

                            <p style="margin-bottom:20px;">
                                Your account has been successfully created. Below are your secure login details:
                            </p>

                            <!-- Credentials Box -->
                            <table width="100%" style="background:#f1f5f9; border-radius:6px; padding:15px; margin:20px 0;">
                                <tr>
                                    <td style="padding:10px 0;">
                                        <strong>Email:</strong> {{ $employee->email }}
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding:10px 0;">
                                        <strong>Password:</strong> {{ $plainPassword }}
                                    </td>
                                </tr>
                            </table>

                            <p style="margin-bottom:25px;">
                                Click the button below to access your dashboard:
                            </p>

                            <!-- Button -->
                            <p style="text-align:center; margin:30px 0;">
                                <a href="{{ route('login') }}" 
                                   style="background:#722c81; color:white; padding:14px 28px; text-decoration:none; 
                                          border-radius:6px; font-weight:bold; display:inline-block; font-size:16px;">
                                    Login Now
                                </a>
                            </p>

                            <p style="margin-top:30px;">
                                If you did not request this account, please contact support immediately.
                            </p>

                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="background:#f1f5f9; padding:15px; text-align:center; font-size:13px; color:#666;">
                            © {{ date('Y') }} Speed Logistics. All rights reserved.
                        </td>
                    </tr>

                </table>
                <!-- End Main Card -->

            </td>
        </tr>
    </table>

</body>
</html>
