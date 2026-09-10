#!/usr/bin/env python3
"""Build the Hubzero documentation site from docs/ into gh-pages/public/.

The documentation is a set of books. Each book is a directory under docs/
listed in gh-pages/site.json; inside a book, every subdirectory is a section
whose landing page is its README.md, and every other *.md file is a chapter.
Chapters and sections are ordered by an optional numeric filename prefix
(``01-``), then by title. The prefix is stripped from the published URL.

Every page may start with a metadata comment::

    <!--
    status: imported
    source: https://help.hubzero.org/documentation/240/managers/components/kb
    modified: 2016-03-01
    -->

``status`` drives the banner readers see (imported pages carry a warning,
reviewed pages carry a stamp); the other keys are informational and feed the
status report at /status/. See docs/STYLE.md for the full list.

Run: python3 gh-pages/build_site.py [--output DIR]
"""

from __future__ import annotations

import argparse
import datetime
import hashlib
import json
import os
import re
import shutil
import sys
import unicodedata
from collections.abc import Callable
from dataclasses import dataclass, field
from html import escape
from pathlib import Path, PurePosixPath

try:
    from markdown_it import MarkdownIt
except ImportError as exc:  # pragma: no cover - exercised by humans
    raise SystemExit(
        "markdown-it-py is required to build the docs site.\n"
        "Install it with: python3 -m pip install -r gh-pages/requirements.txt"
    ) from exc


ROOT = Path(__file__).resolve().parent.parent
SOURCE_DIR = ROOT / "gh-pages"
DOCS_DIR = ROOT / "docs"
OUTPUT_DIR = SOURCE_DIR / "public"
ASSET_SOURCE_PREFIX = "gh-pages/assets/"
DOCS_PREFIX = "docs/"
HEADER_LOGO = "assets/hubzero-mark.svg"
SKIP_DOC_DIRS = {"_import", "plan"}
COPIED_SUFFIXES = {".png", ".jpg", ".jpeg", ".gif", ".svg", ".webp", ".pdf", ".zip", ".gz", ".csv"}
LANDING_NAMES = ("readme.md", "index.md")
ORDER_PREFIX_RE = re.compile(r"^(\d+)[-_.]\s*")
META_COMMENT_RE = re.compile(r"^\s*<!--(.*?)-->", re.S)
ADMONITION_KINDS = ("note", "tip", "warning", "important", "caution")
STATUS_LABELS = {
    "imported": "Imported, not yet reviewed",
    "merged": "Merged from an older version, not yet reviewed",
    "draft": "Draft",
    "reviewed": "Reviewed",
    "rewritten": "Rewritten",
    "generated": "Generated from source",
}
INCLUDE_LANGS = {
    ".php": "php",
    ".py": "python",
    ".sh": "bash",
    ".json": "json",
    ".xml": "xml",
    ".ini": "ini",
    ".yml": "yaml",
    ".yaml": "yaml",
    ".md": "markdown",
    ".js": "javascript",
    ".css": "css",
    ".less": "less",
    ".html": "html",
    ".sql": "sql",
    ".conf": "apache",
}


# --------------------------------------------------------------------------- helpers


def slugify(text: str) -> str:
    normalized = unicodedata.normalize("NFKD", text)
    ascii_text = normalized.encode("ascii", "ignore").decode("ascii")
    slug = re.sub(r"[^a-zA-Z0-9]+", "-", ascii_text.lower()).strip("-")
    return slug or "section"


def read_json(path: Path) -> dict:
    with path.open("r", encoding="utf-8") as handle:
        return json.load(handle)


def render_template(path: Path, context: dict[str, str]) -> str:
    text = path.read_text(encoding="utf-8")
    for key, value in context.items():
        text = text.replace(f"{{{{ {key} }}}}", value)
    return text


def relative_href(from_file: Path, to_file: Path) -> str:
    return os.path.relpath(to_file, from_file.parent).replace(os.sep, "/")


def ensure_clean_dir(path: Path) -> None:
    if path.exists():
        shutil.rmtree(path)
    path.mkdir(parents=True, exist_ok=True)


def write_text(path: Path, content: str) -> None:
    path.parent.mkdir(parents=True, exist_ok=True)
    path.write_text(content, encoding="utf-8")


def parse_meta(text: str) -> tuple[dict[str, str], str]:
    """Split a leading ``<!-- key: value -->`` comment off a Markdown file.

    Returns the metadata and the remaining Markdown. A leading comment that
    does not look like ``key: value`` lines is left in place untouched.
    """
    match = META_COMMENT_RE.match(text)
    if not match:
        return {}, text
    meta: dict[str, str] = {}
    for line in match.group(1).strip().splitlines():
        line = line.strip().rstrip(";")
        if not line:
            continue
        key, sep, value = line.partition(":")
        if not sep or not re.fullmatch(r"[a-z][a-z0-9-]*", key.strip()):
            return {}, text
        meta[key.strip()] = value.strip()
    return meta, text[match.end():]


