<h2><?php echo htmlentities($title) ?></h2>
<p>You have <strong><?php echo timeDiff($realLimit*60) ?></strong> to complete this form. There are <strong><?php echo $pdf->getQuestionCount() ?></strong> questions.</p>
<?php if ($realLimit == $limit): ?>
	<p><em>Time will begin counting when you click 'Continue' below.</em></p>
<?php else: ?>
	<p><em>Time is already running because the form is close to expiring!</em></p>
<?php endif; ?>
<form action="/pdf2form" method="post">
	<fieldset>
		<input type="hidden" name="task" value="startWork" />
		<input type="hidden" name="crumb" value="<?php echo $dep->getCrumb() ?>" />
		<?php echo isset($_GET['tmpl']) ? '<input type="hidden" name="tmpl" value="'.str_replace('"', '&quot;', $_GET['tmpl']).'" />' : '' ?>
		<button type="submit">Continue</button>
	</fieldset>
</form>