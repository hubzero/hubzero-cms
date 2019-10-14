<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$series = $this->series;
$abstract = $series->abstract;
$publicationId = $series->publication_id;
$versionNumber = $series->version_number;
$title = $series->title;
$url = "/publications/$publicationId/$versionNumber";
?>

<?php if (!empty($series)): ?>
	<li>
		<a href="<?php echo $url; ?>" rel="noreferrer noopener" target="_blank">
			<u><?php echo $title; ?></u>
		</a>
		<p>
			<?php echo $abstract; ?>
		</p>
	</li>
<?php endif; ?>
