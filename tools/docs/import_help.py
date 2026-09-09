#!/usr/bin/env python3
"""Import the help.hubzero.org documentation into docs/ as Markdown.

Source, in order of preference:

1. ``docs/_import/help-export.json``, the full-table export written by
   ``tools/docs/export_help_db.php`` (includes unpublished trees such as 2.2).
2. The public API, ``/api/documentation/articles/list``, which returns only
   published articles. Its response is cached at ``docs/_import/help-api.json``.

Each article's CKEditor HTML is converted to Markdown, its images are
downloaded into the book's ``media/`` directory, its links to other
documentation pages are rewritten to relative Markdown links, and the page is
written with a metadata header (``status: imported``) that the site builder
turns into a "not yet reviewed" banner. Old URLs are recorded in
``gh-pages/redirects.json`` so they keep resolving.

An older version tree can be merged in with ``--merge-from 220``: pages the
2.4 tree lacks are imported next to their 2.4 siblings and marked
``merged-from``; pages both trees have are compared, and where the older text
differs substantially it is appended under a marked heading for the reviewer.

The importer never overwrites a page whose header says anything other than
``status: imported`` or ``status: merged``, so hand-written landing pages and
reviewed chapters survive a rerun.

Usage:
    python3 tools/docs/import_help.py [--merge-from 220] [--no-fetch] [--dry-run]
"""

from __future__ import annotations

import argparse
import base64
import datetime
import hashlib
import json
import mimetypes
import os
import re
import sys
import urllib.error
import urllib.parse
import urllib.request
from collections import Counter, defaultdict
from dataclasses import dataclass, field
from pathlib import Path, PurePosixPath

try:
    from bs4 import BeautifulSoup, Comment, NavigableString, Tag
except ImportError as exc:  # pragma: no cover
    raise SystemExit("beautifulsoup4 is required: python3 -m pip install beautifulsoup4 lxml") from exc

ROOT = Path(__file__).resolve().parents[2]
sys.path.insert(0, str(ROOT / "gh-pages"))
from build_site import parse_meta, slug_for, slugify  # noqa: E402


def slugify_heading(html: str) -> str:
    return slugify(re.sub(r"<[^>]+>", "", html).replace("`", "").strip())

DOCS = ROOT / "docs"
IMPORT_DIR = DOCS / "_import"
API_URL = "https://help.hubzero.org/api/documentation/articles/list"
SITE = "https://help.hubzero.org"
TODAY = datetime.date.today().isoformat()
USER_AGENT = "hubzero-cms docs importer (+https://github.com/hubzero/hubzero-cms)"

# Old section -> new book/section. Longest prefix wins. A value ending in "/"
# keeps the remainder of the old path beneath it; otherwise the page lands at
# exactly that file.
CMS_MAP = [
    ("introduction", "getting-started/introduction.md"),
    ("installation", "installation/"),
    ("managers", "managers/"),
    ("users", "users/"),
    ("webdevs/index/contributions", "contributing/contributions.md"),
    ("webdevs/conventions", "contributing/conventions/"),
    ("webdevs", "developers/"),
]
# Old alias -> new file/directory name, applied in place (the page keeps its
# position among its siblings).
RENAMES = {
    "managers/index": "getting-started",
    "managers/fqas": "faq",
    "managers/extensions/extmanger": "extension-manager",
    "users/fqas": "faq",
    "webdevs/index": "getting-started",
    "webdevs/supergroups_gitlab": "supergroups-gitlab",
}
PLATFORM_MAP = [
    ("tooldevs", "tools/developers/"),
    ("tool-administrators", "tools/administrators/"),
    ("users", "tools/users/"),
]
# Old 2.4 sections that are not worth importing: unpublished leftovers from
# the Joomla era whose subject is covered elsewhere.
CMS_SKIP = (
    "managers/other", "managers/amazon", "managers/hubmanagement",
    # components that are no longer part of the 2.4 tree
    "managers/components/feedaggregator", "managers/components/geosearch",
    "users/feedaggregator", "users/geosearch",
)

# Groups of tiny old pages that read better as one page with a section each.
# target -> (title, intro, [old rel paths in order]). Old URLs redirect to the
# combined page.
CONSOLIDATE = {
    "optional-services.md": (
        "Optional services",
        "Each of these services is installed from its own Hubzero package and switched on with "
        "`hzcms configure <service> --enable`. Install only the ones your hub needs; none of them is "
        "required for the CMS itself.",
        [
            "installation/el8/install/installmailgateway", "installation/el8/install/openldap",
            "installation/el8/install/firewall", "installation/el8/install/subversion",
            "installation/el8/install/trac", "installation/el8/install/forge",
            "installation/el8/install/openvz", "installation/el8/install/maxwell_file_service",
            "installation/el8/install/maxwell_client", "installation/el8/install/metrics",
            "installation/el8/install/workspace", "installation/el8/install/filexfer",
            "installation/el8/install/solr",
        ],
    ),
    "user-notes.md": (
        "User notes",
        "Administrators can attach notes to member accounts and file them under categories.",
        ["managers/users/usernotes", "managers/users/usernotecateg"],
    ),
}
CONSOLIDATED_RELS = {rel: name for name, (_, _, rels) in CONSOLIDATE.items() for rel in rels}
CONSOLIDATED_TARGETS: dict[str, str] = {}   # name -> docs-relative target, filled during placement

# Old paths whose page is a thin stub between two real levels; the stub is
# collapsed so its children move up one level.
COLLAPSE = {"installation/el8/install": "installation/el8"}

# Merging an older tree (--merge-from): sections to leave behind, and where
# the sections that survive but have no 2.4 counterpart go.
MERGE_SKIP = (
    "installation/centos7",   # obsolete platforms; the EL8 pages cover the same steps
    "installation/debian",
    "installation/redhat",
    "installation/virtualbox",
    "internaldocs",           # internal operations material, not user documentation
    "releasenotes",           # a single "3.0" page
)
MERGE_MAP = [
    ("security_considerations", "managers/security/"),
    ("toolsnewdocs", "tools/developers/"),   # an older draft of tooldevs; only its unique pages are kept
]

ADMONITION_CLASSES = {
    "note": "Note", "info": "Note", "help": "Tip", "warning": "Warning",
    "error": "Important", "important": "Important", "caution": "Caution",
}
CODE_LANGS = {
    "php": "php", "html": "html", "css": "css", "ini": "ini", "sql": "sql", "xml": "xml",
    "javascript": "javascript", "js": "javascript", "bash": "bash", "sh": "bash", "shell": "bash",
    "python": "python", "json": "json", "yaml": "yaml", "apache": "apache", "text": "",
}
INLINE_TAGS = {"a", "abbr", "b", "strong", "em", "i", "code", "tt", "span", "font", "u", "sup",
               "sub", "br", "img", "kbd", "small", "big", "s", "strike", "del", "ins", "mark", "label", "www"}


