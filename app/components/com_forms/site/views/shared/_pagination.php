<?php
/*
 * @package   hubzero-cms
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$minDisplayLimit = isset($this->minDisplayLimit) ? $this->minDisplayLimit : 0;
$pagination = $this->pagination;
$paginationUrl = $this->paginationUrl;
$recordsCount = $this->recordsCount;
?>

<?php if ($recordsCount > $minDisplayLimit): ?>
	<span>
		<form method="POST" action="<?php echo $paginationUrl; ?>">
			<?php echo $pagination; ?>
		</form>
	</span>
<?php endif; ?>
