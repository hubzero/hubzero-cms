<!--
status: imported
source: https://help.hubzero.org/documentation/240/managers/components/search/blacklist
source-id: 3397
modified: 2019-09-11
imported: 2026-09-09
-->
# Blacklist

The blacklist allows Hub administrators to “strike” things from the search index. This may be necessary to override If Solr indexes something, it will be reindexed unless it is on the blacklist.

To remove an item from the seach index, go to the Administrative Backend > Component > Search > Search Index and Click on the name of the type of record you would like to remove. Let’s say, for example, you needed to remove a Resource.

1. Click **Resource**

2. You will then see all resources indexed by Solr
3. You may use the search box to locate the record
4. Once you locate the record, click **Add to Blacklist**

5. Once the button is pressed, the request to remove the record will be placed into the queue
6. Once the worker processes the record, it will no longer be searchable by anyone
