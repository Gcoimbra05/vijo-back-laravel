<!DOCTYPE html>
<html lang="en" dir="auto" xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml" xmlns:o="urn:schemas-microsoft-com:office:office">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!--[if mso]> <noscript><xml><o:OfficeDocumentSettings><o:AllowPNG/><o:PixelsPerInch>96</o:PixelsPerInch></o:OfficeDocumentSettings></xml></noscript>
    <![endif]-->
    <!--[if !mso]><!-->
    <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!--<![endif]-->

    <meta name="format-detection" content="telephone=no, date=no, address=no, email=no, url=no">
    <meta name="x-apple-disable-message-reformatting">
    <meta name="color-scheme" content="light dark">
    <meta name="supported-color-schemes" content="light dark">
</head>

<body style="margin: 0; padding: 0; background-color: #F8FCFF; font-family: 'Quicksand', sans-serif;">
    <table cellpadding="0" cellspacing="0" border="0" width="100%">
        <tr>
            <td align="center" class="mobile-padding" style="padding: 40px 20px;">
                <table cellpadding="0" cellspacing="0" border="0" width="100%" style="font-family: 'Segoe UI', 'Trebuchet MS', Verdana, Geneva, sans-serif;">
                    <tr>
                        <td align="center" style="background-color: #ffffff; padding: 40px 20px;">
                            <table cellpadding="0" cellspacing="0" border="0" width="600" style="width: 600px;">
                                <tr>
                                    <td align="center" style="padding-bottom: 30px;">
                                        <table cellpadding="0" cellspacing="0" border="0" style="background-color: #5b67f0; padding: 20px 40px; border-radius: 10px; width: 100%;">
                                            <tr>
                                                <td align="center">
                                                    <span style="color: #ffffff; font-size: 32px; margin: 0; font-weight: 700; display: block;">Vijo ™</span>
                                                    <p style="color: #ffffff; margin: 5px 0 0 0; font-size: 14px;">Your Video Journal</p>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>

                                <tr>
                                    <td>
                                        <table cellpadding="0" cellspacing="0" border="0" width="100%" style="background-color: #ffffff; border: 1px solid #e2e8f0; border-radius: 10px; box-shadow: 0 4px 12px rgba(0,0,0,0.05);">
                                            <tr>
                                                <td style="padding: 40px;">
                                                    @isset($contentView)
                                                        @include($contentView, $contentData ?? [])
                                                    @else
                                                        <p style="font-size: 16px; color: #1A1C1F; line-height: 1.5;">{{ $slot ?? '' }}</p>
                                                    @endisset
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>

                                <tr>
                                    <td align="center" style="padding-top: 30px;">
                                        <p style="color: #a0aec0; font-size: 12px; margin: 0 0 5px 0;">
                                            Need help? Contact <a href="mailto:Admin@Vijo.com" style="color: #5b67f0; text-decoration: none;">Admin@Vijo.com</a>
                                        </p>
                                        <p style="color: #a0aec0; font-size: 12px; margin: 0;">
                                            © 2025 Vijo ™. Build better habits, one Vijo at a time.
                                        </p>
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
