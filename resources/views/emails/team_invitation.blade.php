<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Team Invitation</title>
</head>
<body style="margin:0; padding:0; background:#f4f6f8; font-family:Arial, sans-serif; color:#222;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0">
        <tr>
            <td align="center" style="padding:32px 16px;">
                <table role="presentation" width="600" cellspacing="0" cellpadding="0" border="0" style="max-width:600px; background:#ffffff; border-radius:8px; padding:32px;">
                    <tr>
                        <td>
                            <h2 style="margin:0 0 12px; color:#1f2937;">You are invited to join {{ $invitation->group->name }}</h2>
                            <p style="margin:0 0 18px; line-height:1.6; color:#4b5563;">
                                {{ $invitation->inviter->name ?? 'A teammate' }} invited you to join their SALTIII team group for shared team access and billing.
                            </p>

                            <p style="margin:0 0 24px; line-height:1.6; color:#4b5563;">
                                This invitation was sent to <strong>{{ $invitation->email }}</strong>
                                @if($invitation->expires_at)
                                    and expires on <strong>{{ $invitation->expires_at->format('M d, Y') }}</strong>.
                                @endif
                            </p>

                            <p style="margin:0 0 28px;">
                                <a href="{{ $inviteLink }}" style="display:inline-block; background:#3577f1; color:#ffffff; text-decoration:none; padding:12px 20px; border-radius:6px; font-weight:bold;">
                                    Accept Invitation
                                </a>
                            </p>

                            <p style="margin:0 0 8px; font-size:13px; color:#6b7280;">If the button does not work, copy and paste this link into your browser:</p>
                            <p style="margin:0; font-size:13px; line-height:1.5; word-break:break-all; color:#3577f1;">{{ $inviteLink }}</p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
