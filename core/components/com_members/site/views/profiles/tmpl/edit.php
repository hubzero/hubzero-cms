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
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$this->css();

$surname = stripslashes($this->profile->get('surname'));
$givenName = stripslashes($this->profile->get('givenName'));
$middleName = stripslashes($this->profile->get('middleName'));

if (!$surname) {
	$bits = explode(' ', $name);
	$surname = array_pop($bits);
	if (count($bits) >= 1) {
		$givenName = array_shift($bits);
	}
	if (count($bits) >= 1) {
		$middleName = implode(' ',$bits);
	}
}

$html  = '<header id="content-header">'."\n";
$html .= "\t".'<h2>'.$this->title.'</h2>'."\n";
$html .= '<div id="content-header-extra">'."\n";
$html .= "\t".'<ul id="useroptions">'."\n";
$html .= "\t\t".'<li class="last"><a href="'.Route::url('index.php?option='.$this->option.'&task=cancel&id='. $this->profile->get('id')) .'">'.Lang::txt('CANCEL').'</a></li>'."\n";
$html .= "\t".'</ul>'."\n";
$html .= '</div><!-- / #content-header-extra -->'."\n";
$html .= '</header><!-- / #content-header-extra -->'."\n";

$html .= '<section class="main section">'."\n";


$html .= "\t".'<form id="hubForm" class="edit-profile" method="post" action="index.php" enctype="multipart/form-data">'."\n";

if ($this->authorized === 'admin') {
	$html .= "\t".'<div class="explaination">'."\n";
	$html .= "\t\t".'<p>'.Lang::txt('The following options are available to administrators only.').'</p>'."\n";
	$html .= "\t".'</div>'."\n";
	$html .= "\t".'<fieldset>'."\n";
	$html .= "\t\t".'<legend>'.Lang::txt('Admin Options').'</legend>'."\n";
	$html .= "\t\t".'<label>'."\n";
	$html .= "\t\t\t".'<input type="checkbox" class="option" name="profile[vip]" value="1"';
	if ($this->profile->get('vip') == 1) {
		$html .= ' checked="checked"';
	}
	$html .= '/>'."\n";
	$html .= "\t\t\t".Lang::txt('VIP')."\n";
	$html .= "\t\t".'</label>'."\n";
	$html .= "<span class=\"hint\">".Lang::txt('**The following options are available to administrators only.')."</span>";
	$html .= "\t".'</fieldset><div class="clear"></div>'."\n";
} else {
	$html .= "\t\t".'<input type="hidden" name="profile[vip]" value="'. $this->profile->get('vip') .'" />'."\n";
}

//$html .= "\t".'<div class="explaination">'."\n";
//$html .= "\t\t".'<p class="help">'.Lang::txt('E-mail may be changed with <a href="/hub/registration/edit">this form</a>.')."\n";
//$html .= "\t\t".'<p class="help">'.Lang::txt('Passwords can be changed with <a href="'.Route::url('index.php?option='.$this->option.a.'id='.$this->profile->get('id').a.'task=changepassword').'">this form</a>.').'</p>'."\n";

//$mwconfig = Component::params( 'com_mw' );
//$enabled = $mwconfig->get('mw_on');
//if ($enabled) {
//	$html .= "\t\t".'<p class="help">'.Lang::txt('Request for more storage or sessions may be made with <a href="'.Route::url('index.php?option='.$this->option.a.'id='.$this->profile->get('id').a.'task=raiselimit').'">this form</a>.').'</p>'."\n";
//}
//$html .= "\t".'</div>'."\n";
$html .= "\t".'<fieldset>'."\n";
$html .= "\t\t".'<legend>'.Lang::txt('Contact Information').'</legend>'."\n";
$html .= "\t\t".'<input type="hidden" name="id" value="'. $this->profile->get('id') .'" />'."\n";
$html .= "\t\t".'<input type="hidden" name="option" value="'. $this->option .'" />'."\n";
$html .= "\t\t".'<input type="hidden" name="task" value="save" />'."\n";

$html .= "\t\t".'<label>'."\n";
$html .= "\t\t\t".'<input type="checkbox" class="option" name="profile[public]" value="1"';
if ($this->profile->get('public') == 1) {
	$html .= ' checked="checked"';
}
$html .= '/>'."\n";
$html .= "\t\t\t".Lang::txt('List me in the Members directory (others may view my profile)')."\n";
$html .= "\t\t".'</label>'."\n";

