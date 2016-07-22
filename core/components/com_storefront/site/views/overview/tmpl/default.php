<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2011 Purdue University. All rights reserved.
 *
 * This file is part of: The HUBzero(R) Platform for Scientific Collaboration
 *
 * The HUBzero(R) Platform for Scientific Collaboration (HUBzero) is free
 * software: you can redistribute it and/or modify it under the terms of
 * the GNU Lesser General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or (at your option) any
 * later version.
 *
 * HUBzero is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Ilya Shunko <ishunko@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

$this->css();

$customLandingPage = $this->config->get('landingPage', 0);

$return = base64_encode(Route::url('index.php?option=storefront'));
$loginUrl = Route::url('index.php?option=com_users&view=login&return=' . $return);

if ($customLandingPage && is_numeric($customLandingPage))
{
	include_once(PATH_CORE . DS . 'components' . DS . 'com_content' . DS . 'site' . DS . 'models' . DS . 'article.php');
	$content = new ContentModelArticle();
	$article = $content->getItem($customLandingPage);

	if ($article->params->get('show_intro', '1') == '1')
	{
		$article->text = $article->introtext . ' ' . $article->fulltext;
	}
	elseif ($item->fulltext)
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

			<div class="login-storefront"><a class="btn" href="<?php echo($loginUrl); ?>">Login</a></div>

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
				<a class="btn" href="<?php echo($loginUrl); ?>">Login</a>
			</div>
		</div>
	</section>

<?php
}
?>