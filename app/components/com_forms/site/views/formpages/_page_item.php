<?php
/*
 * @package   hubzero-cms
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$editUrl = $this->editUrl;
$page = $this->page;
$pageId = $page->get('id');
$order = $page->get('order');
$title = $page->get('title');
?>

<li class="page-item" data-id="<?php echo $pageId; ?>">
	<span class="grid">

		<span class="col span1 offset1">
			<input name="pages[<?php echo $pageId; ?>][id]"
				type="hidden"
				value="<?php echo $pageId; ?>">
			<input name="pages[<?php echo $pageId; ?>][order]"
				type="number"
				min="1"
				value="<?php echo $order; ?>">
		</span>

		<span class="col span4">
			<input name="pages[<?php echo $pageId; ?>][title]"
				type="text"
				value="<?php echo $title; ?>">
		</span>

		<span class="col span1 offset5 omega crud-buttons">
			<a href="<?php echo $editUrl; ?>">
				<span class="fontcon">&#x270E;</span>
			</a>
			<span class="fontcon destroy-button">
				&#xf014;
			</span>
		</span>

	</span>
</li>
