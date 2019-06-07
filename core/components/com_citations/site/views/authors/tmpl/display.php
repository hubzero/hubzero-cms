<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die( 'Restricted access');

$authors = $this->citation->relatedAuthors();

if (count($authors)) { ?>
	<?php foreach ($authors as $author) { ?>
		<p class="citation-author" id="author_<?php echo $this->escape($author->id); ?>">
			<span class="author-handle">
			</span>
			<span class="author-name">
				<?php echo $this->escape($author->author); ?>
			</span>
			<span class="author-description">
				<a class="delete" data-id="<?php echo $this->escape($author->id); ?>" href="<?php echo Route::url('index.php?option=com_citations&controller=authors&task=remove&citation=' . $this->citation->id . '&author=' . $author->id . '&' . Session::getFormToken() . '=1'); ?>">
					<?php echo Lang::txt('JACTION_DELETE'); ?>
				</a>
			</span>
		</p>
	<?php } ?>
<?php }
