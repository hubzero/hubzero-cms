<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// no direct access
defined('_HZEXEC_') or die();

// Push the module CSS to the template
$this->css();
?>
<div<?php echo ($this->params->get('moduleclass')) ? ' class="' . $this->params->get('moduleclass') . '"' : ''; ?>>
	<h3><?php echo Lang::txt('MOD_WISHVOTERS_GIVING_MOST_INPUT'); ?></h3>
<?php if (count($this->rows) <= 0) { ?>
	<p><?php echo Lang::txt('MOD_WISHVOTERS_NO_VOTES'); ?></p>
<?php } else { ?>
	<ul class="voterslist">
		<li class="title">
			<?php echo Lang::txt('MOD_WISHVOTERS_COL_NAME'); ?>
			<span><?php echo Lang::txt('MOD_WISHVOTERS_COL_RANKED'); ?></span>
		</li>
		<?php
			$k=1;
			foreach ($this->rows as $row)
			{
				if ($k <= intval($this->params->get('limit', 10)))
				{
					$name = Lang::txt('MOD_WISHVOTERS_UNKNOWN');
					$auser = User::getInstance($row->userid);
					if (is_object($auser))
					{
						$name  = $auser->get('name');
						$login = $auser->get('username');
					}
					?>
					<li>
						<span class="lnum"><?php echo $k; ?>.</span>
						<?php echo stripslashes($name); ?>
						<span class="wlogin">(<?php echo stripslashes($login); ?>)</span>
						<span><?php echo $row->times; ?></span>
					</li>
					<?php
					$k++;
				}
			}
		?>
	</ul>
<?php } ?>
</div><!-- / <?php echo ($this->params->get('moduleclass')) ? '.' . $this->params->get('moduleclass') : ''; ?> -->