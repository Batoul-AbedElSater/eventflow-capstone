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
            background: var(--cream);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .thank-you-container {
            max-width: 500px;
            width: 100%;
            max-height:700px;
            background: white;
            border-radius: 30px;
            padding: 50px 50px;
            text-align: center;
        }



        h1 {
            font-size: 35px;
            color: var(--dark-green);
            margin-bottom: 15px;
        }

        .status-message {
            margin: 30px 0;
            padding: 20px;
            border-radius: 15px;
            font-weight: 600;
        }

        .status-accepted {
            background:var(--cream);
            color: var(--burgendy);
        }

        .status-declined {
            background: var(--cream);
            color: var(--burgendy);

        }
    </style>
</head>
<body>
 <div class="thank-you-container">
        <h1>Thank You!</h1>

        <div class="status-message {{ $guest->rsvp_status === 'accepted' ? 'status-accepted' : 'status-declined' }}">
            @if($guest->rsvp_status === 'accepted')
                We're excited to see you there!
            @else
                We appreciate your response.
            @endif

</body>
</html>
