<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Facades\Date;
use Hubzero\Facades\Lang;
use Hubzero\Facades\Route;
use Hubzero\Facades\User;

// No direct access
defined('_HZEXEC_') or die();

$loginUrl = Route::url(
    'index.php?option=' . $this->option . '&task=view&action=login'
);
$dashboardUrl = Route::url(
    'index.php?option=' . $this->option . '&task=dashboard'
);
$shortlistUrl = Route::url(
    'index.php?option=' . $this->option . '&task=resumes'
) . '?filterby=shortlisted';
$resumeUrl = Route::url(
    'index.php?option=' . $this->option . '&task=addresume'
);
?>
<header id="content-header">
    <h2><?php echo $this->title; ?></h2>

    <div id="content-header-extra">
        <ul id="useroptions">
            <?php if (User::isGuest()) { ?>
                <li>
                    <?php echo Lang::txt('COM_JOBS_PLEASE')
                        . ' <a href="' . $loginUrl . '">'
                        . Lang::txt('COM_JOBS_ACTION_LOGIN') . '</a> '
                        . Lang::txt('COM_JOBS_ACTION_LOGIN_TO_VIEW_OPTIONS'); ?>
                </li>
            <?php } elseif ($this->emp && $this->config->get('allowsubscriptions', 0)) { ?>
                <li>
                    <a class="myjobs btn" href="<?php echo $dashboardUrl; ?>">
                        <?php echo Lang::txt('COM_JOBS_EMPLOYER_DASHBOARD'); ?>
                    </a>
                </li>
                <li>
                    <a class="shortlist btn" href="<?php echo $shortlistUrl; ?>">
                        <?php echo Lang::txt('COM_JOBS_SHORTLIST'); ?>
                    </a>
                </li>
            <?php } elseif ($this->admin) { ?>
                <li>
                    <?php echo Lang::txt('COM_JOBS_NOTICE_YOU_ARE_ADMIN'); ?>
                    <a class="myjobs btn" href="<?php echo $dashboardUrl; ?>">
                        <?php echo Lang::txt('COM_JOBS_ADMIN_DASHBOARD'); ?>
                    </a>
                </li>
            <?php } else { ?>
                <li>
                    <a class="myresume btn" href="<?php echo $resumeUrl; ?>">
                        <?php echo Lang::txt('COM_JOBS_MY_RESUME'); ?>
                    </a>
                </li>
            <?php } ?>
        </ul>
    </div><!-- / #content-header-extra -->
</header><!-- / #content-header -->

