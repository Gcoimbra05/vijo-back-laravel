<table width="100%" cellpadding="0" cellspacing="0" border="0">
    <tr>
        <td style="font-size: 88px;">🔑</td>
    </tr>
    <tr>
        <td style="padding-top: 16px; font-size: 28px; font-weight: 500; line-height: 120%; letter-spacing: 0%;">
            Your Vijo account verification code.
        </td>
    </tr>
    <tr>
        <td style="padding-top: 16px; font-size: 16px; font-weight: 500; line-height: 140%;">
            Hi {{ $recipientName ?? '[First Name]' }},<br><br>
            To finish account registration, enter this verification code:
        </td>
    </tr>
    <tr>
        <td style="padding-top: 16px;">
            <table cellspacing="0" cellpadding="0" border="0">
                <tr>
                    <td style="min-height: 48px; line-height: 40px; letter-spacing: 5%; text-transform: uppercase; background: #3E5DFE; padding: 8px 32px; border-radius: 8px; font-weight: 700; text-align: center; color: #fff; font-size: 20px;">
                        {{ $verificationCode ?? '123456' }}
                    </td>
                </tr>
            </table>
        </td>
    </tr>
    <tr>
        <td style="padding-top: 16px; font-size: 16px; font-weight: 500; line-height: 140%; color: #1A1C1F;">
            If you didn't make this request or need assistance, just
            <a href="{{ $pageUrl ?? '#' }}" style="color: #3E5DFE; text-decoration: none;">Contact Us</a>.
        </td>
    </tr>
</table>
