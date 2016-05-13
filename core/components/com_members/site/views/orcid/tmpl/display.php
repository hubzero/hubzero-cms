<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Ahmed Abdel-Gawad <aabdelga@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$fname = Request::getVar('fname', '');
$lname = Request::getVar('lname', '');
$email = Request::getVar('email', '');

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

if (!$isRegister)
{
	// Instantiate a new profile object
	$profile = User::getInstance();
	if ($profile)
	{
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
?>
<section class="main section">
	<form name="orcid-search-form">
		<?php if ($srv != 'public' && !$tkn) { ?>
			<p class="warning"><?php echo Lang::txt('This service is currently unavailable and/or not configured correctly. Please contact <a href="%s">support</a> for further assistance.', Route::url('index.php?option=com_support')); ?></p>
		<?php } else { ?>
			<h3><?php echo Lang::txt('Associate your <b>ORCID</b> (Open Researcher and Contributor ID)'); ?></h3>
			<fieldset>
				<legend><?php echo Lang::txt('Profile Info'); ?></legend>

				<div class="grid nobreak">
					<div class="col span4">
						<label for="first-name">
							<?php echo Lang::txt('First name:'); ?>
							<input type="text" id="first-name" name="first-name" value="<?php echo $this->escape($fname); ?>" />
						</label>
					</div>
					<div class="col span4">
						<label for="last-name">
							<?php echo Lang::txt('Last name:'); ?>
							<input type="text" id="last-name" name="last-name" value="<?php echo $this->escape($lname); ?>" />
						</label>
					</div>
					<div class="col span4 omega">
						<label for="email">
							<?php echo Lang::txt('Email:'); ?>
							<input type="text" id="email" name="email" value="<?php echo $this->escape($email); ?>" />
						</label>
					</div>
				</div>

				<input type="hidden" name="base_uri" id="base_uri" value="<?php echo rtrim(Request::base(true), '/'); ?>" />
			</fieldset>

			<div class="orcid-section orcid-search">
				<h4><?php echo Lang::txt('Search for an existing ORCID'); ?></h4>
				<div class="grid nobreak">
					<div class="col span8">
						<p><?php echo Lang::txt('If you have created an ORCID or your institution has generated one for you, fill in the fields above and search for your ID from the list.'); ?></p>
						<p><?php echo Lang::txt('Note that most ORCID records have the email address marked as private and private information will not be returned in the search results.'); ?></p>
					</div>
					<div class="col span4 omega">
						<p>
							<a id="get-orcid-results" class="btn" href="javascript:;"><?php echo Lang::txt('Search ORCID'); ?></a>
						</p>
					</div>
				</div>

				<div id="section-orcid-results">
					<?php
					if (isset($this->orcid_records_html))
					{
						echo $this->orcid_records_html;
					}
					?>
				</div>
			</div>

			<?php if ($this->config->get('orcid_service', 'members') != 'public') { ?>
				<div class="orcid-section orcid-create">
					<h4><?php echo Lang::txt('Create a new ORCID'); ?></h4>
					<div class="grid nobreak">
						<div class="col span8">
							<p><?php echo Lang::txt('If you can\'t find your ID or would like to create one, click the "Create new ORCID" button to generate a new ID based on the info above. You will receive an email from ORCID to claim the new ID.'); ?></p>
						</div>
						<div class="col span4 omega">
							<p><a id="create-orcid" class="btn" onclick="<?php echo "HUB.Orcid.createOrcid(document.getElementById('first-name').value, document.getElementById('last-name').value, document.getElementById('email').value)"; ?>"><?php echo Lang::txt('Create new ORCID'); ?></a></p>
						</div>
					</div>
				</div>
			<?php } ?>
		<?php } ?>
	</form>
</section>