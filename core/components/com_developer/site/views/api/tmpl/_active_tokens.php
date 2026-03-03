<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

if (empty($this->tokens) || count($this->tokens)==0) {
    return;
}
?>

<div class="doc-section" id="active-session-tokens">
    <h3 class="endpoint-header collapsed" role="button" tabindex="0" aria-expanded="false">Active Session Tokens</h3>
    <div class="endpoint-content collapsed">
        <p class="information"><strong>You are currently authenticated!</strong> Below are your active session tokens
            that you
            can use to make API requests.</p>

        <div class="tokens-list">
            <?php foreach ($this->tokens as $token): ?>
                <div class="token-item">
                    <div class="token-display">
                        Token: <strong><?php echo htmlspecialchars($token->access_token); ?></strong> will expire on
                        <?php echo \Hubzero\Utility\Date::of($token->expires)->toLocal('M d, Y g:i a'); ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <h4>Using Your Token</h4>
        <p>Include this token in the Authorization header of your API requests:</p>
        <pre><code class="http">Authorization: Bearer <?php echo htmlspecialchars($this->tokens->first()->access_token); ?></code></pre>
    </div>
</div>
