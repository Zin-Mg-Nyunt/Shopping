<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>{{ config('app.name') }} – Your verification code</title>
    <style>
        body, table, td, p, a { -webkit-text-size-adjust: 100%; }
        table, td { mso-table-lspace: 0; mso-table-rspace: 0; }
        img { border: 0; height: auto; line-height: 100%; outline: none; text-decoration: none; }
        body { margin: 0 !important; padding: 0 !important; width: 100% !important; }
        @media screen and (max-width: 600px) {
            .wrapper { width: 100% !important; max-width: 100% !important; }
            .code-cell { font-size: 28px !important; }
        }
    </style>
</head>
<body style="margin: 0; padding: 0; background-color: #f1f5f9; font-family: 'Instrument Sans', ui-sans-serif, system-ui, sans-serif;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="background-color: #f1f5f9;">
        <tr>
            <td align="center" style="padding: 40px 20px;">
                <table role="presentation" class="wrapper" width="600" cellspacing="0" cellpadding="0" border="0" style="max-width: 600px; margin: 0 auto; background-color: #ffffff; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.08);">
                    <tr>
                        <td style="padding: 40px 32px;">
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0">
                                <tr>
                                    <td style="padding-bottom: 24px;">
                                        <h1 style="margin: 0; font-size: 20px; font-weight: 600; color: #0f172a;">
                                            {{ config('app.name') }}
                                        </h1>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding-bottom: 8px;">
                                        <h2 style="margin: 0; font-size: 18px; font-weight: 600; color: #0f172a;">
                                            Your verification code
                                        </h2>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding-bottom: 24px; font-size: 15px; line-height: 1.5; color: #64748b;">
                                        Use the code below to verify your identity and continue with your password reset.
                                    </td>
                                </tr>
                                <tr>
                                    <td align="center" style="padding: 24px 0;">
                                        <table role="presentation" align="center" cellspacing="0" cellpadding="0" border="0" style="background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px;">
                                            <tr>
                                                <td style="padding: 20px 32px;">
                                                    <span class="code-cell" style="font-size: 32px; font-weight: 700; letter-spacing: 0.25em; color: #0f172a; font-variant-numeric: tabular-nums;">{{ str_pad((string) $otpCode, 6, '0', STR_PAD_LEFT) }}</span>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding-top: 8px; padding-bottom: 16px; font-size: 13px; line-height: 1.6; color: #64748b;">
                                        <strong style="color: #0f172a;">Please note:</strong><br>
                                        • This code will expire after 3 minutes.<br>
                                        • Do not share this code with anyone. We will never ask for it by phone, email, or any other channel.<br>
                                        • If you didn't request this code, you can safely ignore this email.
                                    </td>
                                </tr>
                                <tr>
                                    <td style="font-size: 13px; line-height: 1.5; color: #94a3b8;">
                                        Need a new code? You can request another one from the password reset page after this code expires.
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding-top: 24px; font-size: 13px; line-height: 1.5; color: #94a3b8;">
                                        Thanks,<br>
                                        The {{ config('app.name') }} team
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
