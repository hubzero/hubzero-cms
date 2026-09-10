#!/usr/bin/env python3
"""Generate the REST API reference from the API controller docblocks.

Each component's ``api/controllers/<name>v<major>_<minor>.php`` declares its
endpoints with ``@apiMethod``, ``@apiUri``, and ``@apiParameter`` tags in the
task docblocks, the same tags ``Hubzero\\Api\\Doc\\Generator`` reads for the
``com_developer`` API explorer. This script parses them directly and writes
one page per component under ``docs/reference/api/``.

Usage:
    python3 tools/docs/gen_api_reference.py
"""

from __future__ import annotations

import json
import re
from pathlib import Path

ROOT = Path(__file__).resolve().parents[2]
CORE = ROOT / "core"
OUT = ROOT / "docs" / "reference" / "api"
HEADER = "<!--\nstatus: generated\nsource: {source}\n-->\n"

METHOD_RE = re.compile(r"/\*\*(?P<doc>(?:(?!\*/).)*)\*/\s*public\s+function\s+(?P<name>\w+)Task\s*\(", re.S)
FILE_RE = re.compile(r"^(?P<controller>.+?)v(?P<major>\d+)_(?P<minor>\d+)\.php$")


def doc_lines(doc: str) -> list[str]:
    out = []
    for raw in doc.splitlines():
        line = raw.strip()
        if line.startswith("*"):
            line = line[1:]
        out.append(line.rstrip())
    return out


def summary(doc: str) -> str:
    text = []
    for line in doc_lines(doc):
        stripped = line.strip()
        if stripped.startswith("@"):
            break
        if stripped:
            text.append(stripped)
        elif text:
            break
    return " ".join(text)


def parameters(doc: str) -> list[dict]:
    """Parse every @apiParameter { ... } block. Blocks are loose JSON."""
    params = []
    text = "\n".join(doc_lines(doc))
    for match in re.finditer(r"@apiParameter\s*(\{.*?\})\s*(?=@|\Z)", text, re.S):
        raw = match.group(1)
        cleaned = re.sub(r",\s*}", "}", raw)
        cleaned = re.sub(r"\t", " ", cleaned)
        try:
            params.append(json.loads(cleaned))
        except json.JSONDecodeError:
            fields = dict(re.findall(r'"(\w+)"\s*:\s*"?([^",}\n]*)"?', cleaned))
            if fields:
                params.append(fields)
    return params


def cell(value) -> str:
    if value is None:
        return "—"
    if isinstance(value, bool):
        return "yes" if value else "no"
    if isinstance(value, (list, dict)):
        return "`" + json.dumps(value) + "`"
    text = str(value).strip()
    return text.replace("|", "\\|").replace("\n", " ") or "—"


def main() -> int:
    if OUT.exists():
        for old in OUT.rglob("*.md"):
            old.unlink()
    OUT.mkdir(parents=True, exist_ok=True)
    index = []
    for component_dir in sorted(CORE.glob("components/com_*")):
        controllers = sorted((component_dir / "api" / "controllers").glob("*.php")) if (component_dir / "api" / "controllers").is_dir() else []
        endpoints = []
        for path in controllers:
            match = FILE_RE.match(path.name)
            if not match:
                continue
            version = f"{match.group('major')}.{match.group('minor')}"
            source = path.read_text(encoding="utf-8", errors="replace")
            for method in METHOD_RE.finditer(source):
                doc = method.group("doc")
                http = re.search(r"@apiMethod\s+(\S+)", doc)
                uri = re.search(r"@apiUri\s+(\S+)", doc)
                if not http and not uri:
                    continue
                endpoints.append({
                    "controller": match.group("controller"),
                    "version": version,
                    "task": method.group("name"),
                    "method": http.group(1).upper() if http else "GET",
                    "uri": uri.group(1) if uri else f"/{component_dir.name[4:]}/{method.group('name')}",
                    "summary": summary(doc),
                    "params": parameters(doc),
                    "file": path.relative_to(ROOT).as_posix(),
                    "line": source.count("\n", 0, method.start()) + 1,
                })
        if not endpoints:
            continue
        short = component_dir.name[4:]
        endpoints.sort(key=lambda e: (e["uri"], e["method"], e["version"]))
        lines = [
            HEADER.format(source=f"core/components/{component_dir.name}/api/controllers/"),
            f"# {short.capitalize()} API",
            "",
            f"Endpoints under `/api/{short}`, from the `{component_dir.name}` API controllers. "
            "Authenticate with an OAuth bearer token or a session cookie; see the developers book for the API basics.",
            "",
            "| Method | Endpoint | Purpose |",
            "|---|---|---|",
        ]
        # A component may expose the same endpoint at more than one API version.
        # Those would otherwise share a heading, and so an anchor, and every
        # link to the later version would land on the earlier one.
        from collections import Counter
        repeated = Counter((e["method"], e["uri"]) for e in endpoints)

        # Version alone is not always enough: several controllers in one
        # component can declare the same URI at the same version.
        with_version = Counter(
            (e["method"], e["uri"], e["version"]) for e in endpoints
        )

        def title_of(e):
            base = f"{e['method']} {e['uri']}"
            if repeated[(e["method"], e["uri"])] == 1:
                return base
            if with_version[(e["method"], e["uri"], e["version"])] == 1:
                return f"{base} (v{e['version']})"
            return f"{base} (v{e['version']}, {Path(e['file']).stem})"

        for e in endpoints:
            anchor = re.sub(r"[^a-z0-9]+", "-", title_of(e).lower()).strip("-")
            extra = title_of(e)[len(f"{e['method']} {e['uri']}"):]
            label = f"`{e['uri']}`{extra}"
            lines.append(f"| `{e['method']}` | [{label}](#{anchor}) | {cell(e['summary'])} |")
        lines.append("")
        for e in endpoints:
            lines.append(f"## {title_of(e)}")
            lines.append("")
            if e["summary"]:
                lines += [e["summary"], ""]
            lines.append(f"API version {e['version']}, task `{e['task']}` in [`{Path(e['file']).name}`](../../../{e['file']}#L{e['line']}).")
            lines.append("")
            if e["params"]:
                lines += ["| Parameter | Type | Required | Default | Description |", "|---|---|---|---|---|"]
                for p in e["params"]:
                    lines.append(
                        f"| `{cell(p.get('name'))}` | {cell(p.get('type'))} | {cell(p.get('required', False))} | "
                        f"{cell(p.get('default'))} | {cell(p.get('description'))} |"
                    )
                lines.append("")
        (OUT / f"{short}.md").write_text("\n".join(lines).rstrip() + "\n", encoding="utf-8")
        index.append((short, len(endpoints)))

    lines = [
        HEADER.format(source="core/components/*/api/controllers/"),
        "# REST API reference",
        "",
        "Every endpoint the CMS exposes under `/api/`, generated from the `@apiMethod`, `@apiUri`, and `@apiParameter` "
        "tags in the API controllers. The same tags feed the interactive API explorer that the `com_developer` "
        "component shows at `/developer/api` on a running hub.",
        "",
        "| Component | Endpoints |",
        "|---|---|",
    ]
    for short, count in index:
        lines.append(f"| [{short}]({short}.md) | {count} |")
    lines.append("")
    (OUT / "README.md").write_text("\n".join(lines), encoding="utf-8")
    print(f"{len(index)} component pages, {sum(c for _, c in index)} endpoints -> {OUT.relative_to(ROOT)}")
    return 0


if __name__ == "__main__":
    raise SystemExit(main())
