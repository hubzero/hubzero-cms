<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

if (empty($this->tokens)) {
    return;
}
?>

<div class="doc-section" id="active-session-tokens">
    <h3 class="endpoint-header collapsed">Active Session Tokens</h3>
    <div class="endpoint-content collapsed">
        <p class="information"><strong>You are currently authenticated!</strong> Below are your active session tokens that you
            can use to make API requests.</p>

        <div class="tokens-list">
            <?php foreach ($this->tokens as $token): 
                $tokenId = 'token-' . md5($token->access_token);
                ?>
                <div class="token-item">
                    <div class="token-display">
                        <div class="token-value-container">
                            <input 
                                type="password" 
                                id="<?php echo $tokenId; ?>" 
                                value="<?php echo htmlspecialchars($token->access_token); ?>" 
                                readonly 
                                class="token-input"
                            />
                            <button class="token-action-btn" title="Show/Hide Token" onclick="toggleTokenVisibility('<?php echo $tokenId; ?>')">
                                <i class="icon-eye"></i>
                            </button>
                            <button class="token-action-btn" title="Copy to Clipboard" onclick="copyToken('<?php echo $tokenId; ?>')">
                                <i class="icon-copy"></i>
                            </button>
                        </div>
                        <small class="token-expires">Expires: <?php echo \Hubzero\Utility\Date::of($token->expires)->toLocal('M d, Y g:i a'); ?></small>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="token-actions">
            <button class="btn btn-primary" id="generate-session-token-btn" onclick="generateNewSessionToken()">
                Generate New Session Token
            </button>
        </div>

        <h4>Using Your Token</h4>
        <p>Include this token in the Authorization header of your API requests:</p>
        <pre><code class="http">Authorization: Bearer [your-token-here]</code></pre>
    </div>
</div>

<style>
    .token-item {
        margin-bottom: 2rem;
    }

    .token-input {
        font-family: 'Monaco', 'Menlo', 'Consolas', monospace !important;
    }

    .token-action-btn {
        padding: 0.5rem 0.75rem;
        background: transparent;
        color: var(--dev-text-secondary);
        border: 1px solid var(--dev-border-light);
        border-radius: var(--dev-radius-sm);
        cursor: pointer;
        transition: var(--dev-transition-fast);
        font-size: 0.9rem;
        font-weight: 500;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
    }

    .icon-eye::before {
        content: '\f06e';
        font-family: 'Fontcons';
    }

    .icon-copy::before {
        content: '\f0c5';
        font-family: 'Fontcons';
    }

    .token-action-btn:hover {
        background: var(--dev-primary);
        color: white;
        border-color: var(--dev-primary);
        transform: translateY(-1px);
    }

    .token-expires {
        display: block;
        margin-top: 0.5rem;
        color: var(--dev-text-secondary);
        font-size: 0.85rem;
    }

    .token-actions {
        margin: 2rem 0;
        padding: 1.5rem;
        background: var(--dev-bg-secondary);
        border-radius: var(--dev-radius-sm);
        border: 1px solid var(--dev-border-light);
    }

    .token-actions .btn {
        font-weight: 600;
    }
</style>

<script>
    function toggleTokenVisibility(elementId) {
        const input = document.getElementById(elementId);
        const btn = event.target.closest('.token-action-btn');
        
        if (input.type === 'password') {
            input.type = 'text';
            btn.title = 'Hide Token';
        } else {
            input.type = 'password';
            btn.title = 'Show Token';
        }
    }

    function copyToken(elementId) {
        const input = document.getElementById(elementId);
        const btn = event.target.closest('.token-action-btn');
        
        // Temporarily show the token if hidden
        const wasHidden = input.type === 'password';
        if (wasHidden) {
            input.type = 'text';
        }
        
        input.select();
        input.setSelectionRange(0, 99999); // For mobile devices

        navigator.clipboard.writeText(input.value).then(function() {
            const originalTitle = btn.title;
            btn.title = 'Copied!';
            
            setTimeout(function() {
                btn.title = originalTitle;
                if (wasHidden) {
                    input.type = 'password';
                }
            }, 2000);
        }).catch(function(err) {
            console.error('Failed to copy:', err);
            if (wasHidden) {
                input.type = 'password';
            }
        });
    }

    function generateNewSessionToken() {
        const btn = document.getElementById('generate-session-token-btn');
        const originalText = btn.textContent;
        btn.disabled = true;
        btn.textContent = 'Generating...';

        fetch('/api/developer/oauth/token', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'grant_type=session'
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            if (data.access_token) {
                // Success - show message and reload
                alert('New session token generated successfully! Reloading page...');
                window.location.reload();
            } else {
                throw new Error('No access token in response');
            }
        })
        .catch(error => {
            console.error('Error generating token:', error);
            alert('Failed to generate new session token: ' + error.message);
            btn.disabled = false;
            btn.textContent = originalText;
        });
    }
</script>
