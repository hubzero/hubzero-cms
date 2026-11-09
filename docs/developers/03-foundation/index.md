<!--
status: rewritten
reviewed-against: 2.4-main @ 348f0057c2
reviewed: 2026-09-10
-->
# Foundation

This section shows how the framework under every extension is put
together. It covers what is on disk, what a request runs through, and how
your classes are found. It also covers how extensions reach each other, and
the handful of names an extension uses constantly.

Start with [Structure](01-structure.md) to see the request lifecycle and
where files belong. Then read [Extensions](05-extensions.md) for the four
extension types and [Facades](06-facades.md) for calls such as
`Request::getString()` and `Lang::txt()`. A **facade** is a short class name
that forwards a call to a framework service. The remaining chapters explain
how the framework finds classes, registers services, and calls plugins.

## In this section

- [Structure](01-structure.md) — the directory layout, what belongs in
  `core/` against `app/`, and the request lifecycle end to end.
- [Constants](02-constants.md) — the paths, the version, and the entry guard.
- [Autoloading](03-autoloading.md) — how a class name becomes a file, and
  what to check when it does not.
- [Events](04-events.md) — how extensions reach each other, and why an event
  sometimes reaches nobody.
- [Extensions](05-extensions.md) — what a component, plugin, module, and
  template are, and how the platform finds them.
- [Facades](06-facades.md) — the short names for the container's services,
  and the import rule that makes them work. **The one to read.**
- [Service providers](07-providers.md) — how those services are registered
  and resolved, and how to add one.
