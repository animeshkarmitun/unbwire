<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>New Reply on Support Ticket</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background-color: #28a745;
            color: white;
            padding: 20px;
            text-align: center;
            border-radius: 5px 5px 0 0;
        }
        .content {
            background-color: #f8f9fa;
            padding: 20px;
            border: 1px solid #dee2e6;
        }
        .ticket-info {
            background-color: white;
            padding: 15px;
            margin: 15px 0;
            border-left: 4px solid #28a745;
        }
        .reply-box {
            background-color: white;
            padding: 15px;
            margin: 15px 0;
            border: 1px solid #dee2e6;
            border-radius: 5px;
        }
        .button {
            display: inline-block;
            padding: 12px 24px;
            background-color: #28a745;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin: 20px 0;
        }
        .footer {
            text-align: center;
            color: #6c757d;
            font-size: 12px;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h2>New Reply on Your Ticket</h2>
    </div>
    
    <div class="content">
        @if($isAdminReply)
            <p>Hello Admin,</p>
            <p>A new reply has been added to ticket {{ $ticket->ticket_number }}:</p>
        @else
            <p>Hello {{ $ticket->user->name ?? 'User' }},</p>
            <p>You have received a new reply on your support ticket:</p>
        @endif

        <div class="ticket-info">
            <p><strong>Ticket Number:</strong> {{ $ticket->ticket_number }}</p>
            <p><strong>Subject:</strong> {{ $ticket->subject }}</p>
        </div>

        <div class="reply-box">
            <p><strong>Reply from:</strong> {{ $reply->isFromAdmin() ? ($reply->admin->name ?? 'Admin') : ($reply->user->name ?? 'User') }}</p>
            <p><strong>Date:</strong> {{ $reply->created_at->format('M d, Y H:i') }}</p>
            <hr>
            <p>{{ $reply->message }}</p>
        </div>

        <div style="text-align: center;">
            <a href="{{ url('/admin/support-tickets/' . $ticket->id) }}" class="button">View Ticket & Reply</a>
        </div>
    </div>

    <div class="footer">
        <p>This is an automated email. Please do not reply to this message.</p>
        <p>&copy; {{ date('Y') }} {{ getSetting('site_name') ?? 'UNB News' }}. All rights reserved.</p>
    </div>
</body>
</html>





