if ($this->registration->Fullname != REG_HIDE) {
	$required = ($this->registration->Fullname == REG_REQUIRED) ? ' <span class="required">'.Lang::txt('REQUIRED').'</span>' : '';
	$message = (!empty($this->xregistration->_invalid['name'])) ? '<p class="error">' . $this->xregistration->_invalid['name'] . '</p>' : '';
	$fieldclass = ($message) ? ' class="fieldWithErrors"' : '';

	$html .= "\t\t".'<div class="threeup group">'."\n";
	$html .= "\t\t".'<label'.$fieldclass.'>'."\n";
	$html .= "\t\t\t".Lang::txt('FIRST_NAME').': '.$required."\n";
	$html .= "\t\t\t".'<input type="text" name="name[first]" value="'. $givenName .'" />'."\n";
	$html .= "\t\t".'</label>'."\n";

	$html .= "\t\t".'<label>'."\n";
	$html .= "\t\t\t".Lang::txt('MIDDLE_NAME').':'."\n";
	$html .= "\t\t\t".'<input type="text" name="name[middle]" value="'. $middleName .'" />'."\n";
	$html .= "\t\t".'</label>'."\n";

	$html .= "\t\t".'<label'.$fieldclass.'>'."\n";
	$html .= "\t\t\t".Lang::txt('LAST_NAME').': '.$required."\n";
	$html .= "\t\t\t".'<input type="text" name="name[last]" value="'. $surname .'" />'."\n";
	$html .= "\t\t".'</label>'."\n";
	$html .= "\t\t".'</div>'."\n";
	$html .= $message;
}

if ($this->registration->Email != REG_HIDE || $this->registration->ConfirmEmail != REG_HIDE)
{
	$html .= "\t\t".'<div class="group twoup">'."\n";

	// Email
	if ($this->registration->Email != REG_HIDE) {
		$required = ($this->registration->Email == REG_REQUIRED) ? '<span class="required">'.Lang::txt('required').'</span>' : '';
		$message = (!empty($this->xregistration->_invalid['email'])) ? '<span class="error">' . $this->xregistration->_invalid['email'] . '</span>' : '';
		$fieldclass = ($message) ? ' class="fieldWithErrors"' : '';

		$html .= "\t\t\t".'<label '.$fieldclass.'>'."\n";
		$html .= "\t\t\t\t".Lang::txt('Valid E-mail').': '.$required."\n";
		$html .= "\t\t\t\t".'<input name="email" id="email" type="text" value="'.$this->escape($this->profile->get('email')).'" />'."\n";
		$html .= ($message) ? "\t\t\t\t".$message."\n" : '';
		$html .= "\t\t\t".'</label>'."\n";
	}

	// Confirm email
	if ($this->registration->ConfirmEmail != REG_HIDE) {
		$message = '';
		$confirmEmail = $this->profile->get('email');
		if (!empty($this->xregistration->_invalid['email'])) {
			$confirmEmail = '';
		}
		if (!empty($this->xregistration->_invalid['confirmEmail'])) {
			$message = '<span class="error">' . $this->xregistration->_invalid['confirmEmail'] . '</span>';
		}

		$required = ($this->registration->ConfirmEmail == REG_REQUIRED) ? '<span class="required">'.Lang::txt('required').'</span>' : '';
		$fieldclass = ($message) ? ' class="fieldWithErrors"' : '';

		$html .= "\t\t\t".'<label'.$fieldclass.'>'."\n";
		$html .= "\t\t\t\t".Lang::txt('Confirm E-mail').': '.$required."\n";
		$html .= "\t\t\t\t".'<input name="email2" id="email2" type="text" value="'.$this->escape($confirmEmail).'" />'."\n";
		$html .= ($message) ? "\t\t\t\t".$message."\n" : '';
		$html .= "\t\t\t".'</label>'."\n";
	}

	$html .= "\t\t".'</div>'."\n";

	if ($this->registration->Email != REG_HIDE) {
		$html .= "\t\t".'<p class="warning">Important! If you change your E-Mail address you <strong>must</strong> confirm receipt of the confirmation e-mail in order to re-activate your account.</p>';
	}
}

if ($this->registration->ORCID != REG_HIDE) {
	$required = ($this->registration->ORCID == REG_REQUIRED) ? '<span class="required">'.Lang::txt('REQUIRED').'</span>' : '';
	$message = (!empty($this->xregistration->_invalid['orcid'])) ? '<p class="error">' . $this->xregistration->_invalid['orcid'] . '</p>' : '';
	$fieldclass = ($message) ? ' class="fieldWithErrors"' : '';

	$html .= '<div class="grid">
				<div class="col span9">
					<label'.$fieldclass.'>' .
						Lang::txt('ORCID').': '.$required. '
						<input type="text" name="orcid" value="'. $this->escape(stripslashes($this->profile->get('orcid'))) .'" />
						' . $message . '
					</label>
				</div>
				<div class="col span3 omega">
					<a class="btn icon-search" id="orcid-fetch" href="' . Route::url('index.php?option=' . $this->option . '&controller=orcid') . '">' . Lang::txt('Find your ID') . '</a>
				</div>
			</div>
			<p>ORCID provides a persistent digital identifier that distinguishes you from every other researcher and supports automated linkages between you and your professional activities ensuring that your work is recognized. <a rel="external" href="http://orcid.org">Find out more.</a></p>'."\n";
}

