{{--
  Marine Technical Solutions — транзакционные письма (строгие углы, палитра как на сайте).
--}}
@php
    $logoUrl = config('marine.mail_logo_url');
    $siteUrl = config('marine.mail_site_url');
@endphp
<!DOCTYPE html>
<html lang="ru" xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="x-apple-disable-message-reformatting">
    <title>@yield('email_title', config('app.name'))</title>
    <!--[if mso]>
    <noscript>
        <xml>
            <o:OfficeDocumentSettings>
                <o:PixelsPerInch>96</o:PixelsPerInch>
            </o:OfficeDocumentSettings>
        </xml>
    </noscript>
    <![endif]-->
</head>
<body style="margin:0;padding:0;background-color:#f8f9fa;-webkit-text-size-adjust:100%;-ms-text-size-adjust:100%;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,'Helvetica Neue',Arial,sans-serif;">
@hasSection('preheader')
    <div style="display:none;max-height:0;overflow:hidden;opacity:0;">
        @yield('preheader')
    </div>
@endif
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="background-color:#f8f9fa;">
        <tr>
            <td align="center" style="padding:32px 16px;">
                <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="max-width:600px;margin:0 auto;border-collapse:collapse;">
                    <tr>
                        <td align="center" style="border-top:4px solid #c14041;background-color:#ffffff;padding:24px 28px 20px 28px;text-align:center;border-left:1px solid #dee2e6;border-right:1px solid #dee2e6;">
                            @if($logoUrl)
                                @if($siteUrl)
                                    <a href="{{ $siteUrl }}" target="_blank" rel="noopener noreferrer" style="display:inline-block;text-decoration:none;">
                                        <img src="{{ $logoUrl }}" alt="{{ config('app.name') }}" width="168" style="display:block;max-width:168px;width:168px;height:auto;margin:0 auto;border:0;outline:none;-ms-interpolation-mode:bicubic;" />
                                    </a>
                                @else
                                    <img src="{{ $logoUrl }}" alt="{{ config('app.name') }}" width="168" style="display:block;max-width:168px;width:168px;height:auto;margin:0 auto;border:0;outline:none;-ms-interpolation-mode:bicubic;" />
                                @endif
                            @else
                                <p style="margin:0 0 4px 0;font-family:Consolas,'Courier New',monospace;font-size:10px;letter-spacing:0.2em;text-transform:uppercase;color:#c14041;font-weight:600;">
                                    {{ config('app.name') }}
                                </p>
                            @endif
                            <h1 style="margin:20px 0 0 0;font-family:Georgia,'Times New Roman',serif;font-size:22px;line-height:1.3;font-weight:600;color:#1c1c1e;letter-spacing:-0.02em;text-align:center;">
                                @yield('heading')
                            </h1>
                        </td>
                    </tr>
                    <tr>
                        <td style="background-color:#ffffff;border-left:1px solid #dee2e6;border-right:1px solid #dee2e6;">
                            @yield('content')
                        </td>
                    </tr>
                    <tr>
                        <td style="background-color:#f8f9fa;border:1px solid #dee2e6;border-top:none;padding:20px 28px;text-align:center;">
                            <p style="margin:0 0 4px 0;font-size:11px;line-height:1.5;color:#6c757d;">
                                @yield('footer_signature')
                            </p>
                            <p style="margin:0;font-family:Consolas,'Courier New',monospace;font-size:10px;letter-spacing:0.12em;text-transform:uppercase;color:#adb5bd;">
                                {{ config('app.name') }}
                            </p>
                            @yield('footer_extra')
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
