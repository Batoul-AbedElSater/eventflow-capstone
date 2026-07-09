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
                          --coral: #E19184;
        --pink: #C63E4E;
        --burgendy: #620607;
        --cream: #EFE7DA;
        --white: #FFFFFF;
        --amnesiac: #F5F9E5;
        --green: #475B35;
        --dark-green: #2C3821;
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

        h1 {
            font-size: 32px;
            color: var(--dark-green);
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
            background: var(--cream);
            color: var(--burgendy);
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Already Responded</h1>
        <div class="status-box">
            Your RSVP status: <strong>{{ ucfirst($guest->rsvp_status) }}</strong>
        </div>
        <p>You have already responded to this invitation.</p>
    </div>
</body>
</html>
