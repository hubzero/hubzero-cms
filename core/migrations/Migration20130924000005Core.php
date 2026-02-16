<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;
use Hubzero\Database\Expression;

/**
 * Migration script for creating and populating new joomla extensions table
 *
*/
class Migration20130924000005Core extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if (!$schema->tableExists('#__extensions')) {
            $schema->createTable('#__extensions')
                ->integer('extension_id', ['autoIncrement' => true])
                ->string('name', 100)
                ->string('type', 20)
                ->string('element', 100)
                ->string('folder', 100)
                ->tinyInteger('client_id')
                ->tinyInteger('enabled')->default(1)
                ->unsignedInteger('access')->default(1)
                ->tinyInteger('protected')->default(0)
                ->text('manifest_cache')
                ->text('params')
                ->text('custom_data')
                ->text('system_data')
                ->unsignedInteger('checked_out')->default(0)
                ->datetime('checked_out_time')->default('0000-00-00 00:00:00')
                ->integer('ordering')->default(0)
                ->integer('state')->default(0)
                ->primaryKey('extension_id')
                ->index('element_clientid', ['element', 'client_id'])
                ->index('element_folder_clientid', ['element', 'folder', 'client_id'])
                ->index('extension', ['type', 'element', 'folder', 'client_id'])
                ->execute();

            // Components
            $columns = [
                'extension_id', 'name', 'type', 'element', 'folder', 'client_id', 'enabled',
                'access', 'protected', 'manifest_cache', 'params', 'custom_data', 'system_data',
                'checked_out', 'checked_out_time', 'ordering', 'state'
            ];

