<?cs
##################################################################
# Site CSS - Place custom CSS, including overriding styles here.
?>

hr { border: none;  border-top: 1px solid #bbc; margin: 2em 0; }
/* Link styles */
:link, :visited {
 color: #00b;
}
input[type=submit]:hover, input[type=reset]:hover { background: #bbc }
input[type=text]:focus, textarea:focus { border: 1px solid #668 }
#ctxtnav li li :link:hover, #ctxtnav li li :visited:hover {
 background: #aab;
}
#prefs {
 background: #f0f0f7;
 border: 1px outset #889;
}

/* Wiki */
a.missing:link,a.missing:visited { background: #f0f0fa; color: #889 }
.wikitoolbar :link:hover, .wikitoolbar :visited:hover {
 border: 1px solid #2bf;
}
table.listing thead { background: #f0f0f7 }
table.listing tbody tr:hover { background: #dde !important }

#content.error .message, div.system-message {
 background: #cdf;
 border: 2px solid #00d;
 color: #005;
}

/* Styles for the TracGuideToc wikimacro */
.wiki-toc {
 border: 1px outset #ccd;
 background: #ddf;
}
.wiki-toc .active { background: #99f; }

/* Browser */
h1 :link, h1 :visited { color: #00b }
h1 .first:link, h1 .first:visited { color: #889 }

/* Styles for the revision info in the file module */
#info {
 background: #f0f0f7;
}

/* General styles for the progress bars */
div.progress div { background: #babae0; height: 1.2em }

/* Styles for the roadmap view */
li.milestone .info h2 em { color: #00b; font-style: normal }

/* Timeline */
dt :link:hover, dt :visited:hover { background-color: #dde; color: #000 }
dt em {
  color: #00b;
}
dd {
 color: #667;
}
