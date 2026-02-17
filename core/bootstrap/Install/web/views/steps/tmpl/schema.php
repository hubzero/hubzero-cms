<div class="schema-content">
    <p>The database schema and essential data will be loaded into your database.</p>

    <div class="schema-status">
        <div class="status-item <?php echo $status['schema_loaded'] ? 'complete' : 'pending'; ?>" id="status-schema">
            <span class="status-icon"><?php echo $status['schema_loaded'] ? '&#10004;' : '&#9679;'; ?></span>
            <span class="status-text">Database Schema</span>
            <?php if ($status['schema_loaded']) : ?>
                <span class="status-info">(<?php echo $status['table_count']; ?> tables found)</span>
            <?php else : ?>
                <span class="status-info">(~380 tables will be created)</span>
            <?php endif; ?>
        </div>

        <div class="status-item <?php echo $status['data_loaded'] ? 'complete' : 'pending'; ?>" id="status-data">
            <span class="status-icon"><?php echo $status['data_loaded'] ? '&#10004;' : '&#9679;'; ?></span>
            <span class="status-text">Base Data</span>
            <span class="status-info">(essential system data)</span>
        </div>

        <div class="status-item <?php echo $status['sample_loaded'] ? 'complete' : 'pending'; ?>" id="status-sample">
            <span class="status-icon"><?php echo $status['sample_loaded'] ? '&#10004;' : '&#9679;'; ?></span>
            <span class="status-text">Sample Data</span>
            <span class="status-info">(optional demo content)</span>
        </div>

        <?php $migrClass = $status['migrations_run'] ? 'complete' : 'pending'; ?>
        <div class="status-item <?php echo $migrClass; ?>" id="status-migrations">
            <span class="status-icon"><?php echo $status['migrations_run'] ? '&#10004;' : '&#9679;'; ?></span>
            <span class="status-text">Database Migrations</span>
            <?php if ($status['migrations_run']) : ?>
                <span class="status-info">(up to date)</span>
            <?php elseif ($status['pending_migrations'] > 0) : ?>
                <span class="status-info">(<?php echo $status['pending_migrations']; ?> pending)</span>
            <?php else : ?>
                <span class="status-info">(schema updates)</span>
            <?php endif; ?>
        </div>
    </div>

    <?php if ($status['schema_loaded'] && $status['data_loaded'] && $status['migrations_run']) : ?>
        <div class="info-box success">
            <p><strong>Database is ready!</strong> The schema, base data, and migrations have already been applied.</p>
        </div>
    <?php else : ?>
        <div class="info-box warning" id="warning-box">
            <p><strong>Note:</strong> This process may take several minutes.
                Please do not close or refresh this page.</p>
        </div>
    <?php endif; ?>

    <!-- Migration output section (hidden initially) -->
    <div id="migration-output-section" style="display: none;">
        <h3>Migration Output</h3>
        <div id="migration-status"></div>
        <div class="output" id="migration-output"></div>
    </div>

    <form method="post" class="step-form" id="schema-form">
        <?php $schemaCsrf = htmlspecialchars($installer->getSecurityGuard()->generateCsrfToken()); ?>
        <input type="hidden" name="csrf_token" value="<?php echo $schemaCsrf; ?>">
        <?php if (!$status['sample_loaded'] && !$status['schema_loaded']) : ?>
        <div class="form-group checkbox-group">
            <label>
                <input type="checkbox" name="load_sample" value="1">
                Load sample data (recommended for new users)
            </label>
            <small>Includes demo content to help you get started</small>
        </div>
        <?php endif; ?>

        <input type="hidden" name="migrations_complete" id="migrations-complete" value="">

        <div class="form-actions">
            <a href="?step=database" class="btn btn-secondary">Back</a>
            <?php if ($status['schema_loaded'] && $status['data_loaded'] && $status['migrations_run']) : ?>
                <button type="submit" class="btn btn-primary">Next</button>
            <?php elseif ($status['schema_loaded'] && $status['data_loaded']) : ?>
                <button type="button" class="btn btn-primary"
                    id="run-migrations-btn" onclick="runMigrations()">Run Migrations</button>
                <button type="submit" class="btn btn-primary"
                    id="continue-btn" style="display: none;">Continue</button>
            <?php else : ?>
                <button type="submit" class="btn btn-primary" id="load-schema-btn">Load Database</button>
            <?php endif; ?>
        </div>
    </form>

    <div id="loading-overlay" class="loading-overlay" style="display: none;">
        <div class="loading-content">
            <div class="spinner"></div>
            <p id="loading-message">Loading database schema...</p>
            <p class="loading-note">This may take several minutes. Please wait.</p>
        </div>
    </div>
