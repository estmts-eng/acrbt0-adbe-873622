<?php
// ============= CONFIGURATION =============
$HCAPTCHA_SECRET = "6LdEbicsAAAAAAU0Kb0puEqteReZf5fufOL7Z_4V";  // ← Put your real hCaptcha secret here
$REDIRECT_TO     = "instadownload.html";                           // ← File in the same folder

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['h-captcha-response'])) {
    $response = $_POST['h-captcha-response'];
    $verify   = file_get_contents("https://hcaptcha.com/siteverify?secret={$HCAPTCHA_SECRET}&response={$response}");
    $result   = json_decode($verify);

    if ($result->success === true) {
        header("Location: $REDIRECT_TO");
        exit;
    } else {
        $error = "Verification failed. Please try again.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Human Verification | CAPTCHA</title>
    <meta name="description" content="Interactive human verification system">
    
    <!-- hCaptcha (invisible) -->
    <script src="https://js.hcaptcha.com/1/api.js" async defer></script>

    <style>
        * { margin:0; padding:0; box-sizing:border-box; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(180deg, hsl(220,25%,97%) 0%, hsl(220,20%,92%) 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1rem;
        }
        .captcha-card {
            background: white;
            border-radius: .75rem;
            box-shadow: 0 4px 16px -4px rgba(0,0,0,.12);
            padding: 2rem;
            width: 100%;
            max-width: 28rem;
        }
        .header {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: .5rem;
            margin-bottom: 1.5rem;
        }
        .header svg {
            width: 1.5rem;
            height: 1.5rem;
            color: hsl(217,91%,60%);
        }
        .header h2 {
            font-size: 1.25rem;
            font-weight: 700;
            color: hsl(220,15%,15%);
        }
        .title {
            text-align: center;
            margin-bottom: .5rem;
        }
        .title h3 {
            font-size: 1.125rem;
            font-weight: 600;
            color: hsl(220,15%,15%);
        }
        .desc {
            text-align: center;
            font-size: .875rem;
            color: hsl(220,10%,45%);
            margin-bottom: 2rem;
        }
        .success-screen {
            text-align: center;
            padding: 2rem 0;
        }
        .icon-large {
            display: inline-flex;
            background: linear-gradient(135deg, hsl(142,76%,36%), hsl(142,76%,46%));
            color: white;
            padding: 1.5rem;
            border-radius: 50%;
            margin-bottom: 1.5rem;
            animation: zoomIn .5s;
        }
        .icon-large svg { width: 48px; height: 48px; }
        .success-screen h3 {
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: .5rem;
        }
        .success-screen p {
            color: hsl(220,10%,45%);
        }
        .btn {
            width: 100%;
            padding: 1rem;
            font-size: 1.1rem;
            font-weight: 600;
            border: none;
            border-radius: .5rem;
            background: linear-gradient(135deg, hsl(217,91%,60%), hsl(217,91%,70%));
            color: white;
            cursor: pointer;
            transition: opacity .2s;
        }
        .btn:hover { opacity: .9; }
        .error {
            color: #e74c3c;
            text-align: center;
            margin: 1rem 0;
            font-weight: 500;
        }
        .footer {
            text-align: center;
            margin-top: 1.5rem;
            font-size: .75rem;
            color: #777;
        }
        @keyframes zoomIn {
            from { transform: scale(0); }
            to   { transform: scale(1); }
        }
        .hidden { display: none; }
    </style>
</head>
<body>

<div class="captcha-card">
    <!-- Success Screen (shown only after real verification) -->
    <div id="successScreen" class="success-screen hidden">
        <div class="icon-large">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <polyline points="20 6 9 17 4 12"></polyline>
            </svg>
        </div>
        <h3>Verified!</h3>
        <p>You've successfully completed verification</p>
        <p style="margin-top:.5rem;">Redirecting you now...</p>
    </div>

    <!-- Main Verification Screen -->
    <div id="mainScreen">
        <div class="header">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
            </svg>
            <h2>Human Verification</h2>
        </div>

        <div class="title"><h3>Security Check</h3></div>
        <div class="desc">Please complete the verification to continue</div>

        <?php if (!empty($error)): ?>
            <div class="error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST" id="captchaForm">
            <!-- Invisible hCaptcha -->
            <div class="h-captcha" data-sitekey="10000000-ffff-ffff-ffff-000000000001" data-size="invisible" data-callback="onCaptchaSuccess"></div>

            <button type="submit" class="btn">
                Continue →
            </button>
        </form>

        <div class="footer">
            This site is protected by hCaptcha · Your privacy is respected
        </div>
    </div>
</div>

<script>
function onCaptchaSuccess(token) {
    // Auto-submit when invisible captcha passes
    document.getElementById('mainScreen').classList.add('hidden');
    document.getElementById('successScreen').classList.remove('hidden');
    
    setTimeout(() => {
        window.location.href = "<?= $REDIRECT_TO ?>";
    }, 1800);
}

// Trigger invisible captcha on button click
document.querySelector('.btn').addEventListener('click', function(e) {
    e.preventDefault();
    hcaptcha.execute();
});
</script>

</body>
</html>