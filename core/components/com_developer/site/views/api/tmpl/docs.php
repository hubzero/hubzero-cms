<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$host = $_SERVER['HTTP_HOST'];
list($base, ) = explode('.', $host);
$url = 'https://' . $host . '/api';

// include needed css
$this->css('docs')
     ->css();

// add highlight lib
//Document::addStyleSheet('//cdnjs.cloudflare.com/ajax/libs/highlight.js/8.4/styles/github.min.css');
//Document::addScript('//cdnjs.cloudflare.com/ajax/libs/highlight.js/8.4/highlight.min.js');

// pull list of versions from doc
$versions = $this->documentation['versions']['available'];
$versions = array_reverse($versions);

// either the request var or the first version (newest)
$activeVersion = Request::getString('version', reset($versions));
?>

<header id="content-header">
	<h2><?php echo Lang::txt('COM_DEVELOPER_API_DOCS'); ?></h2>
	<div id="content-header-extra">
		<ul>
			<li>
				<a class="btn icon-cog" href="<?php echo Route::url('index.php?option=com_developer&controller=api&version=' . $activeVersion); ?>">
					<?php echo Lang::txt('COM_DEVELOPER_API_HOME'); ?>
				</a>
			</li>
		</ul>
	</div>
</header>

<section class="section api docs">
	<div class="section-inner hz-layout-with-aside">
		<aside class="aside">
			<?php 
			$this->view('_menu')
				 ->set('documentation', $this->documentation)
				 ->set('active', '')
				 ->set('version', $activeVersion)
				 ->display();
			?>
		</aside>
		<div class="subject">
			<?php 
			$this->view('_docs_overview')
				 ->set('url', $url)
				 ->set('base', $base)
				 ->display();

			$this->view('_docs_oauth')
				 ->set('url', $url)
				 ->display();
			?>
		</div>
	</div>
</section>
