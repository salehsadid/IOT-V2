<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Parkinson's Monitoring System</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg-base: #09090e;
            --bg-surface: rgba(23, 23, 33, 0.7);
            --neon-cyan: #00f0ff;
            --neon-purple: #b026ff;
            --text-primary: #ffffff;
            --text-secondary: #94a3b8;
            --glass-border: 1px solid rgba(255, 255, 255, 0.05);
        }

        body {
            font-family: 'Outfit', sans-serif;
            background-color: var(--bg-base);
            margin: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-image: 
                radial-gradient(circle at 15% 50%, rgba(0, 240, 255, 0.05) 0%, transparent 25%),
                radial-gradient(circle at 85% 30%, rgba(176, 38, 255, 0.05) 0%, transparent 25%);
            color: var(--text-primary);
        }

        .login-container {
            background: var(--bg-surface);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: var(--glass-border);
            padding: 3rem;
            border-radius: 16px;
            width: 100%;
            max-width: 420px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
            position: relative;
            overflow: hidden;
        }

        .login-container::before {
            content: '';
            position: absolute;
            top: 0;
            left: -50%;
            width: 200%;
            height: 2px;
            background: linear-gradient(90deg, transparent, var(--neon-cyan), var(--neon-purple), transparent);
            animation: borderGlow 4s linear infinite;
        }

        @keyframes borderGlow {
            0% { transform: translateX(-50%); }
            100% { transform: translateX(0%); }
        }

        .login-header {
            text-align: center;
            margin-bottom: 2.5rem;
        }

        .login-header h1 {
            color: var(--text-primary);
            font-size: 1.75rem;
            margin: 0;
            font-weight: 700;
            background: linear-gradient(90deg, #fff, var(--neon-cyan));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .login-header p {
            color: var(--text-secondary);
            font-size: 0.875rem;
            margin-top: 0.5rem;
            letter-spacing: 0.5px;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        label {
            display: block;
            margin-bottom: 0.5rem;
            color: var(--text-secondary);
            font-weight: 500;
            font-size: 0.875rem;
            letter-spacing: 0.5px;
        }

        input[type="email"],
        input[type="password"] {
            width: 100%;
            padding: 0.85rem;
            background: rgba(0, 0, 0, 0.2);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 8px;
            color: white;
            font-size: 1rem;
            font-family: 'Outfit', sans-serif;
            transition: all 0.3s ease;
        }

        input[type="email"]:focus,
        input[type="password"]:focus {
            outline: none;
            border-color: var(--neon-cyan);
            box-shadow: 0 0 15px rgba(0, 240, 255, 0.2);
            background: rgba(0, 0, 0, 0.4);
        }

        .error-message {
            color: #ff0055;
            font-size: 0.875rem;
            margin-top: 0.5rem;
            text-shadow: 0 0 10px rgba(255, 0, 85, 0.3);
        }

        .checkbox-group {
            display: flex;
            align-items: center;
            margin-bottom: 2rem;
        }

        .checkbox-group input {
            margin-right: 0.5rem;
            accent-color: var(--neon-cyan);
        }

        button[type="submit"] {
            width: 100%;
            padding: 0.85rem;
            background: linear-gradient(90deg, var(--neon-cyan), var(--neon-purple));
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.2s, box-shadow 0.2s;
            text-transform: uppercase;
            letter-spacing: 1px;
            box-shadow: 0 0 20px rgba(0, 240, 255, 0.3);
        }

        button[type="submit"]:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 25px rgba(176, 38, 255, 0.4);
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-header">
            <h1>Welcome Back</h1>
            <p>Parkinson's Monitoring System</p>
        </div>

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="email">
                @error('email')
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required autocomplete="current-password">
                @error('password')
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>

            <div class="checkbox-group">
                <input type="checkbox" id="remember" name="remember">
                <label for="remember" style="margin-bottom: 0;">Remember me</label>
            </div>

            <button type="submit">Sign In</button>
        </form>
    </div>
</body>
</html>
