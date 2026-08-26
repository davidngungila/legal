<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Server Error - LegalHR Tanzania</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Lato:wght@300;400;700;900&family=Manrope:wght@200..800&family=Rubik:wght@300..900&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css'])
    <style>
        body { font-family: 'Lato', ui-sans-serif, system-ui, sans-serif; }
        .error-container {
            display: flex;
            min-height: 100vh;
            background: #f8fafc;
            opacity: 0;
            animation: fadeIn 0.6s ease-out forwards;
        }
        @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .error-left {
            display: none;
            flex: 1;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            color: white;
            padding: 3rem;
            position: relative;
            overflow: hidden;
            background: #040344;
        }
        @media (min-width: 1024px) {
            .error-left { display: flex; }
        }
        .error-left::before {
            content: '';
            position: absolute;
            inset: 0;
            opacity: 0.1;
            background-image: radial-gradient(circle at 2px 2px, white 1px, transparent 0);
            background-size: 24px 24px;
        }
        .floating-shape {
            position: absolute;
            border-radius: 50%;
            opacity: 0.2;
            filter: blur(60px);
            animation: pulse 4s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }
        @keyframes pulse { 0%, 100% { opacity: 0.2; } 50% { opacity: 0.3; } }
        .error-right {
            flex: 1;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 1.5rem;
        }
        @media (min-width: 768px) {
            .error-right { padding: 3rem; }
        }
        .error-card {
            width: 100%;
            max-width: 28rem;
            background: white;
            border-radius: 1rem;
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.08);
            border: 1px solid #f1f5f9;
            padding: 2rem;
            text-align: center;
            animation: fadeInUp 0.6s ease-out 0.2s both;
        }
        @media (min-width: 768px) {
            .error-card { padding: 2.5rem; }
        }
        .error-code {
            font-size: 5rem;
            font-weight: 900;
            color: #040344;
            line-height: 1;
            letter-spacing: -0.05em;
        }
        .error-btn-primary {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            width: 100%;
            padding: 0.875rem 1.5rem;
            background: #040344;
            color: white;
            border-radius: 0.75rem;
            font-weight: 700;
            font-size: 0.875rem;
            letter-spacing: 0.05em;
            text-decoration: none;
            transition: all 0.3s;
            box-shadow: 0 4px 14px rgba(4, 3, 68, 0.2);
        }
        .error-btn-primary:hover { background: #060563; }
        .error-btn-outline {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            width: 100%;
            padding: 0.875rem 1.5rem;
            background: white;
            border: 2px solid #e2e8f0;
            color: #475569;
            border-radius: 0.75rem;
            font-weight: 700;
            font-size: 0.875rem;
            text-decoration: none;
            transition: all 0.3s;
        }
        .error-btn-outline:hover { border-color: #cbd5e1; background: #f8fafc; }
    </style>
</head>
<body>
    <div class="error-container">
        <div class="error-left">
            <div class="floating-shape" style="width:16rem;height:16rem;background:#dc2626;top:-10%;left:-10%;"></div>
            <div class="floating-shape" style="width:24rem;height:24rem;background:#991b1b;bottom:-20%;right:-10%;"></div>

            <div style="position:relative;z-10;text-align:center;padding:2rem;">
                <div style="display:inline-flex;align-items:center;justify-content:center;width:8rem;height:8rem;background:white;border-radius:1.5rem;border:1px solid rgba(255,255,255,0.2);margin-bottom:2rem;box-shadow:0 25px 50px rgba(0,0,0,0.25);overflow:hidden;">
                    <img src="{{ asset('Orvion.png') }}" alt="Orvion Logo" style="width:5rem;height:5rem;object-fit:contain;">
                </div>
                <h1 style="font-size:3rem;font-weight:800;letter-spacing:-0.025rem;margin-bottom:1rem;background-clip:text;-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-image:linear-gradient(to right,white,rgba(255,255,255,0.7));">Orvion</h1>
                <p style="font-size:1.25rem;color:rgba(255,255,255,0.8);font-weight:500;">Next Generation HR Management</p>
            </div>
        </div>

        <div class="error-right">
            <div class="error-card">
                <div style="display:inline-flex;align-items:center;justify-content:center;width:4rem;height:4rem;background:#fef2f2;border-radius:1rem;margin-bottom:1.5rem;">
                    <i data-feather="alert-triangle" style="width:2rem;height:2rem;color:#dc2626;"></i>
                </div>

                <div class="error-code">500</div>
                <h2 style="font-size:1.5rem;font-weight:800;color:#0f172a;margin:0.75rem 0 0.5rem;">Server Error</h2>
                <p style="color:#64748b;font-size:0.9375rem;line-height:1.6;margin-bottom:2rem;">
                    Something went wrong on our end. Our team has been notified. Please try again later.
                </p>

                <div style="display:flex;flex-direction:column;gap:0.75rem;">
                    <button onclick="location.reload()" class="error-btn-primary">
                        <i data-feather="refresh-cw" style="width:1rem;height:1rem;"></i>
                        Try Again
                    </button>
                    <a href="{{ url('/login') }}" class="error-btn-outline">
                        <i data-feather="log-in" style="width:1rem;height:1rem;"></i>
                        Go to Login
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script src="https://unpkg.com/feather-icons"></script>
    <script>document.addEventListener('DOMContentLoaded', function() { feather.replace(); });</script>
</body>
</html>
