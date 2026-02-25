<?php
// Extension package names for each OS
$extPackages = [
    'pdo' => ['debian' => 'php-pdo', 'rhel' => 'php-pdo', 'mac' => ''],
    'pdo_mysql' => ['debian' => 'php-mysql', 'rhel' => 'php-mysqlnd', 'mac' => ''],
    'json' => ['debian' => '', 'rhel' => '', 'mac' => ''], // Built-in PHP 8+
    'mbstring' => ['debian' => 'php-mbstring', 'rhel' => 'php-mbstring', 'mac' => ''],
    'openssl' => ['debian' => 'php-openssl', 'rhel' => 'php-openssl', 'mac' => ''],
    'curl' => ['debian' => 'php-curl', 'rhel' => 'php-curl', 'mac' => ''],
    'gd' => ['debian' => 'php-gd', 'rhel' => 'php-gd', 'mac' => 'gd'],
    'fileinfo' => ['debian' => 'php-fileinfo', 'rhel' => 'php-fileinfo', 'mac' => ''],
    'zip' => ['debian' => 'php-zip', 'rhel' => 'php-zip', 'mac' => ''],
];

// Build install commands for missing extensions
$missingExtensions = $requirements['php_extensions']['missing'] ?? [];
$debianPkgs = [];
$rhelPkgs = [];
$macPkgs = [];

foreach ($missingExtensions as $ext) {
    if (isset($extPackages[$ext])) {
        if (!empty($extPackages[$ext]['debian'])) {
            $debianPkgs[] = $extPackages[$ext]['debian'];
        }
        if (!empty($extPackages[$ext]['rhel'])) {
            $rhelPkgs[] = $extPackages[$ext]['rhel'];
        }
        if (!empty($extPackages[$ext]['mac'])) {
            $macPkgs[] = $extPackages[$ext]['mac'];
        }
    }
}

// Help text for each requirement
$helpText = [
    'os' => [
        'title' => 'Operating System',
        'debian' => 'HUBzero is designed for Linux servers. Debian/Ubuntu is fully supported.',
        'rhel' => 'RHEL, CentOS, Rocky Linux, and Oracle Linux are fully supported.',
        'mac' => 'macOS is supported for development environments.',
    ],
    'php_version' => [
        'title' => 'PHP Version',
        'debian' => 'sudo apt update && sudo apt install php8.3',
        'rhel' => 'sudo dnf install php',
        'mac' => 'brew install php@8.3',
    ],
    'php_extensions' => [
        'title' => 'PHP Extensions',
        'debian' => empty($debianPkgs)
            ? 'All required extensions are installed.'
            : 'sudo apt install ' . implode(' ', $debianPkgs) . '\nsudo systemctl restart apache2  # or php-fpm',
        'rhel' => empty($rhelPkgs)
            ? 'All required extensions are installed.'
            : 'sudo dnf install ' . implode(' ', $rhelPkgs) . '\nsudo systemctl restart httpd  # or php-fpm',
        'mac' => empty($macPkgs)
            ? 'All required extensions are included with PHP.'
            : 'brew install ' . implode(' ', $macPkgs) . '\nbrew services restart php',
    ],
    'cmd_unzip' => [
        'title' => 'Unzip Command',
        'debian' => 'sudo apt install unzip',
        'rhel' => 'sudo dnf install unzip',
        'mac' => 'brew install unzip (or use built-in)',
    ],
    'vendor' => [
        'title' => 'Composer Dependencies',
        'debian' => 'cd core && composer install',
        'rhel' => 'cd core && composer install',
        'mac' => 'cd core && composer install',
    ],
    'writable_app' => [
        'title' => 'Document Root Permissions',
        'debian' => 'sudo chown -R www-data:www-data /path/to/hubzero\nsudo chmod -R 755 /path/to/hubzero',
        'rhel' => 'sudo chown -R apache:apache /path/to/hubzero\nsudo chmod -R 755 /path/to/hubzero',
        'mac' => 'sudo chown -R _www:_www /path/to/hubzero\nsudo chmod -R 755 /path/to/hubzero',
    ],
];
?>
<style>
/* Help icon styles */
.help-icon {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 1.25rem;
    height: 1.25rem;
    border-radius: 50%;
    background: var(--gray-200);
    color: var(--gray-500);
    font-size: 0.75rem;
    font-weight: bold;
    cursor: pointer;
    margin-right: 0.5rem;
    transition: all 0.2s;
    vertical-align: middle;
}

.help-icon:hover {
    background: var(--primary-color);
    color: white;
}

/* Modal styles */
.modal-overlay {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0,0,0,0.5);
    display: none;
    align-items: center;
    justify-content: center;
    z-index: 1000;
    padding: 1rem;
}

.modal-overlay.active {
    display: flex;
}

.modal {
    background: white;
    border-radius: 1rem;
    max-width: 600px;
    width: 100%;
    max-height: 90vh;
    overflow-y: auto;
    box-shadow: 0 20px 40px rgba(0,0,0,0.3);
}

