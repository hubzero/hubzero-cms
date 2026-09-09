#!/usr/bin/env python3
"""Generate the muse console reference from the command classes.

``core/bin/muse`` dispatches to classes under
``core/libraries/Hubzero/Console/Command/``. Each public method is a task;
its docblock's ``@museDescription`` line is the help text muse prints, and
``@museArgument`` lines describe options. Commands in a subdirectory are
sub-commands (``muse user:group``). This script reads those docblocks and
writes one page per command under ``docs/reference/muse/``.

Usage:
    python3 tools/docs/gen_muse_reference.py
"""

from __future__ import annotations

import re
from pathlib import Path

ROOT = Path(__file__).resolve().parents[2]
COMMANDS = ROOT / "core" / "libraries" / "Hubzero" / "Console" / "Command"
OUT = ROOT / "docs" / "reference" / "muse"
SKIP_FILES = {"Base.php", "CommandInterface.php"}

HEADER = "<!--\nstatus: generated\nsource: {source}\n-->\n"
DOCBLOCK_METHOD_RE = re.compile(
    r"/\*\*(?P<doc>(?:(?!\*/).)*)\*/\s*public\s+function\s+(?P<name>\w+)\s*\(", re.S
)
CLASS_DOC_RE = re.compile(r"/\*\*(?P<doc>(?:(?!\*/).)*)\*/\s*(?:abstract\s+)?class\s+(?P<name>\w+)", re.S)


def clean_doc(doc: str) -> list[str]:
    lines = []
    for raw in doc.splitlines():
        line = raw.strip()
        if line.startswith("*"):
            line = line[1:].strip()
        lines.append(line)
    return lines


def summary_of(doc: str) -> str:
    text = []
    for line in clean_doc(doc):
        if line.startswith("@"):
            break
        if line:
            text.append(line)
        elif text:
            break
    return " ".join(text)


def tasks_of(source: str) -> list[dict[str, object]]:
    """Every task muse itself would list, described the way muse describes it.

    ``Help::addTasks()`` reflects over the public methods and skips only the
    constructor, ``execute``, and ``help``; a method without a
    ``@museDescription`` is still listed, as "no description available". The
    tag ``@museIgnoreHelp`` reads as though it hides a task, but nothing acts
    on it, so those tasks appear too and are marked here instead.
    """
    tasks = []
    for match in DOCBLOCK_METHOD_RE.finditer(source):
        name = match.group("name")
        doc = match.group("doc")
        if name.startswith("__"):
            continue
        desc = re.search(r"@museDescription\s+(.+)", doc)
        if name == "execute":
            description = desc.group(1).strip() if desc else summary_of(doc)
        elif desc:
            description = desc.group(1).strip()
        else:
            description = "No description available."
        arguments = [arg.group(1).strip() for arg in re.finditer(r"@museArgument\s+(.+)", doc)]
        tasks.append({
            "name": name,
            "description": description,
            "arguments": arguments,
            "hidden": "@museIgnoreHelp" in doc,
        })
    return tasks


def command_name(path: Path) -> str:
    rel = path.relative_to(COMMANDS).with_suffix("")
    return ":".join(part.lower() for part in rel.parts)


def main() -> int:
    if OUT.exists():
        for old in OUT.rglob("*.md"):
            old.unlink()
    OUT.mkdir(parents=True, exist_ok=True)
    index = []
    for path in sorted(COMMANDS.rglob("*.php")):
        if path.name in SKIP_FILES or "Tests" in path.parts:
            continue
        source = path.read_text(encoding="utf-8", errors="replace")
        if "CommandInterface" not in source and "extends Base" not in source:
            continue
        class_match = CLASS_DOC_RE.search(source)
        overview = ""
        if class_match:
            overview = summary_of(class_match.group("doc")).replace("command class", "").strip()
        tasks = [t for t in tasks_of(source) if t["name"] not in ("help",)]
        name = command_name(path)
        default = next((t for t in tasks if t["name"] == "execute"), None)
        named = [t for t in tasks if t["name"] != "execute"]
        rel = path.relative_to(ROOT).as_posix()
        lines = [
            HEADER.format(source=rel),
            f"# muse {name}",
            "",
        ]
        if overview:
            lines += [overview[0].upper() + overview[1:] + ("" if overview.endswith(".") else "."), ""]
        lines += [f"Implemented in [`{path.name}`](../../../{rel}).", ""]
        if default and default["description"]:
            lines += ["## Running the command alone", "", f"`muse {name}` — {default['description']}", ""]
        if named:
            lines += ["## Tasks", ""]
            for task in named:
                lines.append(f"### `muse {name} {task['name']}`")
                lines.append("")
                if task["description"]:
                    lines.append(str(task["description"]))
                    lines.append("")
                if task.get("hidden"):
                    lines.append("> **Note:** This task carries `@museIgnoreHelp`. Nothing acts on that "
                                 "tag, so muse lists the task anyway.")
                    lines.append("")
                if task["arguments"]:
                    lines.append("Arguments:")
                    lines.append("")
                    lines += [f"- {arg}" for arg in task["arguments"]]  # type: ignore[union-attr]
                    lines.append("")
        else:
            lines += ["This command has no documented tasks; run `muse " + name + " help` for its built-in help.", ""]
        page = OUT / (name.replace(":", "-") + ".md")
        page.write_text("\n".join(lines).rstrip() + "\n", encoding="utf-8")
        index.append((name, overview, len(named)))

    lines = [
        HEADER.format(source="core/libraries/Hubzero/Console/Command/"),
        "# Muse console reference",
        "",
        "`muse` is the Hubzero command-line tool, at `core/bin/muse`. Run it from the hub's root directory as a user "
        "that can read the configuration. `muse help` lists the commands; `muse <command> help` describes one. "
        "This reference is generated from the command classes' docblocks.",
        "",
        "| Command | Tasks | Purpose |",
        "|---|---|---|",
    ]
    for name, overview, count in index:
        lines.append(f"| [`muse {name}`]({name.replace(':', '-')}.md) | {count} | {overview.replace('|', ' ')} |")
    lines.append("")
    (OUT / "README.md").write_text("\n".join(lines), encoding="utf-8")
    print(f"{len(index)} command pages -> {OUT.relative_to(ROOT)}")
    return 0


if __name__ == "__main__":
    raise SystemExit(main())
