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

    <style>
        .otp {
          min-height: 48px;
          background: #3E5DFE;
          padding: 4px 24px;
          border-radius: 8px;
          font-size: 16px;
          font-weight: 700;
          text-align: center;
          color: #fff;
          text-decoration: none;
          display: inline-block;
          margin: 0 auto;
          align-content: center;
        }
    </style>
</head>

<body style="margin: 0; padding: 0; background-color: #F8FCFF; font-family: 'Quicksand', sans-serif;">
  <table width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color: #F8FCFF;">
    <tr>
      <td align="center">
        <!-- Logo -->
        <table width="100%" cellpadding="0" cellspacing="0" border="0" style="max-width: 480px;">
          <tr>
            <td align="center" style="padding: 40px 0 20px;">
              <img src="{{ asset('images/logo_blue.gif') }}" alt="Vijo Logo" width="80" style="display: block;">
            </td>
          </tr>
        </table>

        <!-- Card -->
        <table width="100%" cellpadding="0" cellspacing="0" border="0" style="max-width: 328px; font-weight: 500; color: #1A1C1F;">
          <tr>
            <td style="background-color: #DBE1FF; border-radius: 16px; padding: 24px; text-align: left;">
              <!-- <h3 style="margin-top: 0; font-size: 28px;"> {{ $title }}</h3> -->

              @isset($contentView)
                  @include($contentView, $contentData ?? [])
              @else
                  <p>{{ $slot ?? '' }}</p>
              @endisset
            </td>
          </tr>
        </table>

        <!-- Footer -->
        <table width="100%" cellpadding="0" cellspacing="0" border="0" style="max-width: 480px; margin-top: 40px;">
          <tr>
            <td style="background-color: #DBE1FF; padding: 16px 24px; font-size: 16px; font-weight: 500; color: #495057;">
              <p style="margin: 0;">
                <a href="#" style="color: #495057; text-decoration: none;">Vijo</a> <span style="color: #ACB5BD;">|</span>
                <a href="#" target="_blank" style="color: #495057; text-decoration: none;">Support</a> <span style="color: #ACB5BD;">|</span>
                <a href="#" style="color: #495057; text-decoration: none;">Privacy Policy</a>
              </p>
              <p style="margin: 8px 0 0;">Copyright © 2025 Vijo<br>All rights reserved</p>
            </td>
          </tr>
        </table>

      </td>
    </tr>
  </table>
</body>

</html>
