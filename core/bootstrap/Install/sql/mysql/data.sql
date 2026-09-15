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
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1623,'plg_content_formathtml','plugin','formathtml','content',0,0,1,0,'','{\"applyFormat\":1,\"convertFormat\":0,\"sanitizeBefore\":1}','','',0,'0000-00-00 00:00:00',9,0);
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
INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (1703,'meridian','template','meridian','',0,1,1,0,'{}','{}','','',0,'0000-00-00 00:00:00',0,0);
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
INSERT INTO `#__menu` (`id`, `menutype`, `title`, `alias`, `note`, `path`, `link`, `type`, `published`, `parent_id`, `level`, `component_id`, `ordering`, `checked_out`, `checked_out_time`, `browserNav`, `access`, `img`, `template_style_id`, `params`, `lft`, `rgt`, `home`, `language`, `client_id`) VALUES (101,'mainmenu','Home','home','','home','index.php?option=com_content&view=featured','component',1,1,1,22,0,0,'0000-00-00 00:00:00',0,1,'',0,'{\"featured_categories\":[\"\"],\"num_leading_articles\":\"1\",\"num_intro_articles\":\"3\",\"num_columns\":\"3\",\"num_links\":\"0\",\"orderby_pri\":\"\",\"orderby_sec\":\"front\",\"order_date\":\"\",\"multi_column_order\":\"1\",\"show_pagination\":\"2\",\"show_pagination_results\":\"1\",\"show_noauth\":\"\",\"article-allow_ratings\":\"\",\"article-allow_comments\":\"\",\"show_feed_link\":\"1\",\"feed_summary\":\"\",\"show_title\":\"\",\"link_titles\":\"\",\"show_intro\":\"\",\"show_category\":\"\",\"link_category\":\"\",\"show_parent_category\":\"\",\"link_parent_category\":\"\",\"show_author\":\"\",\"show_create_date\":\"\",\"show_modify_date\":\"\",\"show_publish_date\":\"\",\"show_item_navigation\":\"\",\"show_readmore\":\"\",\"show_icons\":\"\",\"show_print_icon\":\"\",\"show_email_icon\":\"\",\"show_hits\":\"\",\"menu-anchor_title\":\"\",\"menu-anchor_css\":\"\",\"menu_image\":\"\",\"show_page_heading\":0,\"page_title\":\"\",\"page_heading\":\"\",\"pageclass_sfx\":\"\",\"menu-meta_description\":\"\",\"menu-meta_keywords\":\"\",\"robots\":\"\",\"secure\":0}',29,30,1,'*',0);

--
-- Dumping data for table `#__menu_types`
--

INSERT INTO `#__menu_types` (`id`, `menutype`, `title`, `description`, `type`) VALUES (1,'mainmenu','Main Menu','The main menu for the site','display');

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