if ($this->registration->URL != REG_HIDE) {
	$required = ($this->registration->URL == REG_REQUIRED) ? '<span class="required">'.Lang::txt('REQUIRED').'</span>' : '';
	$message = (!empty($this->xregistration->_invalid['web'])) ? '<p class="error">' . $this->xregistration->_invalid['web'] . '</p>' : '';
	$fieldclass = ($message) ? ' class="fieldWithErrors"' : '';

	$html .= "\t\t".'<label'.$fieldclass.'>'."\n";
	$html .= "\t\t\t".Lang::txt('WEBSITE').': '.$required."\n";
	$html .= "\t\t\t".'<input type="text" name="web" value="'. $this->escape(stripslashes($this->profile->get('url'))) .'" />'."\n";
	$html .= $message;
	$html .= "\t\t".'</label>'."\n";
}

if ($this->registration->Phone != REG_HIDE) {
	$required = ($this->registration->Phone == REG_REQUIRED) ? '<span class="required">'.Lang::txt('REQUIRED').'</span>' : '';
	$message = (!empty($this->xregistration->_invalid['phone'])) ? '<p class="error">' . $this->xregistration->_invalid['phone'] . '</p>' : '';
	$fieldclass = ($message) ? ' class="fieldWithErrors"' : '';

	$html .= "\t\t".'<label'.$fieldclass.'>'."\n";
	$html .= "\t\t\t".Lang::txt('Phone').': '.$required."\n";
	$html .= "\t\t\t".'<input type="text" name="phone" value="'. $this->escape(stripslashes($this->profile->get('phone'))) .'" />'."\n";
	$html .= $message;
	$html .= "\t\t".'</label>'."\n";
}

$html .= "\t".'</fieldset><div class="clear"></div>'."\n";
$html .= "\t".'<fieldset>'."\n";
$html .= "\t\t".'<legend>'.Lang::txt('Personal Information').'</legend>'."\n";

if ($this->registration->Employment != REG_HIDE) {
	$required = ($this->registration->Employment == REG_REQUIRED) ? '<span class="required">'.Lang::txt('REQUIRED').'</span>' : '';
	$message = (!empty($this->xregistration->_invalid['orgtype'])) ? '<p class="error">' . $this->xregistration->_invalid['orgtype'] . '</p>' : '';
	$fieldclass = ($message) ? ' class="fieldWithErrors"' : '';

	$orgtype = stripslashes($this->profile->get('orgtype'));

	$html .= "\t\t".'<label'.$fieldclass.'>'.Lang::txt('Employment Status').': '.$required."\n";
	$html .= "\t\t".'<select name="orgtype" id="orgtype">'."\n";
	if (empty($orgtype)) {
		$html .= "\t\t\t".'<option value="" selected="selected">'.Lang::txt('(select from list)').'</option>'."\n";
	}
	$html .= "\t\t\t".'<option value="nationallab"';
	if ($orgtype == 'nationallab') {
		$html .= ' selected="selected"';
	}
	$html .= '>'.Lang::txt('National Laboratory').'</option>'."\n";
	$html .= "\t\t\t".'<option value="universityundergraduate"';
	if ($orgtype == 'universityundergraduate') {
		$html .= ' selected="selected"';
	}
	$html .= '>'.Lang::txt('University / College Undergraduate').'</option>'."\n";
	$html .= "\t\t\t".'<option value="universitygraduate"';
	if ($orgtype == 'universitygraduate') {
		$html .= ' selected="selected"';
	}
	$html .= '>'.Lang::txt('University / College Graduate Student').'</option>'."\n";
	$html .= "\t\t\t".'<option value="universityfaculty"';
	if ($orgtype == 'universityfaculty' || $orgtype == 'university') {
		$html .= ' selected="selected"';
	}
	$html .= '>'.Lang::txt('University / College Faculty').'</option>'."\n";
	$html .= "\t\t\t".'<option value="universitystaff"';
	if ($orgtype == 'universitystaff') {
		$html .= ' selected="selected"';
	}
	$html .= '>'.Lang::txt('University / College Staff').'</option>'."\n";
	$html .= "\t\t\t".'<option value="precollegestudent"';
	if ($orgtype == 'precollegestudent') {
		$html .= ' selected="selected"';
	}
	$html .= '>'.Lang::txt('K-12 (Pre-College) Student').'</option>'."\n";
	$html .= "\t\t\t".'<option value="precollegefacultystaff"';
	if ($orgtype == 'precollege' || $orgtype == 'precollegefacultystaff') {
		$html .= ' selected="selected"';
	}
	$html .= '>'.Lang::txt('K-12 (Pre-College) Faculty/Staff').'</option>'."\n";
	$html .= "\t\t\t".'<option value="industry"';
	if ($orgtype == 'industry') {
		$html .= ' selected="selected"';
	}
	$html .= '>'.Lang::txt('Industry / Private Company').'</option>'."\n";
	$html .= "\t\t\t".'<option value="government"';
	if ($orgtype == 'government') {
		$html .= ' selected="selected"';
	}
	$html .= '>'.Lang::txt('Government Agency').'</option>'."\n";
	$html .= "\t\t\t".'<option value="military"';
	if ($orgtype == 'military') {
		$html .= ' selected="selected"';
	}
	$html .= '>'.Lang::txt('Military').'</option>'."\n";
	$html .= "\t\t\t".'<option value="unemployed"';
	if ($orgtype == 'unemployed') {
		$html .= ' selected="selected"';
	}
	$html .= '>'.Lang::txt('Retired / Unemployed').'</option>'."\n";
	$html .= "\t\t".'</select>'."\n";
	$html .= $message;
	$html .= "\t\t\t".'</label>'."\n";
}

