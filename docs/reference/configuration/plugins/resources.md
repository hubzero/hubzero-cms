<!--
status: generated
source: core/plugins/resources/*/*.xml
-->

# Resources plugins

Parameters of every plugin in the `resources` group, from each plugin's manifest. Set them under **Extensions > Plugins** in the administrator interface.

## Resource - About (`plg_resources_about`)

Displays about information for a resource

### Basic

| Parameter | Label | Type | Default | Description |
|---|---|---|---|---|
| `badges` | Show User Badges | radio | `0 (No)` | Show User Badges. Options: `0` No, `1` Yes. |

## Resource - Citations (`plg_resources_citations`)

Displays citations for a resource

### Basic

| Parameter | Label | Type | Default | Description |
|---|---|---|---|---|
| `display_limit` | Display Limit | text | `50` | Number of items to return |
| `format` | Format | text | `APA` | Format to display items in |

## Resources - (metadata) COinS (`plg_resources_coins`)

Add metadata for COinS (ContextObjects in Spans) to the document

### Basic

| Parameter | Label | Type | Default | Description |
|---|---|---|---|---|
| `payload_url` | Use Payload URL? | radio | `0 (No)` | If set to 'Yes', the URL for the primary (first) child will be used as the identifier. Options: `1` Yes, `0` No. |

### Metadata

| Parameter | Label | Type | Default | Description |
|---|---|---|---|---|
| `type` | Resource type to COinS type | resourcetype | — | Associate specific resource types to know COinS types |

## Resource - Collections (`plg_resources_collections`)

Allows Resource to be added to a Series/Collection

### Basic

| Parameter | Label | Type | Default | Description |
|---|---|---|---|---|
| `collection_alias` | Collection Alias | text | `50` | Alias of Collectable Resource Type |
| `collection_afterpublished` | Allow items to be added to a published collection? | radio | `0 (No)` | If this it set to yes, it will allow published collections to have more resources added to it. Options: `1` Yes, `0` No. |

## Resources - (metadata) Dublin Core (`plg_resources_dublincore`)

Add metadata for Dublin Core to the document

This plugin has no parameters.

## Resource - Sponsors (`plg_resources_findthistext`)

Display sponsors for a resource

### Basic

| Parameter | Label | Type | Default | Description |
|---|---|---|---|---|
| `format` | Format | text | `{journal}@{publisher}` | Tag format to use to link sponsor data |

## Resources - (metadata) Google Scholar (`plg_resources_googlescholar`)

Add metadata for Google Scholar to the document

This plugin has no parameters.

## Resource - Group (`plg_resources_groups`)

Display group ownership for a resource

This plugin has no parameters.

## Resources - HIPAA Compliant (`plg_resources_hipaacompliant`)

Save HIPAA Compliance checkbox

This plugin has no parameters.

## Resources - (metadata) Open Graph (`plg_resources_opengraph`)

Add metadata for Open Graph to the document

This plugin has no parameters.

## Resource - Questions (`plg_resources_questions`)

Display questions related to a resource (by tag)

### Basic

| Parameter | Label | Type | Default | Description |
|---|---|---|---|---|
| `display_limit` | Display Limit | text | `50` | Number of items to return |

## plg_resources_ecommendations (`plg_resources_recommendations`)

Display recommendations for a resource

### Basic

| Parameter | Label | Type | Default | Description |
|---|---|---|---|---|
| `threshold` | Threshold | text | `0.21` | The threshold for returning results |
| `display_limit` | Display Limit | text | `10` | Number of items to return |

## Resource - Related (`plg_resources_related`)

Display related resources

### Basic

| Parameter | Label | Type | Default | Description |
|---|---|---|---|---|
| `display_limit` | Display Limit | text | `50` | Number of items to return |
| `miniview` | Minimal view | radio | `0 (No)` | Determines detailed or minimal display of related items. Options: `1` Yes, `0` No. |

## Resource - Reviews (`plg_resources_reviews`)

Display reviews for a resource

### Basic

| Parameter | Label | Type | Default | Description |
|---|---|---|---|---|
| `display_limit` | Display Limit | text | `50` | Number of items to return |
| `voting` | Voting Enabled | radio | `1 (Yes)` | Allow voting on reviews and comments. Options: `0` No, `1` Yes. |

## Resources - Share (`plg_resources_share`)

Display options to post resource link on Facebook, Twitter, etc.

### Basic

| Parameter | Label | Type | Default | Description |
|---|---|---|---|---|
| `icons_limit` | Limit of Icons | text | `7` | Number of share links to display |
| `share_facebook` | Show Facebook icon | radio | `1 (Yes)` | Allow to share with Facebook. Options: `0` No, `1` Yes. |
| `share_twitter` | Show Twitter icon | radio | `1 (Yes)` | Allow to share on Twitter. Options: `0` No, `1` Yes. |
| `share_google` | Show Google icon | radio | `1 (Yes)` | Allow to add a Google bookmark. Options: `0` No, `1` Yes. |
| `share_delicious` | Show Delicious icon | radio | `1 (Yes)` | Allow to share on Delicious. Options: `0` No, `1` Yes. |
| `share_reddit` | Show Reddit icon | radio | `1 (Yes)` | Allow to share on Reddit. Options: `0` No, `1` Yes. |
| `share_linkedin` | Show LinkedIn icon | radio | `1 (Yes)` | Allow to share on LinkedIn. Options: `0` No, `1` Yes. |

## Resource - Sponsors (`plg_resources_sponsors`)

Display sponsors for a resource

### Basic

| Parameter | Label | Type | Default | Description |
|---|---|---|---|---|
| `format` | Format | text | `{journal}@{publisher}` | Tag format to use to link sponsor data |

## Resource - Supporting Documents (`plg_resources_supportingdocs`)

Display supporting documents

### Basic

| Parameter | Label | Type | Default | Description |
|---|---|---|---|---|
| `display_limit` | Display Limit | text | `50` | Number of items to return |

## Resource - Usage (`plg_resources_usage`)

Display usage information for a resource

### Basic

| Parameter | Label | Type | Default | Description |
|---|---|---|---|---|
| `period` | Time Period: | text | `14` | Time period to pull data for |
| `pie_chart_path` | Chart path | text | `/site/usage/pie_chart_resources/` | Path to the directory where charts are stored |
| `defaultDataset` | Default dataset | radio | `cumulative (Cumulative)` | Determines the default dataset to display. Options: `cumulative` Cumulative, `yearly` Yearly, `monthly` Monthly. |
| `mapZoom` | Map Zoom | text | `2` | Map Zoom level for Google Maps |
| `mapLng` | Map Longitude | text | `20` | Map Longitude for Google Maps |
| `mapLat` | Map Latitude | text | `0` | Map Latitude for Google Maps |
| `map_path` | Map path | text | `/site/stats/resource_maps/` | Path to the directory where map images are stored |
| `chart_color_line` | Chart line color | text | `#656565` | Chart line color |
| `chart_color_fill` | Chart fill color | text | `rgba(0, 0, 0, 0.15)` | Chart fill color |
| `chart_color_selection` | Chart select color | text | `#656565` | Chart selection color |
| `pie_chart_color1` | Pie Color 1 | text | `#7c7c7c` | Pie chart colors |
| `pie_chart_color2` | Pie Color 2 | text | `#515151` | Pie chart colors |
| `pie_chart_color3` | Pie Color 3 | text | `#d9d9d9` | Pie chart colors |
| `pie_chart_color4` | Pie Color 4 | text | `#3d3d3d` | Pie chart colors |
| `pie_chart_color5` | Pie Color 5 | text | `#797979` | Pie chart colors |
| `pie_chart_color6` | Pie Color 6 | text | `#595959` | Pie chart colors |
| `pie_chart_color7` | Pie Color 7 | text | `#e5e5e5` | Pie chart colors |
| `pie_chart_color8` | Pie Color 8 | text | `#828282` | Pie chart colors |
| `pie_chart_color9` | Pie Color 9 | text | `#404040` | Pie chart colors |
| `pie_chart_color10` | Pie Color 10 | text | `#3a3a3a` | Pie chart colors |

## Resource - Versions (`plg_resources_versions`)

Display all versions of a resource

### Basic

| Parameter | Label | Type | Default | Description |
|---|---|---|---|---|
| `display_limit` | Display Limit | text | `50` | Number of items to return |

## Watch It (`plg_resources_watch`)

Display Watch feature for a resource

This plugin has no parameters.

## Resource - Windows Tools (`plg_resources_windowstools`)

Handles Windows Tools invocation and displays launch instructions.

### Basic

| Parameter | Label | Type | Default | Description |
|---|---|---|---|---|
| `invoke_url` | Invoke URL | text | `http://wapps.hubzero.org` | The base invoke URL domain. |

## Resource - Wishlist (`plg_resources_wishlist`)

Display wishlist for a resource

### Basic

| Parameter | Label | Type | Default | Description |
|---|---|---|---|---|
| `display_limit` | Display Limit | text | `50` | Number of items to return |
