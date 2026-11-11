<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Facades\App;
use Hubzero\Facades\Event;
use Hubzero\Facades\Lang;
use Hubzero\Facades\Request;
use Hubzero\Facades\Route;
use Hubzero\Facades\User;
use Hubzero\Facades\Session;

// No direct access.
defined('_HZEXEC_') or die();

HTML::behavior('core');

//is this a share session thats read-only
$readOnly = false;
foreach ($this->shares as $share) {
    if (User::get('username') == $share->viewuser) {
        if (strtolower($share->readonly) == 'yes') {
            $readOnly = true;
        }
    }
}

$database = App::get('db');
$preferences = new \Components\Tools\Tables\Preferences($database);
$preferences->loadByUser(User::get('id'));

$declared = Request::getWord('viewer');
if ($declared) {
    if (Request::getInt('preferred', 0)) {
        $preferences->set('user_id', User::get('id'));
        $preferences->param()->set('viewer', $declared);
        $preferences->store();
    }
} elseif ($declared = $preferences->param('viewer')) {
    Request::setVar('viewer', $declared);
}

// PROTOTYPE: weber/Jupyter iframe embed (set by viewTask when &embed=1). When
// embedding we skip the VNC viewer plugins entirely. Revert by removing this.
$embedUrl = (isset($this->output->embed_url) && $this->output->embed_url) ? $this->output->embed_url : '';

// We actually need to do this first so we know what viewer is the active one.
$output  = $embedUrl ? [] : Event::trigger('tools.onToolSessionView', [$this->app, $this->output, $readOnly]);
$plugins = Event::trigger('tools.onToolSessionIdentify');

$this
     ->css('tools.css')
     ->css('session-frame.css')
     ->js('sessions.js');
