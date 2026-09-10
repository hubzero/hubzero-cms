"""Unit tests for the documentation site builder.

The docs are read two ways, as Markdown on GitHub and as pages on the built
site, and a link that is right in one place is usually wrong in the other.
These tests pin the link mapping, the tree ordering, the metadata header,
and the admonition transform, because each of those breaks silently.

Run: python3 -m pytest gh-pages/test_build_site.py -q
"""

from __future__ import annotations

import sys
from pathlib import Path

import pytest

sys.path.insert(0, str(Path(__file__).resolve().parent))

import build_site  # noqa: E402
from build_site import (  # noqa: E402
    MarkdownRenderer,
    build_tree,
    excerpt_of,
    load_section,
    make_link_resolver,
    order_key_for,
    parse_meta,
    slug_for,
    slugify,
)

BLOB = "https://github.com/hubzero/hubzero-cms/blob/2.4-main"
SOURCE_TO_OUTPUT = {
    "docs/managers/README.md": "managers/index.html",
    "docs/managers/components/README.md": "managers/components/index.html",
    "docs/managers/components/kb.md": "managers/components/kb/index.html",
    "docs/users/wiki.md": "users/wiki/index.html",
    "docs/users/03-collections.md": "users/collections/index.html",
    "docs/users/10-groups/README.md": "users/groups/index.html",
    "docs/README.md": "docs/index.html",
}


def resolver(source="docs/managers/components/kb.md", output="managers/components/kb/index.html", warn=None):
    return make_link_resolver(source, output, SOURCE_TO_OUTPUT, BLOB, warn if warn is not None else (lambda m: None))


# ------------------------------------------------------------------ slugs and ordering


def test_slugify_matches_heading_anchors():
    assert slugify("Creating Knowledge Base Articles") == "creating-knowledge-base-articles"
    assert slugify("What's the API?") == "what-s-the-api"
    assert slugify("") == "section"


@pytest.mark.parametrize(
    ("name", "expected"),
    [
        ("01-getting-started.md", "getting-started"),
        ("12_controllers.md", "controllers"),
        ("answers.md", "answers"),
        ("03-Data Types", "data-types"),
    ],
)
def test_numeric_prefix_is_dropped_from_the_url(name, expected):
    assert slug_for(name) == expected


def test_order_key_prefers_meta_then_prefix_then_name():
    assert order_key_for("zzz.md", {"order": "1"}) < order_key_for("02-aaa.md", {})
    assert order_key_for("01-aaa.md", {}) < order_key_for("02-aaa.md", {})
    assert order_key_for("02-zzz.md", {}) < order_key_for("aaa.md", {})
    assert order_key_for("aaa.md", {}) < order_key_for("bbb.md", {})


# ------------------------------------------------------------------ metadata header


def test_meta_header_is_split_from_the_body():
    meta, body = parse_meta(
        "<!--\nstatus: imported\nsource: https://help.hubzero.org/documentation/240/x\nmodified: 2016-03-01\n-->\n# Title\n\nText.\n"
    )
    assert meta == {
        "status": "imported",
        "source": "https://help.hubzero.org/documentation/240/x",
        "modified": "2016-03-01",
    }
    assert body.lstrip().startswith("# Title")


def test_ordinary_leading_comment_is_not_metadata():
    text = "<!-- just a note to editors -->\n# Title\n"
    meta, body = parse_meta(text)
    assert meta == {}
    assert body == text


def test_no_header_means_no_meta():
    meta, body = parse_meta("# Title\n")
    assert meta == {} and body == "# Title\n"


# ------------------------------------------------------------------ links


@pytest.mark.parametrize(
    ("href", "expected"),
    [
        ("../../users/wiki.md", "../../../users/wiki/index.html"),
        ("README.md#creating", "../index.html#creating"),
        ("../README.md", "../../index.html"),
        ("../components/", "../index.html"),
        ("../../README.md", "../../../docs/index.html"),
    ],
)
def test_links_to_pages_become_relative_site_links(href, expected):
    assert resolver()(href) == expected


def test_links_may_omit_the_numeric_prefix():
    assert resolver()("../../users/collections.md") == "../../../users/collections/index.html"
    assert resolver()("../../users/groups/README.md") == "../../../users/groups/index.html"
    assert resolver()("../../users/groups/") == "../../../users/groups/index.html"


def test_link_to_a_file_under_docs_resolves_to_the_copied_file(tmp_path, monkeypatch):
    docs = tmp_path / "docs" / "managers" / "media"
    docs.mkdir(parents=True)
    (docs / "kb.png").write_bytes(b"png")
    monkeypatch.setattr(build_site, "ROOT", tmp_path)
    assert resolver()("../media/kb.png") == "../../media/kb.png"


def test_link_to_a_repo_directory_goes_to_the_github_tree(tmp_path, monkeypatch):
    (tmp_path / "core" / "libraries").mkdir(parents=True)
    monkeypatch.setattr(build_site, "ROOT", tmp_path)
    expected = BLOB.replace("/blob/", "/tree/") + "/core/libraries"
    assert resolver()("../../../core/libraries") == expected


def test_link_to_any_other_repo_file_goes_to_github(tmp_path, monkeypatch):
    (tmp_path / "core").mkdir()
    (tmp_path / "core" / "index.php").write_text("<?php")
    monkeypatch.setattr(build_site, "ROOT", tmp_path)
    assert resolver()("../../../core/index.php") == f"{BLOB}/core/index.php"


def test_site_assets_resolve_inside_the_build():
    assert resolver()("../../../gh-pages/assets/hubzero-mark.svg") == "../../../assets/hubzero-mark.svg"


