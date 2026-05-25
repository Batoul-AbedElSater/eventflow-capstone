<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thank You!</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #EFE7DA 0%, #FFFFF0 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        
        .thank-you-container {
            max-width: 500px;
            width: 100%;
            background: white;
            border-radius: 30px;
            padding: 60px 40px;
            text-align: center;
            box-shadow: 0 25px 80px rgba(71, 91, 53, 0.2);
        }
        
        .success-icon {
            width: 100px;
            height: 100px;
            margin: 0 auto 30px;
            border-radius: 50%;
            background: linear-gradient(135deg, #7ED321, #5FA318);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 50px;
            color: white;
            animation: scaleIn 0.5s ease-out;
        }
        
        @keyframes scaleIn {
            from {
                transform: scale(0);
            }
            to {
                transform: scale(1);
            }
        }
        
        h1 {
            font-size: 32px;
            color: #475B35;
            margin-bottom: 15px;
        }
        
        p {
            font-size: 16px;
            color: #7F8C8D;
            line-height: 1.6;
        }
        
        .status-message {
            margin: 30px 0;
            padding: 20px;
            border-radius: 15px;
            font-weight: 600;
        }
        
        .status-accepted {
            background: rgba(126, 211, 33, 0.15);
            color: #5FA318;
        }
        
        .status-declined {
            background: rgba(208, 2, 27, 0.15);
            color: #D0021B;
        }
    </style>
</head>
<body>
    <div class="thank-you-container">
        <div class="success-icon">
            @if($guest->rsvp_status === 'accepted')
                ✓
            @else
                ℹ
            @endif
        </div>
        
        <h1>Thank You!</h1>
        
        <div class="status-message {{ $guest->rsvp_status === 'accepted' ? 'status-accepted' : 'status-declined' }}">
            @if($guest->rsvp_status === 'accepted')
                We're excited to see you there! 🎉
            @else
                We'll miss you, but we understand!
            @endif
        </div>
        
        <p>Your RSVP has been recorded successfully. The host will be notified of your response.</p>
    </div>
</body>
</html>