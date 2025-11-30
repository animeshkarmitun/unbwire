<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Ticket Status Updated</title>
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
            background-color: #ffc107;
            color: #333;
            padding: 20px;
            text-align: center;
            border-radius: 5px 5px 0 0;
        }
        .content {
            background-color: #f8f9fa;
            padding: 20px;
            border: 1px solid #dee2e6;
        }
        .status-change {
            background-color: white;
            padding: 15px;
            margin: 15px 0;
            border-left: 4px solid #ffc107;
        }
        .button {
            display: inline-block;
            padding: 12px 24px;
            background-color: #ffc107;
            color: #333;
            text-decoration: none;
            border-radius: 5px;
            margin: 20px 0;
            font-weight: bold;
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
        <h2>Ticket Status Updated</h2>
    </div>
    
    <div class="content">
        <p>Hello {{ $ticket->user->name ?? 'User' }},</p>
        <p>The status of your support ticket has been updated:</p>

        <div class="status-change">
            <p><strong>Ticket Number:</strong> {{ $ticket->ticket_number }}</p>
            <p><strong>Subject:</strong> {{ $ticket->subject }}</p>
            <p><strong>Status Changed:</strong> {{ ucfirst(str_replace('_', ' ', $oldStatus)) }} → {{ ucfirst(str_replace('_', ' ', $newStatus)) }}</p>
        </div>

        <div style="text-align: center;">
            <a href="{{ url('/admin/support-tickets/' . $ticket->id) }}" class="button">View Ticket</a>
        </div>
    </div>

    <div class="footer">
        <p>This is an automated email. Please do not reply to this message.</p>
        <p>&copy; {{ date('Y') }} {{ getSetting('site_name') ?? 'UNB News' }}. All rights reserved.</p>
    </div>
</body>
</html>

