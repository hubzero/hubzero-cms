<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Facades\Html;
use Hubzero\Facades\Lang;
use Hubzero\Facades\Request;
use Hubzero\Facades\Route;
use Hubzero\Facades\User;

// No direct access
defined('_HZEXEC_') or die();

$this->css()
     ->js();
?>

<header id="content-header">
    <h2><?php echo $this->title; ?></h2>

    <div id="content-header-extra">
        <ul>
            <li>
                <a class="btn icon-browse" href="<?php echo Route::url('index.php?option=com_newsletter'); ?>">
                    <?php echo Lang::txt('COM_NEWSLETTER_BROWSE'); ?>
                </a>
            </li>
        </ul>
    </div>
</header>

<section class="main section">
    <?php
    if ($this->getError()) {
        echo '<p class="error">' . $this->getError() . '</p>';
    }
    ?>
    <div class="subscribe">
        <form action="<?php echo Route::url('index.php?option=' . $this->option); ?>" method="post" id="hubForm">
            <fieldset>
                <legend><?php echo Lang::txt('COM_NEWSLETTER_MAILINGLISTS_UNSUBSCRIBE'); ?></legend>
                <p><?php echo Lang::txt('COM_NEWSLETTER_MAILINGLISTS_UNSUBSCRIBE_DESC'); ?></p>

                <p>
                    <strong><?php echo $this->escape($this->mailinglist->name); ?></strong><br />
                    <span><?php echo $this->escape($this->mailinglist->description); ?></span>
                    <input type="hidden" name="t" value="<?php echo $this->escape(Request::getString('t', '')); ?>" />
                    <input type="hidden" name="e" value="<?php echo $this->escape(Request::getString('e', '')); ?>" />
                </p>

                <?php if ($this->mailinglist->id == '-1' && User::get('guest') == 1) : ?>
                    <?php
                    $loggedInTxt = Lang::txt(
                        'COM_NEWSLETTER_MAILINGLISTS_LOGGEDIN_AS',
                        User::get('username')
                    );
                    ?>
                    <ol>
                        <li>
                            <?php if (User::isGuest()) : ?>
                                <a href="login?return=<?php echo base64_encode($_SERVER['REQUEST_URI']); ?>">
                                    <?php echo Lang::txt('COM_NEWSLETTER_MAILINGLISTS_LOGIN_TO_UNSUBSCRIBE'); ?>
                                </a>
                            <?php else : ?>
                                <span class="complete">
                                    <?php echo $loggedInTxt; ?>
                                </span>
                            <?php endif; ?>
                        </li>
                    </ol>
                <?php else : ?>
                    <?php
                    $optDefault = Lang::txt('COM_NEWSLETTER_UNSUBSCRIBE_REASON_DEFAULT');
                    $optTooMany = Lang::txt('COM_NEWSLETTER_UNSUBSCRIBE_REASON_TOOMANY');
                    $optNotRelevant = Lang::txt('COM_NEWSLETTER_UNSUBSCRIBE_REASON_NOTRELEVANT');
                    $optNotSignedUp = Lang::txt('COM_NEWSLETTER_UNSUBSCRIBE_REASON_NOTSIGNEDUP');
                    $optPrivacy = Lang::txt('COM_NEWSLETTER_UNSUBSCRIBE_REASON_PRIVACY');
                    $optOther = Lang::txt('COM_NEWSLETTER_UNSUBSCRIBE_REASON_OTHER');
                    ?>
                    <label><?php echo Lang::txt('COM_NEWSLETTER_UNSUBSCRIBE_REASON'); ?>
                        <select name="reason" id="reason">
                            <option value=""><?php echo $optDefault; ?></option>
                            <option value="Too many emails">
                                <?php echo $optTooMany; ?>
                            </option>
                            <option value="Content isn't relevant to me">
                                <?php echo $optNotRelevant; ?>
                            </option>
                            <option value="I don't remember signing up">
                                <?php echo $optNotSignedUp; ?>
                            </option>
                            <option value="Privacy concerns">
                                <?php echo $optPrivacy; ?>
                            </option>
                            <option value="Other">
                                <?php echo $optOther; ?>
                            </option>
                        </select>
                    </label>

                    <?php $otherPlaceholder = Lang::txt('COM_NEWSLETTER_UNSUBSCRIBE_REASON_OTHER_OTHER'); ?>
                    <label>
                        <textarea
                            rows="4"
                            name="reason-alt"
                            id="reason-alt"
                            placeholder="<?php echo $otherPlaceholder; ?>"
                        ></textarea>
                    </label>
                <?php endif; ?>
            </fieldset>
            <?php if (!User::isGuest() || $this->mailinglist->id != '-1') : ?>
                <p class="submit">
                    <?php $unsubVal = Lang::txt('COM_NEWSLETTER_UNSUBSCRIBE'); ?>
                    <input type="submit" class="btn btn-success" value="<?php echo $unsubVal; ?>">
                </p>
            <?php endif; ?>
            <input type="hidden" name="option" value="<?php echo $this->option; ?>" />
            <input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
            <input type="hidden" name="task" value="dounsubscribe" />
            <?php echo Html::input('token'); ?>
        </form>
    </div>
</section>