# --------------------------------------------------------------------------- source rows


def load_rows(no_fetch: bool) -> list[dict]:
    export = IMPORT_DIR / "help-export.json"
    cache = IMPORT_DIR / "help-api.json"
    if export.exists():
        print(f"reading {export.relative_to(ROOT)}")
        return json.loads(export.read_text(encoding="utf-8"))
    if cache.exists() and (no_fetch or True):
        rows = json.loads(cache.read_text(encoding="utf-8"))
        if no_fetch or rows:
            print(f"reading {cache.relative_to(ROOT)} ({len(rows)} articles)")
            return rows
    if no_fetch:
        raise SystemExit("no export or cache found and --no-fetch given")
    rows: list[dict] = []
    start = 0
    while True:
        url = f"{API_URL}?limit=500&start={start}"
        print(f"fetching {url}")
        with urllib.request.urlopen(urllib.request.Request(url, headers={"User-Agent": USER_AGENT}), timeout=90) as r:
            payload = json.load(r)
        batch = payload.get("articles", [])
        rows.extend(batch)
        if len(batch) < 500:
            break
        start += 500
    IMPORT_DIR.mkdir(parents=True, exist_ok=True)
    cache.write_text(json.dumps(rows, ensure_ascii=False), encoding="utf-8")
    print(f"cached {len(rows)} articles at {cache.relative_to(ROOT)}")
    return rows


# --------------------------------------------------------------------------- tree


@dataclass
class Node:
    row: dict
    children: list["Node"] = field(default_factory=list)
    parent: "Node | None" = None
    target: str | None = None          # docs-relative file path, e.g. managers/components/kb.md
    collapsed: bool = False
    skipped: bool = False
    consolidated_into: str | None = None

    @property
    def id(self) -> int:
        return int(self.row["id"])

    @property
    def path(self) -> str:
        return self.row["path"]

    @property
    def alias(self) -> str:
        return self.row["alias"]

    @property
    def title(self) -> str:
        return (self.row.get("title") or self.alias).strip()

    @property
    def content(self) -> str:
        return self.row.get("content") or ""

    @property
    def level(self) -> int:
        return int(self.row["level"])

    @property
    def rel_path(self) -> str:
        """Path below the version root."""
        return "/".join(self.path.split("/")[1:])

    def walk(self):
        yield self
        for child in self.children:
            yield from child.walk()


def build_tree(rows: list[dict]) -> dict[str, Node]:
    nodes = {int(r["id"]): Node(r) for r in rows}
    for node in nodes.values():
        parent = nodes.get(int(node.row.get("parent_id") or 0))
        if parent is not None:
            node.parent = parent
            parent.children.append(node)
    for node in nodes.values():
        node.children.sort(key=lambda n: int(n.row["lft"]))
    return {n.alias: n for n in nodes.values() if n.level == 1}


def word_count(html: str) -> int:
    return len(re.sub(r"<[^>]+>", " ", html).split())


# --------------------------------------------------------------------------- target paths


def map_prefix(rel: str, table: list[tuple[str, str]]) -> str | None:
    for old, new in sorted(table, key=lambda t: -len(t[0])):
        if rel == old:
            return new
        if rel.startswith(old + "/"):
            if not new.endswith("/"):
                new = new[:-3] + "/" if new.endswith(".md") else new + "/"
            return new + rel[len(old) + 1:]
    return None


def exact_map(rel: str, table: list[tuple[str, str]]) -> str | None:
    for old, new in table:
        if rel == old:
            return new
    return None


def assign_targets(root: Node, table: list[tuple[str, str]], book_default: str | None) -> None:
    """Give every node its docs-relative target path, top down.

    A node named in the mapping table lands exactly where the table says.
    Every other node lands under its parent's directory, named by its alias.
    Sections (nodes with children) become directories with a README.md;
    leaves become files. Siblings whose natural order is not alphabetical get
    a numeric prefix so the reading order survives; the site builder drops
    the prefix from URLs. A collapsed stub contributes its content to its
    parent's landing page and its children move up one level.
    """
    root.target = f"{book_default}/README.md" if book_default else None

    def expand(children: list[Node]) -> list[Node]:
        """Replace collapsed stubs with their children so siblings are numbered as one list."""
        out: list[Node] = []
        for child in children:
            if child.rel_path in COLLAPSE:
                child.collapsed = True
                child.target = None
                out.extend(expand(child.children))
            else:
                out.append(child)
        return out

    def visit(node: Node, parent_dir: str | None) -> None:
        placements: list[tuple[Node, str | None, bool]] = []
        for child in expand(node.children):
            rel = child.rel_path
            if table is CMS_MAP and any(rel == skip or rel.startswith(skip + "/") for skip in CMS_SKIP):
                child.target = None
                child.skipped = True
                placements.append((child, None, False))
                continue
            if table is CMS_MAP and rel in CONSOLIDATED_RELS and parent_dir is not None:
                child.target = None
                child.consolidated_into = f"{parent_dir}/{CONSOLIDATED_RELS[rel]}"
                CONSOLIDATED_TARGETS[CONSOLIDATED_RELS[rel]] = child.consolidated_into
                placements.append((child, None, False))
                continue
            for stub, replacement in COLLAPSE.items():
                if rel.startswith(stub + "/"):
                    rel = replacement + rel[len(stub):]
            mapped = exact_map(rel, table)
            if mapped is not None:
                placements.append((child, mapped, True))
            elif parent_dir is None:
                child.target = None
                placements.append((child, None, False))
            else:
                name = RENAMES.get(child.rel_path, child.alias)
                placements.append((child, f"{parent_dir}/{name}", False))

        auto = [b for _, b, explicit in placements if b and not explicit]
        slugs = [PurePosixPath(b).name.removesuffix(".md") for b in auto]
        needs_order = len(slugs) > 1 and slugs != sorted(slugs)

        index = 0
        for child, base, explicit in placements:
            if base is None:
                continue
            if base.endswith("/"):
                child_dir = base.rstrip("/")
                child.target = f"{child_dir}/README.md"
            else:
                path = PurePosixPath(base)
                stem = path.name.removesuffix(".md")
                if not explicit:
                    index += 1
                    if needs_order and not re.match(r"^\d+-", stem):
                        stem = f"{index:02d}-{stem}"
                child_dir = str(path.parent / stem)
                if child.children:
                    child.target = f"{child_dir}/README.md"
                else:
                    child.target = f"{child_dir}.md"
            visit(child, child_dir)

    visit(root, book_default)

    for node in root.walk():
        if node.target:
            node.target = existing_variant(node.target)
        if node.consolidated_into:
            node.consolidated_into = existing_variant(node.consolidated_into)
    for name, target in list(CONSOLIDATED_TARGETS.items()):
        CONSOLIDATED_TARGETS[name] = existing_variant(target)


