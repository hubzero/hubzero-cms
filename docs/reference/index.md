<!--
status: rewritten
reviewed: 2026-09-09
-->
# Reference

Look up configuration fields, REST API endpoints, command-line tasks, and
plugin events declared in the CMS source. These pages are generated from
the repository; the guides linked below explain how to use each feature.

## Choose a reference

| You need to | Reference | Read first if this is new to you |
|---|---|---|
| Find a component or plugin option | [Configuration](configuration/index.md) | [Configuring a hub](../managers/05-configuring/index.md) |
| Call the hub from another program | [REST API](api/index.md) | [The REST API guide](../developers/16-api.md) for authentication and requests |
| Run a CMS task from a terminal | [Muse](muse.md) | [Muse guide](../developers/12-muse.md) for command syntax and component commands |
| Find an event a plugin can handle | [Events](events/index.md) | [Framework events](../developers/03-foundation/04-events.md) for loading plugins and triggering events |

## What the entries describe

- [Configuration](configuration/index.md) — parameters in
  components' `config/config.xml` files and plugin manifests, with each
  label, type, default, options, and description resolved from the
  language files.
- [REST API](api/index.md) — documented endpoints under `/api/`, from the
  `@apiMethod`, `@apiUri`, and `@apiParameter` docblock tags the API
  explorer in `com_developer` also reads.
- [Muse](muse.md) — the console commands and their tasks, from the
  `@museDescription` docblocks.
- [Events](events/index.md) — event calls found through
  `Event::trigger()`, with their call sites, arguments, and the plugins that
  listen.

Generation keeps the references in sync with the declarations it reads;
it does not test the behavior of an option or endpoint. A configuration
**default** is the value in the manifest, which may differ from a value
saved by the installer or a hub manager. API docblocks can also be
incomplete, and an endpoint's availability depends on the hub's enabled
components and permissions. Follow the source links when you need to check
the implementation.

## Updating the references

Each reference is produced by a script under `docs/_tools/`. Edit its source
manifest or docblock, or the generator's explanatory text; direct edits to
generated pages are overwritten. To regenerate and validate the site after
a change, run this from the repository root:

```bash
sh docs/_tools/rebuild.sh
```

Commit the regenerated pages and `gh-pages/public/` with the source change.
The Pages workflow checks that both match a fresh build.
