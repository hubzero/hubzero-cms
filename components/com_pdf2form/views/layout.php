<?
$title = 'Layout: '. $pdf->getTitle();
$doc->addScript('/components/com_pdf2form/resources/layout.js');
$path->addItem(($title ? htmlentities($title) : 'Layout: new form'), $_SERVER['REQUEST_URI']);
?>
<noscript>
	<div class="error">You must enable JavaScript to annotate PDFs for deployment as forms, sorry.</div>
</noscript>
<form action="" method="post">
<label>
	<h2>Title</h2>
	<input type="text" class="required" id="title" value="<?= str_replace('"', '&quot;', $title) ?>" />
	<p id="title-error" class="error"></p>
</label>
<h2>Layout</h2>
<ol id="pages">
	<?
		$tabs = array();
		$layout = $pdf->getPageLayout();
		$pdf->eachPage(function($src, $idx) use(&$tabs, $layout) {
			$tabs[] = '<li><a href="#page-'.$idx.'"'.($idx == 1 ? ' class="current"' : '').'>'.$idx.'</a></li>';
			echo '<li id="page-'.$idx.'">';
			echo '<img src="'.$src.'" />';
			if (isset($layout[$idx - 1])) {
				$qidx = 0;
				foreach ($layout[$idx - 1] as $group) {
					echo '<div class="group-marker" style="width: '.$group['width'].'px; height: '.$group['height'].'px; top: '.$group['top'].'px; left: '.$group['left'].'px;">';
					echo '<button class="remove">x</button>';
					foreach ($group['answers'] as $aidx=>$ans) {
						echo '<div class="radio-container'.($ans['correct'] ? ' selected' : '').'" style="top: '.($ans['top'] - $group['top'] - 5).'px; left: '.($ans['left'] - $group['left'] - 26).'px;">';
						echo '<button class="remove">x</button>';
						echo '<input name="question-saved-'.$qidx.'" value="'.$aidx.'" class="placeholder"'.($ans['correct'] ? ' checked="checked"' : '').' type="radio" />';
						echo '</div>';
					}
					++$qidx;
					echo '</div>';
				}
			}
			echo '</li>';
	}); ?>
</ol>
<ol id="page-tabs">
	<? echo implode("\n", $tabs); ?>
</ol>
<p id="layout-error" class="error"></p>
<button id="save">Save</button><span id="saved-notification">Save complete</span>
</form>
