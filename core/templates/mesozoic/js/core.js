// Settings
// Flag if main navigation links should expand on the carrot mouse over:
let expandNavigationLinksOnHover = false;

(function(CORE, $, undefined) {
	CORE.page;
	CORE.pageBody;

	var header;
	var mainNav;
	var mainNavContainer;

	var searchTrigger;
	var searchPanel;
	var searchField;
	var html;

	// Check if main nav is not fitting, and it is time to switch to mobile
	var checkNav = function() {
		// Min gap allowed
		var navGap = 5;

		if(mainNavContainer.width() < mainNav.outerWidth() + navGap) {
			header.addClass('mobile');
		}
		else {
			header.removeClass('mobile');
		}

	};

	CORE.init = function() {
		html = $('html');
		header = $('.page-head');
		mainNav = $('.page-head nav.main-nav > ul.menu');
		mainNavContainer = $('.page-head nav.main-nav');
		CORE.page = $(this);
		CORE.pageBody = $('body');

		NAV.init();

		searchTrigger = $('.search-trigger');
		searchPanel = $('#big-search');
		searchField = $('#big-search #searchword');

		setupSearch();

		// Resize needs everything to be initialized first, hence, goes after initialization
		CORE.resize();
	};

	// ***************** search

	var setupSearch = function() {
		$(searchTrigger).on('click', function(e) {
			CORE.closeAllPanels();
			openSearchPanel(this);
			e.preventDefault();
		});

		$('#big-search .close').on('click', function(e) {
			CORE.closeAllPanels();

			// Restart input capturing for NoVNC
			if (typeof UI != 'undefined') {
				Util.addEvent(document, 'click', UI.checkFocusBounds);
			}

			e.preventDefault();
		});
	};

	var openSearchPanel = function(trigger) {
		// Disable input capturing for NoVNC
		if (typeof UI != 'undefined') {
			Util.removeEvent(document, 'click', UI.checkFocusBounds);
		}
		openDialog('big-search', trigger);
	};

	var closeSearchPanel = function() {
		if((searchPanel.hasClass('default_dialog'))) {
			closeDialog(document.querySelector('#big-search .close'));
			// Restart input capturing for NoVNC
			if (typeof UI != 'undefined') {
				Util.addEvent(document, 'click', UI.checkFocusBounds);
			}
		}
	};

	// ***************** end search


	CORE.resize = function() {
		checkNav();
	};

	CORE.setupPage = function() {
		CORE.init();

		$(window).resize(function() {
			CORE.resize();
		});

		// resize on styles load too
		window.addEventListener('load', function () {
			CORE.resize();
		});
	};

	CORE.closeAllPanels = function() {
		NAV.hideMobileNav();
		closeSearchPanel();
		CORE.pageBody.removeClass('panel-open');
		// find all template panels and remove tho 'open' class
		$('.template-panel').removeClass('open');
	};

}( window.CORE = window.CORE || {}, jQuery ));

