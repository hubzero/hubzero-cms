<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

$base = trim(preg_replace('/\/administrator/', '', Request::base()), '/');
$sef  = Route::url($this->project->link());
$link = rtrim($base, '/') . '/' . trim($sef, '/');

$message  = $this->subject . "\n";
$message .= '===============================' . "\n";
$message .= Lang::txt('COM_PROJECTS_PROJECT') . ': ' . $this->project->get('title') . ' (' . $this->project->get('alias') . ')' . "\n";
$message .= Lang::txt('COM_PROJECTS_EMAIL_URL') . ': ' . $link . "\n";
$message .= '===============================' . "\n\n";

if (empty($this->activities))
{
	$message .= Lang::txt('There has been no activity in this project.');
}
else
{
	foreach ($this->activities as $a)
	{
		$body = $a->log->get('description');

		$isHtml = false;
		if (preg_match('/^(<([a-z]+)[^>]*>.+<\/([a-z]+)[^>]*>|<(\?|%|([a-z]+)[^>]*).*(\?|%|)>)/is', $body))
		{
			$body = preg_replace('/<br\s?\/?>/ius', "\n", trim($body));
		}

		$creator = User::getInstance($a->log->get('created_by'));
		$name = $creator->get('name');

		$message .= Date::of($a->created)->toLocal(Lang::txt('DATE_FORMAT_HZ1')) . ' &#64 ' .  Date::of($a->created)->toLocal(Lang::txt('TIME_FORMAT_HZ1')) . "\n";
		$message .= $name;
		$message .= ' ' . $a->action;
		$message .= $body ? ':' : '';
		$message .= "\n";
		if ($body)
		{
			$message .= $body . "\n";
		}
		$message .= '-------------------------------' . "\n";
	}
}
echo $message;
?>

This email was sent to you on behalf of <?php echo Request::root(); ?> because you are subscribed
to watch this project. To unsubscribe, go to <?php echo $link; ?>.