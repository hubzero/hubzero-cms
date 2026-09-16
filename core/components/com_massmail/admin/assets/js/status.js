/**
 * Mass Mail -- status dashboard: click a chart card to enlarge it.
 */
(function () {
	function init() {
		var modal = document.getElementById('mm-modal');
		if (!modal) { return; }
		var body = modal.querySelector('.mm-modal-body');
		var hint = modal.getAttribute('data-hint') || '';

		function closeModal() { modal.classList.remove('open'); body.innerHTML = ''; }

		var cards = document.querySelectorAll('.mm-dash .mm-card');
		for (var i = 0; i < cards.length; i++) {
			(function (card) {
				if (!card.querySelector('svg')) { return; }
				card.classList.add('mm-clickable');
				card.setAttribute('title', hint);
				card.addEventListener('click', function () {
					body.innerHTML = card.innerHTML;
					modal.classList.add('open');
				});
			})(cards[i]);
		}

		modal.addEventListener('click', function (e) {
			if (e.target === modal || e.target.classList.contains('mm-modal-close')) { closeModal(); }
		});
		document.addEventListener('keydown', function (e) {
			if (e.key === 'Escape' || e.keyCode === 27) { closeModal(); }
		});
	}
	if (document.readyState === 'loading') { document.addEventListener('DOMContentLoaded', init); }
	else { init(); }
})();