            $values = [
                // com_mailto (1)
                [
                    1,
                    'com_mailto',
                    'component',
                    'com_mailto',
                    '',
                    0,
                    1,
                    1,
                    1,
                    '',
                    '',
                    '',
                    '',
                    0,
                    '0000-00-00 00:00:00',
                    0,
                    0,
                ],
                // com_wrapper (2)
                [
                    2,
                    'com_wrapper',
                    'component',
                    'com_wrapper',
                    '',
                    0,
                    1,
                    1,
                    1,
                    '',
                    '',
                    '',
                    '',
                    0,
                    '0000-00-00 00:00:00',
                    0,
                    0,
                ],
                // com_admin (3)
                [
                    3,
                    'com_admin',
                    'component',
                    'com_admin',
                    '',
                    1,
                    1,
                    1,
                    1,
                    '',
                    '',
                    '',
                    '',
                    0,
                    '0000-00-00 00:00:00',
                    0,
                    0,
                ],
                // com_banners (4)
                [
                    4,
                    'com_banners',
                    'component',
                    'com_banners',
                    '',
                    1,
                    1,
                    1,
                    0,
                    '',
                    '{"purchase_type":"3","track_impressions":"0","track_clicks":"0","metakey_prefix":""}',
                    '',
                    '',
                    0,
                    '0000-00-00 00:00:00',
                    0,
                    0,
                ],
                // com_cache (5)
                [
                    5,
                    'com_cache',
                    'component',
                    'com_cache',
                    '',
                    1,
                    1,
                    1,
                    1,
                    '',
                    '',
                    '',
                    '',
                    0,
                    '0000-00-00 00:00:00',
                    0,
                    0,
                ],
                // com_categories (6)
                [
                    6,
                    'com_categories',
                    'component',
                    'com_categories',
                    '',
                    1,
                    1,
                    1,
                    1,
                    '',
                    '',
                    '',
                    '',
                    0,
                    '0000-00-00 00:00:00',
                    0,
                    0,
                ],
                // com_checkin (7)
                [
                    7,
                    'com_checkin',
                    'component',
                    'com_checkin',
                    '',
                    1,
                    1,
                    1,
                    1,
                    '',
                    '',
                    '',
                    '',
                    0,
                    '0000-00-00 00:00:00',
                    0,
                    0,
                ],
                // com_contact (8)
                [
                    8,
                    'com_contact',
                    'component',
                    'com_contact',
                    '',
                    1,
                    1,
                    1,
                    0,
                    '',
                    '{"show_contact_category":"hide","show_contact_list":"0",'
                        . '"presentation_style":"sliders","show_name":"1","show_position":"1",'
                        . '"show_email":"0","show_street_address":"1","show_suburb":"1","show_state":"1",'
                        . '"show_postcode":"1","show_country":"1","show_telephone":"1","show_mobile":"1",'
                        . '"show_fax":"1","show_webpage":"1","show_misc":"1","show_image":"1","image":"",'
                        . '"allow_vcard":"0","show_articles":"0","show_profile":"0","show_links":"0",'
                        . '"linka_name":"","linkb_name":"","linkc_name":"","linkd_name":"","linke_name":"",'
                        . '"contact_icons":"0","icon_address":"","icon_email":"","icon_telephone":"",'
                        . '"icon_mobile":"","icon_fax":"","icon_misc":"","show_headings":"1",'
                        . '"show_position_headings":"1","show_email_headings":"0",'
                        . '"show_telephone_headings":"1","show_mobile_headings":"0","show_fax_headings":"0",'
                        . '"allow_vcard_headings":"0","show_suburb_headings":"1","show_state_headings":"1",'
                        . '"show_country_headings":"1","show_email_form":"1","show_email_copy":"1",'
                        . '"banned_email":"","banned_subject":"","banned_text":"","validate_session":"1",'
                        . '"custom_reply":"0","redirect":"","show_category_crumb":"0","metakey":"",'
                        . '"metadesc":"","robots":"","author":"","rights":"","xreference":""}',
                    '',
                    '',
                    0,
                    '0000-00-00 00:00:00',
                    0,
                    0,
                ],
                // com_cpanel (9)
                [
                    9,
                    'com_cpanel',
                    'component',
                    'com_cpanel',
                    '',
                    1,
                    1,
                    1,
                    1,
                    '',
                    '',
                    '',
                    '',
                    0,
                    '0000-00-00 00:00:00',
                    0,
                    0,
                ],
                // com_installer (10)
                [
                    10,
                    'com_installer',
                    'component',
                    'com_installer',
                    '',
                    1,
                    1,
                    1,
                    1,
                    '',
                    '{}',
                    '',
                    '',
                    0,
                    '0000-00-00 00:00:00',
                    0,
                    0,
                ],
                // com_languages (11)
                [
                    11,
                    'com_languages',
                    'component',
                    'com_languages',
                    '',
                    1,
                    1,
                    1,
                    1,
                    '',
                    '{"administrator":"en-GB","site":"en-GB"}',
                    '',
                    '',
                    0,
                    '0000-00-00 00:00:00',
                    0,
                    0,
                ],
                // com_login (12)
                [
                    12,
                    'com_login',
                    'component',
                    'com_login',
                    '',
                    1,
                    1,
                    1,
                    1,
                    '',
                    '',
                    '',
                    '',
                    0,
                    '0000-00-00 00:00:00',
                    0,
                    0,
                ],
                // com_media (13)
                [
                    13,
                    'com_media',
                    'component',
                    'com_media',
                    '',
                    1,
                    1,
                    0,
                    1,
                    '',
                    '{"upload_extensions":"bmp,csv,doc,gif,ico,jpg,jpeg,odg,odp,ods,odt,pdf,png,ppt,'
                        . 'swf,txt,xcf,xls,BMP,CSV,DOC,GIF,ICO,JPG,JPEG,ODG,ODP,ODS,ODT,PDF,PNG,PPT,SWF,TXT,'
                        . 'XCF,XLS","upload_maxsize":"10","file_path":"images","image_path":"images",'
                        . '"restrict_uploads":"1","allowed_media_usergroup":"3","check_mime":"1",'
                        . '"image_extensions":"bmp,gif,jpg,png","ignore_extensions":"",'
                        . '"upload_mime":"image\/jpeg,image\/gif,image\/png,image\/bmp,'
                        . 'application\/x-shockwave-flash,application\/msword,application\/excel,'
                        . 'application\/pdf,application\/powerpoint,text\/plain,application\/x-zip",'
                        . '"upload_mime_illegal":"text\/html"}',
                    '',
                    '',
                    0,
                    '0000-00-00 00:00:00',
                    0,
                    0,
                ],
                // com_menus (14)
                [
                    14,
                    'com_menus',
                    'component',
                    'com_menus',
                    '',
                    1,
                    1,
                    1,
                    1,
                    '',
                    '{}',
                    '',
                    '',
                    0,
                    '0000-00-00 00:00:00',
                    0,
                    0,
                ],
                // com_messages (15)
                [
                    15,
                    'com_messages',
                    'component',
                    'com_messages',
                    '',
                    1,
                    1,
                    1,
                    1,
                    '',
                    '',
                    '',
                    '',
                    0,
                    '0000-00-00 00:00:00',
                    0,
                    0,
                ],
                // com_modules (16)
                [
                    16,
                    'com_modules',
                    'component',
                    'com_modules',
                    '',
                    1,
                    1,
                    1,
                    1,
                    '',
                    '{}',
                    '',
                    '',
                    0,
                    '0000-00-00 00:00:00',
                    0,
                    0,
                ],
                // com_newsfeeds (17)
                [
                    17,
                    'com_newsfeeds',
                    'component',
                    'com_newsfeeds',
                    '',
                    1,
                    1,
                    1,
                    0,
                    '',
                    '{"show_feed_image":"1","show_feed_description":"1","show_item_description":"1",'
                        . '"feed_word_count":"0","show_headings":"1","show_name":"1","show_articles":"0",'
                        . '"show_link":"1","show_description":"1","show_description_image":"1",'
                        . '"display_num":"","show_pagination_limit":"1","show_pagination":"1",'
                        . '"show_pagination_results":"1","show_cat_items":"1"}',
                    '',
                    '',
                    0,
                    '0000-00-00 00:00:00',
                    0,
                    0,
                ],
                // com_plugins (18)
                [
                    18,
                    'com_plugins',
                    'component',
                    'com_plugins',
                    '',
                    1,
                    1,
                    1,
                    1,
                    '',
                    '{}',
                    '',
                    '',
                    0,
                    '0000-00-00 00:00:00',
                    0,
                    0,
                ],
                // com_search (19)
                [
                    19,
                    'com_search',
                    'component',
                    'com_search',
                    '',
                    1,
                    1,
                    1,
                    1,
                    '',
                    '{"enabled":"0","show_date":"1"}',
                    '',
                    '',
                    0,
                    '0000-00-00 00:00:00',
                    0,
                    0,
                ],
                // com_templates (20)
                [
                    20,
                    'com_templates',
                    'component',
                    'com_templates',
                    '',
                    1,
                    1,
                    1,
                    1,
                    '',
                    '{}',
                    '',
                    '',
                    0,
                    '0000-00-00 00:00:00',
                    0,
                    0,
                ],
                // com_weblinks (21)
                [
                    21,
                    'com_weblinks',
                    'component',
                    'com_weblinks',
                    '',
                    1,
                    1,
                    1,
                    0,
                    '',
                    '{"show_comp_description":"1","comp_description":"","show_link_hits":"1",'
                        . '"show_link_description":"1","show_other_cats":"0","show_headings":"0",'
                        . '"show_numbers":"0","show_report":"1","count_clicks":"1","target":"0",'
                        . '"link_icons":""}',
                    '',
                    '',
                    0,
                    '0000-00-00 00:00:00',
                    0,
                    0,
                ],
                // com_content (22)
                [
                    22,
                    'com_content',
                    'component',
                    'com_content',
                    '',
                    1,
                    1,
                    0,
                    1,
                    '{"legacy":false,"name":"com_content","type":"component","creationDate":"April '
                        . '2006","author":"Joomla! Project","copyright":"(C) 2005 - 2013 Open Source '
                        . 'Matters. All rights reserved.\t","authorEmail":"admin@joomla.org",'
                        . '"authorUrl":"www.joomla.org","version":"1.7.0",'
                        . '"description":"COM_CONTENT_XML_DESCRIPTION","group":""}',
                    '{"article_layout":"_:default","show_title":"1","link_titles":"1",'
                        . '"show_intro":"1","show_category":"1","link_category":"1",'
                        . '"show_parent_category":"0","link_parent_category":"0","show_author":"1",'
                        . '"link_author":"0","show_create_date":"0","show_modify_date":"0",'
                        . '"show_publish_date":"1","show_item_navigation":"1","show_vote":"0",'
                        . '"show_readmore":"1","show_readmore_title":"1","readmore_limit":"100",'
                        . '"show_icons":"1","show_print_icon":"1","show_email_icon":"1","show_hits":"1",'
                        . '"show_noauth":"0","show_publishing_options":"1","show_article_options":"1",'
                        . '"show_urls_images_frontend":"0","show_urls_images_backend":"1","targeta":0,'
                        . '"targetb":0,"targetc":0,"float_intro":"left","float_fulltext":"left",'
                        . '"category_layout":"_:blog","show_category_title":"0","show_description":"0",'
                        . '"show_description_image":"0","maxLevel":"1","show_empty_categories":"0",'
                        . '"show_no_articles":"1","show_subcat_desc":"1","show_cat_num_articles":"0",'
                        . '"show_base_description":"1","maxLevelcat":"-1","show_empty_categories_cat":"0",'
                        . '"show_subcat_desc_cat":"1","show_cat_num_articles_cat":"1",'
                        . '"num_leading_articles":"1","num_intro_articles":"4","num_columns":"2",'
                        . '"num_links":"4","multi_column_order":"0","show_subcategory_content":"0",'
                        . '"show_pagination_limit":"1","filter_field":"hide","show_headings":"1",'
                        . '"list_show_date":"0","date_format":"","list_show_hits":"1",'
                        . '"list_show_author":"1","orderby_pri":"order","orderby_sec":"rdate",'
                        . '"order_date":"published","show_pagination":"2","show_pagination_results":"1",'
                        . '"show_feed_link":"1","feed_summary":"0"}',
                    '',
                    '',
                    0,
                    '0000-00-00 00:00:00',
                    0,
                    0,
                ],
                // com_config (23)
                [
                    23,
                    'com_config',
                    'component',
                    'com_config',
                    '',
                    1,
                    1,
                    0,
                    1,
                    '{"legacy":false,"name":"com_config","type":"component","creationDate":"April '
                        . '2006","author":"Joomla! Project","copyright":"(C) 2005 - 2013 Open Source '
                        . 'Matters. All rights reserved.\t","authorEmail":"admin@joomla.org",'
                        . '"authorUrl":"www.joomla.org","version":"1.7.0",'
                        . '"description":"COM_CONFIG_XML_DESCRIPTION","group":""}',
                    '{"filters":{"1":{"filter_type":"NH","filter_tags":"","filter_attributes":""},'
                        . '"6":{"filter_type":"BL","filter_tags":"","filter_attributes":""},'
                        . '"7":{"filter_type":"NONE","filter_tags":"","filter_attributes":""},'
                        . '"2":{"filter_type":"NH","filter_tags":"","filter_attributes":""},'
                        . '"3":{"filter_type":"BL","filter_tags":"","filter_attributes":""},'
                        . '"4":{"filter_type":"BL","filter_tags":"","filter_attributes":""},'
                        . '"5":{"filter_type":"BL","filter_tags":"","filter_attributes":""},'
                        . '"10":{"filter_type":"BL","filter_tags":"","filter_attributes":""},'
                        . '"12":{"filter_type":"BL","filter_tags":"","filter_attributes":""},'
                        . '"8":{"filter_type":"NONE","filter_tags":"","filter_attributes":""}}}',
                    '',
                    '',
                    0,
                    '0000-00-00 00:00:00',
                    0,
                    0,
                ],
                // com_redirect (24)
                [
                    24,
                    'com_redirect',
                    'component',
                    'com_redirect',
                    '',
                    1,
                    1,
                    0,
                    1,
                    '',
                    '{}',
                    '',
                    '',
                    0,
                    '0000-00-00 00:00:00',
                    0,
                    0,
                ],
                // com_users (25)
                [
                    25,
                    'com_users',
                    'component',
                    'com_users',
                    '',
                    1,
                    1,
                    0,
                    1,
                    '',
                    '{"allowUserRegistration":"1","new_usertype":"2","useractivation":"1",'
                        . '"frontend_userparams":"1","mailSubjectPrefix":"","mailBodySuffix":""}',
                    '',
                    '',
                    0,
                    '0000-00-00 00:00:00',
                    0,
                    0,
                ],
                // com_finder (27)
                [
                    27,
                    'com_finder',
                    'component',
                    'com_finder',
                    '',
                    1,
                    1,
                    0,
                    0,
                    '',
                    '{"show_description":"1","description_length":255,"allow_empty_query":"0",'
                        . '"show_url":"1","show_advanced":"1","expand_advanced":"0","show_date_filters":"0",'
                        . '"highlight_terms":"1","opensearch_name":"","opensearch_description":"",'
                        . '"batch_size":"50","memory_table_limit":30000,"title_multiplier":"1.7",'
                        . '"text_multiplier":"0.7","meta_multiplier":"1.2","path_multiplier":"2.0",'
                        . '"misc_multiplier":"0.3","stemmer":"snowball"}',
                    '',
                    '',
                    0,
                    '0000-00-00 00:00:00',
                    0,
                    0,
                ],
                // com_joomlaupdate (28)
                [
                    28,
                    'com_joomlaupdate',
                    'component',
                    'com_joomlaupdate',
                    '',
                    1,
                    1,
                    0,
                    1,
                    '{"legacy":false,"name":"com_joomlaupdate","type":"component",'
                        . '"creationDate":"February 2012","author":"Joomla! Project","copyright":"(C) 2005 '
                        . '- 2013 Open Source Matters. All rights reserved.",'
                        . '"authorEmail":"admin@joomla.org","authorUrl":"www.joomla.org","version":"2.5.0",'
                        . '"description":"COM_JOOMLAUPDATE_XML_DESCRIPTION","group":""}',
                    '{}',
                    '',
                    '',
                    0,
                    '0000-00-00 00:00:00',
                    0,
                    0,
                ],
            ];

            // Map values to associative arrays
            $rows = [];
            foreach ($values as $val) {
                $rows[] = array_combine($columns, $val);
            }

            $this->db->getQuery(true)
                ->insertMany('#__extensions', $rows);

            // Libraries
            // Libraries
            $columns = [
                'extension_id', 'name', 'type', 'element', 'folder', 'client_id', 'enabled',
                'access', 'protected', 'manifest_cache', 'params', 'custom_data', 'system_data',
                'checked_out', 'checked_out_time', 'ordering', 'state'
            ];

            $values = [
                // SimplePie (101)
                [
                    101,
                    'SimplePie',
                    'library',
                    'simplepie',
                    '',
                    0,
                    1,
                    1,
                    1,
                    '',
                    '',
                    '',
                    '',
                    0,
                    '0000-00-00 00:00:00',
                    0,
                    0,
                ],
                // Joomla! Platform (103)
                [
                    103,
                    'Joomla! Platform',
                    'library',
                    'joomla',
                    '',
                    0,
                    1,
                    1,
                    1,
                    '{"legacy":false,"name":"Joomla! Platform","type":"library","creationDate":"2008",'
                        . '"author":"Joomla! Project","copyright":"Copyright (C) 2005 - 2013 Open Source '
                        . 'Matters. All rights reserved.","authorEmail":"admin@joomla.org",'
                        . '"authorUrl":"http:\/\/www.joomla.org","version":"11.4",'
                        . '"description":"LIB_JOOMLA_XML_DESCRIPTION","group":""}',
                    '{}',
                    '',
                    '',
                    0,
                    '0000-00-00 00:00:00',
                    0,
                    0,
                ],
            ];

