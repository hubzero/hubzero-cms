#!/usr/bin/env python3
"""Generate the events reference from Event::trigger() calls and plugin listeners.

An event is fired with ``Event::trigger('group.onName', [args])`` and answered
by every enabled plugin in ``core/plugins/<group>/`` that defines a public
``onName`` method. This script finds both sides across the source tree and
writes one page per plugin group under ``docs/reference/events/``: each event,
where it is fired, the arguments as written at the call site, and the
plugins that listen.

Usage:
    python3 tools/docs/gen_events_reference.py
"""

from __future__ import annotations

import re
from collections import defaultdict
from pathlib import Path

ROOT = Path(__file__).resolve().parents[2]
CORE = ROOT / "core"
OUT = ROOT / "docs" / "reference" / "events"
SCAN = ["components", "libraries", "plugins", "modules", "templates", "bootstrap"]
HEADER = "<!--\nstatus: generated\nsource: {source}\n-->\n"

TRIGGER_RE = re.compile(
    r"(?:Event::trigger|->trigger)\(\s*['\"](?P<group>[a-zA-Z0-9_]+)\.(?P<event>on[A-Za-z0-9_]+)['\"]\s*(?:,\s*(?P<args>.*?))?\)\s*;",
    re.S,
)
LISTENER_RE = re.compile(r"public\s+function\s+(on[A-Za-z0-9_]+)\s*\(([^)]*)\)")


def compact(args: str | None) -> str:
    if not args:
        return ""
    text = re.sub(r"\s+", " ", args).strip()
    text = re.sub(r"^array\s*\((.*)\)$", r"[\1]", text)
    return text if len(text) <= 140 else text[:137] + "…"


def main() -> int:
    fired: dict[tuple[str, str], list[tuple[str, int, str]]] = defaultdict(list)
    for area in SCAN:
        for path in (CORE / area).rglob("*.php"):
            if "vendor" in path.parts or "tests" in path.parts:
                continue
            try:
                source = path.read_text(encoding="utf-8", errors="replace")
            except OSError:
                continue
            if "trigger(" not in source:
                continue
            for match in TRIGGER_RE.finditer(source):
                line = source.count("\n", 0, match.start()) + 1
                fired[(match.group("group"), match.group("event"))].append(
                    (path.relative_to(ROOT).as_posix(), line, compact(match.group("args")))
                )

    listeners: dict[str, dict[str, list[tuple[str, str, str]]]] = defaultdict(lambda: defaultdict(list))
    for group_dir in sorted(p for p in (CORE / "plugins").iterdir() if p.is_dir()):
        for plugin_dir in sorted(p for p in group_dir.iterdir() if p.is_dir()):
            main_file = plugin_dir / f"{plugin_dir.name}.php"
            if not main_file.exists():
                continue
            source = main_file.read_text(encoding="utf-8", errors="replace")
            for match in LISTENER_RE.finditer(source):
                signature = re.sub(r"\s+", " ", match.group(2)).strip()
                listeners[group_dir.name][match.group(1)].append(
                    (plugin_dir.name, main_file.relative_to(ROOT).as_posix(), signature)
                )

    groups = sorted(set(g for g, _ in fired) | set(listeners))
    if OUT.exists():
        for old in OUT.rglob("*.md"):
            old.unlink()
    OUT.mkdir(parents=True, exist_ok=True)

    index_rows = []
    for group in groups:
        events = sorted(set(e for g, e in fired if g == group) | set(listeners.get(group, {})))
        if not events:
            continue
        lines = [
            HEADER.format(source=f"Event::trigger('{group}.*') call sites and core/plugins/{group}/"),
            f"# {group.capitalize()} events",
            "",
            f"Events in the `{group}` group. A plugin in `core/plugins/{group}/` receives an event by defining a "
            "public method with the event's name; the arguments are those the call site passes, in order.",
            "",
        ]
        for event in events:
            sites = fired.get((group, event), [])
            heard = listeners.get(group, {}).get(event, [])
            lines.append(f"## `{group}.{event}`")
            lines.append("")
            if sites:
                lines.append("Fired from:")
                lines.append("")
                for file, line, args in sorted(sites)[:12]:
                    link = f"[`{file}:{line}`](../../../{file}#L{line})"
                    lines.append(f"- {link}" + (f" with `{args}`" if args else ""))
                if len(sites) > 12:
                    lines.append(f"- and {len(sites) - 12} more call sites")
                lines.append("")
            else:
                lines.append("No call site in the CMS fires this event; the listener may answer an event fired by another package or by an older plugin.")
                lines.append("")
            if heard:
                lines.append("Listeners:")
                lines.append("")
                for plugin, file, signature in sorted(heard):
                    lines.append(f"- `plg_{group}_{plugin}` — [`{event}({signature})`](../../../{file})")
                lines.append("")
            elif sites:
                lines.append("No plugin in the source tree listens for this event.")
                lines.append("")
        (OUT / f"{group}.md").write_text("\n".join(lines).rstrip() + "\n", encoding="utf-8")
        index_rows.append((group, len(events), sum(1 for e in events if (group, e) in fired), sum(1 for e in events if listeners.get(group, {}).get(e))))

    lines = [
        HEADER.format(source="core/**/*.php"),
        "# Events reference",
        "",
        "Every event the CMS fires through `Event::trigger()`, grouped by plugin group, with the call sites that fire it "
        "and the plugins that listen. Generated from the source tree.",
        "",
        "| Group | Events | Fired in the CMS | With listeners |",
        "|---|---|---|---|",
    ]
    for group, total, with_sites, with_listeners in index_rows:
        lines.append(f"| [{group}]({group}.md) | {total} | {with_sites} | {with_listeners} |")
    lines.append("")
    (OUT / "README.md").write_text("\n".join(lines), encoding="utf-8")
    print(f"{len(index_rows)} group pages, {sum(r[1] for r in index_rows)} events -> {OUT.relative_to(ROOT)}")
    return 0


if __name__ == "__main__":
    raise SystemExit(main())