(function(NAV, $, undefined) {

	// Main Navigation Menu
	var mobileNavSwitch;
	var mobileNavWrapper = null;
	var mobileNavCloseTrigger = null;
	var pageOverlay;

	var cloneMainNav = function() {
		// Find main nav
		var mainNavClone = $('.page-head nav.nav').clone();
		// Strip the .main-nav-bar
		mainNavClone.find('.main-nav-bar').remove();
		mainNavClone.find('.menu-button-links').removeClass('menu-button-links');
		mainNavClone.find('#menubutton').remove();
		mainNavClone.find('#menu2').removeAttr('id').removeAttr('role').removeAttr('aria-labelledby');
		let mainNavContainerClone = mainNavClone.find('.main-nav');

		// Create a wrapper and clone the nav
		mobileNavWrapper = $('<div class="mobile-nav"></div>');
		var mainNavScroller = $('<div class="scroll"></div>');
		mainNavScroller.appendTo(mobileNavWrapper);
		var mainNavInner = $('<div class="inner"></div>');

		var mainNav = $('.main-nav-bar');
		var lis = mainNav.find('> li');

		var menu = $("<div class='accordion main-nav-bar'>");

		lis.each(function() {
			var rootLink = $(this).find('> .inner a');
			var menuButton = $(this).find('> .inner button');
			var id;
			let subList;

			if(menuButton && menuButton.attr('aria-controls')) {
				id = menuButton.attr('aria-controls').replace('aria-control-target-', '');
				subList = $(this).find('> ul');
			}

			var header = $("<header>");
			var newRootLink = rootLink.clone();
			header.append(newRootLink);

			if(menuButton && id) {
				header.addClass('expandable');
				var button = $('<button type="button" aria-expanded="false" class="accordion-trigger">');
				var buttonId = 'accordionButton' + id;
				var buttonControls = 'accordionSection' + id;
				button.attr('aria-controls', buttonControls);
				button.attr('id', buttonId);
				header.append(button);

				var section = $("<div id='" + buttonControls + "' role='region' aria-labelledby='" + buttonId + "' class='accordion-panel' hidden>");
				section.html(subList.clone().removeAttr('id'));

			}

			menu.append(header);

			if(section) {
				menu.append(section);
			}
		});

		mainNavContainerClone.append(menu);

		mainNavClone.appendTo(mainNavInner);
		mainNavInner.appendTo(mainNavScroller);

		// Create a close button and append it to the wrapper
		mobileNavCloseTrigger = $('<button class="close"><span>close</span></button>');
		mobileNavCloseTrigger.appendTo(mobileNavWrapper);

		mobileNavWrapper.appendTo('body');

		// Create and add overlay if no overlay
		if(pageOverlay.length == 0) {
			pageOverlay = $('<div class="page-overlay"></div>');
			pageOverlay.appendTo('body');
		}
	};

	NAV.init = function() {
		mobileNavSwitch = $('.page-head .mobile-menu button');
		pageOverlay = $('.page-overlay');

		cloneMainNav();

		$(mobileNavSwitch).on('click', function(e) {
			if(!(mobileNavWrapper.hasClass('open'))) {
				CORE.closeAllPanels();
				showMobileNav();
			}
			else {
				CORE.closeAllPanels();
			}

			e.preventDefault();
		});

		$(mobileNavCloseTrigger).on('click', function(e) {
			CORE.closeAllPanels();
			e.preventDefault();
		});

		// Escape button to the rescue for those who like to press it in a hope to close whatever is open
		$(document).keyup(function(e) {
			if (e.keyCode == 27) {
				CORE.closeAllPanels();
			}
		});

		$(pageOverlay).on('click', function(e) {
			CORE.closeAllPanels();
			e.preventDefault();
		});

		// init accordions
		const accordions = document.querySelectorAll('.accordion header.expandable');
		accordions.forEach((accordionEl) => {
			new Accordion(accordionEl);
		});

		// init menu buttons
		const menuButtons = document.querySelectorAll('.menu-button-links');
		for (let i = 0; i < menuButtons.length; i++) {
			new MenuButtonLinks(menuButtons[i]);
		}

		/* init Disclosure Menus */
		var menus = document.querySelectorAll('.disclosure-nav');
		var disclosureMenus = [];

		for (var i = 0; i < menus.length; i++) {
			disclosureMenus[i] = new DisclosureNav(menus[i], expandNavigationLinksOnHover);
		}

		if(expandNavigationLinksOnHover) {
			document.addEventListener('keydown', function (event) {
				if(event.key === "Escape" || event.key === "Esc") {
					disclosureMenus.forEach(menu => {
						menu.toggleExpand(menu.openIndex, false);
					});
				}
			});
		}
	};

	NAV.resize = function() {
		CORE.closeAllPanels();
	};

	var showMobileNav = function() {
		mobileNavWrapper.addClass('show');
		CORE.pageBody.addClass('panel-open');
		CORE.pageBody.addClass('side-panel-open');
	};

	NAV.hideMobileNav = function() {
		if(!mobileNavWrapper.hasClass('show')) {
			return;
		}
		mobileNavWrapper.removeClass('show');
		CORE.pageBody.removeClass('side-panel-open');
	};

}( window.NAV = window.NAV || {}, jQuery ));

/*
 *   This content is licensed according to the W3C Software License at
 *   https://www.w3.org/Consortium/Legal/2015/copyright-software-and-document
 *
 *   File:   menu-button-links.js
 *
 *   Desc:   Creates a menu button that opens a menu of links
 */

'use strict';

class MenuButtonLinks {
	constructor(domNode) {
		this.domNode = domNode;
		this.buttonNode = domNode.querySelector('button');
		this.menuNode = domNode.querySelector('[role="menu"]');
		this.menuitemNodes = [];
		this.firstMenuitem = false;
		this.lastMenuitem = false;
		this.firstChars = [];

		this.buttonNode.addEventListener(
			'keydown',
			this.onButtonKeydown.bind(this)
		);
		this.buttonNode.addEventListener('click', this.onButtonClick.bind(this));

		var nodes = domNode.querySelectorAll('[role="menuitem"]');

		for (var i = 0; i < nodes.length; i++) {
			var menuitem = nodes[i];
			this.menuitemNodes.push(menuitem);
			menuitem.tabIndex = -1;
			this.firstChars.push(menuitem.textContent.trim()[0].toLowerCase());

			menuitem.addEventListener('keydown', this.onMenuitemKeydown.bind(this));

			menuitem.addEventListener(
				'mouseover',
				this.onMenuitemMouseover.bind(this)
			);

			if (!this.firstMenuitem) {
				this.firstMenuitem = menuitem;
			}
			this.lastMenuitem = menuitem;
		}

		domNode.addEventListener('focusin', this.onFocusin.bind(this));
		domNode.addEventListener('focusout', this.onFocusout.bind(this));

		window.addEventListener(
			'mousedown',
			this.onBackgroundMousedown.bind(this),
			true
		);
	}

