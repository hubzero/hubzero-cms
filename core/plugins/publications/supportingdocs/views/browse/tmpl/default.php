<?php
/**
 * @package		HUBzero CMS
 * @author		Shawn Rice <zooley@purdue.edu>
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

$this->css();
?>
<div class="supportingdocs" id="supportingdocs">
	<h3>
		<?php echo Lang::txt('PLG_PUBLICATION_SUPPORTINGDOCS'); ?>
	</h3>

	<?php
	// Get elements in primary and supporting role
	$prime    = $this->publication->_curationModel->getElements(1);
	$second   = $this->publication->_curationModel->getElements(2);
	$elements = array_merge($prime, $second);

	// Get attachment type model
	$database = App::get('db');

	$attModel = new \Components\Publications\Models\Attachments($database);

	if ($elements)
	{
		// Draw list
		$list = $attModel->listItems(
			$elements,
			$this->publication,
			$this->authorized
		);
		echo $list ? $list : '<p class="noresults">' . Lang::txt('PLG_PUBLICATION_SUPPORTINGDOCS_NONE_FOUND') . '</p>';
	}
	else
	{
		?>
		<p class="noresults"><?php echo Lang::txt('PLG_PUBLICATION_SUPPORTINGDOCS_NONE_FOUND'); ?></p>
		<?php
	}
	?>
</div><!-- / .supportingdocs -->
