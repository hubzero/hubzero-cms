--
--  mysqldump --no-create-info --skip-add-locks --skip-disable-keys --skip-extended-insert --complete-insert
--
--  Further cleaned by hand for readability
--

SET NAMES 'utf8mb3';
SET @@SESSION.sql_mode = '';

--
-- Dumping data for table `#__abuse_reports`
--


--
-- Dumping data for table `#__announcements`
--


--
-- Dumping data for table `#__answers_log`
--


--
-- Dumping data for table `#__answers_questions`
--


--
-- Dumping data for table `#__answers_questions_log`
--


--
-- Dumping data for table `#__answers_responses`
--


--
-- Dumping data for table `#__assets`
--

INSERT INTO `#__assets` (`id`, `parent_id`, `lft`, `rgt`, `level`, `name`, `title`, `rules`) VALUES (1,0,1,67,0,'root.1','Root Asset','{\"core.login.site\":{\"6\":1,\"2\":1},\"core.login.admin\":{\"6\":1},\"core.login.offline\":{\"6\":1},\"core.admin\":{\"8\":1},\"core.manage\":{\"7\":1},\"core.create\":{\"6\":1,\"3\":1},\"core.delete\":{\"6\":1},\"core.edit\":{\"6\":1,\"4\":1},\"core.edit.state\":{\"6\":1,\"5\":1},\"core.edit.own\":{\"6\":1,\"3\":1}}');
INSERT INTO `#__assets` (`id`, `parent_id`, `lft`, `rgt`, `level`, `name`, `title`, `rules`) VALUES (2,1,1,2,1,'com_admin','com_admin','{}');
INSERT INTO `#__assets` (`id`, `parent_id`, `lft`, `rgt`, `level`, `name`, `title`, `rules`) VALUES (4,1,7,8,1,'com_cache','com_cache','{\"core.admin\":{\"7\":1},\"core.manage\":{\"7\":1}}');
INSERT INTO `#__assets` (`id`, `parent_id`, `lft`, `rgt`, `level`, `name`, `title`, `rules`) VALUES (5,1,9,10,1,'com_checkin','com_checkin','{\"core.admin\":{\"7\":1},\"core.manage\":{\"7\":1}}');
INSERT INTO `#__assets` (`id`, `parent_id`, `lft`, `rgt`, `level`, `name`, `title`, `rules`) VALUES (6,1,11,12,1,'com_config','com_config','{}');
INSERT INTO `#__assets` (`id`, `parent_id`, `lft`, `rgt`, `level`, `name`, `title`, `rules`) VALUES (8,1,17,20,1,'com_content','com_content','{\"core.admin\":{\"7\":1},\"core.manage\":{\"6\":1},\"core.create\":{\"3\":1},\"core.delete\":[],\"core.edit\":{\"4\":1},\"core.edit.state\":{\"5\":1},\"core.edit.own\":[]}');
INSERT INTO `#__assets` (`id`, `parent_id`, `lft`, `rgt`, `level`, `name`, `title`, `rules`) VALUES (9,1,21,22,1,'com_cpanel','com_cpanel','{}');
INSERT INTO `#__assets` (`id`, `parent_id`, `lft`, `rgt`, `level`, `name`, `title`, `rules`) VALUES (10,1,23,24,1,'com_installer','com_installer','{\"core.admin\":[],\"core.manage\":{\"7\":0},\"core.delete\":{\"7\":0},\"core.edit.state\":{\"7\":0}}');
INSERT INTO `#__assets` (`id`, `parent_id`, `lft`, `rgt`, `level`, `name`, `title`, `rules`) VALUES (11,1,25,26,1,'com_languages','com_languages','{\"core.admin\":{\"7\":1},\"core.manage\":[],\"core.create\":[],\"core.delete\":[],\"core.edit\":[],\"core.edit.state\":[]}');
INSERT INTO `#__assets` (`id`, `parent_id`, `lft`, `rgt`, `level`, `name`, `title`, `rules`) VALUES (12,1,27,28,1,'com_login','com_login','{}');
INSERT INTO `#__assets` (`id`, `parent_id`, `lft`, `rgt`, `level`, `name`, `title`, `rules`) VALUES (13,1,29,30,1,'com_mailto','com_mailto','{}');
INSERT INTO `#__assets` (`id`, `parent_id`, `lft`, `rgt`, `level`, `name`, `title`, `rules`) VALUES (14,1,31,32,1,'com_massmail','com_massmail','{}');
INSERT INTO `#__assets` (`id`, `parent_id`, `lft`, `rgt`, `level`, `name`, `title`, `rules`) VALUES (15,1,33,34,1,'com_media','com_media','{\"core.admin\":{\"7\":1},\"core.manage\":{\"6\":1},\"core.create\":{\"3\":1},\"core.delete\":{\"5\":1}}');
INSERT INTO `#__assets` (`id`, `parent_id`, `lft`, `rgt`, `level`, `name`, `title`, `rules`) VALUES (16,1,35,36,1,'com_menus','com_menus','{\"core.admin\":{\"7\":1},\"core.manage\":[],\"core.create\":[],\"core.delete\":[],\"core.edit\":[],\"core.edit.state\":[]}');
INSERT INTO `#__assets` (`id`, `parent_id`, `lft`, `rgt`, `level`, `name`, `title`, `rules`) VALUES (17,1,37,38,1,'com_messages','com_messages','{\"core.admin\":{\"7\":1},\"core.manage\":{\"7\":1}}');
INSERT INTO `#__assets` (`id`, `parent_id`, `lft`, `rgt`, `level`, `name`, `title`, `rules`) VALUES (18,1,39,40,1,'com_modules','com_modules','{\"core.admin\":{\"7\":1},\"core.manage\":[],\"core.create\":[],\"core.delete\":[],\"core.edit\":[],\"core.edit.state\":[]}');
INSERT INTO `#__assets` (`id`, `parent_id`, `lft`, `rgt`, `level`, `name`, `title`, `rules`) VALUES (19,1,41,44,1,'com_newsfeeds','com_newsfeeds','{\"core.admin\":{\"7\":1},\"core.manage\":{\"6\":1},\"core.create\":[],\"core.delete\":[],\"core.edit\":[],\"core.edit.state\":[],\"core.edit.own\":[]}');
INSERT INTO `#__assets` (`id`, `parent_id`, `lft`, `rgt`, `level`, `name`, `title`, `rules`) VALUES (20,1,45,46,1,'com_plugins','com_plugins','{\"core.admin\":{\"7\":1},\"core.manage\":[],\"core.edit\":[],\"core.edit.state\":[]}');
INSERT INTO `#__assets` (`id`, `parent_id`, `lft`, `rgt`, `level`, `name`, `title`, `rules`) VALUES (21,1,47,48,1,'com_redirect','com_redirect','{\"core.admin\":{\"7\":1},\"core.manage\":[]}');
INSERT INTO `#__assets` (`id`, `parent_id`, `lft`, `rgt`, `level`, `name`, `title`, `rules`) VALUES (23,1,51,52,1,'com_templates','com_templates','{\"core.admin\":{\"7\":1},\"core.manage\":[],\"core.create\":[],\"core.delete\":[],\"core.edit\":[],\"core.edit.state\":[]}');
INSERT INTO `#__assets` (`id`, `parent_id`, `lft`, `rgt`, `level`, `name`, `title`, `rules`) VALUES (24,1,53,56,1,'com_users','com_users','{\"core.admin\":{\"7\":1},\"core.manage\":[],\"core.create\":[],\"core.delete\":[],\"core.edit\":[],\"core.edit.state\":[]}');
INSERT INTO `#__assets` (`id`, `parent_id`, `lft`, `rgt`, `level`, `name`, `title`, `rules`) VALUES (27,8,18,19,2,'com_content.category.2','Uncategorised','{\"core.create\":[],\"core.delete\":[],\"core.edit\":[],\"core.edit.state\":[],\"core.edit.own\":[]}');
INSERT INTO `#__assets` (`id`, `parent_id`, `lft`, `rgt`, `level`, `name`, `title`, `rules`) VALUES (30,19,42,43,2,'com_newsfeeds.category.5','Uncategorised','{\"core.create\":[],\"core.delete\":[],\"core.edit\":[],\"core.edit.state\":[],\"core.edit.own\":[]}');
INSERT INTO `#__assets` (`id`, `parent_id`, `lft`, `rgt`, `level`, `name`, `title`, `rules`) VALUES (32,24,54,55,1,'com_users.category.7','Uncategorised','{\"core.create\":[],\"core.delete\":[],\"core.edit\":[],\"core.edit.state\":[]}');
INSERT INTO `#__assets` (`id`, `parent_id`, `lft`, `rgt`, `level`, `name`, `title`, `rules`) VALUES (34,1,65,66,1,'com_joomlaupdate','com_joomlaupdate','{\"core.admin\":[],\"core.manage\":[],\"core.delete\":[],\"core.edit.state\":[]}');

--
-- Dumping data for table `#__associations`
--


--
-- Dumping data for table `#__auth_domain`
--


--
-- Dumping data for table `#__auth_link`
--


--
-- Dumping data for table `#__author_assoc`
--


--
-- Dumping data for table `#__author_role_types`
--


--
-- Dumping data for table `#__author_roles`
--


--
-- Dumping data for table `#__author_stats`
--


--
-- Dumping data for table `#__billboard_collection`
--


--
-- Dumping data for table `#__billboards`
--


--
-- Dumping data for table `#__blog_comments`
--


--
-- Dumping data for table `#__blog_entries`
--


--
-- Dumping data for table `#__cart`
--


--
-- Dumping data for table `#__cart_cart_items`
--


--
-- Dumping data for table `#__cart_carts`
--


--
-- Dumping data for table `#__cart_coupons`
--


--
-- Dumping data for table `#__cart_memberships`
--


--
-- Dumping data for table `#__cart_saved_addresses`
--


--
-- Dumping data for table `#__cart_transaction_info`
--


--
-- Dumping data for table `#__cart_transaction_items`
--


--
-- Dumping data for table `#__cart_transaction_steps`
--


--
-- Dumping data for table `#__cart_transactions`
--


--
-- Dumping data for table `#__categories`
--

INSERT INTO `#__categories` (`id`, `asset_id`, `parent_id`, `lft`, `rgt`, `level`, `path`, `extension`, `title`, `alias`, `note`, `description`, `published`, `checked_out`, `checked_out_time`, `access`, `params`, `metadesc`, `metakey`, `metadata`, `created_user_id`, `created_time`, `modified_user_id`, `modified_time`, `hits`, `language`) VALUES (1,0,0,0,13,0,'','system','ROOT','root','','',1,0,'0000-00-00 00:00:00',1,'{}','','','',0,'2009-10-18 16:07:09',0,'0000-00-00 00:00:00',0,'*');
INSERT INTO `#__categories` (`id`, `asset_id`, `parent_id`, `lft`, `rgt`, `level`, `path`, `extension`, `title`, `alias`, `note`, `description`, `published`, `checked_out`, `checked_out_time`, `access`, `params`, `metadesc`, `metakey`, `metadata`, `created_user_id`, `created_time`, `modified_user_id`, `modified_time`, `hits`, `language`) VALUES (2,27,1,1,2,1,'uncategorised','com_content','Uncategorised','uncategorised','','',1,0,'0000-00-00 00:00:00',1,'{\"target\":\"\",\"image\":\"\"}','','','{\"page_title\":\"\",\"author\":\"\",\"robots\":\"\"}',42,'2010-06-28 13:26:37',0,'0000-00-00 00:00:00',0,'*');
INSERT INTO `#__categories` (`id`, `asset_id`, `parent_id`, `lft`, `rgt`, `level`, `path`, `extension`, `title`, `alias`, `note`, `description`, `published`, `checked_out`, `checked_out_time`, `access`, `params`, `metadesc`, `metakey`, `metadata`, `created_user_id`, `created_time`, `modified_user_id`, `modified_time`, `hits`, `language`) VALUES (3,28,1,3,4,1,'uncategorised','com_banners','Uncategorised','uncategorised','','',1,0,'0000-00-00 00:00:00',1,'{\"target\":\"\",\"image\":\"\",\"foobar\":\"\"}','','','{\"page_title\":\"\",\"author\":\"\",\"robots\":\"\"}',42,'2010-06-28 13:27:35',0,'0000-00-00 00:00:00',0,'*');
INSERT INTO `#__categories` (`id`, `asset_id`, `parent_id`, `lft`, `rgt`, `level`, `path`, `extension`, `title`, `alias`, `note`, `description`, `published`, `checked_out`, `checked_out_time`, `access`, `params`, `metadesc`, `metakey`, `metadata`, `created_user_id`, `created_time`, `modified_user_id`, `modified_time`, `hits`, `language`) VALUES (4,29,1,5,6,1,'uncategorised','com_contact','Uncategorised','uncategorised','','',1,0,'0000-00-00 00:00:00',1,'{\"target\":\"\",\"image\":\"\"}','','','{\"page_title\":\"\",\"author\":\"\",\"robots\":\"\"}',42,'2010-06-28 13:27:57',0,'0000-00-00 00:00:00',0,'*');
INSERT INTO `#__categories` (`id`, `asset_id`, `parent_id`, `lft`, `rgt`, `level`, `path`, `extension`, `title`, `alias`, `note`, `description`, `published`, `checked_out`, `checked_out_time`, `access`, `params`, `metadesc`, `metakey`, `metadata`, `created_user_id`, `created_time`, `modified_user_id`, `modified_time`, `hits`, `language`) VALUES (5,30,1,7,8,1,'uncategorised','com_newsfeeds','Uncategorised','uncategorised','','',1,0,'0000-00-00 00:00:00',1,'{\"target\":\"\",\"image\":\"\"}','','','{\"page_title\":\"\",\"author\":\"\",\"robots\":\"\"}',42,'2010-06-28 13:28:15',0,'0000-00-00 00:00:00',0,'*');
INSERT INTO `#__categories` (`id`, `asset_id`, `parent_id`, `lft`, `rgt`, `level`, `path`, `extension`, `title`, `alias`, `note`, `description`, `published`, `checked_out`, `checked_out_time`, `access`, `params`, `metadesc`, `metakey`, `metadata`, `created_user_id`, `created_time`, `modified_user_id`, `modified_time`, `hits`, `language`) VALUES (6,31,1,9,10,1,'uncategorised','com_weblinks','Uncategorised','uncategorised','','',1,0,'0000-00-00 00:00:00',1,'{\"target\":\"\",\"image\":\"\"}','','','{\"page_title\":\"\",\"author\":\"\",\"robots\":\"\"}',42,'2010-06-28 13:28:33',0,'0000-00-00 00:00:00',0,'*');
INSERT INTO `#__categories` (`id`, `asset_id`, `parent_id`, `lft`, `rgt`, `level`, `path`, `extension`, `title`, `alias`, `note`, `description`, `published`, `checked_out`, `checked_out_time`, `access`, `params`, `metadesc`, `metakey`, `metadata`, `created_user_id`, `created_time`, `modified_user_id`, `modified_time`, `hits`, `language`) VALUES (7,32,1,11,12,1,'uncategorised','com_users','Uncategorised','uncategorised','','',1,0,'0000-00-00 00:00:00',1,'{\"target\":\"\",\"image\":\"\"}','','','{\"page_title\":\"\",\"author\":\"\",\"robots\":\"\"}',42,'2010-06-28 13:28:33',0,'0000-00-00 00:00:00',0,'*');

--
-- Dumping data for table `#__citations`
--


--
-- Dumping data for table `#__citations_assoc`
--


--
-- Dumping data for table `#__citations_authors`
--


--
-- Dumping data for table `#__citations_format`
--


--
-- Dumping data for table `#__citations_secondary`
--


--
-- Dumping data for table `#__citations_sponsors`
--


--
-- Dumping data for table `#__citations_sponsors_assoc`
--


--
-- Dumping data for table `#__citations_types`
--


--
-- Dumping data for table `#__collections`
--


--
-- Dumping data for table `#__collections_assets`
--


--
-- Dumping data for table `#__collections_following`
--


--
-- Dumping data for table `#__collections_items`
--


--
-- Dumping data for table `#__collections_posts`
--


--
-- Dumping data for table `#__collections_votes`
--


--
-- Dumping data for table `#__content`
--


--
-- Dumping data for table `#__content_frontpage`
--


--
-- Dumping data for table `#__content_rating`
--


--
-- Dumping data for table `#__core_log_searches`
--


--
-- Dumping data for table `#__courses`
--


--
-- Dumping data for table `#__courses_announcements`
--


--
-- Dumping data for table `#__courses_asset_associations`
--


--
-- Dumping data for table `#__courses_asset_group_types`
--


--
-- Dumping data for table `#__courses_asset_groups`
--


--
-- Dumping data for table `#__courses_asset_unity`
--


--
-- Dumping data for table `#__courses_asset_views`
--


--
-- Dumping data for table `#__courses_assets`
--


--
-- Dumping data for table `#__courses_certificates`
--


--
-- Dumping data for table `#__courses_form_answers`
--


--
-- Dumping data for table `#__courses_form_deployments`
--


--
-- Dumping data for table `#__courses_form_questions`
--


--
-- Dumping data for table `#__courses_form_respondent_progress`
--


--
-- Dumping data for table `#__courses_form_respondents`
--


--
-- Dumping data for table `#__courses_form_responses`
--


--
-- Dumping data for table `#__courses_forms`
--


--
-- Dumping data for table `#__courses_grade_book`
--


--
-- Dumping data for table `#__courses_grade_policies`
--

INSERT INTO `#__courses_grade_policies` (`id`, `description`, `threshold`, `exam_weight`, `quiz_weight`, `homework_weight`) VALUES (1,'An average exam score of 70% or greater is required to pass the class.  Quizzes and homeworks do not count toward the final score.',0.70,1.00,0.00,0.00);

--
-- Dumping data for table `#__courses_log`
--


--
-- Dumping data for table `#__courses_member_badges`
--


--
-- Dumping data for table `#__courses_member_notes`
--


--
-- Dumping data for table `#__courses_members`
--


--
-- Dumping data for table `#__courses_offering_section_badge_criteria`
--


--
-- Dumping data for table `#__courses_offering_section_badges`
--


--
-- Dumping data for table `#__courses_offering_section_codes`
--


--
-- Dumping data for table `#__courses_offering_section_dates`
--


--
-- Dumping data for table `#__courses_offering_sections`
--


--
-- Dumping data for table `#__courses_offerings`
--


--
-- Dumping data for table `#__courses_page_hits`
--


--
-- Dumping data for table `#__courses_pages`
--


--
-- Dumping data for table `#__courses_prerequisites`
--


--
-- Dumping data for table `#__courses_progress_factors`
--


--
-- Dumping data for table `#__courses_reviews`
--


--
-- Dumping data for table `#__courses_roles`
--

INSERT INTO `#__courses_roles` (`id`, `offering_id`, `alias`, `title`, `permissions`) VALUES (1,0,'instructor','Instructor','');
INSERT INTO `#__courses_roles` (`id`, `offering_id`, `alias`, `title`, `permissions`) VALUES (2,0,'manager','Manager','');
INSERT INTO `#__courses_roles` (`id`, `offering_id`, `alias`, `title`, `permissions`) VALUES (3,0,'student','Student','');

--
-- Dumping data for table `#__courses_units`
--


--
-- Dumping data for table `#__cron_jobs`
--


--
-- Dumping data for table `#__document_resource_rel`
--


--
-- Dumping data for table `#__document_text_data`
--


--
-- Dumping data for table `#__doi_mapping`
--


--
-- Dumping data for table `#__email_bounces`
--


--
-- Dumping data for table `#__event_registration`
--


--
-- Dumping data for table `#__events`
--


--
-- Dumping data for table `#__events_calendars`
--


--
-- Dumping data for table `#__events_categories`
--


--
-- Dumping data for table `#__events_config`
--


--
-- Dumping data for table `#__events_pages`
--


--
-- Dumping data for table `#__events_respondent_race_rel`
--


--
-- Dumping data for table `#__events_respondents`
--


--
-- Dumping data for table `#__extensions`
--

INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1,'com_mailto','component','com_mailto','',0,1,1,1,'','','','',0,'0000-00-00 00:00:00',0,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (3,'com_admin','component','com_admin','',1,1,1,1,'','','','',0,'0000-00-00 00:00:00',0,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (5,'com_cache','component','com_cache','',1,1,1,1,'','','','',0,'0000-00-00 00:00:00',0,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (6,'com_categories','component','com_categories','',1,1,1,1,'','','','',0,'0000-00-00 00:00:00',0,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (7,'com_checkin','component','com_checkin','',1,1,1,1,'','','','',0,'0000-00-00 00:00:00',0,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (9,'com_cpanel','component','com_cpanel','',1,1,1,1,'','','','',0,'0000-00-00 00:00:00',0,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (10,'com_installer','component','com_installer','',1,1,1,1,'','{}','','',0,'0000-00-00 00:00:00',0,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (11,'com_languages','component','com_languages','',1,1,1,1,'','{\"administrator\":\"en-GB\",\"site\":\"en-GB\"}','','',0,'0000-00-00 00:00:00',0,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (12,'com_login','component','com_login','',1,1,1,1,'','','','',0,'0000-00-00 00:00:00',0,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (13,'com_media','component','com_media','',1,1,0,1,'','{\"upload_extensions\":\"bmp,csv,doc,gif,ico,jpg,jpeg,odg,odp,ods,odt,pdf,png,ppt,swf,txt,xcf,xls,BMP,CSV,DOC,GIF,ICO,JPG,JPEG,ODG,ODP,ODS,ODT,PDF,PNG,PPT,SWF,TXT,XCF,XLS\",\"upload_maxsize\":\"10\",\"file_path\":\"images\",\"image_path\":\"images\",\"restrict_uploads\":\"1\",\"allowed_media_usergroup\":\"3\",\"check_mime\":\"1\",\"image_extensions\":\"bmp,gif,jpg,png\",\"ignore_extensions\":\"\",\"upload_mime\":\"image\\/jpeg,image\\/gif,image\\/png,image\\/bmp,application\\/x-shockwave-flash,application\\/msword,application\\/excel,application\\/pdf,application\\/powerpoint,text\\/plain,application\\/x-zip\",\"upload_mime_illegal\":\"text\\/html\"}','','',0,'0000-00-00 00:00:00',0,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (14,'com_menus','component','com_menus','',1,1,1,1,'','{}','','',0,'0000-00-00 00:00:00',0,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (15,'com_messages','component','com_messages','',1,1,1,1,'','','','',0,'0000-00-00 00:00:00',0,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (16,'com_modules','component','com_modules','',1,1,1,1,'','{}','','',0,'0000-00-00 00:00:00',0,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (17,'com_newsfeeds','component','com_newsfeeds','',1,1,1,0,'','{\"show_feed_image\":\"1\",\"show_feed_description\":\"1\",\"show_item_description\":\"1\",\"feed_word_count\":\"0\",\"show_headings\":\"1\",\"show_name\":\"1\",\"show_articles\":\"0\",\"show_link\":\"1\",\"show_description\":\"1\",\"show_description_image\":\"1\",\"display_num\":\"\",\"show_pagination_limit\":\"1\",\"show_pagination\":\"1\",\"show_pagination_results\":\"1\",\"show_cat_items\":\"1\"}','','',0,'0000-00-00 00:00:00',0,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (18,'com_plugins','component','com_plugins','',1,1,1,1,'','{}','','',0,'0000-00-00 00:00:00',0,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (20,'com_templates','component','com_templates','',1,1,1,1,'','{}','','',0,'0000-00-00 00:00:00',0,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (22,'com_content','component','com_content','',1,1,0,1,'{\"legacy\":false,\"name\":\"com_content\",\"type\":\"component\",\"creationDate\":\"April 2006\",\"author\":\"Joomla! Project\",\"copyright\":\"(C) 2005 - 2014 Open Source Matters. All rights reserved.\\t\",\"authorEmail\":\"admin@joomla.org\",\"authorUrl\":\"www.joomla.org\",\"version\":\"1.7.0\",\"description\":\"COM_CONTENT_XML_DESCRIPTION\",\"group\":\"\"}','{\"article_layout\":\"_:default\",\"show_title\":\"1\",\"link_titles\":\"1\",\"show_intro\":\"1\",\"show_category\":\"1\",\"link_category\":\"1\",\"show_parent_category\":\"0\",\"link_parent_category\":\"0\",\"show_author\":\"1\",\"link_author\":\"0\",\"show_create_date\":\"0\",\"show_modify_date\":\"0\",\"show_publish_date\":\"1\",\"show_item_navigation\":\"1\",\"show_vote\":\"0\",\"show_readmore\":\"1\",\"show_readmore_title\":\"1\",\"readmore_limit\":\"100\",\"show_icons\":\"1\",\"show_print_icon\":\"1\",\"show_email_icon\":\"1\",\"show_hits\":\"1\",\"show_noauth\":\"0\",\"show_publishing_options\":\"1\",\"show_article_options\":\"1\",\"show_urls_images_frontend\":\"0\",\"show_urls_images_backend\":\"1\",\"targeta\":0,\"targetb\":0,\"targetc\":0,\"float_intro\":\"left\",\"float_fulltext\":\"left\",\"category_layout\":\"_:blog\",\"show_category_title\":\"0\",\"show_description\":\"0\",\"show_description_image\":\"0\",\"maxLevel\":\"1\",\"show_empty_categories\":\"0\",\"show_no_articles\":\"1\",\"show_subcat_desc\":\"1\",\"show_cat_num_articles\":\"0\",\"show_base_description\":\"1\",\"maxLevelcat\":\"-1\",\"show_empty_categories_cat\":\"0\",\"show_subcat_desc_cat\":\"1\",\"show_cat_num_articles_cat\":\"1\",\"num_leading_articles\":\"1\",\"num_intro_articles\":\"4\",\"num_columns\":\"2\",\"num_links\":\"4\",\"multi_column_order\":\"0\",\"show_subcategory_content\":\"0\",\"show_pagination_limit\":\"1\",\"filter_field\":\"hide\",\"show_headings\":\"1\",\"list_show_date\":\"0\",\"date_format\":\"\",\"list_show_hits\":\"1\",\"list_show_author\":\"1\",\"orderby_pri\":\"order\",\"orderby_sec\":\"rdate\",\"order_date\":\"published\",\"show_pagination\":\"2\",\"show_pagination_results\":\"1\",\"show_feed_link\":\"1\",\"feed_summary\":\"0\"}','','',0,'0000-00-00 00:00:00',0,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (23,'com_config','component','com_config','',1,1,0,1,'{\"legacy\":false,\"name\":\"com_config\",\"type\":\"component\",\"creationDate\":\"April 2006\",\"author\":\"Joomla! Project\",\"copyright\":\"(C) 2005 - 2014 Open Source Matters. All rights reserved.\\t\",\"authorEmail\":\"admin@joomla.org\",\"authorUrl\":\"www.joomla.org\",\"version\":\"1.7.0\",\"description\":\"COM_CONFIG_XML_DESCRIPTION\",\"group\":\"\"}','{\"filters\":{\"1\":{\"filter_type\":\"NH\",\"filter_tags\":\"\",\"filter_attributes\":\"\"},\"6\":{\"filter_type\":\"BL\",\"filter_tags\":\"\",\"filter_attributes\":\"\"},\"7\":{\"filter_type\":\"NONE\",\"filter_tags\":\"\",\"filter_attributes\":\"\"},\"2\":{\"filter_type\":\"NH\",\"filter_tags\":\"\",\"filter_attributes\":\"\"},\"3\":{\"filter_type\":\"BL\",\"filter_tags\":\"\",\"filter_attributes\":\"\"},\"4\":{\"filter_type\":\"BL\",\"filter_tags\":\"\",\"filter_attributes\":\"\"},\"5\":{\"filter_type\":\"BL\",\"filter_tags\":\"\",\"filter_attributes\":\"\"},\"10\":{\"filter_type\":\"BL\",\"filter_tags\":\"\",\"filter_attributes\":\"\"},\"12\":{\"filter_type\":\"BL\",\"filter_tags\":\"\",\"filter_attributes\":\"\"},\"8\":{\"filter_type\":\"NONE\",\"filter_tags\":\"\",\"filter_attributes\":\"\"}}}','','',0,'0000-00-00 00:00:00',0,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (24,'com_redirect','component','com_redirect','',1,1,0,1,'','{}','','',0,'0000-00-00 00:00:00',0,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (25,'com_users','component','com_users','',1,1,0,1,'{\"legacy\":false,\"name\":\"com_users\",\"type\":\"component\",\"creationDate\":\"April 2006\",\"author\":\"Joomla! Project\",\"copyright\":\"(C) 2005 - 2014 Open Source Matters. All rights reserved.\\t\",\"authorEmail\":\"admin@joomla.org\",\"authorUrl\":\"www.joomla.org\",\"version\":\"2.5.0\",\"description\":\"COM_USERS_XML_DESCRIPTION\",\"group\":\"\"}','{\"allowUserRegistration\":\"1\",\"new_usertype\":\"2\",\"guest_usergroup\":\"1\",\"sendpassword\":\"1\",\"useractivation\":\"2\",\"mail_to_admin\":\"0\",\"captcha\":\"\",\"frontend_userparams\":\"1\",\"site_language\":\"0\",\"change_login_name\":\"0\",\"reset_count\":\"10\",\"reset_time\":\"1\",\"mailSubjectPrefix\":\"\",\"mailBodySuffix\":\"\"}','','',0,'0000-00-00 00:00:00',0,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (28,'com_joomlaupdate','component','com_joomlaupdate','',1,1,0,1,'{\"legacy\":false,\"name\":\"com_joomlaupdate\",\"type\":\"component\",\"creationDate\":\"February 2012\",\"author\":\"Joomla! Project\",\"copyright\":\"(C) 2005 - 2014 Open Source Matters. All rights reserved.\",\"authorEmail\":\"admin@joomla.org\",\"authorUrl\":\"www.joomla.org\",\"version\":\"2.5.0\",\"description\":\"COM_JOOMLAUPDATE_XML_DESCRIPTION\",\"group\":\"\"}','{}','','',0,'0000-00-00 00:00:00',0,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (101,'SimplePie','library','simplepie','',0,1,1,1,'','','','',0,'0000-00-00 00:00:00',0,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (103,'Joomla! Platform','library','joomla','',0,1,1,1,'{\"legacy\":false,\"name\":\"Joomla! Platform\",\"type\":\"library\",\"creationDate\":\"2008\",\"author\":\"Joomla! Project\",\"copyright\":\"Copyright (C) 2005 - 2014 Open Source Matters. All rights reserved.\",\"authorEmail\":\"admin@joomla.org\",\"authorUrl\":\"http:\\/\\/www.joomla.org\",\"version\":\"11.4\",\"description\":\"LIB_JOOMLA_XML_DESCRIPTION\",\"group\":\"\"}','{}','','',0,'0000-00-00 00:00:00',0,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (200,'mod_articles_archive','module','mod_articles_archive','',0,1,1,1,'','','','',0,'0000-00-00 00:00:00',0,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (201,'mod_articles_latest','module','mod_articles_latest','',0,1,1,1,'','','','',0,'0000-00-00 00:00:00',0,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (202,'mod_articles_popular','module','mod_articles_popular','',0,1,1,0,'','','','',0,'0000-00-00 00:00:00',0,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (204,'mod_breadcrumbs','module','mod_breadcrumbs','',0,1,1,1,'','','','',0,'0000-00-00 00:00:00',0,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (205,'mod_custom','module','mod_custom','',0,1,1,1,'','','','',0,'0000-00-00 00:00:00',0,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (206,'mod_feed','module','mod_feed','',0,1,1,1,'','','','',0,'0000-00-00 00:00:00',0,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (207,'mod_footer','module','mod_footer','',0,1,1,1,'','','','',0,'0000-00-00 00:00:00',0,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (208,'mod_login','module','mod_login','',0,1,1,1,'','','','',0,'0000-00-00 00:00:00',0,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (209,'mod_menu','module','mod_menu','',0,1,1,1,'','','','',0,'0000-00-00 00:00:00',0,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (210,'mod_articles_news','module','mod_articles_news','',0,1,1,0,'','','','',0,'0000-00-00 00:00:00',0,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (211,'mod_random_image','module','mod_random_image','',0,1,1,0,'','','','',0,'0000-00-00 00:00:00',0,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (212,'mod_related_items','module','mod_related_items','',0,1,1,0,'','','','',0,'0000-00-00 00:00:00',0,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (213,'mod_search','module','mod_search','',0,1,1,0,'','','','',0,'0000-00-00 00:00:00',0,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (214,'mod_stats','module','mod_stats','',0,1,1,0,'','','','',0,'0000-00-00 00:00:00',0,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (215,'mod_syndicate','module','mod_syndicate','',0,1,1,1,'','','','',0,'0000-00-00 00:00:00',0,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (216,'mod_users_latest','module','mod_users_latest','',0,1,1,1,'','','','',0,'0000-00-00 00:00:00',0,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (218,'mod_whosonline','module','mod_whosonline','',0,1,1,0,'','','','',0,'0000-00-00 00:00:00',0,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (219,'mod_wrapper','module','mod_wrapper','',0,1,1,0,'','','','',0,'0000-00-00 00:00:00',0,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (220,'mod_articles_category','module','mod_articles_category','',0,1,1,1,'','','','',0,'0000-00-00 00:00:00',0,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (221,'mod_articles_categories','module','mod_articles_categories','',0,1,1,1,'','','','',0,'0000-00-00 00:00:00',0,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (222,'mod_languages','module','mod_languages','',0,1,1,1,'','','','',0,'0000-00-00 00:00:00',0,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (300,'mod_custom','module','mod_custom','',1,1,1,1,'','','','',0,'0000-00-00 00:00:00',0,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (301,'mod_feed','module','mod_feed','',1,1,1,0,'','','','',0,'0000-00-00 00:00:00',0,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (302,'mod_latest','module','mod_latest','',1,1,1,0,'','','','',0,'0000-00-00 00:00:00',0,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (304,'mod_login','module','mod_login','',1,1,1,1,'','','','',0,'0000-00-00 00:00:00',0,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (305,'mod_menu','module','mod_menu','',1,1,1,0,'','','','',0,'0000-00-00 00:00:00',0,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (307,'mod_popular','module','mod_popular','',1,1,1,0,'','','','',0,'0000-00-00 00:00:00',0,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (308,'mod_quickicon','module','mod_quickicon','',1,1,1,1,'','','','',0,'0000-00-00 00:00:00',0,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (310,'mod_submenu','module','mod_submenu','',1,1,1,0,'','','','',0,'0000-00-00 00:00:00',0,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (311,'mod_title','module','mod_title','',1,1,1,0,'','','','',0,'0000-00-00 00:00:00',0,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (312,'mod_toolbar','module','mod_toolbar','',1,1,1,1,'','','','',0,'0000-00-00 00:00:00',0,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (313,'mod_multilangstatus','module','mod_multilangstatus','',1,1,1,0,'{\"legacy\":false,\"name\":\"mod_multilangstatus\",\"type\":\"module\",\"creationDate\":\"September 2011\",\"author\":\"Joomla! Project\",\"copyright\":\"Copyright (C) 2005 - 2014 Open Source Matters. All rights reserved.\",\"authorEmail\":\"admin@joomla.org\",\"authorUrl\":\"www.joomla.org\",\"version\":\"1.7.1\",\"description\":\"MOD_MULTILANGSTATUS_XML_DESCRIPTION\",\"group\":\"\"}','{\"cache\":\"0\"}','','',0,'0000-00-00 00:00:00',0,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (314,'mod_version','module','mod_version','',1,1,1,0,'{\"legacy\":false,\"name\":\"mod_version\",\"type\":\"module\",\"creationDate\":\"January 2012\",\"author\":\"Joomla! Project\",\"copyright\":\"Copyright (C) 2005 - 2014 Open Source Matters. All rights reserved.\",\"authorEmail\":\"admin@joomla.org\",\"authorUrl\":\"www.joomla.org\",\"version\":\"2.5.0\",\"description\":\"MOD_VERSION_XML_DESCRIPTION\",\"group\":\"\"}','{\"format\":\"short\",\"product\":\"1\",\"cache\":\"0\"}','','',0,'0000-00-00 00:00:00',0,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (404,'plg_content_emailcloak','plugin','emailcloak','content',0,1,1,0,'','{\"mode\":\"1\"}','','',0,'0000-00-00 00:00:00',1,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (405,'plg_content_geshi','plugin','geshi','content',0,0,1,0,'','{}','','',0,'0000-00-00 00:00:00',2,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (406,'plg_content_loadmodule','plugin','loadmodule','content',0,1,1,0,'{\"legacy\":false,\"name\":\"plg_content_loadmodule\",\"type\":\"plugin\",\"creationDate\":\"November 2005\",\"author\":\"Joomla! Project\",\"copyright\":\"Copyright (C) 2005 - 2014 Open Source Matters. All rights reserved.\",\"authorEmail\":\"admin@joomla.org\",\"authorUrl\":\"www.joomla.org\",\"version\":\"1.7.0\",\"description\":\"PLG_LOADMODULE_XML_DESCRIPTION\",\"group\":\"\"}','{\"style\":\"xhtml\"}','','',0,'2011-09-18 15:22:50',0,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (407,'plg_content_pagebreak','plugin','pagebreak','content',0,1,1,1,'','{\"title\":\"1\",\"multipage_toc\":\"1\",\"showall\":\"1\"}','','',0,'0000-00-00 00:00:00',4,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (408,'plg_content_pagenavigation','plugin','pagenavigation','content',0,1,1,1,'','{\"position\":\"1\"}','','',0,'0000-00-00 00:00:00',5,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (409,'plg_content_vote','plugin','vote','content',0,1,1,1,'','{}','','',0,'0000-00-00 00:00:00',6,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (410,'plg_editors_codemirror','plugin','codemirror','editors',0,1,1,1,'','{\"linenumbers\":\"0\",\"tabmode\":\"indent\"}','','',0,'0000-00-00 00:00:00',1,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (411,'plg_editors_none','plugin','none','editors',0,1,1,1,'','{}','','',0,'0000-00-00 00:00:00',2,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (412,'plg_editors_tinymce','plugin','tinymce','editors',0,1,1,1,'{\"legacy\":false,\"name\":\"plg_editors_tinymce\",\"type\":\"plugin\",\"creationDate\":\"2005-2011\",\"author\":\"Moxiecode Systems AB\",\"copyright\":\"Moxiecode Systems AB\",\"authorEmail\":\"N\\/A\",\"authorUrl\":\"tinymce.moxiecode.com\\/\",\"version\":\"3.4.7\",\"description\":\"PLG_TINY_XML_DESCRIPTION\",\"group\":\"\"}','{\"mode\":\"1\",\"skin\":\"0\",\"entity_encoding\":\"raw\",\"lang_mode\":\"0\",\"lang_code\":\"en\",\"text_direction\":\"ltr\",\"content_css\":\"1\",\"content_css_custom\":\"\",\"relative_urls\":\"1\",\"newlines\":\"0\",\"invalid_elements\":\"script,applet,iframe\",\"extended_elements\":\"\",\"toolbar\":\"top\",\"toolbar_align\":\"left\",\"html_height\":\"550\",\"html_width\":\"750\",\"resizing\":\"true\",\"resize_horizontal\":\"false\",\"element_path\":\"1\",\"fonts\":\"1\",\"paste\":\"1\",\"searchreplace\":\"1\",\"insertdate\":\"1\",\"format_date\":\"%Y-%m-%d\",\"inserttime\":\"1\",\"format_time\":\"%H:%M:%S\",\"colors\":\"1\",\"table\":\"1\",\"smilies\":\"1\",\"media\":\"1\",\"hr\":\"1\",\"directionality\":\"1\",\"fullscreen\":\"1\",\"style\":\"1\",\"layer\":\"1\",\"xhtmlxtras\":\"1\",\"visualchars\":\"1\",\"nonbreaking\":\"1\",\"template\":\"1\",\"blockquote\":\"1\",\"wordcount\":\"1\",\"advimage\":\"1\",\"advlink\":\"1\",\"advlist\":\"1\",\"autosave\":\"1\",\"contextmenu\":\"1\",\"inlinepopups\":\"1\",\"custom_plugin\":\"\",\"custom_button\":\"\"}','','',0,'0000-00-00 00:00:00',3,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (413,'plg_editors-xtd_article','plugin','article','editors-xtd',0,1,1,1,'','{}','','',0,'0000-00-00 00:00:00',1,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (414,'plg_editors-xtd_image','plugin','image','editors-xtd',0,1,1,0,'','{}','','',0,'0000-00-00 00:00:00',2,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (415,'plg_editors-xtd_pagebreak','plugin','pagebreak','editors-xtd',0,1,1,0,'','{}','','',0,'0000-00-00 00:00:00',3,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (416,'plg_editors-xtd_readmore','plugin','readmore','editors-xtd',0,1,1,0,'','{}','','',0,'0000-00-00 00:00:00',4,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (422,'plg_system_languagefilter','plugin','languagefilter','system',0,0,1,1,'','{}','','',0,'0000-00-00 00:00:00',1,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (423,'plg_system_p3p','plugin','p3p','system',0,1,1,1,'','{\"headers\":\"NOI ADM DEV PSAi COM NAV OUR OTRo STP IND DEM\"}','','',0,'0000-00-00 00:00:00',2,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (424,'plg_system_cache','plugin','cache','system',0,0,1,1,'','{\"browsercache\":\"0\",\"cachetime\":\"15\"}','','',0,'0000-00-00 00:00:00',9,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (425,'plg_system_debug','plugin','debug','system',0,1,1,0,'','{\"profile\":\"1\",\"queries\":\"1\",\"memory\":\"1\",\"language_files\":\"1\",\"language_strings\":\"1\",\"strip-first\":\"1\",\"strip-prefix\":\"\",\"strip-suffix\":\"\"}','','',0,'0000-00-00 00:00:00',4,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (426,'plg_system_log','plugin','log','system',0,1,1,1,'','{}','','',0,'0000-00-00 00:00:00',5,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (427,'plg_system_redirect','plugin','redirect','system',0,1,1,1,'','{}','','',0,'0000-00-00 00:00:00',6,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (428,'plg_system_remember','plugin','remember','system',0,1,1,1,'','{}','','',0,'0000-00-00 00:00:00',7,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (429,'plg_system_sef','plugin','sef','system',0,1,1,0,'','{}','','',0,'0000-00-00 00:00:00',8,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (430,'plg_system_logout','plugin','logout','system',0,1,1,1,'','{}','','',0,'0000-00-00 00:00:00',3,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (432,'plg_user_joomla','plugin','joomla','user',0,1,1,0,'','{\"autoregister\":\"1\"}','','',0,'0000-00-00 00:00:00',2,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (433,'plg_user_profile','plugin','profile','user',0,0,1,1,'','{\"register-require_address1\":\"1\",\"register-require_address2\":\"1\",\"register-require_city\":\"1\",\"register-require_region\":\"1\",\"register-require_country\":\"1\",\"register-require_postal_code\":\"1\",\"register-require_phone\":\"1\",\"register-require_website\":\"1\",\"register-require_favoritebook\":\"1\",\"register-require_aboutme\":\"1\",\"register-require_tos\":\"1\",\"register-require_dob\":\"1\",\"profile-require_address1\":\"1\",\"profile-require_address2\":\"1\",\"profile-require_city\":\"1\",\"profile-require_region\":\"1\",\"profile-require_country\":\"1\",\"profile-require_postal_code\":\"1\",\"profile-require_phone\":\"1\",\"profile-require_website\":\"1\",\"profile-require_favoritebook\":\"1\",\"profile-require_aboutme\":\"1\",\"profile-require_tos\":\"1\",\"profile-require_dob\":\"1\"}','','',0,'0000-00-00 00:00:00',0,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (434,'plg_extension_joomla','plugin','joomla','extension',0,1,1,1,'','{}','','',0,'0000-00-00 00:00:00',1,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (435,'plg_content_joomla','plugin','joomla','content',0,1,1,0,'','{}','','',0,'0000-00-00 00:00:00',0,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (436,'plg_system_languagecode','plugin','languagecode','system',0,0,1,0,'','{}','','',0,'0000-00-00 00:00:00',10,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (437,'plg_quickicon_joomlaupdate','plugin','joomlaupdate','quickicon',0,1,1,1,'','{}','','',0,'0000-00-00 00:00:00',0,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (438,'plg_quickicon_extensionupdate','plugin','extensionupdate','quickicon',0,1,1,1,'','{}','','',0,'0000-00-00 00:00:00',0,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (439,'plg_captcha_recaptcha','plugin','recaptcha','captcha',0,0,1,0,'{}','{\"public_key\":\"\",\"private_key\":\"\",\"theme\":\"clean\"}','','',0,'0000-00-00 00:00:00',0,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (440,'plg_system_highlight','plugin','highlight','system',0,1,1,0,'','{}','','',0,'0000-00-00 00:00:00',7,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (600,'English (United Kingdom)','language','en-GB','',0,1,1,1,'','','','',0,'0000-00-00 00:00:00',0,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (601,'English (United Kingdom)','language','en-GB','',1,1,1,1,'','','','',0,'0000-00-00 00:00:00',0,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (700,'Joomla! CMS','file','joomla','',0,1,1,1,'','','','',0,'0000-00-00 00:00:00',0,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (800,'joomla','package','pkg_joomla','',0,1,1,1,'','','','',0,'0000-00-00 00:00:00',0,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1000,'com_answers','component','com_answers','',1,1,1,0,'','{\"infolink\":\"\\/kb\\/points\",\"notify_users\":\"\"}','','',0,'0000-00-00 00:00:00',0,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1001,'com_billboards','component','com_billboards','',1,1,1,0,'','','','',0,'0000-00-00 00:00:00',0,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1002,'com_blog','component','com_blog','',1,1,1,0,'','{\"title\":\"\",\"uploadpath\":\"\\/site\\/blog\",\"show_authors\":\"1\",\"allow_comments\":\"1\",\"feeds_enabled\":\"1\",\"feed_entries\":\"partial\",\"show_date\":\"3\"}','','',0,'0000-00-00 00:00:00',0,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1003,'com_citations','component','com_citations','',1,1,1,0,'','{\"citation_label\":\"number\",\"citation_rollover\":\"no\",\"citation_sponsors\":\"yes\",\"citation_import\":\"2\",\"citation_bulk_import\":\"2\",\"citation_download\":\"1\",\"citation_batch_download\":\"1\",\"citation_download_exclude\":\"\",\"citation_coins\":\"1\",\"citation_openurl\":\"1\",\"citation_url\":\"url\",\"citation_custom_url\":\"\",\"citation_cited\":\"0\",\"citation_cited_single\":\"\",\"citation_cited_multiple\":\"\",\"citation_show_tags\":\"no\",\"citation_allow_tags\":\"no\",\"citation_show_badges\":\"no\",\"citation_allow_badges\":\"no\",\"citation_format\":\"\"}','','',0,'0000-00-00 00:00:00',0,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1004,'com_courses','component','com_courses','',1,1,1,0,'','{\"uploadpath\":\"\\/site\\/courses\",\"tmpl\":\"\",\"default_asset_groups\":\"Lectures, Activities, Exam\",\"auto_approve\":\"1\",\"email_comment_processing\":\"0\",\"email_member_coursesidcussionemail_autosignup\":\"0\",\"intro_mycourses\":\"1\",\"intro_interestingcourses\":\"1\",\"intro_popularcourses\":\"1\"}','','',0,'0000-00-00 00:00:00',0,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1005,'com_cron','component','com_cron','',1,1,1,0,'',' ','','',0,'0000-00-00 00:00:00',0,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1006,'com_events','component','com_events','',1,1,1,0,'','','','',0,'0000-00-00 00:00:00',0,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1008,'com_feedback','component','com_feedback','',1,1,1,0,'','{\"defaultpic\":\"\\/components\\/com_feedback\\/assets\\/img\\/contributor.gif\",\"uploadpath\":\"\\/site\\/quotes\",\"maxAllowed\":\"40000000\",\"file_ext\":\"jpg,jpeg,jpe,bmp,tif,tiff,png,gif\",\"blacklist\":\"\",\"badwords\":\"viagra, pharmacy, xanax, phentermine, dating, ringtones, tramadol, hydrocodone, levitra, ambien, vicodin, fioricet, diazepam, cash advance, free online, online gambling, online prescriptions, debt consolidation, baccarat, loan, slots, credit, mortgage, casino, slot, texas holdem, teen nude, orgasm, gay, fuck, crap, shit, asshole, cunt, fucker, fuckers, motherfucker, fucking, milf, cocksucker, porno, videosex, sperm, hentai, internet gambling, kasino, kasinos, poker, lottery, texas hold em, texas holdem, fisting\"}','','',0,'0000-00-00 00:00:00',0,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1009,'com_forum','component','com_forum','',1,1,1,0,'','','','',0,'0000-00-00 00:00:00',0,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1010,'com_groups','component','com_groups','',1,1,1,0,'','{\"ldapGroupMirror\":\"1\",\"ldapGroupLegacy\":\"1\",\"uploadpath\":\"\\/site\\/groups\",\"iconpath\":\"\\/components\\/com_groups\\/assets\\/img\\/icons\",\"join_policy\":\"0\",\"privacy\":\"0\",\"auto_approve\":\"1\",\"display_system_users\":\"no\",\"email_comment_processing\":\"1\",\"email_member_groupsidcussionemail_autosignup\":\"0\"}','','',0,'0000-00-00 00:00:00',0,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1011,'com_help','component','com_help','',1,1,1,0,'','','','',0,'0000-00-00 00:00:00',0,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1012,'com_jobs','component','com_jobs','',1,1,1,0,'','{\"component_enabled\":\"1\",\"industry\":\"\",\"admingroup\":\"\",\"specialgroup\":\"jobsadmin\",\"autoapprove\":\"1\",\"defaultsort\":\"category\",\"jobslimit\":\"25\",\"maxads\":\"3\",\"allowsubscriptions\":\"1\",\"usonly\":\"0\",\"usegoogle\":\"0\",\"banking\":\"1\",\"promoline\":\"For a limited time: FREE Employer Services Basic subscription\",\"infolink\":\"kb\\/jobs\",\"premium_infolink\":\"\"}','','',0,'0000-00-00 00:00:00',0,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1013,'com_kb','component','com_kb','',1,1,1,0,'','','','',0,'0000-00-00 00:00:00',0,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1014,'com_members','component','com_members','',1,1,1,0,'','{\"privacy\":\"1\",\"bankAccounts\":\"1\",\"defaultpic\":\"\\/components\\/com_members\\/assets\\/img\\/profile.gif\",\"webpath\":\"\\/site\\/members\",\"homedir\":\"\",\"maxAllowed\":\"40000000\",\"file_ext\":\"jpg,jpeg,jpe,bmp,tif,tiff,png,gif\",\"user_messaging\":\"1\",\"employeraccess\":\"0\",\"gidNumber\":\"100\",\"gid\":\"public\",\"shadowMax\":\"120\",\"shadowMin\":\"0\",\"shadowWarning\":\"7\",\"LoginReturn\":\"\\/members\\/myaccount\",\"ConfirmationReturn\":\"\\/members\\/myaccount\",\"passwordMeter\":\"0\",\"registrationUsername\":\"RRUU\",\"registrationPassword\":\"RRUU\",\"registrationConfirmPassword\":\"RRUU\",\"registrationFullname\":\"RRUU\",\"registrationEmail\":\"RRUU\",\"registrationConfirmEmail\":\"RRUU\",\"registrationURL\":\"HOHO\",\"registrationPhone\":\"HOHO\",\"registrationEmployment\":\"HOHO\",\"registrationOrganization\":\"HOHO\",\"registrationCitizenship\":\"HHHR\",\"registrationResidency\":\"HHHR\",\"registrationSex\":\"HHHH\",\"registrationDisability\":\"HHHH\",\"registrationHispanic\":\"HHHH\",\"registrationRace\":\"HHHR\",\"registrationInterests\":\"HOHO\",\"registrationReason\":\"HOHO\",\"registrationOptIn\":\"HOHO\",\"registrationCAPTCHA\":\"RHHH\",\"registrationTOU\":\"RHRH\",\"registrationORCID\":\"HHHH\"}','','',0,'0000-00-00 00:00:00',0,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1015,'com_newsletter','component','com_newsletter','',1,1,1,0,'','','','',0,'0000-00-00 00:00:00',0,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1016,'com_oaipmh','component','com_oaipmh','',1,1,1,1,'{}','{}','','',0,'0000-00-00 00:00:00',0,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1017,'com_poll','component','com_poll','',1,1,1,0,'','','','',0,'0000-00-00 00:00:00',0,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1018,'com_dataviewer','component','com_dataviewer','',1,1,1,0,'{\"legacy\":true,\"name\":\"Dataviewer\",\"type\":\"component\",\"creationDate\":\"2013-08-07\",\"author\":\"Sudheera R. Fernando\",\"copyright\":\"Copyright (c) 2010-2020 The Regents of the University of California.\",\"authorEmail\":\"srf@xconsole.org\",\"authorUrl\":\"\",\"version\":\"2.0.2\",\"description\":\"Dataviewer for HUB Databases\",\"group\":\"\"}','','','',0,'0000-00-00 00:00:00',0,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1019,'com_projects','component','com_projects','',1,1,1,1,'{}','component_on=1\ngrantinfo=0\nconfirm_step=0\nedit_settings=1\nrestricted_data=0\nrestricted_upfront=0\napprove_restricted=0\nprivacylink=/legal/privacy\nHIPAAlink=/legal/privacy\nFERPAlink=/legal/privacy\ncreatorgroup=\nadmingroup=projectsadmin\nsdata_group=hipaa_reviewers\nginfo_group=sps_reviewers\nmin_name_length=6\nmax_name_length=25\nreserved_names=clone, temp, test\nwebpath=/srv/projects\noffroot=1\ngitpath=/usr/bin/git\ngitclone=/site/projects/clone/.git\nmaxUpload=104857600\ndefaultQuota=1\npremiumQuota=1\napproachingQuota=90\npubQuota=1\npremiumPubQuota=1\nimagepath=/site/projects\ndefaultpic=/components/com_projects/assets/img/project.png\nimg_maxAllowed=5242880\nimg_file_ext=jpg,jpeg,jpe,bmp,tif,tiff,png,gif\nlogging=0\nmessaging=1\nprivacy=1\nlimit=25\nsidebox_limit=3\ngroup_prefix=pr-\nuse_alias=1\ndocumentation=/projects/features\ndbcheck=1','','',0,'0000-00-00 00:00:00',0,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1020,'com_publications','component','com_publications','',1,1,1,1,'{}','enabled=1\nautoapprove=1\nautoapproved_users=\nemail=0\ndefault_category=dataset\ndefaultpic=/components/com_publications/assets/img/resource_thumb.gif\ntoolpic=/components/com_publications/assets/img/tool_thumb.gif\nvideo_thumb=/components/com_publications/images/video_thumb.gif\ngallery_thumb=/components/com_publications/images/gallery_thumb.gif\nwebpath=/site/publications\naboutdoi=\ndoi_shoulder=\ndoi_prefix=\ndoi_service=\ndoi_userpw=\ndoi_xmlschema=\ndoi_publisher=\ndoi_resolve=https://doi.org/\ndoi_verify=http://n2t.net/ezid/id/\nsupportedtag=\nsupportedlink=\ngoogle_id=\nshow_authors=1\nshow_ranking=1\nshow_rating=1\nshow_date=3\nshow_citation=1\npanels=content, description, authors, audience, gallery, tags, access, license, notes\nsuggest_licence=0\nshow_tags=1\nshow_metadata=1\nshow_notes=1\nshow_license=1\nshow_access=0\nshow_gallery=1\nshow_audience=0\naudiencelink=\ndocumentation=/kb/publications\ndeposit_terms=/legal/termsofdeposit\ndbcheck=0\nrepository=0\naip_path=/srv/AIP','','',0,'0000-00-00 00:00:00',0,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1022,'com_resources','component','com_resources','',1,1,1,0,'','{\"autoapprove\":\"0\",\"autoapproved_users\":\"\",\"cc_license\":\"1\",\"email_when_approved\":\"0\",\"defaultpic\":\"\\/components\\/com_resources\\/images\\/resource_thumb.gif\",\"tagstool\":\"screenshots,poweredby,bio,credits,citations,sponsoredby,references,publications\",\"tagsothr\":\"bio,credits,citations,sponsoredby,references,publications\",\"accesses\":\"Public,Registered,Special,Protected,Private\",\"webpath\":\"\\/site\\/resources\",\"toolpath\":\"\\/site\\/resources\\/tools\",\"uploadpath\":\"\\/site\\/resources\",\"maxAllowed\":\"40000000\",\"file_ext\":\"jpg,jpeg,jpe,bmp,tif,tiff,png,gif,pdf,zip,mpg,mpeg,avi,mov,wmv,asf,asx,ra,rm,txt,rtf,doc,xsl,html,js,wav,mp3,eps,ppt,pps,swf,tar,tex,gz\",\"doi\":\"\",\"aboutdoi\":\"\",\"supportedtag\":\"\",\"supportedlink\":\"\",\"browsetags\":\"on\",\"google_id\":\"\",\"show_authors\":\"1\",\"show_assocs\":\"1\",\"show_ranking\":\"0\",\"show_rating\":\"1\",\"show_date\":\"3\",\"show_metadata\":\"1\",\"show_citation\":\"1\",\"show_audience\":\"0\",\"audiencelink\":\"\"}','','',0,'0000-00-00 00:00:00',0,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1023,'com_services','component','com_services','',1,1,1,0,'','{\"autoapprove\":\"1\"}','','',0,'0000-00-00 00:00:00',0,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1024,'com_store','component','com_store','',1,1,1,0,'','{\"store_enabled\":\"1\",\"webpath\":\"\\/site\\/store\",\"hubaddress_ln1\":\"\",\"hubaddress_ln2\":\"\",\"hubaddress_ln3\":\"\",\"hubaddress_ln4\":\"\",\"hubaddress_ln5\":\"\",\"hubemail\":\"\",\"hubphone\":\"\",\"headertext_ln1\":\"\",\"headertext_ln2\":\"\",\"footertext\":\"\",\"receipt_title\":\"Your Order at HUB Store\",\"receipt_note\":\"Thank You for contributing to our HUB!\"}','','',0,'0000-00-00 00:00:00',0,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1025,'com_support','component','com_support','',1,1,1,0,'','{\"feed_summary\":\"0\",\"severities\":\"critical,major,normal,minor,trivial\",\"webpath\":\"\\/site\\/tickets\",\"maxAllowed\":\"40000000\",\"file_ext\":\"jpg,jpeg,jpe,bmp,tif,tiff,png,gif,pdf,zip,mpg,mpeg,avi,mov,wmv,asf,asx,ra,rm,txt,rtf,doc,xsl,html,js,wav,mp3,eps,ppt,pps,swf,tar,tex,gz\",\"group\":\"\",\"emails\":\"{config.mailfrom}\",\"0\":\"\",\"blacklist\":\"\",\"badwords\":\"viagra, pharmacy, xanax, phentermine, dating, ringtones, tramadol, hydrocodone, levitra, ambien, vicodin, fioricet, diazepam, cash advance, free online, online gambling, online prescriptions, debt consolidation, baccarat, loan, slots, credit, mortgage, casino, slot, texas holdem, teen nude, orgasm, gay, fuck, crap, shit, asshole, cunt, fucker, fuckers, motherfucker, fucking, milf, cocksucker, porno, videosex, sperm, hentai, internet gambling, kasino, kasinos, poker, lottery, texas hold em, texas holdem, fisting\",\"email_processing\":\"1\"}','','',0,'0000-00-00 00:00:00',0,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1026,'com_system','component','com_system','',1,1,1,0,'','{\"geodb_driver\":\"mysql\",\"geodb_host\":\"\",\"geodb_port\":\"\",\"geodb_user\":\"\",\"geodb_password\":\"\",\"geodb_database\":\"\",\"geodb_prefix\":\"\",\"ldap_primary\":\"ldap:\\/\\/127.0.0.1\",\"ldap_secondary\":\"\",\"ldap_basedn\":\"\",\"ldap_searchdn\":\"\",\"ldap_searchpw\":\"\",\"ldap_managerdn\":\"\",\"ldap_managerpw\":\"\",\"ldap_tls\":\"0\"}','','',0,'0000-00-00 00:00:00',0,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1027,'com_tags','component','com_tags','',1,1,1,0,'','','','',0,'0000-00-00 00:00:00',0,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1028,'com_tools','component','com_tools','',1,1,1,0,'','{\"mw_on\":\"1\",\"mw_redirect\":\"\\/home\",\"stopRedirect\":\"index.php?option=com_members&task=myaccount\",\"mwDBDriver\":\"\",\"mwDBHost\":\"\",\"mwDBPort\":\"\",\"mwDBUsername\":\"\",\"mwDBPassword\":\"\",\"mwDBDatabase\":\"\",\"mwDBPrefix\":\"\",\"shareable\":\"1\",\"warn_multiples\":\"0\",\"storagehost\":\"tcp:\\/\\/localhost:300\",\"show_storage\":\"0\",\"params_whitelist\":\"\",\"contribtool_on\":\"1\",\"contribtool_redirect\":\"\\/home\",\"launch_ipad\":\"0\",\"admingroup\":\"apps\",\"default_mw\":\"narwhal\",\"default_vnc\":\"780x600\",\"developer_site\":\"Forge\",\"project_path\":\"\\/tools\\/\",\"invokescript_dir\":\"\\/apps\",\"dev_suffix\":\"_dev\",\"group_prefix\":\"app-\",\"sourcecodePath\":\"site\\/protected\\/source\",\"learn_url\":\"http:\\/\\/rappture.org\\/wiki\\/FAQ_UpDownloadSrc\",\"rappture_url\":\"http:\\/\\/rappture.org\",\"demo_url\":\"\",\"new_doi\":\"0\",\"doi_newservice\":\"\",\"doi_shoulder\":\"\",\"doi_newprefix\":\"\",\"doi_publisher\":\"\",\"doi_resolve\":\"http:\\/\\/dx.doi.org\\/\",\"doi_verify\":\"http:\\/\\/n2t.net\\/ezid\\/id\\/\",\"exec_pu\":\"1\",\"screenshot_edit\":\"0\",\"downloadable_on\":\"0\"}','','',0,'0000-00-00 00:00:00',0,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1030,'com_usage','component','com_usage','',1,1,1,0,'','{\"statsDBDriver\":\"mysql\",\"statsDBHost\":\"\",\"statsDBPort\":\"\",\"statsDBUsername\":\"\",\"statsDBPassword\":\"\",\"statsDBDatabase\":\"\",\"statsDBPrefix\":\"\",\"mapsApiKey\":\"\",\"stats_path\":\"\\/site\\/stats\",\"maps_path\":\"\\/site\\/stats\\/maps\",\"plots_path\":\"\\/site\\/stats\\/plots\",\"charts_path\":\"\\/site\\/stats\\/plots\"}','','',0,'0000-00-00 00:00:00',0,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1031,'com_whatsnew','component','com_whatsnew','',1,1,1,0,'','','','',0,'0000-00-00 00:00:00',0,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1032,'com_wiki','component','com_wiki','',1,1,1,0,'','{\"subpage_separator\":\"\\/\",\"homepage\":\"MainPage\",\"max_pagename_length\":\"100\",\"filepath\":\"\\/site\\/wiki\",\"mathpath\":\"\\/site\\/wiki\\/math\",\"tmppath\":\"\\/site\\/wiki\\/tmp\",\"maxAllowed\":\"40000000\",\"img_ext\":\"jpg,jpeg,jpe,bmp,tif,tiff,png,gif\",\"file_ext\":\"jpg,jpeg,jpe,bmp,tif,tiff,png,gif,pdf,zip,mpg,mpeg,avi,mov,wmv,asf,asx,ra,rm,txt,rtf,doc,xsl,html,js,wav,mp3,eps,ppt,pps,swf,tar,tex,gz\",\"cache\":\"0\",\"cache_time\":\"15\"}','','',0,'0000-00-00 00:00:00',0,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1033,'com_wishlist','component','com_wishlist','',1,1,1,0,'','{\"categories\":\"general, resource, group, user\",\"group\":\"hubdev\",\"banking\":\"1\",\"allow_advisory\":\"0\",\"votesplit\":\"0\",\"webpath\":\"\\/site\\/wishlist\",\"show_percentage_granted\":\"0\"}','','',0,'0000-00-00 00:00:00',0,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1034,'com_search','component','com_search','',1,1,1,0,'','','','',0,'0000-00-00 00:00:00',0,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1035,'com_cart','component','com_cart','',1,0,1,0,'{\"legacy\":true,\"name\":\"Cart\",\"type\":\"component\",\"creationDate\":\"Unknown\",\"author\":\"HUBzero\",\"copyright\":\"\",\"authorEmail\":\"\",\"authorUrl\":\"\",\"version\":\"\",\"description\":\"Configure cart\",\"group\":\"\"}','','','',0,'0000-00-00 00:00:00',0,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1036,'com_storefront','component','com_storefront','',1,0,1,0,'','','','',0,'0000-00-00 00:00:00',0,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1037,'com_collections','component','com_collections','',1,1,1,0,'','','','',0,'0000-00-00 00:00:00',0,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1038,'com_feedaggregator','component','com_feedaggregator','',1,1,1,0,'','','','',0,'0000-00-00 00:00:00',0,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1039,'com_update','component','com_update','',1,1,1,0,'','','','',0,'0000-00-00 00:00:00',0,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1040,'com_time','component','com_time','',1,0,1,0,'','','','',0,'0000-00-00 00:00:00',0,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1041,'com_hubgraph','component','com_hubgraph','',1,1,1,0,'','{\"host\":\"unix:\\/\\/\\/var\\/run\\/hubgraph-server.sock\",\"port\":null,\"showTagCloud\":true,\"enabledOptions\":\"\"}','','',0,'0000-00-00 00:00:00',0,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1400,'Authentication - Facebook','plugin','facebook','authentication',0,0,1,0,'','app_id=\napp_secret=\n','','',0,'0000-00-00 00:00:00',2,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1401,'Authentication - Google','plugin','google','authentication',0,0,1,0,'','app_id=\napp_secret=\n','','',0,'0000-00-00 00:00:00',3,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1402,'Authentication - HUBzero','plugin','hubzero','authentication',0,1,1,0,'','{\"remember_me_default\":\"0\",\"display_name\":\"\",\"site_login\":\"1\",\"admin_login\":\"1\"}','','',0,'0000-00-00 00:00:00',1,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1403,'Authentication - Linkedin','plugin','linkedin','authentication',0,0,1,0,'','api_key=\napp_secret=\n','','',0,'0000-00-00 00:00:00',4,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1404,'Authentication - PUCAS','plugin','pucas','authentication',0,0,1,0,'','domain=Purdue Career Account (CAS)\ndisplay_name=Purdue Career\n\n','','',0,'0000-00-00 00:00:00',6,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1405,'Authentication - Twitter','plugin','twitter','authentication',0,0,1,0,'','','','',0,'0000-00-00 00:00:00',5,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1406,'Citation - Bibtex','plugin','bibtex','citation',0,1,1,0,'','title_match_percent=90%\n\n','','',0,'0000-00-00 00:00:00',2,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1407,'Citation - Default','plugin','default','citation',0,1,1,0,'','','','',0,'0000-00-00 00:00:00',1,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1408,'Citation - Endnote','plugin','endnote','citation',0,1,1,0,'','custom_tags=badges-%=\ntags-%<\ntitle_match_percent=85%\n\n','','',0,'0000-00-00 00:00:00',3,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1409,'Content - xHubTags','plugin','xhubtags','content',0,1,1,0,'','','','',0,'0000-00-00 00:00:00',7,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1410,'Courses - Announcements','plugin','announcements','courses',0,1,1,0,'','','','',0,'0000-00-00 00:00:00',7,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1412,'Courses - Course Offerings','plugin','offerings','courses',0,1,1,0,'','','','',0,'0000-00-00 00:00:00',10,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1413,'Courses - Course Overview','plugin','overview','courses',0,1,1,0,'','','','',0,'0000-00-00 00:00:00',6,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1414,'Courses - Course Related','plugin','related','courses',0,1,1,0,'','','','',0,'0000-00-00 00:00:00',12,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1415,'Courses - Course Reviews','plugin','reviews','courses',0,1,1,0,'','','','',0,'0000-00-00 00:00:00',9,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1416,'Courses - Dashboard','plugin','dashboard','courses',0,1,1,0,'','','','',0,'0000-00-00 00:00:00',8,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1417,'Courses - Disucssions','plugin','discussions','courses',0,1,1,0,'','','','',0,'0000-00-00 00:00:00',2,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1418,'Courses - Guide','plugin','guide','courses',0,0,1,0,'','','','',0,'0000-00-00 00:00:00',15,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1419,'Courses - My Progress','plugin','progress','courses',0,1,1,0,'','','','',0,'0000-00-00 00:00:00',4,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1420,'Courses - Notes','plugin','notes','courses',0,1,1,0,'','','','',0,'0000-00-00 00:00:00',13,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1421,'Courses - Outline','plugin','outline','courses',0,1,1,0,'','','','',0,'0000-00-00 00:00:00',3,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1422,'Courses - Pages','plugin','pages','courses',0,1,1,0,'','','','',0,'0000-00-00 00:00:00',5,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1423,'Courses - Store','plugin','store','courses',0,0,1,0,'','','','',0,'0000-00-00 00:00:00',14,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1425,'Cron - Cache','plugin','cache','cron',0,1,1,0,'','','','',0,'0000-00-00 00:00:00',2,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1426,'Cron - Groups','plugin','groups','cron',0,1,1,0,'','','','',0,'0000-00-00 00:00:00',2,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1427,'Cron - Members','plugin','members','cron',0,1,1,0,'','','','',0,'0000-00-00 00:00:00',3,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1428,'Cron - Newsletter','plugin','newsletter','cron',0,1,1,0,'','','','',0,'0000-00-00 00:00:00',4,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1429,'Cron - Support','plugin','support','cron',0,1,1,0,'','','','',0,'0000-00-00 00:00:00',5,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1430,'Groups - Announcements','plugin','announcements','groups',0,1,1,0,'','plugin_access=members\ndisplay_tab=1','','',0,'0000-00-00 00:00:00',2,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1431,'Groups - Blog','plugin','blog','groups',0,1,1,0,'','uploadpath=/site/groups/{{gid}}/blog\nposting=0\nfeeds_enabled=0\nfeed_entries=partial','','',0,'0000-00-00 00:00:00',3,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1432,'Groups - Calendar','plugin','calendar','groups',0,1,1,0,'','','','',0,'0000-00-00 00:00:00',4,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1433,'Groups - Forum','plugin','forum','groups',0,1,1,0,'','','','',0,'0000-00-00 00:00:00',7,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1434,'Groups - Member Options','plugin','memberoptions','groups',0,1,1,0,'','','','',0,'0000-00-00 00:00:00',8,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1435,'Groups - Members','plugin','members','groups',0,1,1,0,'','','','',0,'0000-00-00 00:00:00',1,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1436,'Groups - Messages','plugin','messages','groups',0,1,1,0,'','{\"limit\":50,\"display_tab\":0}','','',0,'0000-00-00 00:00:00',9,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1437,'Groups - Projects','plugin','projects','groups',0,1,1,0,'','','','',0,'0000-00-00 00:00:00',10,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1438,'Groups - Resources','plugin','resources','groups',0,1,1,0,'','','','',0,'0000-00-00 00:00:00',11,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1439,'Groups - Usage','plugin','usage','groups',0,0,1,0,'','uploadpath=/site/groups/{{gid}}/blog\nposting=0\nfeeds_enabled=0\nfeed_entries=partial','','',0,'0000-00-00 00:00:00',12,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1440,'Groups - Wiki','plugin','wiki','groups',0,1,1,0,'','','','',0,'0000-00-00 00:00:00',13,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1441,'Groups - Wishlist','plugin','wishlist','groups',0,1,1,0,'','limit=50','','',0,'0000-00-00 00:00:00',14,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1442,'HUBzero - Autocompleter','plugin','autocompleter','hubzero',0,1,1,0,'','','','',0,'0000-00-00 00:00:00',1,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1443,'HUBzero - Comments','plugin','comments','hubzero',0,1,1,0,'','','','',0,'0000-00-00 00:00:00',7,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1444,'plg_wiki_parserdefault','plugin','parserdefault','wiki',0,1,1,0,'','','','',0,'0000-00-00 00:00:00',3,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1445,'plg_wiki_editortoolbar','plugin','editortoolbar','wiki',0,1,1,0,'','','','',0,'0000-00-00 00:00:00',2,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1446,'plg_wiki_editorwykiwyg','plugin','editorwykiwyg','wiki',0,1,1,0,'','','','',0,'0000-00-00 00:00:00',8,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1447,'HUBzero - Image CAPTCHA','plugin','imagecaptcha','hubzero',0,1,1,0,'','bgColor=#ffffff\ntextColor=#2c8007\nimageFunction=Adv\n','','',0,'0000-00-00 00:00:00',4,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1448,'HUBzero - Math CAPTCHA','plugin','mathcaptcha','hubzero',0,0,1,0,'','','','',0,'0000-00-00 00:00:00',5,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1449,'HUBzero - ReCAPTCHA','plugin','recaptcha','hubzero',0,0,1,0,'','','','',0,'0000-00-00 00:00:00',6,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1450,'Members - Account','plugin','account','members',0,1,1,0,'','ssh_key_upload=0\n\n','','',0,'0000-00-00 00:00:00',3,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1451,'Members - Blog','plugin','blog','members',0,1,1,0,'','uploadpath=/site/members/{{uid}}/blog\nfeeds_enabled=0\nfeed_entries=partial','','',0,'0000-00-00 00:00:00',4,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1452,'Members - Contributions','plugin','contributions','members',0,1,1,0,'','','','',0,'0000-00-00 00:00:00',6,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1453,'Members - Contributions - Resources','plugin','resources','members',0,1,1,0,'','','','',0,'0000-00-00 00:00:00',7,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1454,'Members - Contributions - Topics','plugin','wiki','members',0,1,1,0,'','','','',0,'0000-00-00 00:00:00',8,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1455,'Members - Courses','plugin','courses','members',0,1,1,0,'','','','',0,'0000-00-00 00:00:00',9,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1456,'Members - Dashboard','plugin','dashboard','members',0,1,1,0,'','{\"allow_customization\":\"1\",\"position\":\"memberDashboard\",\"defaults\":\"[{\\\"module\\\":44,\\\"col\\\":1,\\\"row\\\":1,\\\"size_x\\\":1,\\\"size_y\\\":2},{\\\"module\\\":35,\\\"col\\\":1,\\\"row\\\":3,\\\"size_x\\\":1,\\\"size_y\\\":2},{\\\"module\\\":38,\\\"col\\\":1,\\\"row\\\":5,\\\"size_x\\\":1,\\\"size_y\\\":2},{\\\"module\\\":39,\\\"col\\\":1,\\\"row\\\":7,\\\"size_x\\\":1,\\\"size_y\\\":2},{\\\"module\\\":33,\\\"col\\\":2,\\\"row\\\":1,\\\"size_x\\\":1,\\\"size_y\\\":2},{\\\"module\\\":42,\\\"col\\\":2,\\\"row\\\":3,\\\"size_x\\\":1,\\\"size_y\\\":2},{\\\"module\\\":34,\\\"col\\\":2,\\\"row\\\":5,\\\"size_x\\\":1,\\\"size_y\\\":2},{\\\"module\\\":41,\\\"col\\\":3,\\\"row\\\":1,\\\"size_x\\\":1,\\\"size_y\\\":2},{\\\"module\\\":36,\\\"col\\\":3,\\\"row\\\":3,\\\"size_x\\\":1,\\\"size_y\\\":2},{\\\"module\\\":37,\\\"col\\\":3,\\\"row\\\":5,\\\"size_x\\\":1,\\\"size_y\\\":2}]\"}','','',0,'0000-00-00 00:00:00',1,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1458,'Members - Groups','plugin','groups','members',0,1,1,0,'','','','',0,'0000-00-00 00:00:00',10,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1460,'Members - Messages','plugin','messages','members',0,1,1,0,'','default_method=email\n\n','','',0,'0000-00-00 00:00:00',12,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1461,'Members - Points','plugin','points','members',0,1,1,0,'','','','',0,'0000-00-00 00:00:00',13,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1462,'Members - Profile','plugin','profile','members',0,1,1,0,'','','','',0,'0000-00-00 00:00:00',2,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1463,'Members - Projects','plugin','projects','members',0,1,1,0,'','','','',0,'0000-00-00 00:00:00',14,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1464,'Members - Resume','plugin','resume','members',0,1,1,0,'','limit=50','','',0,'0000-00-00 00:00:00',15,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1465,'Members - Usage','plugin','usage','members',0,1,1,0,'','','','',0,'0000-00-00 00:00:00',16,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1466,'Projects - Blog','plugin','blog','projects',0,1,1,0,'','','','',0,'0000-00-00 00:00:00',1,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1467,'Projects - Files','plugin','files','projects',0,1,1,0,'','maxUpload=104857600\nmaxDownload=1048576\nreservedNames=google , dropbox, shared, temp\nconnectedProjects=\nenable_google=0\ngoogle_clientId=\ngoogle_clientSecret=\ngoogle_appKey=\ngoogle_folder=Google\nsync_lock=0\nauto_sync=1\nlatex=1\ntexpath=/usr/bin/\ngspath=/usr/bin/','','',0,'0000-00-00 00:00:00',3,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1468,'Projects - Notes','plugin','notes','projects',0,1,1,0,'','','','',0,'0000-00-00 00:00:00',5,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1469,'Projects - Publications','plugin','publications','projects',0,1,1,0,'','','','',0,'0000-00-00 00:00:00',6,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1470,'Projects - Team','plugin','team','projects',0,1,1,0,'','','','',0,'0000-00-00 00:00:00',2,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1471,'Projects - Todo','plugin','todo','projects',0,1,1,0,'','','','',0,'0000-00-00 00:00:00',4,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1472,'Resources - About','plugin','about','resources',0,1,1,0,'','','','',0,'0000-00-00 00:00:00',1,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1474,'Resources - Citations','plugin','citations','resources',0,1,1,0,'','','','',0,'0000-00-00 00:00:00',10,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1476,'Resources - Questions','plugin','questions','resources',0,1,1,0,'','','','',0,'0000-00-00 00:00:00',11,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1477,'Resources - Recommendations','plugin','recommendations','resources',0,0,1,0,'','','','',0,'0000-00-00 00:00:00',2,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1478,'Resources - Related','plugin','related','resources',0,1,1,0,'','','','',0,'0000-00-00 00:00:00',3,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1479,'Resources - Reviews','plugin','reviews','resources',0,1,1,0,'','','','',0,'0000-00-00 00:00:00',4,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1480,'Resources - Share','plugin','share','resources',0,1,1,0,'','icons_limit=3\nshare_facebook=1\nshare_twitter=1\nshare_google=1\nshare_digg=1\nshare_technorati=1\nshare_delicious=1\nshare_reddit=0\nshare_email=0\nshare_print=0\n\n','','',0,'0000-00-00 00:00:00',8,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1481,'Resources - Sponsors','plugin','sponsors','resources',0,1,1,0,'','','','',0,'0000-00-00 00:00:00',12,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1482,'Resources - Supporting Documents','plugin','supportingdocs','resources',0,1,1,0,'','display_limit=50','','',0,'0000-00-00 00:00:00',13,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1483,'Resources - Usage','plugin','usage','resources',0,1,1,0,'','{\"period\":\"14\",\"chart_path\":\"\\/site\\/stats\\/chart_resources\\/\",\"map_path\":\"\\/site\\/stats\\/resource_maps\\/\"}','','',0,'0000-00-00 00:00:00',5,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1484,'Resources - Versions','plugin','versions','resources',0,1,1,0,'','','','',0,'0000-00-00 00:00:00',6,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1485,'Resources - Wishlist','plugin','wishlist','resources',0,1,1,0,'','','','',0,'0000-00-00 00:00:00',14,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1486,'Support - Answers','plugin','answers','support',0,1,1,0,'','','','',0,'0000-00-00 00:00:00',1,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1487,'Support - Blog','plugin','blog','support',0,1,1,0,'','','','',0,'0000-00-00 00:00:00',6,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1488,'Support - CAPTCHA','plugin','captcha','support',0,1,1,0,'','modCaptcha=text\ncomCaptcha=image\nbgColor=#2c8007\ntextColor=#ffffff\nimageFunction=Adv\n','','',0,'0000-00-00 00:00:00',7,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1489,'Support - Comments','plugin','comments','support',0,1,1,0,'','','','',0,'0000-00-00 00:00:00',2,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1490,'Support - Knowledgebase Comments','plugin','kb','support',0,1,1,0,'','','','',0,'0000-00-00 00:00:00',8,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1491,'Support - Resources','plugin','resources','support',0,1,1,0,'','','','',0,'0000-00-00 00:00:00',3,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1492,'Support - Transfer','plugin','transfer','support',0,1,1,0,'','','','',0,'0000-00-00 00:00:00',5,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1493,'Support - Wishlist','plugin','wishlist','support',0,1,1,0,'','','','',0,'0000-00-00 00:00:00',4,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1494,'System - HUBzero','plugin','hubzero','system',0,1,1,0,'','search=search\n\n','','',0,'0000-00-00 00:00:00',9,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1495,'System - xFeed','plugin','xfeed','system',0,1,1,0,'','','','',0,'0000-00-00 00:00:00',10,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1496,'System - Disable Cache','plugin','disablecache','system',0,1,1,0,'','definitions=/about/contact\nreenable_afterdispatch=0\n\n','','',0,'0000-00-00 00:00:00',11,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1497,'System - JQuery','plugin','jquery','system',0,1,1,1,'','{\"jquery\":\"1\",\"jqueryVersion\":\"1.7.2\",\"jquerycdnpath\":\"\\/\\/ajax.googleapis.com\\/ajax\\/libs\\/jquery\\/1.7.2\\/jquery.min.js\",\"jqueryui\":\"1\",\"jqueryuiVersion\":\"1.8.6\",\"jqueryuicdnpath\":\"\\/\\/ajax.googleapis.com\\/ajax\\/libs\\/jqueryui\\/1.8.6\\/jquery-ui.min.js\",\"jqueryuicss\":\"0\",\"jqueryuicsspath\":\"\\/plugins\\/system\\/jquery\\/css\\/jquery-ui-1.8.6.custom.css\",\"jquerytools\":\"1\",\"jquerytoolsVersion\":\"1.2.5\",\"jquerytoolscdnpath\":\"http:\\/\\/cdn.jquerytools.org\\/1.2.5\\/all\\/jquery.tools.min.js\",\"jqueryfb\":\"1\",\"jqueryfbVersion\":\"2.0.4\",\"jqueryfbcdnpath\":\"\\/\\/fancyapps.com\\/fancybox\\/\",\"jqueryfbcss\":\"1\",\"jqueryfbcsspath\":\"\\/media\\/system\\/css\\/jquery.fancybox.css\",\"activateSite\":\"1\",\"noconflictSite\":\"0\",\"activateAdmin\":\"0\",\"noconflictAdmin\":\"0\"}','','',1000,'2013-09-01 14:26:58',12,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1498,'Tags - Answers','plugin','answers','tags',0,1,1,0,'','','','',0,'0000-00-00 00:00:00',7,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1499,'Tags - Blogs','plugin','blogs','tags',0,1,1,0,'','','','',0,'0000-00-00 00:00:00',9,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1500,'Tags - Citations','plugin','citations','tags',0,1,1,0,'','','','',0,'0000-00-00 00:00:00',10,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1501,'Tags - Events','plugin','events','tags',0,1,1,0,'','','','',0,'0000-00-00 00:00:00',5,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1502,'Tags - Forum','plugin','forum','tags',0,1,1,0,'','','','',0,'0000-00-00 00:00:00',8,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1503,'Tags - Groups','plugin','groups','tags',0,1,1,0,'','','','',0,'0000-00-00 00:00:00',6,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1504,'Tags - Knowledgebase','plugin','kb','tags',0,1,1,0,'','','','',0,'0000-00-00 00:00:00',11,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1505,'Tags - Members','plugin','members','tags',0,1,1,0,'','','','',0,'0000-00-00 00:00:00',3,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1506,'Tags - Resources','plugin','resources','tags',0,1,1,0,'','','','',0,'0000-00-00 00:00:00',1,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1507,'Tags - Support','plugin','support','tags',0,1,1,0,'','','','',0,'0000-00-00 00:00:00',4,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1508,'Tags - Topics','plugin','wiki','tags',0,1,1,0,'','','','',0,'0000-00-00 00:00:00',2,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1509,'Usage - Domains','plugin','domains','usage',0,0,1,0,'','','','',0,'0000-00-00 00:00:00',6,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1510,'Usage - Domain Class','plugin','domainclass','usage',0,0,1,0,'','','','',0,'0000-00-00 00:00:00',1,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1511,'Usage - Maps','plugin','maps','usage',0,1,1,0,'','','','',0,'0000-00-00 00:00:00',7,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1512,'Usage - Overview','plugin','overview','usage',0,1,1,0,'','','','',0,'0000-00-00 00:00:00',2,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1513,'Usage - Region','plugin','region','usage',0,0,1,0,'','','','',0,'0000-00-00 00:00:00',8,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1514,'Usage - Partners','plugin','partners','usage',0,0,1,0,'','','','',0,'0000-00-00 00:00:00',3,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1515,'Usage - Tools','plugin','tools','usage',0,0,1,0,'','','','',0,'0000-00-00 00:00:00',4,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1516,'User - xHUB','plugin','xusers','user',0,1,1,0,'','','','',0,'0000-00-00 00:00:00',4,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1517,'User - LDAP','plugin','ldap','user',0,0,1,0,'','','','',0,'0000-00-00 00:00:00',5,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1518,'User - Constant Contact','plugin','constantcontact','user',0,1,1,0,'','','','',0,'0000-00-00 00:00:00',6,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1519,'Whatsnew - Content','plugin','content','whatsnew',0,1,1,0,'','','','',0,'0000-00-00 00:00:00',1,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1520,'Whatsnew - Events','plugin','events','whatsnew',0,1,1,0,'','','','',0,'0000-00-00 00:00:00',2,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1521,'Whatsnew - Knowledge Base','plugin','kb','whatsnew',0,1,1,0,'','','','',0,'0000-00-00 00:00:00',3,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1522,'Whatsnew - Resources','plugin','resources','whatsnew',0,1,1,0,'','','','',0,'0000-00-00 00:00:00',4,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1523,'Whatsnew - Topics','plugin','wiki','whatsnew',0,1,1,0,'','','','',0,'0000-00-00 00:00:00',5,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1524,'XMessage - RSS','plugin','rss','xmessage',0,0,1,0,'','','','',0,'0000-00-00 00:00:00',4,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1525,'XMessage - Internal','plugin','internal','xmessage',0,1,1,0,'','','','',0,'0000-00-00 00:00:00',5,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1526,'XMessage - SMS TXT','plugin','smstxt','xmessage',0,0,1,0,'','','','',0,'0000-00-00 00:00:00',3,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1527,'XMessage - Instant Message','plugin','im','xmessage',0,0,1,0,'','','','',0,'0000-00-00 00:00:00',2,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1528,'XMessage - Handler','plugin','handler','xmessage',0,1,1,0,'','','','',0,'0000-00-00 00:00:00',1,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1529,'XMessage - Email','plugin','email','xmessage',0,1,1,0,'','','','',0,'0000-00-00 00:00:00',0,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1530,'plg_search_blogs','plugin','blogs','search',0,1,1,0,'','','','',0,'0000-00-00 00:00:00',0,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1531,'plg_search_citations','plugin','citations','search',0,1,1,0,'','','','',0,'0000-00-00 00:00:00',0,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1532,'plg_search_content','plugin','content','search',0,1,1,0,'','','','',0,'0000-00-00 00:00:00',0,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1533,'plg_search_weighttitle','plugin','weighttitle','search',0,1,1,0,'','','','',0,'0000-00-00 00:00:00',0,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1534,'plg_search_events','plugin','events','search',0,1,1,0,'','','','',0,'0000-00-00 00:00:00',0,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1535,'plg_search_forum','plugin','forum','search',0,1,1,0,'','','','',0,'0000-00-00 00:00:00',0,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1536,'plg_search_groups','plugin','groups','search',0,1,1,0,'','','','',0,'0000-00-00 00:00:00',0,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1537,'plg_search_kb','plugin','kb','search',0,1,1,0,'','','','',0,'0000-00-00 00:00:00',0,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1538,'plg_search_members','plugin','members','search',0,1,1,0,'','','','',0,'0000-00-00 00:00:00',0,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1539,'plg_search_questions and Answers','plugin','questions','search',0,1,1,0,'','','','',0,'0000-00-00 00:00:00',0,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1540,'plg_search_resources','plugin','resources','search',0,1,1,0,'','','','',0,'0000-00-00 00:00:00',0,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1541,'plg_search_sitemap','plugin','sitemap','search',0,1,1,0,'','','','',0,'0000-00-00 00:00:00',0,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1542,'plg_search_sortcourses','plugin','sortcourses','search',0,1,1,0,'','','','',0,'0000-00-00 00:00:00',0,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1543,'plg_search_sortevents','plugin','sortevents','search',0,1,1,0,'','','','',0,'0000-00-00 00:00:00',0,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1544,'plg_search_suffixes','plugin','suffixes','search',0,1,1,0,'','','','',0,'0000-00-00 00:00:00',0,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1545,'plg_search_wiki','plugin','wiki','search',0,1,1,0,'','','','',0,'0000-00-00 00:00:00',0,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1546,'plg_search_weightcontributor','plugin','weightcontributor','search',0,1,1,0,'','','','',0,'0000-00-00 00:00:00',0,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1547,'plg_search_weighttools','plugin','weighttools','search',0,0,1,0,'','','','',0,'0000-00-00 00:00:00',0,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1548,'plg_search_wishlists','plugin','wishlists','search',0,1,1,0,'','','','',0,'0000-00-00 00:00:00',0,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1554,'plg_support_forum','plugin','forum','support',0,1,1,0,'{\"legacy\":false,\"name\":\"Support - Forum Abuse reports\",\"type\":\"plugin\",\"creationDate\":\"Unknown\",\"author\":\"HUBzero\",\"copyright\":\"Copyright (c) 2005-2020 The Regents of the University of California.\",\"authorEmail\":\"\",\"authorUrl\":\"\",\"version\":\"\",\"description\":\"Various functions for the Report Abuse Component\",\"group\":\"\"}','','','',0,'0000-00-00 00:00:00',0,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1555,'plg_courses_memberoptions','plugin','memberoptions','courses',0,0,1,0,'{\"legacy\":true,\"name\":\"Courses - Member options\",\"type\":\"plugin\",\"creationDate\":\"Unknown\",\"author\":\"HUBzero\",\"copyright\":\"Copyright (c) 2005-2020 The Regents of the University of California.\",\"authorEmail\":\"\",\"authorUrl\":\"\",\"version\":\"\",\"description\":\"Display a course\'s member options\",\"group\":\"\"}','','','',0,'0000-00-00 00:00:00',0,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1558,'plg_cron_users','plugin','users','cron',0,1,1,0,'{\"legacy\":false,\"name\":\"Cron - Users\",\"type\":\"plugin\",\"creationDate\":\"Unknown\",\"author\":\"HUBzero\",\"copyright\":\"Copyright (c) 2005-2020 The Regents of the University of California.\",\"authorEmail\":\"\",\"authorUrl\":\"\",\"version\":\"\",\"description\":\"Cron events for users\",\"group\":\"\"}','','','',0,'0000-00-00 00:00:00',0,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1559,'plg_groups_collections','plugin','collections','groups',0,1,1,0,'{\"legacy\":false,\"name\":\"Groups - Collections\",\"type\":\"plugin\",\"creationDate\":\"December 2012\",\"author\":\"HUBzero\",\"copyright\":\"Copyright (c) 2005-2020 The Regents of the University of California.\",\"authorEmail\":\"support@hubzero.org\",\"authorUrl\":\"\",\"version\":\"1.5\",\"description\":\"Display collections\",\"group\":\"\"}','','','',0,'0000-00-00 00:00:00',5,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1560,'plg_members_collections','plugin','collections','members',0,1,1,0,'{\"legacy\":false,\"name\":\"Members - Collections\",\"type\":\"plugin\",\"creationDate\":\"December 2012\",\"author\":\"HUBzero\",\"copyright\":\"Copyright (c) 2005-2020 The Regents of the University of California.\",\"authorEmail\":\"support@hubzero.org\",\"authorUrl\":\"\",\"version\":\"1.5\",\"description\":\"Display collections\",\"group\":\"\"}','','','',0,'0000-00-00 00:00:00',5,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1561,'plg_projects_databases','plugin','databases','projects',0,1,1,0,'{\"legacy\":false,\"name\":\"Projects - Databases\",\"type\":\"plugin\",\"creationDate\":\"Unknown\",\"author\":\"Sudheera R. Fernando\",\"copyright\":\"Copyright (C) 2013-2015 The Regents of the University of California.\",\"authorEmail\":\"\",\"authorUrl\":\"\",\"version\":\"\",\"description\":\"Databases for Projects environment\",\"group\":\"\"}','','','',0,'0000-00-00 00:00:00',0,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1562,'plg_publications_citations','plugin','citations','publications',0,1,1,0,'{\"legacy\":false,\"name\":\"Publication - Citations\",\"type\":\"plugin\",\"creationDate\":\"Unknown\",\"author\":\"HUBzero\",\"copyright\":\"Copyright (c) 2005-2020 The Regents of the University of California.\",\"authorEmail\":\"\",\"authorUrl\":\"\",\"version\":\"\",\"description\":\"Displays citations for a publication\",\"group\":\"\"}','','','',0,'0000-00-00 00:00:00',0,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1564,'plg_publications_questions','plugin','questions','publications',0,1,1,0,'{\"legacy\":false,\"name\":\"Publication - Questions\",\"type\":\"plugin\",\"creationDate\":\"Unknown\",\"author\":\"HUBzero\",\"copyright\":\"Copyright (c) 2005-2020 The Regents of the University of California.\",\"authorEmail\":\"\",\"authorUrl\":\"\",\"version\":\"\",\"description\":\"Displays questions related to a publication (by tag)\",\"group\":\"\"}','','','',0,'0000-00-00 00:00:00',0,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1565,'plg_publications_recommendations','plugin','recommendations','publications',0,1,1,0,'{\"legacy\":false,\"name\":\"Publication - Recommendations\",\"type\":\"plugin\",\"creationDate\":\"Unknown\",\"author\":\"HUBzero\",\"copyright\":\"Copyright (c) 2005-2020 The Regents of the University of California.\",\"authorEmail\":\"\",\"authorUrl\":\"\",\"version\":\"\",\"description\":\"Displays recommendations for a publication\",\"group\":\"\"}','','','',0,'0000-00-00 00:00:00',0,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1566,'plg_publications_related','plugin','related','publications',0,1,1,0,'{\"legacy\":false,\"name\":\"Publication - Related\",\"type\":\"plugin\",\"creationDate\":\"Unknown\",\"author\":\"HUBzero\",\"copyright\":\"Copyright (c) 2005-2020 The Regents of the University of California.\",\"authorEmail\":\"\",\"authorUrl\":\"\",\"version\":\"\",\"description\":\"Displays related publication\",\"group\":\"\"}','','','',0,'0000-00-00 00:00:00',0,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1567,'plg_publications_reviews','plugin','reviews','publications',0,1,1,0,'{\"legacy\":false,\"name\":\"Publication - Reviews\",\"type\":\"plugin\",\"creationDate\":\"Unknown\",\"author\":\"HUBzero\",\"copyright\":\"Copyright (c) 2005-2020 The Regents of the University of California.\",\"authorEmail\":\"\",\"authorUrl\":\"\",\"version\":\"\",\"description\":\"Displays reviews for a publication\",\"group\":\"\"}','','','',0,'0000-00-00 00:00:00',0,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1568,'plg_publications_share','plugin','share','publications',0,1,1,0,'{\"legacy\":false,\"name\":\"Publication - Share\",\"type\":\"plugin\",\"creationDate\":\"Unknown\",\"author\":\"HUBzero\",\"copyright\":\"Copyright (c) 2005-2020 The Regents of the University of California.\",\"authorEmail\":\"\",\"authorUrl\":\"\",\"version\":\"\",\"description\":\"Display options to post publication link on Facebbok, Twitter etc.\",\"group\":\"\"}','','','',0,'0000-00-00 00:00:00',0,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1569,'plg_publications_supportingdocs','plugin','supportingdocs','publications',0,1,1,0,'{\"legacy\":false,\"name\":\"Publication - supportingdocs\",\"type\":\"plugin\",\"creationDate\":\"Unknown\",\"author\":\"HUBzero\",\"copyright\":\"Copyright (c) 2005-2020 The Regents of the University of California.\",\"authorEmail\":\"\",\"authorUrl\":\"\",\"version\":\"\",\"description\":\"Displays supporting docs for a publication\",\"group\":\"\"}','','','',0,'0000-00-00 00:00:00',0,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1570,'plg_publications_usage','plugin','usage','publications',0,1,1,0,'{\"legacy\":false,\"name\":\"Publication - Usage\",\"type\":\"plugin\",\"creationDate\":\"Unknown\",\"author\":\"HUBzero\",\"copyright\":\"Copyright (c) 2005-2020 The Regents of the University of California.\",\"authorEmail\":\"\",\"authorUrl\":\"\",\"version\":\"\",\"description\":\"Displays usage info for a publication\",\"group\":\"\"}','','','',0,'0000-00-00 00:00:00',0,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1571,'plg_publications_versions','plugin','versions','publications',0,1,1,0,'{\"legacy\":false,\"name\":\"Publication - versions\",\"type\":\"plugin\",\"creationDate\":\"Unknown\",\"author\":\"HUBzero\",\"copyright\":\"Copyright (c) 2005-2020 The Regents of the University of California.\",\"authorEmail\":\"\",\"authorUrl\":\"\",\"version\":\"\",\"description\":\"Displays all versions of a publication\",\"group\":\"\"}','','','',0,'0000-00-00 00:00:00',0,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1572,'plg_publications_wishlist','plugin','wishlist','publications',0,1,1,0,'{\"legacy\":false,\"name\":\"Publication - Wishlist\",\"type\":\"plugin\",\"creationDate\":\"Unknown\",\"author\":\"HUBzero\",\"copyright\":\"Copyright (c) 2005-2020 The Regents of the University of California.\",\"authorEmail\":\"\",\"authorUrl\":\"\",\"version\":\"\",\"description\":\"Displays publication wishlist\",\"group\":\"\"}','','','',0,'0000-00-00 00:00:00',0,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1573,'plg_resources_groups','plugin','groups','resources',0,1,1,0,'{\"legacy\":false,\"name\":\"Resource - Group\",\"type\":\"plugin\",\"creationDate\":\"Unknown\",\"author\":\"HUBzero\",\"copyright\":\"Copyright (c) 2005-2020 The Regents of the University of California.\",\"authorEmail\":\"\",\"authorUrl\":\"\",\"version\":\"\",\"description\":\"Display group ownership for a resource\",\"group\":\"\"}','','','',0,'0000-00-00 00:00:00',0,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1575,'plg_system_mobile','plugin','mobile','system',0,1,1,0,'{\"legacy\":true,\"name\":\"System - Mobile\",\"type\":\"plugin\",\"creationDate\":\"December 2012\",\"author\":\"HUBzero\",\"copyright\":\"Copyright (c) 2005-2020 The Regents of the University of California.\",\"authorEmail\":\"\",\"authorUrl\":\"\",\"version\":\"1\",\"description\":\"PLG_SYSTEM_MOBILE_DESC\",\"group\":\"\"}','','','',0,'0000-00-00 00:00:00',0,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1582,'plg_courses_syllabus','plugin','syllabus','courses',0,1,1,0,'','','','',0,'0000-00-00 00:00:00',16,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1584,'plg_courses_faq','plugin','faq','courses',0,1,1,0,'','','','',0,'0000-00-00 00:00:00',18,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1585,'plg_search_courses','plugin','courses','search',0,1,1,0,'','','','',0,'0000-00-00 00:00:00',1,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1586,'plg_search_collections','plugin','collections','search',0,1,1,0,'','','','',0,'0000-00-00 00:00:00',2,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1587,'plg_search_projects','plugin','projects','search',0,1,1,0,'','','','',0,'0000-00-00 00:00:00',3,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1588,'plg_search_publications','plugin','publications','search',0,1,1,0,'','','','',0,'0000-00-00 00:00:00',4,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1589,'plg_cron_courses','plugin','courses','cron',0,1,1,0,'','','','',0,'0000-00-00 00:00:00',6,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1590,'plg_editors_ckeditor','plugin','ckeditor','editors',0,1,1,0,'','','','',0,'0000-00-00 00:00:00',4,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1591,'plg_system_supergroup','plugin','supergroup','system',0,1,1,0,'','','','',0,'0000-00-00 00:00:00',13,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1592,'plg_geocode_arcgisonline','plugin','arcgisonline','geocode',0,0,1,0,'','','','',0,'0000-00-00 00:00:00',1,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1593,'plg_geocode_baidu','plugin','baidu','geocode',0,0,1,0,'','','','',0,'0000-00-00 00:00:00',2,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1594,'plg_geocode_bingmaps','plugin','bingmaps','geocode',0,0,1,0,'','','','',0,'0000-00-00 00:00:00',3,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1595,'plg_geocode_cloudmade','plugin','cloudmade','geocode',0,0,1,0,'','','','',0,'0000-00-00 00:00:00',4,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1596,'plg_geocode_datasciencetoolkit','plugin','datasciencetoolkit','geocode',0,0,1,0,'','','','',0,'0000-00-00 00:00:00',5,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1597,'plg_geocode_freegeoip','plugin','freegeoip','geocode',0,0,1,0,'','','','',0,'0000-00-00 00:00:00',6,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1598,'plg_geocode_geocoderca','plugin','geocoderca','geocode',0,0,1,0,'','','','',0,'0000-00-00 00:00:00',7,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1599,'plg_geocode_geocoderus','plugin','geocoderus','geocode',0,0,1,0,'','','','',0,'0000-00-00 00:00:00',8,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1600,'plg_geocode_geoip','plugin','geoip','geocode',0,0,1,0,'','','','',0,'0000-00-00 00:00:00',9,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1601,'plg_geocode_geoips','plugin','geoips','geocode',0,0,1,0,'','','','',0,'0000-00-00 00:00:00',10,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1602,'plg_geocode_geonames','plugin','geonames','geocode',0,0,1,0,'','','','',0,'0000-00-00 00:00:00',11,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1603,'plg_geocode_geoplugin','plugin','geoplugin','geocode',0,0,1,0,'','','','',0,'0000-00-00 00:00:00',12,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1604,'plg_geocode_googlemaps','plugin','googlemaps','geocode',0,0,1,0,'','','','',0,'0000-00-00 00:00:00',13,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1605,'plg_geocode_googlemapsbusiness','plugin','googlemapsbusiness','geocode',0,0,1,0,'','','','',0,'0000-00-00 00:00:00',14,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1606,'plg_geocode_hostip','plugin','hostip','geocode',0,0,1,0,'','','','',0,'0000-00-00 00:00:00',15,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1607,'plg_geocode_ignopenls','plugin','ignopenls','geocode',0,0,1,0,'','','','',0,'0000-00-00 00:00:00',16,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1608,'plg_geocode_ipgeobase','plugin','ipgeobase','geocode',0,0,1,0,'','','','',0,'0000-00-00 00:00:00',17,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1609,'plg_geocode_ipinfodb','plugin','ipinfodb','geocode',0,0,1,0,'','','','',0,'0000-00-00 00:00:00',18,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1610,'plg_geocode_local','plugin','local','geocode',0,1,1,0,'','','','',0,'0000-00-00 00:00:00',19,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1611,'plg_geocode_mapquest','plugin','mapquest','geocode',0,0,1,0,'','','','',0,'0000-00-00 00:00:00',20,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1612,'plg_geocode_maxmind','plugin','maxmind','geocode',0,0,1,0,'','','','',0,'0000-00-00 00:00:00',21,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1613,'plg_geocode_maxmindbinary','plugin','maxmindbinary','geocode',0,0,1,0,'','','','',0,'0000-00-00 00:00:00',22,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1614,'plg_geocode_nominatim','plugin','nominatim','geocode',0,0,1,0,'','','','',0,'0000-00-00 00:00:00',23,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1615,'plg_geocode_oiorest','plugin','oiorest','geocode',0,0,1,0,'','','','',0,'0000-00-00 00:00:00',24,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1616,'plg_geocode_openstreetmap','plugin','openstreetmap','geocode',0,0,1,0,'','','','',0,'0000-00-00 00:00:00',25,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1617,'plg_geocode_tomtom','plugin','tomtom','geocode',0,0,1,0,'','','','',0,'0000-00-00 00:00:00',26,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1618,'plg_geocode_yandex','plugin','yandex','geocode',0,0,1,0,'','','','',0,'0000-00-00 00:00:00',27,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1619,'plg_resources_findthistext','plugin','findthistext','resources',0,1,1,0,'','','','',0,'0000-00-00 00:00:00',15,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1620,'plg_cron_projects','plugin','projects','cron',0,1,1,0,'','','','',0,'0000-00-00 00:00:00',7,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1621,'plg_cron_publications','plugin','publications','cron',0,1,1,0,'','','','',0,'0000-00-00 00:00:00',8,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1622,'plg_content_formatwiki','plugin','formatwiki','content',0,1,1,0,'','{\"applyFormat\":1,\"convertFormat\":1}','','',0,'0000-00-00 00:00:00',8,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1623,'plg_content_formathtml','plugin','formathtml','content',0,0,1,0,'','{\"applyFormat\":1,\"convertFormat\":0,\"sanitizeBefore\":0}','','',0,'0000-00-00 00:00:00',9,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1624,'plg_editors_wikitoolbar','plugin','wikitoolbar','editors',0,1,1,0,'','','','',0,'0000-00-00 00:00:00',5,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1625,'plg_editors_wikiwyg','plugin','wikiwyg','editors',0,1,1,0,'','','','',0,'0000-00-00 00:00:00',6,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1626,'plg_projects_links','plugin','links','projects',0,1,1,0,'','','','',0,'0000-00-00 00:00:00',7,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1627,'plg_groups_courses','plugin','courses','groups',0,1,1,0,'','','','',0,'0000-00-00 00:00:00',6,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1628,'plg_support_publications','plugin','publications','support',0,1,1,0,'','','','',0,'0000-00-00 00:00:00',9,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1629,'plg_tags_publications','plugin','publications','tags',0,1,1,0,'','','','',0,'0000-00-00 00:00:00',12,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1630,'plg_hubzero_systemplate','plugin','systemplate','hubzero',0,1,1,0,'','','','',0,'0000-00-00 00:00:00',8,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1631,'plg_hubzero_systickets','plugin','systickets','hubzero',0,1,1,0,'','','','',0,'0000-00-00 00:00:00',9,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1632,'plg_hubzero_sysusers','plugin','sysusers','hubzero',0,1,1,0,'','','','',0,'0000-00-00 00:00:00',10,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1633,'plg_support_time','plugin','time','support',0,0,1,0,'','','','',0,'0000-00-00 00:00:00',10,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1634,'plg_content_akismet','plugin','akismet','content',0,0,1,0,'','','','',0,'0000-00-00 00:00:00',10,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1635,'plg_content_mollom','plugin','mollom','content',0,0,1,0,'','','','',0,'0000-00-00 00:00:00',11,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1636,'plg_content_spamassassin','plugin','spamassassin','content',0,0,1,0,'','','','',0,'0000-00-00 00:00:00',12,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1637,'plg_members_impact','plugin','impact','members',0,1,1,0,'','','','',0,'0000-00-00 00:00:00',11,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1638,'plg_publications_groups','plugin','groups','publications',0,1,1,0,'','','','',0,'0000-00-00 00:00:00',1,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1639,'plg_support_wiki','plugin','wiki','support',0,1,1,0,'','','','',0,'0000-00-00 00:00:00',11,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1640,'plg_time_summary','plugin','summary','time',0,0,1,0,'','','','',0,'0000-00-00 00:00:00',1,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1641,'plg_tools_java','plugin','java','tools',0,1,1,0,'','','','',0,'0000-00-00 00:00:00',1,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1642,'plg_tools_novnc','plugin','novnc','tools',0,0,1,0,'','','','',0,'0000-00-00 00:00:00',2,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1643,'plg_whatsnew_publications','plugin','publications','whatsnew',0,1,1,0,'','','','',0,'0000-00-00 00:00:00',6,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1644,'plg_groups_citations','plugin','citations','groups',0,0,1,0,'','','','',0,'0000-00-00 00:00:00',15,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1645,'plg_oaipmh_publications','plugin','publications','oaipmh',0,1,1,0,'','','','',0,'0000-00-00 00:00:00',2,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1646,'plg_user_geo','plugin','geo','user',0,0,1,0,'','','','',0,'0000-00-00 00:00:00',8,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1647,'plg_user_middleware','plugin','middleware','user',0,0,1,0,'','','','',0,'0000-00-00 00:00:00',7,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1648,'plg_oaipmh_resources','plugin','resources','oaipmh',0,1,1,0,'','','','',0,'0000-00-00 00:00:00',1,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1649,'plg_system_certificate','plugin','certificate','system',0,0,1,0,'','','','',0,'0000-00-00 00:00:00',14,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1650,'plg_authentication_certificate','plugin','certificate','authentication',0,0,1,0,'','','','',0,'0000-00-00 00:00:00',7,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1651,'plg_time_csv','plugin','csv','time',0,0,1,0,'','','','',0,'0000-00-00 00:00:00',2,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1700,'hubbasic','template','hubbasic','',0,1,1,0,'{}','{}','','',0,'0000-00-00 00:00:00',0,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1701,'hubbasic2012','template','hubbasic2012','',0,1,1,0,'{}','{}','','',0,'0000-00-00 00:00:00',0,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1702,'hubbasic2013','template','hubbasic2013','',0,1,1,0,'{}','{}','','',0,'0000-00-00 00:00:00',0,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1703,'welcome','template','welcome','',0,1,1,0,'{}','{}','','',0,'0000-00-00 00:00:00',0,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1704,'hubbasicadmin','template','hubbasicadmin','',1,1,1,0,'{}','{}','','',0,'0000-00-00 00:00:00',0,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1705,'kameleon (admin)','template','kameleon','',1,1,1,0,'{}','{}','','',0,'0000-00-00 00:00:00',0,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1706,'Kimera (site)','template','kimera','',0,1,1,1,'{}','{}','','',0,'0000-00-00 00:00:00',0,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1200,'mod_announcements','module','mod_announcements','',0,1,1,0,'{\"legacy\":true,\"name\":\"Announcements Display\",\"type\":\"module\",\"creationDate\":\"May 2010\",\"author\":\"HUBzero\",\"copyright\":\"\",\"authorEmail\":\"support@hubzero.org\",\"authorUrl\":\"\",\"version\":\"1.0.0\",\"description\":\"This module allows the display of announcements\",\"group\":\"\"}','','','',0,'0000-00-00 00:00:00',0,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1201,'mod_application_env','module','mod_application_env','',0,1,1,0,'{\"legacy\":true,\"name\":\"Application Environment\",\"type\":\"module\",\"creationDate\":\"April 2012\",\"author\":\"HUBzero\",\"copyright\":\"Copyright (c) 2005-2020 The Regents of the University of California.\",\"authorEmail\":\"support@hubzero.org\",\"authorUrl\":\"\",\"version\":\"1.5.0\",\"description\":\"This module displays the current application environment (production, stage, testing, development)\",\"group\":\"\"}','','','',0,'0000-00-00 00:00:00',0,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1202,'mod_billboards','module','mod_billboards','',0,1,1,0,'{\"legacy\":true,\"name\":\"Billboards\",\"type\":\"module\",\"creationDate\":\"November 2011\",\"author\":\"HUBzero\",\"copyright\":\"\",\"authorEmail\":\"\",\"authorUrl\":\"\",\"version\":\"1.0\",\"description\":\"Rotate through billboards of content\",\"group\":\"\"}','','','',0,'0000-00-00 00:00:00',0,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1203,'mod_events_cal','module','mod_events_cal','',0,1,1,0,'{\"legacy\":true,\"name\":\"Events Calendar\",\"type\":\"module\",\"creationDate\":\"Unknown\",\"author\":\"HUBzero\",\"copyright\":\"Copyright (c) 2005-2020 The Regents of the University of California.\",\"authorEmail\":\"\",\"authorUrl\":\"\",\"version\":\"\",\"description\":\"Displays a calendar with days that have events linked. Requires events component.\",\"group\":\"\"}','','','',0,'0000-00-00 00:00:00',0,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1204,'mod_events_latest','module','mod_events_latest','',0,1,1,0,'{\"legacy\":true,\"name\":\"Latest Events\",\"type\":\"module\",\"creationDate\":\"Unknown\",\"author\":\"HUBzero\",\"copyright\":\"Copyright (c) 2005-2020 The Regents of the University of California.\",\"authorEmail\":\"\",\"authorUrl\":\"\",\"version\":\"\",\"description\":\"Displays a list of upcoming events.\",\"group\":\"\"}','','','',0,'0000-00-00 00:00:00',0,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1205,'mod_featuredblog','module','mod_featuredblog','',0,1,1,0,'{\"legacy\":true,\"name\":\"Featured Blog\",\"type\":\"module\",\"creationDate\":\"November 2010\",\"author\":\"HUBzero\",\"copyright\":\"(C) 2000 - 2004 Miro International Pty Ltd\",\"authorEmail\":\"\",\"authorUrl\":\"\",\"version\":\"4.5.1\",\"description\":\"This module randomly displays a featured blog entry.\",\"group\":\"\"}','','','',0,'0000-00-00 00:00:00',0,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1206,'mod_featuredmember','module','mod_featuredmember','',0,1,1,0,'{\"legacy\":true,\"name\":\"Featured Member\",\"type\":\"module\",\"creationDate\":\"Unknown\",\"author\":\"HUBzero\",\"copyright\":\"Copyright (c) 2005-2020 The Regents of the University of California.\",\"authorEmail\":\"\",\"authorUrl\":\"\",\"version\":\"\",\"description\":\"This module randomly displays a featured member or contributor.\",\"group\":\"\"}','','','',0,'0000-00-00 00:00:00',0,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1207,'mod_featuredquestion','module','mod_featuredquestion','',0,1,1,0,'{\"legacy\":true,\"name\":\"Featured Question\",\"type\":\"module\",\"creationDate\":\"Unknown\",\"author\":\"HUBzero\",\"copyright\":\"Copyright (c) 2005-2020 The Regents of the University of California.\",\"authorEmail\":\"\",\"authorUrl\":\"\",\"version\":\"\",\"description\":\"This module randomly displays a featured question.\",\"group\":\"\"}','','','',0,'0000-00-00 00:00:00',0,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1208,'mod_featuredresource','module','mod_featuredresource','',0,1,1,0,'{\"legacy\":true,\"name\":\"Featured Resource\",\"type\":\"module\",\"creationDate\":\"Unknown\",\"author\":\"HUBzero\",\"copyright\":\"Copyright (c) 2005-2020 The Regents of the University of California.\",\"authorEmail\":\"\",\"authorUrl\":\"\",\"version\":\"\",\"description\":\"This module randomly displays a featured resource.\",\"group\":\"\"}','','','',0,'0000-00-00 00:00:00',0,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1209,'mod_feed_youtube','module','mod_feed_youtube','',0,1,1,0,'{\"legacy\":true,\"name\":\"YouTube Feed Display\",\"type\":\"module\",\"creationDate\":\"April 2010\",\"author\":\"HUBzero\",\"copyright\":\"Copyright (c) 2005-2020 The Regents of the University of California.\",\"authorEmail\":\"admin@joomla.org\",\"authorUrl\":\"www.joomla.org\",\"version\":\"1.5.0\",\"description\":\"This module allows to display a youtube playlist feed\",\"group\":\"\"}','','','',0,'0000-00-00 00:00:00',0,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1210,'mod_findresources','module','mod_findresources','',0,1,1,0,'{\"legacy\":true,\"name\":\"Find Resources\",\"type\":\"module\",\"creationDate\":\"Sep 2009\",\"author\":\"HUBzero\",\"copyright\":\"Copyright (c) 2005-2020 The Regents of the University of California.\",\"authorEmail\":\"support@hubzero.org\",\"authorUrl\":\"\",\"version\":\"1.5.0\",\"description\":\"Module to display resources search, popular tags and categories.\",\"group\":\"\"}','','','',0,'0000-00-00 00:00:00',0,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1211,'mod_googleanalytics','module','mod_googleanalytics','',0,1,1,0,'{\"legacy\":true,\"name\":\"Google Analytics\",\"type\":\"module\",\"creationDate\":\"April 2012\",\"author\":\"HUBzero\",\"copyright\":\"Copyright (c) 2005-2020 The Regents of the University of California.\",\"authorEmail\":\"support@hubzero.org\",\"authorUrl\":\"\",\"version\":\"1.5.0\",\"description\":\"This module adds some Javascript to the page for Google Analytics reporting\",\"group\":\"\"}','','','',0,'0000-00-00 00:00:00',0,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1212,'mod_hubzilla','module','mod_hubzilla','',0,1,1,0,'{\"legacy\":true,\"name\":\"Hubzilla\",\"type\":\"module\",\"creationDate\":\"August 2012\",\"author\":\"HUBzero\",\"copyright\":\"Copyright (c) 2005-2020 The Regents of the University of California.\",\"authorEmail\":\"support@hubzero.org\",\"authorUrl\":\"\",\"version\":\"1.5.0\",\"description\":\"Hubzilla attack!\",\"group\":\"\"}','','','',0,'0000-00-00 00:00:00',0,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1213,'mod_incremental_registration','module','mod_incremental_registration','',0,1,1,0,'{\"legacy\":true,\"name\":\"Incremental Registration\",\"type\":\"module\",\"creationDate\":\"April 2012\",\"author\":\"HUBzero\",\"copyright\":\"Copyright (c) 2005-2020 The Regents of the University of California.\",\"authorEmail\":\"support@hubzero.org\",\"authorUrl\":\"\",\"version\":\"1.5.0\",\"description\":\"This module displays a page curl for enticing users to incrementally register demographics.\",\"group\":\"\"}','','','',0,'0000-00-00 00:00:00',0,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1214,'mod_latestblog','module','mod_latestblog','',0,1,1,0,'{\"legacy\":true,\"name\":\"Latest Blog posts\",\"type\":\"module\",\"creationDate\":\"Unknown\",\"author\":\"HUBzero\",\"copyright\":\"Copyright (c) 2005-2020 The Regents of the University of California.\",\"authorEmail\":\"\",\"authorUrl\":\"\",\"version\":\"\",\"description\":\"This module shows the latest blog posts in the site blog as well as group blogs.\",\"group\":\"\"}','','','',0,'0000-00-00 00:00:00',0,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1215,'mod_latestdiscussions','module','mod_latestdiscussions','',0,1,1,0,'{\"legacy\":true,\"name\":\"Latest Discussions\",\"type\":\"module\",\"creationDate\":\"Unknown\",\"author\":\"HUBzero\",\"copyright\":\"Copyright (c) 2005-2020 The Regents of the University of California.\",\"authorEmail\":\"\",\"authorUrl\":\"\",\"version\":\"\",\"description\":\"This module shows the latest discussions in the site forum as well as the group forum.\",\"group\":\"\"}','','','',0,'0000-00-00 00:00:00',0,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1216,'mod_latestgroups','module','mod_latestgroups','',0,1,1,0,'{\"legacy\":true,\"name\":\"Latest Groups\",\"type\":\"module\",\"creationDate\":\"Unknown\",\"author\":\"HUBzero\",\"copyright\":\"Copyright (c) 2005-2020 The Regents of the University of California.\",\"authorEmail\":\"\",\"authorUrl\":\"\",\"version\":\"\",\"description\":\"This module shows the latest discussions in the site forum as well as the group forum.\",\"group\":\"\"}','','','',0,'0000-00-00 00:00:00',0,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1217,'mod_latestusage','module','mod_latestusage','',0,1,1,0,'{\"legacy\":true,\"name\":\"Latest Usage\",\"type\":\"module\",\"creationDate\":\"Unknown\",\"author\":\"HUBzero\",\"copyright\":\"Copyright (c) 2005-2020 The Regents of the University of California.\",\"authorEmail\":\"\",\"authorUrl\":\"\",\"version\":\"\",\"description\":\"This module displays the latest usage numbers.\",\"group\":\"\"}','','','',0,'0000-00-00 00:00:00',0,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1218,'mod_logjserrors','module','mod_logjserrors','',0,1,1,0,'{\"legacy\":true,\"name\":\"Log JS Errors\",\"type\":\"module\",\"creationDate\":\"July 2006\",\"author\":\"Joomla! Project\",\"copyright\":\"Copyright (C) 2005 - 2010 Open Source Matters. All rights reserved.\",\"authorEmail\":\"admin@joomla.org\",\"authorUrl\":\"www.joomla.org\",\"version\":\"1.5.0\",\"description\":\"Logs js errors\",\"group\":\"\"}','','','',0,'0000-00-00 00:00:00',0,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1219,'mod_megamenu','module','mod_megamenu','',0,1,1,0,'{\"legacy\":true,\"name\":\"Mega Menu\",\"type\":\"module\",\"creationDate\":\"Feb 2012\",\"author\":\"HUBzero\",\"copyright\":\"Copyright (C) 2005 - 2008 Open Source Matters. All rights reserved.\",\"authorEmail\":\"support@hubzero.org\",\"authorUrl\":\"hubzero.org\",\"version\":\"1.5.0\",\"description\":\"Displays a menu with mega menu option.\",\"group\":\"\"}','','','',0,'0000-00-00 00:00:00',0,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1220,'mod_mycontributions','module','mod_mycontributions','',0,1,1,0,'{\"legacy\":true,\"name\":\"My Contributions\",\"type\":\"module\",\"creationDate\":\"Unknown\",\"author\":\"HUBzero\",\"copyright\":\"Copyright (c) 2005-2020 The Regents of the University of California.\",\"authorEmail\":\"\",\"authorUrl\":\"\",\"version\":\"\",\"description\":\"This module will display a list of contributions\",\"group\":\"\"}','','','',0,'0000-00-00 00:00:00',0,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1221,'mod_mycourses','module','mod_mycourses','',0,1,1,0,'{\"legacy\":true,\"name\":\"My Courses\",\"type\":\"module\",\"creationDate\":\"Unknown\",\"author\":\"HUBzero\",\"copyright\":\"Copyright (c) 2005-2020 The Regents of the University of California.\",\"authorEmail\":\"\",\"authorUrl\":\"\",\"version\":\"\",\"description\":\"This module will display a list of courses the user belongs to and their status in it\",\"group\":\"\"}','','','',0,'0000-00-00 00:00:00',0,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1223,'mod_mygroups','module','mod_mygroups','',0,1,1,0,'{\"legacy\":true,\"name\":\"My Groups\",\"type\":\"module\",\"creationDate\":\"Unknown\",\"author\":\"HUBzero\",\"copyright\":\"Copyright (c) 2005-2020 The Regents of the University of California.\",\"authorEmail\":\"\",\"authorUrl\":\"\",\"version\":\"\",\"description\":\"This module will display a list of groups the user belongs to and their status in it\",\"group\":\"\"}','','','',0,'0000-00-00 00:00:00',0,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1224,'mod_mymessages','module','mod_mymessages','',0,1,1,0,'{\"legacy\":true,\"name\":\"My Messages\",\"type\":\"module\",\"creationDate\":\"Unknown\",\"author\":\"HUBzero\",\"copyright\":\"Copyright (c) 2005-2020 The Regents of the University of California.\",\"authorEmail\":\"\",\"authorUrl\":\"\",\"version\":\"\",\"description\":\"This module will display a list of unread messages sent by the site.\",\"group\":\"\"}','','','',0,'0000-00-00 00:00:00',0,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1225,'mod_mypoints','module','mod_mypoints','',0,1,1,0,'{\"legacy\":true,\"name\":\"My Points\",\"type\":\"module\",\"creationDate\":\"October 2009\",\"author\":\"HUBzero\",\"copyright\":\"Copyright (c) 2005-2020 The Regents of the University of California.\",\"authorEmail\":\"support@hubzero.org\",\"authorUrl\":\"\",\"version\":\"1\",\"description\":\"This module will display a point total and list of most recent point transactions.\",\"group\":\"\"}','','','',0,'0000-00-00 00:00:00',0,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1226,'mod_myprojects','module','mod_myprojects','',0,1,1,0,'{\"legacy\":true,\"name\":\"My Projects\",\"type\":\"module\",\"creationDate\":\"Unknown\",\"author\":\"HUBzero\",\"copyright\":\"Copyright (c) 2005-2020 The Regents of the University of California.\",\"authorEmail\":\"\",\"authorUrl\":\"\",\"version\":\"\",\"description\":\"This module displays a list of projects the user belongs, their role in the project and the number of updates since last visit.\",\"group\":\"\"}','','','',0,'0000-00-00 00:00:00',0,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1227,'mod_myquestions','module','mod_myquestions','',0,1,1,0,'{\"legacy\":true,\"name\":\"My Questions\",\"type\":\"module\",\"creationDate\":\"Jan 2009\",\"author\":\"snowwitje\",\"copyright\":\"Copyright (c) 2005-2020 The Regents of the University of California.\",\"authorEmail\":\"support@hubzero.org\",\"authorUrl\":\"\",\"version\":\"1\",\"description\":\"This module will display a list of questions submitted by the user, as well as those user can answer.\",\"group\":\"\"}','','','',0,'0000-00-00 00:00:00',0,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1228,'mod_myresources','module','mod_myresources','',0,1,1,0,'{\"legacy\":true,\"name\":\"My Resources\",\"type\":\"module\",\"creationDate\":\"January 2011\",\"author\":\"HUBzero\",\"copyright\":\"(C) 2011 HUBzero\",\"authorEmail\":\"\",\"authorUrl\":\"\",\"version\":\"1\",\"description\":\"This module will display a list of publications (resources, wiki pages, etc.)\",\"group\":\"\"}','','','',0,'0000-00-00 00:00:00',0,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1229,'mod_mysessions','module','mod_mysessions','',0,1,1,0,'{\"legacy\":true,\"name\":\"My Sessions\",\"type\":\"module\",\"creationDate\":\"Unknown\",\"author\":\"HUBzero\",\"copyright\":\"Copyright (c) 2005-2020 The Regents of the University of California.\",\"authorEmail\":\"\",\"authorUrl\":\"\",\"version\":\"\",\"description\":\"This module shows a list of the user\'s active tool sessions.\",\"group\":\"\"}','','','',0,'0000-00-00 00:00:00',0,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1230,'mod_mysubmissions','module','mod_mysubmissions','',0,1,1,0,'{\"legacy\":true,\"name\":\"My Submissions\",\"type\":\"module\",\"creationDate\":\"Unknown\",\"author\":\"HUBzero\",\"copyright\":\"Copyright (c) 2005-2020 The Regents of the University of California.\",\"authorEmail\":\"\",\"authorUrl\":\"\",\"version\":\"\",\"description\":\"Shows a list of submissions (resources) in progress.\",\"group\":\"\"}','','','',0,'0000-00-00 00:00:00',0,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1231,'mod_mytickets','module','mod_mytickets','',0,1,1,0,'{\"legacy\":true,\"name\":\"My Tickets\",\"type\":\"module\",\"creationDate\":\"Unknown\",\"author\":\"HUBzero\",\"copyright\":\"Copyright (c) 2005-2020 The Regents of the University of California.\",\"authorEmail\":\"\",\"authorUrl\":\"\",\"version\":\"\",\"description\":\"This module will display a list of active support tickets submitted by the user\",\"group\":\"\"}','','','',0,'0000-00-00 00:00:00',0,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1232,'mod_mytools','module','mod_mytools','',0,1,1,0,'{\"legacy\":true,\"name\":\"My Tools\",\"type\":\"module\",\"creationDate\":\"Unknown\",\"author\":\"HUBzero\",\"copyright\":\"Copyright (c) 2005-2020 The Regents of the University of California.\",\"authorEmail\":\"\",\"authorUrl\":\"\",\"version\":\"\",\"description\":\"This module shows a list of the user\'s favorite tools, recently used tools, and all available tools.\",\"group\":\"\"}','','','',0,'0000-00-00 00:00:00',0,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1233,'mod_mywishes','module','mod_mywishes','',0,1,1,0,'{\"legacy\":true,\"name\":\"My Wishes\",\"type\":\"module\",\"creationDate\":\"Unknown\",\"author\":\"HUBzero\",\"copyright\":\"Copyright (c) 2005-2020 The Regents of the University of California.\",\"authorEmail\":\"\",\"authorUrl\":\"\",\"version\":\"\",\"description\":\"This module will display a list of open wishes submitted by\\/ assigned to the user\",\"group\":\"\"}','','','',0,'0000-00-00 00:00:00',0,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1234,'mod_newsletter','module','mod_newsletter','',0,1,1,0,'{\"legacy\":true,\"name\":\"Newsletter\",\"type\":\"module\",\"creationDate\":\"August 2012\",\"author\":\"HUBzero\",\"copyright\":\"Copyright (c) 2005-2020 The Regents of the University of California.\",\"authorEmail\":\"support@hubzero.org\",\"authorUrl\":\"\",\"version\":\"1.0.0\",\"description\":\"Newsletter Mailing List Sign up\",\"group\":\"\"}','','','',0,'0000-00-00 00:00:00',0,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1235,'mod_notices','module','mod_notices','',0,1,1,0,'{\"legacy\":true,\"name\":\"Notices Module\",\"type\":\"module\",\"creationDate\":\"Unknown\",\"author\":\"HUBzero\",\"copyright\":\"Copyright (c) 2005-2020 The Regents of the University of California.\",\"authorEmail\":\"\",\"authorUrl\":\"\",\"version\":\"\",\"description\":\"This module shows a notice (when site will be down, etc.) box for site visitors.\",\"group\":\"\"}','','','',0,'0000-00-00 00:00:00',0,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1236,'mod_poll','module','mod_poll','',0,1,1,0,'{\"legacy\":true,\"name\":\"Poll\",\"type\":\"module\",\"creationDate\":\"July 2006\",\"author\":\"Joomla! Project\",\"copyright\":\"Copyright (C) 2005 - 2010 Open Source Matters. All rights reserved.\",\"authorEmail\":\"admin@joomla.org\",\"authorUrl\":\"www.joomla.org\",\"version\":\"1.5.0\",\"description\":\"DESCPOLL\",\"group\":\"\"}','','','',0,'0000-00-00 00:00:00',0,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1237,'mod_polltitle','module','mod_polltitle','',0,1,1,0,'{\"legacy\":true,\"name\":\"XPoll Title\",\"type\":\"module\",\"creationDate\":\"Unknown\",\"author\":\"HUBzero\",\"copyright\":\"Copyright (c) 2005-2020 The Regents of the University of California.\",\"authorEmail\":\"\",\"authorUrl\":\"\",\"version\":\"\",\"description\":\"This module shows the most popular FAQs.\",\"group\":\"\"}','','','',0,'0000-00-00 00:00:00',0,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1238,'mod_popularfaq','module','mod_popularfaq','',0,1,1,0,'{\"legacy\":true,\"name\":\"Popular FAQs\",\"type\":\"module\",\"creationDate\":\"Unknown\",\"author\":\"HUBzero\",\"copyright\":\"Copyright (c) 2005-2020 The Regents of the University of California.\",\"authorEmail\":\"\",\"authorUrl\":\"\",\"version\":\"\",\"description\":\"This module shows the most popular FAQs.\",\"group\":\"\"}','','','',0,'0000-00-00 00:00:00',0,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1239,'mod_popularquestions','module','mod_popularquestions','',0,1,1,0,'{\"legacy\":true,\"name\":\"Popular Questions\",\"type\":\"module\",\"creationDate\":\"Unknown\",\"author\":\"HUBzero\",\"copyright\":\"Copyright (c) 2005-2020 The Regents of the University of California.\",\"authorEmail\":\"\",\"authorUrl\":\"\",\"version\":\"\",\"description\":\"This module shows questions with the most popular (helpful) responses added to the Answers component.\",\"group\":\"\"}','','','',0,'0000-00-00 00:00:00',0,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1240,'mod_quicktips','module','mod_quicktips','',0,1,1,0,'{\"legacy\":true,\"name\":\"Quick Tips\",\"type\":\"module\",\"creationDate\":\"Unknown\",\"author\":\"HUBzero\",\"copyright\":\"Copyright (c) 2005-2020 The Regents of the University of California.\",\"authorEmail\":\"\",\"authorUrl\":\"\",\"version\":\"\",\"description\":\"This module shows a quick \\\"tip of the day\\\" or \\\"did you know...\\\" feature.\",\"group\":\"\"}','','','',0,'0000-00-00 00:00:00',0,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1241,'mod_quotes','module','mod_quotes','',0,1,1,0,'{\"legacy\":true,\"name\":\"Quotes\",\"type\":\"module\",\"creationDate\":\"Unknown\",\"author\":\"HUBzero\",\"copyright\":\"Copyright (c) 2005-2020 The Regents of the University of California.\",\"authorEmail\":\"\",\"authorUrl\":\"\",\"version\":\"\",\"description\":\"This module compliments the Feedback component. It is used to display selected quotes on Notable Quotes page.\",\"group\":\"\"}','','','',0,'0000-00-00 00:00:00',0,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1242,'mod_randomquote','module','mod_randomquote','',0,1,1,0,'{\"legacy\":true,\"name\":\"Random Quote\",\"type\":\"module\",\"creationDate\":\"Mar 2010\",\"author\":\"HUBzero\",\"copyright\":\"(C) 2010 HUBzero\",\"authorEmail\":\"support@hubzero.org\",\"authorUrl\":\"\",\"version\":\"1.5.0\",\"description\":\"Module to display random featured quote\",\"group\":\"\"}','','','',0,'0000-00-00 00:00:00',0,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1243,'mod_rapid_contact','module','mod_rapid_contact','',0,1,1,0,'{\"legacy\":true,\"name\":\"Rapid Contact\",\"type\":\"module\",\"creationDate\":\"Unknown\",\"author\":\"HUBzero\",\"copyright\":\"Copyright (c) 2005-2020 The Regents of the University of California.\",\"authorEmail\":\"\",\"authorUrl\":\"\",\"version\":\"\",\"description\":\"This module shows a notice (when site will be down, etc.) box for site visitors.\",\"group\":\"\"}','','','',0,'0000-00-00 00:00:00',0,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1244,'mod_recentquestions','module','mod_recentquestions','',0,1,1,0,'{\"legacy\":true,\"name\":\"Latest Questions\",\"type\":\"module\",\"creationDate\":\"Unknown\",\"author\":\"HUBzero\",\"copyright\":\"Copyright (c) 2005-2020 The Regents of the University of California.\",\"authorEmail\":\"\",\"authorUrl\":\"\",\"version\":\"\",\"description\":\"This module shows the latest questions added to the Answers component.\",\"group\":\"\"}','','','',0,'0000-00-00 00:00:00',0,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1245,'mod_reportproblems','module','mod_reportproblems','',0,1,1,0,'{\"legacy\":true,\"name\":\"Trouble Report\",\"type\":\"module\",\"creationDate\":\"Unknown\",\"author\":\"HUBzero\",\"copyright\":\"Copyright (c) 2005-2020 The Regents of the University of California.\",\"authorEmail\":\"\",\"authorUrl\":\"\",\"version\":\"\",\"description\":\"This module will display a trouble report form\",\"group\":\"\"}','','','',0,'0000-00-00 00:00:00',0,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1246,'mod_resourcemenu','module','mod_resourcemenu','',0,1,1,0,'{\"legacy\":true,\"name\":\"HUB Resource Menu\",\"type\":\"module\",\"creationDate\":\"Unknown\",\"author\":\"HUBzero\",\"copyright\":\"Copyright (c) 2005-2020 The Regents of the University of California.\",\"authorEmail\":\"\",\"authorUrl\":\"\",\"version\":\"\",\"description\":\"This module shows any extra navigation or content in a pop-up style menu. Supports {xhub:module position=\\\"\\\" style=\\\"\\\"} tags.\",\"group\":\"\"}','','','',0,'0000-00-00 00:00:00',0,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1247,'mod_slideshow','module','mod_slideshow','',0,1,1,0,'{\"legacy\":true,\"name\":\"Slideshow\",\"type\":\"module\",\"creationDate\":\"June 2009\",\"author\":\"HUBzero\",\"copyright\":\"(C) 2000 - 2004 Miro International Pty Ltd\",\"authorEmail\":\"support@hubzero.org\",\"authorUrl\":\"\",\"version\":\"1.1.0\",\"description\":\"Displays HUB flash image slideshow.\",\"group\":\"\"}','','','',0,'0000-00-00 00:00:00',0,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1248,'mod_sliding_panes','module','mod_sliding_panes','',0,1,1,0,'{\"legacy\":true,\"name\":\"Sliding Panes\",\"type\":\"module\",\"creationDate\":\"Jan 2010\",\"author\":\"HUBzero\",\"copyright\":\"(C) 2000 - 2004 Miro International Pty Ltd\",\"authorEmail\":\"\",\"authorUrl\":\"\",\"version\":\"1.5.0\",\"description\":\"Rotate through panes of content\",\"group\":\"\"}','','','',0,'0000-00-00 00:00:00',0,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1249,'mod_spotlight','module','mod_spotlight','',0,1,1,0,'{\"legacy\":true,\"name\":\"Spotlight\",\"type\":\"module\",\"creationDate\":\"Unknown\",\"author\":\"HUBzero\",\"copyright\":\"Copyright (c) 2005-2020 The Regents of the University of California.\",\"authorEmail\":\"\",\"authorUrl\":\"\",\"version\":\"\",\"description\":\"This module randomly displays featured items.\",\"group\":\"\"}','','','',0,'0000-00-00 00:00:00',0,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1250,'mod_tagcloud','module','mod_tagcloud','',0,1,1,0,'{\"legacy\":true,\"name\":\"Tag Cloud\",\"type\":\"module\",\"creationDate\":\"Unknown\",\"author\":\"Unknown\",\"copyright\":\"\",\"authorEmail\":\"\",\"authorUrl\":\"\",\"version\":\"\",\"description\":\"This module will display a tag cloud\",\"group\":\"\"}','','','',0,'0000-00-00 00:00:00',0,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1251,'mod_toptags','module','mod_toptags','',0,1,1,0,'{\"legacy\":true,\"name\":\"Top Tags\",\"type\":\"module\",\"creationDate\":\"Unknown\",\"author\":\"HUBzero\",\"copyright\":\"Copyright (c) 2005-2020 The Regents of the University of California.\",\"authorEmail\":\"\",\"authorUrl\":\"\",\"version\":\"\",\"description\":\"This module shows a a list of the top used tags.\",\"group\":\"\"}','','','',0,'0000-00-00 00:00:00',0,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1252,'mod_twitterfeed','module','mod_twitterfeed','',0,1,1,0,'{\"legacy\":true,\"name\":\"Twitter Feed\",\"type\":\"module\",\"creationDate\":\"Unknown\",\"author\":\"HUBzero\",\"copyright\":\"Copyright (c) 2005-2020 The Regents of the University of California.\",\"authorEmail\":\"support@hubzero.org\",\"authorUrl\":\"\",\"version\":\"1.0.0\",\"description\":\"Loads the Twitter feed of the specified Twitter ID\",\"group\":\"\"}','','','',0,'0000-00-00 00:00:00',0,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1253,'mod_whatsnew','module','mod_whatsnew','',0,1,1,0,'{\"legacy\":true,\"name\":\"What\'s New\",\"type\":\"module\",\"creationDate\":\"Unknown\",\"author\":\"HUBzero\",\"copyright\":\"Copyright (c) 2005-2020 The Regents of the University of California.\",\"authorEmail\":\"\",\"authorUrl\":\"\",\"version\":\"\",\"description\":\"Lists the newest resources and events on the site.\",\"group\":\"\"}','','','',0,'0000-00-00 00:00:00',0,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1254,'mod_wishvoters','module','mod_wishvoters','',0,1,1,0,'{\"legacy\":true,\"name\":\"Wish Voters\",\"type\":\"module\",\"creationDate\":\"Unknown\",\"author\":\"HUBzero\",\"copyright\":\"Copyright (c) 2005-2020 The Regents of the University of California.\",\"authorEmail\":\"\",\"authorUrl\":\"\",\"version\":\"\",\"description\":\"This module will display a list of most active wish voters\",\"group\":\"\"}','','','',0,'0000-00-00 00:00:00',0,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1256,'mod_youtube','module','mod_youtube','',0,1,1,0,'{\"legacy\":true,\"name\":\"YouTube\",\"type\":\"module\",\"creationDate\":\"March 2011\",\"author\":\"HUBzero\",\"copyright\":\"Copyright (c) 2005-2020 The Regents of the University of California.\",\"authorEmail\":\"support@hubzero.org\",\"authorUrl\":\"\",\"version\":\"1.0.0\",\"description\":\"This module allows to display a youtube feed\",\"group\":\"\"}','','','',0,'0000-00-00 00:00:00',0,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1257,'mod_grouppages','module','mod_grouppages','',0,1,1,0,'','','','',0,'0000-00-00 00:00:00',0,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1300,'mod_hubmenu','module','mod_hubmenu','',1,1,1,1,'{}','{}','','',0,'0000-00-00 00:00:00',0,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1301,'mod_answers','module','mod_answers','',1,1,1,0,'{\"legacy\":true,\"name\":\"Answers\",\"type\":\"module\",\"creationDate\":\"Unknown\",\"author\":\"Unknown\",\"copyright\":\"Copyright (c) 2005-2020 The Regents of the University of California.\",\"authorEmail\":\"\",\"authorUrl\":\"\",\"version\":\"\",\"description\":\"This module shows on the Admin area Home Page and displays items that administrator needs to watch for.\",\"group\":\"\"}','','','',0,'0000-00-00 00:00:00',0,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1302,'mod_application_env','module','mod_application_env','',1,1,1,0,'{\"legacy\":true,\"name\":\"Application Environment\",\"type\":\"module\",\"creationDate\":\"April 2012\",\"author\":\"HUBzero\",\"copyright\":\"Copyright (c) 2005-2020 The Regents of the University of California.\",\"authorEmail\":\"support@hubzero.org\",\"authorUrl\":\"\",\"version\":\"1.5.0\",\"description\":\"This module displays the current application environment (production, stage, testing, development)\",\"group\":\"\"}','','','',0,'0000-00-00 00:00:00',0,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1303,'mod_dashboard','module','mod_dashboard','',1,1,1,0,'{\"legacy\":true,\"name\":\"Dashboard\",\"type\":\"module\",\"creationDate\":\"Unknown\",\"author\":\"Unknown\",\"copyright\":\"Copyright (c) 2005-2020 The Regents of the University of California.\",\"authorEmail\":\"\",\"authorUrl\":\"\",\"version\":\"\",\"description\":\"This module shows on the Admin area Home Page and displays items that administrator needs to watch for.\",\"group\":\"\"}','','','',0,'0000-00-00 00:00:00',0,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1304,'mod_groups','module','mod_groups','',1,1,1,0,'{\"legacy\":true,\"name\":\"Groups\",\"type\":\"module\",\"creationDate\":\"Unknown\",\"author\":\"Unknown\",\"copyright\":\"Copyright (c) 2005-2020 The Regents of the University of California.\",\"authorEmail\":\"\",\"authorUrl\":\"\",\"version\":\"\",\"description\":\"This module shows on the Admin area Home Page and displays items that administrator needs to watch for.\",\"group\":\"\"}','','','',0,'0000-00-00 00:00:00',0,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1305,'mod_members','module','mod_members','',1,1,1,0,'{\"legacy\":true,\"name\":\"Members\",\"type\":\"module\",\"creationDate\":\"Unknown\",\"author\":\"Unknown\",\"copyright\":\"Copyright (c) 2005-2020 The Regents of the University of California.\",\"authorEmail\":\"\",\"authorUrl\":\"\",\"version\":\"\",\"description\":\"This module shows on the Admin area Home Page and displays items that administrator needs to watch for.\",\"group\":\"\"}','','','',0,'0000-00-00 00:00:00',0,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1306,'mod_resources','module','mod_resources','',1,1,1,0,'{\"legacy\":true,\"name\":\"Resources\",\"type\":\"module\",\"creationDate\":\"Unknown\",\"author\":\"Unknown\",\"copyright\":\"Copyright (c) 2005-2020 The Regents of the University of California.\",\"authorEmail\":\"\",\"authorUrl\":\"\",\"version\":\"\",\"description\":\"This module shows on the Admin area Home Page and displays items that administrator needs to watch for.\",\"group\":\"\"}','','','',0,'0000-00-00 00:00:00',0,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1307,'mod_supporttickets','module','mod_supporttickets','',1,1,1,0,'{\"legacy\":true,\"name\":\"Support Tickets\",\"type\":\"module\",\"creationDate\":\"Unknown\",\"author\":\"Unknown\",\"copyright\":\"Copyright (c) 2005-2020 The Regents of the University of California.\",\"authorEmail\":\"\",\"authorUrl\":\"\",\"version\":\"\",\"description\":\"This module shows on the Admin area Home Page and displays items that administrator needs to watch for.\",\"group\":\"\"}','','','',0,'0000-00-00 00:00:00',0,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1308,'mod_tools','module','mod_tools','',1,1,1,0,'{\"legacy\":true,\"name\":\"Tools\",\"type\":\"module\",\"creationDate\":\"Unknown\",\"author\":\"Unknown\",\"copyright\":\"Copyright (c) 2005-2020 The Regents of the University of California.\",\"authorEmail\":\"\",\"authorUrl\":\"\",\"version\":\"\",\"description\":\"This module shows on the Admin area Home Page and displays items that administrator needs to watch for.\",\"group\":\"\"}','','','',0,'0000-00-00 00:00:00',0,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1309,'mod_whosonline','module','mod_whosonline','',1,1,1,0,'{\"legacy\":true,\"name\":\"Show Online Users\",\"type\":\"module\",\"creationDate\":\"January 2005\",\"author\":\"HUBzero\",\"copyright\":\"Copyright (c) 2005-2020 The Regents of the University of California.\",\"authorEmail\":\"support@hubzero.org\",\"authorUrl\":\"https:\\/\\/hubzero.org\",\"version\":\"1.0.0\",\"description\":\"This module shows a list of the currently logged in users\",\"group\":\"\"}','','','',0,'0000-00-00 00:00:00',0,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1310,'mod_wishlist','module','mod_wishlist','',1,1,1,0,'{\"legacy\":true,\"name\":\"Wishlist\",\"type\":\"module\",\"creationDate\":\"Unknown\",\"author\":\"Unknown\",\"copyright\":\"Copyright (c) 2005-2020 The Regents of the University of California.\",\"authorEmail\":\"\",\"authorUrl\":\"\",\"version\":\"\",\"description\":\"This module shows on the Admin area Home Page and displays items that administrator needs to watch for.\",\"group\":\"\"}','','','',0,'0000-00-00 00:00:00',0,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1311,'mod_supportactivity','module','mod_supportactivity','',1,1,1,0,'','','','',0,'0000-00-00 00:00:00',0,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1312,'mod_mycuration','module','mod_mycuration','',0,1,1,0,'','','','',0,'0000-00-00 00:00:00',0,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1313,'mod_users','module','mod_users','',1,0,1,0,'','','','',0,'0000-00-00 00:00:00',0,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1314,'mod_collect','module','mod_collect','',0,1,1,0,'','','','',0,'0000-00-00 00:00:00',0,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1315,'mod_courses','module','mod_courses','',1,1,1,0,'','','','',0,'0000-00-00 00:00:00',0,0);
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1316,'mod_mytodos','module','mod_mytodos','',0,1,1,0,'','','','',0,'0000-00-00 00:00:00',0,0);

--
-- Dumping data for table `#__faq`
--


--
-- Dumping data for table `#__faq_categories`
--


--
-- Dumping data for table `#__faq_comments`
--


--
-- Dumping data for table `#__faq_helpful_log`
--


--
-- Dumping data for table `#__feedback`
--


--
-- Dumping data for table `#__focus_area_resource_type_rel`
--


--
-- Dumping data for table `#__focus_areas`
--


--
-- Dumping data for table `#__forum_attachments`
--


--
-- Dumping data for table `#__forum_categories`
--


--
-- Dumping data for table `#__forum_posts`
--


--
-- Dumping data for table `#__forum_sections`
--


--
-- Dumping data for table `#__import_hooks`
--


--
-- Dumping data for table `#__import_runs`
--


--
-- Dumping data for table `#__imports`
--


--
-- Dumping data for table `#__incremental_registration_group_label_rel`
--


--
-- Dumping data for table `#__incremental_registration_groups`
--


--
-- Dumping data for table `#__incremental_registration_labels`
--


--
-- Dumping data for table `#__incremental_registration_options`
--


--
-- Dumping data for table `#__incremental_registration_popover_recurrence`
--


--
-- Dumping data for table `#__item_comment_files`
--


--
-- Dumping data for table `#__item_comments`
--


--
-- Dumping data for table `#__item_votes`
--


--
-- Dumping data for table `#__jobs_admins`
--


--
-- Dumping data for table `#__jobs_applications`
--


--
-- Dumping data for table `#__jobs_categories`
--


--
-- Dumping data for table `#__jobs_employers`
--


--
-- Dumping data for table `#__jobs_openings`
--


--
-- Dumping data for table `#__jobs_prefs`
--


--
-- Dumping data for table `#__jobs_resumes`
--


--
-- Dumping data for table `#__jobs_seekers`
--


--
-- Dumping data for table `#__jobs_shortlist`
--


--
-- Dumping data for table `#__jobs_stats`
--


--
-- Dumping data for table `#__jobs_types`
--


--
-- Dumping data for table `#__languages`
--

INSERT INTO `#__languages` (`lang_id`, `lang_code`, `title`, `title_native`, `sef`, `image`, `description`, `metakey`, `metadesc`, `sitename`, `published`, `access`, `ordering`) VALUES (1,'en-GB','English (UK)','English (UK)','en','en','','','','',1,1,1);

--
-- Dumping data for table `#__licenses`
--


--
-- Dumping data for table `#__licenses_tools`
--


--
-- Dumping data for table `#__licenses_users`
--


--
-- Dumping data for table `#__market_history`
--


--
-- Dumping data for table `#__media_tracking`
--


--
-- Dumping data for table `#__media_tracking_detailed`
--


--
-- Dumping data for table `#__menu`
--

INSERT INTO `#__menu` (`id`, `menutype`, `title`, `alias`, `note`, `path`, `link`, `type`, `published`, `parent_id`, `level`, `component_id`, `ordering`, `checked_out`, `checked_out_time`, `browserNav`, `access`, `img`, `template_style_id`, `params`, `lft`, `rgt`, `home`, `language`, `client_id`) VALUES (1,'','Menu_Item_Root','root','','','','',1,0,0,0,0,0,'0000-00-00 00:00:00',0,0,'',0,'',0,43,0,'*',0);
INSERT INTO `#__menu` (`id`, `menutype`, `title`, `alias`, `note`, `path`, `link`, `type`, `published`, `parent_id`, `level`, `component_id`, `ordering`, `checked_out`, `checked_out_time`, `browserNav`, `access`, `img`, `template_style_id`, `params`, `lft`, `rgt`, `home`, `language`, `client_id`) VALUES (2,'menu','com_banners','Banners','','Banners','index.php?option=com_banners','component',0,1,1,4,0,0,'0000-00-00 00:00:00',0,0,'class:banners',0,'',1,10,0,'*',1);
INSERT INTO `#__menu` (`id`, `menutype`, `title`, `alias`, `note`, `path`, `link`, `type`, `published`, `parent_id`, `level`, `component_id`, `ordering`, `checked_out`, `checked_out_time`, `browserNav`, `access`, `img`, `template_style_id`, `params`, `lft`, `rgt`, `home`, `language`, `client_id`) VALUES (3,'menu','com_banners','Banners','','Banners/Banners','index.php?option=com_banners','component',0,2,2,4,0,0,'0000-00-00 00:00:00',0,0,'class:banners',0,'',2,3,0,'*',1);
INSERT INTO `#__menu` (`id`, `menutype`, `title`, `alias`, `note`, `path`, `link`, `type`, `published`, `parent_id`, `level`, `component_id`, `ordering`, `checked_out`, `checked_out_time`, `browserNav`, `access`, `img`, `template_style_id`, `params`, `lft`, `rgt`, `home`, `language`, `client_id`) VALUES (4,'menu','com_banners_categories','Categories','','Banners/Categories','index.php?option=com_categories&extension=com_banners','component',0,2,2,6,0,0,'0000-00-00 00:00:00',0,0,'class:banners-cat',0,'',4,5,0,'*',1);
INSERT INTO `#__menu` (`id`, `menutype`, `title`, `alias`, `note`, `path`, `link`, `type`, `published`, `parent_id`, `level`, `component_id`, `ordering`, `checked_out`, `checked_out_time`, `browserNav`, `access`, `img`, `template_style_id`, `params`, `lft`, `rgt`, `home`, `language`, `client_id`) VALUES (5,'menu','com_banners_clients','Clients','','Banners/Clients','index.php?option=com_banners&view=clients','component',0,2,2,4,0,0,'0000-00-00 00:00:00',0,0,'class:banners-clients',0,'',6,7,0,'*',1);
INSERT INTO `#__menu` (`id`, `menutype`, `title`, `alias`, `note`, `path`, `link`, `type`, `published`, `parent_id`, `level`, `component_id`, `ordering`, `checked_out`, `checked_out_time`, `browserNav`, `access`, `img`, `template_style_id`, `params`, `lft`, `rgt`, `home`, `language`, `client_id`) VALUES (6,'menu','com_banners_tracks','Tracks','','Banners/Tracks','index.php?option=com_banners&view=tracks','component',0,2,2,4,0,0,'0000-00-00 00:00:00',0,0,'class:banners-tracks',0,'',8,9,0,'*',1);
INSERT INTO `#__menu` (`id`, `menutype`, `title`, `alias`, `note`, `path`, `link`, `type`, `published`, `parent_id`, `level`, `component_id`, `ordering`, `checked_out`, `checked_out_time`, `browserNav`, `access`, `img`, `template_style_id`, `params`, `lft`, `rgt`, `home`, `language`, `client_id`) VALUES (7,'menu','com_contact','Contacts','','Contacts','index.php?option=com_contact','component',0,1,1,8,0,0,'0000-00-00 00:00:00',0,0,'class:contact',0,'',11,16,0,'*',1);
INSERT INTO `#__menu` (`id`, `menutype`, `title`, `alias`, `note`, `path`, `link`, `type`, `published`, `parent_id`, `level`, `component_id`, `ordering`, `checked_out`, `checked_out_time`, `browserNav`, `access`, `img`, `template_style_id`, `params`, `lft`, `rgt`, `home`, `language`, `client_id`) VALUES (8,'menu','com_contact','Contacts','','Contacts/Contacts','index.php?option=com_contact','component',0,7,2,8,0,0,'0000-00-00 00:00:00',0,0,'class:contact',0,'',12,13,0,'*',1);
INSERT INTO `#__menu` (`id`, `menutype`, `title`, `alias`, `note`, `path`, `link`, `type`, `published`, `parent_id`, `level`, `component_id`, `ordering`, `checked_out`, `checked_out_time`, `browserNav`, `access`, `img`, `template_style_id`, `params`, `lft`, `rgt`, `home`, `language`, `client_id`) VALUES (9,'menu','com_contact_categories','Categories','','Contacts/Categories','index.php?option=com_categories&extension=com_contact','component',0,7,2,6,0,0,'0000-00-00 00:00:00',0,0,'class:contact-cat',0,'',14,15,0,'*',1);
INSERT INTO `#__menu` (`id`, `menutype`, `title`, `alias`, `note`, `path`, `link`, `type`, `published`, `parent_id`, `level`, `component_id`, `ordering`, `checked_out`, `checked_out_time`, `browserNav`, `access`, `img`, `template_style_id`, `params`, `lft`, `rgt`, `home`, `language`, `client_id`) VALUES (10,'menu','com_messages','Messaging','','Messaging','index.php?option=com_messages','component',0,1,1,15,0,0,'0000-00-00 00:00:00',0,0,'class:messages',0,'',17,22,0,'*',1);
INSERT INTO `#__menu` (`id`, `menutype`, `title`, `alias`, `note`, `path`, `link`, `type`, `published`, `parent_id`, `level`, `component_id`, `ordering`, `checked_out`, `checked_out_time`, `browserNav`, `access`, `img`, `template_style_id`, `params`, `lft`, `rgt`, `home`, `language`, `client_id`) VALUES (11,'menu','com_messages_add','New Private Message','','Messaging/New Private Message','index.php?option=com_messages&task=message.add','component',0,10,2,15,0,0,'0000-00-00 00:00:00',0,0,'class:messages-add',0,'',18,19,0,'*',1);
INSERT INTO `#__menu` (`id`, `menutype`, `title`, `alias`, `note`, `path`, `link`, `type`, `published`, `parent_id`, `level`, `component_id`, `ordering`, `checked_out`, `checked_out_time`, `browserNav`, `access`, `img`, `template_style_id`, `params`, `lft`, `rgt`, `home`, `language`, `client_id`) VALUES (12,'menu','com_messages_read','Read Private Message','','Messaging/Read Private Message','index.php?option=com_messages','component',0,10,2,15,0,0,'0000-00-00 00:00:00',0,0,'class:messages-read',0,'',20,21,0,'*',1);
INSERT INTO `#__menu` (`id`, `menutype`, `title`, `alias`, `note`, `path`, `link`, `type`, `published`, `parent_id`, `level`, `component_id`, `ordering`, `checked_out`, `checked_out_time`, `browserNav`, `access`, `img`, `template_style_id`, `params`, `lft`, `rgt`, `home`, `language`, `client_id`) VALUES (13,'menu','com_newsfeeds','News Feeds','','News Feeds','index.php?option=com_newsfeeds','component',0,1,1,17,0,0,'0000-00-00 00:00:00',0,0,'class:newsfeeds',0,'',23,28,0,'*',1);
INSERT INTO `#__menu` (`id`, `menutype`, `title`, `alias`, `note`, `path`, `link`, `type`, `published`, `parent_id`, `level`, `component_id`, `ordering`, `checked_out`, `checked_out_time`, `browserNav`, `access`, `img`, `template_style_id`, `params`, `lft`, `rgt`, `home`, `language`, `client_id`) VALUES (14,'menu','com_newsfeeds_feeds','Feeds','','News Feeds/Feeds','index.php?option=com_newsfeeds','component',0,13,2,17,0,0,'0000-00-00 00:00:00',0,0,'class:newsfeeds',0,'',24,25,0,'*',1);
INSERT INTO `#__menu` (`id`, `menutype`, `title`, `alias`, `note`, `path`, `link`, `type`, `published`, `parent_id`, `level`, `component_id`, `ordering`, `checked_out`, `checked_out_time`, `browserNav`, `access`, `img`, `template_style_id`, `params`, `lft`, `rgt`, `home`, `language`, `client_id`) VALUES (15,'menu','com_newsfeeds_categories','Categories','','News Feeds/Categories','index.php?option=com_categories&extension=com_newsfeeds','component',0,13,2,6,0,0,'0000-00-00 00:00:00',0,0,'class:newsfeeds-cat',0,'',26,27,0,'*',1);
INSERT INTO `#__menu` (`id`, `menutype`, `title`, `alias`, `note`, `path`, `link`, `type`, `published`, `parent_id`, `level`, `component_id`, `ordering`, `checked_out`, `checked_out_time`, `browserNav`, `access`, `img`, `template_style_id`, `params`, `lft`, `rgt`, `home`, `language`, `client_id`) VALUES (16,'menu','com_redirect','Redirect','','Redirect','index.php?option=com_redirect','component',0,1,1,24,0,0,'0000-00-00 00:00:00',0,0,'class:redirect',0,'',41,42,0,'*',1);
INSERT INTO `#__menu` (`id`, `menutype`, `title`, `alias`, `note`, `path`, `link`, `type`, `published`, `parent_id`, `level`, `component_id`, `ordering`, `checked_out`, `checked_out_time`, `browserNav`, `access`, `img`, `template_style_id`, `params`, `lft`, `rgt`, `home`, `language`, `client_id`) VALUES (17,'menu','com_search','Basic Search','','Basic Search','index.php?option=com_search','component',0,1,1,19,0,0,'0000-00-00 00:00:00',0,0,'class:search',0,'',33,34,0,'*',1);
INSERT INTO `#__menu` (`id`, `menutype`, `title`, `alias`, `note`, `path`, `link`, `type`, `published`, `parent_id`, `level`, `component_id`, `ordering`, `checked_out`, `checked_out_time`, `browserNav`, `access`, `img`, `template_style_id`, `params`, `lft`, `rgt`, `home`, `language`, `client_id`) VALUES (18,'menu','com_weblinks','Weblinks','','Weblinks','index.php?option=com_weblinks','component',0,1,1,21,0,0,'0000-00-00 00:00:00',0,0,'class:weblinks',0,'',35,40,0,'*',1);
INSERT INTO `#__menu` (`id`, `menutype`, `title`, `alias`, `note`, `path`, `link`, `type`, `published`, `parent_id`, `level`, `component_id`, `ordering`, `checked_out`, `checked_out_time`, `browserNav`, `access`, `img`, `template_style_id`, `params`, `lft`, `rgt`, `home`, `language`, `client_id`) VALUES (19,'menu','com_weblinks_links','Links','','Weblinks/Links','index.php?option=com_weblinks','component',0,18,2,21,0,0,'0000-00-00 00:00:00',0,0,'class:weblinks',0,'',36,37,0,'*',1);
INSERT INTO `#__menu` (`id`, `menutype`, `title`, `alias`, `note`, `path`, `link`, `type`, `published`, `parent_id`, `level`, `component_id`, `ordering`, `checked_out`, `checked_out_time`, `browserNav`, `access`, `img`, `template_style_id`, `params`, `lft`, `rgt`, `home`, `language`, `client_id`) VALUES (20,'menu','com_weblinks_categories','Categories','','Weblinks/Categories','index.php?option=com_categories&extension=com_weblinks','component',0,18,2,6,0,0,'0000-00-00 00:00:00',0,0,'class:weblinks-cat',0,'',38,39,0,'*',1);
INSERT INTO `#__menu` (`id`, `menutype`, `title`, `alias`, `note`, `path`, `link`, `type`, `published`, `parent_id`, `level`, `component_id`, `ordering`, `checked_out`, `checked_out_time`, `browserNav`, `access`, `img`, `template_style_id`, `params`, `lft`, `rgt`, `home`, `language`, `client_id`) VALUES (21,'menu','com_finder','Smart Search','','Smart Search','index.php?option=com_finder','component',0,1,1,27,0,0,'0000-00-00 00:00:00',0,0,'class:finder',0,'',31,32,0,'*',1);
INSERT INTO `#__menu` (`id`, `menutype`, `title`, `alias`, `note`, `path`, `link`, `type`, `published`, `parent_id`, `level`, `component_id`, `ordering`, `checked_out`, `checked_out_time`, `browserNav`, `access`, `img`, `template_style_id`, `params`, `lft`, `rgt`, `home`, `language`, `client_id`) VALUES (22,'menu','com_joomlaupdate','Joomla! Update','','Joomla! Update','index.php?option=com_joomlaupdate','component',0,1,1,28,0,0,'0000-00-00 00:00:00',0,0,'class:joomlaupdate',0,'',41,42,0,'*',1);
INSERT INTO `#__menu` (`id`, `menutype`, `title`, `alias`, `note`, `path`, `link`, `type`, `published`, `parent_id`, `level`, `component_id`, `ordering`, `checked_out`, `checked_out_time`, `browserNav`, `access`, `img`, `template_style_id`, `params`, `lft`, `rgt`, `home`, `language`, `client_id`) VALUES (101,'mainmenu','Home','home','','home','index.php?option=com_content&view=featured','component',1,1,1,22,0,0,'0000-00-00 00:00:00',0,1,'',0,'{\"featured_categories\":[\"\"],\"num_leading_articles\":\"1\",\"num_intro_articles\":\"3\",\"num_columns\":\"3\",\"num_links\":\"0\",\"orderby_pri\":\"\",\"orderby_sec\":\"front\",\"order_date\":\"\",\"multi_column_order\":\"1\",\"show_pagination\":\"2\",\"show_pagination_results\":\"1\",\"show_noauth\":\"\",\"article-allow_ratings\":\"\",\"article-allow_comments\":\"\",\"show_feed_link\":\"1\",\"feed_summary\":\"\",\"show_title\":\"\",\"link_titles\":\"\",\"show_intro\":\"\",\"show_category\":\"\",\"link_category\":\"\",\"show_parent_category\":\"\",\"link_parent_category\":\"\",\"show_author\":\"\",\"show_create_date\":\"\",\"show_modify_date\":\"\",\"show_publish_date\":\"\",\"show_item_navigation\":\"\",\"show_readmore\":\"\",\"show_icons\":\"\",\"show_print_icon\":\"\",\"show_email_icon\":\"\",\"show_hits\":\"\",\"menu-anchor_title\":\"\",\"menu-anchor_css\":\"\",\"menu_image\":\"\",\"show_page_heading\":1,\"page_title\":\"\",\"page_heading\":\"\",\"pageclass_sfx\":\"\",\"menu-meta_description\":\"\",\"menu-meta_keywords\":\"\",\"robots\":\"\",\"secure\":0}',29,30,1,'*',0);

--
-- Dumping data for table `#__menu_types`
--

INSERT INTO `#__menu_types` (`id`, `menutype`, `title`, `description`) VALUES (1,'mainmenu','Main Menu','The main menu for the site');

--
-- Dumping data for table `#__messages`
--


--
-- Dumping data for table `#__messages_cfg`
--


--
-- Dumping data for table `#__metrics_author_cluster`
--


--
-- Dumping data for table `#__metrics_ipgeo_cache`
--


--
-- Dumping data for table `#__migrations`
--


--
-- Dumping data for table `#__modules`
--

INSERT INTO `#__modules` (`id`, `title`, `note`, `content`, `ordering`, `position`, `checked_out`, `checked_out_time`, `publish_up`, `publish_down`, `published`, `module`, `access`, `showtitle`, `params`, `client_id`, `language`) VALUES (1,'Main Menu','','',1,'position-7',0,'0000-00-00 00:00:00','0000-00-00 00:00:00','0000-00-00 00:00:00',1,'mod_menu',1,1,'{\"menutype\":\"mainmenu\",\"startLevel\":\"0\",\"endLevel\":\"0\",\"showAllChildren\":\"0\",\"tag_id\":\"\",\"class_sfx\":\"\",\"window_open\":\"\",\"layout\":\"\",\"moduleclass_sfx\":\"_menu\",\"cache\":\"1\",\"cache_time\":\"900\",\"cachemode\":\"itemid\"}',0,'*');
INSERT INTO `#__modules` (`id`, `title`, `note`, `content`, `ordering`, `position`, `checked_out`, `checked_out_time`, `publish_up`, `publish_down`, `published`, `module`, `access`, `showtitle`, `params`, `client_id`, `language`) VALUES (2,'Login','','',1,'login',0,'0000-00-00 00:00:00','0000-00-00 00:00:00','0000-00-00 00:00:00',1,'mod_login',1,1,'',1,'*');
INSERT INTO `#__modules` (`id`, `title`, `note`, `content`, `ordering`, `position`, `checked_out`, `checked_out_time`, `publish_up`, `publish_down`, `published`, `module`, `access`, `showtitle`, `params`, `client_id`, `language`) VALUES (3,'Popular Articles','','',3,'cpanel',0,'0000-00-00 00:00:00','0000-00-00 00:00:00','0000-00-00 00:00:00',1,'mod_popular',3,1,'{\"count\":\"5\",\"catid\":\"\",\"user_id\":\"0\",\"layout\":\"_:default\",\"moduleclass_sfx\":\"\",\"cache\":\"0\",\"automatic_title\":\"1\"}',1,'*');
INSERT INTO `#__modules` (`id`, `title`, `note`, `content`, `ordering`, `position`, `checked_out`, `checked_out_time`, `publish_up`, `publish_down`, `published`, `module`, `access`, `showtitle`, `params`, `client_id`, `language`) VALUES (4,'Recently Added Articles','','',4,'cpanel',0,'0000-00-00 00:00:00','0000-00-00 00:00:00','0000-00-00 00:00:00',1,'mod_latest',3,1,'{\"count\":\"5\",\"ordering\":\"c_dsc\",\"catid\":\"\",\"user_id\":\"0\",\"layout\":\"_:default\",\"moduleclass_sfx\":\"\",\"cache\":\"0\",\"automatic_title\":\"1\"}',1,'*');
INSERT INTO `#__modules` (`id`, `title`, `note`, `content`, `ordering`, `position`, `checked_out`, `checked_out_time`, `publish_up`, `publish_down`, `published`, `module`, `access`, `showtitle`, `params`, `client_id`, `language`) VALUES (8,'Toolbar','','',1,'toolbar',0,'0000-00-00 00:00:00','0000-00-00 00:00:00','0000-00-00 00:00:00',1,'mod_toolbar',3,1,'',1,'*');
INSERT INTO `#__modules` (`id`, `title`, `note`, `content`, `ordering`, `position`, `checked_out`, `checked_out_time`, `publish_up`, `publish_down`, `published`, `module`, `access`, `showtitle`, `params`, `client_id`, `language`) VALUES (9,'Quick Icons','','',1,'icon',0,'0000-00-00 00:00:00','0000-00-00 00:00:00','0000-00-00 00:00:00',1,'mod_quickicon',3,1,'',1,'*');
INSERT INTO `#__modules` (`id`, `title`, `note`, `content`, `ordering`, `position`, `checked_out`, `checked_out_time`, `publish_up`, `publish_down`, `published`, `module`, `access`, `showtitle`, `params`, `client_id`, `language`) VALUES (12,'Admin Menu','','',1,'menu',0,'0000-00-00 00:00:00','0000-00-00 00:00:00','0000-00-00 00:00:00',1,'mod_menu',3,1,'{\"layout\":\"\",\"moduleclass_sfx\":\"\",\"shownew\":\"1\",\"showhelp\":\"1\",\"cache\":\"0\"}',1,'*');
INSERT INTO `#__modules` (`id`, `title`, `note`, `content`, `ordering`, `position`, `checked_out`, `checked_out_time`, `publish_up`, `publish_down`, `published`, `module`, `access`, `showtitle`, `params`, `client_id`, `language`) VALUES (13,'Admin Submenu','','',1,'submenu',0,'0000-00-00 00:00:00','0000-00-00 00:00:00','0000-00-00 00:00:00',1,'mod_submenu',3,1,'',1,'*');
INSERT INTO `#__modules` (`id`, `title`, `note`, `content`, `ordering`, `position`, `checked_out`, `checked_out_time`, `publish_up`, `publish_down`, `published`, `module`, `access`, `showtitle`, `params`, `client_id`, `language`) VALUES (15,'Title','','',1,'title',0,'0000-00-00 00:00:00','0000-00-00 00:00:00','0000-00-00 00:00:00',1,'mod_title',3,1,'',1,'*');
INSERT INTO `#__modules` (`id`, `title`, `note`, `content`, `ordering`, `position`, `checked_out`, `checked_out_time`, `publish_up`, `publish_down`, `published`, `module`, `access`, `showtitle`, `params`, `client_id`, `language`) VALUES (16,'Login Form','','',7,'position-7',0,'0000-00-00 00:00:00','0000-00-00 00:00:00','0000-00-00 00:00:00',1,'mod_login',1,1,'{\"greeting\":\"1\",\"name\":\"0\"}',0,'*');
INSERT INTO `#__modules` (`id`, `title`, `note`, `content`, `ordering`, `position`, `checked_out`, `checked_out_time`, `publish_up`, `publish_down`, `published`, `module`, `access`, `showtitle`, `params`, `client_id`, `language`) VALUES (17,'Breadcrumbs','','',1,'breadcrumbs',0,'0000-00-00 00:00:00','0000-00-00 00:00:00','0000-00-00 00:00:00',1,'mod_breadcrumbs',1,1,'{\"moduleclass_sfx\":\"\",\"showHome\":\"1\",\"homeText\":\"Home\",\"showComponent\":\"1\",\"separator\":\"/\",\"cache\":\"1\",\"cache_time\":\"900\",\"cachemode\":\"itemid\"}',0,'*');
INSERT INTO `#__modules` (`id`, `title`, `note`, `content`, `ordering`, `position`, `checked_out`, `checked_out_time`, `publish_up`, `publish_down`, `published`, `module`, `access`, `showtitle`, `params`, `client_id`, `language`) VALUES (79,'Multilanguage status','','',1,'status',0,'0000-00-00 00:00:00','0000-00-00 00:00:00','0000-00-00 00:00:00',0,'mod_multilangstatus',3,1,'{\"layout\":\"_:default\",\"moduleclass_sfx\":\"\",\"cache\":\"0\"}',1,'*');
INSERT INTO `#__modules` (`id`, `title`, `note`, `content`, `ordering`, `position`, `checked_out`, `checked_out_time`, `publish_up`, `publish_down`, `published`, `module`, `access`, `showtitle`, `params`, `client_id`, `language`) VALUES (86,'Joomla Version','','',1,'footer',0,'0000-00-00 00:00:00','0000-00-00 00:00:00','0000-00-00 00:00:00',1,'mod_version',3,1,'{\"format\":\"short\",\"product\":\"1\",\"layout\":\"_:default\",\"moduleclass_sfx\":\"\",\"cache\":\"0\"}',1,'*');

--
-- Dumping data for table `#__modules_menu`
--

INSERT INTO `#__modules_menu` (`moduleid`, `menuid`) VALUES (1,0);
INSERT INTO `#__modules_menu` (`moduleid`, `menuid`) VALUES (2,0);
INSERT INTO `#__modules_menu` (`moduleid`, `menuid`) VALUES (3,0);
INSERT INTO `#__modules_menu` (`moduleid`, `menuid`) VALUES (4,0);
INSERT INTO `#__modules_menu` (`moduleid`, `menuid`) VALUES (6,0);
INSERT INTO `#__modules_menu` (`moduleid`, `menuid`) VALUES (7,0);
INSERT INTO `#__modules_menu` (`moduleid`, `menuid`) VALUES (8,0);
INSERT INTO `#__modules_menu` (`moduleid`, `menuid`) VALUES (9,0);
INSERT INTO `#__modules_menu` (`moduleid`, `menuid`) VALUES (10,0);
INSERT INTO `#__modules_menu` (`moduleid`, `menuid`) VALUES (12,0);
INSERT INTO `#__modules_menu` (`moduleid`, `menuid`) VALUES (13,0);
INSERT INTO `#__modules_menu` (`moduleid`, `menuid`) VALUES (14,0);
INSERT INTO `#__modules_menu` (`moduleid`, `menuid`) VALUES (15,0);
INSERT INTO `#__modules_menu` (`moduleid`, `menuid`) VALUES (16,0);
INSERT INTO `#__modules_menu` (`moduleid`, `menuid`) VALUES (17,0);
INSERT INTO `#__modules_menu` (`moduleid`, `menuid`) VALUES (79,0);
INSERT INTO `#__modules_menu` (`moduleid`, `menuid`) VALUES (86,0);

--
-- Dumping data for table `#__newsfeeds`
--


--
-- Dumping data for table `#__newsletter_mailing_recipient_actions`
--


--
-- Dumping data for table `#__newsletter_mailing_recipients`
--


--
-- Dumping data for table `#__newsletter_mailinglist_emails`
--


--
-- Dumping data for table `#__newsletter_mailinglist_unsubscribes`
--


--
-- Dumping data for table `#__newsletter_mailinglists`
--


--
-- Dumping data for table `#__newsletter_mailings`
--


--
-- Dumping data for table `#__newsletter_primary_story`
--


--
-- Dumping data for table `#__newsletter_secondary_story`
--


--
-- Dumping data for table `#__newsletter_templates`
--


--
-- Dumping data for table `#__newsletters`
--


--
-- Dumping data for table `#__oauthp_consumers`
--


--
-- Dumping data for table `#__oauthp_nonces`
--


--
-- Dumping data for table `#__oauthp_tokens`
--


--
-- Dumping data for table `#__order_items`
--


--
-- Dumping data for table `#__orders`
--


--
-- Dumping data for table `#__overrider`
--


--
-- Dumping data for table `#__password_blacklist`
--


--
-- Dumping data for table `#__password_character_class`
--


--
-- Dumping data for table `#__password_rule`
--


--
-- Dumping data for table `#__plugin_params`
--


--
-- Dumping data for table `#__poll_data`
--


--
-- Dumping data for table `#__poll_date`
--


--
-- Dumping data for table `#__poll_menu`
--


--
-- Dumping data for table `#__polls`
--


--
-- Dumping data for table `#__profile_completion_awards`
--


--
-- Dumping data for table `#__project_activity`
--


--
-- Dumping data for table `#__project_comments`
--


--
-- Dumping data for table `#__project_database_versions`
--


--
-- Dumping data for table `#__project_databases`
--


--
-- Dumping data for table `#__project_logs`
--


--
-- Dumping data for table `#__project_microblog`
--


--
-- Dumping data for table `#__project_owners`
--


--
-- Dumping data for table `#__project_public_stamps`
--


--
-- Dumping data for table `#__project_remote_files`
--


--
-- Dumping data for table `#__project_stats`
--


--
-- Dumping data for table `#__project_todo`
--


--
-- Dumping data for table `#__project_types`
--

INSERT INTO `#__project_types` (`id`, `type`, `description`, `params`) VALUES (1,'General','Individual or collaborative projects of general nature','apps_dev=0\npublications_public=1\nteam_public=1\nallow_invite=0');
INSERT INTO `#__project_types` (`id`, `type`, `description`, `params`) VALUES (2,'Content publication','Projects created with the purpose to publish data as a resource or a collection of related resources','apps_dev=0\npublications_public=1\nteam_public=1\nallow_invite=0');
INSERT INTO `#__project_types` (`id`, `type`, `description`, `params`) VALUES (3,'Application development','Projects created with the purpose to develop and publish a simulation tool or a code library','apps_dev=1\npublications_public=1\nteam_public=1\nallow_invite=0');

--
-- Dumping data for table `#__projects`
--


--
-- Dumping data for table `#__publication_access`
--


--
-- Dumping data for table `#__publication_attachments`
--


--
-- Dumping data for table `#__publication_audience`
--


--
-- Dumping data for table `#__publication_audience_levels`
--

INSERT INTO `#__publication_audience_levels` (`id`, `label`, `title`, `description`) VALUES (1,'level0','K12','Middle/High School');
INSERT INTO `#__publication_audience_levels` (`id`, `label`, `title`, `description`) VALUES (2,'level1','Easy','Freshmen/Sophomores');
INSERT INTO `#__publication_audience_levels` (`id`, `label`, `title`, `description`) VALUES (3,'level2','Intermediate','Juniors/Seniors');
INSERT INTO `#__publication_audience_levels` (`id`, `label`, `title`, `description`) VALUES (4,'level3','Advanced','Graduate Students');
INSERT INTO `#__publication_audience_levels` (`id`, `label`, `title`, `description`) VALUES (5,'level4','Expert','PhD Experts');
INSERT INTO `#__publication_audience_levels` (`id`, `label`, `title`, `description`) VALUES (6,'level5','Professional','Beyond PhD');

--
-- Dumping data for table `#__publication_authors`
--


--
-- Dumping data for table `#__publication_blocks`
--


--
-- Dumping data for table `#__publication_categories`
--

INSERT INTO `#__publication_categories` (`id`, `name`, `dc_type`, `alias`, `url_alias`, `description`, `contributable`, `state`, `customFields`, `params`) VALUES (1,'Datasets','Dataset','dataset','datasets','A collection of research data',1,1,'bio=Bio=textarea=0\ncredits=Credits=textarea=0\ncitations=Citations=textarea=0\nsponsoredby=Sponsored by=textarea=0\nreferences=References=textarea=0\npublications=Publications=textarea=0','plg_reviews=1\nplg_questions=1\nplg_supportingdocs=1\nplg_versions=1\nplg_wishlist=1\nplg_citations=1\nplg_usage = 1');
INSERT INTO `#__publication_categories` (`id`, `name`, `dc_type`, `alias`, `url_alias`, `description`, `contributable`, `state`, `customFields`, `params`) VALUES (2,'Workshops','Event','workshop','workshops','A collection of lectures, seminars, and materials that were presented at a workshop.',0,0,'bio=Bio=textarea=0\ncredits=Credits=textarea=0\ncitations=Citations=textarea=0\nsponsoredby=Sponsored by=textarea=0\nreferences=References=textarea=0\npublications=Publications=textarea=0','plg_reviews=1\nplg_questions=1\nplg_supportingdocs=1\nplg_versions=1');
INSERT INTO `#__publication_categories` (`id`, `name`, `dc_type`, `alias`, `url_alias`, `description`, `contributable`, `state`, `customFields`, `params`) VALUES (3,'Publications','Dataset','publication','publications','A publication is a paper relevant to the community that has been published in some manner.',0,0,'bio=Bio=textarea=0\ncredits=Credits=textarea=0\ncitations=Citations=textarea=0\nsponsoredby=Sponsored by=textarea=0\nreferences=References=textarea=0\npublications=Publications=textarea=0','plg_reviews=1\nplg_questions=1\nplg_supportingdocs=1\nplg_versions=1');
INSERT INTO `#__publication_categories` (`id`, `name`, `dc_type`, `alias`, `url_alias`, `description`, `contributable`, `state`, `customFields`, `params`) VALUES (4,'Learning Modules','InteractiveResource','learning module','learningmodules','A combination of presentations, tools, assignments, etc. geared toward teaching a specific concept.',0,0,'bio=Bio=textarea=0\ncredits=Credits=textarea=0\ncitations=Citations=textarea=0\nsponsoredby=Sponsored by=textarea=0\nreferences=References=textarea=0\npublications=Publications=textarea=0','plg_reviews=1\nplg_questions=1\nplg_supportingdocs=1\nplg_versions=1');
INSERT INTO `#__publication_categories` (`id`, `name`, `dc_type`, `alias`, `url_alias`, `description`, `contributable`, `state`, `customFields`, `params`) VALUES (5,'Animations','MovingImage','animation','animations','An animation is a Flash-based demo or short movie that illustrates some concept.',0,0,'bio=Bio=textarea=0\ncredits=Credits=textarea=0\ncitations=Citations=textarea=0\nsponsoredby=Sponsored by=textarea=0\nreferences=References=textarea=0\npublications=Publications=textarea=0','plg_reviews=1\nplg_questions=1\nplg_supportingdocs=1\nplg_versions=1');
INSERT INTO `#__publication_categories` (`id`, `name`, `dc_type`, `alias`, `url_alias`, `description`, `contributable`, `state`, `customFields`, `params`) VALUES (6,'Courses','Collection','course','courses','University courses that make videos of lectures and associated teaching materials available.',0,0,'bio=Bio=textarea=0\ncredits=Credits=textarea=0\ncitations=Citations=textarea=0\nsponsoredby=Sponsored by=textarea=0\nreferences=References=textarea=0\npublications=Publications=textarea=0','plg_reviews=1\nplg_questions=1\nplg_supportingdocs=1\nplg_versions=1');
INSERT INTO `#__publication_categories` (`id`, `name`, `dc_type`, `alias`, `url_alias`, `description`, `contributable`, `state`, `customFields`, `params`) VALUES (7,'Tools','Software','tool','tools','A simulation tool is software that allows users to run a specific type of calculation.',0,1,'poweredby=Powered by=textarea=0\nbio=Bio=textarea=0\ncredits=Credits=textarea=0\ncitations=Citations=textarea=0\nsponsoredby=Sponsored by=textarea=0\nreferences=References=textarea=0\npublications=Publications=textarea=0','plg_reviews=1\nplg_questions=1\nplg_supportingdocs=1\nplg_versions=1');
INSERT INTO `#__publication_categories` (`id`, `name`, `dc_type`, `alias`, `url_alias`, `description`, `contributable`, `state`, `customFields`, `params`) VALUES (9,'Downloads','PhysicalObject','download','downloads','A download is a type of resource that users can download and use on their own computer.',0,0,'bio=Bio=textarea=0\ncredits=Credits=textarea=0\ncitations=Citations=textarea=0\nsponsoredby=Sponsored by=textarea=0\nreferences=References=textarea=0\npublications=Publications=textarea=0','plg_reviews=1\nplg_questions=1\nplg_supportingdocs=1\nplg_versions=1');
INSERT INTO `#__publication_categories` (`id`, `name`, `dc_type`, `alias`, `url_alias`, `description`, `contributable`, `state`, `customFields`, `params`) VALUES (10,'Notes','Text','note','notes','Notes are typically a category for any resource that might not fit any of the other categories.',0,0,'bio=Bio=textarea=0\ncredits=Credits=textarea=0\ncitations=Citations=textarea=0\nsponsoredby=Sponsored by=textarea=0\nreferences=References=textarea=0\npublications=Publications=textarea=0','plg_reviews=1\nplg_questions=1\nplg_supportingdocs=1\nplg_versions=1');
INSERT INTO `#__publication_categories` (`id`, `name`, `dc_type`, `alias`, `url_alias`, `description`, `contributable`, `state`, `customFields`, `params`) VALUES (11,'Series','Collection','series','series','Series are collections of other resources, typically online presentations, that cover a specific topic.',0,0,'bio=Bio=textarea=0\ncredits=Credits=textarea=0\ncitations=Citations=textarea=0\nsponsoredby=Sponsored by=textarea=0\nreferences=References=textarea=0\npublications=Publications=textarea=0','plg_reviews=1\nplg_questions=1\nplg_supportingdocs=1\nplg_versions=1');
INSERT INTO `#__publication_categories` (`id`, `name`, `dc_type`, `alias`, `url_alias`, `description`, `contributable`, `state`, `customFields`, `params`) VALUES (12,'Teaching Materials','Text','teaching material','teachingmaterials','Supplementary materials (study notes, guides, etc.) that don\'t quite fit into any of the other categories.',0,0,'bio=Bio=textarea=0\ncredits=Credits=textarea=0\ncitations=Citations=textarea=0\nsponsoredby=Sponsored by=textarea=0\nreferences=References=textarea=0\npublications=Publications=textarea=0','plg_reviews=1\nplg_questions=1\nplg_supportingdocs=1\nplg_versions=1');

--
-- Dumping data for table `#__publication_curation`
--


--
-- Dumping data for table `#__publication_curation_history`
--


--
-- Dumping data for table `#__publication_handlers`
--


--
-- Dumping data for table `#__publication_licenses`
--

INSERT INTO `#__publication_licenses` (`id`, `name`, `text`, `title`, `url`, `info`, `ordering`, `active`, `apps_only`, `main`, `agreement`, `customizable`, `icon`, `opensource`, `restriction`) VALUES (1,'custom','[ONE LINE DESCRIPTION]\r\nCopyright (C) [YEAR] [OWNER]','Custom','http://creativecommons.org/about/cc0','Custom license',3,1,0,0,0,1,'/components/com_publications/assets/img/logos/license.gif',0,NULL);
INSERT INTO `#__publication_licenses` (`id`, `name`, `text`, `title`, `url`, `info`, `ordering`, `active`, `apps_only`, `main`, `agreement`, `customizable`, `icon`, `opensource`, `restriction`) VALUES (2,'cc','','CC0 - Creative Commons','http://creativecommons.org/about/cc0','CC0 enables scientists, educators, artists and other creators and owners of copyright- or database-protected content to waive those interests in their works and thereby place them as completely as possible in the public domain, so that others may freely build upon, enhance and reuse the works for any purposes without restriction under copyright or database law.',2,1,0,1,1,0,'/components/com_publications/assets/img/logos/cc.gif',0,NULL);
INSERT INTO `#__publication_licenses` (`id`, `name`, `text`, `title`, `url`, `info`, `ordering`, `active`, `apps_only`, `main`, `agreement`, `customizable`, `icon`, `opensource`, `restriction`) VALUES (3,'standard','All rights reserved.','Standard HUB License','http://nanohub.org','Standard HUB license.',1,0,0,0,0,0,'/components/com_publications/images/logos/license.gif',0,NULL);

--
-- Dumping data for table `#__publication_logs`
--


--
-- Dumping data for table `#__publication_master_types`
--

INSERT INTO `#__publication_master_types` (`id`, `type`, `alias`, `description`, `contributable`, `supporting`, `ordering`, `params`, `curation`, `curatorgroup`) VALUES (1,'File(s)','files','uploaded material',1,1,1,'peer_review=1',NULL,NULL);
INSERT INTO `#__publication_master_types` (`id`, `type`, `alias`, `description`, `contributable`, `supporting`, `ordering`, `params`, `curation`, `curatorgroup`) VALUES (2,'Link','links','external content',0,0,3,'',NULL,NULL);
INSERT INTO `#__publication_master_types` (`id`, `type`, `alias`, `description`, `contributable`, `supporting`, `ordering`, `params`, `curation`, `curatorgroup`) VALUES (3,'Wiki','notes','from project notes',0,0,5,'',NULL,NULL);
INSERT INTO `#__publication_master_types` (`id`, `type`, `alias`, `description`, `contributable`, `supporting`, `ordering`, `params`, `curation`, `curatorgroup`) VALUES (4,'Application','apps','simulation tool',0,0,4,'',NULL,NULL);
INSERT INTO `#__publication_master_types` (`id`, `type`, `alias`, `description`, `contributable`, `supporting`, `ordering`, `params`, `curation`, `curatorgroup`) VALUES (5,'Series','series','publication collection',0,0,6,'',NULL,NULL);
INSERT INTO `#__publication_master_types` (`id`, `type`, `alias`, `description`, `contributable`, `supporting`, `ordering`, `params`, `curation`, `curatorgroup`) VALUES (6,'Gallery','gallery','image/photo gallery',0,0,7,'',NULL,NULL);
INSERT INTO `#__publication_master_types` (`id`, `type`, `alias`, `description`, `contributable`, `supporting`, `ordering`, `params`, `curation`, `curatorgroup`) VALUES (7,'Databases','databases','project database',0,0,2,'',NULL,NULL);

--
-- Dumping data for table `#__publication_ratings`
--


--
-- Dumping data for table `#__publication_screenshots`
--


--
-- Dumping data for table `#__publication_stats`
--


--
-- Dumping data for table `#__publication_versions`
--


--
-- Dumping data for table `#__publications`
--


--
-- Dumping data for table `#__recent_tools`
--


--
-- Dumping data for table `#__recommendation`
--


--
-- Dumping data for table `#__redirect_links`
--


--
-- Dumping data for table `#__redirection`
--


--
-- Dumping data for table `#__resource_assoc`
--


--
-- Dumping data for table `#__resource_import_hooks`
--


--
-- Dumping data for table `#__resource_import_runs`
--


--
-- Dumping data for table `#__resource_imports`
--


--
-- Dumping data for table `#__resource_licenses`
--


--
-- Dumping data for table `#__resource_ratings`
--


--
-- Dumping data for table `#__resource_sponsors`
--


--
-- Dumping data for table `#__resource_stats`
--


--
-- Dumping data for table `#__resource_stats_clusters`
--


--
-- Dumping data for table `#__resource_stats_tools`
--


--
-- Dumping data for table `#__resource_stats_tools_tops`
--

INSERT INTO `#__resource_stats_tools_tops` (`top`, `name`, `valfmt`, `size`) VALUES (1,'Users By Country Of Residence',1,5);
INSERT INTO `#__resource_stats_tools_tops` (`top`, `name`, `valfmt`, `size`) VALUES (2,'Top Domains By User Count',1,5);
INSERT INTO `#__resource_stats_tools_tops` (`top`, `name`, `valfmt`, `size`) VALUES (3,'Users By Organization Type',1,5);

--
-- Dumping data for table `#__resource_stats_tools_topvals`
--


--
-- Dumping data for table `#__resource_stats_tools_users`
--


--
-- Dumping data for table `#__resource_taxonomy_audience`
--


--
-- Dumping data for table `#__resource_taxonomy_audience_levels`
--


--
-- Dumping data for table `#__resource_types`
--


--
-- Dumping data for table `#__resources`
--


--
-- Dumping data for table `#__schemas`
--


--
-- Dumping data for table `#__screenshots`
--


--
-- Dumping data for table `#__session`
--


--
-- Dumping data for table `#__session_geo`
--


--
-- Dumping data for table `#__session_log`
--


--
-- Dumping data for table `#__stats_tops`
--

INSERT INTO `#__stats_tops` (`id`, `name`, `valfmt`, `size`) VALUES (1,'Top Tools by Ranking',1,5);
INSERT INTO `#__stats_tops` (`id`, `name`, `valfmt`, `size`) VALUES (2,'Top Tools by Simulation Users',1,5);
INSERT INTO `#__stats_tops` (`id`, `name`, `valfmt`, `size`) VALUES (3,'Top Tools by Interactive Sessions',1,5);
INSERT INTO `#__stats_tops` (`id`, `name`, `valfmt`, `size`) VALUES (4,'Top Tools by Simulation Sessions',1,5);
INSERT INTO `#__stats_tops` (`id`, `name`, `valfmt`, `size`) VALUES (5,'Top Tools by Simulation Runs',1,5);
INSERT INTO `#__stats_tops` (`id`, `name`, `valfmt`, `size`) VALUES (6,'Top Tools by Simulation Wall Time',2,5);
INSERT INTO `#__stats_tops` (`id`, `name`, `valfmt`, `size`) VALUES (7,'Top Tools by Simulation CPU Time',2,5);
INSERT INTO `#__stats_tops` (`id`, `name`, `valfmt`, `size`) VALUES (8,'Top Tools by Simulation Interaction Time',2,5);
INSERT INTO `#__stats_tops` (`id`, `name`, `valfmt`, `size`) VALUES (9,'Top Tools by Citations',1,5);

--
-- Dumping data for table `#__stats_topvals`
--


--
-- Dumping data for table `#__store`
--


--
-- Dumping data for table `#__storefront_collections`
--


--
-- Dumping data for table `#__storefront_coupon_actions`
--


--
-- Dumping data for table `#__storefront_coupon_conditions`
--


--
-- Dumping data for table `#__storefront_coupon_objects`
--


--
-- Dumping data for table `#__storefront_coupons`
--


--
-- Dumping data for table `#__storefront_option_groups`
--


--
-- Dumping data for table `#__storefront_options`
--


--
-- Dumping data for table `#__storefront_product_collections`
--


--
-- Dumping data for table `#__storefront_product_option_groups`
--


--
-- Dumping data for table `#__storefront_product_types`
--


--
-- Dumping data for table `#__storefront_products`
--


--
-- Dumping data for table `#__storefront_sku_meta`
--


--
-- Dumping data for table `#__storefront_sku_options`
--


--
-- Dumping data for table `#__storefront_skus`
--


--
-- Dumping data for table `#__support_acl_acos`
--


--
-- Dumping data for table `#__support_acl_aros`
--


--
-- Dumping data for table `#__support_acl_aros_acos`
--


--
-- Dumping data for table `#__support_attachments`
--


--
-- Dumping data for table `#__support_categories`
--


--
-- Dumping data for table `#__support_comments`
--


--
-- Dumping data for table `#__support_messages`
--


--
-- Dumping data for table `#__support_queries`
--


--
-- Dumping data for table `#__support_query_folders`
--


--
-- Dumping data for table `#__support_resolutions`
--


--
-- Dumping data for table `#__support_sections`
--


--
-- Dumping data for table `#__support_statuses`
--


--
-- Dumping data for table `#__support_tickets`
--


--
-- Dumping data for table `#__support_watching`
--


--
-- Dumping data for table `#__tags`
--


--
-- Dumping data for table `#__tags_log`
--


--
-- Dumping data for table `#__tags_object`
--


--
-- Dumping data for table `#__tags_substitute`
--


--
-- Dumping data for table `#__template_styles`
--

INSERT INTO `#__template_styles` (`id`, `template`, `client_id`, `home`, `title`, `params`) VALUES (1,'welcome',0,'1','Welcome Template','{\"flavor\":\"\",\"template\":\"kimera\"}');
INSERT INTO `#__template_styles` (`id`, `template`, `client_id`, `home`, `title`, `params`) VALUES (2,'kameleon',1,'1','kameleon (admin)','{\"header\":\"dark\",\"theme\":\"bluesteel\"}');
INSERT INTO `#__template_styles` (`id`, `template`, `client_id`, `home`, `title`, `params`) VALUES (3,'kimera',0,'0','HUBzero Standard Site Template - 2015','{}');

--
-- Dumping data for table `#__tool`
--


--
-- Dumping data for table `#__tool_authors`
--


--
-- Dumping data for table `#__tool_groups`
--


--
-- Dumping data for table `#__tool_licenses`
--


--
-- Dumping data for table `#__tool_statusviews`
--


--
-- Dumping data for table `#__tool_version`
--


--
-- Dumping data for table `#__tool_version_alias`
--


--
-- Dumping data for table `#__tool_version_hostreq`
--


--
-- Dumping data for table `#__tool_version_middleware`
--


--
-- Dumping data for table `#__tool_version_tracperm`
--


--
-- Dumping data for table `#__tool_version_zone`
--


--
-- Dumping data for table `#__trac_group_permission`
--


--
-- Dumping data for table `#__trac_project`
--


--
-- Dumping data for table `#__trac_projects`
--


--
-- Dumping data for table `#__trac_user_permission`
--


--
-- Dumping data for table `#__update_categories`
--


--
-- Dumping data for table `#__update_sites`
--

INSERT INTO `#__update_sites` (`update_site_id`, `name`, `type`, `location`, `enabled`, `last_check_timestamp`) VALUES (1,'Joomla Core','collection','http://update.joomla.org/core/list.xml',1,0);
INSERT INTO `#__update_sites` (`update_site_id`, `name`, `type`, `location`, `enabled`, `last_check_timestamp`) VALUES (2,'Joomla Extension Directory','collection','http://update.joomla.org/jed/list.xml',1,0);
INSERT INTO `#__update_sites` (`update_site_id`, `name`, `type`, `location`, `enabled`, `last_check_timestamp`) VALUES (3,'Accredited Joomla! Translations','collection','http://update.joomla.org/language/translationlist.xml',1,0);

--
-- Dumping data for table `#__update_sites_extensions`
--

INSERT INTO `#__update_sites_extensions` (`update_site_id`, `extension_id`) VALUES (1,700);
INSERT INTO `#__update_sites_extensions` (`update_site_id`, `extension_id`) VALUES (2,700);
INSERT INTO `#__update_sites_extensions` (`update_site_id`, `extension_id`) VALUES (3,600);

--
-- Dumping data for table `#__updates`
--


--
-- Dumping data for table `#__user_notes`
--


--
-- Dumping data for table `#__user_profiles`
--


--
-- Dumping data for table `#__user_roles`
--


--
-- Dumping data for table `#__user_usergroup_map`
--


--
-- Dumping data for table `#__usergroups`
--

INSERT INTO `#__usergroups` (`id`, `parent_id`, `lft`, `rgt`, `title`) VALUES (1,0,1,20, 'Public');
INSERT INTO `#__usergroups` (`id`, `parent_id`, `lft`, `rgt`, `title`) VALUES (2,1,6,17,     'Registered');
INSERT INTO `#__usergroups` (`id`, `parent_id`, `lft`, `rgt`, `title`) VALUES (3,2,7,14,         'Author');
INSERT INTO `#__usergroups` (`id`, `parent_id`, `lft`, `rgt`, `title`) VALUES (4,3,8,11,             'Editor');
INSERT INTO `#__usergroups` (`id`, `parent_id`, `lft`, `rgt`, `title`) VALUES (5,4,9,10,                 'Publisher');
INSERT INTO `#__usergroups` (`id`, `parent_id`, `lft`, `rgt`, `title`) VALUES (6,1,2,5,  'Manager');
INSERT INTO `#__usergroups` (`id`, `parent_id`, `lft`, `rgt`, `title`) VALUES (7,6,3,4,      'Administrator');
INSERT INTO `#__usergroups` (`id`, `parent_id`, `lft`, `rgt`, `title`) VALUES (8,1,18,19,'Super Users');

--
-- Dumping data for table `#__users`
--


--
-- Dumping data for table `#__users_merge_log`
--


--
-- Dumping data for table `#__users_password`
--


--
-- Dumping data for table `#__users_password_history`
--


--
-- Dumping data for table `#__users_points`
--


--
-- Dumping data for table `#__users_points_config`
--


--
-- Dumping data for table `#__users_points_services`
--


--
-- Dumping data for table `#__users_points_subscriptions`
--


--
-- Dumping data for table `#__users_quotas`
--


--
-- Dumping data for table `#__users_quotas_classes`
--

INSERT INTO `#__users_quotas_classes` (`id`, `alias`, `hard_files`, `soft_files`, `hard_blocks`, `soft_blocks`) VALUES (1,'default',0,0,1000000,900000);

--
-- Dumping data for table `#__users_quotas_classes_groups`
--


--
-- Dumping data for table `#__users_quotas_log`
--


--
-- Dumping data for table `#__users_tracperms`
--


--
-- Dumping data for table `#__users_transactions`
--


--
-- Dumping data for table `#__viewlevels`
--

INSERT INTO `#__viewlevels` (`id`, `title`, `ordering`, `rules`) VALUES (1,'Public',0,'[1]');
INSERT INTO `#__viewlevels` (`id`, `title`, `ordering`, `rules`) VALUES (2,'Registered',1,'[6,2,8]');
INSERT INTO `#__viewlevels` (`id`, `title`, `ordering`, `rules`) VALUES (3,'Special',2,'[6,3,8]');

--
-- Dumping data for table `#__vote_log`
--


--
-- Dumping data for table `#__wiki_attachments`
--


--
-- Dumping data for table `#__wiki_comments`
--


--
-- Dumping data for table `#__wiki_log`
--


--
-- Dumping data for table `#__wiki_math`
--


--
-- Dumping data for table `#__wiki_page`
--


--
-- Dumping data for table `#__wiki_page_author`
--


--
-- Dumping data for table `#__wiki_page_links`
--


--
-- Dumping data for table `#__wiki_page_metrics`
--


--
-- Dumping data for table `#__wiki_version`
--


--
-- Dumping data for table `#__wish_attachments`
--


--
-- Dumping data for table `#__wishlist`
--


--
-- Dumping data for table `#__wishlist_implementation`
--


--
-- Dumping data for table `#__wishlist_item`
--


--
-- Dumping data for table `#__wishlist_ownergroups`
--


--
-- Dumping data for table `#__wishlist_owners`
--


--
-- Dumping data for table `#__wishlist_vote`
--


--
-- Dumping data for table `#__xdomain_users`
--


--
-- Dumping data for table `#__xdomains`
--


--
-- Dumping data for table `#__xgroups`
--


--
-- Dumping data for table `#__xgroups_applicants`
--


--
-- Dumping data for table `#__xgroups_inviteemails`
--


--
-- Dumping data for table `#__xgroups_invitees`
--


--
-- Dumping data for table `#__xgroups_log`
--


--
-- Dumping data for table `#__xgroups_managers`
--


--
-- Dumping data for table `#__xgroups_member_roles`
--


--
-- Dumping data for table `#__xgroups_memberoption`
--


--
-- Dumping data for table `#__xgroups_members`
--


--
-- Dumping data for table `#__xgroups_modules`
--


--
-- Dumping data for table `#__xgroups_modules_menu`
--


--
-- Dumping data for table `#__xgroups_pages`
--


--
-- Dumping data for table `#__xgroups_pages_categories`
--


--
-- Dumping data for table `#__xgroups_pages_checkout`
--


--
-- Dumping data for table `#__xgroups_pages_hits`
--


--
-- Dumping data for table `#__xgroups_pages_versions`
--


--
-- Dumping data for table `#__xgroups_reasons`
--


--
-- Dumping data for table `#__xgroups_roles`
--


--
-- Dumping data for table `#__xgroups_tracperm`
--


--
-- Dumping data for table `#__xmessage`
--


--
-- Dumping data for table `#__xmessage_action`
--


--
-- Dumping data for table `#__xmessage_component`
--

INSERT INTO `#__xmessage_component` (`id`, `component`, `action`, `title`) VALUES (1,'com_support','support_reply_submitted','Someone replies to a support ticket I submitted.');
INSERT INTO `#__xmessage_component` (`id`, `component`, `action`, `title`) VALUES (2,'com_support','support_reply_assigned','Someone replies to a support ticket I am assigned to.');
INSERT INTO `#__xmessage_component` (`id`, `component`, `action`, `title`) VALUES (3,'com_support','support_close_submitted','Someone closes a support ticket I submitted.');
INSERT INTO `#__xmessage_component` (`id`, `component`, `action`, `title`) VALUES (4,'com_answers','answers_reply_submitted','Someone answers a question I submitted.');
INSERT INTO `#__xmessage_component` (`id`, `component`, `action`, `title`) VALUES (5,'com_answers','answers_reply_comment','Someone replies to a comment I posted.');
INSERT INTO `#__xmessage_component` (`id`, `component`, `action`, `title`) VALUES (6,'com_answers','answers_question_deleted','Someone deletes a question I replied to.');
INSERT INTO `#__xmessage_component` (`id`, `component`, `action`, `title`) VALUES (7,'com_groups','groups_requests_membership','Someone requests membership to a group I manage.');
INSERT INTO `#__xmessage_component` (`id`, `component`, `action`, `title`) VALUES (8,'com_groups','groups_requests_status','Someone is approved/denied membership to a group I manage.');
INSERT INTO `#__xmessage_component` (`id`, `component`, `action`, `title`) VALUES (9,'com_groups','groups_cancels_membership','Someone cancels membership to a group I manage.');
INSERT INTO `#__xmessage_component` (`id`, `component`, `action`, `title`) VALUES (10,'com_groups','groups_promoted_demoted','Someone promotes/demotes a member of a group I manage.');
INSERT INTO `#__xmessage_component` (`id`, `component`, `action`, `title`) VALUES (11,'com_groups','groups_approved_denied','My membership request to a group is approved or denied.');
INSERT INTO `#__xmessage_component` (`id`, `component`, `action`, `title`) VALUES (12,'com_groups','groups_status_changed','My membership status changes');
INSERT INTO `#__xmessage_component` (`id`, `component`, `action`, `title`) VALUES (13,'com_groups','groups_cancelled_me','My membership to a group is cancelled.');
INSERT INTO `#__xmessage_component` (`id`, `component`, `action`, `title`) VALUES (14,'com_groups','groups_changed','Someone changes the settings of a group I am a member of.');
INSERT INTO `#__xmessage_component` (`id`, `component`, `action`, `title`) VALUES (15,'com_groups','groups_deleted','Someone deletes a group I am a member of.');
INSERT INTO `#__xmessage_component` (`id`, `component`, `action`, `title`) VALUES (16,'com_resources','resources_submission_approved','A contribution I submitted is approved.');
INSERT INTO `#__xmessage_component` (`id`, `component`, `action`, `title`) VALUES (17,'com_resources','resources_new_comment','Someone adds a review/comment to one of my contributions.');
INSERT INTO `#__xmessage_component` (`id`, `component`, `action`, `title`) VALUES (18,'com_store','store_notifications','Shipping and other notifications about my purchases.');
INSERT INTO `#__xmessage_component` (`id`, `component`, `action`, `title`) VALUES (19,'com_wishlist','wishlist_new_wish','Someone posted a wish on the Wish List I control.');
INSERT INTO `#__xmessage_component` (`id`, `component`, `action`, `title`) VALUES (20,'com_wishlist','wishlist_status_changed','A wish I submitted got accepted/rejected/granted.');
INSERT INTO `#__xmessage_component` (`id`, `component`, `action`, `title`) VALUES (21,'com_support','support_item_transferred','A support ticket/wish/question I submitted got transferred.');
INSERT INTO `#__xmessage_component` (`id`, `component`, `action`, `title`) VALUES (22,'com_wishlist','wishlist_comment_posted','Someone commented on a wish I posted or am assigned to');
INSERT INTO `#__xmessage_component` (`id`, `component`, `action`, `title`) VALUES (23,'com_groups','groups_invite','When you are invited to join a group.');
INSERT INTO `#__xmessage_component` (`id`, `component`, `action`, `title`) VALUES (24,'com_tools','contribtool_status_changed','Tool development status has changed');
INSERT INTO `#__xmessage_component` (`id`, `component`, `action`, `title`) VALUES (25,'com_tools','contribtool_new_message','New contribtool message is received');
INSERT INTO `#__xmessage_component` (`id`, `component`, `action`, `title`) VALUES (26,'com_tools','contribtool_info_changed','Information about a tool I develop has changed');
INSERT INTO `#__xmessage_component` (`id`, `component`, `action`, `title`) VALUES (27,'com_wishlist','wishlist_comment_thread','Someone replied to my comment or followed me in a discussion');
INSERT INTO `#__xmessage_component` (`id`, `component`, `action`, `title`) VALUES (28,'com_wishlist','wishlist_new_owner','You were added as an administrator of a Wish List');
INSERT INTO `#__xmessage_component` (`id`, `component`, `action`, `title`) VALUES (29,'com_wishlist','wishlist_wish_assigned','A wish has been assigned to me');
INSERT INTO `#__xmessage_component` (`id`, `component`, `action`, `title`) VALUES (30,'com_groups','group_message','Messages from fellow group members');
INSERT INTO `#__xmessage_component` (`id`, `component`, `action`, `title`) VALUES (31,'com_members','member_message','Messages from fellow site members');
INSERT INTO `#__xmessage_component` (`id`, `component`, `action`, `title`) VALUES (32,'com_projects','projects_member_added','You were added or invited to a project');
INSERT INTO `#__xmessage_component` (`id`, `component`, `action`, `title`) VALUES (33,'com_projects','projects_new_project_admin','Receive notifications about project(s) you monitor as an admin or reviewer');
INSERT INTO `#__xmessage_component` (`id`, `component`, `action`, `title`) VALUES (34,'com_projects','projects_admin_message','Receive administrative messages about your project(s)');

--
-- Dumping data for table `#__xmessage_notify`
--


--
-- Dumping data for table `#__xmessage_recipient`
--


--
-- Dumping data for table `#__xmessage_seen`
--


--
-- Dumping data for table `#__xorganization_types`
--


--
-- Dumping data for table `#__xorganizations`
--


--
-- Dumping data for table `#__xprofiles`
--


--
-- Dumping data for table `#__xprofiles_address`
--


--
-- Dumping data for table `#__xprofiles_admin`
--


--
-- Dumping data for table `#__xprofiles_bio`
--


--
-- Dumping data for table `#__xprofiles_dashboard_preferences`
--


--
-- Dumping data for table `#__xprofiles_disability`
--


--
-- Dumping data for table `#__xprofiles_edulevel`
--


--
-- Dumping data for table `#__xprofiles_hispanic`
--


--
-- Dumping data for table `#__xprofiles_host`
--


--
-- Dumping data for table `#__xprofiles_race`
--


--
-- Dumping data for table `#__xprofiles_role`
--


--
-- Dumping data for table `#__xsession`
--


--
-- Dumping data for table `#__ysearch_plugin_weights`
--

INSERT INTO `#__ysearch_plugin_weights` (`plugin`, `weight`) VALUES ('content',0.8);
INSERT INTO `#__ysearch_plugin_weights` (`plugin`, `weight`) VALUES ('events',0.8);
INSERT INTO `#__ysearch_plugin_weights` (`plugin`, `weight`) VALUES ('groups',0.8);
INSERT INTO `#__ysearch_plugin_weights` (`plugin`, `weight`) VALUES ('kb',0.8);
INSERT INTO `#__ysearch_plugin_weights` (`plugin`, `weight`) VALUES ('members',0.8);
INSERT INTO `#__ysearch_plugin_weights` (`plugin`, `weight`) VALUES ('resources',0.8);
INSERT INTO `#__ysearch_plugin_weights` (`plugin`, `weight`) VALUES ('wiki',1);
INSERT INTO `#__ysearch_plugin_weights` (`plugin`, `weight`) VALUES ('weighttitle',1);
INSERT INTO `#__ysearch_plugin_weights` (`plugin`, `weight`) VALUES ('sortrelevance',1);
INSERT INTO `#__ysearch_plugin_weights` (`plugin`, `weight`) VALUES ('sortnewer',0.2);
INSERT INTO `#__ysearch_plugin_weights` (`plugin`, `weight`) VALUES ('tagmod',1.3);
INSERT INTO `#__ysearch_plugin_weights` (`plugin`, `weight`) VALUES ('weightcontributor',0.2);

--
-- Dumping data for table `#__ysearch_site_map`
--


--
-- Dumping data for table `app`
--


--
-- Dumping data for table `display`
--


--
-- Dumping data for table `domainclass`
--


--
-- Dumping data for table `domainclasses`
--


--
-- Dumping data for table `fileperm`
--


--
-- Dumping data for table `host`
--


--
-- Dumping data for table `hosttype`
--


--
-- Dumping data for table `job`
--


--
-- Dumping data for table `joblog`
--


--
-- Dumping data for table `session`
--


--
-- Dumping data for table `sessionlog`
--


--
-- Dumping data for table `sessionpriv`
--


--
-- Dumping data for table `venue`
--


--
-- Dumping data for table `view`
--


--
-- Dumping data for table `viewlog`
--


--
-- Dumping data for table `viewperm`
--


--
-- Dumping data for table `zone_locations`
--


--
-- Dumping data for table `zones`
--

