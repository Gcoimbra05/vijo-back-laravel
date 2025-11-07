<table width="100%" cellpadding="0" cellspacing="0" border="0">
    <tr>
        <td style="font-size: 88px;">👀</td>
    </tr>
    <tr>
        <td style="padding-top: 16px; font-size: 28px; font-weight: 500; line-height: 120%; letter-spacing: 0%;">
            {{ $senderName ?? 'Someone' }} has shared a Vijo with you
        </td>
    </tr>
    <tr>
        <td style="padding-top: 16px; font-size: 16px; font-weight: 500; line-height: 140%;">
            Hi {{ $recipientName ?? '[Name]' }},<br><br>
            {{ $senderName ?? 'Someone' }} has shared a Vijo with you:
        </td>
    </tr>
    <tr>
        <td style="padding-top: 24px;">
            <table cellspacing="0" cellpadding="0" border="0">
                <tr>
                    <td style="background: #4F46E5; padding: 14px 24px; border-radius: 8px; text-align: center;">
                        <a href="{{ $url ?? '#' }}" style="color: #fff; text-decoration: none; font-weight: 600; font-size: 16px; display: block;">
                            VIEW VIJO
                        </a>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
    <tr>
        <td style="padding-top: 24px; font-size: 15px; font-weight: 500; line-height: 140%; color: #1A1C1F;">
            Let Vijo™ help you reflect and navigate emotion, as you get to know your best self.
            <a href="{{ $signUpUrl ?? '#' }}" style="color: #4F46E5; text-decoration: underline;">Sign up today</a>
        </td>
    </tr>
</table>