def order_key_for(name: str, meta: dict[str, str]) -> tuple[int, str]:
    """Sort key for a chapter or section: explicit order, filename prefix, then name."""
    if meta.get("order", "").isdigit():
        return int(meta["order"]), name.lower()
    match = ORDER_PREFIX_RE.match(name)
    if match:
        return int(match.group(1)), name.lower()
    return 10_000, name.lower()


def slug_for(name: str) -> str:
    """URL segment for a file or directory name: numeric prefix dropped, then slugified."""
    stem = name[:-3] if name.lower().endswith(".md") else name
    stem = ORDER_PREFIX_RE.sub("", stem)
    return slugify(stem)


def title_from_name(name: str) -> str:
    stem = ORDER_PREFIX_RE.sub("", name[:-3] if name.lower().endswith(".md") else name)
    return stem.replace("-", " ").replace("_", " ").strip().capitalize()


def first_heading(text: str) -> str | None:
    for line in text.splitlines():
        if line.startswith("# "):
            return line[2:].strip()
    return None


def expand_includes(text: str, warn: Callable[[str], None]) -> str:
    """Expand ``<!--include: path[:start-end]-->`` into a fenced code block.

    The path is repository-relative. Real source files are shown rather than
    pasted copies, so examples cannot drift from the code they describe.
    """

    def replace(match: re.Match[str]) -> str:
        spec = match.group(1).strip()
        rel, _, span = spec.partition(":")
        path = ROOT / rel
        try:
            lines = path.read_text(encoding="utf-8").splitlines()
        except OSError:
            warn(f"include of missing file {rel!r}")
            return match.group(0)
        if span:
            start, _, end = span.partition("-")
            lines = lines[max(int(start) - 1, 0): int(end or len(lines))]
        body = "\n".join(lines).rstrip("\n")
        lang = INCLUDE_LANGS.get(path.suffix, "")
        return f"```{lang}\n{body}\n```"

    return re.sub(r"<!--\s*include:\s*([^>]+?)\s*-->", replace, text)


def excerpt_of(html: str, limit: int = 200) -> str:
    text = re.sub(r"<(pre|code)[^>]*>.*?</\1>", " ", html, flags=re.S)
    text = re.sub(r"<[^>]+>", " ", text)
    text = re.sub(r"&[a-z]+;|&#\d+;", " ", text)
    text = re.sub(r"\s+", " ", text).strip()
    if len(text) <= limit:
        return text
    cut = text[:limit].rsplit(" ", 1)[0]
    return cut + "…"


# --------------------------------------------------------------------------- links

_HTML_HREF_RE = re.compile(
    r"""(?P<attr>\b(?:href|src|srcset)\s*=\s*)(?P<q>["'])(?P<url>[^"']*)(?P=q)"""
)


def _resolve_attr_value(attr: str, value: str, resolve: Callable[[str], str]) -> str:
    """Resolve one HTML attribute value; srcset is a comma-separated candidate list."""
    if "srcset" not in attr.lower():
        return resolve(value)
    candidates = []
    for candidate in value.split(","):
        stripped = candidate.strip()
        if not stripped:
            continue
        url, _, descriptor = stripped.partition(" ")
        candidates.append(f"{resolve(url)} {descriptor.strip()}".strip())
    return ", ".join(candidates)


def rewrite_links(tokens: list, resolve: Callable[[str], str]) -> None:
    """Hand every href/src in the token stream to ``resolve``.

    Markdown links and images arrive as tokens; raw HTML passes through
    markdown-it opaquely, so its attributes are rewritten with a regex.
    """
    for token in tokens:
        if token.type == "inline" and token.children:
            rewrite_links(token.children, resolve)
            continue
        if token.type in ("html_block", "html_inline"):
            token.content = _HTML_HREF_RE.sub(
                lambda m: f"{m.group('attr')}{m.group('q')}"
                f"{_resolve_attr_value(m.group('attr'), m.group('url'), resolve)}"
                f"{m.group('q')}",
                token.content,
            )
            continue
        if token.type == "link_open":
            href = token.attrGet("href")
            if href:
                token.attrSet("href", resolve(href))
        elif token.type == "image":
            src = token.attrGet("src")
            if src:
                token.attrSet("src", resolve(src))