@pytest.mark.parametrize("href", ["#anchor", "https://hubzero.org/", "mailto:a@b.c", "//cdn.example/x", ""])
def test_links_that_are_not_ours_are_untouched(href):
    assert resolver()(href) == href


def test_missing_markdown_target_warns_and_is_left_alone():
    warnings: list[str] = []
    assert resolver(warn=warnings.append)("nope.md") == "nope.md"
    assert warnings and "nope.md" in warnings[0]


# ------------------------------------------------------------------ rendering


def test_admonition_blockquote_becomes_an_aside():
    html = MarkdownRenderer().render("> **Note:** Mind the gap.\n")["html"]
    assert '<aside class="admonition admonition--note">' in html
    assert "</aside>" in html
    assert "<blockquote>" not in html


def test_unknown_blockquote_label_stays_a_blockquote():
    html = MarkdownRenderer().render("> **Remember:** nothing.\n")["html"]
    assert "<blockquote>" in html


def test_title_comes_from_h1_and_toc_from_h2_h3():
    rendered = MarkdownRenderer().render("# Page\n\n## One\n\ntext\n\n### One point one\n\n#### Deep\n")
    assert rendered["title"] == "Page"
    assert [(t["level"], t["anchor"]) for t in rendered["toc"]] == [(2, "one"), (3, "one-point-one")]
    assert "<h1" not in rendered["html"]
    assert 'id="deep"' in rendered["html"]


def test_duplicate_headings_get_distinct_anchors():
    html = MarkdownRenderer().render("## Setup\n\n## Setup\n")["html"]
    assert 'id="setup"' in html and 'id="setup-2"' in html


def test_excerpt_strips_tags_and_code():
    text = excerpt_of("<p>Hello <b>world</b></p><pre>code</pre><p>again</p>", 50)
    assert text == "Hello world again"


# ------------------------------------------------------------------ book tree


def make_book(tmp_path):
    book = tmp_path / "docs" / "guide"
    (book / "advanced").mkdir(parents=True)
    (book / "README.md").write_text("# The guide\n")
    (book / "02-second.md").write_text("<!--\nstatus: reviewed\n-->\n# Second\n")
    (book / "01-first.md").write_text("# First\n")
    (book / "zeta.md").write_text("# Zeta\n")
    (book / "advanced" / "README.md").write_text("# Advanced\n")
    (book / "advanced" / "topic.md").write_text("# Topic\n")
    (book / "orphan").mkdir()
    (book / "orphan" / "page.md").write_text("# Orphan page\n")
    return book


def test_sections_and_chapters_are_ordered_and_slugged(tmp_path, monkeypatch):
    monkeypatch.setattr(build_site, "ROOT", tmp_path)
    book = make_book(tmp_path)
    root = load_section(book, ["guide"], build_site.Book("guide", "Guide", "docs/guide", "", "", True), None)
    assert root.title == "The guide"
    assert root.output == "guide/index.html"
    titles = [c.title for c in root.children]
    assert titles == ["First", "Second", "Advanced", "Orphan", "Zeta"]
    outputs = [c.output for c in root.children]
    assert outputs[0] == "guide/first/index.html"
    assert outputs[2] == "guide/advanced/index.html"
    orphan = root.children[3]
    assert orphan.synthetic and orphan.children[0].output == "guide/orphan/page/index.html"
    assert root.children[1].status == "reviewed"


def test_tree_opens_the_active_path_only(tmp_path, monkeypatch):
    monkeypatch.setattr(build_site, "ROOT", tmp_path)
    book = make_book(tmp_path)
    root = load_section(book, ["guide"], build_site.Book("guide", "Guide", "docs/guide", "", "", True), None)
    topic = root.children[2].children[0]
    out = tmp_path / "public"
    html = build_tree(root, topic, out / topic.output, out, "")
    assert '<details open><summary><a class="tree__link" href="../index.html">Advanced</a>' in html
    assert 'aria-current="page">Topic</a>' in html
    assert html.count("<details open>") == 1


def test_redirect_target_may_carry_a_fragment(tmp_path):
    """A page merged into a section of another keeps its old URL working."""
    import build_site
    assert build_site.slugify("Accessing a home directory") == "accessing-a-home-directory"
    page, _, fragment = "tools/developers/index.html#accessing-a-home-directory".partition("#")
    assert page == "tools/developers/index.html"
    assert fragment == "accessing-a-home-directory"


def test_a_page_that_writes_its_own_contents_list_suppresses_the_automatic_one():
    """Otherwise a landing page says "In this section" twice."""
    import build_site
    page = build_site.Page.__new__(build_site.Page)
    page.children = [object()]
    own = '<h2 id="in-this-section">In this section</h2>'
    assert build_site.build_children_list(page, Path("x"), Path("y"), own) == ""


def test_a_landing_page_linking_every_child_suppresses_the_automatic_list():
    """The curated list wins, whatever heading it sits under."""
    import build_site

    class Child:
        def __init__(self, out, title):
            self.output, self.title, self.summary = out, title, ""

    page = build_site.Page.__new__(build_site.Page)
    page.children = [Child("book/one/index.html", "One"), Child("book/two/index.html", "Two")]
    out_dir, out_path = Path("o"), Path("o/book/index.html")
    both = '<a href="one/index.html">x</a><a href="two/index.html">y</a>'
    assert build_site.build_children_list(page, out_path, out_dir, both) == ""
    one_only = '<a href="one/index.html">x</a>'
    assert "In this section" in build_site.build_children_list(page, out_path, out_dir, one_only)
