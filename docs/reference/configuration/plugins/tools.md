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
| `browsers` | Minimum OS/Browsers | textarea | `*, safari 5.1 *, chrome 27.0 *, iceweasel 38.0 *, firefox 30.0 *, opera 23.0 *, mozilla 5.0 iOS, safari 1.0 Windows, msie 10.0 Windows, ie 10.0` | A list of minimum OS/Browser required. One entry per line, the pattern is 'OS, BROWSER MAJOR.MINOR'. If all OSes apply, us an asterisk. |
| `regexes` | UAS Regexes | textarea | — | A list of regular expressions to run against the User Agent String. One entry per line. If a UAS matches a pattern, the plugin will no render. |
