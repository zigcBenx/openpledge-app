{{-- resources/views/maintenance.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>We’ll Be Right Back!</title>
    <style>
        body {
            background-color: #f7f7f7;
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }
        .maintenance-wrapper {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100vh;
            text-align: center;
            color: #333;
        }
        
        /* Logo Styles + Animation */
        .logo {
            width: 150px; /* Adjust to suit your logo size */
            margin-bottom: 1.5rem;
            animation: pulse 2s infinite;
            transform-origin: center center;
        }
        @keyframes pulse {
            0% {
                transform: scale(1);
            }
            50% {
                transform: scale(1.08);
            }
            100% {
                transform: scale(1);
            }
        }

        h1 {
            font-size: 2rem;
            margin: 0 0 1rem;
        }
        p {
            max-width: 600px;
            margin: 0 auto 1rem;
            line-height: 1.5;
        }
        a {
            color: #2c3e50;
            text-decoration: none;
            font-weight: bold;
        }
        a:hover {
            color: #1a252f;
        }
    </style>
</head>
<body>
    <div class="maintenance-wrapper">
        <a href="https://openpledge.io"><img src="{{ asset('images/logo.png') }}" alt="OpenPledge" class="logo"></a>

        <h1>We’re Putting on the Finishing Touches!🔨</h1>
        <p>
            We’re currently running some final tests before our official launch.
            We appreciate your patience and can’t wait to show you what we’ve been building!
        </p>
        <p>
            For real-time updates and announcements, 
            make sure to follow us on 
            <a href="https://www.linkedin.com/company/openpledge" target="_blank">LinkedIn</a>.
        </p>
        <p>We’ll be live again soon. Stay tuned!</p>
    </div>
</body>
</html>