def existing_variant(target: str) -> str:
    """Reuse an on-disk file or directory whose slug matches, whatever its numeric prefix.

    Prefixes are positional, so adding or dropping a sibling renumbers the rest. A page
    that has since been reviewed must not be written a second time under a new number
    just because its neighbours moved; the file already there wins.
    """
    parts = PurePosixPath(target).parts
    resolved: list[str] = []
    current = DOCS
    for index, part in enumerate(parts):
        last = index == len(parts) - 1
        if (current / part).exists():
            resolved.append(part)
        else:
            want = slug_for(part)
            match = None
            if current.is_dir():
                for entry in sorted(current.iterdir()):
                    if entry.name.startswith(".") or entry.is_file() != last:
                        continue
                    if last and entry.suffix != ".md":
                        continue
                    if slug_for(entry.name) == want:
                        match = entry.name
                        break
            resolved.append(match or part)
        current = current / resolved[-1]
    return "/".join(resolved)


def output_href(target: str) -> str:
    """The built-site path for a docs-relative Markdown file, as the builder computes it."""
    parts = PurePosixPath(target).parts
    if parts[-1] == "README.md":
        segs = [slug_for(p) for p in parts[:-1]]
    else:
        segs = [slug_for(p) for p in parts[:-1]] + [slug_for(parts[-1])]
    return "/".join(segs) + "/index.html"


# --------------------------------------------------------------------------- media


class MediaStore:
    def __init__(self, cache_dir: Path, fetch: bool, report: "Report"):
        self.cache_dir = cache_dir
        self.fetch = fetch
        self.report = report
        self.cache_dir.mkdir(parents=True, exist_ok=True)
        self.index_path = cache_dir / "index.json"
        self.index: dict[str, str] = json.loads(self.index_path.read_text()) if self.index_path.exists() else {}

    def save_index(self) -> None:
        self.index_path.write_text(json.dumps(self.index, indent=1, sort_keys=True))

    def fetch_bytes(self, url: str) -> tuple[bytes, str] | None:
        key = hashlib.sha1(url.encode()).hexdigest()
        cached = self.index.get(key)
        if cached:
            path = self.cache_dir / cached
            if path.exists():
                return path.read_bytes(), path.suffix
            if cached == "FAILED":
                return None
        if not self.fetch:
            return None
        try:
            safe_url = urllib.parse.quote(url, safe=":/?&=%#+@,;~")
            req = urllib.request.Request(safe_url, headers={"User-Agent": USER_AGENT})
            with urllib.request.urlopen(req, timeout=30) as r:
                data = r.read()
                ctype = r.headers.get_content_type()
        except Exception as exc:  # noqa: BLE001 - any fetch failure is recorded, never fatal
            self.report.media_failed.append(f"{url}: {exc}")
            self.index[key] = "FAILED"
            return None
        ext = PurePosixPath(urllib.parse.urlparse(url).path).suffix.lower()
        if ext not in (".png", ".jpg", ".jpeg", ".gif", ".svg", ".webp"):
            ext = mimetypes.guess_extension(ctype) or ".png"
            ext = {".jpe": ".jpg"}.get(ext, ext)
        name = f"{key}{ext}"
        (self.cache_dir / name).write_bytes(data)
        self.index[key] = name
        return data, ext

    def place(self, src: str, media_dir: Path, stem_hint: str) -> str | None:
        """Download or decode an image and write it under media_dir. Returns the filename."""
        if src.startswith("data:"):
            match = re.match(r"data:(image/[a-z+]+);base64,(.*)", src, re.S)
            if not match:
                return None
            ext = "." + match.group(1).split("/")[1].replace("jpeg", "jpg").replace("svg+xml", "svg")
            data = base64.b64decode(match.group(2))
            basename = hashlib.sha1(data).hexdigest()[:10]
        else:
            url = absolutize(src)
            failures_before = len(self.report.media_failed)
            result = self.fetch_bytes(url)
            if result is None:
                match = re.match(r"^(https://help\.hubzero\.org/app/site/documentation/)([^/]+)(/.*)$", url)
                if match:
                    for older in ("220", "2-1-0", "2-0-0", "1-3-0", "1-2-2", "1-2-0", "1-1-0", "1-0-0"):
                        if older == match.group(2):
                            continue
                        result = self.fetch_bytes(match.group(1) + older + match.group(3))
                        if result is not None:
                            del self.report.media_failed[failures_before:]  # found in an older tree
                            break
            if result is None:
                return None
            data, ext = result
            basename = PurePosixPath(urllib.parse.urlparse(url).path).stem or "image"
            basename = re.sub(r"[^a-z0-9]+", "-", basename.lower()).strip("-") or "image"
        media_dir.mkdir(parents=True, exist_ok=True)
        name = f"{stem_hint}-{basename}{ext}" if stem_hint else f"{basename}{ext}"
        name = re.sub(r"-+", "-", name)
        target = media_dir / name
        if target.exists() and target.read_bytes() != data:
            digest = hashlib.sha1(data).hexdigest()[:6]
            target = media_dir / f"{target.stem}-{digest}{ext}"
        if not target.exists():
            target.write_bytes(data)
        return target.name


def absolutize(src: str) -> str:
    src = src.strip()
    if src.startswith("//"):
        return "https:" + src
    if src.startswith(("http://", "https://")):
        return src
    if src.startswith("/site/"):
        return SITE + "/app" + src
    if src.startswith("/"):
        return SITE + src
    return SITE + "/" + src


# --------------------------------------------------------------------------- converter


@dataclass
class Report:
    pages: int = 0
    words: int = 0
    images: int = 0
    media_failed: list[str] = field(default_factory=list)
    tables_html: list[str] = field(default_factory=list)
    unresolved_links: list[str] = field(default_factory=list)
    dropped_fragments: list[str] = field(default_factory=list)
    skipped: list[str] = field(default_factory=list)
    orphans: list[str] = field(default_factory=list)
    protected: list[str] = field(default_factory=list)
    merged_new: list[str] = field(default_factory=list)
    merged_appended: list[str] = field(default_factory=list)
    merged_same: list[str] = field(default_factory=list)
    merged_skipped: list[str] = field(default_factory=list)
    counters: Counter = field(default_factory=Counter)


