<!--
status: generated
source: Event::trigger('handlers.*') call sites and core/plugins/handlers/
-->

# Handlers events

Events in the `handlers` group. A plugin in `core/plugins/handlers/` receives an event by defining a public method with the event's name; the arguments are those the call site passes, in order.

## `handlers.onHandleView`

Fired from:

- [`core/plugins/projects/files/connections.php:1596`](../../../core/plugins/projects/files/connections.php#L1596) with `[$items]`

Listeners:

- `plg_handlers_audio` — [`onHandleView(Hubzero\Filesystem\Collection $collection)`](../../../core/plugins/handlers/audio/audio.php)
- `plg_handlers_hubpresenter` — [`onHandleView(\Hubzero\Filesystem\Collection $collection, $entityId = null, $entityType = null)`](../../../core/plugins/handlers/hubpresenter/hubpresenter.php)
- `plg_handlers_ipynb` — [`onHandleView(Hubzero\Filesystem\Collection $collection)`](../../../core/plugins/handlers/ipynb/ipynb.php)
- `plg_handlers_latex` — [`onHandleView(\Hubzero\Filesystem\Collection $collection)`](../../../core/plugins/handlers/latex/latex.php)
- `plg_handlers_markdown` — [`onHandleView(Hubzero\Filesystem\Collection $collection)`](../../../core/plugins/handlers/markdown/markdown.php)
- `plg_handlers_pdf` — [`onHandleView(\Hubzero\Filesystem\Collection $collection)`](../../../core/plugins/handlers/pdf/pdf.php)
- `plg_handlers_script` — [`onHandleView(Hubzero\Filesystem\Collection $collection)`](../../../core/plugins/handlers/script/script.php)
- `plg_handlers_video` — [`onHandleView(Hubzero\Filesystem\Collection $collection)`](../../../core/plugins/handlers/video/video.php)
