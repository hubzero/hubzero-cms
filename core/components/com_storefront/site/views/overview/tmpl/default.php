<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$this->css();

$customLandingPage = $this->config->get('landingPage', 0);

$return = base64_encode(Route::url('index.php?option=storefront'));
$loginUrl = Route::url('index.php?option=com_users&view=login&return=' . $return);

if ($customLandingPage && is_numeric($customLandingPage))
{
	$article = $this->content;

	if ($article->fulltext)
	{
		$article->text = $article->fulltext;
	}
	else
	{
		$article->text = $article->introtext;
	}

	// Prepare content to add CSS and other xhub stuff
	Event::trigger('content.onContentPrepare', array ('com_content.article', &$article, array()));

	?>

	<header id="content-header">
		<h2><?php echo $article->title; ?></h2>
	</header>

	<section class="section">
		<div class="section-inner">

			<div class="login-storefront"><a class="btn" href="<?php echo $loginUrl; ?>">Login</a></div>

			<?php

			echo $article->text;

			?>

		</div>
	</section>

<?php
}
// Use default view
else
{
?>

	<header id="content-header">
		<h2><?php echo Lang::txt('COM_STOREFRONT'); ?></h2>
	</header>

	<section id="introduction" class="section">
		<div class="grid">
			<div class="col span8">
				<p>Welcome to our store! In order to see the items in the store you need to login.</p>
			</div>
			<div class="col span3 offset1 omega">
				<a class="btn" href="<?php echo $loginUrl; ?>">Login</a>
			</div>
		</div>
	</section>

<?php
}