.modal-header {
    padding: 1.25rem 1.5rem;
    border-bottom: 1px solid var(--gray-200);
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.modal-header h3 {
    margin: 0;
    font-size: 1.125rem;
    color: var(--gray-900);
}

.modal-close {
    background: none;
    border: none;
    font-size: 1.5rem;
    cursor: pointer;
    color: var(--gray-500);
    line-height: 1;
}

.modal-close:hover {
    color: var(--gray-900);
}

.modal-body {
    padding: 1.5rem;
}

.os-section {
    margin-bottom: 1.25rem;
}

.os-section:last-child {
    margin-bottom: 0;
}

.os-label {
    font-weight: 600;
    color: var(--gray-700);
    margin-bottom: 0.5rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.os-label .os-icon {
    font-size: 1rem;
}

.os-command {
    background: var(--gray-900);
    color: #10b981;
    padding: 0.75rem 1rem;
    border-radius: 0.375rem;
    font-family: monospace;
    font-size: 0.875rem;
    white-space: pre-wrap;
    word-break: break-all;
}
</style>

<div class="requirements-content">
    <p>The following system requirements have been checked. All items must pass before you can continue.</p>

    <table class="requirements-table">
        <thead>
            <tr>
                <th>Requirement</th>
                <th>Required</th>
                <th>Current</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($requirements as $key => $req) : ?>
                <?php
                $rowClass = $req['passed'] ? 'passed' : 'failed';
                if (isset($req['warning']) && $req['warning']) {
                    $rowClass = 'warning';
                }
                ?>
            <tr class="<?php echo $rowClass; ?>">
                <td>
                    <?php if (isset($helpText[$key])) : ?>
                    <span class="help-icon" onclick="showHelp('<?php echo $key; ?>')" title="How to install">?</span>
                    <?php endif; ?>
                    <?php echo htmlspecialchars($req['name']); ?>
                    <?php if (!empty($req['description']) && !$req['passed']) : ?>
                        <br><small style="color: var(--gray-500);">
                            <?php echo htmlspecialchars($req['description']); ?>
                        </small>
                    <?php endif; ?>
                </td>
                <td><?php echo htmlspecialchars($req['required']); ?></td>
                <td><?php echo htmlspecialchars($req['current']); ?></td>
                <td class="status">
                    <?php if ($req['passed']) : ?>
                        <span class="status-pass">&#10004;</span>
                    <?php elseif (isset($req['warning']) && $req['warning']) : ?>
                        <span class="status-warn">&#9888;</span>
                    <?php else : ?>
                        <span class="status-fail">&#10008;</span>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <form method="post" class="step-form">
        <?php $reqCsrf = htmlspecialchars($installer->getSecurityGuard()->generateCsrfToken()); ?>
        <input type="hidden" name="csrf_token" value="<?php echo $reqCsrf; ?>">
        <div class="form-actions">
            <a href="?step=welcome" class="btn btn-secondary">Back</a>
            <?php if ($passed) : ?>
                <button type="submit" class="btn btn-primary">Next</button>
            <?php else : ?>
                <a href="?step=requirements" class="btn btn-secondary">Check Again</a>
            <?php endif; ?>
        </div>
        <?php if (!$passed) : ?>
            <p class="error-message" style="margin-top: 1rem;">
                Please fix the failed requirements before continuing.
            </p>
        <?php endif; ?>
    </form>
</div>

<!-- Help Modal -->
<div class="modal-overlay" id="helpModal" onclick="closeHelpModal(event)">
    <div class="modal" onclick="event.stopPropagation()">
        <div class="modal-header">
            <h3 id="modalTitle">Installation Help</h3>
            <button class="modal-close" onclick="closeHelpModal()">&times;</button>
        </div>
        <div class="modal-body" id="modalBody"></div>
    </div>
</div>

<script>
var helpData = <?php echo json_encode($helpText); ?>;

function showHelp(key) {
    var data = helpData[key];
    if (!data) return;

    document.getElementById('modalTitle').textContent = 'How to install: ' + data.title;

    var html = '';
    html += '<div class="os-section">';
    html += '<div class="os-label"><span class="os-icon">&#128039;</span> Debian / Ubuntu</div>';
    html += '<div class="os-command">' + escapeHtml(data.debian).replace(/\\n/g, '\n') + '</div>';
    html += '</div>';

    html += '<div class="os-section">';
    html += '<div class="os-label"><span class="os-icon">&#127744;</span> RHEL / CentOS / Rocky / Oracle</div>';
    html += '<div class="os-command">' + escapeHtml(data.rhel).replace(/\\n/g, '\n') + '</div>';
    html += '</div>';

    html += '<div class="os-section">';
    html += '<div class="os-label"><span class="os-icon">&#127822;</span> macOS</div>';
    html += '<div class="os-command">' + escapeHtml(data.mac).replace(/\\n/g, '\n') + '</div>';
    html += '</div>';

    document.getElementById('modalBody').innerHTML = html;
    document.getElementById('helpModal').classList.add('active');
}

function closeHelpModal(event) {
    if (!event || event.target === document.getElementById('helpModal')) {
        document.getElementById('helpModal').classList.remove('active');
    }
}

function escapeHtml(text) {
    var div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') closeHelpModal();
});
</script>