if ($this->registration->Organization != REG_HIDE) {
	$required = ($this->registration->Organization == REG_REQUIRED) ? '<span class="required">'.Lang::txt('REQUIRED').'</span>' : '';
	$message = (!empty($this->xregistration->_invalid['org'])) ? '<p class="error">' . $this->xregistration->_invalid['org'] . '</p>' : '';
	$fieldclass = ($message) ? ' class="fieldWithErrors"' : '';

	$organization = stripslashes($this->profile->get('organization'));
	$orgtext = $organization;
	$org_known = 0;

	//$orgs = array();
	include_once( PATH_CORE.DS.'components'.DS.'com_members'.DS.'tables'.DS.'organization.php' );
	$database = App::get('db');

	$xo = new \Components\Members\Tables\Organization($database);
	$orgs = $xo->find('list');

	foreach ($orgs as $org)
	{
		$org_known = ($org->organization == $organization) ? 1 : 0;
	}

	$html .= "\t\t".'<label'.$fieldclass.'>'."\n";
	$html .= "\t\t\t".Lang::txt('ORG').': '.$required."\n";
	$html .= "\t\t\t".'<select name="org">'."\n";
	$html .= "\t\t\t\t".'<option value=""';
	if (!$org_known)
	{
		$html .= ' selected="selected"';
	}
	$html .= '>';
	if ($org_known)
	{
		$html .= Lang::txt('(other / none)');
	}
	else
	{
		$html .= Lang::txt('(select from list or enter below)');
	}
	$html .= '</option>'."\n";
	foreach ($orgs as $org)
	{
		$html .= "\t\t\t\t".'<option value="'. $this->escape($org->organization) .'"';
		if ($org->organization == $organization)
		{
			$orgtext = '';
			$html .= ' selected="selected"';
		}
		$html .= '>' . $this->escape($org->organization) . '</option>'."\n";
	}
	$html .= "\t\t\t".'</select>'."\n";
	$html .= $message;
	$html .= "\t\t".'</label>'."\n";
	$html .= "\t\t".'<label for="orgtext" id="orgtextlabel">'.Lang::txt('Enter organization below').'</label>'."\n";
	$html .= "\t\t".'<input type="text" name="orgtext" id="orgtext" value="'. $this->escape($orgtext) .'" />'."\n";
}

if ($this->registration->Interests != REG_HIDE) {
	$required = ($this->registration->Interests == REG_REQUIRED) ? '<span class="required">'.Lang::txt('REQUIRED').'</span>' : '';
	$message = (!empty($this->xregistration->_invalid['interests'])) ? '<p class="error">' . $this->xregistration->_invalid['interests'] . '</p>' : '';
	$fieldclass = ($message) ? ' class="fieldWithErrors"' : '';

	$tf = Event::trigger('hubzero.onGetMultiEntry', array(array('tags', 'tags', 'actags','',stripslashes($this->tags))));

	$html .= "\t\t".'<label'.$fieldclass.'>'."\n";
	$html .= "\t\t\t".Lang::txt('MEMBER_FIELD_TAGS').': '.$required."\n";
	if (count($tf) > 0) {
		$html .= $tf[0];
	} else {
		$html .= "\t\t\t".'<input type="text" name="tags" value="'. $this->tags .'" />'."\n";
	}
	$html .= "\t\t\t".'<span>'.Lang::txt('MEMBER_FIELD_TAGS_HINT').'</span>'."\n";
	$html .= $message;
	$html .= "\t\t".'</label>'."\n";
}

$html .= "\t\t".'<label for="profilebio">'."\n";
$html .= "\t\t\t".Lang::txt('BIO').':'."\n";
$html .= "\t\t\t".$this->editor('profile[bio]', $this->escape(stripslashes($this->profile->getBio('raw'))), 35, 10, 'profilebio', array('class' => 'minimal no-footer'));
$html .= "\t\t\t".'<span class="hint"><a class="popup" href="'.Route::url('index.php?option=com_wiki&scope=&pagename=Help:WikiFormatting').'">Wiki formatting</a> is allowed for Bios.</span>'."\n";
$html .= "\t\t".'</label>'."\n";
$html .= "\t".'</fieldset><div class="clear"></div>'."\n";

