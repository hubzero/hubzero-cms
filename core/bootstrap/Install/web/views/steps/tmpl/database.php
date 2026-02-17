<?php

// Determine current connection type from config
$connectionType = '';
$hostVal = '';
$portVal = '';
$socketVal = '';

if (!empty($config['socket'])) {
    $connectionType = 'socket';
    $socketVal = htmlspecialchars($config['socket']);
} elseif (!empty($config['host'])) {
    $host = $config['host'];
    $port = $config['port'] ?? '';

    // Check if it's the standard localhost setup
    if (($host === 'localhost' || $host === '127.0.0.1') && (empty($port) || $port === '3306' || $port === 3306)) {
        $connectionType = 'localhost';
    } else {
        $connectionType = 'custom';
        $hostVal = htmlspecialchars($host);
        $portVal = htmlspecialchars($port);
    }
}

$dbVal = htmlspecialchars($config['db'] ?? '');
$userVal = htmlspecialchars($config['user'] ?? '');
$passVal = htmlspecialchars($config['password'] ?? '');
$prefixVal = htmlspecialchars($config['dbprefix'] ?? 'jos_');

// Default socket path for display
$defaultSocket = '/var/run/mysqld/mysqld.sock';
if (empty($socketVal)) {
    $socketVal = $defaultSocket;
}

// Determine initial substep based on existing config
$initialSubstep = 1;
if (!empty($connectionType)) {
    // If we have connection type, go to setup mode step (step 3)
    // But first check if it's localhost (skip step 2) or socket/custom (goes through step 2)
    $initialSubstep = ($connectionType === 'localhost') ? 3 : 2;
    // If we have credentials too, go to step 4 (credentials)
    if (!empty($dbVal) || !empty($userVal)) {
        $initialSubstep = 4;
    }
}
?>
<style>
/* Substep indicator */
.substep-indicator {
    display: flex;
    justify-content: center;
    margin-bottom: 2rem;
    padding-bottom: 1rem;
    border-bottom: 1px solid var(--gray-200);
}

.substep-dots {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

/* Center the form content */
#database-form {
    max-width: 100%;
}

.substep-panel {
    /* No max-width here - let substep-actions span full width */
}

/* Constrain content elements within panels */
.substep-panel .substep-title,
.substep-panel .substep-desc,
.substep-panel .connection-types,
.substep-panel .form-group,
.substep-panel .form-row,
.substep-panel .connection-result {
    max-width: 500px;
}

.substep-dot {
    width: 0.75rem;
    height: 0.75rem;
    border-radius: 50%;
    background: var(--gray-300);
    transition: all 0.3s;
}

.substep-dot.active {
    background: var(--primary-color);
    transform: scale(1.2);
}

.substep-dot.complete {
    background: var(--success-color);
}

.substep-connector {
    width: 2rem;
    height: 2px;
    background: var(--gray-300);
    transition: background 0.3s;
}

.substep-connector.complete {
    background: var(--success-color);
}

/* Substep panels */
.substep-panel {
    display: none;
    animation: slideIn 0.3s ease;
}

.substep-panel.active {
    display: block;
}

@keyframes slideIn {
    from { opacity: 0; transform: translateX(20px); }
    to { opacity: 1; transform: translateX(0); }
}

/* Connection type cards */
.connection-types {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
}

.connection-type-option {
    display: flex;
    align-items: flex-start;
    padding: 1.25rem;
    border: 2px solid var(--gray-200);
    border-radius: 0.5rem;
    cursor: pointer;
    transition: all 0.2s;
}

.connection-type-option:hover {
    border-color: var(--primary-color);
    background: var(--gray-50);
}

.connection-type-option.selected {
    border-color: var(--primary-color);
    background: rgba(37, 99, 235, 0.05);
}

.connection-type-option input[type="radio"] {
    margin-top: 0.25rem;
    margin-right: 1rem;
    width: 1.25rem;
    height: 1.25rem;
}

.connection-type-info {
    flex: 1;
}

.connection-type-label {
    font-weight: 600;
    color: var(--gray-900);
    display: block;
    margin-bottom: 0.25rem;
}

.connection-type-desc {
    font-size: 0.875rem;
    color: var(--gray-500);
}

.connection-type-dsn {
    font-family: monospace;
    font-size: 0.8rem;
    color: var(--gray-400);
    margin-top: 0.25rem;
    display: block;
}

