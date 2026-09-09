<!--
status: generated
source: core/components/com_wishlist/config/config.xml
-->

# Wishlist (com_wishlist)

Manage wishlists

Parameters from [`core/components/com_wishlist/config/config.xml`](../../../../core/components/com_wishlist/config/config.xml), as shown on the component's **Options** screen in the administrator interface.

## Basic

| Parameter | Label | Type | Default | Description |
|---|---|---|---|---|
| `categories` | Categories | text | `general, resource, group, user` | Comma-separated list of categories |
| `group` | Admin group | text | `hubdev` | Admin group for general wish lists |
| `maxtags` | Max Popular Tags | text | `10` | Number of popular tags to show |
| `banking` | Banking | radio | `0 (Disabled)` | Turn economy functions on/off. Options: `1` Enabled, `0` Disabled. |
| `show_percentage_granted` | Show percentage granted? | radio | `0 (No)` | Allow/Disallow diplay of percentage of granted wishes in selection. Options: `1` Yes, `0` No. |

## Advisory

| Parameter | Label | Type | Default | Description |
|---|---|---|---|---|
| `allow_advisory` | Allow advisory committee? | radio | `0 (No)` | Allow/Disallow advisory committee. Options: `1` Yes, `0` No. |
| `votesplit` | Vote weight: advisory committee vs owners | radio | `0 (50/50)` | You can choose to give more weight to votes from advisory committee. Options: `0` 50/50, `1` 80/20. |

## Files

| Parameter | Label | Type | Default | Description |
|---|---|---|---|---|
| `webpath` | Web path for storing attachments | text | `/site/wishlist` | Specify directory for attachment storage |
