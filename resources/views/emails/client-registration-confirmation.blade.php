<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Client Registration Confirmation - Orvion HRIS</title>
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
        .info-box {
            background: white;
            border-left: 4px solid #4f46e5;
            padding: 15px;
            margin: 20px 0;
            border-radius: 0 8px 8px 0;
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
        <p>Your client registration has been completed successfully!</p>
    </div>

    <div class="content">
        <h2>Dear {{ $client->contact_person }},</h2>
        
        <p>Congratulations! Your organization has been successfully registered with Orvion HRIS. We are pleased to welcome you as our valued client.</p>

        <div class="info-box">
            <h3>Client Registration Details:</h3>
            <ul>
                <li><strong>Employer Name:</strong> {{ $client->employer_name }}</li>
                <li><strong>Employer Number:</strong> {{ $client->employer_number }}</li>
                <li><strong>Contact Person:</strong> {{ $client->contact_person }}</li>
                <li><strong>Contact Email:</strong> {{ $client->contact_email }}</li>
                <li><strong>Contact Phone:</strong> {{ $client->contact_phone }}</li>
                <li><strong>Region:</strong> {{ $client->region }}</li>
                <li><strong>District:</strong> {{ $client->district }}</li>
            </ul>
        </div>

        <div class="info-box">
            <h3>Registration Numbers:</h3>
            <ul>
                <li><strong>TIN Number:</strong> {{ $client->tin_number }}</li>
                <li><strong>OSHA Registration:</strong> {{ $client->osha_registration }}</li>
                <li><strong>NHIF Registration:</strong> {{ $client->nhif_registration }}</li>
                <li><strong>WCF Registration:</strong> {{ $client->wcf_registration }}</li>
                <li><strong>VAT Registration:</strong> {{ $client->vat_registration_number }}</li>
                <li><strong>NSSF Registration:</strong> {{ $client->nssf_registration }}</li>
            </ul>
        </div>

        <h3>What's Next?</h3>
        <ul>
            <li>Your employer number <strong>{{ $client->employer_number }}</strong> will be used for all future transactions</li>
            <li>You can now proceed with employee recruitment and registration</li>
            <li>Your account details will be used for project management and compliance</li>
            <li>Our team will contact you for onboarding and system setup</li>
        </ul>

        <h3>Important Information:</h3>
        <ul>
            <li>Keep your employer number secure and accessible</li>
            <li>Ensure all registration certificates are valid and up-to-date</li>
            <li>Contact our support team for any assistance with system setup</li>
            <li>Regular compliance checks will be conducted based on your registrations</li>
        </ul>

        <p>Thank you for choosing Orvion HRIS for your HR management needs. We look forward to a successful partnership with your organization.</p>
    </div>

    <div class="footer">
        <p>&copy; {{ date('Y') }} Orvion HRIS. All rights reserved.</p>
        <p>This is an automated message. Please do not reply to this email.</p>
        <p>For support, contact: support@orvionhris.com</p>
    </div>
</body>
</html>
