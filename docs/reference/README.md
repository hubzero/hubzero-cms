<!--
status: rewritten
reviewed: 2026-09-09
-->
# Reference

Generated references, rebuilt from the source tree so they cannot drift
from the code. Each is produced by a script under `tools/docs/`, and the
Pages workflow fails if the committed pages differ from a fresh run.

- [Configuration](configuration/README.md) — every parameter in every
  component's `config/config.xml` and every plugin manifest, with its
  label, type, default, options, and description resolved from the
  language files.
- [REST API](api/README.md) — every endpoint under `/api/`, from the
  `@apiMethod`, `@apiUri`, and `@apiParameter` docblock tags the API
  explorer in `com_developer` also reads.
- [Muse](muse.md) — the console commands and their tasks, from the
  `@museDescription` docblocks.
- [Events](events/README.md) — every event the CMS fires through
  `Event::trigger()`, with its call sites, arguments, and the plugins that
  listen.

To regenerate after changing a manifest, a controller, or a command:

```bash
for g in config muse events api; do python3 tools/docs/gen_${g}_reference.py; done
python3 gh-pages/build_site.py
```

The narrative counterparts live in the [Developers](../developers/README.md)
book.
