<?php
$doc->addScript('/components/com_pdf2form/resources/select.js');
$doc->addScript('/components/com_pdf2form/resources/tablesorter/jquery.tablesorter.min.js');
$doc->addStyleSheet('/components/com_pdf2form/resources/tablesorter/themes/blue/style.css');
$doc->setTitle('PDF Forms');
?>
<h2>Upload a PDF</h2>
<?php if ($errors): ?>
	<ul class="errors">
	<?php foreach ($errors as $error): ?>
		<li><?php echo $error; ?></li>
	<?php endforeach; ?>
	</ul>
<?php endif; ?>
<form action="" method="post" enctype="multipart/form-data">
	<input type="file" name="pdf" accept="application/pdf" required autofocus />
	<input type="hidden" name="task" value="upload" />
	<button type="submit">Upload</button>
</form>
<h2>Select a previous PDF</h2>
<table class="tablesorter">
	<thead>
		<tr><th>Title</th><th>Created</th><th>Updated</th></tr>
	</thead>
	<tbody>
	<?php foreach (PdfForm::getActiveList() as $form): ?>
		<tr>
			<td>
				<span class="title"><?php echo $form['title'] ?></span>
				<form action="/pdf2form" method="get">
					<input type="hidden" name="task" value="deploy" />
					<input type="hidden" name="formId" value="<?php echo $form['id'] ?>" />
					<button type="submit">Deploy</button>
				</form>
				<form action="/pdf2form" method="get">
					<input type="hidden" name="task" value="layout" />
					<input type="hidden" name="formId" value="<?php echo $form['id'] ?>" />
					<button type="submit">Edit</button>
				</form>
				<br />
				<?php if (($deps = PdfFormDeployment::forForm($form['id']))): ?>
				<table class="tablesorter nested">
					<thead>
						<tr><th>Deployment</th><th>User</th><th>Start date</th><th>End date</th></tr>
					</thead>
					<tbody>
					<?php foreach ($deps as $dep): ?>
						<tr>
							<td>
								<span class="state"><?php echo $dep->getState() ?></span>
								<a href="/pdf2form?task=showDeployment&id=<?php echo $dep->getId() ?>&formId=<?php echo $form['id'] ?>">
									<?php echo $dep->getLink() ?>
								</a>
							</td>
							<td><?php echo htmlentities($dep->getUserName()) ?></td>
							<td><?php echo date('Y-m-d H:i', strtotime($dep->getStartTime())) ?></td>
							<td><?php echo date('Y-m-d H:i', strtotime($dep->getEndTime())) ?></td>
						</tr>
					<?php endforeach; ?>
					</tbody>
				</table>
				<?php endif; ?>
			</td>
			<td><?php echo date('Y-m-d H:i', strtotime($form['created'])) ?></td>
			<td><?php echo date('Y-m-d H:i', strtotime($form['updated'])) ?></td></tr>
	<?php endforeach; ?>
	</tbody>
</table>
