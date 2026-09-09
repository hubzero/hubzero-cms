"""Unit tests for the help.hubzero.org importer.

The importer runs rarely and against a 17 MB export, so its edge cases are
hard to see in a diff: a link that silently stays absolute, a heading level
that drifts, a reviewed page duplicated under a new number. These tests pin
the parts that decide those outcomes.

Run: python3 -m pytest tools/docs/test_import_help.py -q
"""

from __future__ import annotations

import sys
from pathlib import Path

import pytest

sys.path.insert(0, str(Path(__file__).resolve().parent))
sys.path.insert(0, str(Path(__file__).resolve().parents[2] / "gh-pages"))

import import_help as ih  # noqa: E402


# ------------------------------------------------------------------ target paths


@pytest.fixture()
def docs(tmp_path, monkeypatch):
    root = tmp_path / "docs"
    (root / "managers" / "components").mkdir(parents=True)
    (root / "managers" / "components" / "13-kb.md").write_text("# Knowledge base\n")
    (root / "managers" / "components" / "16-publications").mkdir()
    (root / "managers" / "components" / "16-publications" / "README.md").write_text("# Publications\n")
    monkeypatch.setattr(ih, "DOCS", root)
    return root


def test_a_renumbered_page_resolves_to_the_file_already_on_disk(docs):
    assert ih.existing_variant("managers/components/12-kb.md") == "managers/components/13-kb.md"


def test_a_renumbered_section_resolves_too(docs):
    assert (
        ih.existing_variant("managers/components/14-publications/README.md")
        == "managers/components/16-publications/README.md"
    )


def test_a_genuinely_new_page_keeps_its_computed_name(docs):
    assert ih.existing_variant("managers/components/07-new-thing.md") == "managers/components/07-new-thing.md"


def test_a_file_never_stands_in_for_a_directory(docs):
    # 13-kb.md must not be mistaken for the section directory of the same slug
    assert ih.existing_variant("managers/components/13-kb/README.md") == "managers/components/13-kb/README.md"


@pytest.mark.parametrize(
    ("target", "expected"),
    [
        ("managers/09-components/13-kb.md", "managers/components/kb/index.html"),
        ("managers/09-components/README.md", "managers/components/index.html"),
        ("users/11-groups/02-groupmembers.md", "users/groups/groupmembers/index.html"),
    ],
)
def test_output_href_drops_prefixes_and_readme(target, expected):
    assert ih.output_href(target) == expected


# ------------------------------------------------------------------ merge comparison


def test_identical_pages_score_one_and_unrelated_pages_score_zero():
    assert ih.similarity("<p>the same words</p>", "<p>the same words</p>") == 1.0
    assert ih.similarity("<p>alpha beta</p>", "<p>gamma delta</p>") == 0.0


def test_similarity_ignores_markup():
    assert ih.similarity("<p><b>one</b> two</p>", "<div>one two</div>") == 1.0


# ------------------------------------------------------------------ conversion


@pytest.fixture()
def convert(tmp_path, monkeypatch):
    monkeypatch.setattr(ih, "DOCS", tmp_path / "docs")
    report = ih.Report()
    media = ih.MediaStore(tmp_path / "cache", fetch=False, report=report)
    link_map = {"240/users/wiki": "users/23-wiki.md"}
    converter = ih.Converter(media, link_map, report)

    def run(html: str, target: str = "managers/kb.md") -> str:
        return converter.convert(html, target, "kb")

    run.report = report  # type: ignore[attr-defined]
    return run


def test_the_old_h3_section_headings_become_top_level_sections(convert):
    assert convert("<h3>Overview</h3><p>Text.</p>").startswith("## Overview")


def test_heading_levels_stay_in_order(convert):
    out = convert("<h3>One</h3><h4>Two</h4><h5>Three</h5>")
    assert "## One" in out and "### Two" in out and "#### Three" in out


def test_a_code_block_keeps_its_language(convert):
    out = convert('<pre class="php:nogutter:nocontrols">echo 1;</pre>')
    assert out.strip() == "```php\necho 1;\n```"


def test_a_note_paragraph_becomes_a_callout(convert):
    out = convert('<p class="note"><strong>Note:</strong> Mind the gap.</p>')
    assert out.strip() == "> **Note:** Mind the gap."


def test_a_bold_run_keeps_the_spaces_around_it(convert):
    out = convert("<p>Click the <strong>Members</strong> tab now.</p>")
    assert "the **Members** tab" in out


def test_a_numbered_placeholder_list_item_is_dropped(convert):
    out = convert("<ol><li>Real step</li><li>2</li><li>Another step</li></ol>")
    assert "Real step" in out and "Another step" in out
    assert "\n2. 2" not in out and convert.report.counters["placeholder_items_dropped"] == 1


def test_a_documentation_link_becomes_a_relative_markdown_link(convert):
    out = convert('<p><a href="/documentation/240/users/wiki">the wiki</a></p>')
    assert "[the wiki](../users/23-wiki.md)" in out


def test_a_current_alias_link_resolves_to_the_same_page(convert):
    out = convert('<p><a href="https://help.hubzero.org/documentation/current/users/wiki">wiki</a></p>')
    assert "(../users/23-wiki.md)" in out


def test_an_unknown_documentation_link_stays_absolute(convert):
    out = convert('<p><a href="/documentation/240/nope/gone">gone</a></p>')
    assert "https://help.hubzero.org/documentation/240/nope/gone" in out
    assert convert.report.unresolved_links


def test_the_version_macros_are_expanded(convert):
    out = convert("<p>Version {{version}}, path {{versionpath}}.</p>")
    assert "Version 240, path 240." in out


def test_a_simple_table_becomes_a_markdown_table(convert):
    out = convert("<table><tr><th>A</th><th>B</th></tr><tr><td>1</td><td>2</td></tr></table>")
    assert "| A | B |" in out and "| 1 | 2 |" in out


def test_a_table_with_a_rowspan_is_kept_as_html(convert):
    out = convert('<table><tr><td rowspan="2">x</td><td>y</td></tr></table>')
    assert out.strip().startswith("<table>")
    assert convert.report.tables_html


def test_an_image_that_cannot_be_fetched_keeps_an_absolute_url(convert):
    out = convert('<p><img src="/site/documentation/240/x.png" alt="A screen"></p>')
    assert "![A screen](https://help.hubzero.org/app/site/documentation/240/x.png)" in out


def test_text_that_looks_like_markdown_is_escaped(convert):
    assert "\\*not emphasis\\*" in convert("<p>*not emphasis*</p>")


# ------------------------------------------------------------------ page headers


def test_an_imported_page_is_owned_by_the_importer_but_a_reviewed_one_is_not(tmp_path):
    imported = tmp_path / "a.md"
    imported.write_text("<!--\nstatus: imported\n-->\n# A\n")
    reviewed = tmp_path / "b.md"
    reviewed.write_text("<!--\nstatus: reviewed\n-->\n# B\n")
    assert ih.importer_owns(imported)
    assert not ih.importer_owns(reviewed)
    assert ih.importer_owns(tmp_path / "missing.md")
