<!DOCTYPE html>
<html>
<body style="margin:0;padding:0;background-color:#faf9f5;font-family:Helvetica,Arial,sans-serif;">
    <table width="100%" cellpadding="0" cellspacing="0" style="padding:32px 0;">
        <tr>
            <td align="center">
                <table width="480" cellpadding="0" cellspacing="0" style="background-color:#ffffff;border-radius:16px;overflow:hidden;">
                    <tr>
                        <td style="background-color:#d97757;padding:24px 32px;">
                            <p style="margin:0;font-size:18px;font-weight:700;color:#ffffff;">Reset Your Password</p>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:32px;">
                            <p style="margin:0 0 20px;font-size:15px;color:#24211c;">Hi {{ $firstName }},</p>
                            <p style="margin:0 0 24px;font-size:15px;color:#24211c;line-height:1.6;">
                                We received a request to reset your InvoiceHub password. Click the button below to choose a new one. This link expires in 60 minutes.
                            </p>

                            <table cellpadding="0" cellspacing="0" style="margin:0 auto 24px;">
                                <tr>
                                    <td style="background-color:#d97757;border-radius:10px;">
                                        <a href="{{ $resetUrl }}" style="display:inline-block;padding:12px 28px;font-size:14px;font-weight:700;color:#ffffff;text-decoration:none;">Reset Password</a>
                                    </td>
                                </tr>
                            </table>

                            <p style="margin:0;font-size:13px;color:#7a7368;line-height:1.6;">
                                If you didn't request this, you can safely ignore this email — your password won't be changed.
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
