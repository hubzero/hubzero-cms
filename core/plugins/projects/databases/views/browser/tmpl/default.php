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

// Get databases to choose from
$objPD = new \Components\Projects\Tables\Database($this->database);
$items = $objPD->getItems($this->model->get('id'), array());

$missing = array();
$shown = array();

// Attached item
$selected = NULL;
if ($this->primary && !empty($this->attachments))
{
	$selected = $this->attachments[0]->object_name;
}

// Build url
$route = $this->model->isProvisioned()
	? 'index.php?option=com_publications' . '&task=submit'
	: 'index.php?option=com_projects' . '&alias=' . $this->model->get('alias');
$p_url = Route::url($route . '&active=databases');

?>
<ul id="c-browser" <?php if (count($items) == 0 && isset($this->attachments) && count($this->attachments) == 0) { echo 'class="hidden"'; } ?> >
<?php
	if (count($items) > 0)
	{ ?>
	<?php
		foreach ($items as $item)
		{
			if ($item->revision == NULL || ($selected && $selected == $item->database_name) )
			{
			?>
		<li class="c-click databases" id="data::<?php echo $item->database_name; ?>"><?php echo $item->title; ?></li>
	<?php
			$shown[] = $item->database_name;
			}
		} ?>

		<?php

		// Check for missing items
		// Primary content / Supporting docs
		if (isset($this->attachments))
		{
			if (count($this->attachments) > 0)
			{
				foreach ($this->attachments as $attachment)
				{
					if (!in_array($attachment->object_name, $shown))
					{
						// Found missing
						$miss = array();
						$miss['id'] = $attachment->object_name;
						$miss['title'] = $attachment->title;
						$missing[] = $miss;
					}
				}
			}
		}

		// Add missing items
		if (count($missing) > 0)
		{
			foreach ($missing as $miss)
			{ ?>
				<li class="c-click databases i-missing" id="data::<?php echo $miss['id']; ?>"><?php echo $miss['title']; ?><span class="c-missing"><?php echo Lang::txt('PLG_PROJECTS_DATA_MISSING_DATABASE'); ?></span></li>
		<?php	}
		}
	}
?>
</ul>

<?php if ((count($shown) + count($missing)) == 0) { ?>
	<p class="noresults"><?php echo Lang::txt('PLG_PROJECTS_DATA_NO_SELECTION_ITEMS_FOUND_DATA'); ?></p>
<?php } ?>

<?php if (!$this->model->isProvisioned()) { ?>
	<p class="addnew"><?php echo Lang::txt('PLG_PROJECTS_DATABASES_GO_TO'); ?> <a href="<?php echo Route::url($route . '&active=databases'); ?>"><?php echo Lang::txt('PLG_PROJECTS_DATABASES'); ?></a> <?php echo Lang::txt('PLG_PROJECTS_DATABASES_TO_CREATE'); ?></p>
<?php } ?>