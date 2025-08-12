<!doctype html>
<html lang="en" dir="ltr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>{{ $appName }} – Entity Type Decision</title>
</head>
<body style="margin:0;background:#f6f7f9;font-family:system-ui,-apple-system,Segoe UI,Roboto,Helvetica,Arial,sans-serif;color:#111">
<table role="presentation" cellpadding="0" cellspacing="0" width="100%">
    <tr>
        <td align="center" style="padding:24px">
            <table role="presentation" cellpadding="0" cellspacing="0" style="width:100%;max-width:560px;background:#fff;border:1px solid #e5e7eb;border-radius:16px;overflow:hidden">
                <tr>
                    <td style="padding:20px 24px;background:linear-gradient(135deg,#ec4899,#8b5cf6);color:#fff">
                        <div style="font-size:18px;font-weight:700">{{ $appName }}</div>
                        <div style="font-size:13px;opacity:.9">Profile entity type update</div>
                    </td>
                </tr>

                @if($decision === 'approved')
                <tr>
                    <td style="padding:20px 24px">
                        <h1 style="margin:0 0 8px 0;font-size:18px">Hi {{ $userName }},</h1>
                        <p style="margin:0 0 12px 0;font-size:14px;line-height:1.6">
                            Your request to change your profile type has been <strong>approved</strong>.
                            Your profile is now set to <strong>{{ ucfirst($requestedType) }}</strong>.
                        </p>
                        <p style="margin:0 0 12px 0;font-size:14px;line-height:1.6">
                            Manage your profile here:
                            <a href="{{ $profileUrl }}" style="color:#2563eb;text-decoration:none">{{ $profileUrl }}</a>
                        </p>
                    </td>
                </tr>
                <tr>
                    <td style="padding:0 24px 16px 24px"><hr style="border:none;border-top:1px solid #e5e7eb"></td>
                </tr>
                <tr>
                    <td style="padding:0 24px 20px 24px" dir="rtl">
                        <h2 style="margin:0 0 8px 0;font-size:16px;font-weight:700">سلام {{ $userName }}،</h2>
                        <p style="margin:0 0 12px 0;font-size:14px;line-height:1.9">
                            درخواست شما برای تغییر نوع پروفایل <strong>تأیید</strong> شد.
                            اکنون نوع پروفایل شما <strong>{{ $requestedType }}</strong> است.
                        </p>
                        <p style="margin:0 0 12px 0;font-size:14px;line-height:1.9">
                            برای مدیریت پروفایل:
                            <a href="{{ $profileUrl }}" style="color:#2563eb;text-decoration:none" dir="ltr">{{ $profileUrl }}</a>
                        </p>
                    </td>
                </tr>
                @else
                <tr>
                    <td style="padding:20px 24px">
                        <h1 style="margin:0 0 8px 0;font-size:18px">Hi {{ $userName }},</h1>
                        <p style="margin:0 0 12px 0;font-size:14px;line-height:1.6">
                            Your request to change your profile type was <strong>not approved</strong> at this time.
                        </p>
                        <p style="margin:0 0 12px 0;font-size:14px;line-height:1.6">
                            You can review and update your details here:
                            <a href="{{ $profileUrl }}" style="color:#2563eb;text-decoration:none">{{ $profileUrl }}</a>
                            or contact support if you have questions.
                        </p>
                    </td>
                </tr>
                <tr>
                    <td style="padding:0 24px 16px 24px"><hr style="border:none;border-top:1px solid #e5e7eb"></td>
                </tr>
                <tr>
                    <td style="padding:0 24px 20px 24px" dir="rtl">
                        <h2 style="margin:0 0 8px 0;font-size:16px;font-weight:700">سلام {{ $userName }}،</h2>
                        <p style="margin:0 0 12px 0;font-size:14px;line-height:1.9">
                            درخواست شما برای تغییر نوع پروفایل در حال حاضر <strong>تأیید نشد</strong>.
                        </p>
                        <p style="margin:0 0 12px 0;font-size:14px;line-height:1.9">
                            می‌توانید اطلاعات را بازبینی کنید:
                            <a href="{{ $profileUrl }}" style="color:#2563eb;text-decoration:none" dir="ltr">{{ $profileUrl }}</a>
                            یا برای راهنمایی با پشتیبانی در تماس باشید.
                        </p>
                    </td>
                </tr>
                @endif

                <tr>
                    <td align="center" style="padding:16px 24px 24px 24">
                        <a href="{{ $profileUrl }}" style="display:inline-block;padding:10px 16px;background:#111827;color:#fff;border-radius:10px;text-decoration:none;font-weight:600">Open Profile</a>
                    </td>
                </tr>

                <tr>
                    <td style="padding:16px 24px;background:#f9fafb;font-size:12px;color:#6b7280;text-align:center">
                        Need help? <a href="{{ $supportUrl }}" style="color:#2563eb;text-decoration:none">Contact support</a><br>
                        © {{ date('Y') }} {{ $appName }}
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
</body>
</html>
