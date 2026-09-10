<!--
status: rewritten
reviewed-against: 2.4-main @ f22290e4e4
reviewed: 2026-09-09
screenshots: ok
source: https://help.hubzero.org/documentation/240/managers/components/search/boosting
source-id: 3394
modified: 2019-09-11
-->
# Boosting

A boost moves one kind of matching result up or down the ranking. It does not
change which results are found, only their order.

> **Note:** Boosting is a Solr feature. On a hub running Basic search the
> **Boosts** tab is not reachable and the stored boosts have no effect.

## What can be boosted

The **Type** menu offers one entry for each resource type defined under
**Components > Resources**, plus **Citations**. Those are the only choices;
there is no boost for blog entries, groups, members, or the other indexed
types.

Behind the menu, a resource type becomes the Solr clause
`type:<the type>^<strength>` and **Citations** becomes
`hubtype:citation^<strength>`. Every boost is added to the query as a Solr
boost query, so the effect is cumulative with the field weights in
**Query Fields**.

## Creating a boost

1. Go to **Components > Search**.
2. Select the **Boosts** tab.

   ![The Boosts tab in the Search component's sub-navigation](../../media/boosting-boost-tab.png)

3. Select the **+** button in the toolbar.

   ![The Boosts list with the new-boost button highlighted](../../media/boosting-new-boost.png)

4. Choose a **Type**.

   ![The new boost form, with a Type menu and a Strength field](../../media/boosting-new-boost-form.png)

5. Enter a whole number for **Strength**.
   - A positive value moves matching results higher.
   - A negative value moves them lower.
   - Zero has no effect.
6. Save the boost with the tick button in the toolbar.

   ![The completed new boost form before saving](../../media/boosting-finalize-new-boost.png)

The new boost applies to the next search; nothing has to be re-indexed.

## Editing and removing

Selecting a row on the **Boosts** list opens it for editing. **Strength** can
be changed; **Type** is shown as a disabled field, because a boost is
identified by its type. To boost a different type, delete this boost and
create another. The edit screen's trash button deletes the boost outright —
there is no trashed state to recover it from.

Only one boost may exist per type. Saving a second one for a type that
already has a boost fails with *A boost already exists for …*.

## When boosts are ignored

Turning the **Tag Search Box** option on disables boosting altogether: the
query is built without any boost queries, and the **Boosts** screen shows the
notice *Query boosts are currently omitted because tag search is enabled*.
The boosts stay stored and take effect again when the option is turned off.
