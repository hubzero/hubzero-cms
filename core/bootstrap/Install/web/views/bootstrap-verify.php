<?php

/**
 * Bootstrap Installer - Verification Step
 *
 * Requires proof of file system ownership before allowing composer install.
 * This is a simplified version of the main installer's verify step.
 *
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2025 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// phpcs:disable Generic.Files.LineLength
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HUBzero Bootstrap Setup - Verify Ownership</title>
    <style>
:root {
    --primary-color: #2563eb;
    --primary-dark: #1d4ed8;
    --success-color: #16a34a;
    --warning-color: #ca8a04;
    --error-color: #dc2626;
    --gray-50: #f9fafb;
    --gray-100: #f3f4f6;
    --gray-200: #e5e7eb;
    --gray-300: #d1d5db;
    --gray-500: #6b7280;
    --gray-700: #374151;
    --gray-900: #111827;
}

* {
    box-sizing: border-box;
    margin: 0;
    padding: 0;
}

body {
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    font-size: 16px;
    line-height: 1.6;
    color: var(--gray-700);
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    min-height: 100vh;
    padding: 2rem 1rem;
}

.container {
    max-width: 700px;
    margin: 0 auto;
}

.header {
    text-align: center;
    color: white;
    margin-bottom: 2rem;
}

.header h1 {
    font-size: 2rem;
    font-weight: 300;
    margin-bottom: 0.5rem;
}

.header p {
    opacity: 0.9;
}

.card {
    background: white;
    border-radius: 1rem;
    box-shadow: 0 20px 40px rgba(0,0,0,0.2);
    padding: 2rem;
    margin-bottom: 1.5rem;
}

.card h2 {
    font-size: 1.25rem;
    color: var(--gray-900);
    margin-bottom: 1rem;
    padding-bottom: 0.75rem;
    border-bottom: 1px solid var(--gray-200);
}

.warning-box {
    background: #fff3cd;
    border: 1px solid #ffc107;
    border-radius: 0.5rem;
    padding: 1rem 1.25rem;
    margin-bottom: 1.5rem;
}

.warning-box h3 {
    color: #856404;
    margin-bottom: 0.5rem;
    font-size: 1rem;
}

.warning-box p {
    color: #856404;
    margin: 0;
    font-size: 0.95rem;
}

.error-box {
    background: #fef2f2;
    border: 1px solid #fecaca;
    border-radius: 0.5rem;
    padding: 1rem 1.25rem;
    margin-bottom: 1.5rem;
}

.error-box h3 {
    color: var(--error-color);
    margin-bottom: 0.5rem;
    font-size: 1rem;
}

.error-box p {
    color: var(--error-color);
    margin: 0;
}

.info-box {
    background: #e7f3ff;
    border: 1px solid #b6d4fe;
    border-radius: 0.5rem;
    padding: 1.25rem;
    margin-bottom: 1.5rem;
}

.filename-display {
    background: #fff;
    border: 2px solid var(--primary-color);
    border-radius: 0.5rem;
    padding: 1rem;
    margin: 1rem 0;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 1rem;
}

.filename-display code {
    font-size: 1.4rem;
    font-weight: bold;
    color: var(--primary-color);
    letter-spacing: 0.1em;
    font-family: 'Courier New', Courier, monospace;
}

.file-location {
    background: var(--gray-50);
    padding: 0.5rem 0.75rem;
    border-radius: 0.25rem;
    margin: 1rem 0;
    font-size: 0.9rem;
}

.file-location code {
    color: var(--gray-700);
    word-break: break-all;
}

.time-remaining {
    background: #fff;
    padding: 0.75rem 1rem;
    border-radius: 0.25rem;
    margin: 1rem 0;
    text-align: center;
    font-size: 1.1rem;
}

.timer-icon {
    font-size: 1.2rem;
}

#time-remaining {
    color: var(--error-color);
    font-family: 'Courier New', Courier, monospace;
}

.instructions-detail {
    margin-top: 1.5rem;
    padding-top: 1rem;
    border-top: 1px solid #b6d4fe;
}

.instructions-detail h4 {
    margin-top: 0;
    margin-bottom: 0.75rem;
    font-size: 1rem;
}

.instructions-detail ul {
    margin: 0;
    padding-left: 1.25rem;
}

.instructions-detail li {
    margin-bottom: 0.75rem;
}

.instructions-detail code {
    background: #fff;
    padding: 0.25rem 0.5rem;
    border-radius: 0.25rem;
    font-size: 0.85rem;
    display: inline-block;
    margin-top: 0.25rem;
    word-break: break-all;
}

.attempts-warning {
    background: #fff3cd;
    border: 1px solid #ffc107;
    border-radius: 0.25rem;
    padding: 0.5rem 0.75rem;
    margin-top: 1rem;
    font-size: 0.9rem;
    color: #856404;
}

.btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 0.75rem 1.5rem;
    font-size: 1rem;
    font-weight: 500;
    border-radius: 0.5rem;
    border: none;
    cursor: pointer;
    text-decoration: none;
    transition: all 0.2s;
}

.btn-primary {
    background: var(--primary-color);
    color: white;
}

.btn-primary:hover {
    background: var(--primary-dark);
}

.btn-small {
    padding: 0.25rem 0.75rem;
    font-size: 0.875rem;
}

.btn-copy {
    background: var(--primary-color);
    color: white;
}

.btn-copy:hover {
    background: var(--primary-dark);
}

.form-actions {
    display: flex;
    gap: 1rem;
    margin-top: 1.5rem;
    padding-top: 1rem;
    border-top: 1px solid var(--gray-200);
}

.message {
    padding: 0.75rem 1rem;
    border-radius: 0.5rem;
    margin-bottom: 1rem;
}

.message-error {
    background: #fef2f2;
    border: 1px solid #fecaca;
    color: var(--error-color);
}

.message-success {
    background: #f0fdf4;
    border: 1px solid #bbf7d0;
    color: var(--success-color);
}

.footer {
    text-align: center;
    color: rgba(255,255,255,0.6);
    margin-top: 2rem;
    font-size: 0.875rem;
}
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>HUBzero Bootstrap Setup</h1>
            <p>Verify file system ownership</p>
        </div>

        <div class="card">
            <h2>Verify Ownership</h2>

            <?php if (!empty($errorMessage)) : ?>
            <div class="message message-error">
                <?php echo htmlspecialchars($errorMessage); ?>
            </div>
            <?php endif; ?>

            <div class="warning-box">
                <h3>Why This Step?</h3>
                <p>This verification prevents unauthorized users from running the installer on your server. Only someone with file system access should be able to install HUBzero.</p>
            </div>

            <?php if ($security->isLockedOut()) : ?>
            <div class="error-box">
                <h3>Access Temporarily Blocked</h3>
                <p>Too many failed verification attempts. Please wait <strong><?php echo ceil($security->getLockoutRemaining() / 60); ?> minute(s)</strong> before trying again.</p>
            </div>
            <?php else : ?>
            <div class="info-box">
                <h3>Create Verification File</h3>
                <p>Create an <strong>empty file</strong> with the following name in your document root:</p>

                <div class="filename-display">
                    <code id="verification-filename"><?php echo htmlspecialchars($security->getVerificationFilename()); ?></code>
                    <button type="button" class="btn btn-small btn-copy" onclick="copyFilename()" title="Copy filename">
                        Copy
                    </button>
                </div>

                <div class="file-location">
                    <strong>Location:</strong>
                    <code><?php echo htmlspecialchars($security->getDocumentRoot()); ?>/</code>
                </div>

                <div class="time-remaining">
                    <span class="timer-icon">&#9202;</span>
                    Time remaining: <strong id="time-remaining"><?php echo $security->formatTime($security->getTokenTimeRemaining()); ?></strong>
                </div>

                <div class="instructions-detail">
                    <h4>How to create the file:</h4>
                    <ul>
                        <li><strong>Via SSH/Terminal:</strong>
                            <code>touch <?php echo htmlspecialchars($security->getDocumentRoot() . '/' . $security->getVerificationFilename()); ?></code>
                        </li>
                        <li><strong>Via FTP:</strong> Create a new empty text file and upload it to your document root</li>
                        <li><strong>Via File Manager:</strong> Navigate to your document root and create an empty file with the exact name shown above</li>
                    </ul>
                </div>

                <?php
                $attempts = $security->getFailedAttempts();
                if ($attempts > 0) :
                    ?>
                <div class="attempts-warning">
                    <strong>Note:</strong> <?php echo $attempts; ?> of <?php echo $security->getMaxAttempts(); ?> attempts used.
                </div>
                <?php endif; ?>
            </div>

            <form method="post">
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($security->generateCsrfToken()); ?>">
                <input type="hidden" name="action" value="verify">

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">Verify &amp; Continue</button>
                </div>
            </form>
            <?php endif; ?>
        </div>

        <div class="footer">
            <p>&copy; <?php echo date('Y'); ?> HUBzero. Released under the MIT License.</p>
        </div>
    </div>

    <script>
    function copyFilename() {
        var filename = document.getElementById('verification-filename').textContent;
        navigator.clipboard.writeText(filename).then(function() {
            var btn = document.querySelector('.btn-copy');
            var originalText = btn.textContent;
            btn.textContent = 'Copied!';
            setTimeout(function() {
                btn.textContent = originalText;
            }, 2000);
        });
    }

    // Countdown timer
    (function() {
        var remaining = <?php echo $security->getTokenTimeRemaining(); ?>;
        var timerEl = document.getElementById('time-remaining');

        if (timerEl && remaining > 0) {
            var interval = setInterval(function() {
                remaining--;
                if (remaining <= 0) {
                    clearInterval(interval);
                    timerEl.innerHTML = '<span style="color: #dc3545;">Expired - Refreshing...</span>';
                    setTimeout(function() {
                        window.location.reload();
                    }, 1000);
                    return;
                }
                var minutes = Math.floor(remaining / 60);
                var seconds = remaining % 60;
                timerEl.textContent = minutes + ':' + (seconds < 10 ? '0' : '') + seconds;
            }, 1000);
        }
    })();
    </script>
</body>
</html>