?>
<div id="session">
<?php
if (!$this->app->sess) {
    echo '<p class="error">' . implode('<br />', $this->output) . '</p>';
} else {
    ?>

    <?php if ($readOnly) : ?>
        <p class="warning readonly-warning">
            <?php echo Lang::txt('COM_TOOLS_WARNING_SESSION_READ_ONLY'); ?>
        </p>
    <?php endif; ?>

    <?= implode("\n", Event::trigger('tools.onToolSessionViewBefore', [$this->app, $this->output, $readOnly])) ?>

    <div id="app-wrap" style="width: <?php echo $this->output->width; ?>px;">
        <div id="app-header">
            <?php
            $editable = is_object($this->app->owns) ? 'editable' : '';
            ?>
            <h2 id="session-title" class="session-title item:name id:<?= $this->app->sess ?> <?= $editable ?>"
                rel="<?= $this->app->sess ?>"><?= $this->escape($this->app->caption) ?></h2>
            <?php if ($this->app->sess) { ?>
                <ul class="app-toolbar" id="session-options">
                    <li class="timer-li">
                        <div id="session-timer" hidden
                            data-help="<?= $this->escape(Lang::txt('COM_TOOLS_HELP_TIMER')) ?>"
                            data-sess="<?= (int) $this->app->sess ?>"
                            data-base="<?= $this->escape(rtrim(Request::base(true), '/')) ?>"
                             data-warntitle="<?php echo $this->escape(Lang::txt('COM_TOOLS_SESSION_WARN_TITLE')); ?>"
                             data-warn5="<?php echo $this->escape(Lang::txt('COM_TOOLS_SESSION_WARN_5')); ?>"
                             data-warn1="<?php echo $this->escape(Lang::txt('COM_TOOLS_SESSION_WARN_1')); ?>"
                             data-dismiss="<?php echo $this->escape(Lang::txt('COM_TOOLS_SESSION_WARN_DISMISS')); ?>"
                             data-ending="<?php echo $this->escape(Lang::txt('COM_TOOLS_SESSION_ENDING')); ?>"
                             data-endingtip="<?php echo $this->escape(Lang::txt('COM_TOOLS_SESSION_ENDING_TIP')); ?>">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12"
                                cy="12" r="9"/><path d="M12 7v5l3 2"/></svg>
                            <span class="st-label"><?php echo Lang::txt('COM_TOOLS_SESSION_ENDS_IN'); ?></span>
                            <span class="st-clock">--:--:--</span>
                        </div>
                    </li>
                    <li>
                        <a id="app-btn-keep" class="keep sessiontips icon-only" data-ts-action="leave"
                            data-help="<?= $this->escape(Lang::txt('COM_TOOLS_HELP_KEEP')) ?>"
                            href="<?= Route::url('index.php?option=com_members&task=myaccount') ?>"
                            title="<?= $this->escape(Lang::txt('COM_TOOLS_KEEP_FOR_LATER')) ?>"
                            aria-label="<?= $this->escape(Lang::txt('COM_TOOLS_KEEP_FOR_LATER')) ?>">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path
                                d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line
                                x1="21" y1="12" x2="9" y2="12"/></svg>
                        </a>
                    </li>
                    <?php if ($this->app->owns) { ?>
                        <li>
                            <?php
                            $stopUrl = Route::url(
                                'index.php?option=' . $this->option . '&app=' . $this->toolname . '&task=stop&sess='
                                    . $this->app->sess . '&return=' . $this->rtrn . '&' . Session::getFormToken()
                                    . '=1',
                            );
                            ?>
                            <a id="app-btn-close" class="terminate sessiontips icon-only" data-ts-action="terminate"
                                data-help="<?= $this->escape(Lang::txt('COM_TOOLS_HELP_TERMINATE')) ?>"
                                href="<?= $stopUrl ?>" title="<?= Lang::txt('COM_TOOLS_TERMINATE_WARNING') ?>"
                                aria-label="<?= $this->escape(Lang::txt('COM_TOOLS_TERMINATE')) ?>">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                    stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path
                                    d="M18.36 6.64A9 9 0 1 1 5.64 6.64"/><line x1="12" y1="2" x2="12" y2="12"/></svg>
                            </a>
                        </li>
                    <?php } else { ?>
                        <li>
                            <?php
                            $unshareUrl = Route::url(
                                'index.php?option=' . $this->option . '&app=' . $this->toolname . '&task=unshare&sess='
                                    . $this->app->sess . '&return=' . $this->rtrn . '&' . Session::getFormToken()
                                    . '=1',
                            );
                            ?>
                            <a id="app-btn-close" class="terminate sessiontips"
                                data-help="<?= $this->escape(Lang::txt('COM_TOOLS_HELP_STOP_SHARING')) ?>"
                                href="<?= $unshareUrl ?>" title="<?= Lang::txt('COM_TOOLS_TERMINATE_WARNING') ?>">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                    stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path
                                    d="M18.36 6.64A9 9 0 1 1 5.64 6.64"/><line x1="12" y1="2" x2="12" y2="12"/></svg>
                                <span><?php echo Lang::txt('COM_TOOLS_STOP_SHARING'); ?></span>
                            </a>
                        </li>
                    <?php } ?>
                    <?php if (count($plugins) > 1) { ?>
                        <li>
                            <?php
                            $linkUrl = Route::url(
                                'index.php?option=' . $this->option . '&app=' . $this->toolname . '&task=session&sess='
                                    . $this->app->sess,
                            );
                            ?>
                            <a id="app-btn-options" class="options"
                                data-help="<?= $this->escape(Lang::txt('COM_TOOLS_HELP_OPTIONS')) ?>"
                                href="<?= $linkUrl ?>">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                    stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><line x1="4"
                                    y1="21" x2="4" y2="14"/><line x1="4" y1="10" x2="4" y2="3"/><line x1="12" y1="21"
                                    x2="12" y2="12"/><line x1="12" y1="8" x2="12" y2="3"/><line x1="20" y1="21" x2="20"
                                    y2="16"/><line x1="20" y1="12" x2="20" y2="3"/><line x1="2" y1="14" x2="6"
                                    y2="14"/><line x1="10" y1="8" x2="14" y2="8"/><line x1="18" y1="16" x2="22"
                                    y2="16"/></svg>
                                <span><?php echo Lang::txt('COM_TOOLS_SESSION_OPTIONS'); ?></span>
                            </a>
                        </li>
                    <?php } ?>
                </ul>
            <?php } ?>
        </div><!-- #app-header -->
        <?php if (count($plugins) > 1) { ?>
            <div id="app-options">
                <?php
                $actionUrl = Route::url(
                    'index.php?option=' . $this->option . '&app=' . $this->toolname . '&task=session&sess='
                        . $this->app->sess,
                );
                ?>
                <form method="get" action="<?= $actionUrl ?>">
                    <fieldset>
                        <?php
                        $viewer = ($declared ? $declared : null); //Session::get('tool_viewer'));
                        if (isset($this->output->rendered)) {
                            $viewer = $this->output->rendered;
                        }
                        ?>
                        <?= $viewer
                            ? Lang::txt('COM_TOOLS_SESSION_USING_VIEWER', Lang::txt('PLG_TOOLS_' . $viewer . '_TITLE'))
                            : Lang::txt('COM_TOOLS_UNKNOWN_VIEWER') ?>

                        <span class="input-wrap">
                            <label for="app-viewer">
                                <?php echo Lang::txt('COM_TOOLS_SESSION_VIEWER_CHANGE'); ?>
                            </label>
                            <select name="viewer" id="app-viewer">
                                <?php foreach ($plugins as $plugin) {
                                    if ($viewer == $plugin->name) {
                                        continue;
                                    }
                                    ?>
                                    <?php
                                    $selectedAttr = $viewer == $plugin->name ? ' selected="selected"' : '';
                                    $optionText = $this->escape($plugin->title);
                                    ?>
                                    <option value="<?= $plugin->name ?>"<?= $selectedAttr ?>><?= $optionText ?></option>
                                <?php } ?>
                            </select>
                        </span>

                        <span class="input-wrap">
                            <input type="checkbox" name="preferred" id="app-viewer-preferred" value="1" />
                            <label for="app-viewer-preferred">
                                <?php echo Lang::txt('Use for future sessions.'); ?>
                            </label>
                        </span>

                        <span class="input-wrap">
                            <input type="submit" value="<?php echo Lang::txt('COM_TOOLS_APPLY'); ?>" />
                        </span>
                        <input type="hidden" name="sess" value="<?php echo $this->app->sess; ?>" />
                    </fieldset>
                </form>
            </div>
        <?php } ?>
        <div id="app-content" tabindex="1" class="<?= $readOnly ? 'view-only' : '' ?>"
            style="width: <?= $this->output->width ?>px; height: <?= $this->output->height ?>px">
            <noscript>
                <p class="warning">
                    <?php echo Lang::txt('COM_TOOLS_ERROR_NOSCRIPT'); ?>
                </p>
            </noscript>
            <input type="hidden" id="app-orig-width" name="apporigwidth"
                value="<?= $this->escape($this->output->width) ?>" />
            <input type="hidden" id="app-orig-height" name="apporigheight"
                value="<?= $this->escape($this->output->height) ?>" />
            <?php
            if ($embedUrl) {
                // PROTOTYPE: embed the weber/Jupyter page. Cross-origin, so we can
                // overlay our own popups but cannot script into it.
                echo '<iframe id="app-embed" src="' . $this->escape($embedUrl) . '"'
                    . ' allow="fullscreen; clipboard-read; clipboard-write; autoplay"'
                    . ' referrerpolicy="no-referrer-when-downgrade"'
                    . ' style="width:100%;height:100%;border:0;display:block;background:#0b0d10;"></iframe>';
            } else {
                $output = implode("\n", $output);
                if (!trim($output)) {
                    $output = '<p class="error">' . Lang::txt('COM_TOOLS_ERROR_NOVIEWER') . '</p>';
                }
                echo $output;
            }
            ?>
        </div><!-- / #app-content -->
        <div id="app-footer" data-more="<?= $this->escape(Lang::txt('COM_TOOLS_SESSION_MORE')) ?>"
            data-morehelp="<?= $this->escape(Lang::txt('COM_TOOLS_HELP_MORE')) ?>">
            <?php // Designer mark (dark two-tone), seated on the metal footer?>
            <div id="app-logo" class="brand" aria-hidden="true">
                <svg viewBox="0 0 48.52 85.7" xmlns="http://www.w3.org/2000/svg" focusable="false">
                    <?php
                    $pathData = 'M34.93 61.44c0 5.2-3.1 12.31-10.67 12.31s-10.67-6.2-10.67-12.31v-8.68L0 57.53v3.55C0 '
                        . '79.5 15.05 85.7 24.53 85.7s23.98-6.2 23.98-24.62V57.4l-13.59-4.59v8.64ZM24.35 15.66c-2.98 '
                        . '0-6.92.69-10.76 2.43V0L0 3.9v42.5h1.04l12.55 4.24V40.02c0-5.93 3.01-12.4 10.58-12.4 9.3 0 '
                        . '10.76 8.48 10.76 12.31v11.14l13.59-4.77v-6.01c0-18.24-14.68-24.62-24.17-24.62Z';
                    ?>
                    <path fill="#383a36" d="<?= $pathData ?>"/>
                    <path fill="#0b0b0b"
                        d="M13.59 18.09C8.88 19.65 0 22.99 0 29.92v-6.15c0-5.37 9.37-7.78 13.59-7.49z"/>
                </svg>
            </div>
            <?php
            if ($this->config->get('show_storage')) {
                $this
                     ->view('diskusage', 'storage')
                     ->set('option', $this->option)
                     ->set('amt', $this->app->percent)
                     ->set('du', null)
                     ->set('percent', 0)
                     ->set('msgs', 0)
                     ->set('ajax', 0)
                     ->set('writelink', 1)
                     ->set('total', $this->total)
                     ->display();

                if ($this->app->percent >= 100 && isset($this->app->remaining)) {
                    $this
                         ->view('warning', 'storage')
                         ->set('sec', $this->app->remaining)
                         ->set('padHours', false)
                         ->set('option', $this->option)
                         ->display();
                }
            }
            ?>
        </div><!-- #app-footer -->
        <?php if ($this->zone->config('zones') && $this->zone->exists()) { ?>
            <div id="app-zone">
                <div class="grid">
                    <div class="col span6">
                        <p class="zone-identity">
                            <?php if ($logo = $this->zone->logo()) { ?>
                                <img src="<?php echo $logo; ?>" alt="" />
                            <?php } ?>
                        </p>
                        <p>
                            <?= $this->zone->get(
                                'description',
                                Lang::txt(
                                    'COM_TOOLS_POWERED_BY_MIRROR',
                                    $this->zone->get('title', $this->zone->get('zone')),
                                ),
                            ) ?>
                        </p>
                    </div><!-- / .col span6 -->
                    <div class="col span6 omega">
                        <?php
                        $reinvokeUrl = Route::url(
                            'index.php?option=' . $this->option . '&app=' . $this->toolname . '&task=reinvoke&sess='
                                . $this->app->sess,
                        );
                        ?>
                        <form name="share" id="app-zone" method="post" action="<?= $reinvokeUrl ?>">
                            <p><?php echo Lang::txt('COM_TOOLS_ZONE_WARNING_CHANGE'); ?></p>
                            <p><label for="field-zone">
                                <?php echo Lang::txt('COM_TOOLS_ZONE_RELAUNCH'); ?>
                                <select name="zone" id="field-zone">
                                    <option value=""><?php echo Lang::txt('COM_TOOLS_SELECT'); ?></option>
                                    <?php
                                    $zones = $this->middleware->zones(
                                        'list',
                                        ['state' => 'up', 'id' => $this->middleware->get('allowed')],
                                    );
                                    foreach ($zones as $zone) {
                                        if ($zone->get('id') == $this->zone->get('id')) {
                                            continue;
                                        }
                                        ?>
                                    <option value="<?= $zone->get('id') ?>">
                                        <?= $this->escape($zone->get('title', $zone->get('zone'))) ?>
                                    </option>
                                    <?php } ?>
                                </select>
                            </label>
                            <input type="submit" value="Go" /></p>
                        </form>
                    </div><!-- / .col span6 omega -->
                </div><!-- .grid -->
            </div><!-- #app-zone -->
        <?php } ?>
        <?php if ($this->app->owns && $this->app->sess) : ?>
            <?php
            // Shared confirmation for the Keep (leave) and Terminate toolbar buttons.
            // Both open this chooser; the button pressed is the default (focused)
            // choice. The choices are real links so it works without JS too.
            // It lives inside #app-wrap, like the timeout popup and hover help, so
            // it still renders in native fullscreen (only the fullscreen element's
            // subtree is drawn) and stacks above the theater layer; as a <body>
            // child it opened unseen behind the canvas in both modes, with the
            // default choice focused.
            $leaveUrl     = Route::url('index.php?option=com_members&task=myaccount');
            $terminateUrl = Route::url(
                'index.php?option=' . $this->option . '&app=' . $this->toolname . '&task=stop&sess=' . $this->app->sess
                    . '&return=' . $this->rtrn . '&' . Session::getFormToken() . '=1',
            );
            ?>
        <div id="ts-confirm" class="ts-confirm" hidden role="dialog" aria-modal="true"
            aria-labelledby="ts-confirm-title">
            <div class="ts-confirm-backdrop" data-ts-close></div>
            <div class="ts-confirm-card" role="document">
                <h2 id="ts-confirm-title"
                    class="ts-confirm-title"><?= $this->escape(Lang::txt('COM_TOOLS_SESSION_CONFIRM_TITLE')) ?></h2>
                <div class="ts-confirm-choices">
                    <a class="ts-choice ts-choice-leave" data-ts-choice="leave" href="<?php echo $leaveUrl; ?>">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path
                            d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line
                            x1="21" y1="12" x2="9" y2="12"/></svg>
                        <span class="ts-choice-label"><?= $this->escape(Lang::txt('COM_TOOLS_SESSION_LEAVE')) ?></span>
                        <?php
                        $tsChoiceDescText = $this->escape(Lang::txt('COM_TOOLS_SESSION_LEAVE_DESC'));
                        ?>
                        <span class="ts-choice-desc"><?= $tsChoiceDescText ?></span>
                    </a>
                    <a class="ts-choice ts-choice-terminate" data-ts-choice="terminate" href="<?= $terminateUrl ?>">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path
                            d="M18.36 6.64A9 9 0 1 1 5.64 6.64"/><line x1="12" y1="2" x2="12" y2="12"/></svg>
                        <span class="ts-choice-label"><?= $this->escape(Lang::txt('COM_TOOLS_TERMINATE')) ?></span>
                        <?php
                        $tsChoiceDescText = $this->escape(Lang::txt('COM_TOOLS_SESSION_TERMINATE_DESC'));
                        ?>
                        <span class="ts-choice-desc"><?= $tsChoiceDescText ?></span>
                    </a>
                </div>
                <button type="button" class="ts-confirm-cancel"
                    data-ts-close><?= $this->escape(Lang::txt('COM_TOOLS_SESSION_CONFIRM_CANCEL')) ?></button>
            </div>
        </div>
        <?php endif; ?>
    </div><!-- #app-wrap -->

    <?php echo implode("\n", Event::trigger('tools.onToolSessionViewAfter', [$this->app, $this->output, $readOnly])); ?>

    <?php
    // Are we on an iPad?
    $isiPad = (bool) strpos($_SERVER['HTTP_USER_AGENT'], 'iPad');

    if ($isiPad && $this->config->get('launch_ipad', 0) && $this->config->get('launch_ipad_app')) {
        ?>
        <?php
        $linkUrl = $this->config->get('launch_ipad_app') . '://tools/session/' . $this->app->sess;
        ?>
        <p class="tablet-app"><a class="btn icon-tablet"
            href="<?= $linkUrl ?>"><?= Lang::txt('Launch in iPad app') ?></a></p>
        <?php
    }
    ?>

    <div class="clear share-divider"></div>
    <?php if ($this->config->get('shareable', 0)) {
        $sessionUrl = Route::url(
            'index.php?option=' . $this->option . '&app=' . $this->toolname . '&task=session&sess=' . $this->app->sess,
        );
        ?>
    <form name="share" id="app-share" method="post" action="<?= $sessionUrl ?>">
        <div class="grid">
        <?php if (is_object($this->app->owns)) : ?>
            <div class="col span8">
                <p class="share-member-photo" id="shareform">
                    <?php
                    $jxuser = \Components\Members\Models\Member::oneOrNew(User::get('id'));
                    ?>
                    <img src="<?php echo $jxuser->picture(); ?>" alt="" />
                </p>
                <fieldset>
                    <legend><?php echo Lang::txt('COM_TOOLS_SHARE_SESSION'); ?></legend>

                    <input type="hidden" name="option" value="<?php echo $this->escape($this->option); ?>" />
                    <input type="hidden" name="controller" value="<?php echo $this->escape($this->controller); ?>" />
                    <input type="hidden" name="task" value="share" />
                    <input type="hidden" name="sess" value="<?php echo $this->escape($this->app->sess); ?>" />
                    <input type="hidden" name="app" value="<?php echo $this->escape($this->toolname); ?>" />
                    <?php
                    $fieldValue = base64_encode(
                        Route::url(
                            'index.php?option=' . $this->option . '&app=' . $this->toolname . '&task=session&sess='
                                . $this->app->sess,
                        ),
                    );
                    ?>
                    <input type="hidden" name="return" value="<?= $fieldValue ?>" />

                    <label for="field-username">
                        <?php echo Lang::txt('COM_TOOLS_SHARE_SESSION_WITH'); ?>
                        <?php
                        $mc = Event::trigger('hubzero.onGetMultiEntry', [['members', 'username', 'acmembers']]);
                        if (count($mc) > 0) {
                            echo '<span class="hint">' . Lang::txt('COM_TOOLS_SHARE_SESSION_HINT_AUTOCOMPLETE')
                                . '</span>' . $mc[0];
                        } else { ?>
                            <span class="hint"><?php echo Lang::txt('COM_TOOLS_SHARE_SESSION_HINT'); ?></span>
                            <input type="text" name="username" id="field-username" value="" />
                        <?php } ?>
                    </label>
                    <label for="group">
                        <?php echo Lang::txt('COM_TOOLS_SHARE_SESSION_WITH_GROUP'); ?>
                        <select name="group" id="group">
                            <option value=""><?php echo Lang::txt('- Select Group &mdash;'); ?></option>
                            <?php if (!empty($this->mygroups)) {
                                foreach ($this->mygroups as $group) : ?>
                                <option value="<?= $group->gidNumber ?>"><?= $group->description ?></option>
                                <?php endforeach;
                            } ?>
                        </select>
                    </label>

                    <?php if (0) : /* https://hubzero.org/support/ticket/9826 */ ?>
                    <label for="field-readonly" id="readonly-label">
                        <input class="option" type="checkbox" name="readonly" id="readonly" value="Yes" />
                        <?php echo Lang::txt('COM_TOOLS_SHARE_SESSION_READ_ONLY'); ?>
                    </label>
                    <?php endif; ?>

                    <div>&nbsp;</div>

                    <label for="confirm-share" id="confirm-share-label">
                        <input class="option" type="checkbox" value="Yes" name="confirm" id="confirm-share">
                        <?php echo Lang::txt('COM_TOOLS_SHARE_SESSION_CONFIRM'); ?>
                    </label>
                    <div>&nbsp;</div>

                    <p class="submit">
                        <input type="submit" value="<?php echo Lang::txt('COM_TOOLS_SHARE'); ?>" id="share-btn" />
                    </p>

                    <div class="sidenote">
                        <p>
                            <?php echo Lang::txt('COM_TOOLS_SHARE_SESSION_NOTES'); ?>
                        </p>
                    </div>
                </fieldset>
            </div><!-- / .col span8 -->
        <?php endif; ?>
            <div class="<?php if (is_object($this->app->owns)) :
                ?>col span4 omega<?php
                        endif; ?>">
                <table class="entries">
                    <thead>
                        <tr>
                            <th<?php if (count($this->shares) > 1) {
                                ?> colspan="3"<?php
                               } ?>>
                                    <?php echo Lang::txt('COM_TOOLS_SESSION_SHARED_WITH'); ?>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php if (count($this->shares) <= 1) { ?>
                        <tr>
                            <td>
                                <?php echo Lang::txt('COM_TOOLS_SHARE_SESSION_NONE'); ?>
                            </td>
                        </tr>
                    <?php } else {
                        foreach ($this->shares as $row) {
                            if ($row->viewuser != User::get('username')) {
                                $user = User::getInstance($row->viewuser);

                                $id = ($user->get('id') < 0) ? 'n' . -$user->get('id') : $user->get('id');

                                // User picture
                                $p = $user->picture();
                                ?>
                        <tr>
                            <th class="entry-img">
                                <img width="40" height="40" src="<?= $p ?>"
                                    alt="<?= $this->escape(stripslashes($user->get('name'))) ?>" />
                            </th>
                            <td>
                                <a class="entry-title"
                                    href="<?= Route::url('index.php?option=com_members&id=' . $id) ?>">
                                    <?php echo $this->escape(stripslashes($user->get('name'))); ?>
                                </a><br />
                                <span class="entry-details">
                                    <?php
                                    $usernameText = $this->escape(stripslashes($user->get('username')));
                                    ?>
                                    <span class="username"><?= $usernameText ?></span>
                                </span>
                            </td>
                            <td class="entry-actions">
                                <?php if (is_object($this->app->owns)) : ?>
                                    <?php if (strtolower($row->readonly) == 'yes') : ?>
                                        <span class="readonly"><?= Lang::txt('COM_TOOLS_SESSION_READ_ONLY') ?></span>
                                    <?php endif; ?>
                                    <?php
                                    $unshareUrl = Route::url(
                                        'index.php?option=' . $this->option . '&app=' . $this->toolname
                                            . '&task=unshare&sess=' . $this->app->sess . '&username=' . $row->viewuser
                                            . '&return=' . $this->rtrn . '&' . Session::getFormToken() . '=1',
                                    );
                                    ?>
                                    <a class="entry-remove" href="<?= $unshareUrl ?>"
                                        title="<?= Lang::txt('COM_TOOLS_SESSION_SHARED_REMOVE_USER') ?>">
                                        <span><?php echo Lang::txt('COM_TOOLS_SESSION_SHARED_REMOVE_USER'); ?></span>
                                    </a>
                                <?php endif; ?>
                            </td>
                        </tr>
                                <?php
                            }
                        }
                        ?>
                    <?php } ?>
                    </tbody>
                </table>
            </div><!-- / .col span4 -->
        </div><!-- / .grid -->
    </form>
    <?php } ?>
<?php } ?>

<?php if ($this->config->get('access-manage-session')) { ?>
    <p id="app-manager">
        <?= Lang::txt('COM_TOOLS_SESSION_ADMIN_INFO', $this->app->username, $this->app->ip, $this->app->sess) ?>
    </p>
<?php } ?>

    <?php
    $output = Event::trigger(
        'mw.onSessionView',
        [
            $this->option,
            $this->toolname,
            $this->app->sess,
        ],
    );

    if (count($output) > 0) {
        ?>
        <div id="app-info">
            <h2><?php echo Lang::txt('COM_TOOLS_SESSION_APP_INFO'); ?></h2>
            <div id="app-info-content">
                <?php
                foreach ($output as $out) {
                    echo $out;
                }
                ?>
            </div>
        </div><!-- #app-info -->
        <?php
    }
    ?>
</div><!-- / .main section #session-section -->