if ($this->registration->Citizenship != REG_HIDE || $this->registration->Residency != REG_HIDE || $this->registration->Sex != REG_HIDE || $this->registration->Disability != REG_HIDE || $this->registration->Hispanic != REG_HIDE || $this->registration->Race != REG_HIDE)
{
	$html .= "\t".'<fieldset>'."\n";
	$html .= "\t\t".'<legend>'.Lang::txt('Demographics').'</legend>'."\n";

	if ($this->registration->Citizenship != REG_HIDE || $this->registration->Residency != REG_HIDE) {
		$countries = \Hubzero\Geocode\Geocode::countries();
	}

	if ($this->registration->Citizenship != REG_HIDE) {
		$required = ($this->registration->Citizenship == REG_REQUIRED) ? ' <span class="required">'.Lang::txt('REQUIRED').'</span>' : '';
		$message = (!empty($this->xregistration->_invalid['countryorigin'])) ? '<p class="error">' . $this->xregistration->_invalid['countryorigin'] . '</p>' : '';
		$fieldclass = ($message) ? ' class="fieldsWithErrors"' : '';

		$countryorigin = strtoupper($this->profile->get('countryorigin'));

		$html .= "\t\t".'<fieldset'.$fieldclass.'>'."\n";
		$html .= "\t\t\t".'<legend>'.Lang::txt('Are you a Legal Citizen or Permanent Resident of the <abbr title="United States">US</abbr>?').$required.'</legend>'."\n";
		$html .= $message;
		$html .= "\t\t\t".'<label><input type="radio" class="option" name="corigin_us" id="corigin_usyes" value="yes"';
		if (strcasecmp($countryorigin,'US') == 0) {
			$html .= ' checked="checked"';
		}
		$html .= ' /> '.Lang::txt('Yes').'</label>'."\n";
		$html .= "\t\t\t\t".'<label><input type="radio" class="option" name="corigin_us" id="corigin_usno" value="no"';
		if (!empty($countryorigin) && (strcasecmp($countryorigin,'US') != 0)) {
			$html .= ' checked="checked"';
		}
		$html .= ' /> '.Lang::txt('No').'</label>'."\n";
		$html .= "\t\t\t\t".'<label>'.Lang::txt('Citizen or Permanent Resident of').':'."\n";
		$html .= "\t\t\t\t".'<select name="corigin" id="corigin">'."\n";
		if (!$countryorigin || $countryorigin == 'US') {
			$html .= "\t\t\t\t".' <option value="">'.Lang::txt('(select from list)').'</option>'."\n";
		}
		foreach ($countries as $country)
		{
			if ($country->code != 'US') {
				$html .= "\t\t\t\t".' <option value="' . $country->code . '"';
				if ($countryorigin == strtoupper($country->code)) {
					$html .= ' selected="selected"';
				}
				$html .= '>' . $this->escape($country->name) . '</option>'."\n";
			}
		}
		$html .= "\t\t\t\t".'</select></label>'."\n";
		$html .= "\t\t".'</fieldset>'."\n";
	}

	if ($this->registration->Residency != REG_HIDE) {
		$required = ($this->registration->Residency == REG_REQUIRED) ? ' <span class="required">'.Lang::txt('REQUIRED').'</span>' : '';
		$message = (!empty($this->xregistration->_invalid['countryresident'])) ? '<p class="error">' . $this->xregistration->_invalid['countryresident'] . '</p>' : '';
		$fieldclass = ($message) ? ' class="fieldsWithErrors"' : '';

		$countryresident = strtoupper($this->profile->get('countryresident'));

		$html .= "\t\t".'<fieldset'.$fieldclass.'>';
		$html .= "\t\t\t".'<legend>'.Lang::txt('Do you Currently Live in the <abbr title="United States">US</abbr>?').$required.'</legend>'."\n";
		$html .= $message;
		$html .= "\t\t\t".'<label><input type="radio" class="option" name="cresident_us" id="cresident_usyes" value="yes"';
		if (strcasecmp($countryresident,'US') == 0) {
			$html .= ' checked="checked"';
		}
		$html .= ' /> '.Lang::txt('Yes').'</label>'."\n";
		$html .= "\t\t\t\t".'<label><input type="radio" class="option" name="cresident_us" id="cresident_usno" value="no"';
		if (!empty($countryresident) && strcasecmp($countryresident,'US') != 0) {
			$html .= ' checked="checked"';
		}
		$html .= ' /> '.Lang::txt('No').'</label>'."\n";
		$html .= "\t\t\t\t".'<label>'.Lang::txt('Currently Living in').':'."\n";
		$html .= "\t\t\t\t".'<select name="cresident" id="cresident">'."\n";
		if (!$countryresident || strcasecmp($countryresident,'US') == 0) {
			$html .= "\t\t\t"."\t\t".' <option value="">'.Lang::txt('(select from list)').'</option>'."\n";
		}
		foreach ($countries as $country)
		{
			if (strcasecmp($country->code,"US") != 0) {
				$html .= "\t\t\t"."\t\t".'<option value="' . $country->code . '"';
				if ($countryresident == strtoupper($country->code)) {
					$html .= ' selected="selected"';
				}
				$html .= '>' . $this->escape($country->name) . '</option>'."\n";
			}
		}
		$html .= "\t\t\t\t".'</select></label>'."\n";
		$html .= "\t\t".'</fieldset>'."\n";
	}

	if ($this->registration->Sex != REG_HIDE) {
		$message = (!empty($this->xregistration->_invalid['countryresident'])) ? '<p class="error">' . $this->xregistration->_invalid['countryresident'] . '</p>' : '';
		$fieldclass = ($message) ? ' class="fieldsWithErrors"' : '';
		$required = ($this->registration->Sex == REG_REQUIRED) ? ' <span class="required">'.Lang::txt('REQUIRED').'</span>' : '';

		$html .= "\t".'<fieldset'.$fieldclass.'>'."\n";
		$html .= $message;
		$html .= "\t\t".'<legend>'.Lang::txt('Gender').':'.$required.'</legend>'."\n";
		$html .= "\t\t".'<input type="hidden" name="sex" value="unspecified" />'."\n";
		$html .= "\t\t".'<label><input type="radio" name="sex" value="male" class="option"';
		$html .= ($this->profile->get('gender') == 'male') ? ' checked="checked"' : '';
		$html .= ' /> '.Lang::txt('Male').'</label>'."\n";
		$html .= "\t\t".'<label><input type="radio" name="sex" value="female" class="option"';
		$html .= ($this->profile->get('gender') == 'female') ? ' checked="checked"' : '';
		$html .= ' /> '.Lang::txt('Female').'</label>'."\n";
		$html .= "\t\t".'<label><input type="radio" name="sex" value="refused" class="option"';
		$html .= ($this->profile->get('gender') == 'refused') ? ' checked="checked"' : '';
		$html .= ' /> '.Lang::txt('Do not wish to reveal').'</label>'."\n";
		$html .= "\t".'</fieldset>'."\n";
	}

	// Disability
	if ($this->registration->Disability != REG_HIDE) {
		$message = (!empty($this->xregistration->_invalid['disability'])) ? '<p class="error">' . $this->xregistration->_invalid['disability'] . '</p>' : '';
		$fieldclass = ($message) ? ' class="fieldsWithErrors"' : '';
		$required = ($this->registration->Disability == REG_REQUIRED) ? ' <span class="required">'.Lang::txt('REQUIRED').'</span>' : '';

		$disabilities = $this->profile->get('disability');
		if (!is_array($disabilities)) {
			$disabilities = array();
		}

		$disabilityyes = false;
		$disabilityother = '';
		foreach ($disabilities as $disabilityitem)
		{
			if ($disabilityitem != 'no'
			 && $disabilityitem != 'refused') {
				if (!$disabilityyes) {
					$disabilityyes = true;
				}

				if ($disabilityitem != 'blind'
				 && $disabilityitem != 'deaf'
				 && $disabilityitem != 'physical'
				 && $disabilityitem != 'learning'
				 && $disabilityitem != 'vocal'
				 && $disabilityitem != 'yes') {
					$disabilityother = $disabilityitem;
				}
			}
		}

		$html .= "\t\t".'<fieldset'.$fieldclass.'>'."\n";
		$html .= $message;
		$html .= "\t\t\t".'<legend>'.Lang::txt('Disability').':'.$required.'</legend>'."\n";
		$html .= "\t\t\t\t".'<label><input type="radio" class="option" name="disability" id="disabilityyes" value="yes"';
		if ($disabilityyes) {
			$html .= ' checked="checked"';
		}
		$html .= ' /> '.Lang::txt('Yes').'</label>'."\n";
		$html .= "\t\t\t".'<fieldset>'."\n";
		$html .= "\t\t\t\t".'<label><input type="checkbox" class="option" name="disabilityblind" id="disabilityblind" ';
		if (in_array('blind', $disabilities)) {
			$html .= 'checked="checked" ';
		}
		$html .= '/> '.Lang::txt('Blind / Visually Impaired').'</label>'."\n";
		$html .= "\t\t\t\t".'<label><input type="checkbox" class="option" name="disabilitydeaf" id="disabilitydeaf" ';
		if (in_array('deaf', $disabilities)) {
			$html .= 'checked="checked" ';
		}
		$html .= '/> '.Lang::txt('Deaf / Hard of Hearing').'</label>'."\n";
		$html .= "\t\t\t\t".'<label><input type="checkbox" class="option" name="disabilityphysical" id="disabilityphysical" ';
		if (in_array('physical', $disabilities)) {
			$html .= 'checked="checked" ';
		}
		$html .= '/> '.Lang::txt('Physical / Orthopedic Disability').'</label>'."\n";
		$html .= "\t\t\t\t".'<label><input type="checkbox" class="option" name="disabilitylearning" id="disabilitylearning" ';
		if (in_array('learning', $disabilities)) {
			$html .= 'checked="checked" ';
		}
		$html .= '/> '.Lang::txt('Learning / Cognitive Disability').'</label>'."\n";
		$html .= "\t\t\t\t".'<label><input type="checkbox" class="option" name="disabilityvocal" id="disabilityvocal" ';
		if (in_array('vocal', $disabilities)) {
			$html .= 'checked="checked" ';
		}
		$html .= '/> '.Lang::txt('Vocal / Speech Disability').'</label>'."\n";
		$html .= "\t\t\t\t".'<label>'.Lang::txt('Other (please specify)').':'."\n";
		$html .= "\t\t\t\t".'<input name="disabilityother" id="disabilityother" type="text" value="'. $this->escape($disabilityother) .'" /></label>'."\n";
		$html .= "\t\t\t".'</fieldset>'."\n";
		$html .= "\t\t\t".'<label><input type="radio" class="option" name="disability" id="disabilityno" value="no"';
		if (in_array('no', $disabilities)) {
			$html .= ' checked="checked"';
		}
		$html .= '> '.Lang::txt('No (none)').'</label>'."\n";
		$html .= "\t\t\t".'<label><input type="radio" class="option" name="disability" id="disabilityrefused" value="refused"';
		if (in_array('refused', $disabilities)) {
			$html .= ' checked="checked"';
		}
		$html .= '> '.Lang::txt('Do not wish to reveal').'</label>'."\n";
		$html .= "\t\t".'</fieldset>'."\n";
	}

	// Hispanic
	if ($this->registration->Hispanic != REG_HIDE) {
		$message = (!empty($this->xregistration->_invalid['hispanic'])) ? '<p class="error">' . $this->xregistration->_invalid['hispanic'] . '</p>' : '';
		$fieldclass = ($message) ? ' class="fieldsWithErrors"' : '';
		$required = ($this->registration->Hispanic == REG_REQUIRED) ? ' <span class="required">'.Lang::txt('REQUIRED').'</span>' : '';

		$hispanic = $this->profile->get('hispanic');
		if (!is_array($hispanic)) {
			$hispanic = array();
		}

		$hispanicyes = false;
		$hispanicother = '';
		foreach ($hispanic as $hispanicitem)
		{
			if ($hispanicitem != 'no'
			 && $hispanicitem != 'refused') {
				if (!$hispanicyes) {
					$hispanicyes = true;
				}

				if ($hispanicitem != 'cuban'
				 && $hispanicitem != 'mexican'
				 && $hispanicitem != 'puertorican') {
					$hispanicother = $hispanicitem;
				}
			}
		}

		$html .= "\t\t".'<fieldset'.$fieldclass.'>'."\n";
		$html .= $message;
		$html .= "\t\t\t".'<legend>'.Lang::txt('Hispanic or Latino').':'.$required.'</legend>'."\n";
		$html .= "\t\t\t\t".'<label><input type="radio" class="option" name="hispanic" id="hispanicyes" value="yes" ';
		if ($hispanicyes) {
			$html .= 'checked="checked"';
		}
		$html .= ' /> '.Lang::txt('Yes (Hispanic Origin or Descent)').'</label>'."\n";
		$html .= "\t\t\t".'<fieldset>'."\n";
		$html .= "\t\t\t\t".'<label><input type="checkbox" class="option" name="hispaniccuban" id="hispaniccuban" ';
		if (in_array('cuban', $hispanic)) {
			$html .= 'checked="checked" ';
		}
		$html .= '/> '.Lang::txt('Cuban').'</label>'."\n";
		$html .= "\t\t\t\t".'<label><input type="checkbox" class="option" name="hispanicmexican" id="hispanicmexican" ';
		if (in_array('mexican', $hispanic)) {
			$html .= 'checked="checked" ';
		}
		$html .= '/> '.Lang::txt('Mexican American or Chicano').'</label>'."\n";
		$html .= "\t\t\t\t".'<label><input type="checkbox" class="option" name="hispanicpuertorican" id="hispanicpuertorican" ';
		if (in_array('puertorican', $hispanic)) {
			$html .= 'checked="checked" ';
		}
		$html .= '/> '.Lang::txt('Puerto Rican').'</label>'."\n";
		$html .= "\t\t\t\t".'<label>'.Lang::txt('Other Hispanic or Latino').':'."\n";
		$html .= "\t\t\t\t".'<input name="profile[hispanic][other]" id="hispanicother" type="text" value="'. $this->escape($hispanicother) .'" /></label>'."\n";
		$html .= "\t\t\t".'</fieldset>'."\n";
		$html .= "\t\t\t".'<label><input type="radio" class="option" name="hispanic" id="hispanicno" value="no"';
		if (in_array('no', $hispanic)) {
			$html .= ' checked="checked"';
		}
		$html .= '> '.Lang::txt('No (not Hispanic or Latino)').'</label>'."\n";
		$html .= "\t\t\t".'<label><input type="radio" class="option" name="hispanic" id="hispanicrefused" value="refused"';
		if (in_array('refused', $hispanic)) {
			$html .= ' checked="checked"';
		}
		$html .= '> '.Lang::txt('Do not wish to reveal').'</label>'."\n";
		$html .= "\t\t".'</fieldset>'."\n";
	}

	// Race
	if ($this->registration->Race != REG_HIDE) {
		$message = (!empty($this->xregistration->_invalid['race'])) ? '<p class="error">' . $this->xregistration->_invalid['race'] . '</p>' : '';
		$fieldclass = ($message) ? ' class="fieldsWithErrors"' : '';
		$required = ($this->registration->Race == REG_REQUIRED) ? ' <span class="required">'.Lang::txt('REQUIRED').'</span>' : '';

		$race = $this->profile->get('race');
		if (!is_array($race)) {
			$race = array();
		}

		$html .= "\t\t".'<fieldset'.$fieldclass.'>'."\n";
		$html .= $message;
		$html .= "\t\t\t".'<legend>'.Lang::txt('Racial Background').':'.$required.'</legend>'."\n";
		$html .= "\t\t\t".'<p class="hint">'.Lang::txt('Select one or more that apply.').'</p>'."\n";

		$html .= "\t\t\t".'<label><input type="checkbox" class="option" name="racenativeamerican" id="racenativeamerican" value="nativeamerican" ';
		if (in_array('nativeamerican', $race)) {
			$html .= 'checked="checked" ';
		}
		$html .= '/> '.Lang::txt('American Indian or Alaska Native').'</label>'."\n";
		$html .= "\t\t\t".'<label class="indent">'.Lang::txt('Tribal Affiliation(s)').':'."\n";
		$html .= "\t\t\t".'<input name="profile[nativetribe]" id="racenativetribe" type="text" value="'. $this->escape($this->profile->get('nativeTribe')) .'" /></label>'."\n";
		$html .= "\t\t\t".'<label><input type="checkbox" class="option" name="raceasian" id="raceasian" ';
		if (in_array('asian', $race)) {
			$html .= 'checked="checked" ';
		}
		$html .= '/> '.Lang::txt('Asian').'</label>'."\n";
		$html .= "\t\t\t".'<label><input type="checkbox" class="option" name="raceblack" id="raceblack" ';
		if (in_array('black', $race)) {
			$html .= 'checked="checked" ';
		}
		$html .= '/> '.Lang::txt('Black or African American').'</label>'."\n";
		$html .= "\t\t\t".'<label><input type="checkbox" class="option" name="racehawaiian" id="racehawaiian" ';
		if (in_array('hawaiian', $race)) {
			$html .= 'checked="checked" ';
		}
		$html .= '/> '.Lang::txt('Native Hawaiian or Other Pacific Islander').'</label>'."\n";
		$html .= "\t\t\t".'<label><input type="checkbox" class="option" name="racewhite" id="racewhite" ';
		if (in_array('white', $race)) {
			$html .= 'checked="checked" ';
		}
		$html .= '/> '.Lang::txt('White').'</label>'."\n";
		$html .= "\t\t\t".'<label><input type="checkbox" class="option" name="racerefused" id="racerefused" ';
		if (in_array('refused', $race)) {
			$html .= 'checked="checked" ';
		}
		$html .= '/> '.Lang::txt('Do not wish to reveal').'</label>'."\n";
		$html .= "\t\t".'</fieldset>'."\n";
	}

	$html .= "\t".'</fieldset><div class="clear"></div>'."\n";
}

