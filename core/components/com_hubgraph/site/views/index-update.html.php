<?php
defined('_HZEXEC_') or die();

$doc = App::get('document');
if (!defined('HG_INLINE')) {
	$doc->setTitle('Search');
}
\Hubzero\Document\Assets::addComponentStylesheet('com_hubgraph');
$doc->addScript($basePath.'/assets/hubgraph-update.js');
$doc->addScript($basePath.'/assets/jquery.inview.js');

if (isset($results['js'])): ?>
<script type="text/javascript">
	<?php echo $results['js'] ?>
</script>
<?php endif; ?>
<?php if (isset($results['css'])): ?>
<style type="text/css">
	<?php echo $results['css'] ?>
</style>
<?php endif; ?>
<form id="search-form" class="search" action="" method="get">
	<div class="bar">
		<input type="text" autocomplete="off" name="terms" class="terms" placeholder="Enter search terms" value="<?php echo a($req->getTerms()) ?>" />
		<a class="clear" href="<?php echo preg_replace('/[?&]+$/', '', $base.($_SERVER['QUERY_STRING'] ? '?'.preg_replace('/^&/', '', preg_replace('/&?terms=[^&]*/', '', urldecode($_SERVER['QUERY_STRING']))) : '')) ?>">x</a>
		<button class="submit" type="submit"><span>Search</span></button>
	<?php
		if ($results['terms']['autocorrected']):
			$terms = h($req->getTerms());
			foreach ($results['terms']['autocorrected'] as $k=>$v):
				$terms = preg_replace('#'.preg_quote($k).'#i', '<strong>'.$v.'</strong>', $terms);
			endforeach;
		elseif ($results['terms']['suggested']):
			$terms = h($req->getTerms());
			$rawTerms = $terms;
			foreach ($results['terms']['suggested'] as $k=>$v):
				$terms    = str_replace($k, '<strong>'.$v.'</strong>', strtolower($terms));
				$rawTerms = str_replace($k, $v, $rawTerms);
			endforeach;
			$link = preg_replace('/\?terms=[^&]*/', 'terms='.$rawTerms, $_SERVER['QUERY_STRING']);
			if ($link[0] != '?'):
				$link = '?'.$link;
			endif;
		endif;
	?>
		<ul class="complete">
			<li class="cat users" title="Contributors"><ul></ul></li>
			<li class="cat tags" title="Tags"><ul></ul></li>
			<li class="cat orgs" title="Organization"><ul></ul></li>
			<li class="cat text"><ul></ul></li>
		</ul>
	</div>
	<?php 
	if (isset($results['clientDebug'])):
		define('HG_DEBUG', 1);
	endif;
	if (isset($results['html'])):
		echo $results['html'];
	endif;
	require 'page.html.php';
	?>
</form>
