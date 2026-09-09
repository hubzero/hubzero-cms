<!--
status: imported
source: https://help.hubzero.org/documentation/240/managers/components/search/admin
source-id: 3392
modified: 2019-09-11
imported: 2026-09-09
-->
# Administration

## Restarting Solr

If Solr needs to be restarted, a system administrator can issues the following commands:

1. sudo service hubzero-solr restart
2. sudo /etc/init.d/hubzero-solr restart

## Solr Index CRON Updater

In order to keep the Solr index up-to-date, the CMS will periodically call a routine to process the queue. Although CRON is not the best tool for the job, it will dutifully process the queue every minute if configured. There are plans to develop a background process which will make this process more efficient.

To configure the CRON Task, follow these steps:

1. Navigate to **/administrator**
2. Hover over **Components** and click **Cron** from the drop-down
3. Click **Add a New Task** and set the "Event:" to **Process Queue**

4. The **New Cron Task** should be configured to run every minute
