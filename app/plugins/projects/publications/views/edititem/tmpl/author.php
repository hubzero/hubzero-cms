<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

// Load full record
$pAuthor 	= new \Components\Publications\Tables\Author( $this->database );

$author = $pAuthor->getAuthorByOwnerId($this->row->publication_version_id, $this->row->project_owner_id);

// Get profile thumb image
$profile = User::getInstance($this->row->user_id);

$actor   = User::getInstance(User::get('id'));

$thumb   = $profile->get('id') ? $profile->picture() : $actor->picture(true);

$name = $author->name ? $author->name : $author->p_name;
$name = trim($name) ? $name : $author->invited_name;

if (trim($name)) {
	$nameParts    = explode(" ", $name);
	$lastname  	  = end($nameParts);
	$firstname    = count($nameParts) > 1 ? $nameParts[0] : '';
}
else {
	$firstname = htmlspecialchars($author->givenName);
	$lastname  = htmlspecialchars($author->surname);
	if (!$author->user_id)
	{
		$name = $author->invited_email;
	}
}

$firstname = $author->firstName ? htmlspecialchars($author->firstName) : $firstname;
$lastname = $author->lastName ? htmlspecialchars($author->lastName) : $lastname;

?>
<div id="abox-content">
<h3><?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_AUTHORS_EDIT_AUTHOR'); ?></h3>
	<form id="hubForm-ajax" method="post" action="">
			<fieldset>
				<input type="hidden" name="id" value="<?php echo $this->project->get('id'); ?>" />
				<input type="hidden" name="aid" value="<?php echo $this->row->id; ?>" />
				<input type="hidden" name="pid" value="<?php echo $this->pub->id; ?>" />
				<input type="hidden" name="version" value="<?php echo $this->pub->version_number; ?>" />
				<input type="hidden" name="p" value="<?php echo $this->props; ?>" />
				<input type="hidden" name="action" value="saveitem" />
				<input type="hidden" name="active" value="publications" />
				<input type="hidden" name="option" value="<?php echo $this->project->isProvisioned() ? 'com_publications' : $this->option; ?>" />
				<input type="hidden" name="backUrl" value="<?php echo $this->backUrl; ?>" />
				<?php if ($this->project->isProvisioned()) { ?>
				<input type="hidden" name="task" value="submit" />
				<?php } ?>
			</fieldset>
			<div class="content-wrap">
				<div class="profile-info">
					<p><img src="<?php echo $thumb; ?>" alt="<?php echo $name; ?>" />
						<span>
						<span class="block faded"><?php echo ucfirst(Lang::txt('PLG_PROJECTS_PUBLICATIONS_AUTHORS_TEAM_MEMBER')); ?>:</span>
						<?php echo $author->username ? $author->p_name.' ('.$author->username.')' : $name.' (unconfirmed)';  ?></span>
					</p>
				</div>
				<div class="author-edit">
					<label class="display_inline">
						<span class="leftshift faded"><?php echo ucfirst(Lang::txt('PLG_PROJECTS_PUBLICATIONS_AUTHORS_AUTHOR_FIRST_NAME')); ?>*:</span>
						<input type="text" name="firstName" value="<?php echo $firstname;  ?>" maxlength="255" />
					</label>
					<label class="display_inline">
						<span class="faded"><?php echo ucfirst(Lang::txt('PLG_PROJECTS_PUBLICATIONS_AUTHORS_AUTHOR_LAST_NAME')); ?>*:</span>
						<input type="text" name="lastName" value="<?php echo $lastname;  ?>" maxlength="255" />
					</label>
					<div class="clear"></div>
					<label for="organization">
						<span class="leftshift faded"><?php echo ucfirst(Lang::txt('PLG_PROJECTS_PUBLICATIONS_AUTHORS_AUTHOR_ORGANIZATION')); ?>*:</span>
						<input type="text" name="organization" class="long" value="<?php echo $author->organization ? htmlspecialchars($author->organization) : htmlspecialchars($author->p_organization); ?>" maxlength="255" />
					</label>
					<p class="hint"><?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_AUTHORS_REQUIRED_FIELDS'); ?></p>
					<div class="clear"></div>
					<?php if (!$author->username) { ?>
						<label for="email">
							<span class="leftshift faded"><?php echo ucfirst(Lang::txt('PLG_PROJECTS_PUBLICATIONS_AUTHORS_AUTHOR_EMAIL')); ?>:</span>
							<input type="text" name="email" class="long" value="<?php echo $author->invited_email ? $author->invited_email : ''; ?>" maxlength="255" /><span class="optional"><?php echo Lang::txt('OPTIONAL'); ?></span>
						</label>
						<div class="clear"></div>
					<?php } ?>
					<label for="credit">
						<span class="leftshift faded"><?php echo ucfirst(Lang::txt('PLG_PROJECTS_PUBLICATIONS_AUTHORS_AUTHOR_CREDIT')); ?>:</span>
						<input type="text" name="credit"  class="long" value="<?php echo htmlspecialchars($author->credit); ?>" maxlength="255"  /><span class="optional"><?php echo Lang::txt('OPTIONAL'); ?></span>
					</label>
				</div>

				<p class="submitarea">
					<input type="submit" class="btn" value="<?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_SAVE'); ?>" />
					<?php if ($this->ajax) { ?>
					<input type="reset" id="cancel-action" class="btn btn-cancel" value="<?php echo Lang::txt('JCANCEL'); ?>" />
					<?php } else { ?>
					<a href="<?php echo $this->backUrl; ?>" class="btn btn-cancel"><?php echo Lang::txt('JCANCEL'); ?></a>
					<?php } ?>
				</p>
				
			</div>
	</form>
	<div class="clear"></div>
</div>