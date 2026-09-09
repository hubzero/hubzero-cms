#!/usr/bin/env python3
"""Fail if the built documentation site contains a broken internal link.

A dead link is invisible until a reader follows it, and the books
cross-reference each other heavily, so a rename quietly breaks pages that
were never touched. This walks the built output and checks that every
internal href, src, and srcset resolves, both the file and, when the link
carries a fragment, the anchor it points at.

External URLs are not fetched; this is an offline structural check.

Run: python3 gh-pages/check_links.py gh-pages/public
"""

from __future__ import annotations

import os
import re
import sys
from pathlib import Path

HREF_RE = re.compile(r'(?:href|src|srcset)="([^"]+)"')
ID_RE = re.compile(r'\bid="([^"]+)"')
NAME_RE = re.compile(r'<a[^>]+\bname="([^"]+)"')
EXTERNAL_PREFIXES = ("http://", "https://", "mailto:", "//", "data:", "tel:")


def check(root: Path) -> list[str]:
    pages: dict[str, str] = {}
    for path in sorted(root.rglob("*.html")):
        pages[str(path.relative_to(root))] = path.read_text(encoding="utf-8")

    anchors = {
        rel: set(ID_RE.findall(html)) | set(NAME_RE.findall(html)) for rel, html in pages.items()
    }

    problems: list[str] = []
    for rel, html in pages.items():
        base = os.path.dirname(rel)
        for raw in HREF_RE.findall(html):
            for candidate in raw.split(",") if "srcset" in raw else [raw]:
                href = candidate.strip().split(" ")[0]
                if not href or href.startswith(EXTERNAL_PREFIXES):
                    continue
                href = href.split("?", 1)[0]
                path_part, _, fragment = href.partition("#")
                if path_part:
                    target = os.path.normpath(os.path.join(base, path_part))
                    if (root / target).is_dir():
                        target = os.path.join(target, "index.html")
                else:
                    target = rel
                if not (root / target).exists():
                    problems.append(f"{rel}: -> {href} (no such file: {target})")
                elif fragment and target.endswith(".html") and fragment not in anchors.get(target, set()):
                    problems.append(f"{rel}: -> {href} (no anchor #{fragment} in {target})")
    return problems


def main(argv: list[str]) -> int:
    root = Path(argv[1] if len(argv) > 1 else "gh-pages/public").resolve()
    if not root.is_dir():
        print(f"error: {root} is not a directory", file=sys.stderr)
        return 2

    problems = check(root)
    page_count = len(list(root.rglob("*.html")))
    for problem in problems:
        prefix = "::error::broken link " if os.environ.get("GITHUB_ACTIONS") else "broken link "
        print(prefix + problem, file=sys.stderr)
    if problems:
        print(f"{len(problems)} broken link(s) across {page_count} pages", file=sys.stderr)
        return 1
    print(f"no broken internal links across {page_count} pages")
    return 0


if __name__ == "__main__":
    raise SystemExit(main(sys.argv))
