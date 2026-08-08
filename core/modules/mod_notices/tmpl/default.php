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
				<div class="inner">
					<?php echo stripslashes($this->message); ?>
				</div>
			</div>
			<?php
			$page = Request::getString('REQUEST_URI', '', 'server');
			if ($page && $this->params->get('allowClose', 1))
			{
				// Strip any existing close parameter before appending.
				// REQUEST_URI is whatever the client asked for, so a page
				// reached via this very link already carries the param --
				// blindly appending produced ?sitenotice=close&
				// sitenotice=close&... growing by one on every render.
				// Observed live up to 11 copies, 6,175 requests, and it
				// mints a distinct crawlable URL for every page on the
				// site.
				$bits = explode('?', $page, 2);
				$path = $bits[0];
				$qs   = isset($bits[1]) ? $bits[1] : '';
				if ($qs !== '')
				{
					$keep = array();
					foreach (explode('&', $qs) as $pair)
					{
						if ($pair === '')
						{
							continue;
						}
						$nv   = explode('=', $pair, 2);
						$name = $nv[0];
						// 'close' as a bare key shows up in the wild too.
						if ($name === $this->moduleid || $name === 'close')
						{
							continue;
						}
						$keep[] = $pair;
					}
					$qs = implode('&', $keep);
				}
				$page  = $path . ($qs !== '' ? '?' . $qs : '');
				$page .= (strstr($page, '?')) ? '&' : '?';
				$page .= $this->moduleid . '=close';
                                $page = htmlspecialchars($page,ENT_COMPAT, 'UTF-8');
				?>
				<a class="close" rel="nofollow" href="<?php echo $page; ?>" data-duration="<?php echo $this->days_left; ?>" title="<?php echo Lang::txt('MOD_NOTICES_CLOSE_TITLE'); ?>">
					<span><?php echo Lang::txt('MOD_NOTICES_CLOSE'); ?></span>
				</a>
				<?php
			}
			?>
		</div>
	</div>
	<?php
}
