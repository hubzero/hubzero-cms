<!--
status: generated
source: core/plugins/handlers/*/*.xml
-->

# Handlers plugins

Parameters of every plugin in the `handlers` group, from each plugin's manifest. Set them under **Extensions > Plugins** in the administrator interface.

## Handlers - Audio (`plg_handlers_audio`)

File handlerfor HTML5 audio (mp3, wav)

This plugin has no parameters.

## Handlers - Hubpresenter (`plg_handlers_hubpresenter`)

Hubpresenter file handler

This plugin has no parameters.

## Handlers - Jupyter Notebook (`plg_handlers_ipynb`)

Jupyter Notebook file handler

This plugin has no parameters.

## Handlers - Latex (`plg_handlers_latex`)

Latex file handler

### Basic

| Parameter | Label | Type | Default | Description |
|---|---|---|---|---|
| `texpath` | Path to LaTeX | text | `/usr/bin/pdflatex` | Path to LaTeX |
| `compile_dir` | Compilation directory | text | `site/latex/compiled` | Location where compiled PDF's and logs should be saved. |

## Handlers - MarkDown (`plg_handlers_markdown`)

MarkDown file handler

### Basic

| Parameter | Label | Type | Default | Description |
|---|---|---|---|---|
| `type` | Style | style | `Markdown` | Choose the flavor of Markdown to use. Options: `Markdown`, `GithubMarkdown` Github Markdown, `MarkdownExtra` Markdown Extra. |

## Handlers - PDF (`plg_handlers_pdf`)

PDF file handler

This plugin has no parameters.

## Handlers - Script (`plg_handlers_script`)

Generic script/code file handler with syntax highlighting

This plugin has no parameters.

## Handlers - Video (`plg_handlers_video`)

PLG_HANDLERS_SCRIPT_XML_DESCRIPTION

This plugin has no parameters.
