<?php
/**
 * @package		HUBzero CMS
 * @author		Alissa Nedossekina <alisa@purdue.edu>
 * @copyright	Copyright 2005-2009 HUBzero Foundation, LLC.
 * @license		http://opensource.org/licenses/MIT MIT
 *
 * Copyright 2005-2009 HUBzero Foundation, LLC.
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
 */

// No direct access
defined('_HZEXEC_') or die();

$pubParams = $this->publication->params;

?>
<ul class="pub-selector" id="pub-selector">
	<?php foreach ($this->selections as $item)
	{
		$selected = $this->publication->get('license_type') && $this->publication->get('license_type') == $item->id ? true : false;

		// Automatically seleçt default license
		if ($item->main)
		{
			$selected = true;
		}

		$liId = 'choice-' . $item->id;

		$info = $item->info;
		if ($item->url)
		{
			$info .= ' <a href="' . $item->url . '" target="_blank">' . Lang::txt('Read license terms &rsaquo;') . '</a>';
		}

		$icon = $item->icon;
		$icon = str_replace('/components/com_publications/assets/img/', '/core/components/com_publications/site/assets/img/', $icon);

		?>
		<li class="type-license allowed <?php if ($selected) { echo ' selectedfilter'; } ?>" id="<?php echo $liId; ?>">
			<span class="item-info"></span>
			<span class="item-wrap">
			<?php if ($item->icon) { echo '<img src="' . $icon . '" alt="' . htmlentities($item->title) . '" />'; } ?><?php echo $item->title; ?>
			</span>
			<span class="item-fullinfo">
				<?php echo $info; ?>
			</span>
		</li>
	<?php } ?>
</ul>

<?php if ($this->publication->config()->get('suggest_licence')) { ?>
	<p class="hint"><?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_LICENSE_DONT_SEE_YOURS') . ' ' . Lang::txt('PLG_PROJECTS_PUBLICATIONS_LICENSE_YOU_CAN') ; ?> <a href="<?php echo $this->url . '?action=suggest_license&amp;version=' . $this->publication->get('version_number'); ?>" class="showinbox"><?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_LICENSE_SUGGEST'); ?></a></p>
<?php } ?>