if ($this->registration->OptIn != REG_HIDE) // newsletter Opt-In
{
	$required = ($this->registration->OptIn == REG_REQUIRED) ? '<span class="required">'.Lang::txt('required').'</span>' : '';
	$message = (!empty($this->xregistration->_invalid['mailPreferenceOption'])) ? '<p class="error">' . $this->xregistration->_invalid['mailPreferenceOption'] . '</p>' : '';
	$fieldclass = ($message) ? ' class="fieldWithErrors"' : '';

	$html .= "\t".'<fieldset>'."\n";
	$html .= "\t\t".'<legend>'.Lang::txt('Updates').'</legend>'."\n";
	$html .= "\t\t".'<input type="hidden" name="mailPreferenceOption" value="unset" />'."\n";
	$html .= "\t\t".'<label '.$fieldclass.'><input type="checkbox" class="option" id="mailPreferenceOption" name="mailPreferenceOption" value="1" ';
	if ($this->profile->get('mailPreferenceOption')) {
		$html .= 'checked="checked" ';
	}
	$html .= '/> '.$required.' '.Lang::txt('Yes, I would like to receive newsletters and other updates by e-mail.').'</label>'."\n";
	$html .= $message;
	$html .= "\t".'</fieldset><div class="clear"></div>'."\n";
}

$html .= "\t".'<fieldset>'."\n";
$html .= "<a name=\"memberpicture\"></a>";
$html .= "\t\t".'<legend>'.Lang::txt('MEMBER_PICTURE').'</legend>'."\n";
$html .= "\t\t".'<iframe width="100%" height="350" border="0" name="filer" id="filer" src="index.php?option='.$this->option.'&amp;controller=media&amp;tmpl=component&amp;file='.stripslashes($this->profile->get('picture')).'&amp;id='.$this->profile->get('id').'"></iframe>'."\n";
$html .= "\t".'</fieldset><div class="clear"></div>'."\n";
$html .= Html::input('token');
$html .= "\t".'<p class="submit"><input class="btn btn-success" type="submit" name="submit" value="'.Lang::txt('SAVE').'" /></p>'."\n";
$html .= '</form>'."\n";
$html .= '</section>'."\n";

echo $html;
