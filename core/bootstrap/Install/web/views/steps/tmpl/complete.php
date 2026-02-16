<?php
$siteUrlEsc = htmlspecialchars($siteUrl);
$adminUrlEsc = htmlspecialchars($siteUrl . '/administrator');
$sitenameEsc = htmlspecialchars($settings['sitename'] ?? 'Your Hub');
$usernameEsc = htmlspecialchars($admin['username'] ?? 'admin');
$emailEsc = htmlspecialchars($admin['email'] ?? '');
?>
<style>
.success-banner {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 1.5rem;
    background: linear-gradient(135deg, #ecfdf5 0%, #d1fae5 100%);
    border: 1px solid #10b981;
    border-radius: 1rem;
    padding: 1.5rem 2rem;
    margin-bottom: 2rem;
}

.success-banner .success-icon {
    flex-shrink: 0;
    width: 4rem;
    height: 4rem;
    background: #10b981;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2rem;
    color: white;
}

.success-banner .success-text h2 {
    margin: 0 0 0.25rem 0;
    color: #065f46;
    font-size: 1.5rem;
}

.success-banner .success-text p {
    margin: 0;
    color: #047857;
}
</style>
<div class="complete-content">
    <div class="success-banner">
        <div class="success-icon">&#10004;</div>
        <div class="success-text">
            <h2>Installation Complete!</h2>
            <p>Your HUBzero site has been successfully installed.</p>
        </div>
    </div>

    <div class="summary-box">
        <h3>Site Information</h3>
        <dl>
            <dt>Site Name:</dt>
            <dd><?php echo $sitenameEsc; ?></dd>

            <dt>Site URL:</dt>
            <dd><a href="<?php echo $siteUrlEsc; ?>"><?php echo $siteUrlEsc; ?></a></dd>
        </dl>
    </div>

    <div class="summary-box">
        <h3>Admin Account</h3>
        <dl>
            <dt>Username:</dt>
            <dd><strong><?php echo $usernameEsc; ?></strong></dd>

            <dt>Email:</dt>
            <dd><?php echo $emailEsc; ?></dd>
        </dl>
    </div>

    <div class="next-steps">
        <h3>Next Steps</h3>
        <ol>
            <li>
                <strong>Access your site</strong>
                <p><a href="<?php echo $siteUrlEsc; ?>" class="btn btn-link"><?php echo $siteUrlEsc; ?></a></p>
            </li>
            <li>
                <strong>Log in to the admin panel</strong>
                <p><a href="<?php echo $adminUrlEsc; ?>" class="btn btn-link"><?php echo $adminUrlEsc; ?></a></p>
            </li>
            <li>
                <strong>Configure additional settings</strong>
                <p>Set up your site's appearance, users, and components in the admin panel.</p>
            </li>
        </ol>
    </div>

    <div class="form-actions center">
        <a href="<?php echo $siteUrlEsc; ?>" class="btn btn-primary btn-large">Visit Your Site</a>
        <a href="<?php echo $adminUrlEsc; ?>" class="btn btn-secondary btn-large">Admin Panel</a>
    </div>
</div>