	setFocusToMenuitem(newMenuitem) {
		this.menuitemNodes.forEach(function (item) {
			if (item === newMenuitem) {
				item.tabIndex = 0;
				newMenuitem.focus();
			} else {
				item.tabIndex = -1;
			}
		});
	}

	setFocusToFirstMenuitem() {
		this.setFocusToMenuitem(this.firstMenuitem);
	}

	setFocusToLastMenuitem() {
		this.setFocusToMenuitem(this.lastMenuitem);
	}

	setFocusToPreviousMenuitem(currentMenuitem) {
		var newMenuitem, index;

		if (currentMenuitem === this.firstMenuitem) {
			newMenuitem = this.lastMenuitem;
		} else {
			index = this.menuitemNodes.indexOf(currentMenuitem);
			newMenuitem = this.menuitemNodes[index - 1];
		}

		this.setFocusToMenuitem(newMenuitem);

		return newMenuitem;
	}

	setFocusToNextMenuitem(currentMenuitem) {
		var newMenuitem, index;

		if (currentMenuitem === this.lastMenuitem) {
			newMenuitem = this.firstMenuitem;
		} else {
			index = this.menuitemNodes.indexOf(currentMenuitem);
			newMenuitem = this.menuitemNodes[index + 1];
		}
		this.setFocusToMenuitem(newMenuitem);

		return newMenuitem;
	}

	setFocusByFirstCharacter(currentMenuitem, char) {
		var start, index;

		if (char.length > 1) {
			return;
		}

		char = char.toLowerCase();

		// Get start index for search based on position of currentItem
		start = this.menuitemNodes.indexOf(currentMenuitem) + 1;
		if (start >= this.menuitemNodes.length) {
			start = 0;
		}

		// Check remaining slots in the menu
		index = this.firstChars.indexOf(char, start);

		// If not found in remaining slots, check from beginning
		if (index === -1) {
			index = this.firstChars.indexOf(char, 0);
		}

		// If match was found...
		if (index > -1) {
			this.setFocusToMenuitem(this.menuitemNodes[index]);
		}
	}

	// Utilities

	getIndexFirstChars(startIndex, char) {
		for (var i = startIndex; i < this.firstChars.length; i++) {
			if (char === this.firstChars[i]) {
				return i;
			}
		}
		return -1;
	}

	// Popup menu methods

	openPopup() {
		this.menuNode.style.display = 'block';
		this.buttonNode.setAttribute('aria-expanded', 'true');
	}

	closePopup() {
		if (this.isOpen()) {
			this.buttonNode.removeAttribute('aria-expanded');
			this.menuNode.style.display = 'none';
		}
	}

	isOpen() {
		return this.buttonNode.getAttribute('aria-expanded') === 'true';
	}

	// Menu event handlers

	onFocusin() {
		this.domNode.classList.add('focus');
	}

	onFocusout() {
		this.domNode.classList.remove('focus');
	}

	onButtonKeydown(event) {
		var key = event.key,
			flag = false;

		switch (key) {
			case ' ':
			case 'Enter':
			case 'ArrowDown':
			case 'Down':
				this.openPopup();
				this.setFocusToFirstMenuitem();
				flag = true;
				break;

			case 'Esc':
			case 'Escape':
				this.closePopup();
				this.buttonNode.focus();
				flag = true;
				break;

			case 'Up':
			case 'ArrowUp':
				this.openPopup();
				this.setFocusToLastMenuitem();
				flag = true;
				break;

			default:
				break;
		}

		if (flag) {
			event.stopPropagation();
			event.preventDefault();
		}
	}

	onButtonClick(event) {
		if (this.isOpen()) {
			this.closePopup();
			this.buttonNode.focus();
		} else {
			this.openPopup();
			this.setFocusToFirstMenuitem();
		}

		event.stopPropagation();
		event.preventDefault();
	}