def make_link_resolver(
    source: str,
    output: str,
    source_to_output: dict[str, str],
    blob_base: str,
    warn: Callable[[str], None],
    tree_base: str | None = None,
) -> Callable[[str], str]:
    """Rewrite a repository-relative link so it works in the built site.

    * a link to a Markdown file that is a page -> that page, relative
    * a link to a directory whose README.md is a page -> that page
    * a link to any other file under docs/ (images, downloads) -> the copy of
      that file in the built site, relative
    * a link to a file under gh-pages/assets/ -> assets/, relative
    * a link to any other repository file -> its GitHub blob URL
    * a link to a repository directory -> its GitHub tree URL
    * absolute URLs, bare fragments, and already-built paths are untouched
    """
    source_dir = PurePosixPath(source).parent
    tree_base = tree_base or blob_base.replace("/blob/", "/tree/")

    def prefix_insensitive(candidate: str) -> str | None:
        """Match `users/collections.md` to `users/01-collections.md` (and the same for directories).

        Numeric prefixes only set the order and are dropped from URLs, so a link should not
        have to know them. Every path segment may carry one."""
        want = [slug_for(part) if part.lower() != "readme.md" else "readme.md" for part in PurePosixPath(candidate).parts]
        for known in source_to_output:
            parts = PurePosixPath(known).parts
            if len(parts) != len(want):
                continue
            if all((slug_for(p) if p.lower() != "readme.md" else "readme.md") == w for p, w in zip(parts, want)):
                return known
        return None

    def resolve(href: str) -> str:
        if not href or href.startswith(("#", "//")):
            return href
        if re.match(r"^[a-zA-Z][a-zA-Z0-9+.-]*:", href):  # http:, mailto:, data:
            return href
        path_part, sep, fragment = href.partition("#")
        if not path_part:
            return href

        target = os.path.normpath(str(source_dir / path_part)).replace(os.sep, "/")
        if target.startswith(".."):
            return href

        candidates = [target]
        if not target.lower().endswith(".md"):
            candidates += [f"{target.rstrip('/')}/README.md", f"{target.rstrip('/')}/index.md"]
        for candidate in candidates:
            found = candidate if candidate in source_to_output else prefix_insensitive(candidate)
            if found is not None:
                rebased = relative_href(
                    Path("/site") / output, Path("/site") / source_to_output[found]
                )
                return rebased + sep + fragment

        if target.startswith(ASSET_SOURCE_PREFIX):
            rebased = relative_href(
                Path("/site") / output,
                Path("/site") / "assets" / target[len(ASSET_SOURCE_PREFIX):],
            )
            return rebased + sep + fragment

        if target.startswith(DOCS_PREFIX) and (ROOT / target).is_file():
            rebased = relative_href(
                Path("/site") / output, Path("/site") / target[len(DOCS_PREFIX):]
            )
            return rebased + sep + fragment

        if (ROOT / target).is_file():
            return f"{blob_base}/{target}" + sep + fragment

        # A directory in the repository: GitHub shows it under /tree/, not
        # /blob/. Left relative it would point into the built site, where the
        # source tree does not exist.
        if (ROOT / target).is_dir():
            return f"{tree_base}/{target}" + sep + fragment

        if path_part.endswith(".md") or "/" not in path_part:
            warn(f"{source}: link to missing file {path_part!r}")
        return href

    return resolve


# --------------------------------------------------------------------------- markdown


def _apply_admonitions(tokens: list) -> None:
    """Turn ``> **Note:** ...`` blockquotes into ``<aside class="admonition">``.

    The label stays in the content as its first bold run; the stylesheet
    picks the colour from the class. Anything that is not one of the known
    labels stays an ordinary blockquote.
    """
    for index, token in enumerate(tokens):
        if token.type != "blockquote_open":
            continue
        kind = None
        for probe in tokens[index + 1: index + 4]:
            if probe.type == "inline" and probe.children:
                children = [c for c in probe.children if not (c.type == "text" and not c.content.strip())]
                if len(children) > 1 and children[0].type == "strong_open":
                    label = children[1].content.strip().rstrip(":").lower()
                    if label in ADMONITION_KINDS:
                        kind = label
                break
        if not kind:
            continue
        token.tag = "aside"
        token.attrSet("class", f"admonition admonition--{kind}")
        depth = 1
        for closer in tokens[index + 1:]:
            if closer.type == "blockquote_open":
                depth += 1
            elif closer.type == "blockquote_close":
                depth -= 1
                if depth == 0:
                    closer.tag = "aside"
                    break