            // Map values to associative arrays
            $rows = [];
            foreach ($values as $val) {
                $rows[] = array_combine($columns, $val);
            }

            $this->db->getQuery(true)
                ->insertMany('#__extensions', $rows);

            // Site Modules
            $columns = [
                'extension_id', 'name', 'type', 'element', 'folder', 'client_id', 'enabled',
                'access', 'protected', 'manifest_cache', 'params', 'custom_data', 'system_data',
                'checked_out', 'checked_out_time', 'ordering', 'state'
            ];

            $values = [
                // mod_articles_archive (200)
                [
                    200,
                    'mod_articles_archive',
                    'module',
                    'mod_articles_archive',
                    '',
                    0,
                    1,
                    1,
                    1,
                    '',
                    '',
                    '',
                    '',
                    0,
                    '0000-00-00 00:00:00',
                    0,
                    0,
                ],
                // mod_articles_latest (201)
                [
                    201,
                    'mod_articles_latest',
                    'module',
                    'mod_articles_latest',
                    '',
                    0,
                    1,
                    1,
                    1,
                    '',
                    '',
                    '',
                    '',
                    0,
                    '0000-00-00 00:00:00',
                    0,
                    0,
                ],
                // mod_articles_popular (202)
                [
                    202,
                    'mod_articles_popular',
                    'module',
                    'mod_articles_popular',
                    '',
                    0,
                    1,
                    1,
                    0,
                    '',
                    '',
                    '',
                    '',
                    0,
                    '0000-00-00 00:00:00',
                    0,
                    0,
                ],
                // mod_banners (203)
                [
                    203,
                    'mod_banners',
                    'module',
                    'mod_banners',
                    '',
                    0,
                    1,
                    1,
                    0,
                    '',
                    '',
                    '',
                    '',
                    0,
                    '0000-00-00 00:00:00',
                    0,
                    0,
                ],
                // mod_breadcrumbs (204)
                [
                    204,
                    'mod_breadcrumbs',
                    'module',
                    'mod_breadcrumbs',
                    '',
                    0,
                    1,
                    1,
                    1,
                    '',
                    '',
                    '',
                    '',
                    0,
                    '0000-00-00 00:00:00',
                    0,
                    0,
                ],
                // mod_custom (205)
                [
                    205,
                    'mod_custom',
                    'module',
                    'mod_custom',
                    '',
                    0,
                    1,
                    1,
                    1,
                    '',
                    '',
                    '',
                    '',
                    0,
                    '0000-00-00 00:00:00',
                    0,
                    0,
                ],
                // mod_feed (206)
                [206, 'mod_feed', 'module', 'mod_feed', '', 0, 1, 1, 1, '', '', '', '', 0, '0000-00-00 00:00:00', 0, 0],
                // mod_footer (207)
                [
                    207,
                    'mod_footer',
                    'module',
                    'mod_footer',
                    '',
                    0,
                    1,
                    1,
                    1,
                    '',
                    '',
                    '',
                    '',
                    0,
                    '0000-00-00 00:00:00',
                    0,
                    0,
                ],
                // mod_login (208)
                [
                    208,
                    'mod_login',
                    'module',
                    'mod_login',
                    '',
                    0,
                    1,
                    1,
                    1,
                    '',
                    '',
                    '',
                    '',
                    0,
                    '0000-00-00 00:00:00',
                    0,
                    0,
                ],
                // mod_menu (209)
                [209, 'mod_menu', 'module', 'mod_menu', '', 0, 1, 1, 1, '', '', '', '', 0, '0000-00-00 00:00:00', 0, 0],
                // mod_articles_news (210)
                [
                    210,
                    'mod_articles_news',
                    'module',
                    'mod_articles_news',
                    '',
                    0,
                    1,
                    1,
                    0,
                    '',
                    '',
                    '',
                    '',
                    0,
                    '0000-00-00 00:00:00',
                    0,
                    0,
                ],
                // mod_random_image (211)
                [
                    211,
                    'mod_random_image',
                    'module',
                    'mod_random_image',
                    '',
                    0,
                    1,
                    1,
                    0,
                    '',
                    '',
                    '',
                    '',
                    0,
                    '0000-00-00 00:00:00',
                    0,
                    0,
                ],
                // mod_related_items (212)
                [
                    212,
                    'mod_related_items',
                    'module',
                    'mod_related_items',
                    '',
                    0,
                    1,
                    1,
                    0,
                    '',
                    '',
                    '',
                    '',
                    0,
                    '0000-00-00 00:00:00',
                    0,
                    0,
                ],
                // mod_search (213)
                [
                    213,
                    'mod_search',
                    'module',
                    'mod_search',
                    '',
                    0,
                    1,
                    1,
                    0,
                    '',
                    '',
                    '',
                    '',
                    0,
                    '0000-00-00 00:00:00',
                    0,
                    0,
                ],
                // mod_stats (214)
                [
                    214,
                    'mod_stats',
                    'module',
                    'mod_stats',
                    '',
                    0,
                    1,
                    1,
                    0,
                    '',
                    '',
                    '',
                    '',
                    0,
                    '0000-00-00 00:00:00',
                    0,
                    0,
                ],
                // mod_syndicate (215)
                [
                    215,
                    'mod_syndicate',
                    'module',
                    'mod_syndicate',
                    '',
                    0,
                    1,
                    1,
                    1,
                    '',
                    '',
                    '',
                    '',
                    0,
                    '0000-00-00 00:00:00',
                    0,
                    0,
                ],
                // mod_users_latest (216)
                [
                    216,
                    'mod_users_latest',
                    'module',
                    'mod_users_latest',
                    '',
                    0,
                    1,
                    1,
                    1,
                    '',
                    '',
                    '',
                    '',
                    0,
                    '0000-00-00 00:00:00',
                    0,
                    0,
                ],
                // mod_weblinks (217)
                [
                    217,
                    'mod_weblinks',
                    'module',
                    'mod_weblinks',
                    '',
                    0,
                    1,
                    1,
                    0,
                    '',
                    '',
                    '',
                    '',
                    0,
                    '0000-00-00 00:00:00',
                    0,
                    0,
                ],
                // mod_whosonline (218)
                [
                    218,
                    'mod_whosonline',
                    'module',
                    'mod_whosonline',
                    '',
                    0,
                    1,
                    1,
                    0,
                    '',
                    '',
                    '',
                    '',
                    0,
                    '0000-00-00 00:00:00',
                    0,
                    0,
                ],
                // mod_wrapper (219)
                [
                    219,
                    'mod_wrapper',
                    'module',
                    'mod_wrapper',
                    '',
                    0,
                    1,
                    1,
                    0,
                    '',
                    '',
                    '',
                    '',
                    0,
                    '0000-00-00 00:00:00',
                    0,
                    0,
                ],
                // mod_articles_category (220)
                [
                    220,
                    'mod_articles_category',
                    'module',
                    'mod_articles_category',
                    '',
                    0,
                    1,
                    1,
                    1,
                    '',
                    '',
                    '',
                    '',
                    0,
                    '0000-00-00 00:00:00',
                    0,
                    0,
                ],
                // mod_articles_categories (221)
                [
                    221,
                    'mod_articles_categories',
                    'module',
                    'mod_articles_categories',
                    '',
                    0,
                    1,
                    1,
                    1,
                    '',
                    '',
                    '',
                    '',
                    0,
                    '0000-00-00 00:00:00',
                    0,
                    0,
                ],
                // mod_languages (222)
                [
                    222,
                    'mod_languages',
                    'module',
                    'mod_languages',
                    '',
                    0,
                    1,
                    1,
                    1,
                    '',
                    '',
                    '',
                    '',
                    0,
                    '0000-00-00 00:00:00',
                    0,
                    0,
                ],
                // mod_finder (223)
                [
                    223,
                    'mod_finder',
                    'module',
                    'mod_finder',
                    '',
                    0,
                    1,
                    0,
                    0,
                    '',
                    '',
                    '',
                    '',
                    0,
                    '0000-00-00 00:00:00',
                    0,
                    0,
                ],
            ];

            // Map values to associative arrays
            $rows = [];
            foreach ($values as $val) {
                $rows[] = array_combine($columns, $val);
            }

            $this->db->getQuery(true)
                ->insertMany('#__extensions', $rows);

            // Administrator Modules
            $columns = [
                'extension_id', 'name', 'type', 'element', 'folder', 'client_id', 'enabled',
                'access', 'protected', 'manifest_cache', 'params', 'custom_data', 'system_data',
                'checked_out', 'checked_out_time', 'ordering', 'state'
            ];