	onMenuitemKeydown(event) {
		var tgt = event.currentTarget,
			key = event.key,
			flag = false;

		function isPrintableCharacter(str) {
			return str.length === 1 && str.match(/\S/);
		}

		if (event.ctrlKey || event.altKey || event.metaKey) {
			return;
		}

		if (event.shiftKey) {
			if (isPrintableCharacter(key)) {
				this.setFocusByFirstCharacter(tgt, key);
				flag = true;
			}

			if (event.key === 'Tab') {
				this.buttonNode.focus();
				this.closePopup();
				flag = true;
			}
		} else {
			switch (key) {
				case ' ':
					window.location.href = tgt.href;
					break;

				case 'Esc':
				case 'Escape':
					this.closePopup();
					this.buttonNode.focus();
					flag = true;
					break;

				case 'Up':
				case 'ArrowUp':
					this.setFocusToPreviousMenuitem(tgt);
					flag = true;
					break;

				case 'ArrowDown':
				case 'Down':
					this.setFocusToNextMenuitem(tgt);
					flag = true;
					break;

				case 'Home':
				case 'PageUp':
					this.setFocusToFirstMenuitem();
					flag = true;
					break;

				case 'End':
				case 'PageDown':
					this.setFocusToLastMenuitem();
					flag = true;
					break;

				case 'Tab':
					this.closePopup();
					break;

				default:
					if (isPrintableCharacter(key)) {
						this.setFocusByFirstCharacter(tgt, key);
						flag = true;
					}
					break;
			}
		}

		if (flag) {
			event.stopPropagation();
			event.preventDefault();
		}
	}

	onMenuitemMouseover(event) {
		var tgt = event.currentTarget;
		tgt.focus();
	}

	onBackgroundMousedown(event) {
		if (!this.domNode.contains(event.target)) {
			if (this.isOpen()) {
				this.closePopup();
				this.buttonNode.focus();
			}
		}
	}
}

/*
 *   This content is licensed according to the W3C Software License at
 *   https://www.w3.org/Consortium/Legal/2015/copyright-software-and-document
 *
 *   Supplemental JS for the disclosure menu keyboard behavior
 */

'use strict';

class DisclosureNav {
	constructor(domNode, expandNavigationLinksOnHover) {
		this.expandNavigationLinksOnHover = expandNavigationLinksOnHover;
		this.rootNode = domNode;
		this.controlledNodes = [];
		this.openIndex = null;
		this.useArrowKeys = true;
		this.topLevelNodes = [
			...this.rootNode.querySelectorAll(
				'.main-link, button[aria-expanded][aria-controls]'
			),
		];

		this.hovers = [
			...this.rootNode.querySelectorAll(
				'.parent'
			),
		]

		this.topLevelNodes.forEach((node) => {
			// handle button + menu
			if (
				node.tagName.toLowerCase() === 'button' &&
				node.hasAttribute('aria-controls')
			) {
				const menu = node.parentNode.parentNode.querySelector('ul');
				if (menu) {
					// save ref controlled menu
					this.controlledNodes.push(menu);

					// collapse menus
					node.setAttribute('aria-expanded', 'false');
					this.toggleMenu(menu, false);

					// attach event listeners
					menu.addEventListener('keydown', this.onMenuKeyDown.bind(this));
					node.addEventListener('click', this.onButtonClick.bind(this));
					node.addEventListener('keydown', this.onButtonKeyDown.bind(this));
				}
			}
			// handle links
			else {
				this.controlledNodes.push(null);
				node.addEventListener('keydown', this.onLinkKeyDown.bind(this));
			}
		});

		if(this.expandNavigationLinksOnHover) {
			this.hovers.forEach((hover) => {
				hover.addEventListener('mouseenter', this.onLiHover.bind(this));
				hover.addEventListener('mouseleave', this.onLiHoverOut.bind(this));
			})
		}

		this.rootNode.addEventListener('focusout', this.onBlur.bind(this));
	}

	controlFocusByKey(keyboardEvent, nodeList, currentIndex) {
		switch (keyboardEvent.key) {
			case 'ArrowUp':
			case 'ArrowLeft':
				keyboardEvent.preventDefault();
				if (currentIndex > -1) {
					var prevIndex = Math.max(0, currentIndex - 1);
					nodeList[prevIndex].focus();
				}
				break;
			case 'ArrowDown':
			case 'ArrowRight':
				keyboardEvent.preventDefault();
				if (currentIndex > -1) {
					var nextIndex = Math.min(nodeList.length - 1, currentIndex + 1);
					nodeList[nextIndex].focus();
				}
				break;
			case 'Home':
				keyboardEvent.preventDefault();
				nodeList[0].focus();
				break;
			case 'End':
				keyboardEvent.preventDefault();
				nodeList[nodeList.length - 1].focus();
				break;
		}
	}

	// public function to close open menu
	close() {
		this.toggleExpand(this.openIndex, false);
	}

