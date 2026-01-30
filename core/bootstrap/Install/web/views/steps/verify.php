<?php // phpcs:disable Generic.Files.LineLength ?>
<div class="verify-content">
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
        <div class="info-box verify-instructions">
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

        <form method="post" class="step-form">
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($security->generateCsrfToken()); ?>">

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Verify &amp; Next</button>
            </div>
        </form>

    <?php endif; ?>
</div>

<style>
.verify-content .warning-box {
    background: #fff3cd;
    border: 1px solid #ffc107;
    border-radius: 4px;
    padding: 1rem 1.25rem;
    margin-bottom: 1.5rem;
}

.verify-content .warning-box h3 {
    color: #856404;
    margin-top: 0;
    margin-bottom: 0.5rem;
    font-size: 1.1rem;
}

.verify-content .warning-box p {
    color: #856404;
    margin: 0;
}

.verify-content .error-box {
    background: #f8d7da;
    border: 1px solid #f5c6cb;
    border-radius: 4px;
    padding: 1rem 1.25rem;
    margin-bottom: 1.5rem;
}

.verify-content .error-box h3 {
    color: #721c24;
    margin-top: 0;
    margin-bottom: 0.5rem;
    font-size: 1.1rem;
}

.verify-content .error-box p {
    color: #721c24;
    margin: 0;
}

.verify-instructions {
    background: #e7f3ff;
    border: 1px solid #b6d4fe;
    border-radius: 4px;
    padding: 1.25rem;
    margin-bottom: 1.5rem;
}

.filename-display {
    background: #fff;
    border: 2px solid #0d6efd;
    border-radius: 4px;
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
    color: #0d6efd;
    letter-spacing: 0.1em;
    font-family: 'Courier New', Courier, monospace;
}

.btn-copy {
    padding: 0.25rem 0.75rem;
    font-size: 0.875rem;
    background: #0d6efd;
    color: white;
    border: none;
    border-radius: 3px;
    cursor: pointer;
}

.btn-copy:hover {
    background: #0b5ed7;
}

.file-location {
    background: #f8f9fa;
    padding: 0.5rem 0.75rem;
    border-radius: 4px;
    margin: 1rem 0;
    font-size: 0.9rem;
}

.file-location code {
    color: #495057;
}

.time-remaining {
    background: #fff;
    padding: 0.75rem 1rem;
    border-radius: 4px;
    margin: 1rem 0;
    text-align: center;
    font-size: 1.1rem;
}

.timer-icon {
    font-size: 1.2rem;
}

#time-remaining {
    color: #dc3545;
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
    border-radius: 3px;
    font-size: 0.85rem;
    display: inline-block;
    margin-top: 0.25rem;
    word-break: break-all;
}

.attempts-warning {
    background: #fff3cd;
    border: 1px solid #ffc107;
    border-radius: 4px;
    padding: 0.5rem 0.75rem;
    margin-top: 1rem;
    font-size: 0.9rem;
    color: #856404;
}
</style>

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
