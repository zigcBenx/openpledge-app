@extends('emails.layouts.basic')

@section('content')
    <tr>
        <td align="center">
        <table class="t22" role="presentation" cellpadding="0" cellspacing="0" style="Margin-left:auto;Margin-right:auto;">
            <tr>
            <!--[if mso]>
                                                            <td width="514" class="t21" style="border-bottom:1px solid #EFF1F4;width:514px;">
                                                                <![endif]-->
            <!--[if !mso]>-->
            <td class="t21" style="border-bottom:1px solid #EFF1F4;width:514px;">
                <!--
                                                                    <![endif]-->
                <table class="t20" role="presentation" cellpadding="0" cellspacing="0" width="100%" style="width:100%;">
                <tr>
                    <td class="t19" style="padding:0 0 18px 0;">
                    <h1 class="t18" style="margin:0;Margin:0;font-family:Montserrat,BlinkMacSystemFont,Segoe UI,Helvetica Neue,Arial,sans-serif;line-height:28px;font-weight:700;font-style:normal;font-size:24px;text-decoration:none;text-transform:none;letter-spacing:-1px;direction:ltr;color:#141414;text-align:left;mso-line-height-rule:exactly;mso-text-raise:1px;">New Pledge On Your Active Issue!</h1>
                    </td>
                </tr>
                </table>
            </td>
            </tr>
        </table>
        </td>
    </tr>
    <tr>
        <td>
        <div class="t23" style="mso-line-height-rule:exactly;mso-line-height-alt:18px;line-height:18px;font-size:1px;display:block;">&nbsp;&nbsp;</div>
        </td>
    </tr>
    <tr>
        <td align="center">
        <table class="t28" role="presentation" cellpadding="0" cellspacing="0" style="Margin-left:auto;Margin-right:auto;">
            <tr>
            <!--[if mso]>
                                                                <td width="514" class="t27" style="width:514px;">
                                                                    <![endif]-->
            <!--[if !mso]>-->
            <td class="t27" style="width:514px;">
                <!--
                                                                        <![endif]-->
                <table class="t26" role="presentation" cellpadding="0" cellspacing="0" width="100%" style="width:100%;">
                <tr>
                    <td class="t25">
                        <p class="t24" style="margin:0;Margin:0;font-family:Exo,BlinkMacSystemFont,Segoe UI,Helvetica Neue,Arial,sans-serif;line-height:25px;font-weight:400;font-style:normal;font-size:15px;text-decoration:none;text-transform:none;letter-spacing:-0.1px;direction:ltr;color:#141414;text-align:left;mso-line-height-rule:exactly;mso-text-raise:3px;">
                            Dear {{ $resolverName }},
                        </p>
                        <div style="height:10px;line-height:10px;">&nbsp;</div>
                        <p class="t24" style="margin:0;Margin:0;font-family:Exo,BlinkMacSystemFont,Segoe UI,Helvetica Neue,Arial,sans-serif;line-height:25px;font-weight:400;font-style:normal;font-size:15px;text-decoration:none;text-transform:none;letter-spacing:-0.1px;direction:ltr;color:#141414;text-align:left;mso-line-height-rule:exactly;mso-text-raise:3px;">
                            Great news! A new pledge of <b>{{ number_format($amount, 2) }} €</b> has been made on an issue you're actively working on. Your dedication to resolving this issue has caught the attention of our community, and your efforts are being recognized and supported.
                        </p>
                        <div style="height:10px;line-height:10px;">&nbsp;</div>
                        <p class="t24" style="margin:0;Margin:0;font-family:Exo,BlinkMacSystemFont,Segoe UI,Helvetica Neue,Arial,sans-serif;line-height:25px;font-weight:400;font-style:normal;font-size:15px;text-decoration:none;text-transform:none;letter-spacing:-0.1px;direction:ltr;color:#141414;text-align:left;mso-line-height-rule:exactly;mso-text-raise:3px;">
                            We appreciate your commitment to improving the open-source ecosystem. The support from donors helps ensure that your hard work does not go unnoticed.
                        </p>
                        <div style="height:10px;line-height:10px;">&nbsp;</div>
                        <p class="t24" style="margin:0;Margin:0;font-family:Exo,BlinkMacSystemFont,Segoe UI,Helvetica Neue,Arial,sans-serif;line-height:25px;font-weight:400;font-style:normal;font-size:15px;text-decoration:none;text-transform:none;letter-spacing:-0.1px;direction:ltr;color:#141414;text-align:left;mso-line-height-rule:exactly;mso-text-raise:3px;">
                            You can view the issue and the new pledge details <a href="{{ $issueLink }}">here</a>. Keep up the excellent work, and let's continue making a difference together!
                        </p>
                        <div style="height:10px;line-height:10px;">&nbsp;</div>
                        <p class="t24" style="margin:0;Margin:0;font-family:Exo,BlinkMacSystemFont,Segoe UI,Helvetica Neue,Arial,sans-serif;line-height:25px;font-weight:400;font-style:normal;font-size:15px;text-decoration:none;text-transform:none;letter-spacing:-0.1px;direction:ltr;color:#141414;text-align:left;mso-line-height-rule:exactly;mso-text-raise:3px;">
                            Sincerely,<br>The OpenPledge.io Team
                        </p>
                    </td>
                </tr>
                </table>
            </td>
            </tr>
        </table>
        </td>
    </tr>
    <tr>
        <td>
        <div class="t30" style="mso-line-height-rule:exactly;mso-line-height-alt:24px;line-height:24px;font-size:1px;display:block;">&nbsp;&nbsp;</div>
        </td>
    </tr>
@endsection