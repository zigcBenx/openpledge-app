@extends('emails.layouts.basic')

@section('content')
    <table class="row row-3" align="center" width="100%" border="0" cellpadding="0" cellspacing="0" role="presentation" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt;">
        <tbody>
            <tr>
                <td>
                    <table class="row-content stack" align="center" border="0" cellpadding="0" cellspacing="0" role="presentation" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt; color: #000; width: 600px; margin: 0 auto; padding: 20px; border-radius: 8px;" width="600">
                        <tbody>
                            <tr>
                                <td class="column column-1" width="100%" style="padding: 20px 0; text-align: center;">
                                    <h1 style="color: #ffffff; font-family: Arial, 'Helvetica Neue', Helvetica, sans-serif; font-size: 28px; font-weight: 700; text-align: center; margin: 0;">
                                        New Feedback Received
                                    </h1>
                                </td>
                            </tr>
                            <tr>
                                <td class="column column-1" width="100%" style="font-family: Arial, 'Helvetica Neue', Helvetica, sans-serif; color: #ffffff; padding: 20px; text-align: left;">
                                    <p style="font-size: 16px; line-height: 1.5; color: #b9b9b9; margin-bottom: 20px;">
                                        Hello,
                                    </p>
                                    <p style="font-size: 16px; line-height: 1.5; color: #b9b9b9; margin-bottom: 20px;">
                                        we have received new feedback from a user on the platform. Here are the details:
                                    </p>
                                    <p style="font-size: 16px; color: #ffffff; margin-bottom: 10px;">
                                        <strong>User Email:</strong> {{ $email }}
                                    </p>
                                    <p style="font-size: 16px; color: #ffffff; margin-bottom: 20px;">
                                        <strong>Feedback:</strong>
                                    </p>
                                    <div style="background-color: #1a1a1a; color: #e0e0e0; padding: 15px; border-radius: 5px; margin-bottom: 20px;">
                                        {{ $content }}
                                    </div>

                                    <p style="font-size: 16px; line-height: 1.5; color: #b9b9b9; margin-bottom: 20px;">
                                        Best regards,<br>
                                        The OpenPledge.io Team
                                    </p>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </td>
            </tr>
        </tbody>
    </table>
@endsection
