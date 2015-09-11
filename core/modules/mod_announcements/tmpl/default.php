<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
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
 * @author    Alissa Nedossekina <alisa@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

defined('_HZEXEC_') or die;

$morelink = count($this->content) > 0 ? $this->content[0]->catpath : 'announcements';
$morelink = $this->params->get('show_viewall', '') ? $morelink : '';
$subscribelink = $this->params->get('show_subscribe', '') &&  $this->params->get('subscribe_path', '') ?  $this->params->get('subscribe_path', '') : '';

?>
<?php if ($morelink or $subscribelink) { ?>
<p class="sublinks">
	<?php if ($morelink) { ?><a href="<?php echo $morelink;  ?>"><?php echo Lang::txt('MOD_ANNOUNCEMENTS_VIEW_ALL'); ?></a><?php } ?>
	<?php if ($morelink && $subscribelink) { ?> <span>|</span> <?php } ?>
	<?php if ($subscribelink) { ?><a href="<?php echo $subscribelink;  ?>" class="add"><?php echo $this->params->get('subscribe_label', Lang::txt('MOD_ANNOUNCEMENTS_SUBSCRIBE')); ?></a><?php } ?>
</p>
<?php } ?>

<div id="<?php echo $this->container; ?>">
<?php if ($this->params->get('show_search', '')) { ?>
	<form action="<?php echo Route::url('index.php?option=com_search'); ?>" method="get" class="search">
		<fieldset>
			<p>
				<input type="text" name="terms" value="" />
				<input type="hidden" name="section" value="content:announcements" />
				<input type="submit" value="<?php echo Lang::txt('MOD_ANNOUNCEMENTS_SEARCH'); ?>" />
			</p>
		</fieldset>
	</form>
<?php } ?>

<?php if (count($this->content) > 0) { ?>
	<ul>
		<?php
		foreach ($this->content as $item)
		{
			$url = DS . $item->catpath . DS . $item->alias;

			// get associated image
			preg_match('/<img\s+.*?src="(.*?)"/is', $item->introtext , $match);
			$img = count($match) > 1
			     ? trim(stripslashes($match[1]))
			     : $this->params->get('default_image', 'modules/mod_announcements/assets/img/default.gif');
		?>
		<li>
		<?php if ($this->params->get('show_image', '')) { ?>
			<img src="<?php echo $img; ?>" alt="<?php echo $this->escape(stripslashes($item->title)); ?>" />
		<?php } ?>
			<span class="a-content">
				<span class="a-title">
					<a href="<?php echo $url; ?>">
						<?php echo $this->escape(stripslashes($item->title)); ?>
					</a>
				</span>
			<?php if ($this->params->get('show_date', '')) { ?>
				<span class="a-date">
					<?php echo Date::of($item->publish_up)->toLocal(Lang::txt('DATE_FORMAT_HZ1')); ?>
				</span>
			<?php } ?>
			<?php if ($this->params->get('show_desc', '')) { ?>
				<span class="a-desc">
					<?php
					// get cleaned article body text
					$desc = stripslashes($item->introtext);
					$desc = strip_tags($desc);
					$desc = str_replace("\n", '', $desc);
					$desc = str_replace("&nbsp;", '', $desc);
					echo \Hubzero\Utility\String::truncate($desc, $this->params->get('word_count', 200));
					?>
				</span>
			<?php } ?>
			<?php if ($this->params->get('show_morelink', '')) { ?>
				<span class="a-link">
					<a href="<?php echo $url; ?>">
						<?php echo Lang::txt('MOD_ANNOUNCEMENTS_READ_MORE'); ?>
					</a>
				</span>
			<?php } ?>
			</span>
		</li>
	<?php } ?>
	</ul>
<?php } else { ?>
	<p><?php echo Lang::txt('MOD_ANNOUNCEMENTS_NO_RESULTS'); ?></p>
<?php } ?>
</div><!-- / #pane-sliders -->