.connection-type-error {
    display: block;
    margin-top: 0.5rem;
    padding: 0.5rem 0.75rem;
    background: #fef2f2;
    color: var(--error-color);
    font-size: 0.85rem;
    border-radius: 0.25rem;
    border-left: 3px solid var(--error-color);
}

/* Form styling */
.substep-title {
    font-size: 1.125rem;
    color: var(--gray-900);
    margin-bottom: 0.5rem;
}

.substep-desc {
    color: var(--gray-500);
    font-size: 0.9rem;
    margin-bottom: 1.5rem;
}

.substep-title + .connection-types,
.substep-title + .form-group,
.substep-title + .form-row {
    margin-top: 1.5rem;
}

.form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1rem;
}

@media (max-width: 600px) {
    .form-row {
        grid-template-columns: 1fr;
    }
}

/* Connection result */
.connection-result {
    margin-top: 1rem;
    margin-bottom: 1rem;
}

.connection-result.testing {
    color: var(--gray-500);
    padding: 0.75rem 1rem;
}

.connection-result.success {
    background: #f0fdf4;
    color: var(--success-color);
    padding: 0.75rem 1rem;
    border-radius: 0.5rem;
    border: 1px solid #bbf7d0;
}

.connection-result.error {
    background: #fef2f2;
    color: var(--error-color);
    padding: 0.75rem 1rem;
    border-radius: 0.5rem;
    border: 1px solid #fecaca;
}

/* Navigation buttons */
.substep-actions {
    display: flex;
    justify-content: flex-end;
    gap: 1rem;
    margin-top: 2rem;
    padding-top: 1.5rem;
    border-top: 1px solid var(--gray-200);
}

.substep-actions .btn-test {
    margin-right: auto;
}

/* Hint text styling - more faded to distinguish from regular text */
.form-group small {
    color: var(--gray-400);
    font-size: 0.8rem;
    font-style: italic;
}

/* Error text styling */
.form-group .error {
    color: var(--error-color);
    font-size: 0.875rem;
    display: block;
    margin-top: 0.25rem;
}
</style>