class MarkdownRenderer:
    def __init__(self) -> None:
        self.md = MarkdownIt("commonmark", {"html": True, "typographer": True})
        self.md.enable("table")
        self.md.enable("strikethrough")

    def render(
        self, text: str, link_resolver: Callable[[str], str] | None = None
    ) -> dict[str, object]:
        tokens = self.md.parse(text)
        if link_resolver is not None:
            rewrite_links(tokens, link_resolver)
        _apply_admonitions(tokens)

        slug_counts: dict[str, int] = {}
        toc: list[dict[str, object]] = []
        headings: list[str] = []
        title = None
        first_h1_index = None

        for index, token in enumerate(tokens):
            if token.type != "heading_open" or index + 1 >= len(tokens):
                continue
            inline = tokens[index + 1]
            if inline.type != "inline":
                continue
            heading_text = re.sub(r"<[^>]+>", "", inline.content).strip()
            heading_text = heading_text.replace("`", "")
            if not heading_text:
                continue
            level = int(token.tag[1])
            base_slug = slugify(heading_text)
            count = slug_counts.get(base_slug, 0)
            slug_counts[base_slug] = count + 1
            anchor = base_slug if count == 0 else f"{base_slug}-{count + 1}"
            token.attrSet("id", anchor)
            if level == 1 and title is None:
                title = heading_text
                first_h1_index = index
            else:
                headings.append(heading_text)
                if level in (2, 3):
                    toc.append({"level": level, "anchor": anchor, "text": heading_text})

        if first_h1_index is not None:
            del tokens[first_h1_index: first_h1_index + 3]

        html = self.md.renderer.render(tokens, self.md.options, {})
        return {"title": title, "toc": toc, "html": html, "headings": headings}


# --------------------------------------------------------------------------- model


@dataclass
class Book:
    slug: str
    title: str
    dir: str
    summary: str
    audience: str
    featured: bool
    root: "Page | None" = None

    @property
    def href(self) -> str:
        return f"{self.slug}/index.html"


@dataclass
class Page:
    source: str | None
    output: str
    title: str
    book: Book | None = None
    parent: "Page | None" = None
    children: list["Page"] = field(default_factory=list)
    meta: dict[str, str] = field(default_factory=dict)
    order: tuple[int, str] = (0, "")
    template: str = "doc"
    summary: str = ""
    is_section: bool = False
    synthetic: bool = False
    text: str = ""

    @property
    def href(self) -> str:
        return self.output.replace(os.sep, "/")

    @property
    def status(self) -> str:
        return self.meta.get("status", "")

    def ancestors(self) -> list["Page"]:
        chain: list[Page] = []
        node = self.parent
        while node is not None:
            chain.append(node)
            node = node.parent
        chain.reverse()
        return chain

    def walk(self) -> list["Page"]:
        pages = [self]
        for child in self.children:
            pages.extend(child.walk())
        return pages


def read_page_source(path: Path) -> tuple[dict[str, str], str, str]:
    """Return (meta, markdown body, title) for a Markdown file."""
    raw = path.read_text(encoding="utf-8")
    meta, body = parse_meta(raw)
    title = meta.get("title") or first_heading(body) or title_from_name(path.name)
    return meta, body, title


def load_section(
    directory: Path, url_parts: list[str], book: Book, parent: Page | None
) -> Page:
    landing = next((directory / n for n in ("README.md", "index.md") if (directory / n).exists()), None)
    output = str(Path(*url_parts) / "index.html") if url_parts else "index.html"
    if landing is not None:
        meta, body, title = read_page_source(landing)
        section = Page(
            source=str(landing.relative_to(ROOT)).replace(os.sep, "/"),
            output=output, title=title, book=book, parent=parent, meta=meta,
            text=body, is_section=True,
        )
    else:
        section = Page(
            source=None, output=output, title=title_from_name(directory.name),
            book=book, parent=parent, is_section=True, synthetic=True,
        )
    section.order = order_key_for(directory.name, section.meta)

    entries: list[Page] = []
    for child in sorted(directory.iterdir()):
        if child.name.startswith((".", "_")) or child.name == "media":
            continue
        if child.is_dir():
            if any(p.suffix.lower() == ".md" for p in child.rglob("*.md")):
                entries.append(load_section(child, url_parts + [slug_for(child.name)], book, section))
        elif child.suffix.lower() == ".md" and child.name.lower() not in LANDING_NAMES:
            meta, body, title = read_page_source(child)
            leaf = Page(
                source=str(child.relative_to(ROOT)).replace(os.sep, "/"),
                output=str(Path(*url_parts, slug_for(child.name)) / "index.html"),
                title=title, book=book, parent=section, meta=meta, text=body,
            )
            leaf.order = order_key_for(child.name, meta)
            entries.append(leaf)
    entries.sort(key=lambda p: p.order)
    section.children = entries
    return section


# --------------------------------------------------------------------------- html fragments


def build_toc(entries: list[dict[str, object]]) -> str:
    if not entries:
        return ""
    items = []
    for entry in entries:
        level = int(entry["level"])
        items.append(
            f'<li class="toc__item toc__item--l{level}">'
            f'<a href="#{escape(str(entry["anchor"]))}">{escape(str(entry["text"]))}</a></li>'
        )
    return '<ol class="toc">\n' + "\n".join(items) + "\n</ol>"


