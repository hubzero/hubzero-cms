<!--
status: generated
source: core/components/com_storefront/config/config.xml
-->

# Storefront (com_storefront)

Store

Parameters from [`core/components/com_storefront/config/config.xml`](../../../../core/components/com_storefront/config/config.xml), as shown on the component's **Options** screen in the administrator interface.

## Basic

| Parameter | Label | Type | Default | Description |
|---|---|---|---|---|
| `downloadFolder` | Files download folder | text | `/site/protected/storefront/software` | Example: /app/storefront |
| `imagesFolder` | Product images folder | text | `/site/storefront/products` | Example: /app/site/storefront/products |
| `requirelogin` | Require Login | list | `0 (No)` | Do users need to login to see the store pages?. Options: `0` No, `1` Yes. |
| `landingPage` | Landing page ID | text | — | If login is required an article can optionally be assigned to be a custom landing page. Provide an article's numeric ID here to set it as a landing page (set the article's access to private to prevent direct access.) |
| `productAccess` | Product Access Control | list | `0 (Access levels)` | Select the method use for access to products. 'Access levels' is the default behavior used by the majority of the CMS. 'Access groups' allows for more fine-grained control by specifying the explicit access groups a user must be apart of in order to purchase a product. Options: `0` Access levels, `1` Access groups. |
| `quantityText` | Quantity Text | text | — | Customize the text used for the number of items to be purchased. Defaults to 'Quantity' if nothing is entered. |
