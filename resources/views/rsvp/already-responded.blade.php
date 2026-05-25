<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Already Responded</title>
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
        
        .container {
            max-width: 500px;
            width: 100%;
            background: white;
            border-radius: 30px;
            padding: 60px 40px;
            text-align: center;
            box-shadow: 0 25px 80px rgba(71, 91, 53, 0.2);
        }
        
        .icon {
            width: 100px;
            height: 100px;
            margin: 0 auto 30px;
            border-radius: 50%;
            background: linear-gradient(135deg, #4A90E2, #357ABD);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 50px;
            color: white;
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
        
        .status-box {
            margin: 30px 0;
            padding: 20px;
            border-radius: 15px;
            font-weight: 600;
            background: rgba(74, 144, 226, 0.15);
            color: #4A90E2;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="icon">ℹ</div>
        <h1>Already Responded</h1>
        <div class="status-box">
            Your RSVP status: <strong>{{ ucfirst($guest->rsvp_status) }}</strong>
        </div>
        <p>You have already responded to this invitation. If you need to change your response, please contact the event host directly.</p>
    </div>
</body>
</html>