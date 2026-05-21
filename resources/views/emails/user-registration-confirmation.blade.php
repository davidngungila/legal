<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to Orvion HRIS</title>
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
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
            border-radius: 10px 10px 0 0;
        }
        .content {
            background: #f9fafb;
            padding: 30px;
            border-radius: 0 0 10px 10px;
        }
        .button {
            display: inline-block;
            background: #4f46e5;
            color: white;
            padding: 12px 24px;
            text-decoration: none;
            border-radius: 6px;
            margin: 20px 0;
        }
        .footer {
            text-align: center;
            color: #6b7280;
            margin-top: 30px;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Welcome to Orvion HRIS</h1>
        <p>Your account has been created successfully!</p>
    </div>

    <div class="content">
        <h2>Hello {{ $user->full_name }},</h2>
        
        <p>Welcome to the Orvion HR Management System! Your account has been successfully created and you can now access the system.</p>

        <h3>Your Account Details:</h3>
        <ul>
            <li><strong>Name:</strong> {{ $user->full_name }}</li>
            <li><strong>Email:</strong> {{ $user->email }}</li>
            <li><strong>Phone:</strong> {{ $user->phone_number }}</li>
            <li><strong>Department:</strong> {{ $user->department_name }}</li>
            <li><strong>Designation:</strong> {{ $user->designation }}</li>
        </ul>

        <p>You can now log in to the system using your email address and the password you created during registration.</p>

        <a href="{{ $loginUrl }}" class="button">Login to Your Account</a>

        <p><strong>Important:</strong></p>
        <ul>
            <li>Keep your login credentials secure</li>
            <li>Update your profile information if needed</li>
            <li>Contact your administrator if you have any issues</li>
        </ul>

        <p>If you have any questions or need assistance, please don't hesitate to contact our support team.</p>
    </div>

    <div class="footer">
        <p>&copy; {{ date('Y') }} Orvion HRIS. All rights reserved.</p>
        <p>This is an automated message. Please do not reply to this email.</p>
    </div>
</body>
</html>
