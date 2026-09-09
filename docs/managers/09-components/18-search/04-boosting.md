<!--
status: imported
source: https://help.hubzero.org/documentation/240/managers/components/search/boosting
source-id: 3394
modified: 2019-09-11
imported: 2026-09-09
-->
# Boosting

Search boosting allows hub administrators to move categories of matching results higher or lower in Solr search results.

Note: your hub must have Solr installed and enabled as boosting only affects Solr search results.

To create a boost follow the steps below:

1. Access your hub's administrator portal
2. Click on Search under the Components drop-down menu
3. Click on the Boosts tab under the component title bar
   1. ![admin boost tab](../../media/boosting-boost-tab.png)
4. Click on the plus in the upper right hand corner
   1. ![new boost](../../media/boosting-new-boost.png)
5. Select a category from the Type drop-down
   1. ![new boost form](../../media/boosting-new-boost-form.png)
6. Enter a number for the boost strength
   1. Positive value moves matching results higher
   2. Negative value moves matching results lower
7. Save the boost
   1. ![save boost](../../media/boosting-finalize-new-boost.png)
