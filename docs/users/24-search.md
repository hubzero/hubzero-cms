<!--
status: imported
source: https://help.hubzero.org/documentation/240/users/search
source-id: 3328
modified: 2016-08-30
imported: 2026-09-09
-->
# Search

## Overview

The HUBzero platform uses Solr as the main search engine. Solr is a highly configurable search engine with many different options that control its behavior. Solr’s default configuration comes with a ton of helpful features which provide a high level of productivity out-of-the-box.

Solr is not implemented in all areas of the Hub, however, it will be expanded throughout the upcoming releases. The foundation of the integration will allow for basic search functionalities. The following search plugins are currently available:

- Answers
- Citations
- Collections
- Content
- Courses
- Forum
- Groups
- Members (public profile and public fields)
- Projects
- Publications
- Resources
- Tools
- Wiki
- Knowledge Base

## Query Syntax

**Searching Fields:**

Solr supports matching within a particular field. This means that one can specify a term to search for within a particular area of a document.

Available Search Fields:

- title - filters search down to title results
- author - filters search down to author results
- description - filters search down to full-text results
- path - filters search down to related path results
- DOI - filters search if the object has a DOI issued

**Partial Words:**

The Hubzero implementation of Solr has not yet been tuned to understand complex phrases in the English language (or any other language for that matter). Therefore Solr sees the words “puppy” and “puppies” as two different concepts. Solr supports partial word matching through the use of the wildcard symbol, \*. To perform a search for both “puppies” and “puppy” the query will read “pupp\*”.

**Complex Queries:**

Solr comes with a query parser that understands Boolean search terms. This means that the keywords “OR” and “AND” can be used. When “OR” is used, Solr returns inclusive results. When “AND” is used, Solr returns exclusive results. For example, the query “cats OR dogs OR puppies OR pupp” will return any results that contain the words cats, dogs, puppies, or the partial word ‘pupp’.’.

To demonstrate the use of the keyword “AND”, consider the query of “cats AND dogs”. This means that Solr will only return the results that have both cats and dogs within the record. In this dataset, this was not a popular combination. More information about the Solr Query Syntax is available on their wiki: <https://solr.apache.org/guide/6_6/the-standard-query-parser.html>.

## Search Results

A search result listing contains a few portions:

1. Title - The title of the content Category - The plugin / facet of the data.
2. Date - The date (usually created date) associated with the record.
3. Author - Authors listed on the item.
4. Tags - Hubzero tags applied to the item.
5. Description - A snippet of all matching text in the record.
6. Path - The path to the original item.

## What is this "Feed" button?

Navigating to the **What's New** section of Resources or Knowledge Base, one can find a **Feed** button that spits out RSS feed. A user can take this feed and add it to their favorite page services or personal browser feed, depending on their use case. This way one can catch up on all the new resources and knowledge base articles that have been posted recently and stay on top of Hub activity.
