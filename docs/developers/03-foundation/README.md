<!--
status: rewritten
reviewed-against: 2.4-main @ d48e29db14
reviewed: 2026-09-09
source: https://help.hubzero.org/documentation/240/webdevs/foundation
-->
# Foundation

How the framework underneath every extension is put together: what is on
disk, what a request runs through, and the handful of names an extension
uses constantly.

## In this section

- [Structure](01-structure.md) — the directory layout, and what belongs in
  `core/` against `app/`.
- [Constants](02-constants.md) — the paths, the version, and the entry guard.
- [Service providers](03-providers.md) — how services are registered and
  resolved.
- [Facades](04-facades.md) — the short names for those services, and the
  import rule that makes them work.
- [Extensions](05-extensions.md) — what a component, plugin, module, and
  template are, and how the platform finds them.
