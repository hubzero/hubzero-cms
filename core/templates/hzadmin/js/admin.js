/**
 * Admin JS — hzadmin template
 *
 * Core functionality for admin Blade views:
 * - Form submission helpers
 * - Bulk checkbox toggle
 * - Table sorting
 * - Sidebar flyout menu
 * - CSP-safe event delegation (no inline handlers needed)
 *
 * @package    hubzero-cms
 * @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */
(function () {
  'use strict';

  /* ================================================================
     Hubzero admin namespace
     ================================================================ */
  var Hubzero = window.Hubzero = window.Hubzero || {};

  /**
   * submitbutton — set the task on adminForm and submit.
   * Called by toolbar buttons via data-task attributes.
   */
  Hubzero.submitbutton = function (task) {
    var form = document.getElementById('adminForm')
            || document.getElementById('item-form');
    if (!form) return;

    var taskField = form.querySelector('input[name="task"]');
    if (taskField) {
      taskField.value = task;
    }

    // For edit forms with validation
    if (form.classList.contains('form-validate')) {
      var invalid = form.querySelectorAll(':invalid');
      if (invalid.length > 0 && task.indexOf('cancel') === -1) {
        invalid[0].focus();
        return;
      }
    }

    form.submit();
  };

  Hubzero.submitform = function (task, form) {
    form = form || document.getElementById('adminForm');
    if (!form) return;
    var taskField = form.querySelector('input[name="task"]');
    if (taskField) {
      taskField.value = task;
    }
    form.submit();
  };

  /**
   * checkAll — "check all" checkbox in table header.
   */
  Hubzero.checkAll = function (checkbox) {
    var form = checkbox.closest('form');
    if (!form) return;
    var boxes = form.querySelectorAll('input[name="id[]"], input[name="cid[]"]');
    var boxchecked = form.querySelector('input[name="boxchecked"]');
    var count = 0;

    for (var i = 0; i < boxes.length; i++) {
      boxes[i].checked = checkbox.checked;
      if (boxes[i].checked) count++;
    }

    if (boxchecked) {
      boxchecked.value = count;
    }
  };

  /**
   * isChecked — individual row checkbox, update boxchecked count.
   */
  Hubzero.isChecked = function (isItChecked) {
    var form = document.getElementById('adminForm');
    if (!form) return;
    var boxchecked = form.querySelector('input[name="boxchecked"]');
    if (!boxchecked) return;

    boxchecked.value = parseInt(boxchecked.value || 0, 10) + (isItChecked ? 1 : -1);
  };

  /**
   * tableOrdering — called by sortable column headers.
   */
  Hubzero.tableOrdering = function (order, dir, task) {
    var form = document.getElementById('adminForm');
    if (!form) return;

    var orderField = form.querySelector('input[name="filter_order"]');
    var dirField   = form.querySelector('input[name="filter_order_Dir"]');

    if (orderField) orderField.value = order;
    if (dirField) dirField.value = dir;

    Hubzero.submitform(task || '', form);
  };

  /**
   * listItemTask — used by state-toggle links in list views.
   */
  Hubzero.listItemTask = function (cbId, task) {
    var form = document.getElementById('adminForm');
    if (!form) return false;
    var cb = document.getElementById(cbId);
    if (cb) cb.checked = true;
    var boxchecked = form.querySelector('input[name="boxchecked"]');
    if (boxchecked) boxchecked.value = '1';
    Hubzero.submitform(task, form);
    return false;
  };

  /* ================================================================
     Legacy aliases — for non-Blade PHP views that still reference
     the Joomla namespace. Remove once all views are converted.
     ================================================================ */
  window.Joomla = window.Joomla || {};
  Joomla.submitbutton = Hubzero.submitbutton;
  Joomla.submitform   = Hubzero.submitform;
  Joomla.checkAll     = Hubzero.checkAll;
  Joomla.isChecked    = Hubzero.isChecked;
  Joomla.tableOrdering = Hubzero.tableOrdering;
  window.listItemTask  = Hubzero.listItemTask;

  /* ================================================================
     Sidebar flyout menu
     ================================================================ */
  function initSidebarMenu() {
    var menu = document.getElementById('menu');
    if (!menu || !menu.closest('.drawer-side')) return;

    var sidebar = menu.closest('aside') || menu.closest('.drawer-side');
    var flyout = null;
    var activeLi = null;

    var sections = [];
    for (var i = 0; i < menu.children.length; i++) {
      var child = menu.children[i];
      if (child.tagName === 'LI' && child.classList.contains('node')) {
        sections.push(child);
      }
    }

    sections.forEach(function (li) {
      var sub = li.querySelector('ul');
      if (sub) sub.style.display = 'none';

      var link = li.children[0];
      if (!link || link.tagName !== 'A') return;

      var svgNS = 'http://www.w3.org/2000/svg';
      var chevron = document.createElementNS(svgNS, 'svg');
      chevron.setAttribute('class', 'admin-menu-chevron');
      chevron.setAttribute('viewBox', '0 0 20 20');
      chevron.setAttribute('fill', 'currentColor');
      chevron.setAttribute('aria-hidden', 'true');
      var path = document.createElementNS(svgNS, 'path');
      path.setAttribute('fill-rule', 'evenodd');
      path.setAttribute('d', 'M7.21 14.77a.75.75 0 0 1 .02-1.06L11.168 10 7.23 6.29a.75.75 0 1 1 1.04-1.08l4.5 4.25a.75.75 0 0 1 0 1.08l-4.5 4.25a.75.75 0 0 1-1.06-.02Z');
      path.setAttribute('clip-rule', 'evenodd');
      chevron.appendChild(path);
      link.style.display = 'flex';
      link.style.alignItems = 'center';
      link.style.gap = '0.375rem';
      link.appendChild(chevron);

      link.addEventListener('click', function (e) {
        e.preventDefault();
        e.stopPropagation();
        if (activeLi === li) {
          closeFlyout();
        } else {
          openFlyout(li);
        }
      });
    });

    function openFlyout(li) {
      closeFlyout();
      activeLi = li;
      li.classList.add('flyout-active');
      var trigger = li.querySelector('a[aria-expanded]');
      if (trigger) trigger.setAttribute('aria-expanded', 'true');

      var link = li.children[0];
      var sub = li.querySelector('ul');
      if (!sub) return;

      var href = link ? link.getAttribute('href') : null;
      var title = '';
      if (link) {
        // Extract text, skipping SVG child nodes
        for (var n = 0; n < link.childNodes.length; n++) {
          if (link.childNodes[n].nodeType === 3) {
            var t = link.childNodes[n].textContent.trim();
            if (t) { title = t; break; }
          }
        }
      }

      flyout = document.createElement('div');
      flyout.className = 'admin-flyout';

      var header = document.createElement('div');
      header.className = 'admin-flyout-header';

      if (href && href !== '#') {
        var titleLink = document.createElement('a');
        titleLink.href = href;
        titleLink.textContent = title;
        titleLink.className = 'admin-flyout-title-link';
        header.appendChild(titleLink);
      } else {
        header.textContent = title;
      }

      flyout.appendChild(header);

      var clonedUl = sub.cloneNode(true);
      clonedUl.style.display = '';
      clonedUl.removeAttribute('id');
      clonedUl.className = 'admin-flyout-list';

      var nestedHidden = clonedUl.querySelectorAll('ul');
      for (var i = 0; i < nestedHidden.length; i++) {
        nestedHidden[i].style.display = '';
      }

      flyout.appendChild(clonedUl);

      flyout.style.position = 'fixed';
      flyout.style.zIndex = '9999';

      document.body.appendChild(flyout);
      positionFlyout(li);
    }

    function positionFlyout(li) {
      if (!flyout) return;

      var sidebarRect = sidebar.getBoundingClientRect();
      var liRect = li.getBoundingClientRect();
      var gap = 6;
      var bottomBuffer = 60;
      var viewportH = window.innerHeight;

      flyout.style.left = (sidebarRect.right + gap) + 'px';
      flyout.style.maxHeight = (viewportH - bottomBuffer - 8) + 'px';

      var idealTop = Math.max(liRect.top - 4, 8);
      flyout.style.top = idealTop + 'px';

      var flyoutRect = flyout.getBoundingClientRect();
      var finalTop = flyoutRect.top;

      if (flyoutRect.bottom > viewportH - bottomBuffer) {
        finalTop = Math.max(8, viewportH - flyoutRect.height - bottomBuffer);
        flyout.style.top = finalTop + 'px';
      }

      var liCenter = liRect.top + liRect.height / 2;
      var notchTop = liCenter - finalTop - 6;
      flyoutRect = flyout.getBoundingClientRect();
      notchTop = Math.max(12, Math.min(notchTop, flyoutRect.height - 20));
      flyout.style.setProperty('--notch-top', notchTop + 'px');
    }

    function closeFlyout() {
      if (flyout) {
        flyout.remove();
        flyout = null;
      }
      if (activeLi) {
        activeLi.classList.remove('flyout-active');
        var trigger = activeLi.querySelector('a[aria-expanded]');
        if (trigger) trigger.setAttribute('aria-expanded', 'false');
        activeLi = null;
      }
    }

    document.addEventListener('click', function (e) {
      if (!flyout) return;
      if (flyout.contains(e.target)) return;
      if (activeLi && activeLi.contains(e.target)) return;
      closeFlyout();
    });

    document.addEventListener('keydown', function (e) {
      if (e.key === 'Escape') closeFlyout();
    });

    window.addEventListener('resize', function () {
      if (activeLi) positionFlyout(activeLi);
    });

    // Mark the section containing the active page
    var activeItem = menu.querySelector('li.active');
    if (activeItem && activeItem.parentNode !== menu) {
      var el = activeItem;
      while (el && el !== menu) {
        if (el.parentNode === menu && el.classList.contains('node')) {
          el.classList.add('section-active');
          break;
        }
        el = el.parentNode;
      }
    }
  }

  /* ================================================================
     Drawer toggle — hamburger collapses/expands sidebar on desktop
     ================================================================ */
  function initDrawerToggle() {
    // Hamburger is mobile-only (hidden on lg: via CSS).
    // The <label for="admin-drawer"> toggles the daisyUI checkbox natively.
    // No extra JS needed — drawer opens/closes via CSS :checked state.
  }

  /* ================================================================
     Toolbar button handlers
     ================================================================ */
  function initToolbar() {
    document.addEventListener('click', function (e) {
      var btn = e.target.closest('.toolbar');
      if (!btn) return;

      if (btn.classList.contains('toolbar-popup')) {
        e.preventDefault();
        openToolbarPopup(btn);
        return;
      }

      if (btn.classList.contains('toolbar-submit') || btn.classList.contains('toolbar-confirm')) {
        e.preventDefault();
        var task = btn.getAttribute('data-task') || '';

        if (btn.classList.contains('toolbar-list')) {
          var form = document.getElementById('adminForm');
          var boxchecked = form ? form.querySelector('input[name="boxchecked"]') : null;
          if (!boxchecked || parseInt(boxchecked.value, 10) < 1) {
            alert(btn.getAttribute('data-message') || 'Please select an item from the list.');
            return;
          }
        }

        if (btn.classList.contains('toolbar-confirm')) {
          var msg = btn.getAttribute('data-confirm') || 'Are you sure?';
          if (!confirm(msg)) return;
        }

        Hubzero.submitbutton(task);
      }
    });
  }

  /**
   * Toolbar popup — lightweight modal with iframe.
   */
  function openToolbarPopup(btn) {
    var url    = btn.getAttribute('data-href') || btn.getAttribute('href') || '';
    var title  = btn.getAttribute('data-title') || '';
    var width  = parseInt(btn.getAttribute('data-width'), 10) || 875;
    var height = parseInt(btn.getAttribute('data-height'), 10) || 550;

    if (!url || url === '#') return;

    var backdrop = document.createElement('div');
    backdrop.className = 'admin-popup-backdrop';

    var dialog = document.createElement('div');
    dialog.className = 'admin-popup-dialog';
    dialog.style.width  = Math.min(width, window.innerWidth - 40) + 'px';
    dialog.style.height = Math.min(height, window.innerHeight - 40) + 'px';

    var header = document.createElement('div');
    header.className = 'admin-popup-header';

    var titleEl = document.createElement('span');
    titleEl.className = 'admin-popup-title';
    titleEl.textContent = title;
    header.appendChild(titleEl);

    var closeBtn = document.createElement('button');
    closeBtn.className = 'admin-popup-close';
    closeBtn.setAttribute('aria-label', 'Close');
    closeBtn.innerHTML = '<svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" /></svg>';
    header.appendChild(closeBtn);

    dialog.appendChild(header);

    var iframe = document.createElement('iframe');
    iframe.className = 'admin-popup-iframe';
    iframe.setAttribute('src', url);
    iframe.setAttribute('frameborder', '0');
    dialog.appendChild(iframe);

    backdrop.appendChild(dialog);
    document.body.appendChild(backdrop);

    document.body.style.overflow = 'hidden';

    requestAnimationFrame(function () {
      backdrop.classList.add('admin-popup-open');
    });

    function close() {
      backdrop.classList.remove('admin-popup-open');
      setTimeout(function () {
        backdrop.remove();
        document.body.style.overflow = '';
      }, 200);
    }

    closeBtn.addEventListener('click', close);
    backdrop.addEventListener('click', function (e) {
      if (e.target === backdrop) close();
    });
    document.addEventListener('keydown', function handler(e) {
      if (e.key === 'Escape') {
        close();
        document.removeEventListener('keydown', handler);
      }
    });

    window.addEventListener('message', function handler(e) {
      if (e.data === 'admin-popup-close') {
        close();
        window.removeEventListener('message', handler);
      } else if (e.data === 'admin-popup-close-refresh') {
        close();
        window.removeEventListener('message', handler);
        window.location.reload();
      }
    });
  }

  /* ================================================================
     Pagination
     ================================================================ */
  function initPagination() {
    var pages = document.querySelectorAll('.pagination a');
    for (var i = 0; i < pages.length; i++) {
      pages[i].addEventListener('click', function (e) {
        e.preventDefault();
        var form = document.getElementById('adminForm');
        if (!form) return;
        var prefix = this.getAttribute('data-prefix') || '';
        var field = form.querySelector('input[name="' + prefix + 'limitstart"]');
        if (field) {
          field.value = parseInt(this.getAttribute('data-start'), 10) || 0;
        }
        form.submit();
      });
    }

    var selects = document.querySelectorAll('.pagination select');
    for (var j = 0; j < selects.length; j++) {
      selects[j].addEventListener('change', function () {
        var form = this.closest('form') || document.getElementById('adminForm');
        if (form) form.submit();
      });
    }
  }

  /* ================================================================
     CSP-safe event delegation

     Replaces inline onclick/onchange handlers with data-* attributes:
       data-check-all        — "check all" checkbox in table header
       data-check-item       — individual row checkbox
       data-submit-on-change — auto-submit form when select changes
       data-clear-search     — clear search input and submit (value = input id)
       data-order-btn        — ordering up/down (with data-cb, data-task)
     ================================================================ */
  function initDelegatedHandlers() {
    document.addEventListener('click', function (e) {
      // Check-all toggle
      var checkAll = e.target.closest('[data-check-all]');
      if (checkAll && checkAll.tagName === 'INPUT') {
        Hubzero.checkAll(checkAll);
        return;
      }

      // Individual row checkbox
      var checkItem = e.target.closest('[data-check-item]');
      if (checkItem && checkItem.tagName === 'INPUT') {
        Hubzero.isChecked(checkItem.checked);
        return;
      }

      // Clear search button
      var clearSearch = e.target.closest('[data-clear-search]');
      if (clearSearch) {
        e.preventDefault();
        var targetId = clearSearch.getAttribute('data-clear-search') || 'filter_search';
        var input = document.getElementById(targetId);
        if (input) input.value = '';
        var form = clearSearch.closest('form') || document.getElementById('adminForm');
        if (form) form.submit();
        return;
      }

      // List item task (state toggle links)
      var listTask = e.target.closest('[data-list-item-task]');
      if (listTask) {
        e.preventDefault();
        var cbId = listTask.getAttribute('data-cb');
        var task = listTask.getAttribute('data-task');
        Hubzero.listItemTask(cbId, task);
        return;
      }

      // Order up/down buttons
      var orderBtn = e.target.closest('[data-order-btn]');
      if (orderBtn) {
        e.preventDefault();
        var form = document.getElementById('adminForm');
        if (!form) return;
        var cbId = orderBtn.getAttribute('data-cb');
        var task = orderBtn.getAttribute('data-task');
        var cb = document.getElementById(cbId);
        if (cb) cb.checked = true;
        var boxchecked = form.querySelector('input[name="boxchecked"]');
        if (boxchecked) boxchecked.value = '1';
        var taskField = form.querySelector('input[name="task"]');
        if (taskField) taskField.value = task;
        form.submit();
        return;
      }

      // Confirm before navigation — <a data-confirm="message">
      var confirmLink = e.target.closest('a[data-confirm]');
      if (confirmLink) {
        if (!confirm(confirmLink.getAttribute('data-confirm'))) {
          e.preventDefault();
        }
        return;
      }

      // History back — <button data-history-back>
      var backBtn = e.target.closest('[data-history-back]');
      if (backBtn) {
        e.preventDefault();
        history.back();
        return;
      }

      // Checkbox toggle — <button data-check-toggle=".selector" data-check-state="true|false|toggle">
      var toggleBtn = e.target.closest('[data-check-toggle]');
      if (toggleBtn) {
        e.preventDefault();
        var selector = toggleBtn.getAttribute('data-check-toggle');
        var state = toggleBtn.getAttribute('data-check-state') || 'toggle';
        document.querySelectorAll(selector).forEach(function (el) {
          if (state === 'true') el.checked = true;
          else if (state === 'false') el.checked = false;
          else el.checked = !el.checked;
        });
        return;
      }

      // Submit task — <button data-submit-task="taskname">
      var submitTaskBtn = e.target.closest('[data-submit-task]');
      if (submitTaskBtn) {
        e.preventDefault();
        Hubzero.submitbutton(submitTaskBtn.getAttribute('data-submit-task'));
        return;
      }

      // Batch clear — <button data-batch-clear>
      var batchClear = e.target.closest('[data-batch-clear]');
      if (batchClear) {
        e.preventDefault();
        var form = batchClear.closest('form') || document.getElementById('adminForm');
        if (form) {
          form.querySelectorAll('select[name^="batch"]').forEach(function (sel) {
            sel.value = '';
          });
          var moveRadio = form.querySelector('input[name="batch[move_copy]"][value="m"]');
          if (moveRadio) moveRadio.checked = true;
        }
        return;
      }

      // Parent callback — <a data-parent-callback="fnName" data-callback-args='["arg1","arg2"]'>
      var parentCb = e.target.closest('[data-parent-callback]');
      if (parentCb) {
        e.preventDefault();
        var fn = parentCb.getAttribute('data-parent-callback');
        var args = JSON.parse(parentCb.getAttribute('data-callback-args') || '[]');
        if (window.parent && window.parent[fn]) {
          window.parent[fn].apply(window.parent, args);
        }
        return;
      }

      // Parent redirect — <a data-parent-redirect href="url">
      // In an iframe, redirects the parent window to the link's href.
      var parentRedirect = e.target.closest('a[data-parent-redirect]');
      if (parentRedirect) {
        if (window.parent !== window) {
          e.preventDefault();
          window.parent.location = parentRedirect.getAttribute('href');
        }
        return;
      }

      // Generic data-onclick fallback — execute mapped handler
      var genericClick = e.target.closest('[data-onclick]');
      if (genericClick) {
        var handler = genericClick.getAttribute('data-onclick');
        if (/^this\.form\.submit\(\)$/.test(handler)) {
          e.preventDefault();
          var form = genericClick.closest('form') || document.getElementById('adminForm');
          if (form) form.submit();
        }
      }
    });

    // Sort column headers (.grid-order links from Html::grid('sort'))
    document.addEventListener('click', function (e) {
      var sortLink = e.target.closest('a.grid-order');
      if (sortLink) {
        e.preventDefault();
        var order = sortLink.getAttribute('data-order');
        var dir   = sortLink.getAttribute('data-direction') || 'asc';
        var task  = sortLink.getAttribute('data-task') || '';
        Hubzero.tableOrdering(order, dir, task);
      }
    });

    // Auto-submit on change
    document.addEventListener('change', function (e) {
      // File input display — <input type="file" data-file-display="target-id">
      var fileInput = e.target.closest('input[type="file"][data-file-display]');
      if (fileInput) {
        var targetId = fileInput.getAttribute('data-file-display');
        var label = document.getElementById(targetId);
        if (label) {
          label.textContent = fileInput.files.length ? fileInput.files[0].name : '';
        }
        return;
      }

      // data-submit-task on select — set task and submit via Hubzero.submitbutton
      var taskSelect = e.target.closest('select[data-submit-task]');
      if (taskSelect) {
        Hubzero.submitbutton(taskSelect.getAttribute('data-submit-task'));
        return;
      }

      var autoSubmit = e.target.closest('[data-submit-on-change]');
      if (autoSubmit) {
        var form = autoSubmit.closest('form') || document.getElementById('adminForm');
        if (form) form.submit();
        return;
      }

      // Generic data-onchange fallback — execute mapped handler
      var generic = e.target.closest('[data-onchange]');
      if (generic) {
        var handler = generic.getAttribute('data-onchange');
        // Known safe patterns only
        if (/^this\.form\.submit\(\)$/.test(handler)) {
          var form = generic.closest('form') || document.getElementById('adminForm');
          if (form) form.submit();
        }
      }
    });

    // Tab switching — [data-tab-target] clicks
    document.addEventListener('click', function (e) {
      var tab = e.target.closest('[data-tab-target]');
      if (!tab) return;
      e.preventDefault();
      var targetId = tab.getAttribute('data-tab-target');
      var panel = document.querySelector(targetId);
      if (!panel) return;

      // Deactivate sibling tabs
      var tabList = tab.closest('[role="tablist"]');
      if (tabList) {
        tabList.querySelectorAll('.tab').forEach(function (t) {
          t.classList.remove('tab-active');
        });
      }
      tab.classList.add('tab-active');

      // Hide sibling panels, show target
      var parent = panel.parentNode;
      if (parent) {
        parent.querySelectorAll(':scope > .tab-panel').forEach(function (p) {
          p.classList.add('hidden');
        });
      }
      panel.classList.remove('hidden');
    });

    // Show first tab panel on load
    document.querySelectorAll('[role="tablist"]').forEach(function (tabList) {
      var firstTab = tabList.querySelector('[data-tab-target]');
      if (!firstTab) return;
      var targetId = firstTab.getAttribute('data-tab-target');
      var panel = document.querySelector(targetId);
      if (!panel) return;
      var parent = panel.parentNode;
      if (parent) {
        parent.querySelectorAll(':scope > .tab-panel').forEach(function (p) {
          p.classList.add('hidden');
        });
      }
      panel.classList.remove('hidden');
      firstTab.classList.add('tab-active');
    });

    // Pagebreak insert — <form data-pagebreak-editor="editorName">
    document.addEventListener('submit', function (e) {
      var form = e.target.closest('[data-pagebreak-editor]');
      if (!form) return;
      e.preventDefault();
      var editorName = form.getAttribute('data-pagebreak-editor');
      var title = form.querySelector('[name="title"]').value;
      var alt = form.querySelector('[name="alt"]').value;
      if (title) { title = 'title="' + title + '" '; }
      if (alt) { alt = 'alt="' + alt + '" '; }
      var tag = '<hr class="system-pagebreak" ' + title + ' ' + alt + '/>';
      if (window.parent && window.parent.jInsertEditorText) {
        window.parent.jInsertEditorText(tag, editorName);
      }
      window.parent.postMessage('admin-popup-close', '*');
    });
  }

  /* ================================================================
     Picker popup — data-picker-url buttons (Blade admin context).

     Legacy picker templates call window.parent.$.fancybox.close()
     to dismiss — we temporarily stub that method to close our overlay
     so no changes are needed to the picker template files themselves.
     ================================================================ */
  function initPickerButtons() {
    document.addEventListener('click', function (e) {
      var btn = e.target.closest('[data-picker-url]');
      if (!btn) return;
      e.preventDefault();
      var url      = btn.getAttribute('data-picker-url');
      var width    = parseInt(btn.getAttribute('data-picker-width'),  10) || 800;
      var height   = parseInt(btn.getAttribute('data-picker-height'), 10) || 500;
      var noHeader = btn.getAttribute('data-picker-no-header') === 'true';
      openPickerPopup(url, width, height, noHeader, btn);
    });

    // Inside iframe: handle [data-article-select] clicks and post to parent
    document.addEventListener('click', function (e) {
      var link = e.target.closest('[data-article-select]');
      if (!link) return;
      e.preventDefault();
      var msg = {
        type:  'picker-select',
        id:    link.getAttribute('data-id'),
        title: link.getAttribute('data-title'),
        catid: link.getAttribute('data-catid') || ''
      };
      if (window.parent && window.parent !== window) {
        window.parent.postMessage(msg, '*');
      }
    });
  }

  function openPickerPopup(url, width, height, noHeader, triggerBtn) {
    var backdrop = document.createElement('div');
    backdrop.className = 'admin-popup-backdrop';

    var dialog = document.createElement('div');
    dialog.className = 'admin-popup-dialog';
    dialog.style.width  = Math.min(width, window.innerWidth  - 40) + 'px';
    dialog.style.height = Math.min(
      noHeader ? height : height + 36, window.innerHeight - 40
    ) + 'px';

    var closeBtn;

    if (!noHeader) {
      var header = document.createElement('div');
      header.className = 'admin-popup-header';

      closeBtn = document.createElement('button');
      closeBtn.className = 'admin-popup-close';
      closeBtn.setAttribute('type', 'button');
      closeBtn.setAttribute('aria-label', 'Close');
      closeBtn.innerHTML = '<svg class="w-4 h-4" fill="none" viewBox="0 0 24 24"'
        + ' stroke-width="2" stroke="currentColor">'
        + '<path stroke-linecap="round" stroke-linejoin="round"'
        + ' d="M6 18 18 6M6 6l12 12" /></svg>';
      header.appendChild(closeBtn);
      dialog.appendChild(header);
    }

    var iframe = document.createElement('iframe');
    iframe.className = 'admin-popup-iframe';
    iframe.src = url;
    dialog.appendChild(iframe);

    backdrop.appendChild(dialog);
    document.body.appendChild(backdrop);
    document.body.style.overflow = 'hidden';

    requestAnimationFrame(function () {
      backdrop.classList.add('admin-popup-open');
    });

    var restoreFancybox = stubFancyboxClose(close);

    // Register a dynamic jSelect* callback if the trigger button
    // specifies data-picker-callback. Legacy picker templates call
    // window.parent.jSelectUser_FIELDID(id, title) via the
    // data-parent-callback pattern — this bridges that to our
    // value/display field update + close logic.
    var callbackName = triggerBtn
      ? triggerBtn.getAttribute('data-picker-callback')
      : null;
    var restoreCallback = null;
    if (callbackName) {
      var prevFn = window[callbackName];
      window[callbackName] = function (id, title) {
        if (triggerBtn) {
          var valField = triggerBtn.getAttribute('data-value-field');
          var dispField = triggerBtn.getAttribute('data-display-field');
          if (valField) {
            var valEl = document.getElementById(valField);
            if (valEl) valEl.value = id || '';
          }
          if (dispField) {
            var dispEl = document.getElementById(dispField);
            if (dispEl) dispEl.value = title || '';
          }
        }
        close();
      };
      restoreCallback = function () {
        if (prevFn) {
          window[callbackName] = prevFn;
        } else {
          delete window[callbackName];
        }
      };
    }

    function close() {
      restoreFancybox();
      if (restoreCallback) restoreCallback();
      window.removeEventListener('message', msgHandler);
      backdrop.classList.remove('admin-popup-open');
      setTimeout(function () {
        backdrop.remove();
        document.body.style.overflow = '';
      }, 200);
    }

    // Listen for picker-select messages from the iframe
    function msgHandler(e) {
      if (!e.data || e.data.type !== 'picker-select') return;
      if (triggerBtn) {
        var valField = triggerBtn.getAttribute('data-value-field');
        var dispField = triggerBtn.getAttribute('data-display-field');
        if (valField) {
          var valEl = document.getElementById(valField);
          if (valEl) valEl.value = e.data.id || '';
        }
        if (dispField) {
          var dispEl = document.getElementById(dispField);
          if (dispEl) dispEl.value = e.data.title || '';
        }
      }
      close();
    }
    window.addEventListener('message', msgHandler);

    if (closeBtn) closeBtn.addEventListener('click', close);
    backdrop.addEventListener('click', function (e) {
      if (e.target === backdrop) close();
    });
    document.addEventListener('keydown', function escHandler(e) {
      if (e.key === 'Escape') {
        close();
        document.removeEventListener('keydown', escHandler);
      }
    });
  }

  /**
   * Temporarily replace jQuery.fancybox.close with closeFn so that legacy
   * picker templates calling window.parent.$.fancybox.close() close our
   * overlay instead. Returns a restore function.
   */
  function stubFancyboxClose(closeFn) {
    function doStub(jq) {
      var prev = jq.fancybox;
      jq.fancybox = { close: closeFn };
      return function () { if (prev) { jq.fancybox = prev; } else { delete jq.fancybox; } };
    }

    if (window.jQuery) return doStub(window.jQuery);

    // jQuery not yet present — wait, then stub.
    var restore = function () {};
    var timer = setInterval(function () {
      if (window.jQuery) {
        restore = doStub(window.jQuery);
        clearInterval(timer);
      }
    }, 30);

    return function () { clearInterval(timer); restore(); };
  }

  /* ================================================================
     CSP-safe dynamic styles

     Inline style="" attributes are blocked by strict CSP.
     Instead, views set data-* attributes and this helper applies
     the styles programmatically from external JS (which IS allowed).

       data-style-bg="#abc"           → backgroundColor
       data-style-width="75%"        → width
       data-style-height="200px"     → height
       data-style-min-height="600px" → minHeight
       data-style-color="#f00"       → color
       data-style-border-radius="8px"→ borderRadius
     ================================================================ */
  function initDynamicStyles() {
    var attrMap = {
      'data-style-bg':            'backgroundColor',
      'data-style-width':         'width',
      'data-style-height':        'height',
      'data-style-min-height':    'minHeight',
      'data-style-color':         'color',
      'data-style-border-radius': 'borderRadius',
      'data-style-border-color':       'borderColor',
      'data-style-border-left-color':  'borderLeftColor',
      'data-style-padding-left':       'paddingLeft'
    };

    Object.keys(attrMap).forEach(function (attr) {
      document.querySelectorAll('[' + attr + ']').forEach(function (el) {
        el.style[attrMap[attr]] = el.getAttribute(attr);
      });
    });
  }

  /* ================================================================
     Flatpickr date/time pickers  (data-flatpickr="date|datetime")
     ================================================================ */
  function initFlatpickr() {
    if (typeof flatpickr === 'undefined') return;
    document.querySelectorAll('[data-flatpickr]').forEach(function (el) {
      if (el._flatpickr) return; // already initialized
      var mode = el.getAttribute('data-flatpickr');
      var opts = mode === 'datetime'
        ? { enableTime: true, time_24hr: true, dateFormat: 'Y-m-d H:i:S', allowInput: true }
        : { dateFormat: 'Y-m-d', allowInput: true };
      flatpickr(el, opts);
    });
  }

  /* ================================================================
     Behavior init — CSP-safe counterparts for Behavior:: PHP methods.
     Each function checks if the required library is loaded, scans for
     target elements, and runs the init. Marked with data-init to
     prevent double-initialization.
     ================================================================ */
  function initBehaviorTooltip() {
    if (typeof jQuery === 'undefined' || !jQuery.ui || !jQuery.ui.tooltip) return;
    document.querySelectorAll('.hasTip:not([data-init])').forEach(function (el) {
      el.setAttribute('data-init', 'tooltip');
      jQuery(el).tooltip({
        track: true,
        show: false,
        content: function () { return jQuery(this).attr('title'); },
        create: function () {
          var tip = jQuery(this), text = tip.attr('title');
          if (text && text.indexOf('::') !== -1) {
            var parts = text.split('::');
            tip.attr('title',
              '<div class="tip-title">' + parts[0] +
              '</div><div class="tip-text">' + parts[1] + '</div>');
          } else if (text) {
            tip.attr('title', '<div class="tip-text">' + text + '</div>');
          }
        },
        tooltipClass: 'tool-tip'
      });
    });
  }

  function initBehaviorModal() {
    if (typeof jQuery === 'undefined' || !jQuery.fn.fancybox) return;
    document.querySelectorAll('a.modal:not([data-init])').forEach(function (el) {
      el.setAttribute('data-init', 'modal');
      jQuery(el).fancybox({ arrows: false, type: 'iframe', fitToView: true });
    });
  }

  function initBehaviorCaption() {
    if (typeof jQuery === 'undefined' || !jQuery.ui || !jQuery.ui.tooltip) return;
    document.querySelectorAll('img.caption:not([data-init])').forEach(function (el) {
      el.setAttribute('data-init', 'caption');
      jQuery(el).tooltip({
        position: { my: 'center bottom', at: 'center top' },
        create: function () {
          var tip = jQuery(this), text = tip.attr('title');
          if (text && text.indexOf('::') !== -1) {
            tip.attr('title', text.split('::')[1]);
          }
        },
        tooltipClass: 'tooltip'
      });
    });
  }

  function initBehaviorSwitcher() {
    if (typeof jQuery === 'undefined' || !jQuery.fn.switcher) return;
    document.querySelectorAll('[data-switcher]:not([data-init])').forEach(function (el) {
      el.setAttribute('data-init', 'switcher');
      jQuery(el).switcher();
    });
  }

  function initBehaviorMultiselect() {
    if (typeof Hubzero === 'undefined' || !Hubzero.MultiSelect) return;
    document.querySelectorAll('[data-multiselect]:not([data-init])').forEach(function (el) {
      el.setAttribute('data-init', 'multiselect');
      new Hubzero.MultiSelect(el.getAttribute('data-multiselect') || el.id);
    });
  }

  function initBehaviorColorpicker() {
    if (typeof jQuery === 'undefined' || !jQuery.fn.colpick) return;
    document.querySelectorAll('.input-colorpicker:not([data-init])').forEach(function (el) {
      el.setAttribute('data-init', 'colorpicker');
      jQuery(el).colpick({
        layout: 'hex',
        colorScheme: 'dark',
        onChange: function (hsb, hex, rgb, el, bySetColor) {
          if (!bySetColor) jQuery(el).val('#' + hex);
        }
      }).keyup(function () {
        jQuery(this).colpickSetColor(this.value);
      });
    });
  }

  function initBehaviorKeepalive() {
    var meta = document.querySelector('meta[name="behavior-keepalive"]');
    if (!meta) return;
    var interval = parseInt(meta.getAttribute('content'), 10);
    if (!interval || interval <= 0) return;
    (function keepAlive() {
      fetch('index.php', { credentials: 'same-origin' })
        .finally(function () { setTimeout(keepAlive, interval); });
    })();
  }

  function initBehaviorHighlighter() {
    var meta = document.querySelector('meta[name="behavior-highlight"]');
    if (!meta) return;
    if (typeof jQuery === 'undefined' || !jQuery.fn.highlight) return;
    try {
      var config = JSON.parse(meta.getAttribute('content'));
      jQuery('body').highlight(config.terms, {
        className: config.className || 'highlight',
        element: config.element || 'span'
      });
    } catch (e) { /* invalid JSON, skip */ }
  }

  function initBehaviorMath() {
    var meta = document.querySelector('meta[name="behavior-math"]');
    if (!meta) return;
    if (typeof MathJax !== 'undefined' && MathJax.Hub) {
      MathJax.Hub.Config({
        extensions: ['tex2jax.js'],
        jax: ['input/TeX', 'output/HTML-CSS'],
        'HTML-CSS': {
          preferredFont: 'TeX',
          availableFonts: ['STIX', 'TeX'],
          linebreaks: { automatic: true },
          EqnChunk: 50
        },
        tex2jax: {
          inlineMath: [['$$', '$$']],
          displayMath: [['$$$', '$$$'], ['\\[', '\\]']],
          processEscapes: true,
          ignoreClass: 'tex2jax_ignore|dno'
        },
        TeX: {
          extensions: ['autoload-all.js', 'mediawiki-texvc.js'],
          noUndefined: { attributes: { mathcolor: 'red', mathbackground: '#FFEEEE', mathsize: '90%' } },
          Macros: { href: '{}' }
        },
        messageStyle: 'none',
        styles: { '.MathJax_Display, .MathJax_Preview, .MathJax_Preview > *': { background: 'inherit' } }
      });
    }
  }

  function initPasswordStrength() {
    document.querySelectorAll('input[data-strength-meter]:not([data-init])').forEach(function (el) {
      el.setAttribute('data-init', 'strength');
      var threshold = parseInt(el.getAttribute('data-strength-meter'), 10) || 66;

      var bar = document.createElement('div');
      bar.className = 'passwordstrength-bar';
      bar.style.cssText = 'width:100%;height:5px;margin-top:2px;background:transparent;border-radius:2px;overflow:hidden;';
      var meter = document.createElement('div');
      meter.className = 'passwordstrength-meter';
      meter.style.cssText = 'width:0;height:100%;transition:width .2s,background-color .2s;';
      bar.appendChild(meter);
      el.parentNode.insertBefore(bar, el.nextSibling);

      function strength(str) {
        var n = 0;
        if (/\d/.test(str)) n += 10;
        if (/[a-z]/.test(str)) n += 26;
        if (/[A-Z]/.test(str)) n += 26;
        if (/[^\da-zA-Z]/.test(str)) n += 33;
        return n === 0 ? 0 : Math.round(str.length * Math.log(n) / Math.log(2));
      }

      function update() {
        var s = strength(el.value);
        var ratio = Math.min(Math.round(s / threshold * 100) / 100, 1);
        var color;
        if (ratio < 0.5) {
          color = 'rgb(255,' + Math.round(255 * ratio * 2) + ',0)';
        } else {
          color = 'rgb(' + Math.round(255 * (1 - ratio) * 2) + ',255,0)';
        }
        meter.style.width = (100 * ratio) + '%';
        meter.style.backgroundColor = color;
        el.setAttribute('data-passwordstrength', s);
      }

      el.addEventListener('keyup', update);
      el.addEventListener('change', update);
      if (el.value) update();
    });
  }

  function initBehaviors() {
    initBehaviorTooltip();
    initBehaviorModal();
    initBehaviorCaption();
    initBehaviorSwitcher();
    initBehaviorMultiselect();
    initBehaviorColorpicker();
    initBehaviorKeepalive();
    initBehaviorHighlighter();
    initBehaviorMath();
    initPasswordStrength();
  }

  /* ================================================================
     Init
     ================================================================ */
  function initAll() {
    initSidebarMenu();
    initDrawerToggle();
    initToolbar();
    initPagination();
    initDelegatedHandlers();
    initPickerButtons();
    initDynamicStyles();
    initFlatpickr();
    initBehaviors();
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initAll);
  } else {
    initAll();
  }

  /* ================================================================
     jQuery alias (replaces inline <script>var jq = jQuery;</script>)
     ================================================================ */
  if (typeof jQuery !== 'undefined' && typeof window.jq === 'undefined') {
    window.jq = jQuery;
  }

})();
