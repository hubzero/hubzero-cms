<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * This file is part of: The HUBzero(R) Platform for Scientific Collaboration
 *
 * The HUBzero(R) Platform for Scientific Collaboration (HUBzero) is free
 * software: you can redistribute it and/or modify it under the terms of
 * the GNU Lesser General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or (at your option) any
 * later version.
 *
 * HUBzero is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// No direct access
defined('_HZEXEC_') or die();

include_once(PATH_CORE . DS . 'components' . DS . 'com_resources' . DS . 'helpers' . DS . 'helper.php');
include_once(PATH_CORE . DS . 'components' . DS . 'com_resources' . DS . 'helpers' . DS . 'usage.php');

$database = App::get('db');

// Instantiate a helper object
$RE = new \Components\Resources\Helpers\Helper($this->row->id, $database);
$RE->getContributors();

// Get the component params and merge with resource params
$config = Component::params('com_resources');

$rparams = new \Hubzero\Config\Registry($this->row->params);
$params = $config;
$params->merge($rparams);

// Set the display date
switch ($params->get('show_date'))
{
	case 0: $thedate = ''; break;
	case 1: $thedate = Date::of($this->row->created)->toLocal('d M Y');    break;
	case 2: $thedate = Date::of($this->row->modified)->toLocal('d M Y');   break;
	case 3: $thedate = Date::of($this->row->publish_up)->toLocal('d M Y'); break;
}

if (strstr($this->row->href, 'index.php'))
{
	$this->row->href = Route::url($this->row->href);
}

switch ($this->row->access)
{
	case 1: $cls = 'registered'; break;
	case 2: $cls = 'special';    break;
	case 3: $cls = 'protected';  break;
	case 4: $cls = 'private';    break;
	case 0:
	default: $cls = 'public'; break;
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
						$RE->getCitationsCount();
						$RE->getLastCitationDate();

						if ($this->row->category == 7)
						{
							$stats = new \Components\Resources\Helpers\Usage\Tools($database, $this->row->id, $this->row->category, $this->row->rating, $RE->citationsCount, $RE->lastCitationDate);
						}
						else
						{
							$stats = new \Components\Resources\Helpers\Usage\Andmore($database, $this->row->id, $this->row->category, $this->row->rating, $RE->citationsCount, $RE->lastCitationDate);
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
			case 0.5: $class = ' half-stars';      break;
			case 1:   $class = ' one-stars';       break;
			case 1.5: $class = ' onehalf-stars';   break;
			case 2:   $class = ' two-stars';       break;
			case 2.5: $class = ' twohalf-stars';   break;
			case 3:   $class = ' three-stars';     break;
			case 3.5: $class = ' threehalf-stars'; break;
			case 4:   $class = ' four-stars';      break;
			case 4.5: $class = ' fourhalf-stars';  break;
			case 5:   $class = ' five-stars';      break;
			case 0:
			default:  $class = ' no-stars';      break;
		}
		?>
		<div class="metadata">
			<p class="rating"><span class="avgrating<?php echo $class; ?>"><span><?php echo Lang::txt('PLG_GROUPS_RESOURCES_OUT_OF_5_STARS', $this->row->rating); ?></span>&nbsp;</span></p>
		</div>
	<?php } ?>

	<p class="details">
		<?php echo $thedate; ?> <span>|</span> <?php echo stripslashes($this->row->area); ?>
		<?php if ($RE->contributors) { ?>
			<span>|</span> <?php echo Lang::txt('PLG_GROUPS_RESOURCES_CONTRIBUTORS') . ': ' . $RE->contributors; ?>
		<?php } ?>
	</p>

	<?php
	$text = $this->row->ftext;
	if ($this->row->itext)
	{
		$text = $this->row->itext;
	}
	$text = strip_tags($text);
	echo \Hubzero\Utility\String::truncate(\Hubzero\Utility\Sanitize::clean(stripslashes($text)), 200) . "\n";
	?>

	<p class="href"><?php echo Request::base() . ltrim($this->row->href, '/'); ?></p>
</li>