            $values = [
                // mod_custom (300)
                [
                    300,
                    'mod_custom',
                    'module',
                    'mod_custom',
                    '',
                    1,
                    1,
                    1,
                    1,
                    '',
                    '',
                    '',
                    '',
                    0,
                    '0000-00-00 00:00:00',
                    0,
                    0,
                ],
                // mod_feed (301)
                [301, 'mod_feed', 'module', 'mod_feed', '', 1, 1, 1, 0, '', '', '', '', 0, '0000-00-00 00:00:00', 0, 0],
                // mod_latest (302)
                [
                    302,
                    'mod_latest',
                    'module',
                    'mod_latest',
                    '',
                    1,
                    1,
                    1,
                    0,
                    '',
                    '',
                    '',
                    '',
                    0,
                    '0000-00-00 00:00:00',
                    0,
                    0,
                ],
                // mod_logged (303)
                [
                    303,
                    'mod_logged',
                    'module',
                    'mod_logged',
                    '',
                    1,
                    1,
                    1,
                    0,
                    '',
                    '',
                    '',
                    '',
                    0,
                    '0000-00-00 00:00:00',
                    0,
                    0,
                ],
                // mod_login (304)
                [
                    304,
                    'mod_login',
                    'module',
                    'mod_login',
                    '',
                    1,
                    1,
                    1,
                    1,
                    '',
                    '',
                    '',
                    '',
                    0,
                    '0000-00-00 00:00:00',
                    0,
                    0,
                ],
                // mod_menu (305)
                [305, 'mod_menu', 'module', 'mod_menu', '', 1, 1, 1, 0, '', '', '', '', 0, '0000-00-00 00:00:00', 0, 0],
                // mod_popular (307)
                [
                    307,
                    'mod_popular',
                    'module',
                    'mod_popular',
                    '',
                    1,
                    1,
                    1,
                    0,
                    '',
                    '',
                    '',
                    '',
                    0,
                    '0000-00-00 00:00:00',
                    0,
                    0,
                ],
                // mod_quickicon (308)
                [
                    308,
                    'mod_quickicon',
                    'module',
                    'mod_quickicon',
                    '',
                    1,
                    1,
                    1,
                    1,
                    '',
                    '',
                    '',
                    '',
                    0,
                    '0000-00-00 00:00:00',
                    0,
                    0,
                ],
                // mod_status (309)
                [
                    309,
                    'mod_status',
                    'module',
                    'mod_status',
                    '',
                    1,
                    1,
                    1,
                    0,
                    '',
                    '',
                    '',
                    '',
                    0,
                    '0000-00-00 00:00:00',
                    0,
                    0,
                ],
                // mod_submenu (310)
                [
                    310,
                    'mod_submenu',
                    'module',
                    'mod_submenu',
                    '',
                    1,
                    1,
                    1,
                    0,
                    '',
                    '',
                    '',
                    '',
                    0,
                    '0000-00-00 00:00:00',
                    0,
                    0,
                ],
                // mod_title (311)
                [
                    311,
                    'mod_title',
                    'module',
                    'mod_title',
                    '',
                    1,
                    1,
                    1,
                    0,
                    '',
                    '',
                    '',
                    '',
                    0,
                    '0000-00-00 00:00:00',
                    0,
                    0,
                ],
                // mod_toolbar (312)
                [
                    312,
                    'mod_toolbar',
                    'module',
                    'mod_toolbar',
                    '',
                    1,
                    1,
                    1,
                    1,
                    '',
                    '',
                    '',
                    '',
                    0,
                    '0000-00-00 00:00:00',
                    0,
                    0,
                ],
                // mod_multilangstatus (313)
                [
                    313,
                    'mod_multilangstatus',
                    'module',
                    'mod_multilangstatus',
                    '',
                    1,
                    1,
                    1,
                    0,
                    '{"legacy":false,"name":"mod_multilangstatus","type":"module",'
                        . '"creationDate":"September 2011","author":"Joomla! Project",'
                        . '"copyright":"Copyright (C) 2005 - 2013 Open Source Matters. All rights '
                        . 'reserved.","authorEmail":"admin@joomla.org","authorUrl":"www.joomla.org",'
                        . '"version":"1.7.1","description":"MOD_MULTILANGSTATUS_XML_DESCRIPTION",'
                        . '"group":""}',
                    '{"cache":"0"}',
                    '',
                    '',
                    0,
                    '0000-00-00 00:00:00',
                    0,
                    0,
                ],
                // mod_version (314)
                [
                    314,
                    'mod_version',
                    'module',
                    'mod_version',
                    '',
                    1,
                    1,
                    1,
                    0,
                    '{"legacy":false,"name":"mod_version","type":"module","creationDate":"January '
                        . '2012","author":"Joomla! Project","copyright":"Copyright (C) 2005 - 2013 Open '
                        . 'Source Matters. All rights reserved.","authorEmail":"admin@joomla.org",'
                        . '"authorUrl":"www.joomla.org","version":"2.5.0",'
                        . '"description":"MOD_VERSION_XML_DESCRIPTION","group":""}',
                    '{"format":"short","product":"1","cache":"0"}',
                    '',
                    '',
                    0,
                    '0000-00-00 00:00:00',
                    0,
                    0,
                ],
            ];

            // Map values to associative arrays
            $rows = [];
            foreach ($values as $val) {
                $rows[] = array_combine($columns, $val);
            }

            $this->db->getQuery(true)
                ->insertMany('#__extensions', $rows);

            // Plugins
            $columns = [
                'extension_id', 'name', 'type', 'element', 'folder', 'client_id', 'enabled',
                'access', 'protected', 'manifest_cache', 'params', 'custom_data', 'system_data',
                'checked_out', 'checked_out_time', 'ordering', 'state'
            ];