	onBlur(event) {
		var menuContainsFocus = this.rootNode.contains(event.relatedTarget);
		if (!menuContainsFocus && this.openIndex !== null) {
			this.toggleExpand(this.openIndex, false);
		}
	}

	onLiHover(event) {
		// Keep the subnav open as long li is hovered
		this.toggleExpand(this.getButtonIndex(event), true)
	}

	onLiHoverOut(event) {
		// Close the subnav on mouseout
		this.toggleExpand(this.getButtonIndex(event), false);
	}

	getButtonIndex(event) {
		// Get the parent li that was hovered
		var li = event.target.closest('li.parent');
		// Find the button
		var button = li.querySelector('button');
		// Return the index of the button
		return this.topLevelNodes.indexOf(button);
	}

	onButtonClick(event) {
		var button = event.target;
		var buttonIndex = this.topLevelNodes.indexOf(button);
		var buttonExpanded = button.getAttribute('aria-expanded') === 'true';
		this.toggleExpand(buttonIndex, !buttonExpanded);
	}

	onButtonKeyDown(event) {
		var targetButtonIndex = this.topLevelNodes.indexOf(document.activeElement);

		// close on escape
		if (event.key === 'Escape') {
			this.toggleExpand(this.openIndex, false);
		}

		// move focus into the open menu if the current menu is open
		else if (
			this.useArrowKeys &&
			this.openIndex === targetButtonIndex &&
			event.key === 'ArrowDown'
		) {
			event.preventDefault();
			this.controlledNodes[this.openIndex].querySelector('a').focus();
		}

		// handle arrow key navigation between top-level buttons, if set
		else if (this.useArrowKeys) {
			this.controlFocusByKey(event, this.topLevelNodes, targetButtonIndex);
		}
	}

	onLinkKeyDown(event) {
		var targetLinkIndex = this.topLevelNodes.indexOf(document.activeElement);

		// handle arrow key navigation between top-level buttons, if set
		if (this.useArrowKeys) {
			this.controlFocusByKey(event, this.topLevelNodes, targetLinkIndex);
		}
	}

	onMenuKeyDown(event) {
		if (this.openIndex === null) {
			return;
		}

		var menuLinks = Array.prototype.slice.call(
			this.controlledNodes[this.openIndex].querySelectorAll('a')
		);
		var currentIndex = menuLinks.indexOf(document.activeElement);

		// close on escape
		if (event.key === 'Escape') {
			this.topLevelNodes[this.openIndex].focus();
			this.toggleExpand(this.openIndex, false);
		}

		// handle arrow key navigation within menu links, if set
		else if (this.useArrowKeys) {
			this.controlFocusByKey(event, menuLinks, currentIndex);
		}
	}

	toggleExpand(index, expanded) {
		// close open menu, if applicable
		if (this.openIndex !== index) {
			this.toggleExpand(this.openIndex, false);
		}

		// handle menu at called index
		if (this.topLevelNodes[index]) {
			this.openIndex = expanded ? index : null;
			this.topLevelNodes[index].setAttribute('aria-expanded', expanded);
			this.toggleMenu(this.controlledNodes[index], expanded);
		}
	}

	toggleMenu(domNode, show) {
		if (domNode) {
			//domNode.style.display = show ? 'block' : 'none';
			domNode.classList.remove("show");
			if(show) {
				domNode.classList.add("show");
			}
		}
	}

	updateKeyControls(useArrowKeys) {
		this.useArrowKeys = useArrowKeys;
	}
}

/*
 *   This content is licensed according to the W3C Software License at
 *   https://www.w3.org/Consortium/Legal/2015/copyright-software-and-document
 *
 *   Simple accordion pattern example
 */

'use strict';

class Accordion {
	constructor(domNode) {
		this.rootEl = domNode;
		this.buttonEl = this.rootEl.querySelector('button[aria-expanded]');

		const controlsId = this.buttonEl.getAttribute('aria-controls');
		this.contentEl = document.getElementById(controlsId);

		this.open = this.buttonEl.getAttribute('aria-expanded') === 'true';

		// add event listeners
		this.buttonEl.addEventListener('click', this.onButtonClick.bind(this));
	}

	onButtonClick() {
		this.toggle(!this.open);
	}

	toggle(open) {
		// don't do anything if the open state doesn't change
		if (open === this.open) {
			return;
		}

		// update the internal state
		this.open = open;

		// handle DOM updates
		this.buttonEl.setAttribute('aria-expanded', `${open}`);
		if (open) {
			this.contentEl.removeAttribute('hidden');
		} else {
			this.contentEl.setAttribute('hidden', '');
		}
	}

	// Add public open and close methods for convenience
	open() {
		this.toggle(true);
	}

