<!--
status: imported
source: https://help.hubzero.org/documentation/240/installation/el8/addons/solr
source-id: 3677
modified: 2017-01-10
imported: 2026-09-09
source-state: unpublished
-->
# Solr-powered Search

## Introduction

Apache Solr is a search engine platform which is relatively mature and has a lot of powerful and flexible configurations. There has been extensive work to implement it into the HUBzero CMS and is currently a work-in-progress.

Solr is an open-source, mature, and stable searching service that is built upon the Apache Lucene search engine. The service provides features which lend itself to scaling and has a rich open source community. It is a Java-based service which provides search results through HTTP. Many companies such as Instagram, eBay, and StubHub rely on Solr to provide advanced searching capabilities.

> **Warning:** The integration with Solr is currently under heavy development. It is **strongly recommended** to test on a QA / Stage host before using in a production environment.

## Installation & First Time Configuration

## Step 1: Install the hubzero-solr package

A system administrator must install the hubzero-solr RedHat or Debian Package using a package manager such as yum or aptitude. The package contains a version of Apache Solr and the configuration necessary for Solr to integrate with the CMS.

```
For RedHat / CentOS:
$    sudo yum install hubzero-solr
```

```
For Debian:
$    sudo apt-get install hubzero-solr
```

Once installed the service will need to be enabled.

```
$    sudo service hubzero-solr start
```

## Step 2: Configure Search Service in the CMS

The HUBzero CMS needs to know to use Solr Search instead of Basic Search. To do this, a Hub administrator will need to log into the Administrative Backend and Configure the Search Component.

You will need to set **Engine** to *Apache Solr.* Then click the "Solr tab".

![CapturFiles-06-21-2016_03.21.10.png](https://lh3.googleusercontent.com/wig8yZRiM9tZn4RispF5keEn2ANabJuyNW4-Wl3XUJLebEAqwtW-XIAYP_8wdfkippGHqYewFXXwD4UZC40y0TX4l-Vec61qVKEZ3hjsvgyw89KG5Z6OXFwrr_R_4N31VWA867nq)

The Solr tab's default settings will work for the open-source distribution.

![CapturFiles-06-21-2016_03.21.25.png](https://lh6.googleusercontent.com/EoQBPzs8i9W_C0K27TUBHE2Zly2GxzWU0G85WX1pLDQHPc0lGq1LgqZFvOCRWVx7r9gNTW52z8jBbeKtWgaFJlWdWvhdmxwBttm6z7vbH_StSJliDxZ1ZzEKcJoJcdsqgDNcEHKf)

> **Note:** HUBzero-hosted hubs are configured with different ports! The following scheme is used:

```
Development (dev.hub.org): 2090
Stage (stage.hub.org): 2091
Scan / QA (qa.hub.org): 2092
Production (hub.org): 2093
```

Click "Save and Close" to save the settings. If the hubzero-solr service is started and the correct settings were set in the steps above, the status screen should indicate that the search engine is responding.

![CapturFiles-06-23-2016_03.23.01.png](https://lh4.googleusercontent.com/5Z0nQ2fQicXo-EmwTC-qzOifU2xp9hARxZVjEtYNRe7_WhPCxOPFhGCenS5P002IhBrL3ALrfqmET-9KfLYacFv1ygTrLAQ4mZ6vfyhkLD6Xoz_b2JsW516gt7sWRyMt_tYjp1Y1)

If there were any issues with configuration, the following screen will appear.

![CapturFiles-06-20-2016_03.20.54.png](https://lh5.googleusercontent.com/YKkoA4MLv7O5QzeirqoWXET0FgmAFLc6URDJNGmj-WUygoxbjPojVSoX3Aat6xLGDT17VktmcwTuVzv5D-CB5W0S6rZTC82dfNtgvWb1PL_9_wmWyx2uCp_vuUexbZz82omvhstp)

This would be a point where a support ticket is filed for the system administrator to confirm that the service is running. Please include all configuration parameters contained in Step #3 when filing the ticket.

## Step 3: Enable the Search Background Worker

In order to keep the search index fresh, a background worker is implemented to process data from the CMS and push it into the Solr service.

> **Note:** Currently the background worker is implemented as a Cron task that is called once a minute. There is work being done to develop a daemon which listens to CMS events and processes data without relying on Cron.

To setup the Cron-based worker a Hub administrator must go into the Administrative Backend, go to Components, Cron, and add the Task as shown below:

![CapturFiles-07-56-2016_07.56.25.png](https://lh5.googleusercontent.com/soeBrQ8oA7N2FtpCd9Q_h-1ToFGblXYx7quNcwybH0Rl-e0Y238645iexTiNHCkuc3u4hzrej1ulNlZSwc_uVQe-ZEk2ib4lGv-ELgzdYqaCQHbOwX3mA30dd4pTpZHwkqp378jj)

Click "Save and Close".

## Step 4: Build the Initial Index

This implementation of Solr has hooks into the CMS which updates the index when a new record is added or marked for deletion. It will be necessary to add items which have been added before Solr was activated.

![The full-index button](../../media/solr-fullindex-solr.png)

> **Note:** This operation should only need to be completed once. You will be unable to start this operation until it finishes for the first time.

![Informational notification specifying that index building can only be applied once.](../../media/solr-screenshot-2017-02-13-12-51-35.png)

The "Full Index" button populates a Queue which is periodically serviced by a worker. The worker will process the records and format for consumption by the Solr service. **This may take several hours to fully complete if the Hub has a lot of content.**

If an error with the worker occurs, a warning message such as this will appear.

![](../../media/solr-screenshot-2017-02-13-12-57-09.png)

## Search Breadth

Te question is “What can I search for?”. The answer is “anything you have access to contained within the list in Search Categories. To see all content within these categories perform a simple query using the wildcard character “\*” as shown below.

![CapturFiles-12-49-2016_09.49.46.png](https://lh6.googleusercontent.com/hJOROZdy4_fF47z5s_AKDpBsnzzew9w4HgckuVfSNJWPIX_SoQSuLIMANgnNnPHZqxvje_xmc5OO64KRr03n4RIo8IRZ2Caxx8NZBekF2V83W7WTzFWSEYSoV1cfbxmCtC236XfR)

A better of what is currently inside Solr’s index can be viewed on the administrative backend by going to Components >> Search >> Search Index Tab. The number of index items is located to next to each type. Clicking on the name of the hub type will perform a search on that type, displaying all items that are within the index of that type.

![](https://lh5.googleusercontent.com/u4by9I73SQ4IDFHc-ogmOzSkulmFaxz0qkLE1mevDpgTYZI_4TLG4W2pnOouSD_BIkzpd4jqitrNgqYMT5CycVQELt8len5cahTeDVGjErKeI7dPQrfiChlDWa_ISD2F8cIEZ3qR)

For instance clicking “Resources” shows the following screen:

![](https://lh4.googleusercontent.com/Mm1jfhQ0Z-DKrqqBf_7cWlsKSSbKLUV_xPsEZrbMqpyOABUqJd5WRyuYEaJ-TqS56A3Tbgr_7S-GG8fUyfVq8PaTxuug71Qs3k6cLq7xgoAF5GCPHwdKdjGoDA3gbYUXvPjFza7V)

One can perform additional searching using the “Filter” bar on top of the results listing.
