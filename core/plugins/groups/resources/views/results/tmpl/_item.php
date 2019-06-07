<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

include_once Component::path('com_resources') . DS . 'helpers' . DS . 'usage.php';

$params = $this->row->params;

switch ($this->row->access)
{
	case 1:
		$cls = 'registered';
		break;
	case 2:
		$cls = 'special';
		break;
	case 3:
		$cls = 'protected';
		break;
	case 4:
		$cls = 'private';
		break;
	case 0:
	default:
		$cls = 'public';
		break;
}
?>

<li class="<?php echo $cls; ?> resource">
	<p class="title"><a href="<?php echo $this->row->href; ?>"><?php echo $this->escape(stripslashes($this->row->title)); ?></a></p>

	<?php if ($params->get('show_ranking')) { ?>
		<?php
		$this->row->ranking = round($this->row->ranking, 1);

		$r = (10*$this->row->ranking);
		if (intval($r) < 10)
		{
			$r = '0' . $r;
		}
		?>
		<div class="metadata">
			<dl class="rankinfo">
				<dt class="ranking"><span class="rank-<?php echo $r; ?>"><?php echo Lang::txt('PLG_GROUPS_RESOURCES_THIS_HAS'); ?></span> <?php echo number_format($this->row->ranking, 1) . ' ' . Lang::txt('PLG_GROUPS_RESOURCES_RANKING'); ?></dt>
				<dd>
					<p><?php echo Lang::txt('PLG_GROUPS_RESOURCES_RANKING_EXPLANATION'); ?></p>
					<div>
						<?php
						$database = App::get('db');

						if ($this->row->isTool())
						{
							$stats = new \Components\Resources\Helpers\Usage\Tools($database, $this->row->id, $this->row->category, $this->row->rating);
						}
						else
						{
							$stats = new \Components\Resources\Helpers\Usage\Andmore($database, $this->row->id, $this->row->category, $this->row->rating);
						}
						echo $stats->display();
						?>
					</div>
				</dd>
			</dl>
		</div>
	<?php } elseif ($params->get('show_rating')) { ?>
		<?php
		switch ($this->row->rating)
		{
			case 0.5:
				$class = ' half-stars';
				break;
			case 1:
				$class = ' one-stars';
				break;
			case 1.5:
				$class = ' onehalf-stars';
				break;
			case 2:
				$class = ' two-stars';
				break;
			case 2.5:
				$class = ' twohalf-stars';
				break;
			case 3:
				$class = ' three-stars';
				break;
			case 3.5:
				$class = ' threehalf-stars';
				break;
			case 4:
				$class = ' four-stars';
				break;
			case 4.5:
				$class = ' fourhalf-stars';
				break;
			case 5:
				$class = ' five-stars';
				break;
			case 0:
			default:
				$class = ' no-stars';
				break;
		}
		?>
		<div class="metadata">
			<p class="rating"><span class="avgrating<?php echo $class; ?>"><span><?php echo Lang::txt('PLG_GROUPS_RESOURCES_OUT_OF_5_STARS', $this->row->rating); ?></span>&nbsp;</span></p>
		</div>
	<?php } ?>

	<p class="details">
		<?php echo $this->row->date; ?> <span>|</span> <?php echo stripslashes($this->row->type->get('type')); ?>
		<?php if ($authors = $this->row->authorsList()) { ?>
			<span>|</span> <?php echo Lang::txt('PLG_GROUPS_RESOURCES_CONTRIBUTORS') . ': ' . $authors; ?>
		<?php } ?>
	</p>

	<?php
	$text = $this->row->ftext;
	if ($this->row->itext)
	{
		$text = $this->row->itext;
	}
	$text = strip_tags($text);
	echo \Hubzero\Utility\Str::truncate(\Hubzero\Utility\Sanitize::clean(stripslashes($text)), 200) . "\n";
	?>

	<p class="href"><?php echo Request::base() . ltrim($this->row->href, '/'); ?></p>
</li>