	close() {
		this.toggle(false);
	}
}

// Dialogs

'use strict';

var aria = aria || {};

aria.Utils = aria.Utils || {};

(function () {
	/*
	 * When util functions move focus around, set this true so the focus listener
	 * can ignore the events.
	 */
	aria.Utils.IgnoreUtilFocusChanges = false;

	aria.Utils.dialogOpenClass = 'has-dialog';

	/**
	 * @description Set focus on descendant nodes until the first focusable element is
	 *       found.
	 * @param element
	 *          DOM node for which to find the first focusable descendant.
	 * @returns {boolean}
	 *  true if a focusable element is found and focus is set.
	 */
	aria.Utils.focusFirstDescendant = function (element) {
		for (var i = 0; i < element.childNodes.length; i++) {
			var child = element.childNodes[i];
			if (
				aria.Utils.attemptFocus(child) ||
				aria.Utils.focusFirstDescendant(child)
			) {
				return true;
			}
		}
		return false;
	}; // end focusFirstDescendant

	/**
	 * @description Find the last descendant node that is focusable.
	 * @param element
	 *          DOM node for which to find the last focusable descendant.
	 * @returns {boolean}
	 *  true if a focusable element is found and focus is set.
	 */
	aria.Utils.focusLastDescendant = function (element) {
		for (var i = element.childNodes.length - 1; i >= 0; i--) {
			var child = element.childNodes[i];
			if (
				aria.Utils.attemptFocus(child) ||
				aria.Utils.focusLastDescendant(child)
			) {
				return true;
			}
		}
		return false;
	}; // end focusLastDescendant

	/**
	 * @description Set Attempt to set focus on the current node.
	 * @param element
	 *          The node to attempt to focus on.
	 * @returns {boolean}
	 *  true if element is focused.
	 */
	aria.Utils.attemptFocus = function (element) {
		if (!aria.Utils.isFocusable(element)) {
			return false;
		}

		aria.Utils.IgnoreUtilFocusChanges = true;
		try {
			element.focus();
		} catch (e) {
			// continue regardless of error
		}
		aria.Utils.IgnoreUtilFocusChanges = false;
		return document.activeElement === element;
	}; // end attemptFocus

	/* Modals can open modals. Keep track of them with this array. */
	aria.OpenDialogList = aria.OpenDialogList || new Array(0);

	/**
	 * @returns {object} the last opened dialog (the current dialog)
	 */
	aria.getCurrentDialog = function () {
		if (aria.OpenDialogList && aria.OpenDialogList.length) {
			return aria.OpenDialogList[aria.OpenDialogList.length - 1];
		}
	};

	aria.closeCurrentDialog = function () {
		var currentDialog = aria.getCurrentDialog();
		if (currentDialog) {
			currentDialog.close();
			return true;
		}

		return false;
	};

	aria.handleEscape = function (event) {
		var key = event.which || event.keyCode;

		if (key === aria.KeyCode.ESC && aria.closeCurrentDialog()) {
			event.stopPropagation();
		}
	};

	document.addEventListener('keyup', aria.handleEscape);

	/**
	 * @class
	 * @description Dialog object providing modal focus management.
	 *
	 * Assumptions: The element serving as the dialog container is present in the
	 * DOM and hidden. The dialog container has role='dialog'.
	 * @param dialogId
	 *          The ID of the element serving as the dialog container.
	 * @param focusAfterClosed
	 *          Either the DOM node or the ID of the DOM node to focus when the
	 *          dialog closes.
	 * @param focusFirst
	 *          Optional parameter containing either the DOM node or the ID of the
	 *          DOM node to focus when the dialog opens. If not specified, the
	 *          first focusable element in the dialog will receive focus.
	 */
	aria.Dialog = function (dialogId, focusAfterClosed, focusFirst) {
		this.dialogNode = document.getElementById(dialogId);
		if (this.dialogNode === null) {
			throw new Error('No element found with id="' + dialogId + '".');
		}

		var validRoles = ['dialog', 'alertdialog'];
		var isDialog = (this.dialogNode.getAttribute('role') || '')
			.trim()
			.split(/\s+/g)
			.some(function (token) {
				return validRoles.some(function (role) {
					return token === role;
				});
			});
		if (!isDialog) {
			throw new Error(
				'Dialog() requires a DOM element with ARIA role of dialog or alertdialog.'
			);
		}

		// Wrap in an individual backdrop element if one doesn't exist
		// Native <dialog> elements use the ::backdrop pseudo-element, which
		// works similarly.
		var backdropClass = 'dialog-backdrop';
		if (this.dialogNode.parentNode.classList.contains(backdropClass)) {
			this.backdropNode = this.dialogNode.parentNode;
		} else {
			this.backdropNode = document.createElement('div');
			this.backdropNode.className = backdropClass;
			this.dialogNode.parentNode.insertBefore(
				this.backdropNode,
				this.dialogNode
			);
			this.backdropNode.appendChild(this.dialogNode);
		}
		this.backdropNode.classList.add('active');

		// Disable scroll on the body element
		document.body.classList.add(aria.Utils.dialogOpenClass);

		if (typeof focusAfterClosed === 'string') {
			this.focusAfterClosed = document.getElementById(focusAfterClosed);
		} else if (typeof focusAfterClosed === 'object') {
			this.focusAfterClosed = focusAfterClosed;
		} else {
			throw new Error(
				'the focusAfterClosed parameter is required for the aria.Dialog constructor.'
			);
		}

		if (typeof focusFirst === 'string') {
			this.focusFirst = document.getElementById(focusFirst);
		} else if (typeof focusFirst === 'object') {
			this.focusFirst = focusFirst;
		} else {
			this.focusFirst = null;
		}

		// Bracket the dialog node with two invisible, focusable nodes.
		// While this dialog is open, we use these to make sure that focus never
		// leaves the document even if dialogNode is the first or last node.
		var preDiv = document.createElement('div');
		this.preNode = this.dialogNode.parentNode.insertBefore(
			preDiv,
			this.dialogNode
		);
		this.preNode.tabIndex = 0;
		var postDiv = document.createElement('div');
		this.postNode = this.dialogNode.parentNode.insertBefore(
			postDiv,
			this.dialogNode.nextSibling
		);
		this.postNode.tabIndex = 0;

		// If this modal is opening on top of one that is already open,
		// get rid of the document focus listener of the open dialog.
		if (aria.OpenDialogList.length > 0) {
			aria.getCurrentDialog().removeListeners();
		}

		this.addListeners();
		aria.OpenDialogList.push(this);
		this.clearDialog();
		this.dialogNode.className = 'default_dialog'; // make visible

		if (this.focusFirst) {
			this.focusFirst.focus();
		} else {
			aria.Utils.focusFirstDescendant(this.dialogNode);
		}

		this.lastFocus = document.activeElement;
	}; // end Dialog constructor

	aria.Dialog.prototype.clearDialog = function () {
		Array.prototype.map.call(
			this.dialogNode.querySelectorAll('input'),
			function (input) {
				input.value = '';
			}
		);
	};

	/**
	 * @description
	 *  Hides the current top dialog,
	 *  removes listeners of the top dialog,
	 *  restore listeners of a parent dialog if one was open under the one that just closed,
	 *  and sets focus on the element specified for focusAfterClosed.
	 */
	aria.Dialog.prototype.close = function () {
		aria.OpenDialogList.pop();
		this.removeListeners();
		aria.Utils.remove(this.preNode);
		aria.Utils.remove(this.postNode);
		this.dialogNode.className = 'hidden';
		this.backdropNode.classList.remove('active');
		this.focusAfterClosed.focus();

		// If a dialog was open underneath this one, restore its listeners.
		if (aria.OpenDialogList.length > 0) {
			aria.getCurrentDialog().addListeners();
		} else {
			document.body.classList.remove(aria.Utils.dialogOpenClass);
		}
	}; // end close

	/**
	 * @description
	 *  Hides the current dialog and replaces it with another.
	 * @param newDialogId
	 *  ID of the dialog that will replace the currently open top dialog.
	 * @param newFocusAfterClosed
	 *  Optional ID or DOM node specifying where to place focus when the new dialog closes.
	 *  If not specified, focus will be placed on the element specified by the dialog being replaced.
	 * @param newFocusFirst
	 *  Optional ID or DOM node specifying where to place focus in the new dialog when it opens.
	 *  If not specified, the first focusable element will receive focus.
	 */
	aria.Dialog.prototype.replace = function (
		newDialogId,
		newFocusAfterClosed,
		newFocusFirst
	) {
		aria.OpenDialogList.pop();
		this.removeListeners();
		aria.Utils.remove(this.preNode);
		aria.Utils.remove(this.postNode);
		this.dialogNode.className = 'hidden';
		this.backdropNode.classList.remove('active');

		var focusAfterClosed = newFocusAfterClosed || this.focusAfterClosed;
		new aria.Dialog(newDialogId, focusAfterClosed, newFocusFirst);
	}; // end replace

	aria.Dialog.prototype.addListeners = function () {
		document.addEventListener('focus', this.trapFocus, true);
	}; // end addListeners

	aria.Dialog.prototype.removeListeners = function () {
		document.removeEventListener('focus', this.trapFocus, true);
	}; // end removeListeners

	aria.Dialog.prototype.trapFocus = function (event) {
		if (aria.Utils.IgnoreUtilFocusChanges) {
			return;
		}
		var currentDialog = aria.getCurrentDialog();
		if (currentDialog.dialogNode.contains(event.target)) {
			currentDialog.lastFocus = event.target;
		} else {
			aria.Utils.focusFirstDescendant(currentDialog.dialogNode);
			if (currentDialog.lastFocus == document.activeElement) {
				aria.Utils.focusLastDescendant(currentDialog.dialogNode);
			}
			currentDialog.lastFocus = document.activeElement;
		}
	}; // end trapFocus

	window.openDialog = function (dialogId, focusAfterClosed, focusFirst) {
		new aria.Dialog(dialogId, focusAfterClosed, focusFirst);
	};

	window.closeDialog = function (closeButton) {
		var topDialog = aria.getCurrentDialog();
		if (topDialog.dialogNode.contains(closeButton)) {
			topDialog.close();
		}
	}; // end closeDialog

	window.replaceDialog = function (
		newDialogId,
		newFocusAfterClosed,
		newFocusFirst
	) {
		var topDialog = aria.getCurrentDialog();
		if (topDialog.dialogNode.contains(document.activeElement)) {
			topDialog.replace(newDialogId, newFocusAfterClosed, newFocusFirst);
		}
	}; // end replaceDialog
})();