            $values = [
                // plg_authentication_gmail (400)
                [
                    400,
                    'plg_authentication_gmail',
                    'plugin',
                    'gmail',
                    'authentication',
                    0,
                    0,
                    1,
                    0,
                    '',
                    '{"applysuffix":"0","suffix":"","verifypeer":"1","user_blacklist":""}',
                    '',
                    '',
                    0,
                    '0000-00-00 00:00:00',
                    1,
                    0,
                ],
                // plg_authentication_joomla (401)
                [
                    401,
                    'plg_authentication_joomla',
                    'plugin',
                    'joomla',
                    'authentication',
                    0,
                    1,
                    1,
                    1,
                    '',
                    '{}',
                    '',
                    '',
                    0,
                    '0000-00-00 00:00:00',
                    0,
                    0,
                ],
                // plg_authentication_ldap (402)
                [
                    402,
                    'plg_authentication_ldap',
                    'plugin',
                    'ldap',
                    'authentication',
                    0,
                    0,
                    1,
                    0,
                    '',
                    '{"host":"","port":"389","use_ldapV3":"0","negotiate_tls":"0","no_referrals":"0",'
                        . '"auth_method":"bind","base_dn":"","search_string":"","users_dn":"",'
                        . '"username":"admin","password":"bobby7","ldap_fullname":"fullName",'
                        . '"ldap_email":"mail","ldap_uid":"uid"}',
                    '',
                    '',
                    0,
                    '0000-00-00 00:00:00',
                    3,
                    0,
                ],
                // plg_content_emailcloak (404)
                [
                    404,
                    'plg_content_emailcloak',
                    'plugin',
                    'emailcloak',
                    'content',
                    0,
                    1,
                    1,
                    0,
                    '',
                    '{"mode":"1"}',
                    '',
                    '',
                    0,
                    '0000-00-00 00:00:00',
                    1,
                    0,
                ],
                // plg_content_geshi (405)
                [
                    405,
                    'plg_content_geshi',
                    'plugin',
                    'geshi',
                    'content',
                    0,
                    0,
                    1,
                    0,
                    '',
                    '{}',
                    '',
                    '',
                    0,
                    '0000-00-00 00:00:00',
                    2,
                    0,
                ],
                // plg_content_loadmodule (406)
                [
                    406,
                    'plg_content_loadmodule',
                    'plugin',
                    'loadmodule',
                    'content',
                    0,
                    1,
                    1,
                    0,
                    '{"legacy":false,"name":"plg_content_loadmodule","type":"plugin",'
                        . '"creationDate":"November 2005","author":"Joomla! Project","copyright":"Copyright '
                        . '(C) 2005 - 2013 Open Source Matters. All rights reserved.",'
                        . '"authorEmail":"admin@joomla.org","authorUrl":"www.joomla.org","version":"1.7.0",'
                        . '"description":"PLG_LOADMODULE_XML_DESCRIPTION","group":""}',
                    '{"style":"xhtml"}',
                    '',
                    '',
                    0,
                    '2011-09-18 15:22:50',
                    0,
                    0,
                ],
                // plg_content_pagebreak (407)
                [
                    407,
                    'plg_content_pagebreak',
                    'plugin',
                    'pagebreak',
                    'content',
                    0,
                    1,
                    1,
                    1,
                    '',
                    '{"title":"1","multipage_toc":"1","showall":"1"}',
                    '',
                    '',
                    0,
                    '0000-00-00 00:00:00',
                    4,
                    0,
                ],
                // plg_content_pagenavigation (408)
                [
                    408,
                    'plg_content_pagenavigation',
                    'plugin',
                    'pagenavigation',
                    'content',
                    0,
                    1,
                    1,
                    1,
                    '',
                    '{"position":"1"}',
                    '',
                    '',
                    0,
                    '0000-00-00 00:00:00',
                    5,
                    0,
                ],
                // plg_content_vote (409)
                [
                    409,
                    'plg_content_vote',
                    'plugin',
                    'vote',
                    'content',
                    0,
                    1,
                    1,
                    1,
                    '',
                    '{}',
                    '',
                    '',
                    0,
                    '0000-00-00 00:00:00',
                    6,
                    0,
                ],
                // plg_editors_codemirror (410)
                [
                    410,
                    'plg_editors_codemirror',
                    'plugin',
                    'codemirror',
                    'editors',
                    0,
                    1,
                    1,
                    1,
                    '',
                    '{"linenumbers":"0","tabmode":"indent"}',
                    '',
                    '',
                    0,
                    '0000-00-00 00:00:00',
                    1,
                    0,
                ],
                // plg_editors_none (411)
                [
                    411,
                    'plg_editors_none',
                    'plugin',
                    'none',
                    'editors',
                    0,
                    1,
                    1,
                    1,
                    '',
                    '{}',
                    '',
                    '',
                    0,
                    '0000-00-00 00:00:00',
                    2,
                    0,
                ],
                // plg_editors_tinymce (412)
                [
                    412,
                    'plg_editors_tinymce',
                    'plugin',
                    'tinymce',
                    'editors',
                    0,
                    1,
                    1,
                    1,
                    '{"legacy":false,"name":"plg_editors_tinymce","type":"plugin",'
                        . '"creationDate":"2005-2011","author":"Moxiecode Systems AB",'
                        . '"copyright":"Moxiecode Systems AB","authorEmail":"N\/A",'
                        . '"authorUrl":"tinymce.moxiecode.com\/","version":"3.4.7",'
                        . '"description":"PLG_TINY_XML_DESCRIPTION","group":""}',
                    '{"mode":"1","skin":"0","entity_encoding":"raw","lang_mode":"0","lang_code":"en",'
                        . '"text_direction":"ltr","content_css":"1","content_css_custom":"",'
                        . '"relative_urls":"1","newlines":"0","invalid_elements":"script,applet,iframe",'
                        . '"extended_elements":"","toolbar":"top","toolbar_align":"left",'
                        . '"html_height":"550","html_width":"750","resizing":"true",'
                        . '"resize_horizontal":"false","element_path":"1","fonts":"1","paste":"1",'
                        . '"searchreplace":"1","insertdate":"1","format_date":"%Y-%m-%d","inserttime":"1",'
                        . '"format_time":"%H:%M:%S","insert_params":"1","toolbar1":"bold,italic,underline,'
                        . 'strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,styleselect,'
                        . 'formatselect,fontselect,fontsizeselect","toolbar2":"cut,copy,paste,pastetext,'
                        . 'pasteword,|,search,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,'
                        . 'redo,|,link,unlink,anchor,image,cleanup,help,code,|,insertdate,inserttime,'
                        . 'preview,|,forecolor,backcolor","toolbar3":"tablecontrols,|,hr,removeformat,'
                        . 'visualaid,|,sub,sup,|,charmap,emotions,iespell,media,advhr,|,print,|,ltr,rtl,|,'
                        . 'fullscreen","toolbar4":"","custom_plugin1":"searchreplace,insertdatetime,'
                        . 'preview","custom_plugin2":"table,advhr,advimage,advlink,emotions,iespell,'
                        . 'inlinepopups,media,print,contextmenu,paste,fullscreen,nonbreaking,visualchars,'
                        . 'wordcount,advlist","custom_plugin3":"","custom_plugin4":"","custom_button1":"",'
                        . '"custom_button2":"","custom_button3":"","custom_button4":""}',
                    '',
                    '',
                    0,
                    '0000-00-00 00:00:00',
                    0,
                    0,
                ],
                // plg_editors-xtd_article (413)
                [
                    413,
                    'plg_editors-xtd_article',
                    'plugin',
                    'article',
                    'editors-xtd',
                    0,
                    1,
                    1,
                    1,
                    '',
                    '{}',
                    '',
                    '',
                    0,
                    '0000-00-00 00:00:00',
                    1,
                    0,
                ],
                // plg_editors-xtd_image (414)
                [
                    414,
                    'plg_editors-xtd_image',
                    'plugin',
                    'image',
                    'editors-xtd',
                    0,
                    1,
                    1,
                    0,
                    '',
                    '{}',
                    '',
                    '',
                    0,
                    '0000-00-00 00:00:00',
                    2,
                    0,
                ],
                // plg_editors-xtd_pagebreak (415)
                [
                    415,
                    'plg_editors-xtd_pagebreak',
                    'plugin',
                    'pagebreak',
                    'editors-xtd',
                    0,
                    1,
                    1,
                    0,
                    '',
                    '{}',
                    '',
                    '',
                    0,
                    '0000-00-00 00:00:00',
                    3,
                    0,
                ],
                // plg_editors-xtd_readmore (416)
                [
                    416,
                    'plg_editors-xtd_readmore',
                    'plugin',
                    'readmore',
                    'editors-xtd',
                    0,
                    1,
                    1,
                    0,
                    '',
                    '{}',
                    '',
                    '',
                    0,
                    '0000-00-00 00:00:00',
                    4,
                    0,
                ],
                // plg_search_categories (417)
                [
                    417,
                    'plg_search_categories',
                    'plugin',
                    'categories',
                    'search',
                    0,
                    1,
                    1,
                    0,
                    '',
                    '{"search_limit":"50","search_content":"1","search_archived":"1"}',
                    '',
                    '',
                    0,
                    '0000-00-00 00:00:00',
                    0,
                    0,
                ],
                // plg_search_contacts (418)
                [
                    418,
                    'plg_search_contacts',
                    'plugin',
                    'contacts',
                    'search',
                    0,
                    1,
                    1,
                    0,
                    '',
                    '{"search_limit":"50","search_content":"1","search_archived":"1"}',
                    '',
                    '',
                    0,
                    '0000-00-00 00:00:00',
                    0,
                    0,
                ],
                // plg_search_content (419)
                [
                    419,
                    'plg_search_content',
                    'plugin',
                    'content',
                    'search',
                    0,
                    1,
                    1,
                    0,
                    '',
                    '{"search_limit":"50","search_content":"1","search_archived":"1"}',
                    '',
                    '',
                    0,
                    '0000-00-00 00:00:00',
                    0,
                    0,
                ],
                // plg_search_newsfeeds (420)
                [
                    420,
                    'plg_search_newsfeeds',
                    'plugin',
                    'newsfeeds',
                    'search',
                    0,
                    1,
                    1,
                    0,
                    '',
                    '{"search_limit":"50","search_content":"1","search_archived":"1"}',
                    '',
                    '',
                    0,
                    '0000-00-00 00:00:00',
                    0,
                    0,
                ],
                // plg_search_weblinks (421)
                [
                    421,
                    'plg_search_weblinks',
                    'plugin',
                    'weblinks',
                    'search',
                    0,
                    1,
                    1,
                    0,
                    '',
                    '{"search_limit":"50","search_content":"1","search_archived":"1"}',
                    '',
                    '',
                    0,
                    '0000-00-00 00:00:00',
                    0,
                    0,
                ],
                // plg_system_languagefilter (422)
                [
                    422,
                    'plg_system_languagefilter',
                    'plugin',
                    'languagefilter',
                    'system',
                    0,
                    0,
                    1,
                    1,
                    '',
                    '{}',
                    '',
                    '',
                    0,
                    '0000-00-00 00:00:00',
                    1,
                    0,
                ],
                // plg_system_p3p (423)
                [
                    423,
                    'plg_system_p3p',
                    'plugin',
                    'p3p',
                    'system',
                    0,
                    1,
                    1,
                    1,
                    '',
                    '{"headers":"NOI ADM DEV PSAi COM NAV OUR OTRo STP IND DEM"}',
                    '',
                    '',
                    0,
                    '0000-00-00 00:00:00',
                    2,
                    0,
                ],
                // plg_system_cache (424)
                [
                    424,
                    'plg_system_cache',
                    'plugin',
                    'cache',
                    'system',
                    0,
                    0,
                    1,
                    1,
                    '',
                    '{"browsercache":"0","cachetime":"15"}',
                    '',
                    '',
                    0,
                    '0000-00-00 00:00:00',
                    9,
                    0,
                ],
                // plg_system_debug (425)
                [
                    425,
                    'plg_system_debug',
                    'plugin',
                    'debug',
                    'system',
                    0,
                    1,
                    1,
                    0,
                    '',
                    '{"profile":"1","queries":"1","memory":"1","language_files":"1",'
                        . '"language_strings":"1","strip-first":"1","strip-prefix":"","strip-suffix":""}',
                    '',
                    '',
                    0,
                    '0000-00-00 00:00:00',
                    4,
                    0,
                ],
                // plg_system_log (426)
                [
                    426,
                    'plg_system_log',
                    'plugin',
                    'log',
                    'system',
                    0,
                    1,
                    1,
                    1,
                    '',
                    '{}',
                    '',
                    '',
                    0,
                    '0000-00-00 00:00:00',
                    5,
                    0,
                ],
                // plg_system_redirect (427)
                [
                    427,
                    'plg_system_redirect',
                    'plugin',
                    'redirect',
                    'system',
                    0,
                    1,
                    1,
                    1,
                    '',
                    '{}',
                    '',
                    '',
                    0,
                    '0000-00-00 00:00:00',
                    6,
                    0,
                ],
                // plg_system_remember (428)
                [
                    428,
                    'plg_system_remember',
                    'plugin',
                    'remember',
                    'system',
                    0,
                    1,
                    1,
                    1,
                    '',
                    '{}',
                    '',
                    '',
                    0,
                    '0000-00-00 00:00:00',
                    7,
                    0,
                ],
                // plg_system_sef (429)
                [
                    429,
                    'plg_system_sef',
                    'plugin',
                    'sef',
                    'system',
                    0,
                    1,
                    1,
                    0,
                    '',
                    '{}',
                    '',
                    '',
                    0,
                    '0000-00-00 00:00:00',
                    8,
                    0,
                ],
                // plg_system_logout (430)
                [
                    430,
                    'plg_system_logout',
                    'plugin',
                    'logout',
                    'system',
                    0,
                    1,
                    1,
                    1,
                    '',
                    '{}',
                    '',
                    '',
                    0,
                    '0000-00-00 00:00:00',
                    3,
                    0,
                ],
                // plg_user_contactcreator (431)
                [
                    431,
                    'plg_user_contactcreator',
                    'plugin',
                    'contactcreator',
                    'user',
                    0,
                    0,
                    1,
                    1,
                    '',
                    '{"autowebpage":"","category":"34","autopublish":"0"}',
                    '',
                    '',
                    0,
                    '0000-00-00 00:00:00',
                    1,
                    0,
                ],
                // plg_user_joomla (432)
                [
                    432,
                    'plg_user_joomla',
                    'plugin',
                    'joomla',
                    'user',
                    0,
                    1,
                    1,
                    0,
                    '',
                    '{"autoregister":"1"}',
                    '',
                    '',
                    0,
                    '0000-00-00 00:00:00',
                    2,
                    0,
                ],
                // plg_user_profile (433)
                [
                    433,
                    'plg_user_profile',
                    'plugin',
                    'profile',
                    'user',
                    0,
                    0,
                    1,
                    1,
                    '',
                    '{"register-require_address1":"1","register-require_address2":"1",'
                        . '"register-require_city":"1","register-require_region":"1",'
                        . '"register-require_country":"1","register-require_postal_code":"1",'
                        . '"register-require_phone":"1","register-require_website":"1",'
                        . '"register-require_favoritebook":"1","register-require_aboutme":"1",'
                        . '"register-require_tos":"1","register-require_dob":"1",'
                        . '"profile-require_address1":"1","profile-require_address2":"1",'
                        . '"profile-require_city":"1","profile-require_region":"1",'
                        . '"profile-require_country":"1","profile-require_postal_code":"1",'
                        . '"profile-require_phone":"1","profile-require_website":"1",'
                        . '"profile-require_favoritebook":"1","profile-require_aboutme":"1",'
                        . '"profile-require_tos":"1","profile-require_dob":"1"}',
                    '',
                    '',
                    0,
                    '0000-00-00 00:00:00',
                    0,
                    0,
                ],
                // plg_extension_joomla (434)
                [
                    434,
                    'plg_extension_joomla',
                    'plugin',
                    'joomla',
                    'extension',
                    0,
                    1,
                    1,
                    1,
                    '',
                    '{}',
                    '',
                    '',
                    0,
                    '0000-00-00 00:00:00',
                    1,
                    0,
                ],
                // plg_content_joomla (435)
                [
                    435,
                    'plg_content_joomla',
                    'plugin',
                    'joomla',
                    'content',
                    0,
                    1,
                    1,
                    0,
                    '',
                    '{}',
                    '',
                    '',
                    0,
                    '0000-00-00 00:00:00',
                    0,
                    0,
                ],
                // plg_system_languagecode (436)
                [
                    436,
                    'plg_system_languagecode',
                    'plugin',
                    'languagecode',
                    'system',
                    0,
                    0,
                    1,
                    0,
                    '',
                    '{}',
                    '',
                    '',
                    0,
                    '0000-00-00 00:00:00',
                    10,
                    0,
                ],
                // plg_quickicon_joomlaupdate (437)
                [
                    437,
                    'plg_quickicon_joomlaupdate',
                    'plugin',
                    'joomlaupdate',
                    'quickicon',
                    0,
                    1,
                    1,
                    1,
                    '',
                    '{}',
                    '',
                    '',
                    0,
                    '0000-00-00 00:00:00',
                    0,
                    0,
                ],
                // plg_quickicon_extensionupdate (438)
                [
                    438,
                    'plg_quickicon_extensionupdate',
                    'plugin',
                    'extensionupdate',
                    'quickicon',
                    0,
                    1,
                    1,
                    1,
                    '',
                    '{}',
                    '',
                    '',
                    0,
                    '0000-00-00 00:00:00',
                    0,
                    0,
                ],
                // plg_captcha_recaptcha (439)
                [
                    439,
                    'plg_captcha_recaptcha',
                    'plugin',
                    'recaptcha',
                    'captcha',
                    0,
                    1,
                    1,
                    0,
                    '{}',
                    '{"public_key":"","private_key":"","theme":"clean"}',
                    '',
                    '',
                    0,
                    '0000-00-00 00:00:00',
                    0,
                    0,
                ],
                // plg_system_highlight (440)
                [
                    440,
                    'plg_system_highlight',
                    'plugin',
                    'highlight',
                    'system',
                    0,
                    1,
                    1,
                    0,
                    '',
                    '{}',
                    '',
                    '',
                    0,
                    '0000-00-00 00:00:00',
                    7,
                    0,
                ],
                // plg_content_finder (441)
                [
                    441,
                    'plg_content_finder',
                    'plugin',
                    'finder',
                    'content',
                    0,
                    0,
                    1,
                    0,
                    '{"legacy":false,"name":"plg_content_finder","type":"plugin",'
                        . '"creationDate":"December 2011","author":"Joomla! Project","copyright":"Copyright '
                        . '(C) 2005 - 2013 Open Source Matters. All rights reserved.",'
                        . '"authorEmail":"admin@joomla.org","authorUrl":"www.joomla.org","version":"1.7.0",'
                        . '"description":"PLG_CONTENT_FINDER_XML_DESCRIPTION","group":""}',
                    '{}',
                    '',
                    '',
                    0,
                    '0000-00-00 00:00:00',
                    0,
                    0,
                ],
                // plg_finder_categories (442)
                [
                    442,
                    'plg_finder_categories',
                    'plugin',
                    'categories',
                    'finder',
                    0,
                    1,
                    1,
                    0,
                    '',
                    '{}',
                    '',
                    '',
                    0,
                    '0000-00-00 00:00:00',
                    1,
                    0,
                ],
                // plg_finder_contacts (443)
                [
                    443,
                    'plg_finder_contacts',
                    'plugin',
                    'contacts',
                    'finder',
                    0,
                    1,
                    1,
                    0,
                    '',
                    '{}',
                    '',
                    '',
                    0,
                    '0000-00-00 00:00:00',
                    2,
                    0,
                ],
                // plg_finder_content (444)
                [
                    444,
                    'plg_finder_content',
                    'plugin',
                    'content',
                    'finder',
                    0,
                    1,
                    1,
                    0,
                    '',
                    '{}',
                    '',
                    '',
                    0,
                    '0000-00-00 00:00:00',
                    3,
                    0,
                ],
                // plg_finder_newsfeeds (445)
                [
                    445,
                    'plg_finder_newsfeeds',
                    'plugin',
                    'newsfeeds',
                    'finder',
                    0,
                    1,
                    1,
                    0,
                    '',
                    '{}',
                    '',
                    '',
                    0,
                    '0000-00-00 00:00:00',
                    4,
                    0,
                ],
                // plg_finder_weblinks (446)
                [
                    446,
                    'plg_finder_weblinks',
                    'plugin',
                    'weblinks',
                    'finder',
                    0,
                    1,
                    1,
                    0,
                    '',
                    '{}',
                    '',
                    '',
                    0,
                    '0000-00-00 00:00:00',
                    5,
                    0,
                ],
            ];

