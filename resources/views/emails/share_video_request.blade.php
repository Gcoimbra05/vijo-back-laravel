<div style="background: #DBE1FF; border-radius: 16px; padding: 15px 10px; max-width: 328px; margin: 0 auto; font-family: 'Quicksand', Arial, sans-serif; color: #1A1C1F;">
    <div style="font-size: 40px; line-height: 1; margin-bottom: 12px;">👀</div>
    <div style="font-size: 28px; font-weight: 600; margin-bottom: 16px; line-height: 1.1;">
        {{ $senderName ?? 'Someone' }} has shared a Vijo with you
    </div>
    <div style="font-size: 16px; margin-bottom: 8px;">
        Hi {{ $recipientName ?? '[Name]' }},
    </div>
    <div style="font-size: 16px; margin-bottom: 24px;">
        {{ $senderName ?? 'Someone' }} has shared a Vijo with you:
    </div>
    <div style="margin-bottom: 24px;">
        <a href="{{ $url ?? '#' }}" style="display: block; width: 100%; background: #4F46E5; color: #fff; text-align: center; text-decoration: none; font-weight: 600; font-size: 16px; border-radius: 8px; padding: 14px 0;">
            VIEW VIJO
        </a>
    </div>
    <div style="font-size: 15px; color: #1A1C1F;">
        Let Vijo help you reflect and navigate emotion, as you get to know your best self.
        <a href="{{ $signUpUrl ?? '#' }}" style="color: #4F46E5; text-decoration: underline;">Sign up today</a>
    </div>
</div>