// Aria utils

var aria = aria || {};

/**
 * @description
 *  Key code constants
 */
aria.KeyCode = {
	BACKSPACE: 8,
	TAB: 9,
	RETURN: 13,
	SHIFT: 16,
	ESC: 27,
	SPACE: 32,
	PAGE_UP: 33,
	PAGE_DOWN: 34,
	END: 35,
	HOME: 36,
	LEFT: 37,
	UP: 38,
	RIGHT: 39,
	DOWN: 40,
	DELETE: 46,
};

aria.Utils = aria.Utils || {};

// Polyfill src https://developer.mozilla.org/en-US/docs/Web/API/Element/matches
aria.Utils.matches = function (element, selector) {
	if (!Element.prototype.matches) {
		Element.prototype.matches =
			Element.prototype.matchesSelector ||
			Element.prototype.mozMatchesSelector ||
			Element.prototype.msMatchesSelector ||
			Element.prototype.oMatchesSelector ||
			Element.prototype.webkitMatchesSelector ||
			function (s) {
				var matches = element.parentNode.querySelectorAll(s);
				var i = matches.length;
				while (--i >= 0 && matches.item(i) !== this) {
					// empty
				}
				return i > -1;
			};
	}

	return element.matches(selector);
};

aria.Utils.remove = function (item) {
	if (item.remove && typeof item.remove === 'function') {
		return item.remove();
	}
	if (
		item.parentNode &&
		item.parentNode.removeChild &&
		typeof item.parentNode.removeChild === 'function'
	) {
		return item.parentNode.removeChild(item);
	}
	return false;
};

