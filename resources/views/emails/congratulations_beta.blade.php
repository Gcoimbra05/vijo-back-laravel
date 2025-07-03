<table width="100%" cellpadding="0" cellspacing="0" border="0">
    <tr>
        <td style="font-size: 88px;">✨</td>
    </tr>
    <tr>
        <td style="padding-top: 16px; font-size: 28px; font-weight: 500; line-height: 120%;">
            <strong>Congratulations, <br>
            {{ $recipientName ?? '[First Name]' }}!</strong> You crushed Beta Week 1.
        </td>
    </tr>
    <tr>
        <td style="padding-top: 16px; font-size: 16px; font-weight: 500; line-height: 140%;">
            Hey {{ $recipientName ?? '[First Name]' }},<br><br>
            You completed all 7 days of Beta testing — and we couldn't be more excited to have had you on this journey. 🙌
        </td>
    </tr>
    <tr>
        <td style="padding-top: 16px; font-size: 16px; font-weight: 500; line-height: 140%;">
            Here's your exclusive Premium Code:<br><br>
            <strong>{{ $betaCode ?? 'BETA-WEEK2-ACCESS' }}</strong><br><br>
            (Use this inside the app to unlock special features for Beta Week 2)
        </td>
    </tr>
    <tr>
        <td style="padding-top: 16px; font-size: 16px; font-weight: 500; line-height: 140%;">
            <strong>What's Next?</strong><br><br>
            ✅ Keep using Vijo with your upgraded access.<br><br>
            ✅ Experience new Beta features before anyone else.<br><br>
            ✅ Give us your honest feedback to help us keep improving.
        </td>
    </tr>
    <tr>
        <td style="padding-top: 16px; font-size: 16px; font-weight: 500; line-height: 140%;">
            Your support is literally building the future of Vijo. 🚀
        </td>
    </tr>
    <tr>
        <td style="padding-top: 16px; font-size: 16px; font-weight: 500; line-height: 140%;">
            Ready to unlock your premium access?<br>
            🔑 <a href="{{ $pageUrl ?? '#' }}" style="color: #3E5DFE; text-decoration: none;">Open the App and Enter Your Code</a>
        </td>
    </tr>
    <tr>
        <td style="padding-top: 16px; font-size: 16px; font-weight: 500; line-height: 140%;">
            Thanks for being a true pioneer,<br>
            — The Vijo Team 💙
        </td>
    </tr>
</table>
