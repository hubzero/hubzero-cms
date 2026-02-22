/* global window, localStorage */
(function () {
  "use strict";

  if (window.__hzDebugPanel) {
    return;
  }

  window.__hzDebugPanel = function (protocol, storageKey) {
    const kind = (typeof protocol === "string" ? protocol.toLowerCase() : "").trim();
    const timelineStore = window.__hzDebugTimeline(storageKey + "_timeline", 120);

    return {
      open: true,
      autoscroll: true,
      mode: "timeline",
      timelineKind: "request",
      payload: {},
      timeline: [],

      init(raw) {
        const openValue = localStorage.getItem(storageKey + "_open");
        const autoValue = localStorage.getItem(storageKey + "_autoscroll");
        const modeValue = localStorage.getItem(storageKey + "_mode");
        const timelineKindValue = localStorage.getItem(storageKey + "_timeline_kind");

        if (openValue !== null) {
          this.open = openValue === "1";
        }
        if (autoValue !== null) {
          this.autoscroll = autoValue === "1";
        }
        if (modeValue === "timeline" || modeValue === "snapshot") {
          this.mode = modeValue;
        }
        if (timelineKindValue === "request" || timelineKindValue === "profile" || timelineKindValue === "snapshot") {
          this.timelineKind = timelineKindValue;
        }
        this.timeline = timelineStore.list();

        try {
          this.payload = JSON.parse(raw || "{}");
        } catch (e) {
          this.payload = { error: "invalid_debug_json" };
        }

        this.timeline = timelineStore.push(timelineStore.buildEvent(this.payload, kind));
        this.persistPrefs();

        this.$watch("open", () => this.persistPrefs());
        this.$watch("autoscroll", () => this.persistPrefs());
        this.$watch("mode", () => this.persistPrefs());
        this.$watch("timelineKind", () => this.persistPrefs());
        this.$watch("timeline", () => {
          if (Array.isArray(this.timeline)) {
            const recent = this.timeline.slice(-120);
            timelineStore.clear();
            for (let i = 0; i < recent.length; i += 1) {
              timelineStore.push(recent[i]);
            }
          }
          this.$nextTick(() => this.scroll());
        });

        this.$nextTick(() => this.scroll());
      },

      clear() {
        this.timeline = timelineStore.clear();
      },

      persistPrefs() {
        localStorage.setItem(storageKey + "_open", this.open ? "1" : "0");
        localStorage.setItem(storageKey + "_autoscroll", this.autoscroll ? "1" : "0");
        localStorage.setItem(storageKey + "_mode", this.mode);
        localStorage.setItem(storageKey + "_timeline_kind", this.timelineKind);
      },

      panelData() {
        if (this.mode !== "timeline") {
          return this.payload;
        }

        if (this.timelineKind === "profile") {
          return this.timeline.map((event) => ({ ts: event.ts, action: event.action, profile: event.profile }));
        }

        if (this.timelineKind === "snapshot") {
          return this.timeline;
        }

        return this.timeline.map((event) => ({ ts: event.ts, action: event.action, request: event.request }));
      },

      scroll() {
        if (!this.open || !this.autoscroll || !this.$refs || !this.$refs.pre) {
          return;
        }

        timelineStore.autoscroll(this.$refs.pre);
      }
    };
  };
})();
