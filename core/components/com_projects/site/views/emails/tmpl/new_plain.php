<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$base = rtrim(Request::base(), '/');
$base = rtrim(str_replace('/administrator', '', $base), '/');

$sef 		= Route::url('index.php?option=' . $this->option . '&alias=' . $this->project->get('alias'));
$sef_browse = Route::url('index.php?option=' . $this->option . '&task=browse');

$link = rtrim($base, DS) . DS . trim($sef, DS);
$browseLink = rtrim($base, DS) . DS . trim($sef_browse, DS);

$message  = $this->project->owner('name') . ' ' .Lang::txt('COM_PROJECTS_EMAIL_STARTED_NEW_PROJECT');
$message .= ' "' . $this->project->get('title') . '"' . "\n";
$message .= '-------------------------------' . "\n";
$message .= Lang::txt('COM_PROJECTS_PROJECT') . ': ' . $this->project->get('title') . ' (' . $this->project->get('alias') . ')' . "\n";
$message .= ucfirst(Lang::txt('COM_PROJECTS_CREATED')) . ' '
		 . Date::of($this->project->get('created'))->format('M d, Y') . ' '
		 . Lang::txt('COM_PROJECTS_BY') . ' ';
$message .= $this->project->groupOwner()
			 ? $this->project->groupOwner('cn') . ' ' . Lang::txt('COM_PROJECTS_GROUP')
			 : $this->project->owner('name');
$message .= "\n";

if ($this->project->isPublic())
{
	$message .= Lang::txt('COM_PROJECTS_EMAIL_URL') . ': ' . $link . "\n";
}
$message .= '-------------------------------' . "\n\n";
$message .= Lang::txt('COM_PROJECTS_EMAIL_PRIVACY') . ': ';
$message .= !$this->project->isPublic()
			? Lang::txt('COM_PROJECTS_EMAIL_PRIVATE') . "\n"
			: Lang::txt('COM_PROJECTS_EMAIL_PUBLIC') . "\n";

if ($this->project->config()->get('restricted_data', 0))
{
	$message .= Lang::txt('COM_PROJECTS_EMAIL_HIPAA') . ': ' . $this->project->params->get('hipaa_data') . "\n";
	$message .= Lang::txt('COM_PROJECTS_EMAIL_FERPA') . ': ' . $this->project->params->get('ferpa_data') . "\n";
	$message .= Lang::txt('COM_PROJECTS_EMAIL_EXPORT') . ': ' . $this->project->params->get('export_data') . "\n";
	if ($this->project->params->get('followup'))
	{
		$message .= Lang::txt('COM_PROJECTS_EMAIL_FOLLOWUP_NEEDED') . ': ' . $this->project->params->get('followup') . "\n";
	}
	$message .= '-------------------------------' . "\n\n";
}
if ($this->project->config()->get('grantinfo', 0))
{
	$message .= Lang::txt('COM_PROJECTS_EMAIL_GRANT_TITLE') . ': ' . $this->project->params->get('grant_title') . "\n";
	$message .= Lang::txt('COM_PROJECTS_EMAIL_GRANT_PI') . ': ' . $this->project->params->get('grant_PI') . "\n";
	$message .= Lang::txt('COM_PROJECTS_EMAIL_GRANT_AGENCY') . ': ' . $this->project->params->get('grant_agency') . "\n";
	$message .= Lang::txt('COM_PROJECTS_EMAIL_GRANT_BUDGET') . ': ' . $this->project->params->get('grant_budget') . "\n";
}
$message .= '-------------------------------' . "\n\n";

if ($this->project->config()->get('ginfo_group', 0))
{
	$message .= Lang::txt('COM_PROJECTS_EMAIL_LINK_SPS') . "\n";
	$message .= $browseLink . '?reviewer=sponsored' . "\n\n";
}

if ($this->project->config()->get('sdata_group', 0))
{
	$message .= Lang::txt('COM_PROJECTS_EMAIL_LINK_HIPAA') . "\n";
	$message .= $browseLink . '?reviewer=sensitive' . "\n";
}

$message = str_replace('<br />', '', $message);
$message = preg_replace('/\n{3,}/', "\n\n", $message);

echo $message;