-- The migrations this schema already embodies. A new hub runs only the ones
-- that came after, whether or not it is given sample data.
INSERT INTO `#__migrations` (`id`, `file`, `scope`, `hash`, `direction`, `date`, `action_by`, `status`)
VALUES
	(1, 'Migration20120101000000Core.php', 'migrations', '9d859ff24ee0f494d85ab78bdf40b1ff', 'up', '2014-09-03 19:13:35', 'root', 'success'),
	(2, 'Migration20120101000001Core.php', 'migrations', '4a9f8891218f27622d7065bec5aa7744', 'up', '2014-09-03 19:13:40', 'root', 'success'),
	(3, 'Migration20120101000002Core.php', 'migrations', 'c9ce6fbcb031136b3d632be5cdeaafd5', 'up', '2014-09-03 19:13:44', 'root', 'success'),
	(4, 'Migration20120101000003Core.php', 'migrations', '375d1bc02488c3ff9c0a458eb12922f8', 'up', '2014-09-03 19:13:47', 'root', 'success'),
	(5, 'Migration20120101000004Core.php', 'migrations', 'd0391e65c90255014dd3e57d6f3b53bd', 'up', '2014-09-03 19:13:53', 'root', 'success'),
	(6, 'Migration20120101000005Core.php', 'migrations', 'da0691900747a6bdbb2053e6aa326761', 'up', '2014-09-03 19:13:57', 'root', 'success'),
	(7, 'Migration20120101000006Core.php', 'migrations', 'a6ca583dab9cc196455b87b9b6b192ec', 'up', '2014-09-03 19:14:01', 'root', 'success'),
	(8, 'Migration20121009000000ComWiki.php', 'migrations', '4d38777de32a2742e9bc19a8c16f451f', 'up', '2014-09-03 19:14:12', 'root', 'success'),
	(9, 'Migration20121015000000ComTools.php', 'migrations', '105873a4abea1cfabce923a974954050', 'up', '2014-09-03 19:14:12', 'root', 'success'),
	(10, 'Migration20121016000000ComCourses.php', 'migrations', '68f8ffa8b5cfdff8f69dc082ade1bba1', 'up', '2014-09-03 19:14:12', 'root', 'success'),
	(11, 'Migration20121018000000ComWiki.php', 'migrations', '7a2334999fbd25108a01cdfd00e02962', 'up', '2014-09-03 19:14:12', 'root', 'success'),
	(12, 'Migration20121027000000ComCitations.php', 'migrations', 'fa28bbbb0848676966596e9dc33ca2d0', 'up', '2014-09-03 19:14:12', 'root', 'success'),
	(13, 'Migration20121130000000ComForum.php', 'migrations', 'a927d10f1b7013d144d50921dc191336', 'up', '2014-09-03 19:14:12', 'root', 'success'),
	(14, 'Migration20121217000000ComForum.php', 'migrations', 'becd8037cb2da9b3268e23d54c4b6328', 'up', '2014-09-03 19:14:12', 'root', 'success'),
	(15, 'Migration20130101000000ComCourses.php', 'migrations', '1e18fc06847c699ea2f169028ed9c439', 'up', '2014-09-03 19:14:12', 'root', 'success'),
	(16, 'Migration20130114000000Core.php', 'migrations', '085352375f2ff65e8c512788462cb35b', 'up', '2014-09-03 19:14:12', 'root', 'success'),
	(17, 'Migration20130201000000PlgUserConstantcontact.php', 'migrations', '8eaac8ff756f52e4cb8ae1b633b4e559', 'up', '2014-09-03 19:14:12', 'root', 'success'),
	(18, 'Migration20130208000000ComGroups.php', 'migrations', '00f7c4aea09f1ff9e86e271ed66309aa', 'up', '2014-09-03 19:14:12', 'root', 'success'),
	(19, 'Migration20130214000000Core.php', 'migrations', 'debe105763913a76dd82b81ff941c8de', 'up', '2014-09-03 19:14:12', 'root', 'success'),
	(20, 'Migration20130220000000ComResources.php', 'migrations', '72b11c90bdf8bacff9021a6509b9875f', 'up', '2014-09-03 19:14:12', 'root', 'success'),
	(21, 'Migration20130311000000PlgHubzeroComments.php', 'migrations', 'c538f5e482c73e4ffe9b14ca75efa531', 'up', '2014-09-03 19:14:12', 'root', 'success'),
	(22, 'Migration20130320000000ComCourses.php', 'migrations', 'b844452d09baf5d4b680432d795f4fca', 'up', '2014-09-03 19:14:12', 'root', 'success'),
	(23, 'Migration20130322000000ComCourses.php', 'migrations', '311e009451898df6de6ea90b232bf4fd', 'up', '2014-09-03 19:14:12', 'root', 'success'),
	(24, 'Migration20130329000000ComCourses.php', 'migrations', '430df927a67ebda2f485ad9340b1634c', 'up', '2014-09-03 19:14:12', 'root', 'success'),
	(25, 'Migration20130331000000ComCourses.php', 'migrations', '13d010e8c42f2855cf05eee3854285d5', 'up', '2014-09-03 19:14:12', 'root', 'success'),
	(26, 'Migration20130401000000ComCourses.php', 'migrations', '3c8891353dcbf5ef7a3d33a6ae09017e', 'up', '2014-09-03 19:14:12', 'root', 'success'),
	(27, 'Migration20130401000000ComGroups.php', 'migrations', '3dc7cfb1a413fe3bc3fcfddd27ac3ab6', 'up', '2014-09-03 19:14:12', 'root', 'success'),
	(28, 'Migration20130403000000ComCourses.php', 'migrations', '3977afdb4209d767be0653ec3a6391d4', 'up', '2014-09-03 19:14:12', 'root', 'success'),
	(29, 'Migration20130403000000Core.php', 'migrations', '3d6af0961683f4e77e25f6916ae8c87c', 'up', '2014-09-03 19:14:12', 'root', 'success'),
	(30, 'Migration20130404000000ComCitations.php', 'migrations', '96133f48b193a1d76f7a7be31d0dbf5f', 'up', '2014-09-03 19:14:12', 'root', 'success'),
	(31, 'Migration20130404000000ComCourses.php', 'migrations', 'b0c569d47023a47001bc5c6ebc45d543', 'up', '2014-09-03 19:14:12', 'root', 'success'),
	(32, 'Migration20130410000000ComCourses.php', 'migrations', 'e4ffc5c7220a48bae192446457ae3a72', 'up', '2014-09-03 19:14:12', 'root', 'success'),
	(33, 'Migration20130410000000Core.php', 'migrations', '88ceaf7e64db4e4de17707715371140e', 'up', '2014-09-03 19:14:12', 'root', 'success'),
	(34, 'Migration20130411000000ComCourses.php', 'migrations', 'f83e4903c93d12c69664d880e5673953', 'up', '2014-09-03 19:14:12', 'root', 'success'),
	(35, 'Migration20130412000000ComCourses.php', 'migrations', '196274412289c68de1e160dd9b49e84b', 'up', '2014-09-03 19:14:12', 'root', 'success'),
	(36, 'Migration20130413000000ComCourses.php', 'migrations', 'c51e0833db0c5286789b85c793ed4d0a', 'up', '2014-09-03 19:14:12', 'root', 'success'),
	(37, 'Migration20130415000000ComSupport.php', 'migrations', '3de99f5f127c19841f04b8d6a1ffd8fd', 'up', '2014-09-03 19:14:12', 'root', 'success'),
	(38, 'Migration20130416193151PlgCoursesOutline.php', 'migrations', 'ec5a92145d120ddc3c5750bae38ea5d3', 'up', '2014-09-03 19:14:12', 'root', 'success'),
	(39, 'Migration20130423001442ComCourses.php', 'migrations', '9bbcbf50e45f16c1f9f4b1c5443d75db', 'up', '2014-09-03 19:14:12', 'root', 'success'),
	(40, 'Migration20130423115530ComTags.php', 'migrations', '1a7b5976b56e44a9f06273c59e93b36b', 'up', '2014-09-03 19:14:12', 'root', 'success'),
	(41, 'Migration20130423115913ComWiki.php', 'migrations', '3e5ac2de3a5dc73f15ed5c980fe7bb58', 'up', '2014-09-03 19:14:12', 'root', 'success'),
	(42, 'Migration20130423204702PlgCoursesPages.php', 'migrations', '04c82ce82a6707e4a5205ccfb5187799', 'up', '2014-09-03 19:14:12', 'root', 'success'),
	(43, 'Migration20130423204715ComCourses.php', 'migrations', '034244a90c4ce86424906f5b69772042', 'up', '2014-09-03 19:14:12', 'root', 'success'),
	(44, 'Migration20130423213901ComCourses.php', 'migrations', '2226978242ebb07d139c76e2feb5c289', 'up', '2014-09-03 19:14:12', 'root', 'success'),
	(45, 'Migration20130426062338ComCourses.php', 'migrations', '928bcc6458c490c44546eb6b5a94d9a8', 'up', '2014-09-03 19:14:12', 'root', 'success'),
	(46, 'Migration20130426070126ComTags.php', 'migrations', '10b87268e6cc90e98745b36983114063', 'up', '2014-09-03 19:14:12', 'root', 'success'),
	(47, 'Migration20130426071400Core.php', 'migrations', '5a0f9b902c9cc4c43b4850b4f469d298', 'up', '2014-09-03 19:14:12', 'root', 'success'),
	(48, 'Migration20130426071658ComHelp.php', 'migrations', 'd0748f74b9158958ca367ac829cfd497', 'up', '2014-09-03 19:14:13', 'root', 'success'),
	(49, 'Migration20130426072033ComCron.php', 'migrations', 'b0278bb91b8f904aa1fa4b680e71b6a7', 'up', '2014-09-03 19:14:13', 'root', 'success'),
	(50, 'Migration20130426074801ComSupport.php', 'migrations', 'c608ffbf6d386b85e7175fbfbc62e511', 'up', '2014-09-03 19:14:13', 'root', 'success'),
	(51, 'Migration20130429103200PlgGroupsCalendar.php', 'migrations', 'c209ffdaf1cd1892946e9ff38753a574', 'up', '2014-09-03 19:14:13', 'root', 'success'),
	(52, 'Migration20130430112401ComCourses.php', 'migrations', '3f1a4136748fe7da64e2e76ad45e1182', 'up', '2014-09-03 19:14:13', 'root', 'success'),
	(53, 'Migration20130501120700PlgCronGroups.php', 'migrations', 'f854a44560ef02a1407a70d7c6747e6c', 'up', '2014-09-03 19:14:13', 'root', 'success'),
	(54, 'Migration20130506233657ComCourses.php', 'migrations', '81c68decb346f25c4cfa80eadda003b7', 'up', '2014-09-03 19:14:13', 'root', 'success'),
	(55, 'Migration20130507030333ComCourses.php', 'migrations', 'ed2f713b568e61b156fbfb18f86b704f', 'up', '2014-09-03 19:14:13', 'root', 'success'),
	(56, 'Migration20130507085501ComForum.php', 'migrations', '392b473abb12b4cc83eb59d45d5aeb73', 'up', '2014-09-03 19:14:13', 'root', 'success'),
	(57, 'Migration20130507170000PlgMembersProfile.php', 'migrations', 'f9b670bd56e5eb430192576a556005f0', 'up', '2014-09-03 19:14:13', 'root', 'success'),
	(58, 'Migration20130512175201ComForum.php', 'migrations', '98b081a12ca344ec4756d33e5c1604e5', 'up', '2014-09-03 19:14:13', 'root', 'success'),
	(59, 'Migration20130512175301PlgCoursesDiscussions.php', 'migrations', 'de5ec225357cbf7b82a2575a885c3a23', 'up', '2014-09-03 19:14:13', 'root', 'success'),
	(60, 'Migration20130516175532ComFeedback.php', 'migrations', '6587433b05cfb7dd75baa0c68ac0b688', 'up', '2014-09-03 19:14:13', 'root', 'success'),
	(61, 'Migration20130517101308ComContent.php', 'migrations', '7dfb2ccdd73de0254fa1c9f6640ff114', 'up', '2014-09-03 19:14:13', 'root', 'success'),
	(62, 'Migration20130521160001ComForum.php', 'migrations', '59bdfb0f7773760ea4ffcd065d823a05', 'up', '2014-09-03 19:14:13', 'root', 'success'),
	(63, 'Migration20130524101101PlgCoursesDiscussions.php', 'migrations', 'c7c62aa350a8a598baf3de31a05b075f', 'up', '2014-09-03 19:14:13', 'root', 'success'),
	(64, 'Migration20130529204838PlgAuthenticationTwitter.php', 'migrations', '21999cd694c4a2c6f221d36f15dc8b39', 'up', '2014-09-03 19:14:13', 'root', 'success'),
	(65, 'Migration20130530153638ComCron.php', 'migrations', 'e33fab341344db643e9ba56f93462750', 'up', '2014-09-03 19:14:13', 'root', 'success'),
	(66, 'Migration20130531081238PlgCoursesNotes.php', 'migrations', '95f6a849fef71b7951b1eec3c04a0094', 'up', '2014-09-03 19:14:13', 'root', 'success'),
	(67, 'Migration20130610123201PlgCoursesDiscussions.php', 'migrations', '623789040dccfd065003c705e199af02', 'up', '2014-09-03 19:14:13', 'root', 'success'),
	(68, 'Migration20130617121701ComCourses.php', 'migrations', 'c87da2902fcd4c42c817531b99fff395', 'up', '2014-09-03 19:14:13', 'root', 'success'),
	(69, 'Migration20130617171001ComForum.php', 'migrations', 'fae3f4c2e79c5dd61aa13d47ba88747a', 'up', '2014-09-03 19:14:13', 'root', 'success'),
	(70, 'Migration20130618144751PlgMembersProfile.php', 'migrations', '39f85d30d6c25ffee6fd8bed8039ae17', 'up', '2014-09-03 19:14:13', 'root', 'success'),
	(71, 'Migration20130619181459PlgGroupsAnnouncements.php', 'migrations', 'fba56331387376d9ae5f28166b77b2c8', 'up', '2014-09-03 19:14:13', 'root', 'success'),
	(72, 'Migration20130621115001ComSupport.php', 'migrations', '4d086dcd0b1b0696434cd14080054b3d', 'up', '2014-09-03 19:14:13', 'root', 'success'),
	(73, 'Migration20130621155138PlgCoursesNotes.php', 'migrations', '40694fd727e927f4f39312f901176275', 'up', '2014-09-03 19:14:13', 'root', 'success'),
	(74, 'Migration20130624184902PlgGroupsCalendar.php', 'migrations', '9b45454f204767fc0c6f3a079a8c9f88', 'up', '2014-09-03 19:14:13', 'root', 'success'),
	(75, 'Migration20130702181838ComWiki.php', 'migrations', '190377c4e8def55044573f24c5007847', 'up', '2014-09-03 19:14:13', 'root', 'success'),
	(76, 'Migration20130703075132ComCourses.php', 'migrations', 'cef0950db16b6dd8bf637341054aa609', 'up', '2014-09-03 19:14:13', 'root', 'success'),
	(77, 'Migration20130708082138ComWiki.php', 'migrations', 'e6f637f86a84a63c8ac57e7818dfa474', 'up', '2014-09-03 19:14:13', 'root', 'success'),
	(78, 'Migration20130715111246ModIncrementalRegistration.php', 'migrations', '47cbaefce5c432e11280cf2ba3b0a477', 'up', '2014-09-03 19:14:13', 'root', 'success'),
	(79, 'Migration20130716202127ComNewsletter.php', 'migrations', '7f0427a8f44024763e02981d081a2ae9', 'up', '2014-09-03 19:14:13', 'root', 'success'),
	(80, 'Migration20130717140704PlgCronNewsletter.php', 'migrations', '4227bf712d4d339e8d0cd53c2cf14106', 'up', '2014-09-03 19:14:13', 'root', 'success'),
	(81, 'Migration20130723163332ComCourses.php', 'migrations', '0d451a44f9da2ff6fcabbe2b88e3ace8', 'up', '2014-09-03 19:14:13', 'root', 'success'),
	(82, 'Migration20130723171332ComCourses.php', 'migrations', 'ee1c86ef882d6ef30de8953103b1bd80', 'up', '2014-09-03 19:14:13', 'root', 'success'),
	(83, 'Migration20130725184342ComCourses.php', 'migrations', 'ff2e8775db31c3e8ca356cada9cb2e89', 'up', '2014-09-03 19:14:13', 'root', 'success'),
	(84, 'Migration20130729084642ComForum.php', 'migrations', '61bfc81cfff30e5e29661a7c295b4007', 'up', '2014-09-03 19:14:13', 'root', 'success'),
	(85, 'Migration20130729130302PlgYsearchCourses.php', 'migrations', '1ca275a7edfb14bdff6826fbd445c945', 'up', '2014-09-03 19:14:13', 'root', 'success'),
	(86, 'Migration20130731170234ComCourses.php', 'migrations', '07f14cd52ed816a4d5de7396f2dcf383', 'up', '2014-09-03 19:14:13', 'root', 'success'),
	(87, 'Migration20130731203037ComResources.php', 'migrations', 'a7f10ca0a71c48e636d145e07e7b3d81', 'up', '2014-09-03 19:14:13', 'root', 'success'),
	(88, 'Migration20130801182039ComPdf2form.php', 'migrations', '0f20a38b11b5457f3a413030b1809628', 'up', '2014-09-03 19:14:13', 'root', 'success'),
	(89, 'Migration20130809151809ComTags.php', 'migrations', 'e5d0cec98d604a84ceca1e25f95a70e9', 'up', '2014-09-03 19:14:13', 'root', 'success'),
	(90, 'Migration20130809152737ComCourses.php', 'migrations', '10694cc0fd3b15b30254d4f201ab9ade', 'up', '2014-09-03 19:14:13', 'root', 'success'),
	(91, 'Migration20130812132139ComCollections.php', 'migrations', '75a8ed41da8ba67499599deee4801a51', 'up', '2014-09-03 19:14:13', 'root', 'success'),
	(92, 'Migration20130812182339ComCart.php', 'migrations', '3041c23955421f5e98b4841d3e06d9f5', 'up', '2014-09-03 19:14:13', 'root', 'success'),
	(93, 'Migration20130812201731ComStorefront.php', 'migrations', 'bad1e44540cd3757634918929863ee14', 'up', '2014-09-03 19:14:13', 'root', 'success'),
	(94, 'Migration20130813195602ComOaipmh.php', 'migrations', '2e62f1439099d09d368536521a33d6da', 'up', '2014-09-03 19:14:13', 'root', 'success'),
	(95, 'Migration20130813210535PlgProjectsDatabases.php', 'migrations', 'abefb5b05e69e76d343d43883ff67be3', 'up', '2014-09-03 19:14:14', 'root', 'success'),
	(96, 'Migration20130814185755ComNewsletter.php', 'migrations', '36aaf46d71945972e9b8d1a2055abc68', 'up', '2014-09-03 19:14:14', 'root', 'success'),
	(97, 'Migration20130814201250ComCourses.php', 'migrations', '7f3a0d9b02dcd74c800d1c5246b14bf5', 'up', '2014-09-03 19:14:14', 'root', 'success'),
	(98, 'Migration20130815175354ComDataviewer.php', 'migrations', '2b2951fee852448fa9c4e92710efddfe', 'up', '2014-09-03 19:14:14', 'root', 'success'),
	(99, 'Migration20130816143341ComCitations.php', 'migrations', 'c11206a40ab8b0c7b8fdfe0610bbf12b', 'up', '2014-09-03 19:14:14', 'root', 'success'),
	(100, 'Migration20130819145850ComCourses.php', 'migrations', '7de4c2e68aaa2adbbc2613fa9156779e', 'up', '2014-09-03 19:14:14', 'root', 'success'),
	(101, 'Migration20130820135328ComNewsletter.php', 'migrations', 'ac55cd216a381a8a0f546d2009941475', 'up', '2014-09-03 19:14:14', 'root', 'success'),
	(102, 'Migration20130821164314ComStorefront.php', 'migrations', '0b87606e4b6733187cc07a0790b776f3', 'up', '2014-09-03 19:14:14', 'root', 'success'),
	(103, 'Migration20130821164628ComCart.php', 'migrations', 'fbefebeab9a70c799cbbc41968086a93', 'up', '2014-09-03 19:14:14', 'root', 'success'),
	(104, 'Migration20130826150814ComResources.php', 'migrations', '227a0e97a65419b657308309b1402a2c', 'up', '2014-09-03 19:14:14', 'root', 'success'),
	(105, 'Migration20130827143717Core.php', 'migrations', '7e25e6848c5ee7d60b23bafc2d73a790', 'up', '2014-09-03 19:14:14', 'root', 'success'),
	(106, 'Migration20130828201526ComPublications.php', 'migrations', 'c8036f7262dda05b07add524f2f4da47', 'up', '2014-09-03 19:14:14', 'root', 'success'),
	(107, 'Migration20130828203404Core.php', 'migrations', '4e42d7b290d963dbd40d6d0cbdaae5c4', 'up', '2014-09-03 19:14:14', 'root', 'success'),
	(108, 'Migration20130829203107ComProjects.php', 'migrations', '78dc7180fbad54c4842db1ab8dc7d663', 'up', '2014-09-03 19:14:14', 'root', 'success'),
	(109, 'Migration20130829211856ComPublications.php', 'migrations', 'befedf94d321d88e2673d88378533d2e', 'up', '2014-09-03 19:14:14', 'root', 'success'),
	(110, 'Migration20130830175756ComCourses.php', 'migrations', 'aa7ba7de0263dd6dc53ba9c428fb608d', 'up', '2014-09-03 19:14:14', 'root', 'success'),
	(111, 'Migration20130905195600ComCourses.php', 'migrations', '3a77a9ddf5a77f910339dcc9c8cd6f43', 'up', '2014-09-03 19:14:14', 'root', 'success'),
	(112, 'Migration20130911070500ComCourses.php', 'migrations', 'fa9471f7390ba8361ebdb5597ef72009', 'up', '2014-09-03 19:14:14', 'root', 'success'),
	(113, 'Migration20130916080500ComWiki.php', 'migrations', '01ef84e10af396e560e045a11f01f213', 'up', '2014-09-03 19:14:14', 'root', 'success'),
	(114, 'Migration20130918132946ComForum.php', 'migrations', '94fbb1e67fde497c956082a88ca1fb99', 'up', '2014-09-03 19:14:14', 'root', 'success'),
	(115, 'Migration20130924000000ComCategories.php', 'migrations', '9d826b54252d765f522935c1bb70d982', 'up', '2014-09-03 19:14:14', 'root', 'success'),
	(116, 'Migration20130924000001ComContent.php', 'migrations', 'acf0a04f1c5fc7b1feabb3104a4de62a', 'up', '2014-09-03 19:14:14', 'root', 'success'),
	(117, 'Migration20130924000002Core.php', 'migrations', '3f4527ffbdfeeef20c5da83ab70f9833', 'up', '2014-09-03 19:14:14', 'root', 'success'),
	(118, 'Migration20130924000003ComBanners.php', 'migrations', '06f8217b7f3cf88e5915b4425de0e7ca', 'up', '2014-09-03 19:14:14', 'root', 'success'),
	(119, 'Migration20130924000004ComContacts.php', 'migrations', 'e5b55b821abfaa52cad30d51bb7c33b5', 'up', '2014-09-03 19:14:15', 'root', 'success'),
	(120, 'Migration20130924000005Core.php', 'migrations', '649f28e7601c6613a342c816a547f4fe', 'up', '2014-09-03 19:14:15', 'root', 'success'),
	(121, 'Migration20130924000006ComFinder.php', 'migrations', '79e8c90e208dfaf60a43111b1630f72d', 'up', '2014-09-03 19:14:15', 'root', 'success'),
	(122, 'Migration20130924000007Core.php', 'migrations', '6a2c5938fcf46aad0c724f2b47e01b90', 'up', '2014-09-03 19:14:15', 'root', 'success'),
	(123, 'Migration20130924000008Core.php', 'migrations', 'c2cfb7c58f738f9771ca8c5cf0d0fdd5', 'up', '2014-09-03 19:14:15', 'root', 'success'),
	(124, 'Migration20130924000009ComMenus.php', 'migrations', '7d36ed6b99bd79101e806abfa9bbcdc0', 'up', '2014-09-03 19:14:15', 'root', 'success'),
	(125, 'Migration20130924000010ComMessages.php', 'migrations', '261bb825a6af71667d39f875050fb125', 'up', '2014-09-03 19:14:15', 'root', 'success'),
	(126, 'Migration20130924000011ComModules.php', 'migrations', '51725306e1edbf18471e7d840d8c78a0', 'up', '2014-09-03 19:14:15', 'root', 'success'),
	(127, 'Migration20130924000012ComNewsfeeds.php', 'migrations', '2c25893333e66f159340119260a11d28', 'up', '2014-09-03 19:14:15', 'root', 'success'),
	(128, 'Migration20130924000013Core.php', 'migrations', '7281393178fa6fe1b441f5887010e116', 'up', '2014-09-03 19:14:15', 'root', 'success'),
	(129, 'Migration20130924000014ComWeblinks.php', 'migrations', '04d08ca3153b73779b8b307d19689103', 'up', '2014-09-03 19:14:15', 'root', 'success'),
	(130, 'Migration20130926143200Core.php', 'migrations', '04771524e2f8b2806f462cf21a57b21f', 'up', '2014-09-03 19:14:15', 'root', 'success'),
	(131, 'Migration20130930160944ComBillboards.php', 'migrations', '3abeaa15fe60b65b244f12cef206b52c', 'up', '2014-09-03 19:14:15', 'root', 'success'),
	(132, 'Migration20131002133124ComCollections.php', 'migrations', '9493f286bac5a1bbd8d73c176e74c216', 'up', '2014-09-03 19:14:15', 'root', 'success'),
	(133, 'Migration20131011164749ComCourses.php', 'migrations', '2d57d06ca62c892eb9b509e5c861127d', 'up', '2014-09-03 19:14:15', 'root', 'success'),
	(134, 'Migration20131011210918ComTools.php', 'migrations', '3f8d18135a7db6d6bc9a7b1e3b923e67', 'up', '2014-09-03 19:14:15', 'root', 'success'),
	(135, 'Migration20131014103753ComMembers.php', 'migrations', '3348945c25c8c0c1e6425bb8bfb79f2b', 'up', '2014-09-03 19:14:15', 'root', 'success'),
	(136, 'Migration20131016184016PlgGroupsAnnouncements.php', 'migrations', '7276c1fc325e6c9bc13b77e31ececa63', 'up', '2014-09-03 19:14:15', 'root', 'success'),
	(137, 'Migration20131017133750ComWiki.php', 'migrations', '1203e8a9fc7a3414a39e904c1f6afbc1', 'up', '2014-09-03 19:14:15', 'root', 'success'),
	(138, 'Migration20131018163729ComCollections.php', 'migrations', '2da3363e392407adbdc46beffe7885e2', 'up', '2014-09-03 19:14:15', 'root', 'success'),
	(139, 'Migration20131021090512ComCourses.php', 'migrations', '60dd497cb78775c78d74a3d0955b947c', 'up', '2014-09-03 19:14:15', 'root', 'success'),
	(140, 'Migration20131021211223Core.php', 'migrations', '3ddb3eb4af78c52e9b5d404126cc64b1', 'up', '2014-09-03 19:14:15', 'root', 'success'),
	(141, 'Migration20131021225942ComCitations.php', 'migrations', 'd39606737c08df397b855dc3fac4d817', 'up', '2014-09-03 19:14:15', 'root', 'success'),
	(142, 'Migration20131022144858ComMembers.php', 'migrations', 'b89dc94db0a52e47639b27d21a244d42', 'up', '2014-09-03 19:14:15', 'root', 'success'),
	(143, 'Migration20131024114858ComCourses.php', 'migrations', '5d9fd0d02557a9aca26797ee626f1675', 'up', '2014-09-03 19:14:15', 'root', 'success'),
	(144, 'Migration20131024182332Core.php', 'migrations', '3f97532ddd05ce539b67bb45a0aa874b', 'up', '2014-09-03 19:14:15', 'root', 'success'),
	(145, 'Migration20131031124923ComCitations.php', 'migrations', '9f62ca16b397428a65eb7bd5e77a034a', 'up', '2014-09-03 19:14:15', 'root', 'success'),
	(146, 'Migration20131106150723ComProjects.php', 'migrations', '9745c5569e93d3a479ab6a1aa5fb9a3a', 'up', '2014-09-03 19:14:15', 'root', 'success'),
	(147, 'Migration20131106150723PlgYsearchCollections.php', 'migrations', '689fa41eb7e9687e52b656632b99b3d1', 'up', '2014-09-03 19:14:15', 'root', 'success'),
	(148, 'Migration20131106150723PlgYsearchProjects.php', 'migrations', '26a90de4fca38b6909df79ee2ed85a32', 'up', '2014-09-03 19:14:16', 'root', 'success'),
	(149, 'Migration20131106150723PlgYsearchPublications.php', 'migrations', '1f042842e7460aba9e820a4d2a19d4d4', 'up', '2014-09-03 19:14:16', 'root', 'success'),
	(150, 'Migration20131106152023ComCollections.php', 'migrations', 'ae8ebe5cb2acca2a35399618ddcbfc32', 'up', '2014-09-03 19:14:16', 'root', 'success'),
	(151, 'Migration20131106153123PlgYsearchCollections.php', 'migrations', 'be94f6f1a6075b75db00828187d8a0ab', 'up', '2014-09-03 19:14:16', 'root', 'success'),
	(152, 'Migration20131106154023ComPublications.php', 'migrations', '9c434a945305ec42b9631d8d7e0ef13b', 'up', '2014-09-03 19:14:16', 'root', 'success'),
	(153, 'Migration20131108091700ComBlog.php', 'migrations', '43f3ad06466cda7efeaa18b542c73a0c', 'up', '2014-09-03 19:14:16', 'root', 'success'),
	(154, 'Migration20131108095500ComAnswers.php', 'migrations', '4ca152ef5eb47fb0837edc14f2e87f25', 'up', '2014-09-03 19:14:16', 'root', 'success'),
	(155, 'Migration20131111165410Core.php', 'migrations', '591f0d517a0529dee0b46e722a6f7115', 'up', '2014-09-03 19:14:16', 'root', 'success'),
	(156, 'Migration20131111200831ComCourses.php', 'migrations', '6fcad5e995b581746f3f017ab894731f', 'up', '2014-09-03 19:14:16', 'root', 'success'),
	(157, 'Migration20131112130740ComUsage.php', 'migrations', 'b6139ffc3c111b8184a25ae10a7629d6', 'up', '2014-09-03 19:14:16', 'root', 'success'),
	(158, 'Migration20131112134513PlgCoursesStore.php', 'migrations', '936b280d6b1af9c61b554563c88cd6ba', 'up', '2014-09-03 19:14:16', 'root', 'success'),
	(159, 'Migration20131113134600ComTags.php', 'migrations', '88b8e752ca21b1bfd5686bab69591f99', 'up', '2014-09-03 19:14:16', 'root', 'success'),
	(160, 'Migration20131113143500ComSupport.php', 'migrations', 'b1c3ba67a3fd651d3a64c1074148cbe1', 'up', '2014-09-03 19:14:16', 'root', 'success'),
	(161, 'Migration20131113193815ComResources.php', 'migrations', '323ff48a9a4efbaf43a0cf8415843045', 'up', '2014-09-03 19:14:16', 'root', 'success'),
	(162, 'Migration20131206231821ComCourses.php', 'migrations', '7e95d9378e7ab5fb89c7d738a6089845', 'up', '2014-09-03 19:14:16', 'root', 'success'),
	(163, 'Migration20131209221353ComUsers.php', 'migrations', 'fc5a8ce93df3b0d4f5224502f6ddfe82', 'up', '2014-09-03 19:14:16', 'root', 'success'),
	(164, 'Migration20131216011106PlgCronCourses.php', 'migrations', 'eb3504126f6963e8bb3958bfb2ae6770', 'up', '2014-09-03 19:14:16', 'root', 'success'),
	(165, 'Migration20140108233318ComGroups.php', 'migrations', 'cdf56252657c7bd25fa10739c102856b', 'up', '2014-09-03 19:14:16', 'root', 'success'),
	(166, 'Migration20140108233319ComGroups.php', 'migrations', '578bbbec8b2bcd11f39bddf3bad742e5', 'up', '2014-09-03 19:14:16', 'root', 'success'),
	(167, 'Migration20140108233320PlgEditorsCkeditor.php', 'migrations', 'abb903ceb45edab5976ff9f1d10a6b44', 'up', '2014-09-03 19:14:16', 'root', 'success'),
	(168, 'Migration20140108233321PlgGroupsMembers.php', 'migrations', 'eec9f4dc67f3e284a20fb932f3f656a9', 'up', '2014-09-03 19:14:16', 'root', 'success'),
	(169, 'Migration20140108233322PlgSystemSupergroup.php', 'migrations', '8fecdaff1338a5e8cc01e5c60d4317f9', 'up', '2014-09-03 19:14:16', 'root', 'success'),
	(170, 'Migration20140108233323ModGroupPages.php', 'migrations', 'ab3761d5183d840358ef60b9fd0e4c1b', 'up', '2014-09-03 19:14:16', 'root', 'success'),
	(171, 'Migration20140109024336ComGroups.php', 'migrations', '76aa9158d0d23651a3e0d0e40ef78f75', 'up', '2014-09-03 19:14:16', 'root', 'success'),
	(172, 'Migration20140109101723ComProjects.php', 'migrations', '09238a379c605f3a3425ee8302aa8adf', 'up', '2014-09-03 19:14:16', 'root', 'success'),
	(173, 'Migration20140110125436ComWrapper.php', 'migrations', 'ff92a6b49c9dd2e70b36ff66456f9fa9', 'up', '2014-09-03 19:14:16', 'root', 'success'),
	(174, 'Migration20140110130812ComBanners.php', 'migrations', '60a47eb91532a0233598e0c7e734ac0c', 'up', '2014-09-03 19:14:17', 'root', 'success'),
	(175, 'Migration20140110132217ComWeblinks.php', 'migrations', '6cf15d40bb0bc226b5894324be84d578', 'up', '2014-09-03 19:14:17', 'root', 'success'),
	(176, 'Migration20140110132511ComContact.php', 'migrations', 'e27fd8406523f1de1e439b83b9a2223a', 'up', '2014-09-03 19:14:17', 'root', 'success'),
	(177, 'Migration20140110135511ComFinder.php', 'migrations', '127cd73b24131a937794343d49c8bfb4', 'up', '2014-09-03 19:14:17', 'root', 'success'),
	(178, 'Migration20140113135231ComSearch.php', 'migrations', 'acddc88f38881626eeca91fb3fd228f3', 'up', '2014-09-03 19:14:17', 'root', 'success'),
	(179, 'Migration20140114091331PlgGeocode.php', 'migrations', '54630e3e61b64fd809c643bae6c14465', 'up', '2014-09-03 19:14:17', 'root', 'success'),
	(180, 'Migration20140115171149PlgResourcesFindthistext.php', 'migrations', 'b4fb45a492e9bf5d8c6f962880f79c0c', 'up', '2014-09-03 19:14:17', 'root', 'success'),
	(181, 'Migration20140117212240ComCourses.php', 'migrations', '4992169ed34a40e5be0848612d25828f', 'up', '2014-09-03 19:14:18', 'root', 'success'),
	(182, 'Migration20140121131313PlgCronProjects.php', 'migrations', 'f5b6a7391dbc07d958c33dcb587ebbfe', 'up', '2014-09-03 19:14:18', 'root', 'success'),
	(183, 'Migration20140121142313PlgCronPublications.php', 'migrations', '5355a8d3e21c734047ba5b540dc3ee0c', 'up', '2014-09-03 19:14:18', 'root', 'success'),
	(184, 'Migration20140130105700ComAnswers.php', 'migrations', '0a1f0101e6cd8c5efd81d9a5d19bf739', 'up', '2014-09-03 19:14:18', 'root', 'success'),
	(185, 'Migration20140131091600HubzeroComments.php', 'migrations', '1f48e02b6efc5de9e86fe887cc8346e9', 'up', '2014-09-03 19:14:18', 'root', 'success'),
	(186, 'Migration20140206131800ComCitations.php', 'migrations', '94067690ab427a2c68f51db7c0f787cf', 'up', '2014-09-03 19:14:18', 'root', 'success'),
	(187, 'Migration20140206141200ComCitations.php', 'migrations', 'ce584bed8964c698a474b5ff1aa543f3', 'up', '2014-09-03 19:14:18', 'root', 'success'),
	(188, 'Migration20140207091831PlgContent.php', 'migrations', 'd32e32abee14b548a73be929eb1ea36f', 'up', '2014-09-03 19:14:18', 'root', 'success'),
	(189, 'Migration20140211083020PlgEditorsWikitoolbar.php', 'migrations', '2b6902258b464a2e91d8b063e81ac7b6', 'up', '2014-09-03 19:14:18', 'root', 'success'),
	(190, 'Migration20140211083120PlgEditorsWikiwyg.php', 'migrations', 'cb14c8925e18ee04e2d1e4504d4de131', 'up', '2014-09-03 19:14:18', 'root', 'success'),
	(191, 'Migration20140211154400PlgProjectsLinks.php', 'migrations', '9144fee0d37b25722b2b1e9841864523', 'up', '2014-09-03 19:14:18', 'root', 'success'),
	(192, 'Migration20140212162812ComCourses.php', 'migrations', '2b5f32e847322f54c2ed07f87ce3a44f', 'up', '2014-09-03 19:14:18', 'root', 'success'),
	(193, 'Migration20140217151012ComCourses.php', 'migrations', '4ffce0e9b7e892007cd151dd066e36ee', 'up', '2014-09-03 19:14:18', 'root', 'success'),
	(194, 'Migration20140219221812ComCourses.php', 'migrations', 'deea404cc920735f53459b32a20319ab', 'up', '2014-09-03 19:14:18', 'root', 'success'),
	(195, 'Migration20140220183257ComCourses.php', 'migrations', '98f3466f0101fa8d8d6fb4b5ff6d8c8d', 'up', '2014-09-03 19:14:18', 'root', 'success'),
	(196, 'Migration20140224112212PlgWiki.php', 'migrations', 'e28b09f1490d2293bcac04496bd248c8', 'up', '2014-09-03 19:14:18', 'root', 'success'),
	(197, 'Migration20140225094500ComGroups.php', 'migrations', '4d002f8f0230f753cb0a6c7769d26948', 'up', '2014-09-03 19:14:18', 'root', 'success'),
	(198, 'Migration20140303123720PlgGroupsCourses.php', 'migrations', '99258b842adf33a5dfccd4ed8bcac9f4', 'up', '2014-09-03 19:14:18', 'root', 'success'),
	(199, 'Migration20140305081020PlgUsageChart.php', 'migrations', 'ff49908bb41d0a83d9e890a02df80387', 'up', '2014-09-03 19:14:18', 'root', 'success'),
	(200, 'Migration20140305081320ComMembers.php', 'migrations', 'cd29eed09984a7e937e0bdf4612b9f2b', 'up', '2014-09-03 19:14:18', 'root', 'success'),
	(201, 'Migration20140305090721ComCron.php', 'migrations', '00ec508fd29dd51dfbc6a404b8011a89', 'up', '2014-09-03 19:14:18', 'root', 'success'),
	(202, 'Migration20140305142845Core.php', 'migrations', 'f99ca841596a4146cf5ef7eba4b291cd', 'up', '2014-09-03 19:14:19', 'root', 'success'),
	(203, 'Migration20140310130202ComTags.php', 'migrations', '74ef163a7db2e4110db114013c938aa1', 'up', '2014-09-03 19:14:19', 'root', 'success'),
	(204, 'Migration20140311150000ComFeedaggregator.php', 'migrations', 'f7f9b00e8d62a17d1a109f513ec6d088', 'up', '2014-09-03 19:14:19', 'root', 'success'),
	(205, 'Migration20140311160400ComFeedaggregator.php', 'migrations', '1bc30baaba9e1b941c38fecd785187e9', 'up', '2014-09-03 19:14:19', 'root', 'success'),
	(206, 'Migration20140314080012PlgSupportPublications.php', 'migrations', '8e31e1184dd896cc7c4b54a5e90ea9a3', 'up', '2014-09-03 19:14:19', 'root', 'success'),
	(207, 'Migration20140314131113ComKb.php', 'migrations', 'ee0066a58dc5431df923020985c881b4', 'up', '2014-09-03 19:14:19', 'root', 'success'),
	(208, 'Migration20140324161600ComResources.php', 'migrations', '717eb7d8a95bb4782108ff02cf44a3d0', 'up', '2014-09-03 19:14:19', 'root', 'success'),
	(209, 'Migration20140325093000ComResources.php', 'migrations', 'cd2d13002c43366713b904e8fcd770f3', 'up', '2014-09-03 19:14:19', 'root', 'success'),
	(210, 'Migration20140407091900ComResources.php', 'migrations', '13215b8be3e67cbcbb35f24ab98fae1b', 'up', '2014-09-03 19:14:19', 'root', 'success'),
	(211, 'Migration20140408121756ComCourses.php', 'migrations', 'e6de7ac64a9352175692c56c686f76e7', 'up', '2014-09-03 19:14:19', 'root', 'success'),
	(212, 'Migration20140408190610ComEvents.php', 'migrations', '38526ffe9784e8da23545970d0b99a93', 'up', '2014-09-03 19:14:19', 'root', 'success'),
	(213, 'Migration20140410085610ComSupport.php', 'migrations', 'e2a46fb1de57373479c75b35511ebcd3', 'up', '2014-09-03 19:14:19', 'root', 'success'),
	(214, 'Migration20140415105610ComTopics.php', 'migrations', 'a422ed5de3500da8c332f817d21fd261', 'up', '2014-09-03 19:14:19', 'root', 'success'),
	(215, 'Migration20140416082740ComFeatures.php', 'migrations', '38540f65cade947c02405814a8fc03a8', 'up', '2014-09-03 19:14:20', 'root', 'success'),
	(216, 'Migration20140417132300ComFeedaggregator.php', 'migrations', '9450ad27cdfca0e878fef3682e2f1117', 'up', '2014-09-03 19:14:20', 'root', 'success'),
	(217, 'Migration20140417134640TplKameleonAdmin.php', 'migrations', 'dc12dc36261dbf9fd59b82b6513877d4', 'up', '2014-09-03 19:14:20', 'root', 'success'),
	(218, 'Migration20140417203615ComResources.php', 'migrations', 'a539d6ea0f8fbb7233ef6d5d6d620171', 'up', '2014-09-03 19:14:20', 'root', 'success'),
	(219, 'Migration20140421080012PlgTagsPublications.php', 'migrations', '18d9da5aed4b26bdbba99c7ba2013551', 'up', '2014-09-03 19:14:20', 'root', 'success'),
	(220, 'Migration20140421112851ComTools.php', 'migrations', '6993fc2600764e1e35b78b7a51a8569b', 'up', '2014-09-03 19:14:20', 'root', 'success'),
	(221, 'Migration20140421130020PlgHubzeroSystemplate.php', 'migrations', 'c736baf04657efcca3c222adc7ca33ee', 'up', '2014-09-03 19:14:20', 'root', 'success'),
	(222, 'Migration20140421130020PlgHubzeroSystickets.php', 'migrations', '2fe9c4550151025d7f3e10de51bc45de', 'up', '2014-09-03 19:14:20', 'root', 'success'),
	(223, 'Migration20140421130020PlgHubzeroSysusers.php', 'migrations', '1b912287e7ab93dec7efea1058e151e9', 'up', '2014-09-03 19:14:20', 'root', 'success'),
	(224, 'Migration20140421135022ComTools.php', 'migrations', 'c10911424e592d18b2cd1f62fb155dd8', 'up', '2014-09-03 19:14:20', 'root', 'success'),
	(225, 'Migration20140421182216Core.php', 'migrations', '71f39cd9c5e52cc4ee38ac161a01f4bc', 'up', '2014-09-03 19:14:20', 'root', 'success'),
	(226, 'Migration20140422082422ComTools.php', 'migrations', 'ca662aede45dc4e2ea8c9a41dd0732cd', 'up', '2014-09-03 19:14:20', 'root', 'success'),
	(227, 'Migration20140423131112ComGroups.php', 'migrations', 'af8a0e03e971225764420712983dba4a', 'up', '2014-09-03 19:14:20', 'root', 'success'),
	(228, 'Migration20140428094910ComSupport.php', 'migrations', '8d29f576b55a8e066c65a97df3961cdc', 'up', '2014-09-03 19:14:20', 'root', 'success'),
	(229, 'Migration20140428183704Core.php', 'migrations', '58ae41533557b4e03b6c676eb74ffa99', 'up', '2014-09-03 19:14:21', 'root', 'success'),
	(230, 'Migration20140502184454Core.php', 'migrations', '747dfaee88f169d90a4532022baf621d', 'up', '2014-09-03 19:14:21', 'root', 'success'),
	(231, 'Migration20140505141538ComTools.php', 'migrations', 'b5961d1e8b9a87f33464bce67e8a1782', 'up', '2014-09-03 19:14:21', 'root', 'success'),
	(232, 'Migration20140505160720PlgSupportTime.php', 'migrations', 'ed84ad1b74460c96b8a6db468adb9d5b', 'up', '2014-09-03 19:14:21', 'root', 'success'),
	(233, 'Migration20140506104910PlgMembersFavorites.php', 'migrations', '13069051ff2d774ce0331a93e7a454ca', 'up', '2014-09-03 19:14:21', 'root', 'success'),
	(234, 'Migration20140508120000PlgMembersDashboard.php', 'migrations', '878f103169d3e5f71268ae6adc2d9972', 'up', '2014-09-03 19:14:21', 'root', 'success'),
	(235, 'Migration20140508122800PlgMembersDashboard.php', 'migrations', '1b7523a669dc229fd2d726e6d5b6d8c6', 'up', '2014-09-03 19:14:21', 'root', 'success'),
	(236, 'Migration20140509133410ComSupport.php', 'migrations', '51257ab13c6dda7aaccd5ed5b2b0e642', 'up', '2014-09-03 19:14:21', 'root', 'success'),
	(237, 'Migration20140512120000ComPublications.php', 'migrations', '45633a75e5997b41eeb0421d3a8a2a00', 'up', '2014-09-03 19:14:21', 'root', 'success'),
	(238, 'Migration20140515130000ComPublications.php', 'migrations', '96f353949933804c367a4a26cef4f2b3', 'up', '2014-09-03 19:14:21', 'root', 'success'),
	(239, 'Migration20140519120000ComPublications.php', 'migrations', 'b3bf1b9b66b602c31c7d8c197a5593fe', 'up', '2014-09-03 19:14:21', 'root', 'success'),
	(240, 'Migration20140520200100ComUpdate.php', 'migrations', 'db6330986e43897a207b2a69d269ef46', 'up', '2014-09-03 19:14:21', 'root', 'success'),
	(241, 'Migration20140521141237ComNewsletter.php', 'migrations', '5d3f829341d20dc72f3343e149244196', 'up', '2014-09-03 19:14:21', 'root', 'success'),
	(242, 'Migration20140521145232ComUsers.php', 'migrations', 'db1153ac28ecaca9e82b139a165458a9', 'up', '2014-09-03 19:14:21', 'root', 'success'),
	(243, 'Migration20140522100120PlgContentAntispam.php', 'migrations', '3fec3a3a2fe8f6e688d8efb5a00b9935', 'up', '2014-09-03 19:14:21', 'root', 'success'),
	(244, 'Migration20140528145010ComRegister.php', 'migrations', '3d0c48b1d3b1b78b078de325e01f929c', 'up', '2014-09-03 19:14:21', 'root', 'success'),
	(245, 'Migration20140529150000PlgMembersImpact.php', 'migrations', '2c27e581ce5ee2757365895dfc2798b7', 'up', '2014-09-03 19:14:22', 'root', 'success'),
	(246, 'Migration20140529172125ComCourses.php', 'migrations', '4cceede5063636ba9a894a96eb3fb549', 'up', '2014-09-03 19:14:22', 'root', 'success'),
	(247, 'Migration20140529192810ComCourses.php', 'migrations', 'd5faed53d7a308b544bf0e425fce3eeb', 'up', '2014-09-03 19:14:22', 'root', 'success'),
	(248, 'Migration20140603164434ComCourses.php', 'migrations', '50170eb0e00664f571a656917862a52b', 'up', '2014-09-03 19:14:22', 'root', 'success'),
	(249, 'Migration20140605155804ComPublications.php', 'migrations', '8af3b556322071425499649275e812a1', 'up', '2014-09-03 19:14:22', 'root', 'success'),
	(250, 'Migration20140606165053ComCourses.php', 'migrations', 'a686ee88116241670c9ea552af41e6ca', 'up', '2014-09-03 19:14:22', 'root', 'success'),
	(251, 'Migration20140609160011ComFeedback.php', 'migrations', '99a67a2164361ff55c8c5f980dc09e44', 'up', '2014-09-03 19:14:22', 'root', 'success'),
	(252, 'Migration20140611102711ComCourses.php', 'migrations', '7fc25caf3cfc2d18cbd2791b2be77e18', 'up', '2014-09-03 19:14:22', 'root', 'success'),
	(253, 'Migration20140611141603ComMembers.php', 'migrations', '6252ca878b3c587d6ffe57303e6a24e0', 'up', '2014-09-03 19:14:22', 'root', 'success'),
	(254, 'Migration20140617153609ComTools.php', 'migrations', '74d7bf2ef154f6448de7779141d4f54d', 'up', '2014-09-03 19:14:22', 'root', 'success'),
	(255, 'Migration20140623144900Core.php', 'migrations', '9234d45e7361dcb8c4ecd7e4bb6ca77e', 'up', '2014-09-03 19:14:22', 'root', 'success'),
	(256, 'Migration20140624123157ComCitations.php', 'migrations', '353651b7f7ba15800ae04290c240e4ff', 'up', '2014-09-03 19:14:22', 'root', 'success'),
	(257, 'Migration20140626100712PlgPublicationsGroups.php', 'migrations', '843ac717207969fbc2124440bec55b93', 'up', '2014-09-03 19:14:22', 'root', 'success'),
	(258, 'Migration20140626155344ComUsers.php', 'migrations', 'cad0b846df2907cb0986f72426960d59', 'up', '2014-09-03 19:14:22', 'root', 'success'),
	(259, 'Migration20140627062357ComSupport.php', 'migrations', '0e26ff5d015903a447ee39d89c9d46d4', 'up', '2014-09-03 19:14:22', 'root', 'success'),
	(260, 'Migration20140627091431ComSupport.php', 'migrations', '817a8df1de833da30556dabef799266a', 'up', '2014-09-03 19:14:22', 'root', 'success'),
	(261, 'Migration20140627140011PlgResourcesReviews.php', 'migrations', 'e8427197e87ff3104f39fed8ee4f236e', 'up', '2014-09-03 19:14:22', 'root', 'success'),
	(262, 'Migration20140630084843ComCollections.php', 'migrations', 'dd4b14226292191927e8fba24006d2fb', 'up', '2014-09-03 19:14:22', 'root', 'success'),
	(263, 'Migration20140702071151ComBlog.php', 'migrations', 'da2a6aae5e9a318dcf29bc0d35bb64f5', 'up', '2014-09-03 19:14:22', 'root', 'success'),
	(264, 'Migration20140702115751ComForum.php', 'migrations', 'e0329449772180a87e8622a58cc01d3a', 'up', '2014-09-03 19:14:22', 'root', 'success'),
	(265, 'Migration20140702122251ComKb.php', 'migrations', '1507677f5be9b06980ae4532d9ab4d1a', 'up', '2014-09-03 19:14:22', 'root', 'success'),
	(266, 'Migration20140702130951ComAnswers.php', 'migrations', 'ccd4fb8b9bd1f58935424989101a8e83', 'up', '2014-09-03 19:14:22', 'root', 'success'),
	(267, 'Migration20140702160951ComWishlist.php', 'migrations', '166dde2a5560e6fded28b000593d2927', 'up', '2014-09-03 19:14:22', 'root', 'success'),
	(268, 'Migration20140703083327PlgResourcesReviews.php', 'migrations', '6988d49f341dea191a049b6595763448', 'up', '2014-09-03 19:14:22', 'root', 'success'),
	(269, 'Migration20140703100727ComWiki.php', 'migrations', '3b98a99fc27b7700708c912a14e3f591', 'up', '2014-09-03 19:14:22', 'root', 'success'),
	(270, 'Migration20140703163627PlgXmessageHandler.php', 'migrations', '0edf3a6d6c02c5b425670c3a8718cf86', 'up', '2014-09-03 19:14:22', 'root', 'success'),
	(271, 'Migration20140709144527ComTags.php', 'migrations', 'f567d99d34b774bf0248e8016ca028dd', 'up', '2014-09-03 19:14:22', 'root', 'success'),
	(272, 'Migration20140709174727ModSupportactivity.php', 'migrations', '05c8188c1dbddebe55a30aa7706ae54a', 'up', '2014-09-03 19:14:22', 'root', 'success'),
	(274, 'Migration20140716180300ComTools.php', 'migrations', 'f4bea1a995cfc0ba595097c344157115', 'up', '2014-09-03 19:14:22', 'root', 'success'),
	(275, 'Migration20140716182939Core.php', 'migrations', '9a4f6fbf07cc092cf3c1b20730d20986', 'up', '2014-09-03 19:14:22', 'root', 'success'),
	(276, 'Migration20140721163818PlgTimeSummary.php', 'migrations', 'b87e562c9585c145c186c64659484db0', 'up', '2014-09-03 19:14:22', 'root', 'success'),
	(277, 'Migration20140722152439PlgAuthenticationHubzero.php', 'migrations', 'a03d6caa952a5f104974c820cf2056e1', 'up', '2014-09-03 19:14:22', 'root', 'success'),
	(278, 'Migration20140730153624ComUsers.php', 'migrations', 'fe303201fcbf7682e0ff3868eb00cfa9', 'up', '2014-09-03 19:14:22', 'root', 'success'),
	(279, 'Migration20140730181124PlgSystemHubzero.php', 'migrations', '258f930c1046bc28433c552b93ede87e', 'up', '2014-09-03 19:14:22', 'root', 'success'),
	(280, 'Migration20140804153815Core.php', 'migrations', 'd91c99665160703676594596f4afaa29', 'up', '2014-09-03 19:14:22', 'root', 'success'),
	(281, 'Migration20140805185942ComMembers.php', 'migrations', '94582da6a04ca7a0f6c4dc5f3cab7fb7', 'up', '2014-09-03 19:14:22', 'root', 'success'),
	(282, 'Migration20140805223444PlgGroupsBlog.php', 'migrations', '67b85bdcb3f56032c473a3bb363465f0', 'up', '2014-09-03 19:14:22', 'root', 'success'),
	(283, 'Migration20140807143056ComMembers.php', 'migrations', '8ea6301855f4704e70dd5d99435b1d0a', 'up', '2014-09-03 19:14:22', 'root', 'success'),
	(285, 'Migration20140808195514ComTools.php', 'migrations', 'cc6ca2062f8b12869a1add55702c8b38', 'up', '2014-09-03 19:14:22', 'root', 'success'),
	(286, 'Migration20140813132614ComSupport.php', 'migrations', 'a0693170b8d8fc36659d186f961abb11', 'up', '2014-09-03 19:14:22', 'root', 'success'),
	(287, 'Migration20140813200031ComTools.php', 'migrations', 'e6565c74ce47739656b07aec9eeb6ba7', 'up', '2014-09-03 19:14:22', 'root', 'success'),
	(288, 'Migration20140814103014ComSupport.php', 'migrations', 'f42305fa77a0e85568fb24d3a32d36a4', 'up', '2014-09-03 19:14:22', 'root', 'success'),
	(289, 'Migration20140815061927ComSupport.php', 'migrations', 'a1d1ec3a729db33aa390b3f8c3069758', 'up', '2014-09-03 19:14:23', 'root', 'success'),
	(290, 'Migration20140815153140ComCourses.php', 'migrations', 'a433fa0af47d680ef8ec213e83a8c847', 'up', '2014-09-03 19:14:23', 'root', 'success'),
	(291, 'Migration20140818160255ComCitations.php', 'migrations', 'ed6748169df1d72672f641e6a3bcd3d6', 'up', '2014-09-03 19:14:23', 'root', 'success'),
	(292, 'Migration20140818160800ComSupport.php', 'migrations', 'e2834c531541bf93cc90e814add67a87', 'up', '2014-09-03 19:14:23', 'root', 'success'),
	(293, 'Migration20140818161500ComWishlist.php', 'migrations', 'f02263b0feb1c447629c1c3829d3059c', 'up', '2014-09-03 19:14:23', 'root', 'success'),
	(294, 'Migration20140818162220PlgTools.php', 'migrations', 'c1d7842636858e67d870f70c3c8c0ea5', 'up', '2014-09-03 19:14:23', 'root', 'success'),
	(295, 'Migration20140820171853ComEvents.php', 'migrations', 'b89c1b0b9a8b16acbb1c47e1abbfe452', 'up', '2014-09-03 19:14:23', 'root', 'success'),
	(296, 'Migration20140822132824ComUsers.php', 'migrations', 'b85443253a106a48485b00920d2fd386', 'up', '2014-09-03 19:14:23', 'root', 'success'),
	(297, 'Migration20140822153500PlgGroupsAnnouncements.php', 'migrations', 'c3022825b4ad406e7fd2cdbeae742e32', 'up', '2014-09-03 19:14:23', 'root', 'success'),
	(298, 'Migration20140822154400ComAnswers.php', 'migrations', 'a57759bfcbf23cbcc212f69464f769f8', 'up', '2014-09-03 19:14:23', 'root', 'success'),
	(299, 'Migration20140822154900ComBlog.php', 'migrations', '6b7c9403edb226c9a0ad9ab8d64696d1', 'up', '2014-09-03 19:14:23', 'root', 'success'),
	(300, 'Migration20140822155500ComCollections.php', 'migrations', '805e29f0df7e534634560e633d0b266d', 'up', '2014-09-03 19:14:23', 'root', 'success'),
	(301, 'Migration20140822155900ComCron.php', 'migrations', 'd6587c72ba29ec626972a1e509d11075', 'up', '2014-09-03 19:14:23', 'root', 'success'),
	(302, 'Migration20140822161100ComKb.php', 'migrations', '185c7472518d0d76a3c520f1660fb219', 'up', '2014-09-03 19:14:23', 'root', 'success'),
	(303, 'Migration20140822161900PlgHubzeroComments.php', 'migrations', '261fa5ec4534dceefa4c24a8f4dc3d43', 'up', '2014-09-03 19:14:23', 'root', 'success'),
	(304, 'Migration20140822203559ComPoll.php', 'migrations', 'c9594778fb9517a755c2a9cfad8d4294', 'up', '2014-09-03 19:14:23', 'root', 'success'),
	(305, 'Migration20140826120656ComEvents.php', 'migrations', '3acd83ed9bc788374e22ce4f34484060', 'up', '2014-09-03 19:14:23', 'root', 'success'),
	(306, 'Migration20140827100656ComPublications.php', 'migrations', 'd2f3fce7ac5706f8ac30279937ba732d', 'up', '2014-09-03 19:14:23', 'root', 'success'),
	(307, 'Migration20140828150712PlgWhatsnewPublications.php', 'migrations', 'a255521250e0c84bc4534cabe32812d6', 'up', '2014-09-03 19:14:23', 'root', 'success'),
	(308, 'Migration20140829131016PlgMembersDashboard.php', 'migrations', '0692e180dba74d8c185ec0d6baeee077', 'up', '2014-09-03 19:14:23', 'root', 'success'),
	(309, 'Migration20140829131400PlgMembersMessages.php', 'migrations', 'c1aa60f4d2fa2c9786a45fc8cd01b1e5', 'up', '2014-09-03 19:14:23', 'root', 'success'),
	(310, 'Migration20140829132200ComWiki.php', 'migrations', 'd76e6cd1dcee33d4313bc6ed13f8b382', 'up', '2014-09-03 19:14:23', 'root', 'success'),
	(311, 'Migration20140829142600ComCollections.php', 'migrations', 'e016899c6034e9663e9fbb6c15f20de5', 'up', '2014-09-03 19:14:23', 'root', 'success'),
	(312, 'Migration20140904174546ComKb.php', 'migrations', 'fad15f5c5ceda64f022ef1bc75ada55f', 'up', '2014-09-20 17:22:57', 'root', 'success'),
	(313, 'Migration20140922135214Core.php', 'migrations', 'e506b37f04680b0540937b984991a0e6', 'up', '2014-09-22 13:55:41', 'root', 'success'),
	(314, 'Migration20140925213032ComTools.php', 'migrations', '82a01d29182bff4ebaf08f0ad246dc07', 'up', '2014-09-25 21:37:26', 'root', 'success'),
	(315, 'Migration20140806131000ModLogged.php','migrations','d3336ff30569e7e393b3f6980a8e4d5b','up','2015-03-11 18:27:58','root', 'success'),
	(316, 'Migration20140806144415ModStatus.php','migrations','f1cbb8a833daf28fefa80fc1c5c2152c','up','2015-03-11 18:27:58','root', 'success'),
	(317, 'Migration20140806152408ModXwhosonline.php','migrations','0f0b4fc4be20cb5c542981b00a2ebc05','up','2015-03-11 18:27:58','root', 'success'),
	(318, 'Migration20140806152610ModWhosonline.php','migrations','4224376fae89a7ee9a4b4060c76e7534','up','2015-03-11 18:27:58','root', 'success'),
	(319, 'Migration20140908193005ComGroups.php','migrations','c798006901f098f50162243719e6c374','up','2015-03-11 18:27:58','root', 'success'),
	(320, 'Migration20140916140933ComCitations.php','migrations','2184572bcfa657e5f65614041f1fecb7','up','2015-03-11 18:27:58','root', 'success'),
	(321, 'Migration20141004111111ComCitations.php','migrations','77a8c0db26a1dce7e445270c48a2d67c','up','2015-03-11 18:27:58','root', 'success'),
	(322, 'Migration20141009072712ModCollect.php','migrations','d7252bc36b233d6cc9f32b4b9ad72f4f','up','2015-03-11 18:27:58','root', 'success'),
	(323, 'Migration20141009154241Core.php','migrations','266dfbd2cf503c9982efa76afc4494ae','up','2015-03-11 18:27:58','root', 'success'),
	(324, 'Migration20141009171309ComTools.php','migrations','d8920f3de4ff60bb08640ca63fc2ffeb','up','2015-03-11 18:27:58','root', 'success'),
	(325, 'Migration20141009175833ComCourses.php','migrations','91229fd432bdf2c41346482660ecbd74','up','2015-03-11 18:27:58','root', 'success'),
	(326, 'Migration20141009182840ComAnswers.php','migrations','4a3af152265b83bb2821fa58ea82a352','up','2015-03-11 18:27:58','root', 'success'),
	(327, 'Migration20141009203743ComBlog.php','migrations','a1761e5e9aa9ebba1e3c7a47f7a7d852','up','2015-03-11 18:27:58','root', 'success'),
	(328, 'Migration20141009204207ComSupport.php','migrations','aaff0924c1eaf0c8c30ff50202c7d90d','up','2015-03-11 18:27:58','root', 'success'),
	(329, 'Migration20141009205234ComTags.php','migrations','bd6342b4aaddec878c7572db1052d768','up','2015-03-11 18:27:58','root', 'success'),
	(330, 'Migration20141009205422ComUsers.php','migrations','ec12d6e3ea94ba699d83caf7cb8482e2','up','2015-03-11 18:27:58','root', 'success'),
	(331, 'Migration20141010090013Imports.php','migrations','f056970e147c54681637dcf024a0e296','up','2015-03-11 18:27:58','root', 'success'),
	(332, 'Migration20141010150100ComSupport.php','migrations','5cab691ac534cc5ba0954b5381745fd4','up','2015-03-11 18:27:58','root', 'success'),
	(333, 'Migration20141021161308Core.php','migrations','aafdbc4dcfedf81e5c99bbf28dbd1d6a','up','2015-03-11 18:27:58','root', 'success'),
	(334, 'Migration20141021164246Core.php','migrations','65dd11945ca90ba14914b1d427354f7d','up','2015-03-11 18:27:58','root', 'success'),
	(335, 'Migration20141022103600PlgResourcesAbout.php','migrations','1ae4912f7423e56787787c01770f267c','up','2015-03-11 18:27:58','root', 'success'),
	(336, 'Migration20141022110100ComSupport.php','migrations','741910662755b8adbeb659100c796291','up','2015-03-11 18:27:58','root', 'success'),
	(337, 'Migration20141028094100ComPublications.php','migrations','359c6287a4f030e26bc9a8b8e3de6601','up','2015-03-11 18:27:58','root', 'success'),
	(338, 'Migration20141029112543ComBlog.php','migrations','5ff8cdcb6d65bc027ae7d2f27d34549c','up','2015-03-11 18:27:58','root', 'success'),
	(339, 'Migration20141029174920PlgUserContactcreator.php','migrations','8f7bc39fae673f4b6456f13b7df2dec1','up','2015-03-11 18:27:58','root', 'success'),
	(340, 'Migration20141029185515Core.php','migrations','a72c8da3cfc0c7cf6c2f846bde6c3874','up','2015-03-11 18:27:58','root', 'success'),
	(341, 'Migration20141029192338ComOaipmh.php','migrations','aad2f33356f1f29a5e426ab46701f01b','up','2015-03-11 18:27:58','root', 'success'),
	(342, 'Migration20141029193053Core.php','migrations','c063aa10d5260cbcaf68736730a221c8','up','2015-03-11 18:27:58','root', 'success'),
	(343, 'Migration20141030163700ComBlog.php','migrations','de1db44ab8206f1392d0bbacebf1a9f8','up','2015-03-11 18:27:58','root', 'success'),
	(344, 'Migration20141105073734ComTags.php','migrations','e7aaff5113478454ef4c1c5a75768cac','up','2015-03-11 18:27:58','root', 'success'),
	(345, 'Migration20141105142938ComUpdate.php','migrations','8dfaad338ffbb8578bb93b60ba97001b','up','2015-03-11 18:27:58','root', 'success'),
	(346, 'Migration20141105145512PlgSystemIndent.php','migrations','fa35cc34d81489e70c9992a4428c86fd','up','2015-03-11 18:27:58','root', 'success'),
	(347, 'Migration20141106184927PlgTimeCsv.php','migrations','66d6da14ab5ba3cda744c199b9ae63ad','up','2015-03-11 18:27:58','root', 'success'),
	(348, 'Migration20141106202312ModCourses.php','migrations','94079e71e289698c6d93447a4ece532e','up','2015-03-11 18:27:58','root', 'success'),
	(349, 'Migration20141110143234ComMembers.php','migrations','5bce1ba1bab1fd9d33e201552c63258a','up','2015-03-11 18:27:58','root', 'success'),
	(350, 'Migration20141110232014ComMembers.php','migrations','5a0bc1557226c2637239e47689705f72','up','2015-03-11 18:27:58','root', 'success'),
	(351, 'Migration20141111193301ComCollections.php','migrations','9fcb35dba181b441ef3a389e4d512759','up','2015-03-11 18:27:59','root', 'success'),
	(352, 'Migration20141112142733ComCourses.php','migrations','a7cceb9d418e29ccf315414a38e16e8b','up','2015-03-11 18:27:59','root', 'success'),
	(353, 'Migration20141112191247ComCourses.php','migrations','cf00988db7845167156ef18bd1975f9a','up','2015-03-11 18:27:59','root', 'success'),
	(354, 'Migration20141112203625ComHubgraph.php','migrations','769025425da5df639fbca6126868dcae','up','2015-03-11 18:27:59','root', 'success'),
	(355, 'Migration20141112203716ComResources.php','migrations','c749be411eaefcb82a7ecb166e8bcc97','up','2015-03-11 18:27:59','root', 'success'),
	(356, 'Migration20141113215958ComCourses.php','migrations','b9c28008ab5fb91685d9b2e1b15482d8','up','2015-03-11 18:27:59','root', 'success'),
	(357, 'Migration20141113222151ComCourses.php','migrations','4529429f38ea90ed6e21ec5bb4675568','up','2015-03-11 18:27:59','root', 'success'),
	(358, 'Migration20141114195247ComHubgraph.php','migrations','1afdf2463f9863e732996d5265cde4f0','up','2015-03-11 18:27:59','root', 'success'),
	(359, 'Migration20141114195313ComGroups.php','migrations','d17541b932134c69a8fb4eaa73b6f478','up','2015-03-11 18:27:59','root', 'success'),
	(360, 'Migration20141117095313ComPublications.php','migrations','98b5c3f54bad9c387dabdc783fd755d1','up','2015-03-11 18:27:59','root', 'success'),
	(361, 'Migration20141119145715ComUsers.php','migrations','57ecbec1f4c98511f9023e15eb330759','up','2015-03-11 18:27:59','root', 'success'),
	(362, 'Migration20141119164113ComPublications.php','migrations','d0500db38342cacc04767348f1d3d97b','up','2015-03-11 18:27:59','root', 'success'),
	(363, 'Migration20141119180000ComCitations.php','migrations','70e0785ad2bf3e253852b698feceb326','up','2015-03-11 18:27:59','root', 'success'),
	(365, 'Migration20141120215253ComHubgraph.php','migrations','8c97bbc1f873d68e01fe0fa9b798300b','up','2015-03-11 18:27:59','root', 'success'),
	(366, 'Migration20141121132012ModMytodos.php','migrations','40a5430e75bd2c6ce2ae8e864385ead0','up','2015-03-11 18:27:59','root', 'success'),
	(367, 'Migration20141121144051ComCollections.php','migrations','6da5ce15d1e83db00726d68c7491a227','up','2015-03-11 18:27:59','root', 'success'),
	(368, 'Migration20141201142000PlgGroupsCitations.php','migrations','0401f306afe24358570b870ffe7ae381','up','2015-03-11 18:27:59','root', 'success'),
	(369, 'Migration20141201151300ComCitations.php','migrations','0ddaf7b17d47d6e464771196699bfbe1','up','2015-03-11 18:27:59','root', 'success'),
	(370, 'Migration20141201170547ComGroups.php','migrations','014416e087852fb24f13d9e42b016cf9','up','2015-03-11 18:27:59','root', 'success'),
	(371, 'Migration20141202211549PlgMembersDashboard.php','migrations','2b01589053818e07fc3c308a97593b6a','up','2015-03-11 18:27:59','root', 'success'),
	(372, 'Migration20141212142300ComCitations.php','migrations','65f2c83cc61a42f5c110eca0f6387545','up','2015-03-11 18:27:59','root', 'success'),
	(373, 'Migration20141215165100ComCitations.php','migrations','b7723a5de312764f31b1ed571e0df663','up','2015-03-11 18:27:59','root', 'success'),
	(374, 'Migration20141222135427ComTags.php','migrations','22388935f56d35b6e14732adc50e1d7c','up','2015-03-11 18:27:59','root', 'success'),
	(375, 'Migration20150105220027ComWiki.php','migrations','7c1792a855e9351a3dc9f4c5ea91f071','up','2015-03-11 18:27:59','root', 'success'),
	(376, 'Migration20150106211443ComCollections.php','migrations','392104429eefbbf5e8a693feb52b7aa0','up','2015-03-11 18:27:59','root', 'success'),
	(377, 'Migration20150107021244PlgHubzeroRecaptcha.php','migrations','9c574e6f61fa87187f3bcbb600ea22bf','up','2015-03-11 18:27:59','root', 'success'),
	(378, 'Migration20150107022822PlgHubzeroRecaptcha.php','migrations','8b4186db09388c1631046992127871f8','up','2015-03-11 18:27:59','root', 'success'),
	(379, 'Migration20150108140000ComCitations.php','migrations','66e38b1fbc1cd62cc9430f3090c712e5','up','2015-03-11 18:27:59','root', 'success'),
	(380, 'Migration20150108152843ComSupport.php','migrations','49dd52a49020910d3fdd9189633492a1','up','2015-03-11 18:27:59','root', 'success'),
	(381, 'Migration20150109180705ComUsers.php','migrations','b2a566296ad95342844bcf9ca9cd0564','up','2015-03-11 18:27:59','root', 'success'),
	(382, 'Migration20150109200952ComUsers.php','migrations','bcf46b62da94160f54a60c6126149c83','up','2015-03-11 18:27:59','root', 'success'),
	(383, 'Migration20150112212929PlgSystemCertificate.php','migrations','e8b35023446a72b47095e73307615fbe','up','2015-03-11 18:27:59','root', 'success'),
	(384, 'Migration20150112212939PlgAuthenticationCertificate.php','migrations','47956a5ca948992d8112b1e596639f6d','up','2015-03-11 18:27:59','root', 'success'),
	(385, 'Migration20150115234546PlgGroupsWishlist.php','migrations','909802a145fd15f485c31b77587fbc2c','up','2015-03-11 18:27:59','root', 'success'),
	(386, 'Migration20150116111000ComPublications.php','migrations','d2d4a2d9d00f9903e09f8dad3582016a','up','2015-03-11 18:27:59','root', 'success'),
	(387, 'Migration20150119120000ModMycuration.php','migrations','d0acc6307e03d5719f232e957e7258c2','up','2015-03-11 18:27:59','root', 'success'),
	(388, 'Migration20150122165523ComSupport.php','migrations','90e7b0504a5370f7d8ae3868b26c923c','up','2015-03-11 18:27:59','root', 'success'),
	(389, 'Migration20150122170000ComPublications.php','migrations','c481f07903d82e600ac0a4eb2dc49f80','up','2015-03-11 18:27:59','root', 'success'),
	(390, 'Migration20150123133439PlgUserMiddleware.php','migrations','adf157b0c8e08f0c2e917bc2663281d1','up','2015-03-11 18:27:59','root', 'success'),
	(391, 'Migration20150123134039ComMembers.php','migrations','1e4c65d58e9d1a280e823dabc613fc0d','up','2015-03-11 18:27:59','root', 'success'),
	(392, 'Migration20150126193709ComWishlist.php','migrations','94462325a8d030e6c580348fd9360801','up','2015-03-11 18:27:59','root', 'success'),
	(393, 'Migration20150204181025ComMembers.php','migrations','a6f908df1df77d64de86d03af5d217a1','up','2015-03-11 18:27:59','root', 'success'),
	(394, 'Migration20150210234536ComMembers.php','migrations','8c13afc1ccc5a5462673f7434c481a2b','up','2015-03-11 18:27:59','root', 'success'),
	(395, 'Migration20150211164131ComUsers.php','migrations','55bd298d7212c9527670a1fa7e8425fe','up','2015-03-11 18:27:59','root', 'success'),
	(396, 'Migration20150218105200PlgGroupsForum.php','migrations','5a9e6c935ee10e5aaac6822e87b9596e','up','2015-03-11 18:27:59','root', 'success'),
	(397, 'Migration20150218180139PlgOaipmh.php','migrations','d659d80c45c822aa7265c4e7d49745db','up','2015-03-11 18:27:59','root', 'success'),
	(398, 'Migration20150218183139ComMenus.php','migrations','8aa17d17c4851ba39b66c4a34a05f391','up','2015-03-11 18:27:59','root', 'success'),
	(399, 'Migration20150224205200PlgContent.php','migrations','51f3a4e61c040708ab7bfd5cc81d3a38','up','2015-03-11 18:27:59','root', 'success'),
	(400, 'Migration20150225144415PlgUserGeo.php','migrations','533642c67319a2e06a4f92a55d60e971','up','2015-03-11 18:27:59','root', 'success'),
	(401, 'Migration20150225150953ComTools.php','migrations','af44b4ad0ed116fd5cbeced64e8e70df','up','2015-03-11 18:27:59','root', 'success'),
	(402, 'Migration20150305100000ComPublications.php','migrations','d188399b3df2b5e17e666e1560fc028b','up','2015-03-11 18:27:59','root', 'success');

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
INSERT INTO `#__modules` (`id`, `title`, `note`, `content`, `ordering`, `position`, `checked_out`, `checked_out_time`, `publish_up`, `publish_down`, `published`, `module`, `access`, `showtitle`, `params`, `client_id`, `language`) VALUES (200,'Welcome','Front page','<div class=\"hero-copy\">\r\n<p class=\"eyebrow\">Research &middot; Teaching &middot; Collaboration</p>\r\n<div class=\"hero-heading\"><h2>Somewhere to put your work, and to find everyone else&rsquo;s</h2></div>\r\n<p>Datasets, papers, teaching material and the software behind them, published by the people who made them and open to anyone who wants them.</p>\r\n<div class=\"hero-actions\">\r\n<a class=\"primary-button\" href=\"/resources\">Browse the resources</a>\r\n<a class=\"outline-button\" href=\"/members/register\">Create an account</a>\r\n</div>\r\n</div>',1,'hero',0,'0000-00-00 00:00:00','0000-00-00 00:00:00','0000-00-00 00:00:00',1,'mod_custom',1,0,'{\"prepare_content\":\"1\",\"layout\":\"_:default\"}',0,'*');
INSERT INTO `#__modules` (`id`, `title`, `note`, `content`, `ordering`, `position`, `checked_out`, `checked_out_time`, `publish_up`, `publish_down`, `published`, `module`, `access`, `showtitle`, `params`, `client_id`, `language`) VALUES (201,'What you will find here','Front page','<div class=\"grid cf\">\r\n	<div class=\"col span-third\">\r\n		<h3>Work you can use</h3>\r\n		<p>Datasets, papers and reports, teaching material and recorded seminars, catalogued by what they are and citable when you need to refer to them.</p>\r\n		<p><a href=\"/resources\">Browse the resources</a></p>\r\n	</div>\r\n	<div class=\"col span-third\">\r\n		<h3>Work that is citable</h3>\r\n		<p>A publication is versioned, so a dataset can be released, revised and cited like anything else, each version keeping a record of its own.</p>\r\n		<p><a href=\"/publications\">See the publications</a></p>\r\n	</div>\r\n	<div class=\"col span-third omega\">\r\n		<h3>People to ask</h3>\r\n		<p>A forum, a question-and-answer queue and a wiki, kept by the people who work in this field. Most of what is useful here was written by someone answering a question.</p>\r\n		<p><a href=\"/answers\">Ask a question</a></p>\r\n	</div>\r\n</div>',1,'home-1',0,'0000-00-00 00:00:00','0000-00-00 00:00:00','0000-00-00 00:00:00',1,'mod_custom',1,1,'{\"prepare_content\":\"1\",\"layout\":\"_:default\"}',0,'*');
INSERT INTO `#__modules` (`id`, `title`, `note`, `content`, `ordering`, `position`, `checked_out`, `checked_out_time`, `publish_up`, `publish_down`, `published`, `module`, `access`, `showtitle`, `params`, `client_id`, `language`) VALUES (202,'Where to start','Front page','<div class=\"grid cf\">\r\n	<div class=\"col span-third\">\r\n		<h3>Create an account</h3>\r\n		<p>It is free, and it opens up the groups, the projects and everything else that needs to know who you are.</p>\r\n		<p><a href=\"/members/register\">Register</a></p>\r\n	</div>\r\n	<div class=\"col span-third\">\r\n		<h3>Work with your group</h3>\r\n		<p>Private spaces for the part of the work that is not ready to be public. Invite colleagues and students, and publish when you are ready.</p>\r\n		<p><a href=\"/groups\">Find or start a group</a></p>\r\n	</div>\r\n	<div class=\"col span-third omega\">\r\n		<h3>Contribute something</h3>\r\n		<p>Upload a dataset, a paper, a set of slides, or a tool you have written, and let other people build on it.</p>\r\n		<p><a href=\"/resources/new\">Submit a resource</a></p>\r\n	</div>\r\n</div>',1,'home-2',0,'0000-00-00 00:00:00','0000-00-00 00:00:00','0000-00-00 00:00:00',1,'mod_custom',1,1,'{\"prepare_content\":\"1\",\"layout\":\"_:default\"}',0,'*');
INSERT INTO `#__modules_menu` (`moduleid`, `menuid`) VALUES (200,101);
INSERT INTO `#__modules_menu` (`moduleid`, `menuid`) VALUES (201,101);
INSERT INTO `#__modules_menu` (`moduleid`, `menuid`) VALUES (202,101);

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

INSERT INTO `#__template_styles` (`id`, `template`, `client_id`, `home`, `title`, `params`) VALUES (1,'meridian',0,'1','Meridian, a hub before it has a subject','{}');
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