aria.Utils.isFocusable = function (element) {
	if (element.tabIndex < 0) {
		return false;
	}

	if (element.disabled) {
		return false;
	}

	switch (element.nodeName) {
		case 'A':
			return !!element.href && element.rel != 'ignore';
		case 'INPUT':
			return element.type != 'hidden';
		case 'BUTTON':
		case 'SELECT':
		case 'TEXTAREA':
			return true;
		default:
			return false;
	}
};

aria.Utils.getAncestorBySelector = function (element, selector) {
	if (!aria.Utils.matches(element, selector + ' ' + element.tagName)) {
		// Element is not inside an element that matches selector
		return null;
	}

	// Move up the DOM tree until a parent matching the selector is found
	var currentNode = element;
	var ancestor = null;
	while (ancestor === null) {
		if (aria.Utils.matches(currentNode.parentNode, selector)) {
			ancestor = currentNode.parentNode;
		} else {
			currentNode = currentNode.parentNode;
		}
	}

	return ancestor;
};

aria.Utils.hasClass = function (element, className) {
	return new RegExp('(\\s|^)' + className + '(\\s|$)').test(element.className);
};

aria.Utils.addClass = function (element, className) {
	if (!aria.Utils.hasClass(element, className)) {
		element.className += ' ' + className;
	}
};

aria.Utils.removeClass = function (element, className) {
	var classRegex = new RegExp('(\\s|^)' + className + '(\\s|$)');
	element.className = element.className.replace(classRegex, ' ').trim();
};

aria.Utils.bindMethods = function (object /* , ...methodNames */) {
	var methodNames = Array.prototype.slice.call(arguments, 1);
	methodNames.forEach(function (method) {
		object[method] = object[method].bind(object);
	});
};
