<?
$title = $pdf->getTitle();
$doc->addScript('/components/com_pdf2form/resources/deploy.js');
$doc->addScript('/components/com_pdf2form/resources/timepicker.js');
$doc->addStyleSheet('/plugins/system/jquery/css/jquery-ui-1.8.6.custom.css');
$path->addItem('Deploy: '.htmlentities($title), $_SERVER['REQUEST_URI']);
?>
<h2>Deploy: <?= htmlentities($title) ?></h2>
<form action="/pdf2form" method="post" id="deployment">
	<? require 'deployment_form.php'; ?>
	<fieldset>
		<input type="hidden" name="task" value="createDeployment" />
		<input type="hidden" name="formId" value="<?= $pdf->getId() ?>" />
		<button type="submit">Create deployment</button>
	</fieldset>
</form>