            // Map values to associative arrays
            $rows = [];
            foreach ($values as $val) {
                $rows[] = array_combine($columns, $val);
            }

            $this->db->getQuery(true)
                ->insertMany('#__extensions', $rows);

            // Templates
            $columns = [
                'extension_id', 'name', 'type', 'element', 'folder', 'client_id', 'enabled',
                'access', 'protected', 'manifest_cache', 'params', 'custom_data', 'system_data',
                'checked_out', 'checked_out_time', 'ordering', 'state'
            ];

            $values = [
                // atomic (500)
                [
                    500,
                    'atomic',
                    'template',
                    'atomic',
                    '',
                    0,
                    1,
                    1,
                    0,
                    '{"legacy":false,"name":"atomic","type":"template","creationDate":"10\/10\/09",'
                        . '"author":"Ron Severdia","copyright":"Copyright (C) 2005 - 2013 Open Source '
                        . 'Matters, Inc. All rights reserved.","authorEmail":"contact@kontentdesign.com",'
                        . '"authorUrl":"http:\/\/www.kontentdesign.com","version":"1.6.0",'
                        . '"description":"TPL_ATOMIC_XML_DESCRIPTION","group":""}',
                    '{}',
                    '',
                    '',
                    0,
                    '0000-00-00 00:00:00',
                    0,
                    0,
                ],
                // bluestork (502)
                [
                    502,
                    'bluestork',
                    'template',
                    'bluestork',
                    '',
                    1,
                    1,
                    1,
                    0,
                    '{"legacy":false,"name":"bluestork","type":"template","creationDate":"07\/02\/09",'
                        . '"author":"Joomla! Project","copyright":"Copyright (C) 2005 - 2013 Open Source '
                        . 'Matters, Inc. All rights reserved.","authorEmail":"admin@joomla.org",'
                        . '"authorUrl":"http:\/\/www.joomla.org","version":"1.6.0",'
                        . '"description":"TPL_BLUESTORK_XML_DESCRIPTION","group":""}',
                    '{"useRoundedCorners":"1","showSiteName":"0","textBig":"0","highContrast":"0"}',
                    '',
                    '',
                    0,
                    '0000-00-00 00:00:00',
                    0,
                    0,
                ],
                // beez_20 (503)
                [
                    503,
                    'beez_20',
                    'template',
                    'beez_20',
                    '',
                    0,
                    1,
                    1,
                    0,
                    '{"legacy":false,"name":"beez_20","type":"template","creationDate":"25 November '
                        . '2009","author":"Angie Radtke","copyright":"Copyright (C) 2005 - 2013 Open Source '
                        . 'Matters, Inc. All rights reserved.","authorEmail":"a.radtke@derauftritt.de",'
                        . '"authorUrl":"http:\/\/www.der-auftritt.de","version":"1.6.0",'
                        . '"description":"TPL_BEEZ2_XML_DESCRIPTION","group":""}',
                    '{"wrapperSmall":"53","wrapperLarge":"72","sitetitle":"","sitedescription":"",'
                        . '"navposition":"center","templatecolor":"nature"}',
                    '',
                    '',
                    0,
                    '0000-00-00 00:00:00',
                    0,
                    0,
                ],
                // hathor (504)
                [
                    504,
                    'hathor',
                    'template',
                    'hathor',
                    '',
                    1,
                    1,
                    1,
                    0,
                    '{"legacy":false,"name":"hathor","type":"template","creationDate":"May 2010",'
                        . '"author":"Andrea Tarr","copyright":"Copyright (C) 2005 - 2013 Open Source '
                        . 'Matters, Inc. All rights reserved.","authorEmail":"hathor@tarrconsulting.com",'
                        . '"authorUrl":"http:\/\/www.tarrconsulting.com","version":"1.6.0",'
                        . '"description":"TPL_HATHOR_XML_DESCRIPTION","group":""}',
                    '{"showSiteName":"0","colourChoice":"0","boldText":"0"}',
                    '',
                    '',
                    0,
                    '0000-00-00 00:00:00',
                    0,
                    0,
                ],
                // beez5 (505)
                [
                    505,
                    'beez5',
                    'template',
                    'beez5',
                    '',
                    0,
                    1,
                    1,
                    0,
                    '{"legacy":false,"name":"beez5","type":"template","creationDate":"21 May 2010",'
                        . '"author":"Angie Radtke","copyright":"Copyright (C) 2005 - 2013 Open Source '
                        . 'Matters, Inc. All rights reserved.","authorEmail":"a.radtke@derauftritt.de",'
                        . '"authorUrl":"http:\/\/www.der-auftritt.de","version":"1.6.0",'
                        . '"description":"TPL_BEEZ5_XML_DESCRIPTION","group":""}',
                    '{"wrapperSmall":"53","wrapperLarge":"72","sitetitle":"","sitedescription":"",'
                        . '"navposition":"center","html5":"0"}',
                    '',
                    '',
                    0,
                    '0000-00-00 00:00:00',
                    0,
                    0,
                ],
            ];

