<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// no direct access
defined('_HZEXEC_') or die();

if ($this->publish)
{
	$this->css()
	     ->js();
	?>
	<div id="<?php echo $this->moduleid; ?>" class="modnotices <?php echo $this->alertlevel; ?>">
		<div class="inner">
			<div class="notice">
				<?php echo stripslashes($this->message); ?>
				<?php
				$page = Request::getString('REQUEST_URI', '', 'server');
				if ($page && $this->params->get('allowClose', 1))
				{
					$page .= (strstr($page, '?')) ? '&' : '?';
					$page .= $this->moduleid . '=close';
					?>
					
					<?php
				}
				?>
			</div>
			<a class="close" href="<?php echo $page; ?>" data-duration="<?php echo $this->days_left; ?>" title="<?php echo Lang::txt('MOD_NOTICES_CLOSE_TITLE'); ?>">
				<span><?php echo Lang::txt('MOD_NOTICES_CLOSE'); ?></span>
			</a>
		</div>
	</div>
	<?php
}
