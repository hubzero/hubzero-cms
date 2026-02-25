<?php

// @phpcs:disable PSR1.Files.SideEffects

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

// Load full record
$pAuthor    = new \Components\Publications\Tables\Author($this->database);

$author = $pAuthor->getAuthorByOwnerId($this->row->publication_version_id, $this->row->project_owner_id);

// Get profile thumb image
$profile = User::getInstance($this->row->user_id);

$actor   = User::getInstance(User::get('id'));

$thumb   = $profile->get('id') ? $profile->picture() : $actor->picture(true);

$name = $author->name ? $author->name : $author->p_name;
$name = trim($name) ? $name : $author->invited_name;

if (trim($name)) {
    $nameParts    = explode(" ", $name);
    $lastname     = end($nameParts);
    $firstname    = count($nameParts) > 1 ? $nameParts[0] : '';
} else {
    $firstname = htmlspecialchars($author->givenName);
    $lastname  = htmlspecialchars($author->surname);
    if (!$author->user_id) {
        $name = $author->invited_email;
    }
}

$firstname = $author->firstName ? htmlspecialchars($author->firstName) : $firstname;
$lastname = $author->lastName ? htmlspecialchars($author->lastName) : $lastname;

$scriptBase = rtrim(Request::base(true), '/');
$scriptSrc = $scriptBase
    . '/core/plugins/projects/publications'
    . '/assets/js/editauthor.js';

