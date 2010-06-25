<?php
/**
 * $Id: finnish.php 41 2009-07-23 20:50:14Z guilleva $
 * $LastChangedDate: 2009-07-23 14:50:14 -0600 (jue, 23 jul 2009) $
 * $LastChangedBy: guilleva $
 * Xmap by Guillermo Vargas
 * A sitemap component for Joomla! CMS (http://www.joomla.org)
 * Author Website: http://joomla.vargas.co.cr
 * Project License: GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/

defined( '_JEXEC' ) or die( 'Direct Access to this location is not allowed.' );

if( !defined( 'JOOMAP_LANG' )) {
	define('JOOMAP_LANG', 1);

	// -- General ------------------------------------------------------------------
	define('_XMAP_CFG_OPTIONS', 'Ominaisuudet');
	define('_XMAP_CFG_CSS_CLASSNAME', 'CSS-luokkanimi');
	define('_XMAP_CFG_EXPAND_CATEGORIES', 'Laajenna alaryhmät');
	define('_XMAP_CFG_EXPAND_SECTIONS', 'Laajenna pääryhmät');
	define('_XMAP_CFG_SHOW_MENU_TITLES', 'Näytä valikko-otsikot');
	define('_XMAP_CFG_NUMBER_COLUMNS', 'Palstojen lukumäärä');
	define('_XMAP_EX_LINK', 'Merkitse ulkoiset linkit');
	define('_XMAP_CFG_CLICK_HERE', 'Paina tästä');
	define('_XMAP_CFG_GOOGLE_MAP', 'Google-sivukartta');
	define('_XMAP_EXCLUDE_MENU', 'Jätä pois valikoiden ID:t');
	define('_XMAP_TAB_DISPLAY', 'Näytä');
	define('_XMAP_TAB_MENUS', 'Valikot');
	define('_XMAP_CFG_WRITEABLE', 'Kirjoitettavissa');
	define('_XMAP_CFG_UNWRITEABLE', 'Kirjoitussuojattu');
	define('_XMAP_MSG_MAKE_UNWRITEABLE', 'Kirjoitussuojaa tallennuksen jälkeen');
	define('_XMAP_MSG_OVERRIDE_WRITE_PROTECTION', 'Yliaja kirjoitussuojaus tallennettaessa');
	define('_XMAP_GOOGLE_LINK', 'Googlelink');
	define('_XMAP_CFG_INCLUDE_LINK', 'Lisää linkki tekijän kotisivulle');

	// -- Tips ---------------------------------------------------------------------
	define('_XMAP_EXCLUDE_MENU_TIP', 'Määrittele ne valikko-ID:t joita et halua mukaan sivukarttaan.<br /><strong>HUOM!</strong><br />Erota ID:t pilkulla!');

	// -- Menus --------------------------------------------------------------------
	define('_XMAP_CFG_SET_ORDER', 'Aseta valikoiden katselujärjestys');
	define('_XMAP_CFG_MENU_SHOW', 'Näytä');
	define('_XMAP_CFG_MENU_REORDER', 'Järjestä uudelleen');
	define('_XMAP_CFG_MENU_ORDER', 'Järjestä');
	define('_XMAP_CFG_MENU_NAME', 'Valikon nimi');
	define('_XMAP_CFG_DISABLE', 'Paina estääksesi näkyminen');
	define('_XMAP_CFG_ENABLE', 'Paina salliaksesi näkyminen');
	define('_XMAP_SHOW', 'Näytä');
	define('_XMAP_NO_SHOW', 'Älä näytä');

	// -- Toolbar ------------------------------------------------------------------
	define('_XMAP_TOOLBAR_SAVE', 'Tallenna');
	define('_XMAP_TOOLBAR_CANCEL', 'Peru');

	// -- Errors -------------------------------------------------------------------
	define('_XMAP_ERR_NO_LANG', 'Kielitiedostoa \'%s\' ei löytynyt. Käytetään vakiokieltä: english<br />');
	define('_XMAP_ERR_CONF_SAVE', 'VIRHE: Asetuksia ei kyetty tallentamaan');
	define('_XMAP_ERR_NO_CREATE', 'VIRHE: Asetustaulua ei voitu luoda');
	define('_XMAP_ERR_NO_DEFAULT_SET', 'ERROR: Vakioasetuksia ei onnistuttu lisäämään');
	define('_XMAP_ERR_NO_PREV_BU', 'WARNING: Aikaisemman varmuuskopion poisto epäonnistui');
	define('_XMAP_ERR_NO_BACKUP', 'ERROR: Varmuuskopion tekeminen epäonnistui');
	define('_XMAP_ERR_NO_DROP_DB', 'ERROR: Asetustaulun poisto epäonnistui');
	define('_XMAP_ERR_NO_SETTINGS', 'ERROR: Asennusten lataaminen tietokannasta epäonnistui: <a href="%s">Luo asetustaulu uudelleen</a>');

	// -- Config -------------------------------------------------------------------
	define('_XMAP_MSG_SET_RESTORED', 'Asetukset palautettu');
	define('_XMAP_MSG_SET_BACKEDUP', 'Asetukset tallennettu');
	define('_XMAP_MSG_SET_DB_CREATED', 'Asetustaulu luotu');
	define('_XMAP_MSG_SET_DEF_INSERT', 'Oletusasetukset luotu');
	define('_XMAP_MSG_SET_DB_DROPPED', 'Xmap-taulut talletettu!');

	// -- CSS ----------------------------------------------------------------------
	define('_XMAP_CSS', 'Xmap CSS');
	define('_XMAP_CSS_EDIT', 'Muokkaa CSS-tyylitietoja');

	// -- Sitemap (Frontend) -------------------------------------------------------
	define('_XMAP_SHOW_AS_EXTERN_ALT', 'Linkki avautuu uuteen ikkunaan');

	// -- Added for Xmap -----------------------------------------------------------
	define('_XMAP_CFG_MENU_SHOW_HTML', 'Näytä sivustolla');
	define('_XMAP_CFG_MENU_SHOW_XML', 'Näytä XML-sivukartalla');
	define('_XMAP_CFG_MENU_PRIORITY', 'Tärkeä');
	define('_XMAP_CFG_MENU_CHANGEFREQ', 'Muuta aikaväliä');
	define('_XMAP_CFG_CHANGEFREQ_ALWAYS', 'Aina');
	define('_XMAP_CFG_CHANGEFREQ_HOURLY', 'Tunneittain');
	define('_XMAP_CFG_CHANGEFREQ_DAILY', 'Päivittäin');
	define('_XMAP_CFG_CHANGEFREQ_WEEKLY', 'Viikoittain');
	define('_XMAP_CFG_CHANGEFREQ_MONTHLY', 'Kuukausittain');
	define('_XMAP_CFG_CHANGEFREQ_YEARLY', 'Vuosittain');
	define('_XMAP_CFG_CHANGEFREQ_NEVER', 'Ei koskaan');

	define('_XMAP_TIT_SETTINGS_OF', '%s: asetukset');
	define('_XMAP_TAB_SITEMAPS', 'Sivukartat');
	define('_XMAP_MSG_NO_SITEMAPS', 'Ei luotuja sivukarttoja');
	define('_XMAP_MSG_NO_SITEMAP', 'Sivukartta ei ole saatavilla');
	define('_XMAP_MSG_LOADING_SETTINGS', 'Ladataan asetuksia...');
	define('_XMAP_MSG_ERROR_LOADING_SITEMAP', 'Virhe. Sivukartan lataaminen epäonnistui.');
	define('_XMAP_MSG_ERROR_SAVE_PROPERTY', 'Virhe. Sivukartan tietojen tallennus epäonnistui.');
	define('_XMAP_MSG_ERROR_CLEAN_CACHE', 'Virhe. Sivukartan välimuistin tyhjennys epäonnistui.');
	define('_XMAP_ERROR_DELETE_DEFAULT', 'Oletussivukartan poistaminen ei sallittua.');
	define('_XMAP_MSG_CACHE_CLEANED', 'Välimuisti tyhjennetty!');
	define('_XMAP_CHARSET', 'UTF-8');
	define('_XMAP_SITEMAP_ID', 'Sivukartan ID');
	define('_XMAP_ADD_SITEMAP', 'Lisää sivukartta');
	define('_XMAP_NAME_NEW_SITEMAP', 'Uusi sivukartta');
	define('_XMAP_DELETE_SITEMAP', 'Poista');
	define('_XMAP_SETTINGS_SITEMAP', 'Tiedot');
	define('_XMAP_COPY_SITEMAP', 'Kopioi');
	define('_XMAP_SITEMAP_SET_DEFAULT', 'Aseta oletukseksi');
	define('_XMAP_EDIT_MENU', 'Asetukset');
	define('_XMAP_DELETE_MENU', 'Poista');
	define('_XMAP_CLEAR_CACHE', 'Tyhjennä välimuisti');
	define('_XMAP_MOVEUP_MENU', 'Ylös');
	define('_XMAP_MOVEDOWN_MENU', 'Alas');
	define('_XMAP_ADD_MENU', 'Lisää valikko');
	define('_XMAP_COPY_OF', '%s:n kopio');
	define('_XMAP_INFO_LAST_VISIT', 'Viimeksi vierailtu');
	define('_XMAP_INFO_COUNT_VIEWS', 'Vierailuiden lukumäärä');
	define('_XMAP_INFO_TOTAL_LINKS', 'Linkkien lukumäärä');
	define('_XMAP_CFG_URLS', 'Sivukartan URL');
	define('_XMAP_XML_LINK_TIP', 'Kopioi linkit ja lähetä ne Googleen tai Yahooseen');
	define('_XMAP_HTML_LINK_TIP', 'Tämä on sivukartan osoite. Voit käyttä sitä luodaksesi uuden valikkokohdan.');
	define('_XMAP_CFG_XML_MAP', 'XML-sivukartta');
	define('_XMAP_CFG_HTML_MAP', 'HTML-sivukartta');
	define('_XMAP_XML_LINK', 'Googlelink');
	define('_XMAP_CFG_XML_MAP_TIP', 'XML-tiedosto hakukoneita varten');
	define('_XMAP_ADD', 'Tallenna');
	define('_XMAP_CANCEL', 'Peruuta');
	define('_XMAP_LOADING', 'Ladataan...');
	define('_XMAP_CACHE', 'Peruuta');
	define('_XMAP_USE_CACHE', 'Käytä välimuistia');
	define('_XMAP_CACHE_LIFE_TIME', 'Välimuistin kesto');
	define('_XMAP_NEVER_VISITED', 'Ei koskaan');

	// New on Xmap 1.1 beta 1
	define('_XMAP_PLUGINS', 'Laajennukset');
	define( '_XMAP_INSTALL_3PD_WARN', 'Varoitus: Kolmansien osapuolten laajennusten asentaminen saattaa vaarantaa palvelimesi turvallisuuden.');
	define('_XMAP_INSTALL_NEW_PLUGIN', 'Asenna uusi laajennus');
	define('_XMAP_UNKNOWN_AUTHOR', 'Tuntematon tekijä');
	define('_XMAP_PLUGIN_VERSION', 'Versio %s');
	define('_XMAP_TAB_INSTALL_PLUGIN', 'Asenna');
	define('_XMAP_TAB_EXTENSIONS', 'Laajennukset');
	define('_XMAP_TAB_INSTALLED_EXTENSIONS', 'Asennetut laajennukset');
	define('_XMAP_NO_PLUGINS_INSTALLED', 'Ei asennettuja laajennuksia');
	define('_XMAP_AUTHOR', 'Tekijä');
	define('_XMAP_CONFIRM_DELETE_SITEMAP', 'Oletko varma, että haluat poistaa tämän sivukartan?');
	define('_XMAP_CONFIRM_UNINSTALL_PLUGIN', 'Oletko varma, että haluat poistaa tämän laajennuksen?');
	define('_XMAP_UNINSTALL', 'Poista laajennus');
	define('_XMAP_EXT_PUBLISHED', 'Julkaistu');
	define('_XMAP_EXT_UNPUBLISHED', 'Julkaisematon');
	define('_XMAP_PLUGIN_OPTIONS', 'Asetukset');
	define('_XMAP_EXT_INSTALLED_MSG', 'Laajennus asennettu. Ole hyvä ja tarkista asetukset ennen sen julkaisemista.');
	define('_XMAP_CONTINUE', 'Jatka...');
	define('_XMAP_MSG_EXCLUDE_CSS_SITEMAP', 'Älä liitä CSS-tietoja sivukarttaan');
	define('_XMAP_MSG_EXCLUDE_XSL_SITEMAP', 'Käytä vanhanaikaista XML-sivukarttaa');

	// New on Xmap 1.1
	define('_XMAP_MSG_SELECT_FOLDER', 'Valitse hakemisto');
	define('_XMAP_UPLOAD_PKG_FILE', 'Siirrä tiedostopaketti');
	define('_XMAP_UPLOAD_AND_INSTALL', 'Siirrä ja asenna tiedosto');
	define('_XMAP_INSTALL_F_DIRECTORY', 'Asenna hakemistosta');
	define('_XMAP_INSTALL_DIRECTORY', 'Asennushakemisto');
	define('_XMAP_INSTALL', 'Asenna');
	define('_XMAP_WRITEABLE', 'Kirjoitettavissa');
	define('_XMAP_UNWRITEABLE', 'Kirjoitussuojattu');

	// New on Xmap 1.2
	define('_XMAP_COMPRESSION','Compression');
	define('_XMAP_USE_COMPRESSION','Compress the XML sitemap to save bandwidth');

        // New on Xmap 1.2.1
        define('_XMAP_CFG_NEWS_MAP',            'News Sitemap');
        define('_XMAP_NEWS_LINK_TIP',   'This is the news sitemap\'s URL.');

        // New on Xmap 1.2.2
       define('_XMAP_CFG_MENU_MODULE',            'Module');
       define('_XMAP_CFG_MENU_MODULE_TIP',            'Specify the module you use to show this menu in your site (Default: mod_mainmenu).');

        // New on Xmap 1.2.3
    define('_XMAP_TEXT',            'Link Text');
        define('_XMAP_TITLE',            'Link Title');
        define('_XMAP_LINK',            'Link URL');
        define('_XMAP_CSS_STYLE',            'CSS style');
        define('_XMAP_CSS_CLASS',            'CSS class');
        define('_XMAP_INVALID_SITEMAP',            'Invalid Sitemap');
        define('_XMAP_OK', 'Ok');
}
