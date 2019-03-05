jQuery(document).ready(function($){
	$('.entry-role').on('click', function(e) {
		var task = document.getElementById('task');
		task.value = 'update';

		var form = document.getElementById('adminForm');
		form.submit();
	});
});
