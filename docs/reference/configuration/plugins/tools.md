<!--
status: generated
source: core/plugins/tools/*/*.xml
-->

# Tools plugins

Parameters of every plugin in the `tools` group, from each plugin's manifest. Set them under **Extensions > Plugins** in the administrator interface.

## Tools - NoVNC (`plg_tools_novnc`)

Display a tool session with NoVNC

### Basic

| Parameter | Label | Type | Default | Description |
|---|---|---|---|---|
| `vnc_engine` | VNC JS engine | list | `modern (Modern noVNC 1.7.0 (bundled + HubZero patches))` | Which VNC client to run in the viewer. Overridable per-request with &vncengine=legacy\|modern\|vanilla. Options: `legacy` HubZero noVNC (bundled + extensions), `modern` Modern noVNC 1.7.0 (bundled + HubZero patches), `vanilla` Vanilla noVNC (current upstream, from CDN). |
| `clipboard_block_hostreq` | No clipboard on hosts | text | — | Comma-separated host requirements (e.g. geddes_restricted). Tools whose version requires one of these get no copy and paste in the modern viewer. |
| `browsers` | Minimum OS/Browsers | textarea | `*, safari 5.1 *, chrome 27.0 *, iceweasel 38.0 *, firefox 30.0 *, opera 23.0 *, mozilla 5.0 iOS, safari 1.0 Windows, msie 10.0 Windows, ie 10.0` | A list of minimum OS/Browser required. One entry per line, the pattern is 'OS, BROWSER MAJOR.MINOR'. If all OSes apply, us an asterisk. |
| `regexes` | UAS Regexes | textarea | — | A list of regular expressions to run against the User Agent String. One entry per line. If a UAS matches a pattern, the plugin will no render. |
