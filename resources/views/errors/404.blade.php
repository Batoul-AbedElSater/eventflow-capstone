<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Page Not Found</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background: linear-gradient(135deg, #475B35, #2C3821);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
        }
        
        .error-container {
            text-align: center;
            padding: 40px;
        }
        
        .error-code {
            font-size: 120px;
            font-weight: 900;
            color: #E19184;
            text-shadow: 0 10px 30px rgba(0,0,0,0.3);
        }
        
        .error-message {
            font-size: 28px;
            font-weight: 700;
            margin: 20px 0;
        }
        
        .error-description {
            font-size: 16px;
            color: rgba(255,255,255,0.8);
            margin-bottom: 40px;
        }
        
        .back-btn {
            display: inline-block;
            padding: 15px 40px;
            background: #E19184;
            color: white;
            text-decoration: none;
            border-radius: 12px;
            font-weight: 700;
            transition: all 0.3s;
        }
        
        .back-btn:hover {
            background: #C63E4E;
            transform: translateY(-3px);
            box-shadow: 0 10px 30px rgba(225,145,132,0.4);
        }
    </style>
</head>
<body>
    <div class="error-container">
        <div class="error-code">404</div>
        <h1 class="error-message">Page Not Found</h1>
        <p class="error-description">The page you're looking for doesn't exist.</p>
        <a href="{{ url('/') }}" class="back-btn">Go Back Home</a>
    </div>
</body>
</html>