class Converter:
    """CKEditor HTML -> Markdown, for one page at a time."""

    def __init__(self, media: MediaStore, link_map: dict[str, str], report: Report, anchors_wanted: dict[str, bool] | None = None):
        self.media = media
        self.link_map = link_map   # old rel path (version/x/y) -> docs target file
        self.report = report
        self.anchors_wanted = anchors_wanted or {}
        self.ids_by_target: dict[str, set[str]] = {}

    # ---- entry point

    def convert(self, html: str, target: str, page_stem: str, version_alias: str = "240") -> str:
        self.target = target
        self.version_alias = version_alias
        self.version_path = version_alias.replace(".", "-")
        self.media_dir = DOCS / PurePosixPath(target).parts[0] / "media"
        self.page_stem = page_stem
        html = html.replace("&nbsp;", " ").replace("\u00a0", " ").replace("\r\n", "\n")
        html = html.replace("{{versionpath}}", self.version_path).replace("{{version}}", self.version_alias)
        html = re.sub(r"</?(?:font|www)\b[^>]*>", "", html)
        soup = BeautifulSoup(html, "html.parser")
        for comment in soup.find_all(string=lambda s: isinstance(s, Comment)):
            comment.extract()
        for tag in soup.find_all(["script", "style"]):
            tag.decompose()
        body = self.blocks(soup)
        body = re.sub(r"\n{3,}", "\n\n", body).strip() + "\n"
        return body

    # ---- blocks

    def blocks(self, node: Tag, indent: str = "") -> str:
        """Render the children of `node` as a sequence of Markdown blocks."""
        out: list[str] = []
        inline_run: list = []

        def flush_inline() -> None:
            if not inline_run:
                return
            text = self.inline_nodes(inline_run).strip()
            inline_run.clear()
            if text:
                out.append(self.escape_block_start(text) + "\n\n")

        for child in node.children:
            if isinstance(child, NavigableString):
                if isinstance(child, Comment):
                    continue
                inline_run.append(child)
                continue
            if not isinstance(child, Tag):
                continue
            if child.name in INLINE_TAGS:
                inline_run.append(child)
                continue
            flush_inline()
            out.append(self.block(child, indent))
        flush_inline()
        text = "".join(out)
        if indent:
            text = "\n".join((indent + line) if line.strip() else line for line in text.splitlines()) + "\n"
        return text

    def block(self, tag: Tag, indent: str = "") -> str:
        name = tag.name
        classes = set(tag.get("class") or [])

        if name in ("section", "div", "article", "center", "main", "header", "footer", "aside", "body", "html", "form", "fieldset"):
            anchor = self.anchor_for(tag)
            kind = next((ADMONITION_CLASSES[c] for c in classes if c in ADMONITION_CLASSES), None)
            if kind and not tag.find(["pre", "table", "ul", "ol", "h2", "h3", "h4"]):
                return anchor + self.admonition(kind, tag)
            if "img-wrap" in classes:
                return anchor + self.figure(tag)
            return anchor + self.blocks(tag)

        if name == "p":
            kind = next((ADMONITION_CLASSES[c] for c in classes if c in ADMONITION_CLASSES), None)
            if kind:
                return self.admonition(kind, tag)
            if tag.find(["ul", "ol", "pre", "table", "div"]):
                return self.blocks(tag)
            text = self.inline(tag).strip()
            if not text:
                return ""
            label = re.match(r"^\*\*(Note|Tip|Warning|Important|Caution)s?:?\*\*:?\s*", text, re.I)
            if label:
                kind = label.group(1).capitalize()
                rest = text[label.end():]
                self.report.counters["admonitions"] += 1
                lines = rest.splitlines() or [""]
                return f"> **{kind}:** {lines[0]}" + "".join("\n> " + l for l in lines[1:]) + "\n\n"
            return self.escape_block_start(text) + "\n\n"

        if name in ("h1", "h2", "h3", "h4", "h5", "h6"):
            level = {"h1": 2, "h2": 2, "h3": 2, "h4": 3, "h5": 4, "h6": 5}[name]
            text = re.sub(r"\s+", " ", self.inline(tag)).strip()
            if not text:
                return ""
            self.report.counters["headings"] += 1
            return f"{'#' * level} {text}\n\n"

        if name == "pre":
            return self.code_block(tag)

        if name in ("ul", "ol"):
            return self.list_block(tag)

        if name == "li":  # stray li outside a list
            return "- " + self.blocks(tag, "  ").lstrip()

        if name == "table":
            return self.table(tag)

        if name == "dl":
            return self.definition_list(tag)

        if name == "hr":
            return "---\n\n"

        if name == "blockquote":
            kind = next((ADMONITION_CLASSES[c] for c in classes if c in ADMONITION_CLASSES), None)
            if kind:
                return self.admonition(kind, tag)
            inner = self.blocks(tag).strip()
            return "\n".join("> " + line if line else ">" for line in inner.splitlines()) + "\n\n"

        if name == "figure":
            return self.figure(tag)

        if name == "iframe":
            src = tag.get("src", "")
            if src:
                self.report.counters["iframes"] += 1
                return f"[Embedded video]({absolutize(src)})\n\n"
            return ""

        if name in ("figcaption",):
            text = self.inline(tag).strip()
            return f"*{text}*\n\n" if text else ""

        if name in ("thead", "tbody", "tr", "td", "th", "dt", "dd", "caption"):
            return self.blocks(tag)

        if name in ("img",):
            return self.inline_nodes([tag]).strip() + "\n\n"

        if name in ("object", "embed", "video", "audio", "noscript", "input", "button", "select", "textarea"):
            return ""

        # unknown block: render children
        self.report.counters[f"unknown:{name}"] += 1
        return self.blocks(tag)

    def anchor_for(self, tag: Tag) -> str:
        """Keep the old page's in-page anchors so deep links from other pages survive."""
        ident = (tag.get("id") or "").strip()
        if not ident or ident.startswith("section-") and not self.anchors_wanted.get(ident):
            return ""
        return f'<a id="{ident}"></a>\n\n'

    def admonition(self, kind: str, tag: Tag) -> str:
        if tag.name == "p":
            text = self.inline(tag).strip()
            body = text
        else:
            body = self.blocks(tag).strip()
        if not body:
            return ""
        body = re.sub(rf"^\*\*{kind}:?\*\*:?\s*", "", body, flags=re.I)
        body = re.sub(rf"^{kind}:\s*", "", body, flags=re.I)
        lines = body.splitlines()
        self.report.counters["admonitions"] += 1
        first = f"> **{kind}:** {lines[0]}" if lines else f"> **{kind}:**"
        rest = "\n".join(("> " + line) if line.strip() else ">" for line in lines[1:])
        return first + ("\n" + rest if rest else "") + "\n\n"

    def figure(self, tag: Tag) -> str:
        img = tag.find("img")
        parts = []
        if img is not None:
            parts.append(self.image(img).strip())
            img.extract()
        caption = re.sub(r"\s+", " ", self.inline(tag)).strip()
        if caption:
            parts.append(f"*{caption}*")
        return "\n\n".join(p for p in parts if p) + "\n\n" if parts else ""

    def code_block(self, tag: Tag) -> str:
        lang = ""
        for cls in tag.get("class") or []:
            head = cls.split(":")[0].replace("language-", "").replace("lang-", "").lower()
            if head in CODE_LANGS:
                lang = CODE_LANGS[head]
                break
        code = tag.get_text()
        code = code.replace("\u00a0", " ").strip("\n")
        code = "\n".join(line.rstrip() for line in code.splitlines())
        fence = "```" if "```" not in code else "````"
        self.report.counters["code_blocks"] += 1
        return f"{fence}{lang}\n{code}\n{fence}\n\n"

    def list_block(self, tag: Tag, depth: int = 0) -> str:
        ordered = tag.name == "ol"
        try:
            start = int(tag.get("start", 1))
        except ValueError:
            start = 1
        out = []
        number = start
        for li in tag.find_all("li", recursive=False):
            if re.fullmatch(r"\s*\d{1,2}\s*", li.get_text()) and li.find("img") is None:
                self.report.counters["placeholder_items_dropped"] += 1
                continue
            marker = f"{number}. " if ordered else "- "
            pad = " " * len(marker)
            # inline content first, then nested blocks
            inline_bits = []
            nested = []
            for child in li.children:
                if isinstance(child, Tag) and child.name not in INLINE_TAGS:
                    nested.append(child)
                else:
                    inline_bits.append(child)
            head = self.inline_nodes(inline_bits).strip()
            body_parts = []
            for child in nested:
                if child.name in ("ul", "ol"):
                    body_parts.append(self.list_block(child, depth + 1))
                else:
                    rendered = self.block(child).rstrip("\n")
                    if rendered:
                        body_parts.append(rendered + "\n")
            head_lines = head.splitlines() or [""]
            first = marker + head_lines[0]
            rest = [pad + line for line in head_lines[1:]]
            item = "\n".join([first] + rest)
            if body_parts:
                body = "\n".join(bp.rstrip("\n") for bp in body_parts)
                body = "\n".join((pad + line) if line.strip() else "" for line in body.splitlines())
                item += ("\n" if head else "") + body if head else body.lstrip()
                if not head:
                    item = marker + body.lstrip()[len(pad):] if body.lstrip().startswith(pad) else marker + body.lstrip()
            out.append(item)
            number += 1
        self.report.counters["lists"] += 1
        return "\n".join(out) + "\n\n"

    def table(self, tag: Tag) -> str:
        rows = tag.find_all("tr")
        simple = bool(rows)
        grid: list[list[str]] = []
        header: list[str] | None = None
        for tr in rows:
            cells = tr.find_all(["td", "th"], recursive=False)
            if not cells:
                continue
            if any(c.has_attr("colspan") or c.has_attr("rowspan") for c in cells):
                simple = False
            if any(c.find(["ul", "ol", "pre", "table", "h2", "h3", "h4"]) for c in cells):
                simple = False
            texts = [re.sub(r"\s+", " ", self.inline(c).replace("|", "\\|")).strip().replace("\n", " ") for c in cells]
            if header is None and all(c.name == "th" for c in cells):
                header = texts
            else:
                grid.append(texts)
        widths = {len(r) for r in grid} | ({len(header)} if header else set())
        if len(widths) != 1:
            simple = False
        if not simple or not grid and not header:
            self.report.tables_html.append(self.target)
            return self.raw_table(tag)
        width = widths.pop()
        if header is None:
            header, grid = grid[0], grid[1:]
        lines = ["| " + " | ".join(header) + " |", "|" + "---|" * width]
        for row in grid:
            lines.append("| " + " | ".join(row) + " |")
        self.report.counters["tables"] += 1
        return "\n".join(lines) + "\n\n"

    def raw_table(self, tag: Tag) -> str:
        for el in tag.find_all(True):
            for attr in ("style", "class", "width", "height", "border", "cellpadding", "cellspacing", "align", "valign", "bgcolor"):
                if el.has_attr(attr):
                    del el[attr]
            if el.name == "img" and el.get("src"):
                name = self.media.place(el["src"], self.media_dir, self.page_stem)
                if name:
                    el["src"] = self.media_rel(name)
            if el.name == "a" and el.get("href"):
                el["href"] = self.link(el["href"])
        for attr in list(tag.attrs):
            del tag[attr]
        self.report.counters["tables_html"] += 1
        html = str(tag)
        html = re.sub(r"\n\s*\n", "\n", html)
        return html + "\n\n"

    def definition_list(self, tag: Tag) -> str:
        out = []
        term = None
        for child in tag.children:
            if not isinstance(child, Tag):
                continue
            if child.name == "dt":
                term = re.sub(r"\s+", " ", self.inline(child)).strip()
            elif child.name == "dd":
                definition = self.blocks(child).strip()
                definition = "\n  ".join(definition.splitlines())
                out.append(f"- **{term}**" + (f"  \n  {definition}" if definition else "") if term else f"- {definition}")
                term = None
        if term:
            out.append(f"- **{term}**")
        return "\n".join(out) + "\n\n"

    # ---- inline

    def inline(self, tag: Tag) -> str:
        return self.inline_nodes(list(tag.children))

    def inline_nodes(self, nodes: list) -> str:
        parts = []
        for node in nodes:
            if isinstance(node, Comment):
                continue
            if isinstance(node, NavigableString):
                parts.append(self.text(str(node)))
            elif isinstance(node, Tag):
                parts.append(self.inline_tag(node))
        text = "".join(parts)
        text = re.sub(r"[ \t]+", " ", text)
        text = re.sub(r" ?\n ?", "\n", text)
        return text

    def inline_tag(self, tag: Tag) -> str:
        name = tag.name
        if name == "br":
            return "  \n"
        if name == "img":
            return self.image(tag)
        if name in ("strong", "b", "em", "i"):
            raw = self.inline(tag)
            inner = raw.strip()
            if not inner:
                return " " if raw else ""
            lead = " " if raw[:1].isspace() else ""
            trail = " " if raw[-1:].isspace() else ""
            marker = "**" if name in ("strong", "b") else "*"
            return f"{lead}{marker}{inner}{marker}{trail}"
        if name in ("code", "tt", "kbd", "samp"):
            code = tag.get_text().replace("\n", " ").strip()
            if not code:
                return ""
            ticks = "``" if "`" in code else "`"
            return f"{ticks}{code}{ticks}"
        if name == "a":
            href = tag.get("href", "").strip()
            ident = (tag.get("name") or tag.get("id") or "").strip()
            if ident and not href:
                return f'<a id="{ident}"></a>' + self.inline(tag)
            inner = self.inline(tag).strip()
            if not href:
                return inner
            resolved = self.link(href)
            if not inner:
                inner = resolved
            if inner == resolved and resolved.startswith("http"):
                return f"<{resolved}>"
            return f"[{inner}]({resolved})"
        if name in ("sup", "sub"):
            return f"<{name}>{self.inline(tag).strip()}</{name}>"
        if name in ("s", "strike", "del"):
            return f"~~{self.inline(tag).strip()}~~"
        if name in ("abbr", "span", "font", "u", "small", "big", "label", "ins", "mark", "www"):
            return self.inline(tag)
        # block inside inline context (a <div> inside <a>): render children
        return self.blocks(tag).strip()

    def image(self, tag: Tag) -> str:
        src = tag.get("src", "").strip()
        if not src:
            return ""
        alt = re.sub(r"\s+", " ", tag.get("alt", "") or "").strip()
        name = self.media.place(src, self.media_dir, self.page_stem)
        if name is None:
            return f"![{alt}]({absolutize(src)})"
        self.report.images += 1
        return f"![{alt}]({self.media_rel(name)})"

    def media_rel(self, name: str) -> str:
        depth = len(PurePosixPath(self.target).parts) - 2  # parts below the book dir
        return ("../" * depth) + "media/" + name

    def link(self, href: str) -> str:
        href = href.strip().replace("&amp;", "&")
        if href.startswith(("mailto:", "#", "tel:")):
            return href
        if href.startswith("//"):
            href = "https:" + href
        parsed = urllib.parse.urlparse(href)
        path = parsed.path
        fragment = f"#{parsed.fragment}" if parsed.fragment else ""
        host = parsed.netloc.lower()
        is_help = host in ("", "help.hubzero.org", "hubzero.org", "www.hubzero.org")
        if is_help:
            match = re.match(r"^/?documentation/([^/]+)(?:/(.*))?$", path)
            if match:
                version, rest = match.group(1), match.group(2) or ""
                if version in ("{{version}}", "current", "{{versionpath}}"):
                    version = "240"
                segments = []
                for i, seg in enumerate(rest.split("/") if rest else []):
                    segments.extend(seg.split(".") if i > 0 and "." in seg and not seg.endswith((".html", ".php")) else [seg])
                rest_path = "/".join(s for s in segments if s)
                old = f"{version}/{rest_path}" if rest_path else version
                target = self.link_map.get(old)
                if target is None and rest_path:
                    # a link into an older version: the same page in the current tree
                    target = self.link_map.get(f"240/{rest_path}")
                    if target is None and rest_path.startswith("tooldevs"):
                        target = self.link_map.get(f"platform_2_4/{rest_path}")
                if target is None and "/" in rest_path:
                    # a page that was filed under a different top section in an older tree
                    tail = rest_path.split("/", 1)[1]
                    target = self.link_map.get(f"{version}/{tail}") or self.link_map.get(f"240/{tail}")
                if target:
                    if fragment and fragment[1:] not in self.ids_by_target.get(target, set()):
                        self.report.dropped_fragments.append(f"{self.target}: {href}")
                        fragment = ""
                    return self.relative_link(target) + fragment
                self.report.unresolved_links.append(f"{self.target}: {href}")
                return f"{SITE}/documentation/{version}/{rest}{fragment}"
            if not parsed.scheme:
                if path.startswith("/site/"):
                    return SITE + "/app" + path + fragment
                return SITE + ("/" if not path.startswith("/") else "") + path + (f"?{parsed.query}" if parsed.query else "") + fragment
        return href

    def relative_link(self, target: str) -> str:
        here = PurePosixPath(self.target).parent
        rel = os.path.relpath(target, str(here)).replace(os.sep, "/")
        return rel

    # ---- text

    def text(self, raw: str) -> str:
        text = raw.replace("\u00a0", " ")
        text = re.sub(r"\s+", " ", text)
        text = text.replace("\\", "\\\\")
        text = re.sub(r"<(?=[a-zA-Z/!])", "&lt;", text)
        text = re.sub(r"&(?=[a-zA-Z]+;|#\d+;)", "&amp;", text)
        text = text.replace("*", "\\*")
        text = re.sub(r"(?<![\w])_(?=\w)|(?<=\w)_(?![\w])", "\\_", text)
        text = text.replace("[[", "\\[\\[").replace("]]", "\\]\\]")
        return text

    @staticmethod
    def escape_block_start(text: str) -> str:
        lines = text.split("\n")
        fixed = []
        for line in lines:
            if re.match(r"^\s*(#{1,6}\s|>|[-+]\s|\d+[.)]\s|---|\*\*\*|```|~~~)", line):
                line = "\\" + line.lstrip() if not line.lstrip().startswith("\\") else line
            fixed.append(line)
        return "\n".join(fixed)


