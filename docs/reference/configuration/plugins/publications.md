<!--
status: generated
source: core/plugins/publications/*/*.xml
-->

# Publications plugins

Parameters of every plugin in the `publications` group, from each plugin's manifest. Set them under **Extensions > Plugins** in the administrator interface.

## Publication - Citations (`plg_publications_citations`)

Displays citations for a publication

### Basic

| Parameter | Label | Type | Default | Description |
|---|---|---|---|---|
| `display_limit` | Display Limit | text | `50` | Number of items to return |
| `format` | Format | text | `APA` | Format to display items in |

## Publications - (metadata) Dublin Core (`plg_publications_dublincore`)

Add metadata for Dublin Core to the document

This plugin has no parameters.

## Publication - Forks (`plg_publications_forks`)

Displays publication forks

### Basic

| Parameter | Label | Type | Default | Description |
|---|---|---|---|---|
| `display_limit` | Display Limit | text | `50` | Number of items to return |

## Publications - (metadata) Google Scholar (`plg_publications_googlescholar`)

Add metadata for Google Scholar to the document

This plugin has no parameters.

## Publication - Group (`plg_publications_groups`)

Display group ownership for a publication

This plugin has no parameters.

## Publications - (metadata) JSON-LD (`plg_publications_jsonld`)

Add metadata for JSON-LD to the document

This plugin has no parameters.

## Publications - (metadata) Open Graph (`plg_publications_opengraph`)

Add metadata for Open Graph to the document

This plugin has no parameters.

## Publication - Questions (`plg_publications_questions`)

Displays questions related to a publication (by tag)

### Basic

| Parameter | Label | Type | Default | Description |
|---|---|---|---|---|
| `display_limit` | Display Limit | text | `50` | Number of items to return |

## Recommendations (`plg_publications_recommendations`)

Displays recommendations for a publication

### Basic

| Parameter | Label | Type | Default | Description |
|---|---|---|---|---|
| `threshold` | Threshold | text | `0.21` | The threshold for returning results |
| `display_limit` | Display Limit | text | `50` | Number of items to return |

## Publication - Related (`plg_publications_related`)

Displays related publication

### Basic

| Parameter | Label | Type | Default | Description |
|---|---|---|---|---|
| `display_limit` | Display Limit | text | `50` | Number of items to return |
| `miniview` | Minimal view | radio | `0 (No)` | Determines detailed or minimal display of related items. Options: `1` Yes, `0` No. |

## Publication - Reviews (`plg_publications_reviews`)

Displays reviews for a publication

### Basic

| Parameter | Label | Type | Default | Description |
|---|---|---|---|---|
| `display_limit` | Display Limit | text | `50` | Number of items to return |
| `voting` | Voting Enabled | radio | `1 (Yes)` | Allow voting on reviews and comments. Options: `0` No, `1` Yes. |

## Publication - Share (`plg_publications_share`)

Display options to post publication link on Facebbok, Twitter etc.

### Basic

| Parameter | Label | Type | Default | Description |
|---|---|---|---|---|
| `icons_limit` | Limit of Icons | radio | `3 (Limit icons to 3, show remaining in a pop-up)` | Number of share links to display. Options: `3` Limit icons to 3, show remaining in a pop-up, `0` Show all, no pop-up. |
| `share_facebook` | Show Facebook icon | radio | `1 (Yes)` | Allow to share with Facebook. Options: `0` No, `1` Yes. |
| `share_twitter` | Show Twitter icon | radio | `1 (Yes)` | Allow to share on Twitter. Options: `0` No, `1` Yes. |
| `share_google` | Show Google icon | radio | `1 (Yes)` | Allow to add a Google bookmark. Options: `0` No, `1` Yes. |
| `share_linkedin` | Show LinkedIn icon | radio | `1 (Yes)` | Allow to share on LinkedIn. Options: `0` No, `1` Yes. |
| `share_pinterest` | Show Pinterest icon | radio | `1 (Yes)` | Allow to share on Pinterest. Options: `0` No, `1` Yes. |
| `share_delicious` | Show Delicious icon | radio | `1 (Yes)` | Allow to share on Delicious. Options: `0` No, `1` Yes. |
| `share_reddit` | Show Reddit icon | radio | `1 (Yes)` | Allow to share on Reddit. Options: `0` No, `1` Yes. |

## Publication - supportingdocs (`plg_publications_supportingdocs`)

Displays supporting docs for a publication

### Basic

| Parameter | Label | Type | Default | Description |
|---|---|---|---|---|
| `display_limit` | Display Limit | text | `50` | Number of items to return |

## Publication - Usage (`plg_publications_usage`)

Displays usage info for a publication

### Basic

| Parameter | Label | Type | Default | Description |
|---|---|---|---|---|
| `period` | Time period | text | `15` | Time period to pull data for |
| `chart_path` | Chart path | text | `/site/usage/chart_resources/` | Path to the directory where charts are stored |
| `map_path` | Map path | text | `/site/usage/resource_maps/` | Path to the directory where map images are stored |

## Publication - versions (`plg_publications_versions`)

Displays all versions of a publication

### Basic

| Parameter | Label | Type | Default | Description |
|---|---|---|---|---|
| `display_limit` | Display Limit | text | `50` | Number of items to return |

## Publications - Watch (`plg_publications_watch`)

Display Watch feature for a publication

This plugin has no parameters.

## Publication - Wishlist (`plg_publications_wishlist`)

Displays publication wishlist

### Basic

| Parameter | Label | Type | Default | Description |
|---|---|---|---|---|
| `display_limit` | Display Limit | text | `50` | Number of items to return |
