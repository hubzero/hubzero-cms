/* global window */
(function (global) {
  'use strict';

  function inferAction(payload, protocol) {
    var request = payload && payload.request ? payload.request : {};
    var kind = (typeof protocol === 'string' && protocol !== '') ? protocol.toLowerCase() : '';

    if (kind === 'inertia') {
      if (request.partial_data || request.partial_except) {
        return 'partial reload';
      }
      if (request.request) {
        return 'inertia visit';
      }
      return 'full page';
    }

    if (kind === 'htmx') {
      if (request.history_restore) {
        return 'history restore';
      }
      if (request.boosted) {
        return 'boosted navigation';
      }
      if (request.request) {
        return 'htmx request';
      }
      return 'full page';
    }

    if (request.request) {
      return 'request';
    }

    return 'full page';
  }

  function buildEvent(payload, protocol) {
    var safePayload = payload || {};
    return {
      ts: new Date().toISOString(),
      action: inferAction(safePayload, protocol),
      request: safePayload.request || {},
      profile: safePayload.profile || {},
      snapshot: safePayload
    };
  }

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
      inferAction: inferAction,
      buildEvent: buildEvent,
      storageKey: key,
      limit: max
    };
  }

  global.__hzDebugTimeline = createTimeline;
})(window);
