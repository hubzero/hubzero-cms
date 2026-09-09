<!--
status: imported
source: https://help.hubzero.org/documentation/240/webdevs/conventions/csscodingstyles
source-id: 3434
modified: 2015-07-29
imported: 2026-09-09
-->
# CSS Coding Styles

## Terminology

Concise terminology used in these standards:

```css
selector {
    property: value;
}
```

## Selectors

Selectors should:

- be on a single line
- end in an opening brace
- be closed with a closing brace on a separate line

A blank line should be placed between each group, section, or block of multiple selectors of logically related styles.

Were appropriate, blocks of related styles should be commented to facilitate understanding of their use.

```css
/* Book Navigation */
    .book-navigation .page-next {
    }
    .book-navigation .page-previous {
    }

/* Book Forms */
    .book-admin-form {
        border: 1px solid #000;
    }
```

> **Note:** Indentation is optional but encouraged when commenting blocks of related styles.

### Multiple selectors

Multiple selectors should each be on a single line, with no space after each comma:

```css
#forum td.posts,
#forum td.topics,
#forum td.replies,
#forum td.pager {
}
```

## Properties

All properties should be on the following line after the opening brace. Each property should:

- be on its own line
- be indented one tab relative to the selector line
- have a colon immediately after (no spaces permitted) the property name
- have a single space after the property and before the property value
- end in a semi-colon

```css
#forum .description {
    color: #EFEFEF;
    font-size: 0.9em;
    margin: 0.5em;
}
```

### Multiple values

Where properties can have multiple values, each value should be separated with a space.

```
font-family: helvetica, sans-serif;
```
