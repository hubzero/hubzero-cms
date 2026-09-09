#!/usr/bin/env python3
"""Convert the in-tree com_help pages to Markdown for triage.

Every component may carry help pages under ``site/help/en-GB/*.phtml`` and
``admin/help/en-GB/*.phtml``; ``com_help`` renders them inside a hub. They
overlap with the Hub users and Hub managers books, so rather than publishing
them as a third set they are converted into ``docs/_import/com_help/`` (not
committed) with a report that says which book chapter each one belongs to.
Reviewers pull what is worth keeping into the real chapter during phase 3.

Usage:
    python3 tools/docs/import_com_help.py
"""

from __future__ import annotations

import re
import shutil
import sys
from pathlib import Path, PurePosixPath

ROOT = Path(__file__).resolve().parents[2]
sys.path.insert(0, str(ROOT / "tools" / "docs"))
sys.path.insert(0, str(ROOT / "gh-pages"))

from import_help import DOCS, IMPORT_DIR, TODAY, Converter, MediaStore, Report  # noqa: E402

OUT = IMPORT_DIR / "com_help"
PHP_BLOCK = re.compile(r"<\?php.*?\?>", re.S)
BASE_ECHO = re.compile(r"<\?php\s+echo\s+rtrim\(Request::base\(true\),\s*'/'\);\s*\?>")
HOST_ECHO = re.compile(r"<\?php\s+echo\s+\$_SERVER\['HTTP_HOST'\];\s*\?>")


class LocalConverter(Converter):
    """Images in help pages point at files in core/; copy those instead of fetching."""

    def convert(self, html, target, page_stem, version_alias="240"):  # type: ignore[override]
        keep = self.media_dir
        try:
            self.target = target
            self.version_alias = version_alias
            self.version_path = version_alias
            self.page_stem = page_stem
            html = html.replace("&nbsp;", " ").replace("\u00a0", " ").replace("\r\n", "\n")
            html = re.sub(r"</?(?:font|www)\b[^>]*>", "", html)
            from bs4 import BeautifulSoup, Comment
            soup = BeautifulSoup(html, "html.parser")
            for comment in soup.find_all(string=lambda s: isinstance(s, Comment)):
                comment.extract()
            for tag in soup.find_all(["script", "style"]):
                tag.decompose()
            body = self.blocks(soup)
            return re.sub(r"\n{3,}", "\n\n", body).strip() + "\n"
        finally:
            self.media_dir = keep

    def media_rel(self, name):  # type: ignore[override]
        return "../media/" + name

    def image(self, tag):  # type: ignore[override]
        src = (tag.get("src") or "").strip()
        alt = re.sub(r"\s+", " ", tag.get("alt", "") or "").strip()
        if src.startswith("/core/"):
            source = ROOT / src.lstrip("/")
            if source.exists():
                self.media_dir.mkdir(parents=True, exist_ok=True)
                name = f"{self.page_stem}-{source.name}"
                shutil.copy2(source, self.media_dir / name)
                self.report.images += 1
                return f"![{alt}]({self.media_rel(name)})"
            self.report.media_failed.append(src)
            return f"![{alt}]({src})"
        return super().image(tag)


def main() -> int:
    report = Report()
    media = MediaStore(IMPORT_DIR / "media-cache", fetch=False, report=report)
    converter = LocalConverter(media, {}, report)
    if OUT.exists():
        shutil.rmtree(OUT)
    rows = []
    for phtml in sorted(ROOT.glob("core/**/help/en-GB/*.phtml")):
        rel = phtml.relative_to(ROOT)
        parts = rel.parts  # core, components, com_x, site, help, en-GB, page.phtml
        extension = parts[2]
        side = parts[3] if parts[1] == "components" else parts[1]
        html = phtml.read_text(encoding="utf-8", errors="replace")
        html = BASE_ECHO.sub("", html)
        html = HOST_ECHO.sub("example.hub", html)
        html = PHP_BLOCK.sub("", html)
        target = f"com_help/{extension}/{side}/{phtml.stem}.md"
        converter.media_dir = OUT / extension / "media"
        body = converter.convert(html, target, phtml.stem)
        # the phtml <h1> is the page title; the converter demoted it to ## like every other heading
        title_match = re.match(r"\s*## (.+)\n", body)
        title = title_match.group(1).strip() if title_match else phtml.stem.replace("-", " ").capitalize()
        if title_match:
            body = body[title_match.end():]
        header = "<!--\nstatus: imported\nsource: " + str(rel) + f"\nimported: {TODAY}\n-->\n"
        out_path = OUT / extension / side / f"{phtml.stem}.md"
        out_path.parent.mkdir(parents=True, exist_ok=True)
        out_path.write_text(header + f"# {title}\n\n" + body.lstrip(), encoding="utf-8")
        book = "managers" if side == "admin" else "users"
        words = len(body.split())
        rows.append((extension, side, phtml.stem, title, words, book))
        report.pages += 1

    lines = [
        "# com_help pages", "",
        f"{report.pages} help pages converted from `core/**/help/en-GB/*.phtml` on {TODAY}. "
        "Each is a candidate for the chapter named in the last column; fold in what is still true and delete the rest.", "",
        "| Extension | Side | Page | Title | Words | Candidate book |", "|---|---|---|---|---|---|",
    ]
    for extension, side, stem, title, words, book in rows:
        lines.append(f"| {extension} | {side} | [{stem}]({extension}/{side}/{stem}.md) | {title} | {words} | {book} |")
    (OUT / "README.md").write_text("\n".join(lines) + "\n", encoding="utf-8")
    print(f"{report.pages} pages, {report.images} images copied, {len(report.media_failed)} missing images -> {OUT.relative_to(ROOT)}")
    return 0


if __name__ == "__main__":
    raise SystemExit(main())
