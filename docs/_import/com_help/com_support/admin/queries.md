<!--
status: imported
source: core/components/com_support/admin/help/en-GB/queries.phtml
imported: 2026-09-09
-->
# Support: Queries

## Overview

The support ticket query builder allows users to create their own, custom queries for looking up tickets. Several default, commonly used queries are provided.

Default queries are generally divided into three groups:

- **Common (in ACL)**  
  Commonly run queries, such as 'Open tickets' or 'New tickets'. Query is built and filtered based on user's ACL entry.
- **Common (*not* in ACL)**  
  Commonly run queries, such as 'Open tickets' or 'New tickets'. Query is built and filtered to exclude data the user does not have access to. Typically, this means the user will only see tickets they submitted, are assigned to, or tickets assigned to a group they belong to.
- **Mine**  
  Commonly run queries, such as 'My open tickets' or 'My submitted tickets'. Queries are filtered to display only tickets associated to the logged in user.

Stats shown may also be filtered by group.

## Adding/Editing

Queries are constructed using the Query Builder interface, which allows for specifying "Any" or "All" set of rules that must be met. Sub-groups of rules may also be applied.
