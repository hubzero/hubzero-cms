<!--
status: imported
source: https://help.hubzero.org/documentation/240/webdevs/templates/socicons
source-id: 3514
modified: 2013-08-02
imported: 2026-09-09
-->
# Socicons

## Overview

In a single collection, Socicons is a pictographic language containing icons for some of the most popular social and web services such as Twitter, Facebook, and Google.

## Integration

The [open source](https://hubzero.org/download) package contains several bootstrap CSS files and fonts for inclusion in your template. Below is the necessary `@font-face` rules to start using Socicons.

```css
@font-face {
	font-family: 'Socicons';
	src: url('/media/system/css/fonts/socicons-webfont.eot');
	src: url('/media/system/css/fonts/socicons-webfont.eot?#iefix') format('embedded-opentype'),
		 url('/media/system/css/fonts/socicons-webfont.woff') format('woff'),
		 url('/media/system/css/fonts/socicons-webfont.ttf') format('truetype'),
		 url('/media/system/css/fonts/socicons-webfont.svg#SociconsRegular') format('svg');
	font-weight: normal;
	font-style: normal;
}
```

Socicons is relatively lightweight due to the limited number of icons available and can be either included in the stylesheet into your site template or on a per use basis (e.g., individual components).

## Use

There are two primary ways to use the font, both with advantages and disadvantages. The first, is to include the necessary HTML and unicode character directly into your markup.

The HTML:

```html
<a href="#"><span class="facebook">&#xf013;</span> facebook</a>
```

The CSS:

```css
.facebook {
    font-family: "Socicons"
}
```

The advantage here is greater browser compatibility. `@font-face` is supported by even Internet Explorer 6. The disadvantage, however, is that you now have to edit the HTML wherever you wish to insert an icon which could change depending upon the styling and theme of your template. That could quickly become a headache!

The alternative is to use the CSS pseudo-elements `:before` and `:after`. This takes a little more setup in your styles but offers greater flexibility and ease of change. Unfortunately, pseudo-elements are **not** supported in Internet Explorer 7 or older. There is, however, a solution which we'll get to in a moment.

The HTML:

```html
<a class="facebook" href="#">facebook</a>
```

The CSS:

```css
/* Note the :before pseudo-element */
small.facebook,  /* for IE 7, more on that below */
.facebook:before {
    font-family: "Socicons"
    content: "\\f013"; /* unicode characters must start with a backslash */
}
```

What about Internet Explorer 7?

```css
.facebook {
    *zoom:expression(this.runtimeStyle['zoom']='1', this.innerHTML='<small class="facebook">&#xf013;</small>' + this.innerHTML);
}
```

We use `<small>` in the example above since it's a relatively unused tag and lessens the potential for styling conflicts. It should be noted that over-use of this technique can slow down IE 7 as it has to process and dynamically include content into the page upon render.

## Icon List

- \\\\f002 Hub
- \\\\f001 Hub alt
- \\\\f006 Purdue
- \\\\f005 Purdue alt
- \\\\f013 Facebook
- \\\\f012 Facebook alt
- \\\\f026 Dropbox
- \\\\f025 Dropbox alt

- \\\\f011 Twitter
- \\\\f010 Twitter alt
- \\\\f019 Github
- \\\\f018 Github alt
- \\\\f024 PayPal
- \\\\f023 PayPal alt
- \\\\f02a eBay
- \\\\f029 eBay alt

- \\\\f017 LinkedIn
- \\\\f016 LinkedIn alt
- \\\\f01b Pinterest
- \\\\f01a Pinterest alt
- \\\\f022 Skype
- \\\\f021 Skype alt
- \\\\f028 Dribbble
- \\\\f027 Dribbble alt

- \\\\f02c Google
- \\\\f02b Google alt
- \\\\f015 Google+
- \\\\f014 Google+ alt
- \\\\f01d Vimeo
- \\\\f01e Vimeo alt
- \\\\f01f YouTube
- \\\\f01e YouTube alt
