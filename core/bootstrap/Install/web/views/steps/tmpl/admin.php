<?php
$nameVal = htmlspecialchars($_POST['name'] ?? ($existingAdmin['name'] ?? 'Site Administrator'));
$emailVal = htmlspecialchars($_POST['email'] ?? ($existingAdmin['email'] ?? $defaultEmail));
$adminExists = !empty($existingAdmin);
?>
<style>
.admin-info {
    background: var(--gray-50);
    border: 1px solid var(--gray-200);
    border-radius: 0.5rem;
    padding: 1rem 1.25rem;
    margin-bottom: 1.5rem;
}

.admin-info p {
    margin: 0;
    color: var(--gray-600);
    font-size: 0.9rem;
}

.admin-info strong {
    color: var(--gray-900);
}

.username-display {
    background: var(--gray-100);
    border: 1px solid var(--gray-200);
    border-radius: 0.375rem;
    padding: 0.625rem 0.875rem;
    color: var(--gray-600);
    font-family: monospace;
    display: inline-block;
}

/* Horizontal form layout */
.form-group.horizontal {
    display: flex;
    align-items: flex-start;
    gap: 1rem;
    margin-bottom: 1rem;
}

.form-group.horizontal label {
    flex: 0 0 140px;
    text-align: right;
    padding-top: 0.625rem;
    font-weight: 500;
}

.form-group.horizontal .input-wrapper {
    flex: 1;
}

.form-group.horizontal input {
    width: 100%;
}

.form-group.horizontal small {
    display: block;
    margin-top: 0.25rem;
}

.form-group.horizontal .error {
    display: block;
    margin-top: 0.25rem;
}

.password-toggle-section {
    margin-top: 1.5rem;
    padding-top: 1.5rem;
    border-top: 1px solid var(--gray-200);
}

.password-toggle-btn {
    background: var(--gray-100);
    border: 1px solid var(--gray-300);
    border-radius: 0.375rem;
    padding: 0.5rem 1rem;
    cursor: pointer;
    font-size: 0.875rem;
    color: var(--gray-700);
}

.password-toggle-btn:hover {
    background: var(--gray-200);
}

.password-fields {
    margin-top: 1rem;
}

.password-fields.hidden {
    display: none;
}


</style>

