<!--
status: rewritten
reviewed: 2026-09-09
-->
# Working on the documentation

The documentation is Markdown under `docs/` in the hubzero-cms repository,
built into the site you are reading by the scripts under `gh-pages/`. It is
edited the same way as the code: on a branch, in a pull request, with the
site rebuilt and checked before it merges.

## Editing a page

Every page has an **Edit this page on GitHub** link in its footer. For
anything larger than a typo, clone the repository and work locally:

```bash
python3 -m pip install -r gh-pages/requirements.txt -r tools/docs/requirements.txt pytest
sh tools/docs/rebuild.sh
python3 -m http.server -d gh-pages/public 8000
```

Then open http://localhost:8000/. Rebuild after each change; the build
takes a few seconds.

The [writing guide](../STYLE.md) covers file naming, the metadata header,
links, code blocks, callouts, and house style.

## Reviewing an imported page

Most pages were imported from help.hubzero.org and carry a banner until
someone checks them against the code. To review one:

1. Read the page against the current code on `2.4-main`: the controllers,
   views, and `config.xml` of the component it describes. Fix or rewrite
   what has drifted. Replace pasted code with an include directive that
   pulls the real file.
2. Regenerate any screenshots from a current hub.
3. Change the header to `status: reviewed`, add `reviewed-against` with the
   branch and commit you checked, and `reviewed` with the date.
4. Rebuild, commit `gh-pages/public/` with the change, and open a pull
   request.

The [status page](https://hubzero.github.io/hubzero-cms/status/) shows what
is left in each book.

## Committing

Commit `gh-pages/public/` together with the source change. The Pages
workflow rebuilds the site from source on every push and fails if the
committed copy does not match, so a stale copy cannot be deployed by
accident.
