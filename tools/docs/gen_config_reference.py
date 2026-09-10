#!/usr/bin/env python3
"""Generate the configuration reference from component and plugin manifests.

Every component's ``config/config.xml`` and every plugin's ``<name>.xml``
declares the parameters an administrator can set, with a language key for
the label and description. This script resolves those keys through the
language files and writes one Markdown page per component and one per
plugin group under ``docs/reference/configuration/``.

The pages carry ``status: generated`` and are rebuilt by CI; edit the XML or
the language files, not the pages.

Usage:
    python3 tools/docs/gen_config_reference.py
"""

from __future__ import annotations

import re
import sys
import xml.etree.ElementTree as ET
from pathlib import Path

ROOT = Path(__file__).resolve().parents[2]
CORE = ROOT / "core"
OUT = ROOT / "docs" / "reference" / "configuration"
SKIP_TYPES = {"spacer", "rules", "hidden"}

HEADER = "<!--\nstatus: generated\nsource: {source}\n-->\n"


# --------------------------------------------------------------------------- language


def load_ini(path: Path, into: dict[str, str]) -> None:
    if not path.exists():
        return
    for line in path.read_text(encoding="utf-8", errors="replace").splitlines():
        line = line.strip()
        if not line or line.startswith((";", "#", "[")):
            continue
        key, sep, value = line.partition("=")
        if not sep:
            continue
        value = value.strip()
        if value[:1] == value[-1:] == '"':
            value = value[1:-1]
        value = value.replace('"_QQ_"', '"').replace("_QQ_", '"').replace('\\"', '"')
        into.setdefault(key.strip(), value)


def base_strings() -> dict[str, str]:
    strings: dict[str, str] = {}
    for client in ("Administrator", "Site"):
        for ini in sorted((CORE / "bootstrap" / client / "language" / "en-GB").glob("*.ini")):
            load_ini(ini, strings)
    return strings


def extension_strings(paths: list[Path], base: dict[str, str]) -> dict[str, str]:
    strings: dict[str, str] = {}
    for path in paths:
        load_ini(path, strings)
    merged = dict(base)
    merged.update(strings)
    return merged


class Lang:
    def __init__(self, strings: dict[str, str]):
        self.strings = strings

    def txt(self, key: str | None) -> str:
        if not key:
            return ""
        key = key.strip()
        return self.strings.get(key, self.strings.get(key.upper(), key))


# --------------------------------------------------------------------------- rendering


def md_escape(text: str) -> str:
    return text.replace("|", "\\|").replace("\n", " ").strip()


def strip_tags(text: str) -> str:
    return re.sub(r"<[^>]+>", "", text)


def render_fieldset(fieldset: ET.Element, lang: Lang, lines: list[str], level: int = 2) -> int:
    fields = [f for f in fieldset.findall("field") if f.get("type", "text") not in SKIP_TYPES and f.get("name")]
    if not fields:
        return 0
    title = lang.txt(fieldset.get("label")) or fieldset.get("name", "").replace("_", " ").capitalize()
    lines.append(f"{'#' * level} {strip_tags(title)}")
    lines.append("")
    description = strip_tags(lang.txt(fieldset.get("description")))
    if description and description != fieldset.get("description"):
        lines.append(description)
        lines.append("")
    lines.append("| Parameter | Label | Type | Default | Description |")
    lines.append("|---|---|---|---|---|")
    for field in fields:
        name = field.get("name", "")
        ftype = field.get("type", "text")
        default = field.get("default", "")
        label = strip_tags(lang.txt(field.get("label")))
        desc = strip_tags(lang.txt(field.get("description")))
        options = field.findall("option")
        if options:
            rendered = []
            for option in options:
                value = option.get("value", "")
                text = strip_tags(lang.txt((option.text or "").strip()))
                rendered.append(f"`{value}` {text}" if text and text != value else f"`{value}`")
            desc = (desc.rstrip(".") + ". " if desc else "") + "Options: " + ", ".join(rendered) + "."
            if default != "":
                for option in options:
                    if option.get("value", "") == default:
                        text = strip_tags(lang.txt((option.text or "").strip()))
                        if text and text != default:
                            default = f"{default} ({text})"
        lines.append(
            f"| `{md_escape(name)}` | {md_escape(label)} | {md_escape(ftype)} | "
            f"{('`' + md_escape(default) + '`') if default != '' else '—'} | {md_escape(desc)} |"
        )
    lines.append("")
    return len(fields)


def component_pages(base: dict[str, str]) -> list[tuple[str, str, int]]:
    """Return (name, description, parameter count) for each component written."""
    written = []
    for config in sorted(CORE.glob("components/com_*/config/config.xml")):
        component = config.parents[1].name
        short = component[4:]
        manifest = next(iter(config.parents[1].glob("*.xml")), None)
        description = ""
        if manifest is not None:
            try:
                node = ET.parse(manifest).getroot().find("description")
                description = (node.text or "").strip() if node is not None else ""
            except ET.ParseError:
                description = ""
        lang_paths = sorted(config.parents[1].glob("admin/language/en-GB/*.ini")) + sorted(
            config.parents[1].glob("site/language/en-GB/*.ini")
        )
        lang = Lang(extension_strings(lang_paths, base))
        try:
            root = ET.parse(config).getroot()
        except ET.ParseError as exc:
            print(f"warning: {config.relative_to(ROOT)}: {exc}", file=sys.stderr)
            continue
        lines = [
            HEADER.format(source=config.relative_to(ROOT).as_posix()),
            f"# {short.capitalize()} ({component})",
            "",
        ]
        if description and description != strip_tags(lang.txt(description)):
            description = strip_tags(lang.txt(description))
        if description:
            lines += [description, ""]
        lines += [
            f"Parameters from [`{config.relative_to(ROOT).as_posix()}`](../../../../{config.relative_to(ROOT).as_posix()}), "
            "as shown on the component's **Options** screen in the administrator interface.",
            "",
        ]
        count = 0
        for fieldset in root.iter("fieldset"):
            count += render_fieldset(fieldset, lang, lines)
        if count == 0:
            continue
        page = OUT / "components" / f"{short}.md"
        page.parent.mkdir(parents=True, exist_ok=True)
        page.write_text("\n".join(lines).rstrip() + "\n", encoding="utf-8")
        written.append((short, description, count))
    return written