<section class="main section">
    <?php if ($this->getError()) { ?>
        <p class="error"><?php echo $this->getError(); ?></p>
    <?php } ?>

    <?php $confirmUrl = Route::url('index.php?option=' . $this->option . '&task=confirm'); ?>
    <form action="<?php echo $confirmUrl; ?>" method="post" id="hubForm">
        <div class="explaination">
            <p><?php echo Lang::txt('COM_JOBS_SUBSCRIBE_HINT_EMPLOYER_INFO') ?></p>
        </div>
        <fieldset id="subForm">
            <legend><?php echo Lang::txt('COM_JOBS_SUBSCRIPTION_EMPLOYER_INFORMATION'); ?></legend>

            <label for="companyName">
                <?php echo Lang::txt('COM_JOBS_EMPLOYER_COMPANY_NAME'); ?>:
                <span class="required"><?php echo Lang::txt('COM_JOBS_REQUIRED'); ?></span>
                <?php $companyNameVal = $this->escape($this->employer->companyName); ?>
                <input class="inputbox" type="text" id="companyName"
                    name="companyName" size="50" maxlength="100"
                    value="<?php echo $companyNameVal; ?>" />
            </label>
            <label for="companyLocation">
                <?php echo Lang::txt('COM_JOBS_EMPLOYER_COMPANY_LOCATION'); ?>:
                <span class="required"><?php echo Lang::txt('COM_JOBS_REQUIRED'); ?></span>
                <?php $companyLocVal = $this->escape($this->employer->companyLocation); ?>
                <input class="inputbox" type="text" id="companyLocation"
                    name="companyLocation" size="50" maxlength="200"
                    value="<?php echo $companyLocVal; ?>" />
            </label>
            <label for="companyWebsite">
                <?php echo Lang::txt('COM_JOBS_EMPLOYER_COMPANY_WEBSITE'); ?>:
                <?php $companyWebVal = $this->escape($this->employer->companyWebsite); ?>
                <input class="inputbox" type="text" id="companyWebsite"
                    name="companyWebsite" size="50" maxlength="200"
                    value="<?php echo $companyWebVal; ?>" />
            </label>
        </fieldset>
        <div class="clear"></div>

        <div class="explaination">
            <p><?php echo Lang::txt('COM_JOBS_SUBSCRIBE_HINT_PICK') ?></p>
            <h4><?php echo Lang::txt('COM_JOBS_SUBSCRIBE_NEXT_STEP') ?></h4>
            <p><?php echo Lang::txt('COM_JOBS_SUBSCRIBE_HINT_PAYMENT') ?></p>
        </div>
        <fieldset>
            <legend><?php echo Lang::txt('COM_JOBS_SUBSCRIPTION_DETAILS'); ?></legend>

            <label>
                <?php echo Lang::txt('COM_JOBS_SUBSCRIBE_SELECT_SERVICE'); ?>:
                <span class="required"><?php echo Lang::txt('COM_JOBS_REQUIRED'); ?></span>
            </label>
            <?php
            $html = '';
            $now = Date::toSql();

            while ($this->services->valid()) {
                $currentService = $this->services->current();
                // do we have an active subscription?
                $thissub = ($currentService->id == $this->subscription->serviceid) ? 1 : 0;

                // Determine expiration date
                if ($thissub) {
                    $length = $this->subscription->status == 0
                        ? $this->subscription->pendingunits
                        : $this->subscription->units;
                    $expires = $this->subscription->expires > $now
                        && $this->subscription->status == 1
                        ? '<p class="yes">'
                        : '<p class="no">';
                    $subTxt = Lang::txt('COM_JOBS_SUBSCRIPTION');
                    $expires .= Lang::txt('COM_JOBS_YOUR') . ' '
                        . $length . '-' . $currentService->unitmeasure
                        . ' ' . $subTxt . ' ';
                    if ($this->subscription->status == 1) {
                        $expiresTxt = $this->subscription->expires > $now
                            ? strtolower(Lang::txt('COM_JOBS_SUBSCRIPTION_STATUS_EXPIRES'))
                            : strtolower(Lang::txt('COM_JOBS_SUBSCRIPTION_STATUS_EXPIRED'));
                        $expires .= $expiresTxt;
                        $expiresDate = Date::of($this->subscription->expires)
                            ->toLocal(Lang::txt('DATE_FORMAT_HZ1'));
                        $expires .= ' ' . Lang::txt('COM_JOBS_ON')
                            . ' ' . $expiresDate . '.';
                    } else {
                        $expires .= Lang::txt('COM_JOBS_SUBSCRIPTION_IS_PENDING');
                    }

                    $expires .= '</p>' . "\n";
                    if ($this->subscription->expires > $now) {
                        $cancelUrl = Route::url(
                            'index.php?option=' . $this->option
                            . '&task=cancel&uid=' . $this->uid
                        );
                        $cancelTxt = Lang::txt('COM_JOBS_SUBSCRIPTION_CANCEL_THIS');
                        $expires .= ' <a href="' . $cancelUrl
                            . '" class="cancelit" id="showconfirm">[ '
                            . $cancelTxt . ' ]</a>';
                    }
                    if (
                        $this->subscription->pendingunits > 0
                        && $this->subscription->status == 1
                    ) {
                        $pendingTxt = Lang::txt('COM_JOBS_SUBSCRIPTION_EXTEND_REQUEST_PENDING');
                        $expires .= '<p class="no">' . $pendingTxt . '</p>';
                    }
                }

                $units_select = array();
                $numunits = $currentService->maxunits / $currentService->unitsize;
                $unitsize = $currentService->unitsize;

                if ($thissub) {
                    $units_select[0] = 0;
                }
                for ($p = 1; $p <= $numunits; $p++) {
                    $units_select[$unitsize] = $unitsize;
                    $unitsize = $unitsize + $currentService->unitsize;
                }

                $unitsChoice = \Components\Jobs\Helpers\Html::formSelect(
                    'units_' . $currentService->id,
                    $units_select,
                    '',
                    "option units"
                );
                $iniprice = $thissub ? 0 : $currentService->unitprice;
                ?>
                <div class="bindtogether product">
                    <input class="option service" type="radio"
                        name="serviceid"
                        id="service_<?php echo $currentService->id; ?>"
                        value="<?php echo $currentService->id; ?>"
                    <?php if ($thissub or ($this->subscription->serviceid == 0 && $this->services->key() == 1)) {
                        echo 'checked="checked"';
                    }
                    echo '> ' . $currentService->title . ' - '; ?>
                    <span class="priceline">
                        <?php
                        echo $currentService->currency . ' '
                            . $currentService->unitprice . '  '
                            . Lang::txt('COM_JOBS_PER') . ' '
                            . $currentService->unitmeasure;
                        ?>
                    </span>
                    <span><?php echo $currentService->description; ?></span>

                    <div class="subdetails" id="plan_<?php echo $currentService->id; ?>">
                        <?php if ($thissub) {
                            echo $expires;
                        } else {
                            echo '';
                        }
                        if ($thissub or ($this->subscription->serviceid == 0 && $this->services->key() == 1)) {
                            $dashUrl = Route::url(
                                'index.php?option=' . $this->option
                                . '&task=dashboard&uid=' . $this->uid
                            );
                            $cancelUrl2 = Route::url(
                                'index.php?option=' . $this->option
                                . '&task=cancel&uid=' . $this->uid
                            );
                            \Components\Jobs\Helpers\Html::confirmscreen(
                                $dashUrl,
                                $cancelUrl2
                            );
                        }
                        ?>
                        <label>
                            <?php if ($thissub) {
                                echo Lang::txt('COM_JOBS_SUBSCRIPTION_EXTEND_OR_RENEW');
                            } else {
                                echo Lang::txt('COM_JOBS_ACTION_SIGN_UP');
                            }
                            echo ' ' . Lang::txt('for') . ' ';
                            echo $unitsChoice;
                            echo $currentService->unitmeasure; ?>
                            (s) </label>
                    <span class="totalprice">
                        <?php echo Lang::txt('COM_JOBS_SUBSCRIBE_YOUR_TOTAL') . ' ';
                        if ($thissub) {
                            echo strtolower(Lang::txt('COM_JOBS_NEW')) . ' ';
                        } else {
                            echo '';
                        }
                        echo Lang::txt('COM_JOBS_SUBSCRIBE_PAYMENT_WILL_BE'); ?>
                        <span class="no"><?php echo $currentService->currency; ?></span>
                    <span id="injecttotal_<?php echo $currentService->id; ?>"><?php echo $iniprice; ?></span>
                    </span>

                        <!-- GOOGLE Checkout (TBD) -->
                        <input type="hidden" class="product-price"
                            value="<?php echo $this->escape($currentService->unitprice); ?>" />
                        <input type="hidden" class="product-title"
                            value="<?php echo $this->escape($currentService->title); ?>" />

                    </div>
                </div>
                <?php $priceId = $currentService->id; ?>
                <input type="hidden"
                    name="price_<?php echo $priceId; ?>"
                    id="price_<?php echo $priceId; ?>"
                    value="<?php echo $this->escape($currentService->unitprice); ?>" />
                <?php $this->services->next();
            }
            $btn = $this->subscription->id
                ? Lang::txt('COM_JOBS_SUBSCRIPTION_SAVE')
                : Lang::txt('COM_JOBS_SUBSCRIPTION_PROCESS_ORDER');
            ?>
            <label for="contact">
                <?php
                $contactLabel = Lang::txt('COM_JOBS_SUBSCRIPTION_CONTACT_PHONE');
                $requiredTxt = Lang::txt('COM_JOBS_REQUIRED_WITH_PAYMENT');
                echo $contactLabel . ': <span class="required">'
                    . $requiredTxt . '</span>';
                ?>
                <?php $contactVal = $this->escape($this->subscription->contact); ?>
                <input class="inputbox" type="text" id="contact"
                    name="contact" size="50" maxlength="15"
                    value="<?php echo $contactVal; ?>" />
            </label>

            <div class="submitblock">
                <input type="hidden" name="subid" value="<?php echo $this->employer->subscriptionid; ?>" />
                <input type="hidden" name="uid" value="<?php echo $this->uid; ?>" />
                <input type="submit" class="option" value="<?php echo $btn; ?>" />
            </div>
        </fieldset>
    </form>
    <div class="clear"></div>
</section>