def build_tree(node: Page, current: Page, output_path: Path, output_dir: Path, toc_html: str) -> str:
    """Nested book navigation. Sections are <details>, open along the active path."""
    active_path = set(id(p) for p in current.ancestors()) | {id(current)}
    items = []
    for child in node.children:
        href = relative_href(output_path, output_dir / child.output)
        is_current = child is current
        on_path = id(child) in active_path
        cls = "tree__link" + (" is-active" if is_current else "")
        link = f'<a class="{cls}" href="{escape(href)}"' + (' aria-current="page"' if is_current else "") + f'>{escape(child.title)}</a>'
        sub = toc_html if (is_current and toc_html) else ""
        if child.children:
            inner = build_tree(child, current, output_path, output_dir, toc_html)
            items.append(
                f'<li class="tree__section{" is-open" if on_path else ""}">'
                f'<details{" open" if on_path else ""}><summary>{link}</summary>{sub}{inner}</details></li>'
            )
        else:
            items.append(f'<li class="tree__leaf">{link}{sub}</li>')
    return '<ul class="tree">\n' + "\n".join(items) + "\n</ul>"


def build_breadcrumbs(page: Page, output_path: Path, output_dir: Path, home_href: str) -> str:
    parts = [f'<a href="{escape(home_href)}">Home</a>']
    for ancestor in page.ancestors():
        href = relative_href(output_path, output_dir / ancestor.output)
        parts.append(f'<a href="{escape(href)}">{escape(ancestor.title)}</a>')
    parts.append(f'<span aria-current="page">{escape(page.title)}</span>')
    sep = '<span class="crumbs__sep" aria-hidden="true">/</span>'
    return sep.join(parts)


def build_status_banner(page: Page, config: dict) -> str:
    status = page.status
    if status in ("imported", "merged"):
        source = page.meta.get("source", "")
        modified = page.meta.get("modified", "")
        origin = f'<a href="{escape(source)}">help.hubzero.org</a>' if source else "help.hubzero.org"
        when = f" It was last edited there in {escape(modified[:4])}." if modified[:4].isdigit() else ""
        if page.meta.get("source-state") == "unpublished":
            when += " It was never published there, so read it as a draft."
        merged = page.meta.get("merged-from", "")
        extra = f" It exists only in the {escape(merged)} documentation and was merged in from there." if merged else ""
        return (
            f'<aside class="banner banner--{status}" role="note">'
            f"<strong>Not yet reviewed.</strong> This page was imported from {origin} and "
            f"has not been checked against Hubzero {escape(config.get('version', ''))}.{when}{extra}"
            "</aside>"
        )
    if status == "draft":
        return '<aside class="banner banner--draft" role="note"><strong>Draft.</strong> This page is still being written.</aside>'
    return ""


def build_review_stamp(page: Page) -> str:
    status = page.status
    if status not in ("reviewed", "rewritten", "generated"):
        return ""
    against = page.meta.get("reviewed-against", "")
    when = page.meta.get("reviewed", "")
    if status == "generated":
        return f'<p class="stamp">Generated from the source tree{(" at " + escape(against)) if against else ""}.</p>'
    detail = ""
    if against:
        detail += f" against <code>{escape(against)}</code>"
    if when:
        detail += f" on {escape(when)}"
    label = "Reviewed" if status == "reviewed" else "Rewritten and checked"
    return f'<p class="stamp">{label}{detail}.</p>'


def build_pager(page: Page, ordered: list[Page], output_path: Path, output_dir: Path) -> str:
    if page not in ordered:
        return ""
    index = ordered.index(page)
    prev_page = ordered[index - 1] if index > 0 else None
    next_page = ordered[index + 1] if index + 1 < len(ordered) else None
    parts = []
    if prev_page:
        href = relative_href(output_path, output_dir / prev_page.output)
        parts.append(f'<a class="pager__prev" href="{escape(href)}" rel="prev"><span>Previous</span>{escape(prev_page.title)}</a>')
    else:
        parts.append("<span></span>")
    if next_page:
        href = relative_href(output_path, output_dir / next_page.output)
        parts.append(f'<a class="pager__next" href="{escape(href)}" rel="next"><span>Next</span>{escape(next_page.title)}</a>')
    return '<nav class="pager" aria-label="Chapter navigation">' + "".join(parts) + "</nav>"


def build_children_list(page: Page, output_path: Path, output_dir: Path,
                        content_html: str = "") -> str:
    """The automatic contents list for a section.

    A landing page that writes its own contents list, with its own wording,
    wins: emitting both leaves the page saying "In this section" twice.
    """
    if not page.children:
        return ""
    if 'id="in-this-section"' in content_html:
        return ""
    items = []
    for child in page.children:
        href = relative_href(output_path, output_dir / child.output)
        summary = f'<p>{escape(child.summary)}</p>' if child.summary else ""
        items.append(f'<li><a href="{escape(href)}">{escape(child.title)}</a>{summary}</li>')
    return '<section class="contents"><h2 id="in-this-section">In this section</h2><ul class="contents__list">' + "\n".join(items) + "</ul></section>"


