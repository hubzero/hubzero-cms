/* global window */
(function (global) {
  'use strict';

  function createTimeline(storageKey, limit) {
    var key = (typeof storageKey === 'string' && storageKey !== '') ? storageKey : 'hubzero.debug.timeline';
    var max = Number.isFinite(limit) && limit > 0 ? Math.floor(limit) : 50;
    var timeline = [];

    function load() {
      try {
        var raw = global.localStorage ? global.localStorage.getItem(key) : null;
        if (!raw) {
          return;
        }
        var parsed = JSON.parse(raw);
        if (Array.isArray(parsed)) {
          timeline = parsed.slice(-max);
        }
      } catch (e) {
        timeline = [];
      }
    }

    function persist() {
      try {
        if (global.localStorage) {
          global.localStorage.setItem(key, JSON.stringify(timeline));
        }
      } catch (e) {
      }
    }

    function push(entry) {
      timeline.push(entry);
      if (timeline.length > max) {
        timeline = timeline.slice(-max);
      }
      persist();
      return timeline;
    }

    function clear() {
      timeline = [];
      persist();
      return timeline;
    }

    function list() {
      return timeline.slice();
    }

    function autoscroll(element) {
      if (!element || typeof element.scrollTop !== 'number') {
        return;
      }
      element.scrollTop = element.scrollHeight;
    }

    function renderJson(element, value, indent) {
      if (!element) {
        return;
      }
      element.textContent = JSON.stringify(value, null, Number.isFinite(indent) ? indent : 2);
    }

    function pushAndRender(entry, element, indent) {
      var snapshot = push(entry);
      if (element) {
        renderJson(element, snapshot, indent);
        autoscroll(element);
      }
      return snapshot;
    }

    load();

    return {
      push: push,
      pushAndRender: pushAndRender,
      clear: clear,
      list: list,
      autoscroll: autoscroll,
      renderJson: renderJson,
      storageKey: key,
      limit: max
    };
  }

  global.__hzDebugTimeline = createTimeline;
  global.__hzInertiaDebugTimeline = global.__hzInertiaDebugTimeline || createTimeline;
})(window);
