<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Jobs\Helpers;

/**
 * Jobs helper class for misc. HTML
 */
class Html
{
	/**
	 * Remove paragraph tags and break tags
	 *
	 * @param   string  $pee  Text to unparagraph
	 * @return  string
	 */
	public static function txt_unpee($pee)
	{
		$pee = str_replace("\t", '', $pee);
		$pee = str_replace('</p><p>', '', $pee);
		$pee = str_replace('<p>', '', $pee);
		$pee = str_replace('</p>', "\n", $pee);
		$pee = str_replace('<br />', '', $pee);
		$pee = trim($pee);
		return $pee;
	}

	/**
	 * Return a confirmation screen
	 *
	 * @param   string  $returnurl  URL to return to if they press 'no'
	 * @param   string  $actionurl  URL to go to if they press 'yes'
	 * @param   string  $action     Action the confirmation is for
	 * @return  string  HTML
	 */
	public static function confirmscreen($returnurl, $actionurl, $action = 'cancelsubscription')
	{
		?>
		<div class="confirmwrap">
			<div class="confirmscreen">
				<p class="warning"><?php echo Lang::txt('COM_JOBS_CONFIRM_ARE_YOU_SURE') . " ";
					if ($action == 'cancelsubscription')
					{
						echo strtolower(Lang::txt('COM_JOBSSUBSCRIPTION_CANCEL_THIS'));
					}
					else if ($action == 'withdrawapp')
					{
						echo Lang::txt('COM_JOBS_APPLICATION_WITHDRAW');
					}
					else
					{
						echo Lang::txt('COM_JOBS_ACTION_PERFORM_THIS');
					}
					$yes  = strtoupper(Lang::txt('YES'));
					$yes .= $action == 'cancelsubscription' ? ', ' . Lang::txt('COM_JOBS_ACTION_CANCEL_IT') : '';
					$yes .= $action == 'withdrawapp' ? ', ' . Lang::txt('COM_JOBS_ACTION_WITHDRAW')  : '';
					$no  = strtoupper(Lang::txt('NO'));
					$no .= $action == 'cancelsubscription' ? ', ' . Lang::txt('COM_JOBS_ACTION_DO_NOT_CANCEL')   : '';
					$no .= $action == 'withdrawapp' ? ', ' . Lang::txt('COM_JOBS_ACTION_DO_NOT_WITHDRAW') : ''; ?>
				</p>
				<p><span class="yes"><a href="<?php echo $actionurl ?>"><?php echo $yes ?></a></span> <span class="no"><a href="<?php echo $returnurl ?>"><?php echo $no ?></a></span></p>
			</div>
		</div>
		<?php
	}

	/**
	 * Generate a select form
	 *
	 * @param   string  $name   Field name
	 * @param   array   $array  Data to populate select with
	 * @param   mixed   $value  Value to select
	 * @param   string  $class  Class to add
	 * @return  string  HTML
	 */
	public static function formSelect($name, $array, $value, $class='')
	{
		$out  = '<select name="' . $name . '" id="' . $name . '"';
		$out .= ($class) ? ' class="' . $class . '">' . "\n" : '>' . "\n";
		foreach ($array as $avalue => $alabel)
		{
			$selected = ($avalue == $value || $alabel == $value)
					  ? ' selected="selected"'
					  : '';
			$out .= ' <option value="' . $avalue . '"' . $selected . '>' . $alabel . '</option>' . "\n";
		}
		$out .= '</select>' . "\n";
		return $out;
	}
}
