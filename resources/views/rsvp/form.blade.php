<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RSVP - {{ $event->name }}</title>
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
        
        .rsvp-container {
            max-width: 600px;
            width: 100%;
            background: white;
            border-radius: 30px;
            box-shadow: 0 25px 80px rgba(71, 91, 53, 0.2);
            overflow: hidden;
        }
        
        .rsvp-header {
            background: linear-gradient(135deg, #475B35 0%, #2C3821 100%);
            padding: 50px 40px;
            text-align: center;
            color: white;
        }
        
        .rsvp-header h1 {
            font-size: 36px;
            font-weight: 900;
            margin-bottom: 10px;
        }
        
        .rsvp-header p {
            font-size: 16px;
            opacity: 0.9;
        }
        
        .event-details {
            background: linear-gradient(135deg, #EFE7DA 0%, #F5F5DC 100%);
            padding: 30px;
            margin: 30px;
            border-radius: 20px;
        }
        
        .event-details h2 {
            color: #475B35;
            font-size: 24px;
            margin-bottom: 20px;
            text-align: center;
        }
        
        .detail-row {
            display: flex;
            justify-content: space-between;
            padding: 12px 0;
            border-bottom: 1px solid rgba(0,0,0,0.1);
        }
        
        .detail-row:last-child {
            border-bottom: none;
        }
        
        .detail-label {
            color: #7F8C8D;
            font-weight: 600;
            font-size: 14px;
        }
        
        .detail-value {
            color: #2C3E50;
            font-weight: 700;
        }
        
        .rsvp-form {
            padding: 40px;
        }
        
        .form-group {
            margin-bottom: 25px;
        }
        
        .form-group label {
            display: block;
            font-weight: 700;
            color: #2C3E50;
            margin-bottom: 10px;
            font-size: 15px;
        }
        
        .rsvp-buttons {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            margin-bottom: 30px;
        }
        
        .rsvp-btn {
            padding: 20px;
            border: 3px solid transparent;
            border-radius: 15px;
            font-weight: 700;
            font-size: 16px;
            cursor: pointer;
            transition: all 0.3s;
            text-align: center;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 10px;
        }
        
        .rsvp-btn input[type="radio"] {
            display: none;
        }
        
        .rsvp-btn.accept {
            background: rgba(126, 211, 33, 0.1);
            border-color: #7ED321;
            color: #5FA318;
        }
        
        .rsvp-btn.accept:hover {
            background: #7ED321;
            color: white;
        }
        
        .rsvp-btn.decline {
            background: rgba(208, 2, 27, 0.1);
            border-color: #D0021B;
            color: #D0021B;
        }
        
        .rsvp-btn.decline:hover {
            background: #D0021B;
            color: white;
        }
        
        .rsvp-btn input[type="radio"]:checked + .btn-content {
            transform: scale(1.05);
        }
        
        .rsvp-btn.accept input[type="radio"]:checked ~ * {
            color: white;
        }
        
        .rsvp-btn.accept:has(input:checked) {
            background: #7ED321;
            color: white;
        }
        
        .rsvp-btn.decline:has(input:checked) {
            background: #D0021B;
            color: white;
        }
        
        .btn-icon {
            font-size: 32px;
        }
        
        .form-input,
        .form-textarea {
            width: 100%;
            padding: 14px 18px;
            border: 2px solid #EFE7DA;
            border-radius: 12px;
            font-size: 15px;
            font-family: inherit;
            transition: all 0.3s;
        }
        
        .form-input:focus,
        .form-textarea:focus {
            outline: none;
            border-color: #E19184;
        }
        
        .form-textarea {
            resize: vertical;
            min-height: 100px;
        }
        
        .submit-btn {
            width: 100%;
            padding: 18px;
            background: linear-gradient(135deg, #E19184, #C63E4E);
            color: white;
            border: none;
            border-radius: 15px;
            font-weight: 700;
            font-size: 18px;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .submit-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(225, 145, 132, 0.5);
        }
    </style>
</head>
<body>
    <div class="rsvp-container">
        <div class="rsvp-header">
            <h1>🎉 You're Invited!</h1>
            <p>Please respond to this invitation</p>
        </div>
        
        <div class="event-details">
            <h2>{{ $event->name }}</h2>
            <div class="detail-row">
                <span class="detail-label">Date:</span>
                <span class="detail-value">{{ $event->start_date->format('F j, Y') }}</span>
            </div>
            @if($event->start_time)
            <div class="detail-row">
                <span class="detail-label">Time:</span>
                <span class="detail-value">{{ date('g:i A', strtotime($event->start_time)) }}</span>
            </div>
            @endif
            <div class="detail-row">
                <span class="detail-label">Location:</span>
                <span class="detail-value">{{ $event->location_text }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Guest:</span>
                <span class="detail-value">{{ $guest->name }}</span>
            </div>
        </div>
        
        <form method="POST" action="{{ route('rsvp.update', $guest->rsvp_token) }}" class="rsvp-form">
            @csrf
            
            <div class="form-group">
                <label>Will you be attending?</label>
                <div class="rsvp-buttons">
                    <label class="rsvp-btn accept">
                        <input type="radio" name="rsvp_status" value="accepted" required>
                        <div class="btn-content">
                            <div class="btn-icon">✓</div>
                            <div>Yes, I'll be there!</div>
                        </div>
                    </label>
                    
                    <label class="rsvp-btn decline">
                        <input type="radio" name="rsvp_status" value="declined" required>
                        <div class="btn-content">
                            <div class="btn-icon">✗</div>
                            <div>Sorry, can't make it</div>
                        </div>
                    </label>
                </div>
            </div>
            
            @if($guest->plus_one_allowed)
            <div class="form-group">
                <label for="plus_one_name">Plus One Name (Optional)</label>
                <input type="text" id="plus_one_name" name="plus_one_name" class="form-input" placeholder="Enter name">
            </div>
            @endif
            
            <div class="form-group">
                <label for="dietary_restrictions">Dietary Restrictions (Optional)</label>
                <input type="text" id="dietary_restrictions" name="dietary_restrictions" class="form-input" placeholder="e.g., Vegetarian, Gluten-free" value="{{ $guest->dietary_restrictions }}">
            </div>
            
            <div class="form-group">
                <label for="rsvp_message">Message to Host (Optional)</label>
                <textarea id="rsvp_message" name="rsvp_message" class="form-textarea" placeholder="Leave a message..."></textarea>
            </div>
            
            <button type="submit" class="submit-btn">Submit RSVP</button>
        </form>
    </div>
</body>
</html>