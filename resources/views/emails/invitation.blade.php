<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event Invitation</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background: #F5F5DC;
            margin: 0;
            padding: 40px 20px;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background: #FFFFFF;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }
        .header {
            background: linear-gradient(135deg, #586041, #353935);
            color: #FFFFF0;
            padding: 40px 30px;
            text-align: center;
        }
        .header h1 {
            margin: 0 0 10px 0;
            font-size: 28px;
            font-weight: 700;
        }
        .header p {
            margin: 0;
            font-size: 16px;
            opacity: 0.9;
        }
        .content {
            padding: 40px 30px;
        }
        .greeting {
            font-size: 20px;
            color: #353935;
            margin-bottom: 20px;
        }
        .message {
            font-size: 16px;
            color: #555;
            line-height: 1.6;
            margin-bottom: 30px;
        }
        .event-details {
            background: #F5F5DC;
            border-radius: 12px;
            padding: 25px;
            margin-bottom: 30px;
        }
        .detail-row {
            display: flex;
            margin-bottom: 15px;
            align-items: flex-start;
        }
        .detail-row:last-child {
            margin-bottom: 0;
        }
        .detail-icon {
            width: 24px;
            margin-right: 12px;
            color: #586041;
        }
        .detail-label {
            font-weight: 600;
            color: #353935;
            margin-right: 8px;
        }
        .detail-value {
            color: #555;
        }
        .rsvp-buttons {
            text-align: center;
            margin: 40px 0;
        }
        .btn {
            display: inline-block;
            padding: 16px 40px;
            margin: 0 10px;
            text-decoration: none;
            font-size: 16px;
            font-weight: 700;
            border-radius: 10px;
            transition: all 0.3s;
        }
        .btn-accept {
            background: linear-gradient(135deg, #7ED321, #5FA119);
            color: #FFFFFF;
        }
        .btn-decline {
            background: linear-gradient(135deg, #D0021B, #A00115);
            color: #FFFFFF;
        }
        .footer {
            background: #F5F5DC;
            padding: 30px;
            text-align: center;
            color: #666;
            font-size: 14px;
        }
        .footer p {
            margin: 5px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1>🎉 You're Invited!</h1>
            <p>{{ $event->name }}</p>
        </div>

        <!-- Content -->
        <div class="content">
            <div class="greeting">
                Dear {{ $guest->name }},
            </div>

            <div class="message">
                <p>{{ $client->name }} is delighted to invite you to:</p>
            </div>

            <!-- Event Details -->
            <div class="event-details">
                <div class="detail-row">
                    <div class="detail-icon">📅</div>
                    <div>
                        <span class="detail-label">Date:</span>
                        <span class="detail-value">{{ $event->start_date->format('l, F d, Y') }}</span>
                    </div>
                </div>

                @if($event->start_date->format('H:i') !== '00:00')
                    <div class="detail-row">
                        <div class="detail-icon">🕐</div>
                        <div>
                            <span class="detail-label">Time:</span>
                            <span class="detail-value">{{ $event->start_date->format('g:i A') }}</span>
                        </div>
                    </div>
                @endif

                <div class="detail-row">
                    <div class="detail-icon">📍</div>
                    <div>
                        <span class="detail-label">Location:</span>
                        <span class="detail-value">{{ $event->location_text }}</span>
                    </div>
                </div>

                <div class="detail-row">
                    <div class="detail-icon">🎊</div>
                    <div>
                        <span class="detail-label">Event Type:</span>
                        <span class="detail-value">{{ $event->eventType->name }}</span>
                    </div>
                </div>

                @if($event->description)
                    <div class="detail-row">
                        <div class="detail-icon">📝</div>
                        <div>
                            <span class="detail-label">Details:</span>
                            <span class="detail-value">{{ $event->description }}</span>
                        </div>
                    </div>
                @endif
            </div>

            <div class="message">
                <p>We hope you can join us for this special occasion!</p>
                <p><strong>Please RSVP by clicking the button below:</strong></p>
            </div>

            <!-- RSVP Button -->
            <div class="rsvp-buttons">
                <a href="{{ $rsvpUrl }}" class="btn btn-accept" style="display: inline-block; padding: 16px 40px; margin: 0 10px; text-decoration: none; font-size: 16px; font-weight: 700; border-radius: 10px; background: linear-gradient(135deg, #7ED321, #5FA119); color: #FFFFFF;">
                    RSVP Now
                </a>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p>This invitation was sent via EventFlow</p>
            <p>If you have any questions, please contact {{ $client->name }}</p>
            @if($client->email)
                <p>Email: {{ $client->email }}</p>
            @endif
        </div>
    </div>
</body>
</html>