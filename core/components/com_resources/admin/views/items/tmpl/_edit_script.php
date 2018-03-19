
<script type="text/javascript">
function submitbutton(pressbutton)
{
	if (pressbutton == 'resethits') {
		if (confirm('<?php echo Lang::txt('COM_RESOURCES_CONFIRM_HITS_RESET'); ?>')){
			submitform(pressbutton);
			return;
		} else {
			return;
		}
	}

	if (pressbutton == 'resetrating') {
		if (confirm('<?php echo Lang::txt('COM_RESOURCES_CONFIRM_RATINGS_RESET'); ?>')){
			submitform(pressbutton);
			return;
		} else {
			return;
		}
	}

	if (pressbutton == 'cancel') {
		submitform(pressbutton);
		return;
	}

	// do field validation
	if (document.getElementById('field-title').value == ''){
		alert('<?php echo Lang::txt('COM_RESOURCES_ERROR_MISSING_TITLE'); ?>');
	} else if (document.getElementById('type').value == "-1"){
		alert('<?php echo Lang::txt('COM_RESOURCES_ERROR_MISSING_TYPE'); ?>');
	} else {
		<?php echo $this->editor()->save('text'); ?>

		submitform(pressbutton);
	}
}

function doFileoptions()
{
	var fwindow = window.filer.window.imgManager;

	if (fwindow) {
		if (fwindow.document) {
			var fform = fwindow.document.forms['filelist'];

			if (fform) {
				//var filepath = fform.elements['listdir'];
				var slctdfiles = fform.slctdfile;
				if (slctdfiles.length > 1) {
					for (var i = 0; i < slctdfiles.length; i++)
					{
						if (slctdfiles[i].checked) {
							var filepath = slctdfiles[i].value;
						}
					}
				} else {
					var filepath = slctdfiles.value;
				}

				box = document.adminForm.fileoptions;
				act = box.options[box.selectedIndex].value;

				//var selection = window.filer.document.forms[0].dirPath;
				//var dir = selection.options[selection.selectedIndex].value;

				if (act == '1') {
					document.forms['adminForm'].elements['params[series_banner]'].value = '<?php echo $this->rconfig->get('uploadpath') . '/'; ?>' + filepath;
				} else if (act == '2') {
					//if (filepath) {
					//document.forms['adminForm'].elements['path'].value = '<?php echo $this->rconfig->get('uploadpath').DS; ?>' + filepath;
					document.forms['adminForm'].elements['fields[path]'].value = filepath;
					//}
				} else if (act == '3') {
					var content = <?php echo $this->editor()->getContent('field-fulltxt'); ?>
					content = content + '<p><img class="contentimg" src="<?php echo $this->rconfig->get('uploadpath').DS; ?>' + filepath + '" alt="image" /></p>';
					<?php echo $this->editor()->setContent('field-fulltxt', 'content'); ?>
				} else if (act == '4') {
					var content = <?php echo $this->editor()->getContent('field-fulltxt'); ?>
					content = content + '<p><a href="<?php echo $this->rconfig->get('uploadpath').DS; ?>' + filepath + '">' + filepath + '</a></p>';
					<?php echo $this->editor()->setContent('field-fulltxt', 'content'); ?>
				}
			}
		}
	}
}
function popratings()
{
	window.open("<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=ratings&id=' . $this->row->id . '&no_html=1', false); ?>", 'ratings', 'status=no,toolbar=no,scrollbars=yes,titlebar=no,menubar=no,resizable=yes,width=400,height=480,directories=no,location=no');
	return false;
}
</script>