# --------------------------------------------------------------------------- pages


def header_for(node: Node, version_alias: str, extra: dict[str, str] | None = None) -> str:
    modified = (node.row.get("modified") or "")[:10]
    if not re.match(r"^[12]\d{3}-", modified):
        modified = (node.row.get("created") or "")[:10]
    if not re.match(r"^[12]\d{3}-", modified):
        modified = ""
    lines = {
        "status": "imported",
        "source": f"{SITE}/documentation/{node.path}",
        "source-id": str(node.id),
        "modified": modified,
        "imported": TODAY,
    }
    if extra:
        lines.update(extra)
    return "<!--\n" + "\n".join(f"{k}: {v}" for k, v in lines.items() if v) + "\n-->\n"


def importer_owns(path: Path) -> bool:
    if not path.exists():
        return True
    meta, _ = parse_meta(path.read_text(encoding="utf-8"))
    return meta.get("status") in ("imported", "merged")


def normalized_words(html: str) -> list[str]:
    return re.findall(r"[a-z0-9]+", re.sub(r"<[^>]+>", " ", html).lower())


def similarity(a: str, b: str) -> float:
    wa, wb = Counter(normalized_words(a)), Counter(normalized_words(b))
    if not wa and not wb:
        return 1.0
    inter = sum((wa & wb).values())
    union = sum((wa | wb).values())
    return inter / union if union else 0.0