def build_book_cards(books: list[Book], config: dict) -> str:
    groups: dict[str, list[Book]] = {}
    for book in books:
        groups.setdefault(book.audience, []).append(book)
    blocks = []
    for audience, group in groups.items():
        cards = []
        for book in group:
            count = len(book.root.walk()) if book.root else 0
            cards.append(
                '<article class="book-card">'
                f'<h3><a href="{escape(book.href)}">{escape(book.title)}</a></h3>'
                f"<p>{escape(book.summary)}</p>"
                f'<p class="book-card__meta">{count} page{"s" if count != 1 else ""}</p>'
                "</article>"
            )
        blocks.append(
            f'<section class="book-group"><h2 class="book-group__title">{escape(audience)}</h2>'
            f'<div class="book-grid">{"".join(cards)}</div></section>'
        )
    return "\n".join(blocks)


# --------------------------------------------------------------------------- status report


def build_status_report(books: list[Book], config: dict) -> tuple[str, dict]:
    """A Markdown status report of every page, grouped by book, plus its JSON twin."""
    lines = ["# Documentation status", "",
             f"Review progress for the Hubzero {config.get('version', '')} documentation. "
             "A page is *reviewed* once someone has checked it against the code on "
             f"`{config.get('blob_ref', 'main')}`; everything else still carries its import banner.", ""]
    data: dict = {"books": [], "totals": {}}
    totals: dict[str, int] = {}
    for book in books:
        pages = [p for p in book.root.walk() if not p.synthetic] if book.root else []
        counts: dict[str, int] = {}
        for page in pages:
            key = page.status or "unlabelled"
            counts[key] = counts.get(key, 0) + 1
            totals[key] = totals.get(key, 0) + 1
        reviewed = sum(counts.get(k, 0) for k in ("reviewed", "rewritten", "generated"))
        lines.append(f"## {book.title}")
        lines.append("")
        lines.append(f"{reviewed} of {len(pages)} pages reviewed. "
                     + ", ".join(f"{v} {k}" for k, v in sorted(counts.items())) + ".")
        lines.append("")
        lines.append("| Page | Status | Source last edited | Reviewed against |")
        lines.append("|---|---|---|---|")
        for page in pages:
            rel = "/".join(p.title for p in page.ancestors()[1:] + [page])
            link = f"[{rel}](../{page.href})"
            lines.append(f"| {link} | {page.status or '—'} | {page.meta.get('modified', '')[:10] or '—'} | {page.meta.get('reviewed-against', '') or '—'} |")
        lines.append("")
        data["books"].append({"slug": book.slug, "title": book.title, "pages": len(pages), "counts": counts})
    data["totals"] = totals
    return "\n".join(lines), data


# --------------------------------------------------------------------------- main


