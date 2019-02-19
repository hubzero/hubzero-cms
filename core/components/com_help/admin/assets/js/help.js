window.onload = function() {
	var history = window.history,
		back    = document.getElementById('back');

	back.click(function(e){
		window.history.back();
	});

	if (history.length > 1 && back !== null) {
		back.style.display = "block";
	}
};