def main() -> int:
    parser = argparse.ArgumentParser(description=__doc__, formatter_class=argparse.RawDescriptionHelpFormatter)
    parser.add_argument("--merge-from", default=None, help="alias of an older version root to merge, e.g. 220")
    parser.add_argument("--no-fetch", action="store_true", help="never download; use caches only")
    parser.add_argument("--dry-run", action="store_true", help="convert but write nothing")
    parser.add_argument("--clean", action="store_true", help="delete importer-owned pages before writing")
    args = parser.parse_args()

    rows = load_rows(args.no_fetch)
    roots = build_tree(rows)
    report = Report()
    media = MediaStore(IMPORT_DIR / "media-cache", fetch=not args.no_fetch, report=report)

    cms = roots.get("240")
    platform = roots.get("platform_2_4")
    known_ids = {int(r["id"]) for r in rows}
    for row in rows:
        if int(row["level"]) > 1 and int(row.get("parent_id") or 0) not in known_ids:
            report.orphans.append(f"{row['path']} (id {row['id']}, parent {row.get('parent_id')} not exported)")
    if cms is None:
        raise SystemExit(f"no 240 root in the export; roots are {sorted(roots)}")
    assign_targets(cms, CMS_MAP, None)
    cms.target = None  # the version landing page is replaced by docs/README.md
    if platform is not None:
        assign_targets(platform, PLATFORM_MAP, "tools")

    # Link map: every old path (any alias form) -> target file.
    link_map: dict[str, str] = {}
    trees = [cms] + ([platform] if platform else [])
    for root in trees:
        for node in root.walk():
            if node.target:
                link_map[node.path] = node.target
                link_map[node.path.replace("240/", "current/", 1)] = node.target
    # collapsed stubs point at the parent's landing; consolidated pages at the combined page
    for root in trees:
        for node in root.walk():
            if node.collapsed and node.parent and node.parent.target:
                link_map[node.path] = node.parent.target
            if node.consolidated_into:
                link_map[node.path] = node.consolidated_into
                link_map[node.path.replace("240/", "current/", 1)] = node.consolidated_into

    # Optional merge of an older tree.
    merge_root = roots.get(args.merge_from) if args.merge_from else None
    if args.merge_from and merge_root is None:
        raise SystemExit(f"no root with alias {args.merge_from!r}; roots are {sorted(roots)}")
    merged_extras: dict[int, str] = {}   # cms node id -> appended html
    merge_new: list[tuple[Node, str]] = []
    if merge_root is not None:
        twins_by_rel: dict[str, Node] = {}
        for root in trees:
            for node in root.walk():
                if node is root:
                    continue
                if node.target:
                    twins_by_rel.setdefault(node.rel_path, node)
                elif node.collapsed and node.parent is not None and node.parent.target:
                    twins_by_rel.setdefault(node.rel_path, node.parent)  # the stub folded into its parent

        def twin_for(rel: str) -> Node | None:
            if rel in twins_by_rel:
                return twins_by_rel[rel]
            if rel == "toolsnewdocs":
                return twins_by_rel.get("tooldevs")
            if rel.startswith("toolsnewdocs/"):
                rest = rel[len("toolsnewdocs/"):]
                for candidate in (rest, rest.removeprefix("prerequisites/")):
                    if "tooldevs/" + candidate in twins_by_rel:
                        return twins_by_rel["tooldevs/" + candidate]
            return None

        def target_dir_of(node: Node) -> str:
            path = PurePosixPath(node.target or "")
            return str(path.parent) if path.name == "README.md" else str(path.parent / path.name.removesuffix(".md"))

        for old in merge_root.walk():
            if old is merge_root:
                continue
            rel = old.rel_path
            if any(rel == skip or rel.startswith(skip + "/") for skip in MERGE_SKIP + CMS_SKIP):
                report.merged_skipped.append(rel)
                continue
            if rel in CONSOLIDATED_RELS and CONSOLIDATED_RELS[rel] in CONSOLIDATED_TARGETS:
                link_map[old.path] = CONSOLIDATED_TARGETS[CONSOLIDATED_RELS[rel]]
                report.merged_same.append(f"{rel} (consolidated into {CONSOLIDATED_TARGETS[CONSOLIDATED_RELS[rel]]})")
                continue
            twin = twin_for(rel)
            if twin is not None:
                link_map[old.path] = twin.target
                sim = similarity(old.content, twin.content)
                if sim >= 0.8 or word_count(old.content) <= word_count(twin.content) * 1.05:
                    report.merged_same.append(f"{rel} (similarity {sim:.2f})")
                else:
                    merged_extras[twin.id] = old.content
                    report.merged_appended.append(f"{rel} (similarity {sim:.2f}, {word_count(old.content)} vs {word_count(twin.content)} words)")
                continue
            if not old.children and word_count(old.content) == 0:
                report.skipped.append(f"{old.path} (empty page)")
                continue
            # No counterpart: place it beside the nearest ancestor that has one.
            mapped = exact_map(rel, MERGE_MAP) or (map_prefix(rel, MERGE_MAP) if rel.split("/")[0] in dict(MERGE_MAP) else None)
            ancestor, remainder = old.parent, [old.alias]
            base_dir = None
            while ancestor is not None and ancestor is not merge_root:
                anc_twin = twin_for(ancestor.rel_path)
                if anc_twin is not None:
                    base_dir = target_dir_of(anc_twin)
                    break
                remainder.insert(0, ancestor.alias)
                ancestor = ancestor.parent
            if base_dir is not None:
                target = base_dir + "/" + "/".join(remainder)
            elif mapped is not None:
                target = mapped.rstrip("/")
            else:
                mapped_default = map_prefix(rel, CMS_MAP)
                if mapped_default is None:
                    report.merged_skipped.append(f"{rel} (no home in the 2.4 books)")
                    continue
                target = mapped_default.rstrip("/").removesuffix(".md")
            target = target + ("/README.md" if old.children else ".md")
            merge_new.append((old, target))
            link_map[old.path] = target
        for old, target in merge_new:
            old.target = target

    if args.clean and not args.dry_run:
        removed = 0
        for path in DOCS.rglob("*.md"):
            rel = path.relative_to(DOCS)
            if rel.parts[0] in ("_import", "plan"):
                continue
            if path.exists() and importer_owns(path) and parse_meta(path.read_text(encoding="utf-8"))[0]:
                path.unlink()
                removed += 1
        for directory in sorted((p for p in DOCS.rglob("*") if p.is_dir()), key=lambda p: -len(p.parts)):
            if directory.name not in ("_import", "plan") and "_import" not in directory.parts and not any(directory.iterdir()):
                directory.rmdir()
        print(f"removed {removed} importer-owned pages")

    anchors_wanted = {frag: True for row in rows for frag in re.findall(r'href="[^"]*#([^"]+)"', row.get("content") or "")}
    converter = Converter(media, link_map, report, anchors_wanted)
    # Every id or name in a page's content, plus the anchors the builder will make from its headings.
    for root in trees:
        for node in root.walk():
            if not node.target:
                continue
            ids = set(re.findall(r'\b(?:id|name)="([^"]+)"', node.content))
            for heading in re.findall(r"<h[1-6][^>]*>(.*?)</h[1-6]>", node.content, re.S):
                ids.add(slugify_heading(heading))
            converter.ids_by_target.setdefault(node.target, set()).update(ids)
    redirects: dict[str, str] = {}
    written: list[str] = []

    def write_page(node: Node, extra_meta: dict[str, str] | None = None, appended_html: str | None = None,
                   version_alias: str = "240") -> None:
        target = node.target
        assert target
        path = DOCS / target
        if not node.children and word_count(node.content) == 0:
            report.skipped.append(f"{node.path} (empty page)")
            if node.parent is not None and node.parent.target:
                for alias_form in (node.path, node.path.replace("240/", "current/", 1)):
                    redirects[f"documentation/{alias_form}"] = output_href(node.parent.target)
            return
        if int(node.row.get("state") or 0) != 1:
            extra_meta = dict(extra_meta or {})
            extra_meta.setdefault("source-state", "unpublished")
            report.counters["unpublished_sources"] += 1
        stem = slug_for(PurePosixPath(target).name if PurePosixPath(target).name != "README.md" else PurePosixPath(target).parent.name)
        content_html = node.content
        if node.children:
            for child in node.children:
                if child.collapsed:
                    content_html += "\n" + child.content
        body = converter.convert(content_html, target, stem, node.path.split("/")[0])
        if appended_html:
            extra = converter.convert(appended_html, target, stem, args.merge_from or "220")
            body += (
                "\n## Material from the 2.2 documentation\n\n"
                "> **Note:** The text below comes from the older 2.2 page of the same name, where it differed "
                "substantially from the 2.4 page above. Reconcile the two when reviewing.\n\n" + extra
            )
        text = header_for(node, version_alias, extra_meta) + f"# {node.title}\n\n" + body
        report.pages += 1
        report.words += len(body.split())
        for alias_form in (node.path, node.path.replace("240/", "current/", 1)):
            redirects[f"documentation/{alias_form}"] = output_href(target)
        if not importer_owns(path):
            report.protected.append(target)
            return
        if args.dry_run:
            return
        path.parent.mkdir(parents=True, exist_ok=True)
        path.write_text(text, encoding="utf-8")
        written.append(target)

    for root in trees:
        for node in root.walk():
            if node is root and node.target is None:
                continue
            if node.target is None:
                if node.skipped and node.parent is not None and node.parent.target:
                    # dropped on purpose: send its old URL to the section it lived in
                    for alias_form in (node.path, node.path.replace("240/", "current/", 1)):
                        redirects[f"documentation/{alias_form}"] = output_href(node.parent.target)
                if not node.collapsed and not node.consolidated_into:
                    report.skipped.append(node.path)
                continue
            extra = {}
            appended = merged_extras.get(node.id)
            if appended:
                extra = {"status": "merged", "merged-from": "2.2"}
            write_page(node, extra, appended)

    for name, (title, intro, rels) in CONSOLIDATE.items():
        target = CONSOLIDATED_TARGETS.get(name)
        if not target:
            continue
        members = [n for root in trees for n in root.walk() if n.consolidated_into == target]
        members.sort(key=lambda n: rels.index(n.rel_path) if n.rel_path in rels else 999)
        if not members:
            continue
        stem = slug_for(PurePosixPath(target).name)
        parts = [f"# {title}", "", intro, ""]
        modified = ""
        for member in members:
            body = converter.convert(member.content, target, stem, member.path.split("/")[0])
            body = re.sub(r"^(#{2,5}) ", r"#\1 ", body, flags=re.M)
            parts += [f"## {member.title}", "", body.strip(), ""]
            modified = max(modified, (member.row.get("modified") or "")[:10])
            for alias_form in (member.path, member.path.replace("240/", "current/", 1)):
                redirects[f"documentation/{alias_form}"] = output_href(target)
        header = "<!--\n" + "\n".join([
            "status: imported",
            "source: " + ", ".join(f"{SITE}/documentation/{m.path}" for m in members),
            f"modified: {modified}" if re.match(r"^[12]\d{3}-", modified) else "",
            f"imported: {TODAY}",
        ]).replace("\n\n", "\n") + "\n-->\n"
        path = DOCS / target
        report.pages += 1
        if importer_owns(path) and not args.dry_run:
            path.parent.mkdir(parents=True, exist_ok=True)
            path.write_text(header + "\n".join(parts).rstrip() + "\n", encoding="utf-8")
        report.counters["consolidated_pages"] += len(members)

    for old, target in merge_new:
        write_page(old, {"status": "merged", "merged-from": "2.2"}, None, version_alias=args.merge_from or "")
        report.merged_new.append(f"{old.rel_path} -> {target}")

    media.save_index()

    if not args.dry_run:
        referenced: set[str] = set()
        for md in DOCS.rglob("*.md"):
            if "_import" in md.parts:
                continue
            referenced.update(re.findall(r"media/([^)\s\"']+)", md.read_text(encoding="utf-8")))
        pruned = 0
        for media_dir in DOCS.glob("*/media"):
            for file in media_dir.iterdir():
                if file.is_file() and file.name not in referenced:
                    file.unlink()
                    pruned += 1
            if not any(media_dir.iterdir()):
                media_dir.rmdir()
        if pruned:
            print(f"pruned {pruned} unreferenced media files")

    if not args.dry_run:
        redirects_path = ROOT / "gh-pages" / "redirects.json"
        redirects_path.write_text(json.dumps(dict(sorted(redirects.items())), indent=1) + "\n", encoding="utf-8")

    # Report.
    lines = [
        "# Import report", "",
        f"Run on {TODAY}. {report.pages} pages, about {report.words:,} words, {report.images} images placed.", "",
        "## Counters", "",
    ]
    lines += [f"- {k}: {v}" for k, v in sorted(report.counters.items())]
    for title, items in (
        ("Pages left alone because they are no longer importer-owned", report.protected),
        ("Old pages with no target (skipped)", report.skipped),
        ("Published pages whose parent is unpublished (not imported)", report.orphans),
        ("Tables kept as HTML (too complex for Markdown)", sorted(set(report.tables_html))),
        ("Images that could not be fetched", report.media_failed),
        ("Documentation links that could not be resolved", report.unresolved_links),
        ("Links whose #anchor no longer exists (fragment dropped)", report.dropped_fragments),
        ("2.2 pages imported because 2.4 has no equivalent", report.merged_new),
        ("2.2 pages appended to their 2.4 twin", report.merged_appended),
        ("2.2 pages judged equivalent to 2.4 (ignored)", report.merged_same),
        ("2.2 pages left behind on purpose", report.merged_skipped),
    ):
        lines += ["", f"## {title} ({len(items)})", ""]
        lines += [f"- {item}" for item in items] or ["- none"]
    report_text = "\n".join(lines) + "\n"
    if not args.dry_run:
        IMPORT_DIR.mkdir(parents=True, exist_ok=True)
        (IMPORT_DIR / "import-report.md").write_text(report_text, encoding="utf-8")
    print(f"{report.pages} pages ({report.words:,} words), {report.images} images, "
          f"{len(report.media_failed)} image failures, {len(report.unresolved_links)} unresolved links, "
          f"{len(report.protected)} protected pages left alone")
    if not args.dry_run:
        print(f"report: {(IMPORT_DIR / 'import-report.md').relative_to(ROOT)}")
    return 0


if __name__ == "__main__":
    raise SystemExit(main())
