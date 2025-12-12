<table width="100%" cellpadding="0" cellspacing="0" border="0">
    <tr>
        <td style="font-size: 88px;">🔒</td>
    </tr>
    <tr>
        <td style="padding-top: 16px; font-size: 28px; font-weight: 500; line-height: 120%; letter-spacing: 0%;">
            Reset your Vijo™ password.
        </td>
    </tr>
    <tr>
        <td style="padding-top: 16px; font-size: 16px; font-weight: 500; line-height: 140%;">
            Hi {{ $user->first_name ?? '[First Name]' }},<br><br>
            We received a request to reset your password on Vijo™.
        </td>
    </tr>
    <tr>
        <td style="padding-top: 16px; font-size: 16px; font-weight: 500; line-height: 140%;">
            This ís your token: <strong>{{ $token }}</strong>
        </td>

        <td>For your safety, do not share this token with anyone.</td>
    </tr>
    <tr>
        <td style="padding-top: 24px;">
            <table cellspacing="0" cellpadding="0" border="0">
                <tr>
                    <td style="background: #3E5DFE; padding: 12px 24px; border-radius: 8px; text-align: center;">
                        <a href="{{ $redirect_link }}" style="color: #fff; text-decoration: none; font-weight: 700; font-size: 16px; display: block;">
                            Reset Password
                        </a>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
    <tr>
        <td style="padding-top: 24px; font-size: 16px; font-weight: 500; line-height: 140%; color: #1A1C1F;">
            If you did not request this change, please ignore this email.
        </td>
    </tr>
</table>
