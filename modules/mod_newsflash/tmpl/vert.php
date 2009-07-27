<?php defined('_JEXEC') or die('Restricted access'); ?>

<?php for ($i = 0, $n = count($list); $i < $n; $i ++) :
	modNewsFlashHelper::renderItem($list[$i], $params, $access);
	if ($n > 1 && (($i < $n - 1) || $params->get('showLastSeparator'))) : ?>
		<span class="article_separator">&nbsp;</span>
 	<?php endif; ?>
<?php endfor; ?>
