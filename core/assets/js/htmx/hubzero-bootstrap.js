/* global window, document, localStorage */
(function () {
  "use strict";

  if (window.__hubzeroHtmxBootstrapLoaded) {
    return;
  }
  window.__hubzeroHtmxBootstrapLoaded = true;

  function parseBool(value) {
    if (value === true || value === 1) {
      return true;
    }
    if (typeof value !== "string") {
      return false;
    }
    const normalized = value.trim().toLowerCase();
    return normalized === "1" || normalized === "true" || normalized === "yes" || normalized === "on";
  }

  function readStateNode() {
    const node = document.getElementById("hx-state");
    if (!node) {
      return {};
    }

    try {
      return JSON.parse(node.textContent || "{}") || {};
    } catch (e) {
      return {};
    }
  }

  function getState() {
    window.hzHtmxState = window.hzHtmxState || readStateNode();
    return window.hzHtmxState;
  }

  if (window.htmx && window.htmx.config) {
    window.htmx.config.historyRestoreAsHxRequest = false;
  }

  function applySecurityConfig() {
    if (!window.htmx || !window.htmx.config) {
      return;
    }

    const state = getState();
    const security = (state.htmx && state.htmx.security) ? state.htmx.security : {};

    if (typeof security.allowEval === "boolean") {
      window.htmx.config.allowEval = security.allowEval;
    }
    if (typeof security.allowScriptTags === "boolean") {
      window.htmx.config.allowScriptTags = security.allowScriptTags;
    }
    if (typeof security.historyCacheSize === "number" && Number.isFinite(security.historyCacheSize)) {
      window.htmx.config.historyCacheSize = Math.max(0, Math.floor(security.historyCacheSize));
    }
  }

  applySecurityConfig();

  function localFlag(name) {
    try {
      return parseBool(localStorage.getItem(name));
    } catch (e) {
      return false;
    }
  }

  function debugEnabled() {
    const state = getState();
    const fromState = !!(state.htmx && state.htmx.debug && parseBool(state.htmx.debug.enabled));
    const fromLocal = localFlag("hubzero.htmx.debug");
    const fromUrl = parseBool((new URLSearchParams(window.location.search)).get("htmx_debug") || "");

    return fromState || fromLocal || fromUrl;
  }

  function logEnabled() {
    const state = getState();
    const fromState = !!(state.htmx && state.htmx.debug && parseBool(state.htmx.debug.log));
    return fromState || localFlag("hubzero.htmx.log");
  }

  function logEvent(name, detail) {
    if (!logEnabled() || !window.console || !console.debug) {
      return;
    }

    console.debug("[hubzero:htmx]", name, detail || {});
  }

  function csrfConfig() {
    const state = getState();
    const csrf = state.csrf || (state.htmx && state.htmx.csrf) || {};

    return {
      token: csrf.token || csrf.value || "",
      header: csrf.header || "X-CSRF-Token"
    };
  }

  document.body.addEventListener("htmx:configRequest", function (evt) {
    const cfg = csrfConfig();
    evt.detail.headers = evt.detail.headers || {};

    if (cfg.token) {
      evt.detail.headers[cfg.header] = cfg.token;
    }

    if (debugEnabled()) {
      evt.detail.headers["HX-Debug"] = "true";
    }

    logEvent("configRequest", {
      path: evt.detail.path || "",
      verb: evt.detail.verb || "",
      target: evt.detail.target ? evt.detail.target.id || evt.detail.target.tagName : "",
      headers: evt.detail.headers
    });
  });

  document.body.addEventListener("htmx:beforeRequest", function (evt) {
    logEvent("beforeRequest", {
      path: evt.detail.path || "",
      boosted: !!(evt.detail.requestConfig && evt.detail.requestConfig.boosted)
    });
  });

  document.body.addEventListener("htmx:afterSwap", function (evt) {
    logEvent("afterSwap", {
      target: evt.detail.target ? evt.detail.target.id || evt.detail.target.tagName : ""
    });
  });

  document.body.addEventListener("htmx:beforeSwap", function (evt) {
    const xhr = evt.detail.xhr;
    if (!xhr || xhr.status !== 422) {
      return;
    }

    evt.detail.shouldSwap = true;
    evt.detail.isError = false;
  });

  document.body.addEventListener("htmx:responseError", function (evt) {
    if (evt.detail && evt.detail.xhr && evt.detail.xhr.status === 422) {
      const validationEvent = new CustomEvent("hubzero:validation-failed", {
        detail: {
          path: evt.detail.requestConfig ? evt.detail.requestConfig.path : "",
          status: 422
        }
      });
      window.dispatchEvent(validationEvent);
      return;
    }

    logEvent("responseError", {
      status: evt.detail.xhr ? evt.detail.xhr.status : 0,
      path: evt.detail.requestConfig ? evt.detail.requestConfig.path : ""
    });
  });

  window.HubzeroHtmx = {
    state: getState,
    setState: function (nextState) {
      window.hzHtmxState = nextState || {};
    },
    debugEnabled: debugEnabled,
    logEnabled: logEnabled
  };
})();
