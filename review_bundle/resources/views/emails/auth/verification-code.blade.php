<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Email Verification Code</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #f9fafb; padding: 30px;">

<div style="max-width: 500px; margin: auto; background-color: #ffffff; border-radius: 8px; padding: 20px; border: 1px solid #e5e7eb;">
    <h2 style="color: #111827; text-align: center;">Verify Your Email</h2>

    <p style="color: #374151; font-size: 16px;">
        Hello,
    </p>

    <p style="color: #374151; font-size: 16px;">
        Thank you for registering. Please use the following verification code to activate your account:
    </p>

    <div style="text-align: center; margin: 20px 0;">
            <span style="display: inline-block; background-color: #ef4444; color: white; font-size: 24px; font-weight: bold; padding: 10px 20px; border-radius: 6px;">
                {{ $code }}
            </span>
    </div>

    <p style="color: #6b7280; font-size: 14px; text-align: center;">
        This code will expire in 15 minutes.
    </p>
</div>

</body>
</html>
