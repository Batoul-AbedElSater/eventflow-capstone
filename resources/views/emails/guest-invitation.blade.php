<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>You're Invited to {{ $event->name }}</title>
</head>
<body style="margin: 0; padding: 0; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #EFE7DA;">
    
    <table width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color: #EFE7DA; padding: 40px 20px;">
        <tr>
            <td align="center">
                
                <!-- Main Container -->
                <table width="600" cellpadding="0" cellspacing="0" border="0" style="background-color: #ffffff; border-radius: 20px; overflow: hidden; box-shadow: 0 10px 40px rgba(0,0,0,0.15);">
                    
                    <!-- Header -->
                    <tr>
                        <td style="background: linear-gradient(135deg, #475B35 0%, #2C3821 100%); padding: 50px 40px; text-align: center;">
                            <h1 style="margin: 0; color: #ffffff; font-size: 42px; font-weight: 900; text-shadow: 0 2px 10px rgba(0,0,0,0.3);">
                                🎉 You're Invited!
                            </h1>
                            <p style="margin: 15px 0 0 0; color: rgba(255,255,255,0.9); font-size: 18px;">
                                Join us for a special celebration
                            </p>
                        </td>
                    </tr>
                    
                    <!-- Content -->
                    <tr>
                        <td style="padding: 40px;">
                            
                            <p style="margin: 0 0 25px 0; font-size: 18px; color: #2C3E50; line-height: 1.6;">
                                Dear <strong>{{ $guest->name }}</strong>,
                            </p>
                            
                            <p style="margin: 0 0 30px 0; font-size: 16px; color: #555555; line-height: 1.8;">
                                You are cordially invited to celebrate with us at:
                            </p>
                            
                            <!-- Event Details Box -->
                            <table width="100%" cellpadding="0" cellspacing="0" border="0" style="background: linear-gradient(135deg, #EFE7DA 0%, #F5F5DC 100%); border-radius: 15px; padding: 30px; margin-bottom: 30px;">
                                <tr>
                                    <td>
                                        <h2 style="margin: 0 0 20px 0; color: #475B35; font-size: 28px; font-weight: 800; text-align: center;">
                                            {{ $event->name }}
                                        </h2>
                                        
                                        <!-- Event Type -->
                                        <table width="100%" cellpadding="8" cellspacing="0" border="0" style="margin-bottom: 10px;">
                                            <tr>
                                                <td width="35%" style="color: #7F8C8D; font-size: 14px; font-weight: 600; text-transform: uppercase;">
                                                    Event Type:
                                                </td>
                                                <td style="color: #2C3E50; font-size: 16px; font-weight: 700;">
                                                    {{ $event->eventType->name }}
                                                </td>
                                            </tr>
                                        </table>
                                        
                                        <!-- Date -->
                                        <table width="100%" cellpadding="8" cellspacing="0" border="0" style="margin-bottom: 10px;">
                                            <tr>
                                                <td width="35%" style="color: #7F8C8D; font-size: 14px; font-weight: 600; text-transform: uppercase;">
                                                    Date:
                                                </td>
                                                <td style="color: #2C3E50; font-size: 16px; font-weight: 700;">
                                                    {{ $event->start_date->format('l, F j, Y') }}
                                                </td>
                                            </tr>
                                        </table>
                                        
                                        <!-- Time (if available) -->
                                        @if($event->start_time)
                                        <table width="100%" cellpadding="8" cellspacing="0" border="0" style="margin-bottom: 10px;">
                                            <tr>
                                                <td width="35%" style="color: #7F8C8D; font-size: 14px; font-weight: 600; text-transform: uppercase;">
                                                    Time:
                                                </td>
                                                <td style="color: #2C3E50; font-size: 16px; font-weight: 700;">
                                                    {{ date('g:i A', strtotime($event->start_time)) }}
                                                </td>
                                            </tr>
                                        </table>
                                        @endif
                                        
                                        <!-- Location -->
                                        <table width="100%" cellpadding="8" cellspacing="0" border="0" style="margin-bottom: 10px;">
                                            <tr>
                                                <td width="35%" style="color: #7F8C8D; font-size: 14px; font-weight: 600; text-transform: uppercase;">
                                                    Location:
                                                </td>
                                                <td style="color: #2C3E50; font-size: 16px; font-weight: 700;">
                                                    {{ $event->location_text }}
                                                </td>
                                            </tr>
                                        </table>
                                        
                                        <!-- Plus One (if allowed) -->
                                        @if($guest->plus_one_allowed)
                                        <table width="100%" cellpadding="8" cellspacing="0" border="0">
                                            <tr>
                                                <td width="35%" style="color: #7F8C8D; font-size: 14px; font-weight: 600; text-transform: uppercase;">
                                                    Plus One:
                                                </td>
                                                <td style="color: #7ED321; font-size: 16px; font-weight: 700;">
                                                    ✓ Allowed
                                                </td>
                                            </tr>
                                        </table>
                                        @endif
                                    </td>
                                </tr>
                            </table>
                            
                            <!-- Description (if available) -->
                            @if($event->description)
                            <div style="margin-bottom: 30px; padding: 20px; background-color: #F8F9FA; border-left: 4px solid #E19184; border-radius: 8px;">
                                <p style="margin: 0; font-size: 15px; color: #555555; line-height: 1.8;">
                                    <strong style="color: #475B35;">About the event:</strong><br>
                                    {{ $event->description }}
                                </p>
                            </div>
                            @endif
                            
                            <!-- RSVP Button -->
                            <table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin: 30px 0;">
                                <tr>
                                    <td align="center">
                                        <a href="{{ $rsvpUrl }}" style="display: inline-block; padding: 18px 50px; background: linear-gradient(135deg, #E19184, #C63E4E); color: #ffffff; text-decoration: none; border-radius: 50px; font-size: 18px; font-weight: 700; box-shadow: 0 8px 25px rgba(225, 145, 132, 0.5); transition: all 0.3s;">
                                            RSVP Now
                                        </a>
                                    </td>
                                </tr>
                            </table>
                            
                            <!-- RSVP Link (for copying) -->
                            <div style="text-align: center; margin: 20px 0;">
                                <p style="margin: 0 0 10px 0; color: #7F8C8D; font-size: 13px;">
                                    Or copy this link:
                                </p>
                                <p style="margin: 0; padding: 12px; background-color: #F8F9FA; border-radius: 8px; font-size: 13px; color: #4A90E2; word-break: break-all;">
                                    {{ $rsvpUrl }}
                                </p>
                            </div>
                            
                            <!-- Closing -->
                            <div style="margin-top: 40px; padding-top: 30px; border-top: 2px solid #EFE7DA;">
                                <p style="margin: 0 0 10px 0; font-size: 16px; color: #2C3E50; line-height: 1.6;">
                                    We hope to see you there!
                                </p>
                                <p style="margin: 0; font-size: 16px; color: #2C3E50; line-height: 1.6;">
                                    <strong>Best regards,</strong><br>
                                    {{ $event->client->name }}
                                </p>
                            </div>
                            
                        </td>
                    </tr>
                    
                    <!-- Footer -->
                    <tr>
                        <td style="background-color: #F8F9FA; padding: 30px; text-align: center; border-top: 1px solid #E0E0E0;">
                            <p style="margin: 0 0 10px 0; color: #475B35; font-size: 18px; font-weight: 700;">
                                EventFlow
                            </p>
                            <p style="margin: 0 0 5px 0; color: #7F8C8D; font-size: 13px;">
                                Event Management Platform
                            </p>
                            <p style="margin: 0; color: #95A5A6; font-size: 12px; font-style: italic;">
                                This is an automated invitation. Please do not reply to this email.
                            </p>
                        </td>
                    </tr>
                    
                </table>
                
            </td>
        </tr>
    </table>
    
</body>
</html>