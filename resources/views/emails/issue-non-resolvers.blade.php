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
                                                <h1 style="margin: 0; color: #ffffff; direction: ltr; font-family: Arial, 'Helvetica Neue', Helvetica, sans-serif; font-size: 32px; font-weight: 700; letter-spacing: normal; line-height: 120%; text-align: center; margin-top: 0; margin-bottom: 0;"><span class="tinyMce-placeholder">Issue Resolved by Another Contributor</span></h1>
                                            </td>
                                        </tr>
                                    </table>
                                    <table class="paragraph_block block-1" width="100%" border="0" cellpadding="10" cellspacing="0" role="presentation" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt; word-break: break-word;">
                                        <tr>
                                            <td class="pad">
                                                <div style="color:#ffffff;direction:ltr;font-family:Arial, 'Helvetica Neue', Helvetica, sans-serif;font-size:16px;font-weight:400;letter-spacing:0px;line-height:120%;text-align:center;mso-line-height-alt:19.2px;">
                                                    <p style="margin: 0; margin-bottom: 16px;">Dear {{ $resolverName }},</p>
                                                    <p style="margin: 0; margin-bottom: 16px;">We wanted to inform you that an issue you were actively working on has been resolved by another contributor. While you may not have been the one to resolve this particular issue, your efforts and contributions are greatly appreciated.</p>
                                                    <p style="margin: 0; margin-bottom: 16px;">We encourage you to explore other active issues that might need your expertise. Your continued involvement is vital to the success of the open-source community.</p>
                                                    <p style="margin: 0;">You can view other active issues <a href="{{ $discoverLink }}">here</a>. We hope you’ll find another project that inspires you.</p>
                                                    <p style="margin: 0; margin-top: 16px;">Thank you for your ongoing contributions!<br>The OpenPledge.io Team</p>
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