<div class="admin-content">
    <?php if ($adminExists) : ?>
        <div class="admin-info">
            <p>An administrator account already exists. You can update the account details below.</p>
        </div>

        <form method="post" class="step-form" id="admin-form">
            <?php $csrfToken = htmlspecialchars($installer->getSecurityGuard()->generateCsrfToken()); ?>
            <input type="hidden" name="csrf_token" value="<?php echo $csrfToken; ?>">
            <input type="hidden" name="existing_admin" value="1">

            <div class="form-group horizontal">
                <label>Username</label>
                <div class="input-wrapper">
                    <div class="username-display"><?php echo htmlspecialchars($existingAdmin['username']); ?></div>
                    <small>Username cannot be changed</small>
                </div>
            </div>

            <div class="form-group horizontal <?php echo isset($errors['name']) ? 'has-error' : ''; ?>">
                <label for="name">Full Name</label>
                <div class="input-wrapper">
                    <input type="text" id="name" name="name" value="<?php echo $nameVal; ?>" required>
                    <?php if (isset($errors['name'])) : ?>
                        <span class="error"><?php echo htmlspecialchars($errors['name']); ?></span>
                    <?php endif; ?>
                </div>
            </div>

            <div class="form-group horizontal <?php echo isset($errors['email']) ? 'has-error' : ''; ?>">
                <label for="email">Email Address</label>
                <div class="input-wrapper">
                    <input type="email" id="email" name="email" value="<?php echo $emailVal; ?>" required>
                    <?php if (isset($errors['email'])) : ?>
                        <span class="error"><?php echo htmlspecialchars($errors['email']); ?></span>
                    <?php endif; ?>
                </div>
            </div>

            <div class="password-toggle-section">
                <button type="button" class="password-toggle-btn" id="toggle-password-btn">Change Password</button>

                <div class="password-fields hidden" id="password-fields">
                    <div class="form-group horizontal <?php echo isset($errors['password']) ? 'has-error' : ''; ?>">
                        <label for="password">New Password</label>
                        <div class="input-wrapper">
                            <input type="password" id="password" name="password" minlength="8">
                            <small>Minimum 8 characters</small>
                            <?php if (isset($errors['password'])) : ?>
                                <span class="error"><?php echo htmlspecialchars($errors['password']); ?></span>
                            <?php endif; ?>
                        </div>
                    </div>

                    <?php $confirmClass = isset($errors['password_confirm']) ? 'has-error' : ''; ?>
                    <div class="form-group horizontal <?php echo $confirmClass; ?>">
                        <label for="password_confirm">Confirm Password</label>
                        <div class="input-wrapper">
                            <input type="password" id="password_confirm" name="password_confirm">
                            <?php if (isset($errors['password_confirm'])) : ?>
                                <span class="error">
                                    <?php echo htmlspecialchars($errors['password_confirm']); ?>
                                </span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-actions">
                <a href="?step=schema" class="btn btn-secondary">Back</a>
                <button type="submit" class="btn btn-primary">Next</button>
            </div>
        </form>

        <script>
        document.getElementById('toggle-password-btn').addEventListener('click', function() {
            var fields = document.getElementById('password-fields');
            var btn = this;
            if (fields.classList.contains('hidden')) {
                fields.classList.remove('hidden');
                btn.textContent = 'Cancel Password Change';
                document.getElementById('password').focus();
            } else {
                fields.classList.add('hidden');
                btn.textContent = 'Change Password';
                document.getElementById('password').value = '';
                document.getElementById('password_confirm').value = '';
            }
        });
        <?php if (isset($errors['password']) || isset($errors['password_confirm'])) : ?>
        // Show password fields if there are validation errors
        document.getElementById('password-fields').classList.remove('hidden');
        document.getElementById('toggle-password-btn').textContent = 'Cancel Password Change';
        <?php endif; ?>
        </script>

    <?php else : ?>
        <p>Create the administrator account for your hub. The username will be <strong>admin</strong>.</p>

        <form method="post" class="step-form">
            <?php $csrfToken2 = htmlspecialchars($installer->getSecurityGuard()->generateCsrfToken()); ?>
            <input type="hidden" name="csrf_token" value="<?php echo $csrfToken2; ?>">

            <div class="form-group horizontal">
                <label>Username</label>
                <div class="input-wrapper">
                    <div class="username-display">admin</div>
                    <small>The admin username is fixed for security consistency</small>
                </div>
            </div>

            <div class="form-group horizontal <?php echo isset($errors['name']) ? 'has-error' : ''; ?>">
                <label for="name">Full Name</label>
                <div class="input-wrapper">
                    <input type="text" id="name" name="name" value="<?php echo $nameVal; ?>" required>
                    <?php if (isset($errors['name'])) : ?>
                        <span class="error"><?php echo htmlspecialchars($errors['name']); ?></span>
                    <?php endif; ?>
                </div>
            </div>

            <div class="form-group horizontal <?php echo isset($errors['email']) ? 'has-error' : ''; ?>">
                <label for="email">Email Address</label>
                <div class="input-wrapper">
                    <input type="email" id="email" name="email" value="<?php echo $emailVal; ?>" required>
                    <?php if (isset($errors['email'])) : ?>
                        <span class="error"><?php echo htmlspecialchars($errors['email']); ?></span>
                    <?php endif; ?>
                </div>
            </div>

            <div class="form-group horizontal <?php echo isset($errors['password']) ? 'has-error' : ''; ?>">
                <label for="password">Password</label>
                <div class="input-wrapper">
                    <input type="password" id="password" name="password" required minlength="8">
                    <small>Minimum 8 characters</small>
                    <?php if (isset($errors['password'])) : ?>
                        <span class="error"><?php echo htmlspecialchars($errors['password']); ?></span>
                    <?php endif; ?>
                </div>
            </div>

            <div class="form-group horizontal <?php echo isset($errors['password_confirm']) ? 'has-error' : ''; ?>">
                <label for="password_confirm">Confirm Password</label>
                <div class="input-wrapper">
                    <input type="password" id="password_confirm" name="password_confirm" required>
                    <?php if (isset($errors['password_confirm'])) : ?>
                        <span class="error"><?php echo htmlspecialchars($errors['password_confirm']); ?></span>
                    <?php endif; ?>
                </div>
            </div>

            <div class="form-actions">
                <a href="?step=schema" class="btn btn-secondary">Back</a>
                <button type="submit" class="btn btn-primary">Create Account</button>
            </div>
        </form>
    <?php endif; ?>
</div>
