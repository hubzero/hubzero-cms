<!--
status: imported
source: https://help.hubzero.org/documentation/240/managers/components/search/install
source-id: 3391
modified: 2019-09-11
imported: 2026-09-09
-->
# Installation & First Time Configuration

1. A system administrator must **install the hubzero-solr** RedHat or Debian Package using a package manager such as yum or aptitude
   1. On RedHat: sudo yum install hubzero-solr
   2. On Debian: sudo apt-get install hubzero-solr
2. To start the service: **sudo service hubzero-solr start OR sudo /etc/init.d/hubzero-solr start>**
3. A Hub administrator may configure the Hubzero CMS to use Solr instead of basic search by going to Components > Search > Options > Engine > Apache Solr
4. A Hub administrator must further configure the service by clicking the “Solr” tab in the Search Options. The default parameters will work out-of-the-box for Open Source Hubs. HUBzero Managed Hubs will use the following port-numbering.
   - Development: 2090
   - Stage: 2091
   - Scan/QA: 2093
   - Production: 2093
     - Note: The default configuration should be acceptable. Running Solr on another host, changing the core, the path, or the log path are at the Hub Administrator's own risk.
5. Click **Save & Close**
6. Return to the **Search** and you should see a status page like the one below:
7. If this is the first time the Hub has used Solr as its search engine, the index will need to be filled with current data
   1. This is a lengthy operation during the first run
   2. Unless there is massive data corruption, this will only need to be performed once
   3. To check the progress of the indexing process, you may click on the **Index Queue** tab in the **Administrative Search** interface
8. Once the full index has been built, searching can be completed from the enabled search interfaces

*If there were any issues with configuration, please submit a support ticket and the HUBzero team will assist you further.*
