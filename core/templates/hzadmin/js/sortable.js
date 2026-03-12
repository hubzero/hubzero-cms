/**
 * Lightweight sortable shim using HTML5 Drag-and-Drop.
 *
 * Implements the subset of the jQuery UI Sortable API used by HubZero admin
 * components (handle, placeholder, start/stop/update callbacks, enable).
 *
 * Provides two APIs:
 *   1. jQuery plugin: $.fn.sortable (backward compat, requires jQuery)
 *   2. Standalone:    Hubzero.sortable(container, opts)
 *
 * @package    hubzero-cms
 * @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */
(function () {
  'use strict';

  // ── Core logic (shared between jQuery plugin and standalone API) ──

  var dragItem = null;
  var placeholder = null;

  function bindItems(container, opts) {
    var handleSel = opts.handle || null;
    var items = container.children;

    for (var i = 0; i < items.length; i++) {
      var item = items[i];
      if (item.nodeType !== 1) continue;

      // If handle specified, only the handle starts drag
      if (handleSel) {
        item.removeAttribute('draggable');
        var handles = item.querySelectorAll(handleSel);
        for (var h = 0; h < handles.length; h++) {
          handles[h].style.cursor = 'grab';
          handles[h].setAttribute('draggable', 'true');
          handles[h].removeEventListener('dragstart', onHandleDragStart);
          handles[h].addEventListener('dragstart', onHandleDragStart);
        }
      } else {
        item.setAttribute('draggable', 'true');
      }

      item.removeEventListener('dragstart', onDragStart);
      item.removeEventListener('dragover', onDragOver);
      item.removeEventListener('dragenter', onDragEnter);
      item.removeEventListener('dragleave', onDragLeave);
      item.removeEventListener('drop', onDrop);
      item.removeEventListener('dragend', onDragEnd);

      item.addEventListener('dragstart', onDragStart);
      item.addEventListener('dragover', onDragOver);
      item.addEventListener('dragenter', onDragEnter);
      item.addEventListener('dragleave', onDragLeave);
      item.addEventListener('drop', onDrop);
      item.addEventListener('dragend', onDragEnd);

      item._sortContainer = container;
      item._sortOpts = opts;
    }
  }

  function onHandleDragStart(e) {
    var li = this.closest(getItemTag(this));
    if (!li) return;
    dragItem = li;
    fireDragStart(e, li);
  }

  function getItemTag(el) {
    var container = el;
    while (container && !container._sortContainer) {
      container = container.parentElement;
    }
    if (!container) return 'li';
    return container.tagName.toLowerCase() === 'tbody' ? 'tr' : 'li';
  }

  function onDragStart(e) {
    if (!this.getAttribute('draggable') && !dragItem) return;
    if (dragItem) return; // handle already set it
    dragItem = this;
    fireDragStart(e, this);
  }

  /**
   * Build the ui object for callbacks.
   * For the jQuery plugin path the item is wrapped in $(); for vanilla
   * callers the raw DOM element is returned.
   */
  function makeUi(item) {
    if (typeof jQuery !== 'undefined') {
      return { helper: jQuery(item), item: jQuery(item) };
    }
    return { helper: item, item: item };
  }

  function fireDragStart(e, item) {
    var opts = item._sortOpts || {};
    e.dataTransfer.effectAllowed = 'move';
    e.dataTransfer.setData('text/plain', ''); // Required for Firefox

    // Create placeholder
    placeholder = document.createElement(item.tagName);
    placeholder.className = opts.placeholder || 'sortable-placeholder';
    if (opts.forcePlaceholderSize !== false) {
      placeholder.style.height = item.offsetHeight + 'px';
    }

    setTimeout(function () {
      item.style.opacity = '0.4';
    }, 0);

    if (opts.start) {
      opts.start.call(item._sortContainer, e, makeUi(item));
    }
  }

  function onDragOver(e) {
    e.preventDefault();
    e.dataTransfer.dropEffect = 'move';
  }

  function onDragEnter(e) {
    e.preventDefault();
    if (!dragItem || this === dragItem || this === placeholder) return;

    var target = this;
    while (target && target._sortContainer !== dragItem._sortContainer) {
      target = target.parentElement;
    }
    if (!target || target === dragItem) return;

    var container = dragItem._sortContainer;
    var items = Array.prototype.slice.call(container.children);
    var dragIdx = items.indexOf(dragItem);
    var targetIdx = items.indexOf(target);

    if (dragIdx < 0 || targetIdx < 0) return;

    if (placeholder.parentNode) {
      placeholder.parentNode.removeChild(placeholder);
    }

    if (dragIdx < targetIdx) {
      target.parentNode.insertBefore(placeholder, target.nextSibling);
    } else {
      target.parentNode.insertBefore(placeholder, target);
    }
  }

  function onDragLeave() {
    // No-op — placeholder stays where it was placed
  }

  function onDrop(e) {
    e.preventDefault();
    e.stopPropagation();
    if (!dragItem || !placeholder || !placeholder.parentNode) return;

    placeholder.parentNode.insertBefore(dragItem, placeholder);
    cleanup();
  }

  function onDragEnd() {
    if (!dragItem) return;
    cleanup();
  }

  function cleanup() {
    if (!dragItem) return;

    var opts = dragItem._sortOpts || {};

    dragItem.style.opacity = '';

    if (opts.stop) {
      opts.stop.call(dragItem._sortContainer, null, makeUi(dragItem));
    }
    if (opts.update) {
      opts.update.call(dragItem._sortContainer, null, makeUi(dragItem));
    }

    if (placeholder && placeholder.parentNode) {
      placeholder.parentNode.removeChild(placeholder);
    }

    dragItem = null;
    placeholder = null;
  }

  // ── jQuery plugin (backward compat) ──

  if (typeof jQuery !== 'undefined' && !jQuery.fn.sortable) {
    jQuery.fn.sortable = function (optionsOrMethod) {
      return this.each(function () {
        var container = this;

        if (typeof optionsOrMethod === 'string') {
          if (optionsOrMethod === 'enable' || optionsOrMethod === 'refresh') {
            bindItems(container, container._sortOpts || {});
          }
          return;
        }

        var opts = optionsOrMethod || {};
        container._sortOpts = opts;
        bindItems(container, opts);
      });
    };
  }

  // ── Standalone vanilla API ──

  window.Hubzero = window.Hubzero || {};

  /**
   * Hubzero.sortable(container, opts)
   *
   * @param {string|Element} container  Selector or DOM element
   * @param {object}         opts       Same options as the jQuery plugin
   */
  Hubzero.sortable = function (container, opts) {
    if (typeof container === 'string') {
      container = document.querySelector(container);
    }
    if (!container) return;

    if (typeof opts === 'string') {
      // Method call: Hubzero.sortable(el, 'enable')
      if (opts === 'enable' || opts === 'refresh') {
        bindItems(container, container._sortOpts || {});
      }
      return;
    }

    opts = opts || {};
    container._sortOpts = opts;
    bindItems(container, opts);
  };
})();
