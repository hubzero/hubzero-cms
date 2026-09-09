# Documentation site builder

The Hubzero documentation is the Markdown under [`docs/`](../docs/). This
directory turns it into the static site published at
https://hubzero.github.io/hubzero-cms/.

```
site.json           site identity, the list of books, and standalone pages
build_site.py       the builder: docs/ -> public/
check_links.py      fails on any broken internal link or anchor in public/
test_build_site.py  unit tests for the builder
redirects.json      old help.hubzero.org paths -> new pages (written by the importer)
templates/          home, doc (book page with rail), page (plain), redirect
assets/             stylesheet, search script, logo
public/             the built site, committed so it can be read without a build
```

## Build locally

```bash
python3 -m pip install -r gh-pages/requirements.txt -r tools/docs/requirements.txt pytest
sh tools/docs/rebuild.sh
python3 -m http.server -d gh-pages/public 8000
```

`rebuild.sh` regenerates the references, builds, checks every link, and runs
the tests, in that order. The order matters: the builder reads the generated
references, so building before regenerating them leaves the committed site
stale.

`public/` is committed. The Pages workflow rebuilds from source and fails if
the committed copy is stale, so after editing anything under `docs/`,
`gh-pages/`, or the code the references are generated from, rebuild and
commit `public/` with it.

## How a book is put together

A book is a directory under `docs/` listed in `site.json`. Inside it:

- `README.md` is the book's landing page.
- Every other `*.md` file is a chapter. Its `# Heading` is the page title.
- Every subdirectory is a section, with its own `README.md` as the landing
  page and its own chapters and subsections. Sections nest as deep as needed.
- A `media/` directory holds that book's images; anything that is not
  Markdown is copied to the site at the same relative path.
- Order comes from an optional numeric filename prefix (`01-`, `02-`), then
  the title. The prefix is dropped from the URL, so `03-controllers.md`
  publishes as `controllers/`.

A page may begin with a metadata comment; see [`docs/STYLE.md`](../docs/STYLE.md)
for the keys. `status: imported` puts a "not yet reviewed" banner on the page;
`status: reviewed` with `reviewed-against` puts a stamp in the footer. The
`/status/` page tallies both for every book.

The builder also writes `search-index.json` (used by the header search box),
`sitemap.xml`, `status.json`, and a redirect stub for every entry in
`redirects.json` so links into the old help.hubzero.org tree keep working once
that host forwards `/documentation/...` here.
