<div id="content-header" class="full">
	<h2><?php echo 'Layout: ' . $this->title; ?></h2>
</div>

<div class="main section courses-form">
	<noscript>
		<div class="error">You must enable JavaScript to annotate PDFs for deployment as forms, sorry.</div>
	</noscript>
	<form action="" method="post">
	<div id="saved-notification" class="passed-box">Save complete</div>
	<label>
		Title:
		<? if (!$this->readonly) : ?>
			<input type="text" class="required" id="title" value="<?= str_replace('"', '&quot;', $this->title) ?>" />
		<? else : ?>
			<?= $this->title ?>
		<? endif; ?>
			<span>
		<p id="title-error" class="error"></p>
	</label>
	<label>
		<?= ($this->pdf->getAssetType() !== false) ? 'Type:' : '' ?>
		<? if (!$this->readonly && $this->pdf->getAssetType() !== false) : ?>
			<select name="type" id="asset-type">
				<option value="exam"<?= ($this->pdf->getAssetType() == 'exam') ? 'selected=selected': '' ?>>Exam</option>
				<option value="quiz"<?= ($this->pdf->getAssetType() == 'quiz') ? 'selected=selected': '' ?>>Quiz</option>
				<option value="homework"<?= ($this->pdf->getAssetType() == 'homework') ? 'selected=selected': '' ?>>Homework</option>
			</select>
		<? else : ?>
			<?= $this->pdf->getAssetType() ?>
		<? endif; ?>
	</label>
	<ol id="pages"<?= ($this->readonly) ? ' class="readonly"' : '' ?>>
		<? 
			$tabs = array();
			$layout = $this->pdf->getPageLayout();
			$this->pdf->eachPage(function($src, $idx) use(&$tabs, $layout) {
				$tabs[] = '<li><a href="#page-'.$idx.'"'.($idx == 1 ? ' class="current"' : '').'>'.$idx.'</a></li>';
				echo '<li id="page-'.$idx.'">';
				echo '<img src="'.$src.'" />';
				if (isset($layout[$idx - 1])) {
					$qidx = 0;
					foreach ($layout[$idx - 1] as $group) {
						echo '<div class="group-marker" style="width: '.$group['width'].'px; height: '.$group['height'].'px; top: '.$group['top'].'px; left: '.$group['left'].'px;">';
						echo '<div class="group-marker-header"></div>';
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
	<div class="navbar">
		<ol id="page-tabs">
			<? echo implode("\n", $tabs); ?>
		</ol>
		<? if (!$this->readonly) : ?>
			<div><a href="" id="save">Save and Close</a></div>
		<? endif; ?>
		<div><a href="/courses/form" id="done"><?= ($this->readonly) ? 'Done' : 'Cancel' ?></a></div>
		<? if (!$this->readonly) : ?>
			<div class="question-info">
				<p>
					<span class="questions-total"><?= $this->pdf->getQuestionCount() ?></span> question(s) total, <span class="questions-unsaved">0</span> changes unsaved
				</p>
			</div>
		<? endif; ?>
	</div>
	<p id="layout-error" class="error"></p>
	<div class="clear"></div>
	</form>
</div>