def main() -> int:
    parser = argparse.ArgumentParser(description="Build the Hubzero documentation site.")
    parser.add_argument("--output", default=str(OUTPUT_DIR), help="Build output directory")
    parser.add_argument("--strict", action="store_true", help="Fail on link warnings")
    args = parser.parse_args()

    output_dir = Path(args.output).resolve()
    config = read_json(SOURCE_DIR / "site.json")
    renderer = MarkdownRenderer()
    warnings: list[str] = []
    warn = warnings.append

    ensure_clean_dir(output_dir)
    shutil.copytree(SOURCE_DIR / "assets", output_dir / "assets", dirs_exist_ok=True)
    write_text(output_dir / ".nojekyll", "")
    css_ver = hashlib.sha1((SOURCE_DIR / "assets" / "site.css").read_bytes()).hexdigest()[:8]

    # Copy every non-Markdown file under docs/ (images, downloads) to the same relative path.
    for path in DOCS_DIR.rglob("*"):
        rel = path.relative_to(DOCS_DIR)
        if path.is_dir() or rel.parts[0] in SKIP_DOC_DIRS or rel.parts[0].startswith("."):
            continue
        if path.suffix.lower() not in COPIED_SUFFIXES:
            continue
        destination = output_dir / rel
        destination.parent.mkdir(parents=True, exist_ok=True)
        shutil.copy2(path, destination)

    # Books and standalone pages.
    books: list[Book] = []
    for entry in config["books"]:
        book = Book(
            slug=entry["slug"], title=entry["title"], dir=entry["dir"],
            summary=entry.get("summary", ""), audience=entry.get("audience", "Documentation"),
            featured=bool(entry.get("featured", True)),
        )
        book_dir = ROOT / entry["dir"]
        if not book_dir.is_dir():
            warn(f"book directory missing: {entry['dir']}")
            continue
        book.root = load_section(book_dir, [book.slug], book, None)
        book.root.title = entry.get("title", book.root.title)
        books.append(book)

    standalone: list[Page] = []
    index_page: Page | None = None
    for entry in config.get("pages", []):
        path = ROOT / entry["source"]
        meta, body, title = read_page_source(path)
        page = Page(
            source=entry["source"], output=str(Path(entry["slug"]) / "index.html"),
            title=entry.get("title", title), meta=meta, text=body, template="page",
        )
        standalone.append(page)
        if entry.get("index"):
            index_page = page

    all_pages: list[Page] = [p for b in books for p in b.root.walk()] + standalone
    for page in all_pages:
        page.summary = page.meta.get("summary", "")

    source_to_output = {p.source: p.href for p in all_pages if p.source}
    outputs_seen: dict[str, str] = {}
    for page in all_pages:
        if page.href in outputs_seen:
            raise SystemExit(f"two pages build to {page.href}: {outputs_seen[page.href]} and {page.source}")
        outputs_seen[page.href] = page.source or "(synthetic)"

    github_href = config.get("github_href", "#")
    blob_ref = config.get("blob_ref", "main")
    blob_base = f"{github_href.rstrip('/')}/blob/{blob_ref}"
    edit_base = f"{github_href.rstrip('/')}/edit/{blob_ref}"
    year = str(datetime.date.today().year)
    version = config.get("version", "")
    site_url = config.get("site_url", "").rstrip("/")

    def canonical_tag(href: str) -> str:
        return f'<link rel="canonical" href="{escape(site_url)}/{escape(href)}">' if site_url else ""

    def common_context(output_path: Path) -> dict[str, str]:
        home_href = relative_href(output_path, output_dir / "index.html")
        return {
            "site_name": escape(config["site_name"]),
            "site_title": escape(config.get("site_title", config["site_name"])),
            "site_tagline": escape(config.get("site_tagline", "")),
            "site_description": escape(config.get("site_description", "")),
            "version": escape(version),
            "assets_href": escape(f"{relative_href(output_path, output_dir / 'assets' / 'site.css')}?v={css_ver}"),
            "search_js_href": escape(f"{relative_href(output_path, output_dir / 'assets' / 'search.js')}?v={css_ver}"),
            "search_index_href": escape(relative_href(output_path, output_dir / "search-index.json")),
            "logo_href": escape(relative_href(output_path, output_dir / HEADER_LOGO)),
            "home_href": escape(home_href),
            "root_href": escape(home_href[: -len("index.html")]),
            "github_href": escape(github_href),
            "docs_href": escape(relative_href(output_path, output_dir / index_page.output) if index_page else home_href),
            "status_href": escape(relative_href(output_path, output_dir / "status" / "index.html")),
            "license_href": escape(relative_href(output_path, output_dir / "license" / "index.html")),
            "year": year,
            "copyright": escape(config.get("copyright", config["site_name"])),
            "books_nav": "".join(
                f'<a href="{escape(relative_href(output_path, output_dir / b.href))}">{escape(b.title)}</a>' for b in books
            ),
        }

    # Render every page.
    search_index: list[dict[str, str]] = []
    for book in books:
        ordered = [p for p in book.root.walk()]
        for page in ordered:
            output_path = output_dir / page.output
            if page.synthetic:
                rendered = {"title": page.title, "toc": [], "html": "", "headings": []}
            else:
                resolver = make_link_resolver(page.source, page.href, source_to_output, blob_base, warn)
                rendered = renderer.render(expand_includes(page.text, warn), resolver)
            content_html = str(rendered["html"])
            if not page.summary:
                page.summary = excerpt_of(content_html, 160)
            toc_html = build_toc(rendered["toc"])  # type: ignore[arg-type]
            children_html = (build_children_list(page, output_path, output_dir, content_html)
                             if page.is_section else "")
            context = common_context(output_path)
            context.update({
                "page_title": escape(page.title),
                "page_summary": escape(page.summary),
                "book_title": escape(book.title),
                "book_href": escape(relative_href(output_path, output_dir / book.root.output)),
                "crumbs": build_breadcrumbs(page, output_path, output_dir, context["home_href"]),
                "tree": build_tree(book.root, page, output_path, output_dir, toc_html),
                "banner": build_status_banner(page, config),
                "stamp": build_review_stamp(page),
                "content": content_html + children_html,
                "pager": build_pager(page, ordered, output_path, output_dir),
                "edit_href": escape(f"{edit_base}/{page.source}") if page.source else "",
                "edit_link": (f'<a class="edit-link" href="{escape(edit_base + "/" + page.source)}">Edit this page on GitHub</a>' if page.source else ""),
                "canonical_tag": canonical_tag(page.href.replace("index.html", "")),
            })
            write_text(output_path, render_template(SOURCE_DIR / "templates" / "doc.html", context))
            search_index.append({
                "t": page.title,
                "u": page.href.replace("index.html", ""),
                "b": book.title,
                "s": " / ".join(a.title for a in page.ancestors()[1:]),
                "h": " | ".join(rendered["headings"]),  # type: ignore[arg-type]
                "x": excerpt_of(content_html, 220),
            })

    for page in standalone:
        output_path = output_dir / page.output
        resolver = make_link_resolver(page.source, page.href, source_to_output, blob_base, warn)
        rendered = renderer.render(expand_includes(page.text, warn), resolver)
        context = common_context(output_path)
        context.update({
            "page_title": escape(page.title),
            "page_summary": escape(page.summary or excerpt_of(str(rendered["html"]), 160)),
            "content": str(rendered["html"]),
            "toc": build_toc(rendered["toc"]),  # type: ignore[arg-type]
            "banner": build_status_banner(page, config),
            "stamp": build_review_stamp(page),
            "edit_link": f'<a class="edit-link" href="{escape(edit_base + "/" + page.source)}">Edit this page on GitHub</a>',
            "canonical_tag": canonical_tag(page.href.replace("index.html", "")),
        })
        write_text(output_path, render_template(SOURCE_DIR / "templates" / "page.html", context))

    # Status report.
    report_md, report_json = build_status_report(books, config)
    output_path = output_dir / "status" / "index.html"
    rendered = renderer.render(report_md)
    context = common_context(output_path)
    context.update({
        "page_title": "Documentation status", "page_summary": "Review progress, page by page.",
        "content": str(rendered["html"]), "toc": build_toc(rendered["toc"]),  # type: ignore[arg-type]
        "banner": "", "stamp": "", "edit_link": "", "canonical_tag": canonical_tag("status/"),
    })
    write_text(output_path, render_template(SOURCE_DIR / "templates" / "page.html", context))
    write_text(output_dir / "status.json", json.dumps(report_json, indent=1))

    # Home page.
    home_context = common_context(output_dir / "index.html")
    start_links = "".join(
        f'<a href="{escape(item["href"])}"><strong>{escape(item["title"])}</strong><span>{escape(item.get("text", ""))}</span></a>'
        for item in config.get("start", [])
    )
    home_context.update({
        "book_cards": build_book_cards(books, config),
        "start_links": start_links,
        "canonical_tag": canonical_tag(""),
        "page_count": str(sum(1 for p in all_pages if not p.synthetic)),
    })
    write_text(output_dir / "index.html", render_template(SOURCE_DIR / "templates" / "home.html", home_context))

    # Search index, redirects, sitemap.
    write_text(output_dir / "search-index.json", json.dumps(search_index, ensure_ascii=False, separators=(",", ":")))

    redirects_path = SOURCE_DIR / "redirects.json"
    redirect_lines = []
    if redirects_path.exists():
        for old, new in read_json(redirects_path).items():
            # A target may carry a fragment, so a page that was merged into a
            # section of another can still send its old URL to the right place.
            page_part, _, fragment = new.partition("#")
            if page_part not in outputs_seen:
                warn(f"redirect target missing: {old} -> {new}")
                continue
            sep = "#" if fragment else ""
            stub_path = output_dir / old.strip("/") / "index.html"
            target = relative_href(stub_path, output_dir / page_part) + sep + fragment
            pretty = f"{site_url}/{page_part.replace('index.html', '')}{sep}{fragment}"
            write_text(stub_path, render_template(SOURCE_DIR / "templates" / "redirect.html", {
                "target": escape(target),
                "canonical": escape(pretty) if site_url else escape(target),
            }))
            redirect_lines.append(f"/{old.strip('/')} {pretty}")
    write_text(output_dir / "redirects.txt", "\n".join(redirect_lines) + ("\n" if redirect_lines else ""))

    if site_url:
        urls = [f"{site_url}/"] + [f"{site_url}/{p.href.replace('index.html', '')}" for p in all_pages]
        sitemap = ['<?xml version="1.0" encoding="UTF-8"?>',
                   '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">']
        sitemap += [f"  <url><loc>{escape(u)}</loc></url>" for u in urls]
        sitemap.append("</urlset>")
        write_text(output_dir / "sitemap.xml", "\n".join(sitemap) + "\n")

    for warning in warnings:
        print(f"warning: {warning}", file=sys.stderr)
    page_total = sum(1 for p in all_pages if not p.synthetic)
    print(f"Built {page_total} pages in {len(books)} books into {output_dir}")
    if args.strict and warnings:
        return 1
    return 0


if __name__ == "__main__":
    raise SystemExit(main())
