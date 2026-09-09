<!--
status: imported
source: https://help.hubzero.org/documentation/240/webdevs/templates/fontcons
source-id: 3515
modified: 2012-10-19
imported: 2026-09-09
-->
# Fontcons

## Overview

In a single collection, Fontcons is a pictographic language designed for a full array of web-related actions and content. Although originally inspired by [Font Awesome](http://fortawesome.github.com/Font-Awesome/), we've heavily modified and added to the available icons; Fontcons brings over 250 icons for use in a package equivalent in file size to just one or two bitmapped icons!

## Integration

The [open source](https://hubzero.org/download) package contains several bootstrap CSS files for inclusion in your template. These stylesheets can be found in the web root's `/media/system/css` directory. Here, our attention is on `fontcons.css` which contains the necessary `@font-face` rules to start using Fontcons.

```css
@font-face {
	font-family: 'Fontcons';
	src: url('/media/system/css/fonts/fontcons-webfont.eot');
	src: url('/media/system/css/fonts/fontcons-webfont.eot?#iefix') format('embedded-opentype'),
		 url('/media/system/css/fonts/fontcons-webfont.woff') format('woff'),
		 url('/media/system/css/fonts/fontcons-webfont.ttf') format('truetype'),
		 url('/media/system/css/fonts/fontcons-webfont.svg#FontconsRegular') format('svg');
	font-weight: normal;
	font-style: normal;
}
```

While you can include Fontcons on a per use basis (e.g., individual components), due to it being relatively light-weight and several Hubzero components making use of it, we recommend including the stylesheet into your site template.

In the `<head>` of your template's html, reference the location to `fontcons.css`:

```html
<link rel="stylesheet" href="/media/system/css/fontcons.css" />
```

Or import fontcons.css into your site's CSS:

```css
/* Note: import rules MUST come first */
@import "/media/system/css/fontcons.css";

/* Other styles here */
```

> **Note:** A word of caution on using `@import`: Internet Explorer 8 and older will download stylesheets in sequence rather than in parallel. This can have effects on page speed and flashes of un-styled content before the CSS files have finished downloading. See Steve Souder's ["donâ€™t use @import"](http://www.stevesouders.com/blog/2009/04/09/dont-use-import/) for more details.

## Use

There are two primary ways to use the font, both with advantages and disadvantages. The first, is to include the necessary HTML and unicode character directly into your markup.

The HTML:

```html
<a href="#"><span class="edit">&#x270E;</span> edit</a>
```

The CSS:

```css
.edit {
    font-family: "Fontcons"
}
```

The advantage here is greater browser compatibility. `@font-face` is supported by even Internet Explorer 6. The disadvantage, however, is that you now have to edit the HTML wherever you wish to insert an icon which could change depending upon the styling and theme of your template. That could quickly become a headache!

The alternative is to use the CSS pseudo-elements `:before` and `:after`. This takes a little more setup in your styles but offers greater flexibility and ease of change. Unfortunately, pseudo-elements are **not** supported in Internet Explorer 7 or older. There is, however, a solution which we'll get to in a moment.

The HTML:

```html
<a class="edit" href="#">edit</a>
```

The CSS:

```css
/* Note the :before pseudo-element */
small.edit,  /* for IE 7, more on that below */
.edit:before {
    font-family: "Fontcons"
    content: "\\270E"; /* unicode characters must start with a backslash */
}
```

What about Internet Explorer 7?

```css
.edit {
    *zoom:expression(this.runtimeStyle['zoom']='1', this.innerHTML='<small class="edit">&#x270E;</small>' + this.innerHTML);
}
```

We use `<small>` in the example above since it's a relatively unused tag and lessens the potential for styling conflicts. It should be noted that over-use of this technique can slow down IE 7 as it has to process and dynamically include content into the page upon render.

## Icon List

- \\\\f000
- \\\\266B
- \\\\f002
- \\\\2709
- \\\\2665
- \\\\2605
- \\\\2606
- \\\\f007
- \\\\f008
- \\\\f009
- \\\\f00a
- \\\\f00b
- \\\\2714
- \\\\2716
- \\\\f00e
- \\\\f010
- \\\\f011
- \\\\f012
- \\\\2699
- \\\\f014
- \\\\2302
- \\\\f016
- \\\\f017
- \\\\2641
- \\\\f01e
- \\\\f018
- \\\\f019
- \\\\f01a
- \\\\f01b
- \\\\f01c
- \\\\f01d
- \\\\21BB
- \\\\f083
- \\\\f092
- \\\\f085
- \\\\f08e
- \\\\f08d
- \\\\f077
- \\\\23F0
- \\\\f071
- \\\\f081
- \\\\260E
- \\\\f056
- \\\\f067
- \\\\f062
- \\\\f044
- \\\\f061
- \\\\f069
- \\\\f07f
- \\\\f01f
- \\\\269B
- \\\\f09c
- \\\\f095
- \\\\f0a1
- \\\\f0a2
- \\\\f0a3
- \\\\f0ad
- \\\\f0ae
- \\\\f0b0
- \\\\f0b2
- \\\\f0e3
- \\\\f0d0
- \\\\f0ea

- \\\\f021
- \\\\f022
- \\\\f023
- \\\\2691
- \\\\f025
- \\\\f026
- \\\\f027
- \\\\f028
- \\\\f029
- \\\\f02a
- \\\\f02b
- \\\\f02c
- \\\\f02d
- \\\\f02e
- \\\\2399
- \\\\f030
- \\\\f031
- \\\\f032
- \\\\f033
- \\\\f034
- \\\\f035
- \\\\f036
- \\\\f037
- \\\\f038
- \\\\f039
- \\\\f03a
- \\\\f03b
- \\\\f03c
- \\\\f03d
- \\\\f03e
- \\\\f082
- \\\\2692
- \\\\25F7
- \\\\f080
- \\\\f084
- \\\\26DF
- \\\\f004
- \\\\26D3
- \\\\f00c
- \\\\237E
- \\\\f072
- \\\\231B
- \\\\f068
- \\\\f005
- \\\\f05c
- \\\\f054
- \\\\f063
- \\\\f053
- \\\\f07d
- \\\\f07e
- \\\\f05f
- \\\\f09a
- \\\\f08f
- \\\\f0a4
- \\\\f0a5
- \\\\f0a6
- \\\\f0a7
- \\\\f0ca
- \\\\f0cb
- \\\\f0cc
- \\\\f0cd
- \\\\f0ce
- \\\\f0db

- \\\\270E
- \\\\f041
- \\\\f043
- \\\\25D1
- \\\\270D
- \\\\f045
- \\\\2611
- \\\\f047
- \\\\21E4
- \\\\f049
- \\\\219E
- \\\\25B6
- \\\\f04c
- \\\\2588
- \\\\21A0
- \\\\21E4
- \\\\f049
- \\\\f052
- \\\\2039
- \\\\203A
- \\\\2295
- \\\\2296
- \\\\f057
- \\\\f058
- \\\\f059
- \\\\f05a
- \\\\f05b
- \\\\2297
- \\\\f05d
- \\\\2298
- \\\\f087
- \\\\f088
- \\\\f086
- \\\\f091
- \\\\f093
- \\\\270B
- \\\\f00d
- \\\\f08a
- \\\\f006
- \\\\f003
- \\\\f001
- \\\\f094
- \\\\f078
- \\\\f040
- \\\\f060
- \\\\f05e
- \\\\f08c
- \\\\f079
- \\\\f097
- \\\\f098
- \\\\f03f
- \\\\f096
- \\\\f09d
- \\\\f0a8
- \\\\f0a9
- \\\\f0aa
- \\\\f0ab
- \\\\f0b1
- \\\\f0c1
- \\\\f0c2
- \\\\f0c3
- \\\\2622
- \\\\2746

- \\\\2190
- \\\\2192
- \\\\2191
- \\\\2193
- \\\\f064
- \\\\f065
- \\\\f066
- \\\\271A
- \\\\2010
- \\\\273D
- \\\\f06b
- \\\\f06c
- \\\\f06d
- \\\\2601
- \\\\f046
- \\\\f06e
- \\\\f070
- \\\\26A0
- \\\\2757
- \\\\2708
- \\\\f073
- \\\\f074
- \\\\f075
- \\\\f0e5
- \\\\f0e6
- \\\\f02f
- \\\\2303
- \\\\2304
- \\\\267B
- \\\\f07a
- \\\\f07b
- \\\\f07c
- \\\\2195
- \\\\2194
- \\\\f076
- \\\\f090
- \\\\f08b
- \\\\f089
- \\\\2661
- \\\\26A1
- \\\\2702
- \\\\22EF
- \\\\f055
- \\\\f042
- \\\\2693
- \\\\275D
- \\\\275E
- \\\\f04a
- \\\\f048
- \\\\f04d
- \\\\f04e
- \\\\f06f
- \\\\f04f
- \\\\f09b
- \\\\f0a0
- \\\\f0d7
- \\\\f0d8
- \\\\f0d9
- \\\\f0da
- \\\\f0d6
- \\\\f0ea
- \\\\f0c5
