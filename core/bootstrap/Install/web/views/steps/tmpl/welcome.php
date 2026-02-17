<div class="welcome-content">
    <p class="lead">Welcome to the HUBzero installation wizard. This will guide you through setting up your new hub.</p>

    <div class="info-box">
        <h3>Before You Begin</h3>
        <p>Make sure you have the following information ready:</p>
        <ul>
            <li>MySQL database credentials (host, database name, username, password)</li>
            <li>Site name and administrator email address</li>
            <li>A password for the administrator account</li>
        </ul>
    </div>

    <div class="license-box">
        <h3>License Agreement</h3>
        <div class="license-text">
            <p><strong>MIT License</strong></p>
            <p>Copyright (c) 2005-<?php echo date('Y'); ?> The Regents of the University of California</p>
            <p>Permission is hereby granted, free of charge, to any person obtaining a copy
            of this software and associated documentation files (the "Software"), to deal
            in the Software without restriction, including without limitation the rights to
            use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies
            of the Software, and to permit persons to whom the Software is furnished to do
            so, subject to the following conditions:</p>
            <p>The above copyright notice and this permission notice shall be included in
            all copies or substantial portions of the Software.</p>
            <p>THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
            IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
            FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
            AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
            LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
            OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
            THE SOFTWARE.</p>
        </div>
    </div>

    <form method="post" class="step-form">
        <?php $welcomeCsrf = htmlspecialchars($installer->getSecurityGuard()->generateCsrfToken()); ?>
        <input type="hidden" name="csrf_token" value="<?php echo $welcomeCsrf; ?>">
        <div class="form-group checkbox-group">
            <label>
                <input type="checkbox" name="accept_license" value="1" required>
                I accept the terms of the MIT License
            </label>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Next</button>
        </div>
    </form>
</div>
