<?php
/**
* @version		$Id: CHANGELOG.php 9961 2008-01-21 12:18:18Z willebil $
* @package		Joomla
* @copyright	Copyright (C) 2005 - 2008 Open Source Matters. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* Joomla! is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );
?>
1. Copyright and disclaimer
---------------------------
This application is opensource software released under the GPL.  Please
see source code and the LICENSE file


2. Changelog
------------
This is a non-exhaustive (but still near complete) changelog for
Joomla! 1.5, including beta and release candidate versions.
Our thanks to all those people who've contributed bug reports and
code fixes.


Legend:

* -> Security Fix
# -> Bug Fix
+ -> Addition
^ -> Change
- -> Removed
! -> Note

-------------------- 1.5.0 Stable Release [21-January-2008] ---------------------

21-Jan-2008 Rob Schley
 ^ Updated COPYRIGHT.php to reference the new, consolidated CREDITS.php
 + Added LICENSES.php which will hold full text versions of other licenses.

17-Jan-2008 Anthony Ferrara
 + [8987] [8986] Added 3 Language strings to com_user and com_installer's language files (Thanks JM)
 # [9285] Administrators not being able to edit their own profile or change password

16-Jan-2008 Anthony Ferrara
 # Fixed session issues with Invalid Token randomly appearing
 # Fixed [9255] Error with Pagination and SEF (Thanks Jenscski)

15-Jan-2008 Wilco Jansen
 + Added language af-ZA and ar-DZ

15-Jan-2008 Andrew Eddie
 ^ Encapsulated public/non-public token logic into JUtility::getToken

14-Jan-2008 Wilco Jansen
 # Fixed [8874] Apostrophes transformed in html entities for page titles
 # Fixed [8673] Wrong encoding for "login redirection url" in user login parameters
 ^ Changed fa-IR langiage pack
 + Added tr-TR langiage pack
 ! Patch for 8874 and 8673 provided by Kevin Devine

14-Jan-2008 Andrew Eddie
 # Fixed inconsistend SQL in backward compat file (#__core_acl_aro_sections.section_id renamed to #__core_acl_aro_sections.id)

13-Jan-2008 Anthony Ferrara
 * [8739] Block user issues in administrator fix
 * [topic,252372] Security fix in com_users
 # [9126] [8702] Fixes for imagepath problems in categories:w
 # Fixed language issues
 # Added default alias for all items in core

12-Jan-2008 Wilco Jansen
 # Fixed [9194] No _JEXEC check in bigdump causes information disclosure if called directly

12-Jan-2008 Ian MacLennan
 # Fixed SEF issue for com_newsfeeds.
 # Removed incorrect line endings from some language files.
 # Fixed issue with page cache caching tokens.

11-Jan-2008 Ian MacLennan
 # Fixed SEF issue for com_poll, com_wrapper and com_search

11-Jan-2008 Wilco Jansen
 # Fixed [9032] cannot upload image
 # Fixed [9161] Media Manager - uploads doesn't work with flash tool
 ! Patch provided by Kevin Devine, thanks Kevin!
 ^ Changes language files for hr-HR, lt-LT, ro-RO, ru-RU
 + Added language files for eu-ES, hi-IN

11-Jan-2008 Ian MacLennan
 # Fixed bug in search where small words were not being filtered out properly
 # Fixed problem in search with regex using too many resources (related to above)
 # Fixed [#8404] Incorrect highlighting of search terms (as a byproduct)

10-Jan-2008 Sam Moffatt
 # Fixed error in backlink migration plugin
 # Fixed error with category/section search in front end
 # Fixed error with weblink search in back end
 # Fixed error with Legacy SEF incorrectly returning 404 page not found error

09-Jan-2008 Andy Miller
 # Fixed issues with pillmenu in both LTR and RTL directions

09-Jan-2008 Ian MacLennan
 # Fixed issue with incorrect building of section links in content router

07-Jan-2008 Johan Janssens
 # Fixed issue with JApplication::route wrongly assuming no route was found if no request variables are
   being returned and throwing a 404.

07-Jan-2008 Andrew Eddie
 # Changed form tokens to display different public and logged in values

05-Jan-2008 Rob Schley
 # Refactored routers for com_contact, com_weblinks, com_polls, and com_newsfeeds to be more reliable
   at finding configurations and to prevent duplicate content URL issues.

05-Jan-2008 Louis Landry
 # Fixed [#8228] Empty categories don't display when the show empty category parameter is selected (proposed solution)
 # Fixed [#8301] Memory consumption problems in com_search
 # Fixed [#8432] Mod_polls Validation: JS Unterminated String Literal--problems with quote marks in alias
 # Fixed [#8532] alias fields on menus and com_pool is not correctly sanitized can break links when sef on and cause other errors

05-Jan-2008 Charl van Niekerk
 # Fixed pagination in backend com_weblinks (similar issue as [#8718])
 # Fixed division by zero in com_weblinks frontend and backend if limit = 0

05-Jan-2008 Anthony Ferrara
 # [#8663] File path issues in media manager for IE6 and IE7 (Thanks Jens-Christian Skibakk)
 # [#8452] Mediamanager in IE6 shows one item in each row (Thanks Michal Sobkowiak)
 ^ Fix for pt-PT installation translation file error (from Translation team)

05-Jan-2008 Mati Kochen
 + Added missing POLL string
 - Removed unnecessary "
 ^ fixed locales again
 # [topic,249218] notice when showing subtree with no active parent (thanks trevornorth)

05-Jan-2008 Wilco Jansen
 ^ Updated the installer language files (thanks Ole for providing, thanks translators for creating these files)
 # Fixed [9019] Content of entryfield 'Style' of 'Image' -> 'Appearances' are not saved in Article Editor (Thanks Bruce Scherzinger)
 ! Make sure to save the plugin properties once of the tinymce editor!

05-Jan-2008 Andrew Eddie
 * SECURITY - Hardened escaping of user supplied text strings used in LIKE queries
 ^ Added extra arguments to JDatabase::Quote and JDatabase::getEscaped to facilitate hardening queries
 # Fixed [#8988] Legacy commonhtml.php bug
 # Fixed missing token in offline page

04-Jan-2008 Charl van Niekerk
 # Fixed pagination in backend com_content (similar issue as [#8718])

04-Jan-2008 Louis Landry
 # Fixed JDate issue with server offsets and daylight savings time as well as GMT output

04-Jan-2008 Jui-Yu Tsai
 # Fixed com_messages manager reset filter

04-Jan-2008 Mati Kochen
 ^ [topic,249292] Minor Typos in Sample Data
 # [topic,249199] Added 404 if no Route was found

04-Jan-2008 Alan Langford
 ^ Removed conditionals in loader.php, to revisit after upcoming release.

03-Jan-2008 Jui-Yu Tsai
 # Fixed [#8615][topic,240577] mod_newsflash "Read more..." parameter issue
 # Fixed [topic,248718] com_search gives an error under Beez template
 # Fixed [topic,248716] Author and date in beez template

03-Jan-2008 Anthony Ferrara
 # Fixed untranslated string in timezones (Thanks Ercan �zkaya)

03-Jan-2008 Andrew Eddie
 # Added JHTML::_( 'form.token' ) and JRequest::checkToken to assist in preventing CSRF exploits

03-Jan-2008 Alan Langford
 ^ Added conditionals to JLoader, __autoload(), jimport() to aid unit testing.

02-Jan-2008 Mati Kochen
 ^ Added UTF locales to en_GB.xml (admin/installation/site)

02-Jan-2008 Andrew Eddie
 # Fixed CSRF exploits in com_installer

02-Jan-2008 Toby Patterson
 # Fixed problem with JDocumentRendererAtom encoding links resulting in invalid urls ( & to &amp; )

02-Jan-2008 Robin Muilwijk
 # Fixed [#8969] Mod_sections missing parameter + patch
 # Fixed [#8828] htaccess does not include rewrite for .htm

02-Jan-2008 Sam Moffatt
 # Fixed radio button selection in com_installer
 ^ Removed administration/media tag from module installer

01-Jan-2008 Chris Davenport
 ^ Local help files replaced by dummy files containing links to online help.

01-Jan-2008 Johan Janssens
 ^ Changed JHTML::_() to support variable prefixes, type can now be prefix.class.function

01-Jan-2008 Wilco Jansen
 ^ Added also front-end language defaulting, see also #8307

01-Jan-2008 Mati Kochen
 # [#8750] Fixed Base URL sent by reminder mail

01-Jan-2008 Sam Moffatt
 ! Welcome to 2008, a great new year for Joomla!
 ^ Updates to the installation system to better handle some situations
 ^ Renamed a variable in the Joomla authentication plugin to make more sense
 # Fixes to prevent against uninitialised variable access in various locations

31-Dec-2007 Mati Kochen
 ^ [topic,247978] Added More Articles string, with corresponding fixes in files
 # [#8935] wrong comparisson for categories

31-Dec-2007 Charl van Niekerk
 # Fixed [#8516] xmlrpc throws errors when using third party blog/content entry tools
 ^ Changed mod_breadcrumbs individual module include to "breadcrumb" position include in rhuk_milkyway and beez
 ^ Renamed "breadcrumbs" position to "breadcrumb" in rhuk_milkyway

31-Dec-2007 Johan Janssens
 + Added scope variable to JApplication

30-Dec-2007 Wilco Jansen
 # Fixed [8307] Local distribs can't define default admin language

30-Dec-2007 Charl van Niekerk
 # Fixed [#8718] Frontend com_weblinks pagination error

30-Dec-2007 Mati Kochen
 # [#8568] Applied proposed fixes
 # [#8797] Added string to com_installer
 # [#7549] type of uninstall not translated
 # [#8901] changed copyright to 2008

30-Dec-2007 Anthony Ferrara
 ^ [#8901] Update copyright date needed in all trunk files
 # [#8736] 'limit' form field ignored in com_search
 ^ Added Istanbul to the timezone listings (Thanks Ercan �zkaya)

29-Dec-2007 Andy Miller
 # Fixed issue with admin login button with Safari

29-Dec-2007 Hannes Papenberg
 # [#8688] fixed pagination in com_categories

29-Dec-2007 Johan Janssens
 + Added transliterate function to JLanguage
 ^ JFilterOutput::stringURLSafe now calls JLanguage::transliterate

29-Dec-2007 Anthony Ferrara
 # [#8690] javascript popup: url not found (images directory incorrect)

29-Dec-2007 Mati Kochen
 ^ change width from 1000px to 960px (khepri)
 # [#8873] added BROWSE string
 # [#8867] fixed (Today) string
 # [#8576] added UNINSTALLLANGPUBLISHEDALREADY to com_installer with the correct call

28-Dec-2007 Hannes Papenberg
 # Fixed [#8229] If Intro Text is set to hide and no Fulltext is available, Intro Text is used as the fulltext

27-Dec-2007 Wilco Jansen
 ! Forgotten to credit Zinho for supplying us with information about the csrf exploit that was fixed
   during PBF weekend. Thanks Zinho for you issue report.

27-Dec-2007 Chris Davenport
 ^ Removed/renamed redundant local help screens.

26-Dec-2007 Nur Aini Rakhmawati
# Fixed [#6111] New button act as Edit when multiply select in Menu Item Manager
# Fixed [t,223403] Warning menu manager standardization for cancel button

25-Dec-2007 Nur Aini Rakhmawati
 # Fixed [#8557] language typo and ordering languange list (Thanks to Ole Bang Ottosen)

24-Dec-2007 Anthony Ferrara
 # Fixed [#8754] issue with SEF plugin rewriting raw anchors (Thanks Jens-Christian Skibakk)

24-Dec-2007 Jui-Yu Tsai
 # Fixed [#8568] language typo

23-Dec-2007 Rob Schley
 # Fixed JRegistryFormatINI::objectToString() method to build proper arrays again. Thanks Ian for testing.
 # Fixed view cache handler not storing module buffer.
 # Fixed JDocumentHTML::getBuffer() so that you can access the entire document buffer.

23-Dec-2007 Nur Aini Rakhmawati
 # Fixed [#8168] Removed Redundant code in Published Section. Thanks Alaattin Kahramanlar

22-Dec-2007 Johan Janssens
 + Added $params parameter to JEditor::display function. This allows to programaticaly set or override
   the editor plugin parameters.

22-Dec-2007 Andrew Eddie
 ^ Moved article edit icon into the print|pdf|email area
 + Added type property to JAuthenticationResponse which is set to the successful authenication method
 ^ Split diff.sql into steps for RC's

21-Dec-2007 Mati Kochen
 ^ [topic,245507] Better Styling with double classes & easier RTL

21-Dec-2007 Anthony Ferrara
 # [#8678] [#8675] [#8648] [topic,245507] Fixed min-width CSS issue forcing scrollbars

21-Dec-2007 Andrew Eddie
 # Fixed [topic,245313] Fatal error in Menu Manager when editing an item
 ! Lots of cosmetic commits (remove trailing ?> tags at EOF, white space, etc)

20-Dec-2007 Jui-Yu Tsai
 # [topic,245322] fixed missing "s" at string for more than one unit

20-Dec-2007 Mickael Maison
 # [#7617] Untranslated error message during authentication

20-Dec-2007 Mati Kochen
 ^ [topic,244583] added $rows = $this->items, and replaced all instaces
 ^ [topic,244213] added limitation to the return pagination only when there is one
 ^ [topic,244895] added missing content display
 ^ [topic,245291] refactor more links to use ContentHelperRoute

20-Dec-2007 Ian MacLennan
 # Fixed Topic 245155 Category Content Filter missing default parameter values in model

20-Dec-2007 Sam Moffatt
 # [#8444] Testing migration script on install - Scripts not executing (added display of current max PHP upload)
 # [#8517] com_installer: Installing from nonexisting URL generates technical error message
 ! SERVER_CONNECT_FAILED language added to com_installer
 ! MAXIMUM UPLOAD SIZE and UPLOADFILESIZE added to installation language
 # [#8628] Extension installer fails to remove media files (proposed solution)
 # [#8573] Google stuff still present in com_search

20-Dec-2007 Andrew Eddie
 # Fixed [t,243324] PHP 4 incompatible syntax in ContentModelArchive::_getList
 # Fixed extra <span> in Content Archive items layout
 # Fixed [#8667] bug in JDate

19-Dec-2007 Ian MacLennan
 # Fixed Content Router swallows up layout (checks to see if it matches Itemid)

19-Dec-2007 Ian MacLennan
 # Fixed topic 244449 XMLRPC Search plugin doesn't work with weblinks search plugin published

-------------------- 1.5.0 Release Candidate 4 Released [19-December-2007] ---------------------
