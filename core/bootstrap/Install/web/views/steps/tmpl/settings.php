<?php
$sitenameVal = htmlspecialchars($config['sitename'] ?? 'My Hub');
$mailfromVal = htmlspecialchars($config['mailfrom'] ?? '');
$timezoneVal = $config['timezone'] ?? 'UTC';
$liveSiteVal = htmlspecialchars($config['live_site'] ?? '');
$metadescVal = htmlspecialchars($config['metadesc'] ?? '');
?>
<div class="settings-content">
    <p>Configure your site's basic settings.</p>

    <form method="post" class="step-form">
        <?php $settingsCsrf = htmlspecialchars($installer->getSecurityGuard()->generateCsrfToken()); ?>
        <input type="hidden" name="csrf_token" value="<?php echo $settingsCsrf; ?>">
        <div class="form-group <?php echo isset($errors['sitename']) ? 'has-error' : ''; ?>">
            <label for="sitename">Site Name</label>
            <input type="text" id="sitename" name="sitename" value="<?php echo $sitenameVal; ?>" required>
            <small>The name of your hub (displayed in the browser title and header)</small>
            <?php if (isset($errors['sitename'])) : ?>
                <span class="error"><?php echo htmlspecialchars($errors['sitename']); ?></span>
            <?php endif; ?>
        </div>

        <div class="form-group <?php echo isset($errors['mailfrom']) ? 'has-error' : ''; ?>">
            <label for="mailfrom">Administrator Email</label>
            <input type="email" id="mailfrom" name="mailfrom" value="<?php echo $mailfromVal; ?>" required>
            <small>Used for system notifications and as the default "from" address</small>
            <?php if (isset($errors['mailfrom'])) : ?>
                <span class="error"><?php echo htmlspecialchars($errors['mailfrom']); ?></span>
            <?php endif; ?>
        </div>

        <div class="form-group">
            <label for="timezone">Timezone</label>
            <select id="timezone" name="timezone">
                <?php foreach ($timezones as $tz => $label) : ?>
                    <?php $selected = ($timezoneVal === $tz) ? 'selected' : ''; ?>
                    <option value="<?php echo htmlspecialchars($tz); ?>" <?php echo $selected; ?>>
                        <?php echo htmlspecialchars($label); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group">
            <label for="live_site">Site URL (Optional)</label>
            <input type="url" id="live_site" name="live_site"
                   value="<?php echo $liveSiteVal; ?>" placeholder="https://example.com">
            <small>Leave blank to auto-detect (recommended for most setups)</small>
        </div>

        <div class="form-group">
            <label for="metadesc">Site Description</label>
            <textarea id="metadesc" name="metadesc" rows="3"><?php echo $metadescVal; ?></textarea>
            <small>Brief description for search engines (meta description)</small>
        </div>

        <div class="form-actions">
            <a href="?step=database" class="btn btn-secondary">Back</a>
            <button type="submit" class="btn btn-primary">Next</button>
        </div>
    </form>
</div>