            // Map values to associative arrays
            $rows = [];
            foreach ($values as $val) {
                $rows[] = array_combine($columns, $val);
            }

            $this->db->getQuery(true)
                ->insertMany('#__extensions', $rows);

            // Languages
            $columns = [
                'extension_id', 'name', 'type', 'element', 'folder', 'client_id', 'enabled',
                'access', 'protected', 'manifest_cache', 'params', 'custom_data', 'system_data',
                'checked_out', 'checked_out_time', 'ordering', 'state'
            ];

            $values = [
                // English (United Kingdom) - Site (600)
                [
                    600,
                    'English (United Kingdom)',
                    'language',
                    'en-GB',
                    '',
                    0,
                    1,
                    1,
                    1,
                    '',
                    '',
                    '',
                    '',
                    0,
                    '0000-00-00 00:00:00',
                    0,
                    0,
                ],
                // English (United Kingdom) - Admin (601)
                [
                    601,
                    'English (United Kingdom)',
                    'language',
                    'en-GB',
                    '',
                    1,
                    1,
                    1,
                    1,
                    '',
                    '',
                    '',
                    '',
                    0,
                    '0000-00-00 00:00:00',
                    0,
                    0,
                ],
            ];

            // Map values to associative arrays
            $rows = [];
            foreach ($values as $val) {
                $rows[] = array_combine($columns, $val);
            }

            $this->db->getQuery(true)
                ->insertMany('#__extensions', $rows);

            // Joomla! CMS (700)
            $this->db->getQuery(true)
                ->insert('#__extensions')
                ->columns([
                    'extension_id', 'name', 'type', 'element', 'folder', 'client_id', 'enabled',
                    'access', 'protected', 'manifest_cache', 'params', 'custom_data', 'system_data',
                    'checked_out', 'checked_out_time', 'ordering', 'state'
                ])
                ->values([
                    700,
                    'Joomla! CMS',
                    'file',
                    'joomla',
                    '',
                    0,
                    1,
                    1,
                    1,
                    '{"legacy":false,"name":"files_joomla","type":"file","creationDate":"April 2013",'
                        . '"author":"Joomla!",'
                        . '"copyright":"(C) 2005 - 2013 Open Source Matters. All rights reserved",'
                        . '"authorEmail":"admin@joomla.org","authorUrl":"www.joomla.org",'
                        . '"version":"2.5.11","description":"FILES_JOOMLA_XML_DESCRIPTION","group":""}',
                    '',
                    '',
                    '',
                    0,
                    '0000-00-00 00:00:00',
                    0,
                    0,
                ])
                ->execute();

            // joomla package (800)
            $this->db->getQuery(true)
                ->insert('#__extensions')
                ->set([
                    'extension_id'   => 800,
                    'name'           => 'joomla',
                    'type'           => 'package',
                    'element'        => 'pkg_joomla',
                    'folder'         => '',
                    'client_id'      => 0,
                    'enabled'        => 1,
                    'access'         => 1,
                    'protected'      => 1,
                    'manifest_cache' => '',
                    'params'         => '',
                    'custom_data'    => '',
                    'system_data'    => '',
                    'checked_out'    => 0,
                    'checked_out_time' => '0000-00-00 00:00:00',
                    'ordering'       => 0,
                    'state'          => 0
                ])
                ->execute();

            // Set AUTO_INCREMENT starting value (handled automatically for SQLite)
            $schema->setAutoIncrement('#__extensions', 1000);

            // Migrate components
            $results = $this->db->getQuery(true)
                ->select('*')
                ->from('#__components')
                ->where('parent', '=', 0)
                ->loadObjectList();

            // Build list of extensions to exclude (i.e. they're no longer in the core)
            $excludes = array(
                'com_sef',
                'com_userpoints',
                'com_hub',
                'com_storefront',
                'com_contribute',
                'com_contribtool'
            );

            foreach ($results as $r) {
                // See if we want to ignore the entry
                if (in_array($r->option, $excludes)) {
                    continue;
                }

                $id = $this->db->getQuery(true)
                    ->select('extension_id')
                    ->from('#__extensions')
                    ->where('element', '=', $r->option)
                    ->value('extension_id');

                if ($id) {
                    $this->db->getQuery(true)
                        ->update('#__extensions')
                        ->set(['enabled' => $r->enabled])
                        ->set(['params' => $r->params])
                        ->set(['ordering' => $r->ordering])
                        ->where('extension_id', '=', $id)
                        ->execute();
                    continue;
                }

                $this->db->getQuery(true)
                    ->insert('#__extensions')
                    ->values([
                        'name'              => $r->option,
                        'type'              => 'component',
                        'element'           => $r->option,
                        'folder'            => '',
                        'client_id'         => 1,
                        'enabled'           => $r->enabled,
                        'access'            => 1,
                        'protected'         => $r->iscore,
                        'manifest_cache'    => '',
                        'params'            => $r->params,
                        'custom_data'       => '',
                        'system_data'       => '',
                        'checked_out'       => 0,
                        'checked_out_time'  => '0000-00-00 00:00:00',
                        'ordering'          => 0,
                        'state'             => 0
                    ])
                    ->execute();
                continue;
            }

            // Look for any components we missed...(frontend?)
            $components = array_diff(scandir(PATH_CORE . DS . 'components'), array(".", ".."));
            foreach ($components as $c) {
                if (!is_dir(PATH_CORE . DS . 'components' . DS . $c . DS . 'site')) {
                    continue;
                }

                $query = $this->db->getQuery(true)
                    ->select('extension_id')
                    ->from('#__extensions')
                    ->where('element', '=', $c)
                    ;

                if ($query->exists()) {
                    continue;
                }

                $this->db->getQuery(true)
                    ->insert('#__extensions')
                    ->values([
                        'name'              => $c,
                        'type'              => 'component',
                        'element'           => $c,
                        'folder'            => '',
                        'client_id'         => 0,
                        'enabled'           => 1,
                        'access'            => 1,
                        'protected'         => 1,
                        'manifest_cache'    => '{}',
                        'params'            => '{}',
                        'custom_data'       => '',
                        'system_data'       => '',
                        'checked_out'       => 0,
                        'checked_out_time'  => '0000-00-00 00:00:00',
                        'ordering'          => 0,
                        'state'             => 0
                    ])
                    ->execute();
            }

            // Look for any components we missed...(backend?)
            $components = array_diff(scandir(PATH_CORE . DS . 'components'), array(".", ".."));
            foreach ($components as $c) {
                if (!is_dir(PATH_CORE . DS . 'components' . DS . $c . DS . 'admin')) {
                    continue;
                }

                $query = $this->db->getQuery(true)
                    ->select('extension_id')
                    ->from('#__extensions')
                    ->where('element', '=', $c)
                    ;

                if ($query->exists()) {
                    continue;
                }

                $this->db->getQuery(true)
                    ->insert('#__extensions')
                    ->values([
                        'name'              => $c,
                        'type'              => 'component',
                        'element'           => $c,
                        'folder'            => '',
                        'client_id'         => 1,
                        'enabled'           => 1,
                        'access'            => 1,
                        'protected'         => 1,
                        'manifest_cache'    => '{}',
                        'params'            => '{}',
                        'custom_data'       => '',
                        'system_data'       => '',
                        'checked_out'       => 0,
                        'checked_out_time'  => '0000-00-00 00:00:00',
                        'ordering'          => 0,
                        'state'             => 0
                    ])
                    ->execute();
            }

            // Migrate plugins
            $results = $this->db->getQuery(true)
                ->select('*')
                ->from('#__plugins')
                ->loadObjectList();

            foreach ($results as $r) {
                $id = $this->db->getQuery(true)
                    ->select('extension_id')
                    ->from('#__extensions')
                    ->where('name', '=', "plg_{$r->folder}_{$r->element}")
                    ->value('extension_id');

                if ($id) {
                    $this->db->getQuery(true)
                        ->update('#__extensions')
                        ->set(['enabled' => $r->published])
                        ->set(['params' => $r->params])
                        ->set(['ordering' => $r->ordering])
                        ->where('extension_id', '=', $id)
                        ->execute();
                    continue;
                }

                // Add 1 to access level
                $r->access++;

                $this->db->getQuery(true)
                    ->insert('#__extensions')
                    ->values([
                        'name'              => "plg_{$r->folder}_{$r->element}",
                        'type'              => 'plugin',
                        'element'           => $r->element,
                        'folder'            => $r->folder,
                        'client_id'         => $r->client_id,
                        'enabled'           => $r->published,
                        'access'            => $r->access,
                        'protected'         => $r->iscore,
                        'manifest_cache'    => '',
                        'params'            => $r->params,
                        'custom_data'       => '',
                        'system_data'       => '',
                        'checked_out'       => $r->checked_out,
                        'checked_out_time'  => $r->checked_out_time,
                        'ordering'          => $r->ordering,
                        'state'             => 0
                    ])
                    ->execute();
            }

