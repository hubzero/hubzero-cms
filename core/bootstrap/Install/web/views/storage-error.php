<?php // phpcs:disable Generic.Files.LineLength ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HUBzero Installation - Configuration Required</title>
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
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
    font-size: 16px;
    line-height: 1.6;
    color: var(--gray-700);
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    min-height: 100vh;
}

.installer {
    max-width: 900px;
    margin: 0 auto;
    padding: 2rem 1rem;
}

.installer-header {
    text-align: center;
    color: white;
    margin-bottom: 2rem;
}

.logo svg {
    width: 180px;
    height: auto;
    margin-bottom: 1rem;
}

.installer-header h1 {
    font-size: 1.5rem;
    font-weight: 300;
    opacity: 0.9;
}

.installer-content {
    background: white;
    border-radius: 1rem;
    box-shadow: 0 20px 40px rgba(0,0,0,0.2);
    padding: 2rem;
}

.error-banner {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 1.5rem;
    padding: 1.5rem 2rem;
    background: linear-gradient(135deg, #dc2626, #b91c1c);
    color: white;
    border-radius: 1rem;
    margin-bottom: 2rem;
}

.error-icon {
    font-size: 3rem;
    flex-shrink: 0;
}

.error-text h2 {
    border: none;
    padding: 0;
    margin: 0;
    color: white;
    font-size: 1.5rem;
}

.error-text p {
    margin-top: 0.25rem;
    opacity: 0.9;
}

.explanation-box {
    background: var(--gray-50);
    padding: 1.5rem;
    border-radius: 0.5rem;
    margin-bottom: 1.5rem;
    border-left: 4px solid var(--primary-color);
}

.explanation-box h3 {
    margin-bottom: 0.75rem;
    font-size: 1.125rem;
    color: var(--gray-900);
}

.explanation-box p {
    margin-bottom: 0.5rem;
}

.tried-paths {
    margin: 1.5rem 0;
}

.tried-paths h3 {
    margin-bottom: 1rem;
    font-size: 1.125rem;
    color: var(--gray-900);
}

.path-item {
    display: flex;
    align-items: flex-start;
    gap: 1rem;
    padding: 1rem;
    border: 1px solid var(--gray-200);
    border-radius: 0.5rem;
    margin-bottom: 0.75rem;
}

.path-status {
    flex-shrink: 0;
    width: 2rem;
    height: 2rem;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.25rem;
}

.path-status.rejected {
    background: #fef2f2;
    color: var(--error-color);
}

.path-status.failed {
    background: #fffbeb;
    color: var(--warning-color);
}

.path-status.unavailable {
    background: var(--gray-100);
    color: var(--gray-500);
}

.path-details {
    flex: 1;
}

.path-location {
    font-weight: 600;
    color: var(--gray-900);
}

.path-value {
    font-family: monospace;
    font-size: 0.875rem;
    color: var(--gray-500);
    word-break: break-all;
}

.path-reason {
    font-size: 0.875rem;
    color: var(--error-color);
    margin-top: 0.25rem;
}

.path-item.rejected .path-reason {
    color: var(--error-color);
}

.path-item.failed .path-reason {
    color: var(--warning-color);
}

.path-item.unavailable .path-reason {
    color: var(--gray-500);
}

.solutions-box {
    background: #f0fdf4;
    border: 1px solid #bbf7d0;
    padding: 1.5rem;
    border-radius: 0.5rem;
    margin-bottom: 1.5rem;
}

.solutions-box h3 {
    margin-bottom: 0.75rem;
    font-size: 1.125rem;
    color: var(--success-color);
}

.solutions-box ol {
    margin-left: 1.5rem;
}

.solutions-box li {
    margin-bottom: 0.75rem;
}

.solutions-box code {
    background: rgba(0,0,0,0.05);
    padding: 0.2rem 0.4rem;
    border-radius: 0.25rem;
    font-size: 0.875rem;
}

.docroot-info {
    background: var(--gray-50);
    padding: 1rem;
    border-radius: 0.5rem;
    margin-bottom: 1.5rem;
    font-size: 0.875rem;
}

.docroot-info strong {
    color: var(--gray-900);
}

.docroot-info code {
    background: white;
    padding: 0.2rem 0.4rem;
    border-radius: 0.25rem;
    font-family: monospace;
    word-break: break-all;
}

.form-actions {
    display: flex;
    gap: 1rem;
    margin-top: 2rem;
    padding-top: 1.5rem;
    border-top: 1px solid var(--gray-200);
    justify-content: center;
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

.btn-large {
    padding: 1rem 2rem;
    font-size: 1.125rem;
}

.installer-footer {
    text-align: center;
    color: rgba(255,255,255,0.6);
    margin-top: 2rem;
    font-size: 0.875rem;
}
    </style>
</head>
<body>
    <div class="installer">
        <header class="installer-header">
            <div class="logo">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 294.6 62.9" width="150">
                    <path fill="currentColor" d="M87.3 15.7v28.9c0 10.2-7.7 18.3-20.4 18.3-9.7 0-19.7-5.5-19.7-19.9V17L58 15.7v25.7c0 4.9.9 11 9.4 11 7.6 0 9-5.2 9-11.9V17.1l10.9-1.4zM177.2 17.5h-28l-4.1 5.1H167l-31.4 39h40.1l1.4-5.1h-31.4M234 26.3h.2c3.2-7.6 8.2-9.9 16.4-9.9l-2.2 5.2c-.5-.1-1.3-.1-2-.1-5.5 0-12.3 6.9-12.3 17.9v22.2l-5.1 1.1V17.5l5.1-1.1v9.9zM294.6 39.5c0 11.9-9.6 23.1-22.6 23.1-13 0-22.6-11.3-22.6-23.1 0-11.9 9.6-23.1 22.6-23.1 13 0 22.6 11.3 22.6 23.1zm-40.1 0c0 9.4 7.6 18 17.5 18s17.5-8.6 17.5-18-7.6-18-17.5-18c-10 0-17.5 8.6-17.5 18zM215.1 48.9c-3 5.1-8.4 8.7-14.9 8.7-9 0-16.1-7.1-17.3-15.5h39.7c.1-.8.2-1.7.2-2.5 0-11.9-9.6-23.1-22.6-23.1s-22.6 11.3-22.6 23.1c0 11.9 9.6 23.1 22.6 23.1 9.5 0 17-6 20.5-13.8h-5.6zm-14.9-27.4c9 0 16.1 7.1 17.3 15.4h-34.6c1.3-8.3 8.3-15.4 17.3-15.4zM24.5 15.5c-6.2 0-9.9 1.4-13.1 5.6h-.2V.1L0 3.3v59l11.2-.7V36.2c0-5.6 3.3-11 10.3-11 5 0 7.2 4 7.2 8.7v28.4l11.2-.7V30.9c-.1-9.5-6.2-15.4-15.4-15.4zM141.2 31.1c-3.3-9.2-11.8-15.5-21.4-15.5-5.5 0-9.2 1.6-12.9 5.2V0L95.6 3.2v35.4c0 13.5 9.2 23.1 19.9 24.3l9-11.1c-1.4.7-3 1.1-4.5 1.1-7.5 0-13.2-6.2-13.2-13.5s5.7-13.5 13.2-13.5c6.3 0 12 6.2 12 13.5 0 1.3-.2 2.6-.5 3.8l9.7-12.1z"/>
                </svg>
            </div>
            <h1>Installation Wizard</h1>
        </header>

        <main class="installer-content">
            <div class="error-banner">
                <div class="error-icon">&#9888;</div>
                <div class="error-text">
                    <h2>Server Configuration Required</h2>
                    <p>The installer cannot create a secure storage directory.</p>
                </div>
            </div>

            <div class="explanation-box">
                <h3>What's happening?</h3>
                <p>For security, the HUBzero installer needs to store sensitive data in a location that is <strong>not accessible via the web</strong>. This prevents potential attackers from accessing installation secrets even if there are web server misconfigurations.</p>
                <p>The installer tried to use the following locations but none were available:</p>
            </div>

            <div class="tried-paths">
                <h3>Locations Tried</h3>
                <?php foreach ($tried as $attempt) : ?>
                <div class="path-item <?php echo htmlspecialchars($attempt['status']); ?>">
                    <div class="path-status <?php echo htmlspecialchars($attempt['status']); ?>">
                        <?php if ($attempt['status'] === 'rejected') : ?>
                            &#10008;
                        <?php elseif ($attempt['status'] === 'failed') : ?>
                            &#9888;
                        <?php else : ?>
                            &#8212;
                        <?php endif; ?>
                    </div>
                    <div class="path-details">
                        <div class="path-location"><?php echo htmlspecialchars($attempt['location']); ?></div>
                        <?php if ($attempt['path']) : ?>
                        <div class="path-value"><?php echo htmlspecialchars($attempt['path']); ?></div>
                        <?php endif; ?>
                        <div class="path-reason"><?php echo htmlspecialchars($attempt['reason']); ?></div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>

            <div class="docroot-info">
                <strong>Document Root:</strong> <code><?php echo htmlspecialchars($docRoot); ?></code>
                <p style="margin-top: 0.5rem; margin-bottom: 0;">The storage directory must be located <em>outside</em> this path for security.</p>
            </div>

            <div class="solutions-box">
                <h3>How to Fix This</h3>
                <ol>
                    <li>
                        <strong>Ensure the PHP process owner has a home directory outside the document root.</strong><br>
                        The web server typically runs as a user like <code>www-data</code>, <code>apache</code>, or <code>nginx</code>.
                        Check that this user has a home directory that is writable and located outside <code><?php echo htmlspecialchars($docRoot); ?></code>.
                    </li>
                    <li>
                        <strong>Configure PHP's session.save_path.</strong><br>
                        Set <code>session.save_path</code> in your <code>php.ini</code> to a writable directory outside the document root, such as <code>/var/lib/php/sessions</code>.
                    </li>
                    <li>
                        <strong>Check directory permissions.</strong><br>
                        Ensure the web server user can write to its home directory or the session save path.
                    </li>
                </ol>
            </div>

            <div class="form-actions">
                <a href="?" class="btn btn-primary btn-large">Retry</a>
            </div>
        </main>

        <footer class="installer-footer">
            <p>&copy; <?php echo date('Y'); ?> HUBzero. Released under the MIT License.</p>
        </footer>
    </div>
</body>
</html>
