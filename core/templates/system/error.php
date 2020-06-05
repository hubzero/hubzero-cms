<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

defined('_HZEXEC_') or die();

if (!isset($this->error))
{
	$this->error = new Exception(Lang::txt('JERROR_ALERTNOAUTHOR'), 404);
	$this->debug = false;
}
?>
<!DOCTYPE html>
<html lang="<?php echo $this->language; ?>" dir="<?php echo $this->direction; ?>" class="no-js <?php echo $this->direction; ?>">
	<head>
		<meta http-equiv="content-type" content="text/html; charset=utf-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1.0" />
		<title><?php echo $this->error->getCode(); ?> - <?php echo htmlspecialchars($this->error->getMessage(), ENT_QUOTES, 'UTF-8'); ?></title>
		<link rel="stylesheet" type="text/css" href="<?php echo $this->baseurl; ?>/templates/system/css/error.css?v=<?php echo filemtime(__DIR__ . '/css/error.css'); ?>" />
	</head>
	<body id="error-body">
		<div class="container">
			<header>
				<h1><?php echo $this->error->getCode(); ?> - <?php echo htmlspecialchars($this->error->getMessage(), ENT_QUOTES, 'UTF-8'); ?></h1>
			</header>
			<main class="content">
				<section>
					<p><?php echo Lang::txt('JERROR_LAYOUT_NOT_ABLE_TO_VISIT'); ?></p>
					<ol>
						<li><?php echo Lang::txt('JERROR_LAYOUT_AN_OUT_OF_DATE_BOOKMARK_FAVOURITE'); ?></li>
						<li><?php echo Lang::txt('JERROR_LAYOUT_SEARCH_ENGINE_OUT_OF_DATE_LISTING'); ?></li>
						<li><?php echo Lang::txt('JERROR_LAYOUT_MIS_TYPED_ADDRESS'); ?></li>
						<li><?php echo Lang::txt('JERROR_LAYOUT_YOU_HAVE_NO_ACCESS_TO_THIS_PAGE'); ?></li>
						<li><?php echo Lang::txt('JERROR_LAYOUT_REQUESTED_RESOURCE_WAS_NOT_FOUND'); ?></li>
						<li><?php echo Lang::txt('JERROR_LAYOUT_ERROR_HAS_OCCURRED_WHILE_PROCESSING_YOUR_REQUEST'); ?></li>
					</ol>
				</section>
				<section>
					<p><?php echo Lang::txt('JERROR_LAYOUT_PLEASE_TRY_ONE_OF_THE_FOLLOWING_PAGES'); ?></p>
					<ul>
						<li><a href="<?php echo $this->baseurl; ?>/index.php" title="<?php echo Lang::txt('JERROR_LAYOUT_GO_TO_THE_HOME_PAGE'); ?>"><?php echo Lang::txt('JERROR_LAYOUT_HOME_PAGE'); ?></a></li>
					</ul>
				</section>
				<p><?php echo Lang::txt('JERROR_LAYOUT_PLEASE_CONTACT_THE_SYSTEM_ADMINISTRATOR'); ?></p>
			</main>
			<?php if ($this->debug) : ?>
				<div class="trace">
					<?php echo $this->renderBacktrace(); ?>
				</div>
			<?php endif; ?>
		</div>
	</body>
</html>