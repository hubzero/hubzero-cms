<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();
?>
This is a reminder, sent out once a month, about your <?php echo $this->sitename; ?>
support tickets.  It includes a list of tickets, highest to
lowest priority, that need to be acted upon.

#   (created)   ::   Link    ::    Summary
------------------------------------------

<?php
foreach ($this->severities as $severity => $tickets)
{
	if (count($tickets) <= 0)
	{
		continue;
	}
	$msg .= '=== ' . $severity . ' ===' . "\n";
	foreach ($tickets as $ticket)
	{
		$sef = Route::url('index.php?option=com_support&controller=tickets&task=ticket&id='. $ticket->id);

		$msg .= '#' . $ticket->id . ' (' . $ticket->created . ') :: ' . Request::base() . ltrim($sef, DS) . ' :: ' . stripslashes($ticket->summary) . "\n";
	}
}
