/**
 * Mass Mail -- a single traffic-light status pill showing the send state
 * (green/orange/red), with rollover detail. State/label/detail arrive via the
 * #mm-pill-data element (emitted on every mass-mail admin page).
 *
 * It normally sits at the right of the component sub-navigation, but screens
 * that hide the sub-nav (e.g. the edit/view page with hidemainmenu) still get
 * it, dropped into the toolbar -- the send state should always be visible.
 */
(function () {
	function init() {
		var data = document.getElementById('mm-pill-data');
		if (!data || document.querySelector('.mm-status-pill')) { return; }

		var state  = data.getAttribute('data-state') || 'red';
		var label  = data.getAttribute('data-label') || '';
		var detail = data.getAttribute('data-detail') || '';

		var pill = document.createElement('span');
		pill.className = 'mm-status-pill mm-pill-' + state;
		if (detail) {
			pill.setAttribute('title', detail);
			pill.setAttribute('aria-label', label + ' — ' + detail);
		} else {
			pill.setAttribute('aria-label', label);
		}

		var dot = document.createElement('span');
		dot.className = 'mm-dot';
		pill.appendChild(dot);
		pill.appendChild(document.createTextNode(label));

		var li = document.createElement('li');
		li.className = 'mm-pill-li';
		li.appendChild(pill);

		// Preferred home: the component sub-navigation.
		var submenu = document.getElementById('submenu');
		if (submenu) {
			submenu.appendChild(li);
			return;
		}

		// Pages that hide the sub-nav (edit/view/verify) have no #submenu, so
		// build an otherwise-empty sub-navigation bar just below the toolbar to
		// host the pill -- same structure/styling as the real one, so the send
		// state sits on its own line rather than crammed into the toolbar.
		var toolbarBox = document.getElementById('toolbar-box');
		if (toolbarBox && toolbarBox.parentNode) {
			var nav = document.createElement('nav');
			nav.className = 'sub-navigation mm-pill-bar';
			nav.setAttribute('role', 'navigation');
			var ul = document.createElement('ul');
			ul.id = 'submenu';
			ul.appendChild(li);
			nav.appendChild(ul);
			toolbarBox.parentNode.insertBefore(nav, toolbarBox.nextSibling);
		}
	}
	if (document.readyState === 'loading') { document.addEventListener('DOMContentLoaded', init); }
	else { init(); }
})();