<div class="database-content">
    <form method="post" class="step-form" id="database-form">
        <?php $dbCsrf = htmlspecialchars($installer->getSecurityGuard()->generateCsrfToken()); ?>
        <input type="hidden" name="csrf_token" value="<?php echo $dbCsrf; ?>">
        <input type="hidden" name="connection_type"
            id="connection_type_hidden"
            value="<?php echo htmlspecialchars($connectionType); ?>">
        <input type="hidden" name="setup_mode" id="setup_mode_hidden" value="existing">

        <!-- Substep indicator -->
        <div class="substep-indicator">
            <div class="substep-dots">
                <div class="substep-dot" data-step="1"></div>
                <div class="substep-connector"></div>
                <div class="substep-dot" data-step="2"></div>
                <div class="substep-connector"></div>
                <div class="substep-dot" data-step="3"></div>
                <div class="substep-connector"></div>
                <div class="substep-dot" data-step="4"></div>
                <div class="substep-connector create-mode-only" style="display: none;"></div>
                <div class="substep-dot create-mode-only" data-step="5" style="display: none;"></div>
            </div>
        </div>

        <!-- Substep 1: Connection Method -->
        <div class="substep-panel" data-substep="1">
            <h3 class="substep-title">How should we connect to MySQL?</h3>

            <div class="connection-types">
                <?php $localhostSel = $connectionType === 'localhost' ? 'selected' : ''; ?>
                <?php $localhostChk = $connectionType === 'localhost' ? 'checked' : ''; ?>
                <label class="connection-type-option <?php echo $localhostSel; ?>"
                    data-type="localhost">
                    <input type="radio" name="connection_type_select"
                        value="localhost" <?php echo $localhostChk; ?>>
                    <div class="connection-type-info">
                        <span class="connection-type-label">Standard (localhost)</span>
                        <span class="connection-type-desc">MySQL running on the same server with default port</span>
                        <span class="connection-type-dsn">127.0.0.1:3306</span>
                        <span class="connection-type-error" id="localhost-error" style="display: none;"></span>
                    </div>
                </label>

                <?php $socketSel = $connectionType === 'socket' ? 'selected' : ''; ?>
                <?php $socketChk = $connectionType === 'socket' ? 'checked' : ''; ?>
                <label class="connection-type-option <?php echo $socketSel; ?>">
                    <input type="radio" name="connection_type_select"
                        value="socket" <?php echo $socketChk; ?>>
                    <div class="connection-type-info">
                        <span class="connection-type-label">Unix Socket</span>
                        <span class="connection-type-desc">Connect via local socket file
                            (faster, Linux/macOS only)</span>
                        <span class="connection-type-dsn">/var/run/mysqld/mysqld.sock</span>
                    </div>
                </label>

                <?php $customSel = $connectionType === 'custom' ? 'selected' : ''; ?>
                <?php $customChk = $connectionType === 'custom' ? 'checked' : ''; ?>
                <label class="connection-type-option <?php echo $customSel; ?>">
                    <input type="radio" name="connection_type_select"
                        value="custom" <?php echo $customChk; ?>>
                    <div class="connection-type-info">
                        <span class="connection-type-label">Custom</span>
                        <span class="connection-type-desc">Remote server or non-standard port</span>
                    </div>
                </label>
            </div>

            <div class="substep-actions">
                <a href="?step=requirements" class="btn btn-secondary">Back</a>
                <button type="button" class="btn btn-primary" id="step1-next" disabled>Next</button>
            </div>
        </div>

        <!-- Substep 2: Server Configuration (socket or custom only) -->
        <div class="substep-panel" data-substep="2">
            <!-- Socket fields -->
            <div id="socket-fields" style="display: none;">
                <h3 class="substep-title">Socket Configuration</h3>
                <div class="form-group">
                    <label for="socket">Socket Path</label>
                    <input type="text" id="socket" name="socket"
                        value="<?php echo $socketVal; ?>"
                        placeholder="/var/run/mysqld/mysqld.sock">
                    <small>Path to the MySQL socket file on this server</small>
                </div>
                <div id="socket-connection-error" class="connection-result error" style="display: none;"></div>
            </div>

            <!-- Custom fields -->
            <div id="custom-fields" style="display: none;">
                <h3 class="substep-title">Server Configuration</h3>
                <div class="form-row">
                    <div class="form-group <?php echo isset($errors['host']) ? 'has-error' : ''; ?>">
                        <label for="host">Hostname / IP Address</label>
                        <input type="text" id="host" name="host"
                            value="<?php echo $hostVal; ?>"
                            placeholder="db.example.com">
                        <?php if (isset($errors['host'])) : ?>
                            <span class="error"><?php echo htmlspecialchars($errors['host']); ?></span>
                        <?php endif; ?>
                    </div>

                    <div class="form-group">
                        <label for="port">Port</label>
                        <input type="text" id="port" name="port" value="<?php echo $portVal; ?>" placeholder="3306">
                        <small>Default: 3306</small>
                    </div>
                </div>
                <div id="custom-connection-error" class="connection-result error" style="display: none;"></div>
            </div>

            <div class="substep-actions">
                <button type="button" class="btn btn-secondary btn-back" id="step2-back">Back</button>
                <button type="button" class="btn btn-primary" id="step2-next">Next</button>
            </div>
        </div>

        <!-- Substep 3: Database Setup Mode -->
        <div class="substep-panel" data-substep="3">
            <h3 class="substep-title">Database Setup</h3>

            <div class="connection-types">
                <label class="connection-type-option selected" data-type="existing">
                    <input type="radio" name="setup_mode_select" value="existing" checked>
                    <div class="connection-type-info">
                        <span class="connection-type-label">I have credentials ready</span>
                        <span class="connection-type-desc">Use an existing database and user account
                            that has already been created</span>
                    </div>
                </label>

                <label class="connection-type-option" data-type="create">
                    <input type="radio" name="setup_mode_select" value="create">
                    <div class="connection-type-info">
                        <span class="connection-type-label">Create a database for me</span>
                        <span class="connection-type-desc">Enter MySQL admin credentials (like root)
                            to automatically create the database and user</span>
                    </div>
                </label>
            </div>

            <div class="substep-actions">
                <button type="button" class="btn btn-secondary btn-back" id="step3-back">Back</button>
                <button type="button" class="btn btn-primary" id="step3-next">Next</button>
            </div>
        </div>

        <!-- Substep 4: Credentials (existing mode) or Admin Credentials (create mode) -->
        <div class="substep-panel" data-substep="4">
            <!-- Existing mode: Enter existing database credentials -->
            <div id="existing-credentials" style="display: block;">
                <h3 class="substep-title">Database Credentials</h3>

                <div class="form-group <?php echo isset($errors['db']) ? 'has-error' : ''; ?>">
                    <label for="db">Database Name</label>
                    <input type="text" id="db" name="db" value="<?php echo $dbVal; ?>" placeholder="hubzero">
                    <small>Will be created if it doesn't exist (requires privileges)</small>
                    <?php if (isset($errors['db'])) : ?>
                        <span class="error"><?php echo htmlspecialchars($errors['db']); ?></span>
                    <?php endif; ?>
                </div>

                <div class="form-row">
                    <div class="form-group <?php echo isset($errors['user']) ? 'has-error' : ''; ?>">
                        <label for="user">Username</label>
                        <input type="text" id="user" name="user"
                            value="<?php echo $userVal; ?>" placeholder="hubzero">
                        <?php if (isset($errors['user'])) : ?>
                            <span class="error"><?php echo htmlspecialchars($errors['user']); ?></span>
                        <?php endif; ?>
                    </div>

                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" id="password" name="password" value="<?php echo $passVal; ?>">
                    </div>
                </div>

                <div id="connection-result" class="connection-result" style="display: none;"></div>

                <div class="substep-actions">
                    <button type="button" id="test-connection"
                        class="btn btn-secondary btn-test">Test Connection</button>
                    <button type="button" class="btn btn-secondary" id="step4-back-existing">Back</button>
                    <button type="submit" class="btn btn-primary">Next</button>
                </div>
            </div>

            <!-- Create mode: Enter admin credentials to connect -->
            <div id="admin-credentials" style="display: none;">
                <h3 class="substep-title">Administrator Credentials</h3>
                <p class="substep-desc">Enter credentials for a MySQL user with privileges
                    to create databases and users (typically root).</p>

                <div class="form-row">
                    <div class="form-group">
                        <label for="admin_user">Admin Username</label>
                        <input type="text" id="admin_user" name="admin_user" value="root" placeholder="root">
                    </div>

                    <div class="form-group">
                        <label for="admin_password">Admin Password</label>
                        <input type="password" id="admin_password" name="admin_password" value="">
                    </div>
                </div>

                <div id="admin-connection-result" class="connection-result" style="display: none;"></div>

                <div class="substep-actions">
                    <button type="button" id="test-admin-connection"
                        class="btn btn-secondary btn-test">Test Connection</button>
                    <button type="button" class="btn btn-secondary" id="step4-back-create">Back</button>
                    <button type="button" class="btn btn-primary" id="step4-next-create">Next</button>
                </div>
            </div>
        </div>

        <!-- Substep 5: New Database/User Details (create mode only) -->
        <div class="substep-panel" data-substep="5">
            <h3 class="substep-title">New Database & User</h3>
            <p class="substep-desc">Enter the details for the new database and user account to create.</p>

            <div class="form-group">
                <label for="new_db">Database Name</label>
                <input type="text" id="new_db" name="new_db" value="hubzero" placeholder="hubzero">
                <small>Name for the new database</small>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="new_user">Username</label>
                    <input type="text" id="new_user" name="new_user" value="hubzero" placeholder="hubzero">
                    <small>Username for the new database user</small>
                </div>

                <div class="form-group">
                    <label for="new_password">Password</label>
                    <input type="password" id="new_password" name="new_password" value="">
                    <small>Password for the new user</small>
                </div>
            </div>

            <div id="create-result" class="connection-result" style="display: none;"></div>

            <div class="substep-actions">
                <button type="button" class="btn btn-secondary btn-back" id="step5-back">Back</button>
                <button type="button" id="create-database" class="btn btn-primary">Create Database & Next</button>
            </div>
        </div>
    </form>
