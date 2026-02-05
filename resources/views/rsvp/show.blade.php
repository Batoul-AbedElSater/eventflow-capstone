<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RSVP - {{ $guest->event->name }}</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --onyx: #353935;
            --ivory: #FFFFF0;
            --beige: #F5F5DC;
            --ebony: #586041;
            --white: #FFFFFF;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, sans-serif;
            background: linear-gradient(135deg, var(--beige) 0%, var(--ivory) 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        
        .rsvp-container {
            max-width: 700px;
            width: 100%;
            background: var(--white);
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.15);
            overflow: hidden;
        }
        
        .rsvp-header {
            background: linear-gradient(135deg, var(--ebony), var(--onyx));
            color: var(--ivory);
            padding: 50px 40px;
            text-align: center;
        }
        
        .rsvp-header h1 {
            font-size: 36px;
            margin-bottom: 10px;
        }
        
        .rsvp-header p {
            font-size: 18px;
            opacity: 0.9;
        }
        
        .rsvp-content {
            padding: 40px;
        }
        
        .success-message {
            background: rgba(126, 211, 33, 0.1);
            border: 2px solid #7ED321;
            color: #5FA119;
            padding: 20px;
            border-radius: 12px;
            margin-bottom: 30px;
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .success-message i {
            font-size: 28px;
        }
        
        .event-details {
            background: var(--beige);
            border-radius: 12px;
            padding: 30px;
            margin-bottom: 30px;
        }
        
        .detail-item {
            display: flex;
            align-items: flex-start;
            margin-bottom: 15px;
        }
        
        .detail-item:last-child {
            margin-bottom: 0;
        }
        
        .detail-item i {
            color: var(--ebony);
            width: 30px;
            font-size: 18px;
            margin-right: 15px;
            margin-top: 3px;
        }
        
        .detail-item strong {
            display: block;
            color: var(--onyx);
            margin-bottom: 5px;
        }
        
        .detail-item span {
            color: #666;
        }
        
        .rsvp-form {
            margin-top: 30px;
        }
        
        .form-group {
            margin-bottom: 25px;
        }
        
        .form-group label {
            display: block;
            font-size: 15px;
            font-weight: 600;
            color: var(--onyx);
            margin-bottom: 8px;
        }
        
        .radio-group {
            display: flex;
            gap: 20px;
        }
        
        .radio-option {
            flex: 1;
            position: relative;
        }
        
        .radio-option input {
            display: none;
        }
        
        .radio-option label {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            border: 3px solid var(--beige);
            border-radius: 12px;
            cursor: pointer;
            transition: all 0.3s;
            font-size: 16px;
            font-weight: 600;
        }
        
        .radio-option input:checked + label {
            border-color: var(--ebony);
            background: var(--ivory);
        }
        
        .radio-option.accept input:checked + label {
            border-color: #7ED321;
            background: rgba(126, 211, 33, 0.1);
            color: #7ED321;
        }
        
        .radio-option.decline input:checked + label {
            border-color: #D0021B;
            background: rgba(208, 2, 27, 0.1);
            color: #D0021B;
        }
        
        input[type="text"],
        textarea {
            width: 100%;
            padding: 14px 18px;
            border: 2px solid var(--beige);
            border-radius: 10px;
            font-size: 15px;
            background: var(--ivory);
        }
        
        input[type="text"]:focus,
        textarea:focus {
            outline: none;
            border-color: var(--ebony);
        }
        
        .btn-submit {
            width: 100%;
            padding: 16px;
            background: linear-gradient(135deg, var(--ebony), var(--onyx));
            color: var(--ivory);
            border: none;
            border-radius: 12px;
            font-size: 16px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(88, 96, 65, 0.3);
        }
        
        .already-responded {
            text-align: center;
            padding: 40px;
        }
        
        .already-responded i {
            font-size: 60px;
            color: var(--beige);
            margin-bottom: 20px;
        }
        
        .already-responded h3 {
            color: var(--onyx);
            margin-bottom: 10px;
        }
        
        .rsvp-status {
            display: inline-block;
            padding: 12px 24px;
            border-radius: 25px;
            font-weight: 700;
            margin-top: 15px;
        }
        
        .rsvp-status.accepted {
            background: rgba(126, 211, 33, 0.2);
            color: #7ED321;
        }
        
        .rsvp-status.declined {
            background: rgba(208, 2, 27, 0.2);
            color: #D0021B;
        }
    </style>
</head>
<body>
    <div class="rsvp-container">
        <!-- Header -->
        <div class="rsvp-header">
            <h1>🎉 You're Invited!</h1>
            <p>{{ $guest->event->name }}</p>
        </div>

        <div class="rsvp-content">
            @if(session('success'))
                <div class="success-message">
                    <i class="fas fa-check-circle"></i>
                    <div>
                        <strong>{{ session('success') }}</strong>
                    </div>
                </div>
            @endif

            <!-- Event Details -->
            <div class="event-details">
                <div class="detail-item">
                    <i class="fas fa-user"></i>
                    <div>
                        <strong>Guest Name</strong>
                        <span>{{ $guest->name }}</span>
                    </div>
                </div>
                
                <div class="detail-item">
                    <i class="fas fa-calendar"></i>
                    <div>
                        <strong>Date</strong>
                        <span>{{ $guest->event->start_date->format('l, F d, Y') }}</span>
                    </div>
                </div>

                <div class="detail-item">
                    <i class="fas fa-map-marker-alt"></i>
                    <div>
                        <strong>Location</strong>
                        <span>{{ $guest->event->location_text }}</span>
                    </div>
                </div>

                <div class="detail-item">
                    <i class="fas fa-tag"></i>
                    <div>
                        <strong>Event Type</strong>
                        <span>{{ $guest->event->eventType->name }}</span>
                    </div>
                </div>

                @if($guest->event->description)
                    <div class="detail-item">
                        <i class="fas fa-info-circle"></i>
                        <div>
                            <strong>Details</strong>
                            <span>{{ $guest->event->description }}</span>
                        </div>
                    </div>
                @endif
            </div>

            @if($guest->rsvp_status === 'pending' && !session('success'))
                <!-- RSVP Form -->
                <form method="POST" action="{{ route('rsvp.submit', $guest->rsvp_token) }}" class="rsvp-form">
                    @csrf
                    
                    <!-- RSVP Response -->
                    <div class="form-group">
                        <label>Will you be attending? *</label>
                        <div class="radio-group">
                            <div class="radio-option accept">
                                <input type="radio" name="rsvp_status" value="accepted" id="accept" required>
                                <label for="accept">
                                    <i class="fas fa-check-circle"></i> Yes, I'll be there!
                                </label>
                            </div>
                            <div class="radio-option decline">
                                <input type="radio" name="rsvp_status" value="declined" id="decline" required>
                                <label for="decline">
                                    <i class="fas fa-times-circle"></i> Sorry, can't make it
                                </label>
                            </div>
                        </div>
                    </div>

                    @if($guest->plus_one_allowed)
                        <!-- Plus One Name -->
                        <div class="form-group">
                            <label for="plus_one_name">Plus One Name (Optional)</label>
                            <input type="text" 
                                   id="plus_one_name" 
                                   name="plus_one_name" 
                                   value="{{ $guest->plus_one_name }}"
                                   placeholder="Name of your guest">
                        </div>
                    @endif

                    <!-- Dietary Restrictions -->
                    <div class="form-group">
                        <label for="dietary">Dietary Restrictions (Optional)</label>
                        <input type="text" 
                               id="dietary" 
                               name="dietary_restrictions" 
                               value="{{ $guest->dietary_restrictions }}"
                               placeholder="e.g., Vegan, Gluten-free, Allergies">
                    </div>

                    <!-- Message -->
                    <div class="form-group">
                        <label for="message">Message to Host (Optional)</label>
                        <textarea id="message" 
                                  name="message" 
                                  rows="3" 
                                  placeholder="Send a message to the host..."></textarea>
                    </div>

                    <button type="submit" class="btn-submit">
                        <i class="fas fa-paper-plane"></i> Submit RSVP
                    </button>
                </form>
            @else
                <!-- Already Responded -->
                <div class="already-responded">
                    <i class="fas fa-check-circle"></i>
                    <h3>Thank You for Your Response!</h3>
                    <p>You have already submitted your RSVP</p>
                    <div class="rsvp-status {{ $guest->rsvp_status }}">
                        @if($guest->rsvp_status === 'accepted')
                            <i class="fas fa-check-circle"></i> Attending
                        @else
                            <i class="fas fa-times-circle"></i> Not Attending
                        @endif
                    </div>
                    @if($guest->rsvp_date)
                        <p style="margin-top: 15px; color: #666;">
                            Responded on {{ $guest->rsvp_date->format('M d, Y') }}
                        </p>
                    @endif
                </div>
            @endif
        </div>
    </div>
</body>
</html>