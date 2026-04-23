<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'LegalHR Tanzania')</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Lato:ital,wght@0,100;0,300;0,400;0,700;0,900;1,100;1,300;1,400;1,700;1,900&family=Manrope:wght@200..800&family=Rubik:ital,wght@0,300..900;1,300..900&display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Feather Icons -->
    <script src="https://unpkg.com/feather-icons"></script>
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Lato', sans-serif;
            overflow: hidden;
        }
        
        .login-container {
            display: flex;
            height: 100vh;
            min-height: 100vh;
            position: relative;
        }
        
        .login-left {
            flex: 1;
            background: linear-gradient(135deg, #040344 0%, #040344 100%);
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            color: white;
            padding: 2rem;
            position: relative;
            overflow: hidden;
            min-height: 100vh;
        }
        
        .login-left::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grid" width="10" height="10" patternUnits="userSpaceOnUse"><path d="M 10 0 L 0 0 0 10" fill="none" stroke="rgba(255,255,255,0.1)" stroke-width="1"/></pattern></defs><rect width="100" height="100" fill="url(%23grid)"/></svg>');
            opacity: 0.3;
        }
        
        .login-right {
            flex: 1;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 2rem;
            background: #f8fafc;
            min-height: 100vh;
        }
        
        .login-form {
            width: 100%;
            max-width: 400px;
            margin: 0 auto;
        }
        
        /* Mobile Responsive Styles */
        
        /* Small Mobile Styles */
        @media (max-width: 768px) {
            .login-container {
                flex-direction: column;
                height: 100vh;
                min-height: 100vh;
            }
            
            .login-left {
                display: none; /* Hide branding section on mobile phones */
            }
            
            .login-right {
                flex: 1;
                padding: 1rem;
                min-height: 100vh;
                background: #f8fafc;
            }
            
            .login-form {
                padding: 0 0.5rem;
                max-width: 100%;
            }
            
            /* Add mobile logo */
            .mobile-logo {
                display: block;
                text-align: center;
                margin-bottom: 2rem;
            }
            
            .mobile-logo-container {
                width: 4rem;
                height: 4rem;
                background: white;
                border-radius: 0.5rem;
                display: flex;
                align-items: center;
                justify-content: center;
                margin: 0 auto 0.5rem;
                box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            }
            
            .mobile-logo img {
                width: 3.5rem;
                height: 3.5rem;
                object-fit: contain;
            }
            
            .mobile-logo h1 {
                font-size: 1.5rem;
                font-weight: bold;
                color: #040344;
                margin-top: 0.5rem;
            }
            
            .text-3xl {
                font-size: 1.75rem !important;
                line-height: 1.2 !important;
            }
            
            .text-xl {
                font-size: 1rem !important;
            }
            
            .space-y-6 > * + * {
                margin-top: 1rem !important;
            }
        }
        
        /* Show mobile logo only on small screens */
        .mobile-logo {
            display: none;
        }
        
        /* Tablet Styles */
        @media (min-width: 769px) and (max-width: 1024px) {
            .login-left {
                padding: 2rem;
            }
            
            .login-right {
                padding: 2rem;
            }
        }
        
        /* Large Desktop Styles */
        @media (min-width: 1025px) {
            .login-container {
                max-width: 1400px;
                margin: 0 auto;
            }
            
            .login-left {
                padding: 3rem;
            }
            
            .login-right {
                padding: 3rem;
            }
            
            .login-form {
                max-width: 450px;
            }
        }
        
        .form-input {
            width: 100%;
            padding: 0.75rem 1rem;
            border: 1px solid #e2e8f0;
            border-radius: 0.5rem;
            font-size: 0.875rem;
            transition: all 0.3s;
        }
        
        .form-input:focus {
            outline: none;
            border-color: #040344;
            box-shadow: 0 0 0 3px rgba(4, 3, 68, 0.1);
        }
        
        .btn-primary {
            width: 100%;
            padding: 0.875rem;
            background: linear-gradient(135deg, #040344 0%, #040344 100%);
            color: white;
            border: none;
            border-radius: 0.5rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(4, 3, 68, 0.3);
        }
        
        .floating-shapes {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            pointer-events: none;
        }
        
        .shape {
            position: absolute;
            border-radius: 50%;
            opacity: 0.1;
        }
        
        .shape-1 {
            width: 200px;
            height: 200px;
            background: rgba(255, 255, 255, 0.1);
            top: 10%;
            left: 10%;
            animation: float 6s ease-in-out infinite;
        }
        
        .shape-2 {
            width: 100px;
            height: 100px;
            background: rgba(255, 255, 255, 0.1);
            top: 60%;
            right: 20%;
            animation: float 8s ease-in-out infinite;
        }
        
        .shape-3 {
            width: 150px;
            height: 150px;
            background: rgba(255, 255, 255, 0.1);
            bottom: 20%;
            left: 30%;
            animation: float 10s ease-in-out infinite;
        }
        
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
        }
        
        /* Auth page transitions */
        .auth-page-transition {
            opacity: 0;
            transform: translateY(30px);
            animation: authPageEnter 0.8s ease-out forwards;
        }
        
        @keyframes authPageEnter {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        /* Form input transitions */
        .auth-form-input {
            transition: all 0.3s ease;
        }
        
        .auth-form-input:focus {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(4, 3, 68, 0.15);
        }
        
        /* Button transitions for auth */
        .auth-btn {
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        
        .auth-btn::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            transform: translate(-50%, -50%);
            transition: width 0.6s, height 0.6s;
        }
        
        .auth-btn:hover::before {
            width: 300px;
            height: 300px;
        }
        
        /* Link transitions */
        .auth-link {
            position: relative;
            transition: all 0.3s ease;
        }
        
        .auth-link::after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 0;
            width: 0;
            height: 2px;
            background: #040344;
            transition: width 0.3s ease;
        }
        
        .auth-link:hover::after {
            width: 100%;
        }
        
        /* Logo transitions */
        .mobile-logo {
            transition: all 0.5s ease;
        }
        
        .mobile-logo img {
            transition: all 0.3s ease;
        }
        
        .mobile-logo:hover img {
            transform: scale(1.1);
        }
        
        /* Form container transitions */
        .login-form {
            transition: all 0.3s ease;
        }
        
        /* Error/success message transitions */
        .auth-message {
            animation: messageSlide 0.4s ease-out;
        }
        
        @keyframes messageSlide {
            from {
                transform: translateY(-20px);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }
    </style>
</head>
<body>
    @yield('content')
    
    <script>
        // Initialize Feather Icons
        feather.replace();
        
        // Auth page transitions
        document.addEventListener('DOMContentLoaded', function() {
            // Add page transition class to login container
            const loginContainer = document.querySelector('.login-container');
            if (loginContainer) {
                loginContainer.classList.add('auth-page-transition');
            }
            
            // Add transition classes to form elements
            const formInputs = document.querySelectorAll('.form-input');
            formInputs.forEach(input => {
                input.classList.add('auth-form-input');
            });
            
            const buttons = document.querySelectorAll('.btn-primary');
            buttons.forEach(button => {
                button.classList.add('auth-btn');
            });
            
            const links = document.querySelectorAll('a:not(.mobile-logo a)');
            links.forEach(link => {
                link.classList.add('auth-link');
            });
            
            // Add transition to messages
            const messages = document.querySelectorAll('.bg-red-100, .bg-green-100');
            messages.forEach(message => {
                message.classList.add('auth-message');
            });
            
            // Add entrance animations to form elements
            const formElements = document.querySelectorAll('.login-form > *');
            formElements.forEach((element, index) => {
                element.style.opacity = '0';
                element.style.transform = 'translateY(20px)';
                
                setTimeout(() => {
                    element.style.transition = 'all 0.5s ease';
                    element.style.opacity = '1';
                    element.style.transform = 'translateY(0)';
                }, 100 + (index * 100));
            });
        });
    </script>
</body>
</html>
