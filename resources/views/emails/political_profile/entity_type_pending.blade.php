<!doctype html>
<html lang="en" dir="ltr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>{{ $appName }} – Entity Type Request</title>
</head>
<body style="margin:0;background:#f6f7f9;font-family:system-ui,-apple-system,Segoe UI,Roboto,Helvetica,Arial,sans-serif;color:#111">
<div style="display:none;max-height:0;overflow:hidden;opacity:0;color:transparent">{{ $preheader }}</div>
<table role="presentation" cellpadding="0" cellspacing="0" width="100%">
    <tr>
        <td align="center" style="padding:24px">
            <table role="presentation" cellpadding="0" cellspacing="0" style="width:100%;max-width:640px;background:#fff;border:1px solid #e5e7eb;border-radius:18px;overflow:hidden">
                <tr>
                    <td style="padding:28px 24px;background:linear-gradient(135deg,{{ $brand['primary'] }},#8b5cf6);color:#fff;text-align:center">
                        <img alt="{{ $appName }}" src="{{ $logoUrl }}" width="48" height="48" style="display:inline-block;border:0;outline:0;border-radius:12px;background:#fff;padding:6px">
                        <div style="font-size:22px;font-weight:800;margin-top:10px">{{ $appName }}</div>
                        <div style="font-size:13px;opacity:.95;margin-top:4px">Entity type approval needed</div>
                    </td>
                </tr>

                <tr>
                    <td style="padding:22px 26px">
                        <h1 style="margin:0 0 10px 0;font-size:18px;line-height:1.4">Hi {{ $userName }},</h1>
                        <p style="margin:0 0 12px 0;font-size:14px;line-height:1.75">
                            We received your request to change your profile type to <strong>{{ $requestedEn }}</strong>.
                            This change requires a quick review. Your current type remains <strong>{{ $currentEn }}</strong> until approval.
                        </p>
                        <p style="margin:0 0 14px 0;font-size:14px;line-height:1.75">
                            You can review or update your details here:
                            <a href="{{ $profileUrl }}" style="color:#2563eb;text-decoration:none">{{ $profileUrl }}</a>
                        </p>
                        <table role="presentation" cellpadding="0" cellspacing="0" style="margin:16px 0 8px 0">
                            <tr>
                                <td>
                                    <a href="{{ $profileUrl }}" style="display:inline-block;background:{{ $brand['accent'] }};color:#fff;border-radius:12px;font-weight:700;text-decoration:none;padding:12px 18px">Open Profile</a>
                                </td>
                            </tr>
                        </table>
                        <p style="margin:8px 0 0 0;font-size:12px;color:#6b7280">If you didn’t make this request, please contact support.</p>
                    </td>
                </tr>

                <tr>
                    <td style="padding:0 26px 8px 26px">
                        <hr style="border:none;border-top:1px solid #e5e7eb">
                    </td>
                </tr>

                <tr>
                    <td style="padding:16px 26px" dir="rtl">
                        <h2 style="margin:0 0 8px 0;font-size:16px;font-weight:800">سلام {{ $userName }}،</h2>
                        <p style="margin:0 0 12px 0;font-size:14px;line-height:2">
                            درخواست شما برای تغییر نوع پروفایل به <strong>{{ $requestedFa }}</strong> ثبت شد.
                            این تغییر نیاز به بررسی دارد. تا زمان تأیید، نوع فعلی پروفایل شما <strong>{{ $currentFa }}</strong> باقی می‌ماند.
                        </p>
                        <p style="margin:0 0 14px 0;font-size:14px;line-height:2">
                            برای مشاهده یا ویرایش اطلاعات، از این لینک استفاده کن:
                            <a href="{{ $profileUrl }}" style="color:#2563eb;text-decoration:none" dir="ltr">{{ $profileUrl }}</a>
                        </p>
                        <table role="presentation" cellpadding="0" cellspacing="0" style="margin:16px 0 8px 0" dir="ltr">
                            <tr>
                                <td>
                                    <a href="{{ $profileUrl }}" style="display:inline-block;background:{{ $brand['primary'] }};color:#fff;border-radius:12px;font-weight:800;text-decoration:none;padding:12px 18px">مشاهده پروفایل</a>
                                </td>
                            </tr>
                        </table>
                        <p style="margin:8px 0 0 0;font-size:12px;color:#6b7280" dir="rtl">اگر این درخواست توسط شما انجام نشده، با پشتیبانی تماس بگیر.</p>
                    </td>
                </tr>

                <tr>
                    <td style="padding:0 26px 12px 26px">
                        <hr style="border:none;border-top:1px solid #e5e7eb">
                    </td>
                </tr>

                <tr>
                    <td style="padding:18px 26px">
                        <table role="presentation" cellpadding="0" cellspacing="0" width="100%" style="border:1px solid #e5e7eb;border-radius:14px;overflow:hidden">
                            <tr>
                                <td style="padding:16px;background:linear-gradient(135deg,{{ $brand['primary'] }}20,#ffffff)">
                                    <table role="presentation" cellpadding="0" cellspacing="0" width="100%">
                                        <tr>
                                            <td style="vertical-align:middle">
                                                <div style="font-size:14px;font-weight:800;margin:0 0 4px 0">Discover Linxx</div>
                                                <div style="font-size:13px;color:#4b5563;margin:0">Create organizations, run campaigns, and publish books with one profile.</div>
                                            </td>
                                            <td align="right" style="vertical-align:middle;white-space:nowrap">
                                                <a href="{{ $homeUrl }}" style="display:inline-block;border:1px solid #e5e7eb;border-radius:12px;padding:10px 14px;text-decoration:none;color:#111827;font-weight:700">Explore</a>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>

                <tr>
                    <td style="padding:14px 26px;background:#f9fafb;text-align:center;font-size:12px;color:#6b7280">
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
