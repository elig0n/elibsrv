<?php

/*
 * This file is part of the elibsrv project.
 * Provides the output functions for HTML feeds.
 *
 * http://elibsrv.sourceforge.net
 *
 * Each output plugin file must contain a set of defined functions, as listed
 * below, where SUFFIX is the plugin's suffix (eg. "atom", "html"...):
 *
 * printheaders_SUFFIX(array $pageinfo)
 *   prints out all necessary headers for the given SUFFIX output plugin. this
 *   function is called once, at the start of every page. the $pageinfo array
 *   contains following fields:
 *     $pageinfo['title'] - the title of the page
 *     $pageinfo['id']    - a unique string identifying this page
 *     $pageinfo['self']  - the main script url (for self-referencing links)
 *
 * printnaventry_SUFFIX(array $nav)
 *  prints out a single 'navigation' entry. that is a menu entry that leads to
 *  another menu within the elibsrv interface. the $nav array contains
 *  following fields:
 *     $nav['title']  - title of the navigation entry
 *     $nav['url']    - target url
 *     $nav['icon']   - url to the icon, if any (can be empty)
 *
 * printaqentry_SUFFIX(array $meta)
 *   used by elibsrv to output an 'acquisition entry', that is an entry that
 *   will allow the user agent to fetch the ebook. it's fed with a $meta array
 *   that contains all ebook's metadata fetched by elibsrv:
 *     $meta['title']     - ebook's title
 *     $meta['author']    - ebook's author
 *     $meta['lang']      - ebook's language (two-letter country code like 'en', 'fr', etc)
 *     $meta['desc']      - ebook's description (also known as "summary")
 *     $meta['publisher'] - ebook's publisher
 *     $meta['pubdate']   - ebook's publication date
 *     $meta['catdate']   - date and time when the ebook has been cataloged
 *     $meta['moddate']   - date and time when the ebook has been last modified
 *     $meta['crc']       - CRC32 sum of the ebook file
 *     $meta['aqlink']    - acquisition link (can be used to fetch the actual ebook file)
 *     $meta['aqlinkmobi']- acquisition link to the mobi version of the file (if any)
 *     $meta['coverlink'] - cover link (can be used to fetch the ebook's cover image)
 *     $meta['thumblink'] - thumbnail link (can be used to fetch a thumbnail image of the cover)
 *     $meta['filename']  - the local filename of the ebook file
 *     $meta['filesize']  - the ebook's file size, in bytes
 *     $meta['format']    - ebook's file format ('epub', 'pdf')
 *
 * printtrailer_SUFFIX(array $info)
 *   used to output the answer's trailer data (if any). this is called once,
 *   at the end of every page. the $info array contains following fields:
 *     $info['version']  - the program's version number
 *     $info['homepage'] - the program's
 */


function printheaders_html(array $pageinfo) {
  header("content-type: text/html;charset=utf-8");
  echo "<!DOCTYPE html>\n";
  echo "<html>\n";
  echo "<head>\n";
  echo "  <title>" . htmlentities($pageinfo['title']) . "</title>\n";
  echo '  <link rel="shortcut icon" href="favicon.png">' . "\n";
  echo '  <meta name="viewport" content="width=device-width">' . "\n";
  echo '  <meta charset="utf-8">' . "\n";
  echo "  <style>\n";
  echo '    body { font-family: "Open Sans", "Helvetica", sans-serif; min-width: 0; }' . "\n";
  echo "    p { padding: 0 1em 0 1em; margin: 0.1em 0 0.1em 0; }\n";
  echo '    p.homesearch { border-bottom: 2px #ddd solid; padding-bottom: 1em; margin-bottom: 1em; }'. "\n";
  echo "    p.menutitle { background-color: #fdc; font-weight: bold; margin-bottom: 0.9em; }\n";
  echo "    p.acqimg { margin: 0.5em 0 0 0; padding: 0; }\n";
  echo "    p.acqlink { margin: -3.25em auto 1em 2.5em; }\n";
  echo "    p.trailer { margin: 1em 0 0 0; text-align: right; padding: 0 0.5em 0 0.5em; font-size: 0.8em; border-top: 2px #ddd solid; color: #777; }\n";
  echo "    a.trailer { color: inherit; text-decoration: none; }\n";
  echo "    img.acqlink { vertical-align: middle; height: 3.5em; max-width: 3.4em; border: 1px #888 solid; }\n";
  echo "    a { text-decoration: none; color: #008; }\n";
  echo "    span.author { color: #707070; }\n";
  echo "  </style>\n";
  echo "</head>\n";
  echo "<body>\n";
  echo '  <p class="menutitle">' . htmlentities($pageinfo['title']) . "</p>\n";
  // build the menu bar
  echo '  <form name="input" action="' . $pageinfo['self'] . '" method="get"><p class="homesearch"><a href="' . $pageinfo['self'] . '?f=html">Home</a> <input type="text" name="query"><input type="hidden" name="action" value="titles"><input type="hidden" name="f" value="html"><input type="submit" value="search"></p></form>' . "\n";
}


function printnaventry_html(array $nav) {
  $iconhtml = '';
  if (isset($nav['icon'])) $iconhtml = '<img src="' . $nav['icon'] . '" style="height: 2em; padding: 0 1em 0 0; vertical-align: middle;">';
  echo '  <p>' . $iconhtml . '<a href="' . $nav['url'] . '">' . htmlentities($nav['title']) . "</a></p>\n";
}


function printaqentry_html(array $meta) {
  echo '  <p class="acqimg"><a href="' . $meta['coverlink'] . '"><img src="' . $meta['thumblink'] . '" class="acqlink"></a></p>' . "\n";
  echo '  <p class="acqlink">' . htmlentities($meta['title']);
  // lang
  if (!empty($meta['lang'])) {
    echo ' <span class="author">' . htmlentities('(' . $meta['lang'] . ')') . '</span>';
  }
  // links
  echo ' <a href="' . $meta['aqlink'] . '">[' . strtoupper($meta['format']) . ']</a>';
  if (!empty($meta['aqlinkmobi'])) echo ' <a href="' . $meta['aqlinkmobi'] . '">[MOBI]</a>';
  // author
  echo '<br><span class="author">';
  if (! empty($meta['author'])) {
    echo htmlentities($meta['author']);
  } else {
    echo '&nbsp;';
  }
  echo "</span></p>\n";
}


function printtrailer_html(array $info) {
  echo '  <p class="trailer"><a href="' . $info['homepage'] . '" class="trailer">elibsrv version ' . $info['version'] . '</a></p>' . "\n";
  echo "</body>\n";
  echo "</html>\n";
}


?>
