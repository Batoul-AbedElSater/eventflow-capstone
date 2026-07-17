<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Invitation - {{ $event->name }}</title>
</head>

<body style="margin:0; padding:0; background:#EFE7DA; font-family:'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;">

<table width="100%" cellpadding="0" cellspacing="0" border="0" style="background:#EFE7DA; padding:45px 15px;">
<tr>
<td align="center">

<!-- CARD -->
<table width="620" cellpadding="0" cellspacing="0" border="0"
style="background:#FFFFFF; border-radius:24px; overflow:hidden; box-shadow:0 18px 50px rgba(0,0,0,0.18);">

<!-- HERO -->
<tr>
<td style="background: linear-gradient(135deg, #C63E4E 0%, #C63E4E 100%);
           padding:15px 10px; text-align:center;">

    <h1 style="margin:18px 0 10px;font-style: italic; font-size:30px;font-family:'DM Serif Display',serif; color:#fff; font-weight:900;">
        Something Beautiful Awaits
    </h1>

</td>
</tr>

<!-- BODY -->
<tr>
<td style="padding:42px;">

    <!-- INTRO -->
    <p style="font-size:18px; color:#2C3E50; margin:2px 0 5px 0;">
        Dear <strong>{{ $guest->name }}</strong>,
    </p>

    <p style="font-size:14px; color:#666; margin:0 0 24px; line-height:1.5;">

        You are warmly invited to join us for a carefully crafted experience filled with moments, memories, and meaning.
    </p>

    <!-- EVENT TITLE BLOCK -->
    <div style="text-align:center; margin-bottom:20px;">

        <h2 style="margin:10px 0 0; font-size:28px; color:#620607; font-weight:900;">
            {{ $event->name }}
        </h2>

    </div>

    <!-- DETAILS BLOCK -->
    <table width="100%" cellpadding="10" cellspacing="0"
           style="background:#F8F9FA; border-radius:16px; padding:20px;">

        <tr>
            <td style="color:#888; font-weight:600;">Type</td>
            <td style="color:#2C3E50; font-weight:700;">
                {{ $event->eventType->name }}
            </td>
        </tr>

        <tr>
            <td style="color:#888; font-weight:600;">Date</td>
            <td style="color:#2C3E50; font-weight:700;">
                {{ $event->start_date->format('l, F j, Y') }}
            </td>
        </tr>

        @if($event->start_time)
        <tr>
            <td style="color:#888; font-weight:600;">Time</td>
            <td style="color:#2C3E50; font-weight:700;">
                {{ date('g:i A', strtotime($event->start_time)) }}
            </td>
        </tr>
        @endif

        <tr>
            <td style="color:#888; font-weight:600;">Location</td>
            <td style="color:#2C3E50; font-weight:700;">
                {{ $event->location_text }}
            </td>
        </tr>

        @if($guest->plus_one_allowed)
        <tr>
            <td style="color:#888; font-weight:600;">Plus One</td>
            <td style="color:#C63E4E; font-weight:800;">
                 Welcome to bring someone special
            </td>
        </tr>
        @endif

    </table>

    <!-- CTA -->
    <div style="text-align:center; margin:30px 0 20px;">

        <a href="{{ $rsvpUrl }}"
           style="display:inline-block;
                  padding:15px 20px;
                  background:#C63E4E;
                  color:#fff;
                  text-decoration:none;
                  border-radius:60px;
                  font-size:14px;
                  font-weight:600;
                  box-shadow:0 12px 30px rgba(198,62,78,0.35);">

            Reserve Your Spot

        </a>

    </div>

    <!-- SIGNATURE -->
    <div style="margin-top:30px;">
        <p style="margin:0; font-size:15px; color:#2C3E50;">
            With love,
        </p>

        <p style="margin:5px 0 0; font-size:16px; font-weight:800; color:#620607;">
            {{ $event->client->name }}
        </p>
    </div>

</td>
</tr>

<!-- FOOTER -->
<tr>
<td style="background:#F8F9FA; text-align:center; padding:25px;">

    <div style="font-size:16px; font-weight:800; color:#620607;">
        Plano-eve
    </div>

    <div style="font-size:12px; color:#777; margin-top:5px;">
        Crafted with care for meaningful events
    </div>

    <div style="font-size:11px; color:#AAA; margin-top:8px;">
        This is an automated invitation email.
    </div>

</td>
</tr>

</table>

</td>
</tr>
</table>

</body>
</html>