</div>

<script>
(function() {
    var currentSubstep = <?php echo $initialSubstep; ?>;
    var connectionType = '<?php echo $connectionType; ?>';
    var setupMode = 'existing';

    var panels = document.querySelectorAll('.substep-panel');
    var dots = document.querySelectorAll('.substep-dot');
    var connectors = document.querySelectorAll('.substep-connector');
    var connectionOptions = document.querySelectorAll('input[name="connection_type_select"]');
    var setupModeOptions = document.querySelectorAll('input[name="setup_mode_select"]');
    var hiddenConnectionType = document.getElementById('connection_type_hidden');
    var hiddenSetupMode = document.getElementById('setup_mode_hidden');

    var step1Next = document.getElementById('step1-next');
    var step2Back = document.getElementById('step2-back');
    var step2Next = document.getElementById('step2-next');
    var step3Back = document.getElementById('step3-back');
    var step3Next = document.getElementById('step3-next');
    var step4BackExisting = document.getElementById('step4-back-existing');
    var step4BackCreate = document.getElementById('step4-back-create');
    var step4NextCreate = document.getElementById('step4-next-create');
    var step5Back = document.getElementById('step5-back');

    var socketFields = document.getElementById('socket-fields');
    var customFields = document.getElementById('custom-fields');
    var existingCredentials = document.getElementById('existing-credentials');
    var adminCredentials = document.getElementById('admin-credentials');
    var createModeIndicators = document.querySelectorAll('.create-mode-only');

    function showSubstep(step) {
        currentSubstep = step;

        // Update panels
        panels.forEach(function(panel) {
            panel.classList.remove('active');
            if (parseInt(panel.dataset.substep) === step) {
                panel.classList.add('active');
            }
        });

        // Update dots
        dots.forEach(function(dot, index) {
            var dotStep = index + 1;
            dot.classList.remove('active', 'complete');
            if (dotStep === step) {
                dot.classList.add('active');
            } else if (dotStep < step) {
                dot.classList.add('complete');
            }
        });

        // Update connectors
        connectors.forEach(function(conn, index) {
            conn.classList.remove('complete');
            if (index + 1 < step) {
                conn.classList.add('complete');
            }
        });

        // Show appropriate fields for step 2
        if (step === 2) {
            socketFields.style.display = (connectionType === 'socket') ? 'block' : 'none';
            customFields.style.display = (connectionType === 'custom') ? 'block' : 'none';
        }

        // Show appropriate content for step 4 based on setup mode
        if (step === 4) {
            existingCredentials.style.display = (setupMode === 'existing') ? 'block' : 'none';
            adminCredentials.style.display = (setupMode === 'create') ? 'block' : 'none';
        }
    }

    // Update step 5 indicator visibility based on setup mode
    function updateCreateModeIndicators() {
        var display = (setupMode === 'create') ? '' : 'none';
        createModeIndicators.forEach(function(el) {
            el.style.display = display;
        });
    }

    // Hide connection type error messages
    function hideConnectionError(type) {
        var errorEl = document.getElementById(type + '-error');
        if (errorEl) {
            errorEl.style.display = 'none';
            errorEl.textContent = '';
        }
        var optionEl = document.querySelector('.connection-type-option[data-type="' + type + '"]');
        if (optionEl) {
            optionEl.classList.remove('has-error');
        }
    }

    // Show connection type error message
    function showConnectionError(type, message) {
        var errorEl = document.getElementById(type + '-error');
        if (errorEl) {
            errorEl.textContent = message;
            errorEl.style.display = 'block';
        }
        var optionEl = document.querySelector('.connection-type-option[data-type="' + type + '"]');
        if (optionEl) {
            optionEl.classList.add('has-error');
        }
    }

    // Test server availability
    function testServerAvailability(type, host, port, socket, callback) {
        var formData = new FormData();
        formData.append('connection_type', type);
        formData.append('host', host);
        formData.append('port', port);
        formData.append('socket', socket);
        formData.append('csrf_token', document.querySelector('input[name="csrf_token"]').value);

        fetch('?action=test_server', {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('input[name="csrf_token"]').value
            }
        })
        .then(function(response) { return response.json(); })
        .then(function(data) {
            callback(data.success, data.message);
        })
        .catch(function(error) {
            callback(false, 'Failed to test server connection');
        });
    }

    function updateConnectionTypeSelection() {
        var selected = document.querySelector('input[name="connection_type_select"]:checked');

        // Update visual selection
        document.querySelectorAll('.connection-type-option').forEach(function(opt) {
            opt.classList.remove('selected');
        });

        // Clear any connection error when selection changes
        hideConnectionError('localhost');

        if (selected) {
            selected.closest('.connection-type-option').classList.add('selected');
            connectionType = selected.value;
            hiddenConnectionType.value = connectionType;
            step1Next.disabled = false;
        } else {
            step1Next.disabled = true;
        }
    }

    // Bind connection type selection
    connectionOptions.forEach(function(radio) {
        radio.addEventListener('change', updateConnectionTypeSelection);
    });

    // Step 1 -> Next
    step1Next.addEventListener('click', function() {
        if (!connectionType) return;

        // Hide any previous errors
        hideConnectionError('localhost');

        // localhost needs server availability test before proceeding
        if (connectionType === 'localhost') {
            step1Next.disabled = true;
            step1Next.textContent = 'Testing...';

            testServerAvailability('localhost', '127.0.0.1', '3306', '', function(success, message) {
                step1Next.disabled = false;
                step1Next.textContent = 'Next';

                if (success) {
                    showSubstep(3);
                } else {
                    showConnectionError('localhost', message);
                }
            });
        } else {
            showSubstep(2);
        }
    });

    // Step 2 -> Back
    step2Back.addEventListener('click', function() {
        showSubstep(1);
    });

    // Hide socket connection error
    function hideSocketError() {
        var errorEl = document.getElementById('socket-connection-error');
        if (errorEl) {
            errorEl.style.display = 'none';
            errorEl.textContent = '';
        }
    }

    // Show socket connection error
    function showSocketError(message) {
        var errorEl = document.getElementById('socket-connection-error');
        if (errorEl) {
            errorEl.innerHTML = '&#10008; ' + message;
            errorEl.style.display = 'block';
        }
    }

    // Clear socket error when user types in socket field
    document.getElementById('socket').addEventListener('input', hideSocketError);

    // Hide custom connection error
    function hideCustomError() {
        var errorEl = document.getElementById('custom-connection-error');
        if (errorEl) {
            errorEl.style.display = 'none';
            errorEl.textContent = '';
        }
    }

    // Show custom connection error
    function showCustomError(message) {
        var errorEl = document.getElementById('custom-connection-error');
        if (errorEl) {
            errorEl.innerHTML = '&#10008; ' + message;
            errorEl.style.display = 'block';
        }
    }

    // Clear custom error when user types in host or port fields
    document.getElementById('host').addEventListener('input', hideCustomError);
    document.getElementById('port').addEventListener('input', hideCustomError);

    // Step 2 -> Next
    step2Next.addEventListener('click', function() {
        // Hide any previous errors
        hideSocketError();
        hideCustomError();

        // Validate step 2 fields
        if (connectionType === 'socket') {
            var socketVal = document.getElementById('socket').value.trim();
            if (!socketVal) {
                alert('Please enter the socket path.');
                return;
            }

            // Test socket connection before proceeding
            step2Next.disabled = true;
            step2Next.textContent = 'Testing...';

            testServerAvailability('socket', '', '', socketVal, function(success, message) {
                step2Next.disabled = false;
                step2Next.textContent = 'Next';

                if (success) {
                    showSubstep(3);
                } else {
                    showSocketError(message);
                }
            });
        } else if (connectionType === 'custom') {
            var hostVal = document.getElementById('host').value.trim();
            var portVal = document.getElementById('port').value.trim();

            if (!hostVal) {
                alert('Please enter the hostname or IP address.');
                return;
            }

            // Normalize: localhost -> 127.0.0.1, empty port -> 3306
            if (hostVal.toLowerCase() === 'localhost') {
                hostVal = '127.0.0.1';
                document.getElementById('host').value = hostVal;
            }
            if (!portVal) {
                portVal = '3306';
                document.getElementById('port').value = portVal;
            }

            // Test custom connection before proceeding
            step2Next.disabled = true;
            step2Next.textContent = 'Testing...';

            testServerAvailability('custom', hostVal, portVal, '', function(success, message) {
                step2Next.disabled = false;
                step2Next.textContent = 'Next';

                if (success) {
                    showSubstep(3);
                } else {
                    showCustomError(message);
                }
            });
        }
    });

    // Step 3 -> Back (setup mode -> connection type or server config)
    step3Back.addEventListener('click', function() {
        if (connectionType === 'localhost') {
            showSubstep(1);
        } else {
            showSubstep(2);
        }
    });

    // Update setup mode selection
    function updateSetupModeSelection() {
        var selected = document.querySelector('input[name="setup_mode_select"]:checked');

        // Update visual selection
        document.querySelectorAll('.substep-panel[data-substep="3"] .connection-type-option').forEach(function(opt) {
            opt.classList.remove('selected');
        });

        if (selected) {
            selected.closest('.connection-type-option').classList.add('selected');
            setupMode = selected.value;
            hiddenSetupMode.value = setupMode;
            // Update step 5 indicator visibility
            updateCreateModeIndicators();
        }
    }

    // Bind setup mode selection
    setupModeOptions.forEach(function(radio) {
        radio.addEventListener('change', updateSetupModeSelection);
    });

    // Step 3 -> Next (setup mode -> credentials or admin credentials)
    step3Next.addEventListener('click', function() {
        showSubstep(4);
    });

    // Step 4 -> Back (existing mode: credentials -> setup mode)
    step4BackExisting.addEventListener('click', function() {
        showSubstep(3);
    });

    // Step 4 -> Back (create mode: admin credentials -> setup mode)
    step4BackCreate.addEventListener('click', function() {
        showSubstep(3);
    });

    // Step 4 -> Next (create mode: admin credentials -> new db/user details)
    step4NextCreate.addEventListener('click', function() {
        var adminUser = document.getElementById('admin_user').value.trim();
        var adminPass = document.getElementById('admin_password').value;

        if (!adminUser) {
            alert('Please enter the admin username.');
            return;
        }

        // Test admin connection before proceeding
        step4NextCreate.disabled = true;
        step4NextCreate.textContent = 'Testing...';

        testAdminConnection(adminUser, adminPass, function(success, message) {
            step4NextCreate.disabled = false;
            step4NextCreate.textContent = 'Next';

            if (success) {
                showSubstep(5);
            } else {
                showAdminConnectionError(message);
            }
        });
    });

    // Step 5 -> Back (new db/user details -> admin credentials)
    step5Back.addEventListener('click', function() {
        showSubstep(4);
    });

    // Test admin connection function
    function testAdminConnection(user, password, callback) {
        var formData = new FormData();
        formData.append('connection_type', connectionType);
        formData.append('admin_user', user);
        formData.append('admin_password', password);
        formData.append('csrf_token', document.querySelector('input[name="csrf_token"]').value);

        // Add connection-specific values
        if (connectionType === 'localhost') {
            formData.append('host', '127.0.0.1');
            formData.append('port', '3306');
        } else if (connectionType === 'socket') {
            formData.append('socket', document.getElementById('socket').value);
        } else {
            formData.append('host', document.getElementById('host').value);
            formData.append('port', document.getElementById('port').value || '3306');
        }

        fetch('?action=test_admin', {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('input[name="csrf_token"]').value
            }
        })
        .then(function(response) { return response.json(); })
        .then(function(data) {
            callback(data.success, data.message);
        })
        .catch(function(error) {
            callback(false, 'Failed to test admin connection');
        });
    }

    // Show/hide admin connection error
    function showAdminConnectionError(message) {
        var resultDiv = document.getElementById('admin-connection-result');
        resultDiv.innerHTML = '&#10008; ' + message;
        resultDiv.className = 'connection-result error';
        resultDiv.style.display = 'block';
    }

    function hideAdminConnectionError() {
        var resultDiv = document.getElementById('admin-connection-result');
        resultDiv.style.display = 'none';
    }

    // Clear admin error when typing
    document.getElementById('admin_user').addEventListener('input', hideAdminConnectionError);
    document.getElementById('admin_password').addEventListener('input', hideAdminConnectionError);

    // Test admin connection button
    document.getElementById('test-admin-connection').addEventListener('click', function() {
        var adminUser = document.getElementById('admin_user').value.trim();
        var adminPass = document.getElementById('admin_password').value;

        if (!adminUser) {
            alert('Please enter the admin username.');
            return;
        }

        var resultDiv = document.getElementById('admin-connection-result');
        resultDiv.innerHTML = 'Testing connection...';
        resultDiv.className = 'connection-result testing';
        resultDiv.style.display = 'block';

        testAdminConnection(adminUser, adminPass, function(success, message) {
            if (success) {
                resultDiv.innerHTML = '&#10004; ' + message;
                resultDiv.className = 'connection-result success';
            } else {
                resultDiv.innerHTML = '&#10008; ' + message;
                resultDiv.className = 'connection-result error';
            }
        });
    });

    // Create database button
    document.getElementById('create-database').addEventListener('click', function() {
        var newDb = document.getElementById('new_db').value.trim();
        var newUser = document.getElementById('new_user').value.trim();
        var newPass = document.getElementById('new_password').value;

        if (!newDb) {
            alert('Please enter a database name.');
            return;
        }
        if (!newUser) {
            alert('Please enter a username for the new database user.');
            return;
        }

        var btn = document.getElementById('create-database');
        btn.disabled = true;
        btn.textContent = 'Creating...';

        var resultDiv = document.getElementById('create-result');
        resultDiv.innerHTML = 'Creating database and user...';
        resultDiv.className = 'connection-result testing';
        resultDiv.style.display = 'block';

        var formData = new FormData();
        formData.append('connection_type', connectionType);
        formData.append('admin_user', document.getElementById('admin_user').value);
        formData.append('admin_password', document.getElementById('admin_password').value);
        formData.append('new_db', newDb);
        formData.append('new_user', newUser);
        formData.append('new_password', newPass);
        formData.append('csrf_token', document.querySelector('input[name="csrf_token"]').value);

        // Add connection-specific values
        if (connectionType === 'localhost') {
            formData.append('host', '127.0.0.1');
            formData.append('port', '3306');
        } else if (connectionType === 'socket') {
            formData.append('socket', document.getElementById('socket').value);
        } else {
            formData.append('host', document.getElementById('host').value);
            formData.append('port', document.getElementById('port').value || '3306');
        }

        fetch('?action=create_database', {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('input[name="csrf_token"]').value
            }
        })
        .then(function(response) { return response.json(); })
        .then(function(data) {
            btn.disabled = false;
            btn.textContent = 'Create Database & Next';

            if (data.success) {
                resultDiv.innerHTML = '&#10004; ' + data.message;
                resultDiv.className = 'connection-result success';
                // Redirect to next step after a short delay
                setTimeout(function() {
                    window.location.href = '?step=schema';
                }, 1500);
            } else {
                resultDiv.innerHTML = '&#10008; ' + data.message;
                resultDiv.className = 'connection-result error';
            }
        })
        .catch(function(error) {
            btn.disabled = false;
            btn.textContent = 'Create Database & Next';
            resultDiv.innerHTML = '&#10008; Failed to create database';
            resultDiv.className = 'connection-result error';
        });
    });

    // Test connection
    document.getElementById('test-connection').addEventListener('click', function() {
        var form = document.getElementById('database-form');
        var formData = new FormData(form);

        // Set connection type from our state
        formData.set('connection_type', connectionType);

        // Add connection type specific values
        if (connectionType === 'localhost') {
            formData.set('host', '127.0.0.1');
            formData.set('port', '3306');
            formData.delete('socket');
        } else if (connectionType === 'socket') {
            formData.delete('host');
            formData.delete('port');
        } else {
            formData.delete('socket');
        }

        var resultDiv = document.getElementById('connection-result');
        resultDiv.innerHTML = 'Testing connection...';
        resultDiv.className = 'connection-result testing';
        resultDiv.style.display = 'block';

        fetch('?action=test_database', {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('input[name="csrf_token"]').value
            }
        })
        .then(function(response) { return response.json(); })
        .then(function(data) {
            resultDiv.textContent = '';
            var icon = document.createElement('span');
            if (data.success) {
                icon.innerHTML = '&#10004; ';
                resultDiv.className = 'connection-result success';
            } else {
                icon.innerHTML = '&#10008; ';
                resultDiv.className = 'connection-result error';
            }
            resultDiv.appendChild(icon);
            resultDiv.appendChild(document.createTextNode(data.message || ''));
        })
        .catch(function(error) {
            resultDiv.innerHTML = '&#10008; Connection test failed';
            resultDiv.className = 'connection-result error';
        });
    });

    // Form submission - ensure proper values are set
    document.getElementById('database-form').addEventListener('submit', function(e) {
        // Set the hidden connection type
        hiddenConnectionType.value = connectionType;

        var hostInput = document.getElementById('host');
        var portInput = document.getElementById('port');
        var socketInput = document.getElementById('socket');

        if (connectionType === 'localhost') {
            hostInput.value = '127.0.0.1';
            portInput.value = '3306';
            socketInput.value = '';
        } else if (connectionType === 'socket') {
            hostInput.value = '';
            portInput.value = '';
        } else {
            socketInput.value = '';
        }
    });

    // Clear validation errors when user types in fields
    function clearErrorOnInput(inputId) {
        var input = document.getElementById(inputId);
        if (input) {
            input.addEventListener('input', function() {
                var formGroup = this.closest('.form-group');
                if (formGroup) {
                    // Remove has-error class
                    formGroup.classList.remove('has-error');
                    // Hide error message
                    var errorSpan = formGroup.querySelector('.error');
                    if (errorSpan) {
                        errorSpan.style.display = 'none';
                    }
                }
            });
        }
    }

    // Bind error clearing to all form fields that may have validation
    clearErrorOnInput('db');
    clearErrorOnInput('user');
    clearErrorOnInput('host');
    clearErrorOnInput('socket');

    // Initialize
    updateConnectionTypeSelection();
    updateSetupModeSelection();
    updateCreateModeIndicators();
    showSubstep(currentSubstep);
})();
</script>