            // Migrate modules (site)
            $modules = array_diff(scandir(PATH_CORE . DS . 'modules'), array(".", ".."));
            foreach ($modules as $m) {
                $query = $this->db->getQuery(true)
                    ->select('extension_id')
                    ->from('#__extensions')
                    ->where('element', '=', $m)
                    ;

                if ($query->exists()) {
                    continue;
                }

                if (!is_dir(PATH_CORE . DS . 'modules' . DS . $m)) {
                    continue;
                }

                $this->db->getQuery(true)
                    ->insert('#__extensions')
                    ->values([
                        'name'              => $m,
                        'type'              => 'module',
                        'element'           => $m,
                        'folder'            => '',
                        'client_id'         => 0,
                        'enabled'           => 1,
                        'access'            => 1,
                        'protected'         => 1,
                        'manifest_cache'    => '{}',
                        'params'            => '{}',
                        'custom_data'       => '',
                        'system_data'       => '',
                        'checked_out'       => 0,
                        'checked_out_time'  => '0000-00-00 00:00:00',
                        'ordering'          => 0,
                        'state'             => 0
                    ])
                    ->execute();
            }

            // Migrate modules (admin)
            // @TODO: 2.0.0+ we can't differentiate admin modules at filesystem level
            $modules = array_diff(scandir(PATH_CORE . DS . 'modules'), array(".", ".."));
            foreach ($modules as $m) {
                $query = $this->db->getQuery(true)
                    ->select('extension_id')
                    ->from('#__extensions')
                    ->where('element', '=', $m)
                    ;

                if ($query->exists()) {
                    continue;
                }

                if (!is_dir(PATH_CORE . DS . 'modules' . DS . $m)) {
                    continue;
                }

                $this->db->getQuery(true)
                    ->insert('#__extensions')
                    ->values([
                        'name'              => $m,
                        'type'              => 'module',
                        'element'           => $m,
                        'folder'            => '',
                        'client_id'         => 1,
                        'enabled'           => 1,
                        'access'            => 1,
                        'protected'         => 1,
                        'manifest_cache'    => '{}',
                        'params'            => '{}',
                        'custom_data'       => '',
                        'system_data'       => '',
                        'checked_out'       => 0,
                        'checked_out_time'  => '0000-00-00 00:00:00',
                        'ordering'          => 0,
                        'state'             => 0
                    ])
                    ->execute();
            }

            // Migrate templates
            $templates = array_diff(
                scandir(PATH_CORE . DS . 'templates'),
                array(".", "..", "system")
            );
            foreach ($templates as $t) {
                $query = $this->db->getQuery(true)
                    ->select('extension_id')
                    ->from('#__extensions')
                    ->where('element', '=', $t)
                    ->where('type', '=', 'template')
                    ;

                if ($query->exists()) {
                    continue;
                }

                if (!is_dir(PATH_CORE . DS . 'templates' . DS . $t)) {
                    continue;
                }

                $this->db->getQuery(true)
                    ->insert('#__extensions')
                    ->values([
                        'name'              => ucfirst($t),
                        'type'              => 'template',
                        'element'           => $t,
                        'folder'            => '',
                        'client_id'         => 0,
                        'enabled'           => 1,
                        'access'            => 1,
                        'protected'         => 0,
                        'manifest_cache'    => '{}',
                        'params'            => '{}',
                        'custom_data'       => '',
                        'system_data'       => '',
                        'checked_out'       => 0,
                        'checked_out_time'  => '0000-00-00 00:00:00',
                        'ordering'          => 0,
                        'state'             => 0
                    ])
                    ->execute();
            }

            // Admin templates too
            // @TODO: 2.0.0+ we can't differentiate admin templates at filesystem level
            $templates = array_diff(
                scandir(PATH_CORE . DS . 'templates'),
                array(".", "..", "system")
            );
            foreach ($templates as $t) {
                $query = $this->db->getQuery(true)
                    ->select('extension_id')
                    ->from('#__extensions')
                    ->where('element', '=', $t)
                    ->where('type', '=', 'template')
                    ;

                if ($query->exists()) {
                    continue;
                }

                if (!is_dir(PATH_CORE . DS . 'templates' . DS . $t)) {
                    continue;
                }

                $this->db->getQuery(true)
                    ->insert('#__extensions')
                    ->values([
                        'name'              => ucfirst($t),
                        'type'              => 'template',
                        'element'           => $t,
                        'folder'            => '',
                        'client_id'         => 1,
                        'enabled'           => 1,
                        'access'            => 1,
                        'protected'         => 0,
                        'manifest_cache'    => '{}',
                        'params'            => '{}',
                        'custom_data'       => '',
                        'system_data'       => '',
                        'checked_out'       => 0,
                        'checked_out_time'  => '0000-00-00 00:00:00',
                        'ordering'          => 0,
                        'state'             => 0
                    ])
                    ->execute();
            }

            // Convert params to json
            $results = $this->db->getQuery(true)
                ->select(['extension_id', 'params'])
                ->from('#__extensions')
                ->whereNotNull('params')
                ->where('params', '!=', '')
                ->where(Expression::substring('params', 1, 1), '!=', '{')
                ->loadObjectList();

            if (count($results) > 0) {
                foreach ($results as $r) {
                    $params = trim($r->params);
                    if (empty($params) || $params == '{}') {
                        continue;
                    }

                    $array = array();
                    $ar    = explode("\n", $params);

                    foreach ($ar as $a) {
                        $a = trim($a);
                        if (empty($a)) {
                            continue;
                        }

                        $ar2     = explode("=", $a, 2);
                        $array[$ar2[0]] = (isset($ar2[1])) ? $ar2[1] : '';
                    }

                    $this->db->getQuery(true)
                        ->update('#__extensions')
                        ->set(['params' => json_encode($array)])
                        ->where('extension_id', '=', $r->extension_id)
                        ->execute();
                }
            }

            // Update com_config defaults
            $configDefaultParams = '{"filters":{"1":{"filter_type":"NH","filter_tags":"",'
                . '"filter_attributes":""},"6":{"filter_type":"BL","filter_tags":"",'
                . '"filter_attributes":""},"7":{"filter_type":"NONE","filter_tags":"",'
                . '"filter_attributes":""},"2":{"filter_type":"NH","filter_tags":"",'
                . '"filter_attributes":""},"3":{"filter_type":"BL","filter_tags":"",'
                . '"filter_attributes":""},"4":{"filter_type":"BL","filter_tags":"",'
                . '"filter_attributes":""},"5":{"filter_type":"BL","filter_tags":"",'
                . '"filter_attributes":""},"10":{"filter_type":"BL","filter_tags":"",'
                . '"filter_attributes":""},"12":{"filter_type":"BL","filter_tags":"",'
                . '"filter_attributes":""},"8":{"filter_type":"NONE","filter_tags":"",'
                . '"filter_attributes":""}}}';
            $this->db->getQuery(true)
                ->update('#__extensions')
                ->set(['params' => $configDefaultParams])
                ->where('element', '=', 'com_config')
                ->execute();

            // Delete some old/unused plugins
            $this->db->getQuery(true)->delete('#__extensions')->where('folder', '=', 'search')->execute();
            $this->db->getQuery(true)->delete('#__extensions')->where('folder', '=', 'xmlrpc')->execute();
            $this->db
                ->getQuery(true)
                ->delete('#__extensions')
                ->where('folder', '=', 'system')
                ->where('element', '=', 'legacy')
                ->execute();
            $this->db
                ->getQuery(true)
                ->delete('#__extensions')
                ->where('folder', '=', 'system')
                ->where('element', '=', 'backlink')
                ->execute();
            $this->db
                ->getQuery(true)
                ->delete('#__extensions')
                ->where('folder', '=', 'system')
                ->where('element', '=', 'mtupgrade')
                ->execute();
            $this->db
                ->getQuery(true)
                ->delete('#__extensions')
                ->where('folder', '=', 'authentication')
                ->where('element', '=', 'ldap')
                ->execute();
            $this->db
                ->getQuery(true)
                ->delete('#__extensions')
                ->where('folder', '=', 'authentication')
                ->where('element', '=', 'gmail')
                ->execute();
            $this->db
                ->getQuery(true)
                ->delete('#__extensions')
                ->where('folder', '=', 'authentication')
                ->where('element', '=', 'openid')
                ->execute();
            $this->db
                ->getQuery(true)
                ->delete('#__extensions')
                ->where('folder', '=', 'authentication')
                ->where('element', '=', 'joomla')
                ->execute();
            $this->db
                ->getQuery(true)
                ->delete('#__extensions')
                ->where('folder', '=', 'editors')
                ->where('element', '=', 'xstandard')
                ->execute();

            // Also, force xusers user plugin last in list
            $max = $this->db->getQuery(true)
                ->select('ordering')
                ->from('#__extensions')
                ->where('folder', '=', 'user')
                ->order('ordering', 'desc')
                ->value('ordering');
            $max++;
            $this->db->getQuery(true)
                ->update('#__extensions')
                ->set(['ordering' => $max])
                ->where('folder', '=', 'user')
                ->where('element', '=', 'xusers')
                ->execute();

            // Delete plugins and components tables
            if ($schema->tableExists('#__plugins')) {
                $this->db->schema()->dropTable('#__plugins');
            }
        }
    }
}