?>
<script src="<?php echo $scriptSrc; ?>"></script>
<div id="abox-content">
<h3><?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_AUTHORS_EDIT_AUTHOR'); ?></h3>
    <form id="hubForm-ajax" method="post" action="">
            <fieldset>
                <input type="hidden" name="id" value="<?php echo $this->project->get('id'); ?>" />
                <input type="hidden" name="aid" value="<?php echo $this->row->id; ?>" />
                <input type="hidden" name="uid" value="<?php echo $this->row->user_id; ?>" />
                <input type="hidden" name="pid" value="<?php echo $this->pub->id; ?>" />
                <input type="hidden" name="version" value="<?php echo $this->pub->version_number; ?>" />
                <input type="hidden" name="p" value="<?php echo $this->props; ?>" />
                <input type="hidden" name="action" value="saveitem" />
                <input type="hidden" name="active" value="publications" />
                <input type="hidden"
                    name="option"
                    value="<?php echo $this->project->isProvisioned() ? 'com_publications' : $this->option; ?>"/>
                <input type="hidden" name="backUrl" value="<?php echo $this->backUrl; ?>" />
                <?php if ($this->project->isProvisioned()) { ?>
                <input type="hidden" name="task" value="submit" />
                <?php } ?>
            </fieldset>
            <div class="content-wrap">
                <div class="profile-info">
                    <p><img src="<?php echo $thumb; ?>" alt="<?php echo $name; ?>" />
                        <span>
                        <span class="block faded"><?php
                            $teamLabel = ucfirst(Lang::txt(
                                'PLG_PROJECTS_PUBLICATIONS_AUTHORS_TEAM_MEMBER'
                            ));
                            echo $teamLabel;
                            ?>:</span>
                        <?php echo $author->username ? $author->p_name
                            . ' ('
                            . $author->username
                            . ')' : $name
                            . ' (unconfirmed)';  ?></span>
                    </p>
                </div>
                <div class="author-edit">
                    <label class="display_inline">
                        <?php
                        $firstNameLabel = ucfirst(Lang::txt(
                            'PLG_PROJECTS_PUBLICATIONS_AUTHORS_AUTHOR_FIRST_NAME'
                        ));
                        ?>
                        <span class="leftshift faded">
                            <?php echo $firstNameLabel; ?>*:
                        </span>
                        <input type="text" name="firstName" value="<?php echo $firstname;  ?>" maxlength="255" />
                    </label>
                    <div class="clear"></div>
                    <label class="display_inline">
                        <?php
                        $lastNameLabel = ucfirst(Lang::txt(
                            'PLG_PROJECTS_PUBLICATIONS_AUTHORS_AUTHOR_LAST_NAME'
                        ));
                        ?>
                        <span class="faded">
                            <?php echo $lastNameLabel; ?>*:
                        </span>
                        <input type="text" name="lastName" value="<?php echo $lastname;  ?>" maxlength="255" />
                    </label>
                    <div class="clear"></div>
                    <label for="organization">
                        <?php
                        $orgLabel = ucfirst(Lang::txt(
                            'PLG_PROJECTS_PUBLICATIONS_AUTHORS_AUTHOR_ORGANIZATION'
                        ));
                        $orgValue = (isset($author->organization) && $author->organization)
                            ? htmlspecialchars(
                                $author->organization ?? ''
                            )
                            : htmlspecialchars(
                                $author->p_organization ?? ''
                            );
                        ?>
                        <span class="leftshift faded">
                            <?php echo $orgLabel; ?>*:
                        </span>
                        <input type="text"
                            name="organization"
                            class="long"
                            value="<?php echo $orgValue; ?>"
                            maxlength="255" />
                        <?php
                            // Add in class for JS selector to conditionally retrieve data from RoR Api
                        if (\Hubzero\Facades\Component::params('com_members')->get('rorApi')) {
                            echo "<div id='autocomplete-organization' class='rorApiAvailable'></div>";
                        }
                        ?>
                    </label>
                    <div class="clear"></div>
                    <p class="hint"><?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_AUTHORS_REQUIRED_FIELDS'); ?></p>
                    <div class="clear"></div>
                    <?php
                    $emailLabel = ucfirst(Lang::txt(
                        'PLG_PROJECTS_PUBLICATIONS_AUTHORS_AUTHOR_EMAIL'
                    ));
                    ?>
                    <?php if (!$author->username) { ?>
                        <label for="email">
                            <span class="leftshift faded">
                                <?php echo $emailLabel; ?>:
                            </span>
                            <input type="text"
                                name="email"
                                class="long"
                                value="<?php echo $author->invited_email ?: ''; ?>"
                                maxlength="255"/>
                            <span class="optional">
                                <?php echo Lang::txt('OPTIONAL'); ?>
                            </span>
                        </label>
                        <div class="clear"></div>
                    <?php } else { ?>
                        <label for="email">
                            <span class="leftshift faded">
                                <?php echo $emailLabel; ?>:
                            </span>
                            <input type="text"
                                name="email"
                                class="long"
                                value="<?php echo $author->p_email ?: ''; ?>"
                                maxlength="255"/>
                            <span class="optional">
                                <?php echo Lang::txt('OPTIONAL'); ?>
                            </span>
                        </label>
                        <div class="clear"></div>
                    <?php } ?>
                    <label for="orcid">
                        <?php
                        $orcidLabel = Lang::txt(
                            'PLG_PROJECTS_PUBLICATIONS_AUTHORS_ORCID_ID'
                        );
                        ?>
                        <span class="leftshift faded">
                            <?php echo $orcidLabel; ?>:
                        </span>
                        <input type="text"
                            name="orcid"
                            class="long"
                            placeholder="####-####-####-####"
                            value="<?php echo $author->orcid; ?>"
                            maxlength="255"/>
                        <p id="orcid-message"
                            class="hint"><?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_AUTHORS_ORCID_ID_DESC'); ?></p>
                        <span class="optional"><?php echo Lang::txt('OPTIONAL'); ?></span>
                    </label>
                    <div class="clear"></div>
                    <label for="credit">
                        <?php
                        $creditLabel = ucfirst(Lang::txt(
                            'PLG_PROJECTS_PUBLICATIONS_AUTHORS_AUTHOR_CREDIT'
                        ));
                        ?>
                        <span class="leftshift faded">
                            <?php echo $creditLabel; ?>:
                        </span>
                        <input type="text"
                            name="credit"
                            class="long"
                            value="<?php echo htmlspecialchars($author->credit ?: ''); ?>"
                            maxlength="255"/><span class="optional"><?php echo Lang::txt('OPTIONAL'); ?></span>
                    </label>
                    <div class="clear"></div>
                </div>

                <p class="submitarea">
                    <input type="submit"
                        class="btn"
                        value="<?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_SAVE'); ?>"/>
                    <?php if ($this->ajax) { ?>
                    <input type="reset"
                        id="cancel-action"
                        class="btn btn-cancel"
                        value="<?php echo Lang::txt('JCANCEL'); ?>"/>
                    <?php } else { ?>
                    <a href="<?php echo $this->backUrl; ?>"
                        class="btn btn-cancel"><?php echo Lang::txt('JCANCEL'); ?></a>
                    <?php } ?>
                </p>
                
            </div>
    </form>
    <div class="clear"></div>
</div>
