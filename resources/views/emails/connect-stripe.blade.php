@extends('emails.layouts.basic')

@section('content')
    <table class="row row-3" align="center" width="100%" border="0" cellpadding="0" cellspacing="0" role="presentation" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt;">
        <tbody>
            <tr>
                <td>
                    <table class="row-content stack" align="center" border="0" cellpadding="0" cellspacing="0" role="presentation" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt; border-radius: 0; color: #000; width: 600px; margin: 0 auto;" width="600">
                        <tbody>
                            <tr>
                                <td class="column column-1" width="100%" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt; font-weight: 400; text-align: left; padding-bottom: 5px; padding-top: 5px; vertical-align: top; border-top: 0px; border-right: 0px; border-bottom: 0px; border-left: 0px;">
                                    <table class="heading_block block-1" width="100%" border="0" cellpadding="10" cellspacing="0" role="presentation" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt;">
                                        <tr>
                                            <td class="pad">
                                                <h1 style="margin: 0; color: #ffffff; direction: ltr; font-family: Arial, 'Helvetica Neue', Helvetica, sans-serif; font-size: 28px; font-weight: 700; letter-spacing: normal; line-height: 120%; text-align: center; margin-top: 0; margin-bottom: 0;">
                                                    <span class="tinyMce-placeholder">Action Required: Connect Your Stripe Account</span>
                                                </h1>
                                            </td>
                                        </tr>
                                    </table>
                                    <table class="paragraph_block block-1" width="100%" border="0" cellpadding="10" cellspacing="0" role="presentation" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt; word-break: break-word;">
                                        <tr>
                                            <td class="pad">
                                                <div style="color:#000000;direction:ltr;font-family:Arial, 'Helvetica Neue', Helvetica, sans-serif;font-size:16px;font-weight:400;letter-spacing:0px;line-height:120%;text-align:left;mso-line-height-alt:19.2px;">
                                                    <p style="margin: 0; margin-bottom: 16px;">Dear {{ $name }},</p>
                                                    <p style="margin: 0; margin-bottom: 16px;">We’ve processed a {{ strtolower($type) }} of <strong>${{ number_format($amount, 2) }}</strong> for you.</p>
                                                    <p style="margin: 0; margin-bottom: 16px;">
                                                        To receive this {{ strtolower($type) }}, please connect your Stripe account as soon as possible.
                                                    </p>
                                                    <p style="margin: 0; margin-bottom: 16px;">
                                                        <strong>Note:</strong> This {{ strtolower($type) }} will only be available for <strong>{{ $holding_days }} days</strong>. If you don’t connect your Stripe account by then, the funds will no longer be accessible.
                                                    </p>
                                                    <p style="margin: 0;">Thank you for being part of our platform. If you have any questions, feel free to contact our support team @ {{ $support_email }}.</p>
                                                    <p style="margin: 0; margin-top: 16px;">Best regards,<br>The OpenPledge.io Team</p>
                                                </div>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </td>
            </tr>
        </tbody>
    </table>
@endsection
