<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 * All rights reserved.
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

// No direct access.
defined('_HZEXEC_') or die();

$database = App::get('db');

// Instantiate a helper object
$helper = new \Components\Resources\Helpers\Helper($this->line->id, $database);
$helper->getContributors();
$helper->getContributorIDs();

/*
// Determine if they have access to edit
if (!Config::isGuest())
{
	if ((!$this->show_edit && $this->line->created_by == Config::get('id'))
	 || in_array(Config::get('id'), $helper->contributorIDs))
	{
		$this->show_edit = 2;
	}
}
*/

// Get parameters
$params = clone($this->config);
$rparams = new \Hubzero\Config\Registry($this->line->params);
$params->merge($rparams);

if (!$this->line->modified || $this->line->modified == '0000-00-00 00:00:00')
{
	$this->line->modified = $this->line->created;
}
if (!$this->line->publish_up || $this->line->publish_up == '0000-00-00 00:00:00')
{
	$this->line->publish_up = $this->line->created;
}

// Set the display date
switch ($params->get('show_date'))
{
	case 0: $thedate = ''; break;
	case 1: $thedate = Date::of($this->line->created)->toLocal(Lang::txt('DATE_FORMAT_HZ1'));    break;
	case 2: $thedate = Date::of($this->line->modified)->toLocal(Lang::txt('DATE_FORMAT_HZ1'));   break;
	case 3: $thedate = Date::of($this->line->publish_up)->toLocal(Lang::txt('DATE_FORMAT_HZ1')); break;
}

switch ($this->line->access)
{
	case 1: $cls = 'registered'; break;
	case 2: $cls = 'special';    break;
	case 3: $cls = 'protected';  break;
	case 4: $cls = 'private';    break;
	case 0:
	default: $cls = 'public';    break;
}

if ($this->config->get('supportedtag') && isset($this->supported))
{
	if (in_array($this->line->id, $this->supported))
	{
		$cls .= ' supported';
	}
}
?>

<li class="<?php echo $cls; ?>">
	<p class="title">
		<a href="<?php echo Route::url('index.php?option=' . $this->option . '&' . ($this->line->alias ? 'alias=' . $this->line->alias : 'id=' . $this->line->id)); ?>">
			<?php echo $this->escape(stripslashes($this->line->title)); ?>
		</a>
		<?php /*if ($this->show_edit != 0) {
			if ($this->line->published >= 0) {
				if ($this->line->type == 7) {
					$link = Route::url('index.php?option=com_tools&task=resource&step=1&app='. $this->line->alias);
				} else {
					$link = Route::url('index.php?option=com_resources&task=draft&step=1&id='. $this->line->id);
				}
				$html .= ' <a class="edit button" href="'. $link .'" title="'. Lang::txt('COM_RESOURCES_EDIT') .'">'. Lang::txt('COM_RESOURCES_EDIT') .'</a>';
			}
		}*/ ?>
	</p>

<?php if ($params->get('show_ranking')) { ?>
	<div class="metadata">
		<dl class="rankinfo">
			<dt class="ranking">
				<?php
				//$database = App::get('db');

				// Get statistics info
				$helper->getCitationsCount();
				$helper->getLastCitationDate();

				$this->line->ranking = round($this->line->ranking, 1);

				$r = (10 * $this->line->ranking);
				?>
				<span class="rank">
					<span class="rank-<?php echo $r; ?>" style="width: <?php echo $r; ?>%;"><?php echo Lang::txt('COM_RESOURCES_THIS_HAS'); ?></span>
				</span>
				<?php echo number_format($this->line->ranking, 1) . ' ' . Lang::txt('COM_RESOURCES_RANKING'); ?>
			</dt>
			<dd>
				<p><?php echo Lang::txt('COM_RESOURCES_RANKING_EXPLANATION'); ?></p>
				<div>
					<?php
					if ($this->line->type == 7)
					{
						$stats = new \Components\Resources\Helpers\Usage\Tools($database, $this->line->id, $this->line->type, $this->line->rating, $helper->citationsCount, $helper->lastCitationDate);
					}
					else
					{
						$stats = new \Components\Resources\Helpers\Usage\Andmore($database, $this->line->id, $this->line->type, $this->line->rating, $helper->citationsCount, $helper->lastCitationDate);
					}
					echo $stats->display();
					?>
				</div>
			</dd>
		</dl>
	</div>
<?php } elseif ($params->get('show_rating')) { ?>
	<?php
	switch ($this->line->rating)
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
		default:  $class = ' no-stars';        break;
	}
	?>
	<div class="metadata">
		<p class="rating">
			<span title="<?php echo Lang::txt('COM_RESOURCES_OUT_OF_5_STARS', $this->line->rating); ?>" class="avgrating<?php echo $class; ?>">
				<span><?php echo Lang::txt('COM_RESOURCES_OUT_OF_5_STARS', $this->line->rating); ?></span>&nbsp;
			</span>
		</p>
	</div>
<?php } ?>
	<p class="details">
		<?php
		$info = array();
		if ($thedate)
		{
			$info[] = $thedate;
		}
		if (($this->line->type && $params->get('show_type')) || $this->line->standalone == 1)
		{
			$info[] = stripslashes($this->line->typetitle);
		}
		if ($helper->contributors && $params->get('show_authors'))
		{
			$info[] = Lang::txt('COM_RESOURCES_CONTRIBUTORS') . ': ' . $helper->contributors;
		}
		echo implode(' <span>|</span> ', $info);
		?>
	</p>
	<p>
		<?php
		$content = '';
		if ($this->line->introtext)
		{
			$content = $this->line->introtext;
		}
		else if ($this->line->fulltxt)
		{
			$content = $this->line->fulltxt;
			$content = preg_replace("#<nb:(.*?)>(.*?)</nb:(.*?)>#s", '', $content);
			$content = trim($content);
		}

		echo \Hubzero\Utility\String::truncate(strip_tags(\Hubzero\Utility\Sanitize::stripAll(stripslashes($content))), 300);
		?>
	</p>
</li>