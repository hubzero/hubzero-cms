<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access.
defined('_HZEXEC_') or die();

$componentParams = Component::params('com_search');
$tagSearchEnabled = !!$componentParams->get('solr_tagsearch', 0);
$tagSearchNotice = Lang::txt('COM_SEARCH_NOTICE_TAG_SEARCH');
?>

<?php if ($tagSearchEnabled): ?>
	<header id="tag-search-notice">
		<?php echo $tagSearchNotice; ?>
	</header>
<?php endif; ?>
