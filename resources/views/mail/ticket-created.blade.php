<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Support Ticket Created</title>
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
            background-color: #007bff;
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
            border-left: 4px solid #007bff;
        }
        .button {
            display: inline-block;
            padding: 12px 24px;
            background-color: #007bff;
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
        <h2>Support Ticket Created</h2>
    </div>
    
    <div class="content">
        @if($isAdmin)
            <p>Hello Admin,</p>
            <p>A new support ticket has been created:</p>
        @else
            <p>Hello {{ $ticket->user->name ?? 'User' }},</p>
            <p>Thank you for contacting us! Your support ticket has been created successfully.</p>
        @endif

        <div class="ticket-info">
            <p><strong>Ticket Number:</strong> {{ $ticket->ticket_number }}</p>
            <p><strong>Subject:</strong> {{ $ticket->subject }}</p>
            <p><strong>Category:</strong> {{ $ticket->category->name }}</p>
            <p><strong>Priority:</strong> {{ ucfirst($ticket->priority) }}</p>
            <p><strong>Status:</strong> {{ ucfirst(str_replace('_', ' ', $ticket->status)) }}</p>
            @if($ticket->admin)
                <p><strong>Assigned To:</strong> {{ $ticket->admin->name }}</p>
            @endif
        </div>

        <p><strong>Description:</strong></p>
        <p>{{ $ticket->description }}</p>

        <div style="text-align: center;">
            <a href="{{ url('/admin/support-tickets/' . $ticket->id) }}" class="button">View Ticket</a>
        </div>

        <p>We will review your ticket and respond as soon as possible.</p>
    </div>

    <div class="footer">
        <p>This is an automated email. Please do not reply to this message.</p>
        <p>&copy; {{ date('Y') }} {{ getSetting('site_name') ?? 'UNB News' }}. All rights reserved.</p>
    </div>
</body>
</html>














