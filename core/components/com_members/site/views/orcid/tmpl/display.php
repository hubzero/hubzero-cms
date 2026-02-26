<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Facades\Lang;
use Hubzero\Facades\Request;
use Hubzero\Facades\Route;
use Hubzero\Facades\User;

// No direct access
defined('_HZEXEC_') or die();

$fname = Request::getString('fname', '');
$lname = Request::getString('lname', '');
$email = Request::getString('email', '');

$returnOrcid = Request::getInt('return', 0);

$isRegister = $returnOrcid == 1;

/*if ($isRegister)
{
    $callbackPrefix = "HUB.Register.";
}
else
{
    $callbackPrefix = "HUB.Members.Profile.";
}*/

if (!$isRegister) {
    // Instantiate a new profile object
    $profile = User::getInstance();
    if ($profile) {
        $fname = $fname ?: $profile->get('givenName');
        $lname = $lname ?: $profile->get('surname');
        $email = $email ?: $profile->get('email');
    }
}


$returnOrcid = Request::getInt('return', 0);
$isRegister = $returnOrcid == 1;

/*if (Request::getInt('return', 0) == 1)
{
    $this->js('register.js');
}
else
{
    \Hubzero\Document\Assets::addPluginScript('members', 'profile');
}*/

$this->js('orcid.js');
$this->css('orcid.css');

$srv = $this->config->get('orcid_service', 'members');
$tkn = $this->config->get('orcid_' . $srv . '_token');
$clientID = $this->config->get('orcid_' . $srv . '_client_id', '');
$redirectURI = $this->config->get('orcid_' . $srv . '_redirect_uri', '');

?>
<section class="main section">
    <form name="orcid-search-form">
        <?php if ($srv != 'public' && !$tkn) { ?>
            <?php
            $val = Lang::txt(
                'COM_MEMBERS_PROFILE_ORCID_UNAVAILABLE',
                Route::url('index.php?option=com_support')
            );
            ?>
            <p class="warning"><?php echo $val; ?></p>
        <?php } else { ?>
            <h3><?php echo Lang::txt('COM_MEMBERS_PROFILE_ORCID_ASSOCIATE_ORCID'); ?></h3>
            <fieldset>
                <legend><?php echo Lang::txt('COM_MEMBERS_PROFILE_ORCID_PROFILE_INFO'); ?></legend>

                <div class="grid nobreak">
                    <div class="col span4">
                        <label for="first-name">
                            <?php $val = Lang::txt('COM_MEMBERS_PROFILE_ORCID_FIRST_NAME'); ?>
                            <?php $val1 = Lang::txt('COM_MEMBERS_SEARCH_ORCID_REQUIRED'); ?>
                            <?php echo $val; ?><span class="required"><?php echo $val1; ?></span>
                            <input
                                type="text"
                                id="first-name"
                                name="first-name"
                                value="<?php echo $this->escape($fname); ?>"/>
                        </label>
                    </div>
                    <div class="col span4">
                        <label for="last-name">
                            <?php $val = Lang::txt('COM_MEMBERS_PROFILE_ORCID_LAST_NAME'); ?>
                            <?php $val1 = Lang::txt('COM_MEMBERS_SEARCH_ORCID_REQUIRED'); ?>
                            <?php echo $val; ?><span class="required"><?php echo $val1; ?></span>
                            <input
                                type="text"
                                id="last-name"
                                name="last-name"
                                value="<?php echo $this->escape($lname); ?>"/>
                        </label>
                    </div>
                    <div id="alert-message hide" class="col span8">
                        <p><?php echo Lang::txt('COM_MEMBERS_SEARCH_ORCID_ALERT_NAME'); ?></p>
                    </div>
                </div>

                <input
                    type="hidden"
                    name="base_uri"
                    id="base_uri"
                    value="<?php echo rtrim(Request::base(true), '/'); ?>"/>
            </fieldset>

            <div class="orcid-section orcid-search">
                <h4><?php echo Lang::txt('COM_MEMBERS_PROFILE_ORCID_SEARCH_FOR_EXISTING'); ?></h4>
                <div class="grid nobreak">
                    <div class="col span8">
                        <p><?php echo Lang::txt('COM_MEMBERS_PROFILE_ORCID_FILL_AND_SEARCH'); ?></p>
                    </div>
                    <div class="col span4 omega">
                        <p>
                            <?php $val = Lang::txt('Search ORCID'); ?>
                            <?php
                            $orcidClick = 'HUB.Orcid.fetchOrcidRecords(\''
                                . $this->escape($fname) . '\', \''
                                . $this->escape($lname) . '\');';
                            ?>
                            <a
                                id="get-orcid-results"
                                class="btn"
                                onclick="<?php echo $orcidClick; ?>"><?php echo $val; ?></a>
                        </p>
                    </div>
                </div>

                <div id="section-orcid-results">
                    <?php
                    if (isset($this->orcid_records_html)) {
                        echo $this->orcid_records_html;
                    }
                    ?>
                </div>
            </div>

            <?php if ($this->config->get('orcid_service', 'members') != 'public') { ?>
                <div class="orcid-section orcid-create">
                    <h4><?php echo Lang::txt('COM_MEMBERS_PROFILE_ORCID_CREATE_ORCID'); ?></h4>
                    <div class="grid nobreak">
                        <div class="col span8">
                            <p><?php echo Lang::txt('COM_MEMBERS_PROFILE_ORCID_CLICK_CREATE_BUTTON'); ?></p>
                        </div>
                        <div class="col span4 omega">
                            <?php
                            $amp = htmlspecialchars('&');
                            $orcidHost = ($this->config->get('orcid_service', 'members') == 'sandbox')
                                ? 'sandbox.orcid.org'
                                : 'orcid.org';
                            $orcidUrl = 'https://' . $orcidHost
                                . '/oauth/authorize?client_id=' . $clientID
                                . $amp . 'response_type=code'
                                . $amp . 'scope=/authenticate'
                                . $amp . 'redirect_uri=' . urlencode($redirectURI)
                                . $amp . 'family_names=' . $this->escape($lname)
                                . $amp . 'given_names=' . $this->escape($fname)
                                . $amp . 'email=' . $this->escape($email);
                            $connectTxt = Lang::txt(
                                'COM_MEMBERS_PROFILE_ORCID_CREATE_OR_CONNECT'
                            );
                            ?>
                            <p><a
                                id="create-orcid"
                                class="btn"
                                href="<?php echo $orcidUrl; ?>"
                                rel="nofollow external">
                            <?php echo $connectTxt; ?></a></p>
                        </div>
                    </div>
                </div>
            <?php } ?>
        <?php } ?>
    </form>
</section>