</div>

<style>
.output {
    background: #1a1a2e;
    color: #10b981;
    padding: 1rem;
    border-radius: 0.5rem;
    font-family: monospace;
    font-size: 0.875rem;
    max-height: 300px;
    overflow-y: auto;
    white-space: pre-wrap;
    word-break: break-all;
    margin-top: 0.5rem;
}
#migration-output-section h3 {
    margin-bottom: 0.5rem;
    font-size: 1rem;
}
</style>

<?php if (!$status['schema_loaded'] || !$status['data_loaded']) : ?>
<script>
document.getElementById('schema-form').addEventListener('submit', function(e) {
    document.getElementById('loading-overlay').style.display = 'flex';
    var btn = document.getElementById('load-schema-btn');
    if (btn) btn.disabled = true;
});
</script>
<?php endif; ?>

<?php if ($startMigrations) : ?>
<script>
// Auto-start migrations after schema/data are loaded
document.addEventListener('DOMContentLoaded', function() {
    runMigrations();
});
</script>
<?php endif; ?>

<script>
function runMigrations() {
    var btn = document.getElementById('run-migrations-btn');
    var outputSection = document.getElementById('migration-output-section');
    var outputEl = document.getElementById('migration-output');
    var statusEl = document.getElementById('migration-status');
    var statusItem = document.getElementById('status-migrations');

    // Disable button and show output section
    if (btn) {
        btn.disabled = true;
        btn.textContent = 'Running...';
    }
    outputSection.style.display = 'block';
    outputEl.textContent = '';
    statusEl.innerHTML = '<div class="info-box warning">Running database migrations...</div>';

    // Update status item to show running
    statusItem.querySelector('.status-icon').innerHTML = '&#8987;';

    // Scroll to make output visible
    outputSection.scrollIntoView({ behavior: 'smooth', block: 'start' });

    // Start SSE connection with CSRF token for security
    var csrfToken = document.querySelector('input[name="csrf_token"]').value;
    var eventSource = new EventSource('?action=stream_migrations&csrf_token=' + encodeURIComponent(csrfToken));

    eventSource.addEventListener('output', function(e) {
        outputEl.textContent += e.data + '\n';
        outputEl.scrollTop = outputEl.scrollHeight;
        // Keep page scrolled to bottom as output grows
        window.scrollTo(0, document.body.scrollHeight);
    });

    eventSource.addEventListener('error', function(e) {
        if (e.data) {
            outputEl.textContent += 'Error: ' + e.data + '\n';
        }
    });

    eventSource.addEventListener('done', function(e) {
        eventSource.close();
        var result = JSON.parse(e.data);

        if (result.success) {
            statusEl.innerHTML = '<div class="info-box success">'
                + '<strong>Success!</strong> Database migrations completed.</div>';
            // Update status item
            statusItem.classList.remove('pending');
            statusItem.classList.add('complete');
            statusItem.querySelector('.status-icon').innerHTML = '&#10004;';
            statusItem.querySelector('.status-info').textContent = '(up to date)';
            // Hide run button, show continue
            if (btn) btn.style.display = 'none';
            document.getElementById('continue-btn').style.display = 'inline-flex';
            document.getElementById('migrations-complete').value = '1';
            // Hide warning box
            var warningBox = document.getElementById('warning-box');
            if (warningBox) warningBox.style.display = 'none';
        } else {
            statusEl.innerHTML = '<div class="info-box error">'
                + '<strong>Migration failed.</strong>'
                + ' Check the output above for details.</div>';
            statusItem.querySelector('.status-icon').innerHTML = '&#10008;';
            if (btn) {
                btn.disabled = false;
                btn.textContent = 'Retry Migrations';
            }
        }

        // Scroll to bottom of page
        window.scrollTo({ top: document.body.scrollHeight, behavior: 'smooth' });
    });

    eventSource.onerror = function() {
        eventSource.close();
        if (btn) {
            btn.disabled = false;
            btn.textContent = 'Retry Migrations';
        }
        statusEl.innerHTML = '<div class="info-box error">'
            + '<strong>Connection error.</strong>'
            + ' The migration process may still be running.'
            + ' Try refreshing the page.</div>';
    };
}
</script>