def plugin_pages(base: dict[str, str]) -> list[tuple[str, int, int]]:
    """Return (group, plugin count, parameter count) for each group page written."""
    written = []
    for group_dir in sorted(p for p in (CORE / "plugins").iterdir() if p.is_dir()):
        group = group_dir.name
        lines = [
            HEADER.format(source=f"core/plugins/{group}/*/*.xml"),
            f"# {group.capitalize()} plugins",
            "",
            f"Parameters of every plugin in the `{group}` group, from each plugin's manifest. "
            "Set them under **Extensions > Plugins** in the administrator interface.",
            "",
        ]
        plugins = 0
        total = 0
        for plugin_dir in sorted(p for p in group_dir.iterdir() if p.is_dir()):
            manifest = plugin_dir / f"{plugin_dir.name}.xml"
            if not manifest.exists():
                continue
            try:
                root = ET.parse(manifest).getroot()
            except ET.ParseError as exc:
                print(f"warning: {manifest.relative_to(ROOT)}: {exc}", file=sys.stderr)
                continue
            lang = Lang(extension_strings(sorted(plugin_dir.glob("language/en-GB/*.ini")), base))
            name_node = root.find("name")
            desc_node = root.find("description")
            name = strip_tags(lang.txt((name_node.text or "").strip() if name_node is not None else plugin_dir.name))
            description = strip_tags(lang.txt((desc_node.text or "").strip() if desc_node is not None else ""))
            fieldsets = list(root.iter("fieldset"))
            section = [f"## {name} (`plg_{group}_{plugin_dir.name}`)", ""]
            if description:
                section += [description, ""]
            count = 0
            for fieldset in fieldsets:
                count += render_fieldset(fieldset, lang, section, level=3)
            if count == 0:
                section += ["This plugin has no parameters.", ""]
            lines += section
            plugins += 1
            total += count
        if plugins == 0:
            continue
        page = OUT / "plugins" / f"{group}.md"
        page.parent.mkdir(parents=True, exist_ok=True)
        page.write_text("\n".join(lines).rstrip() + "\n", encoding="utf-8")
        written.append((group, plugins, total))
    return written


def main() -> int:
    base = base_strings()
    if OUT.exists():
        for old in OUT.rglob("*.md"):
            old.unlink()
    components = component_pages(base)
    groups = plugin_pages(base)

    lines = [
        HEADER.format(source="core/components/*/config/config.xml and core/plugins/*/*/*.xml"),
        "# Configuration reference",
        "",
        "Every parameter an administrator can set, generated from the extension manifests in the source tree. "
        "Component options are edited from each component's **Options** button in the administrator interface; "
        "plugin parameters are edited under **Extensions > Plugins**.",
        "",
        "## Components",
        "",
        "| Component | Parameters | Description |",
        "|---|---|---|",
    ]
    for short, description, count in components:
        lines.append(f"| [{short}](components/{short}.md) | {count} | {md_escape(description)} |")
    lines += ["", "## Plugin groups", "", "| Group | Plugins | Parameters |", "|---|---|---|"]
    for group, plugins, total in groups:
        lines.append(f"| [{group}](plugins/{group}.md) | {plugins} | {total} |")
    lines.append("")
    OUT.mkdir(parents=True, exist_ok=True)
    (OUT / "README.md").write_text("\n".join(lines), encoding="utf-8")
    how_to_read = (
        "\n## How to read these tables\n\n"
        "- **Default** is the value written in the manifest, which is what a hub "
        "runs on until someone saves the screen. It is not always what the code "
        "falls back to when the setting is absent, and where the two disagree the "
        "narrative chapter for that extension says so.\n"
        "- **Type** is the form field the administrator interface renders. An "
        "unrecognised type falls back to a plain text box.\n"
        "- **Description** is the manifest's description string resolved through "
        "the extension's language files. A missing string leaves the raw key.\n"
        "- A parameter listed here is not proof that anything reads it. Several "
        "shipped settings are stored and never used again; those are recorded in "
        "the chapter for the extension.\n"
    )
    (OUT / "components" / "README.md").write_text(
        HEADER.format(source="core/components/*/config/config.xml") + "# Component options\n\n"
        "One page per component with a `config/config.xml`, listing every parameter with its label, "
        "type, default, and description. These are the settings behind each component's **Options** "
        "button in the administrator interface.\n" + how_to_read,
        encoding="utf-8",
    )
    (OUT / "plugins" / "README.md").write_text(
        HEADER.format(source="core/plugins/*/*/*.xml") + "# Plugin parameters\n\n"
        "One page per plugin group, listing every plugin in the group and its parameters. These are "
        "the settings behind each plugin's row under **Extensions** > **Plug-in Manager**.\n" + how_to_read,
        encoding="utf-8",
    )
    print(f"{len(components)} component pages ({sum(c for _, _, c in components)} parameters), "
          f"{len(groups)} plugin group pages ({sum(t for _, _, t in groups)} parameters) -> {OUT.relative_to(ROOT)}")
    return 0


if __name__ == "__main__":
    raise SystemExit(main())
