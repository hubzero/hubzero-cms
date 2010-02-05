# $Id: joomla.sql 12384 2009-06-28 03:02:34Z ian $

# --------------------------------------------------------

#
# Table structure for table `#__banner`
#

CREATE TABLE `#__banner` (
  `bid` int(11) NOT NULL auto_increment,
  `cid` int(11) NOT NULL default '0',
  `type` varchar(30) NOT NULL default 'banner',
  `name` varchar(255) NOT NULL default '',
  `alias` varchar(255) NOT NULL default '',
  `imptotal` int(11) NOT NULL default '0',
  `impmade` int(11) NOT NULL default '0',
  `clicks` int(11) NOT NULL default '0',
  `imageurl` varchar(100) NOT NULL default '',
  `clickurl` varchar(200) NOT NULL default '',
  `date` datetime default NULL,
  `showBanner` tinyint(1) NOT NULL default '0',
  `checked_out` tinyint(1) NOT NULL default '0',
  `checked_out_time` datetime NOT NULL default '0000-00-00 00:00:00',
  `editor` varchar(50) default NULL,
  `custombannercode` text,
  `catid` INTEGER UNSIGNED NOT NULL DEFAULT 0,
  `description` TEXT NOT NULL DEFAULT '',
  `sticky` TINYINT(1) UNSIGNED NOT NULL DEFAULT 0,
  `ordering` INTEGER NOT NULL DEFAULT 0,
  `publish_up` datetime NOT NULL default '0000-00-00 00:00:00',
  `publish_down` datetime NOT NULL default '0000-00-00 00:00:00',
  `tags` TEXT NOT NULL DEFAULT '',
  `params` TEXT NOT NULL DEFAULT '',
  PRIMARY KEY  (`bid`),
  KEY `viewbanner` (`showBanner`),
  INDEX `idx_banner_catid`(`catid`)
) TYPE=MyISAM CHARACTER SET `utf8`;

# --------------------------------------------------------

#
# Table structure for table `#__bannerclient`
#

CREATE TABLE `#__bannerclient` (
  `cid` int(11) NOT NULL auto_increment,
  `name` varchar(255) NOT NULL default '',
  `contact` varchar(255) NOT NULL default '',
  `email` varchar(255) NOT NULL default '',
  `extrainfo` text NOT NULL,
  `checked_out` tinyint(1) NOT NULL default '0',
  `checked_out_time` time default NULL,
  `editor` varchar(50) default NULL,
  PRIMARY KEY  (`cid`)
) TYPE=MyISAM CHARACTER SET `utf8`;

# --------------------------------------------------------

#
# Table structure for table `#__bannertrack`
#

CREATE TABLE  `#__bannertrack` (
  `track_date` date NOT NULL,
  `track_type` int(10) unsigned NOT NULL,
  `banner_id` int(10) unsigned NOT NULL
) TYPE=MyISAM CHARACTER SET `utf8`;

# --------------------------------------------------------

#
# Table structure for table `#__categories`
#

CREATE TABLE `#__categories` (
  `id` int(11) NOT NULL auto_increment,
  `parent_id` int(11) NOT NULL default 0,
  `title` varchar(255) NOT NULL default '',
  `name` varchar(255) NOT NULL default '',
  `alias` varchar(255) NOT NULL default '',
  `image` varchar(255) NOT NULL default '',
  `section` varchar(50) NOT NULL default '',
  `image_position` varchar(30) NOT NULL default '',
  `description` text NOT NULL,
  `published` tinyint(1) NOT NULL default '0',
  `checked_out` int(11) unsigned NOT NULL default '0',
  `checked_out_time` datetime NOT NULL default '0000-00-00 00:00:00',
  `editor` varchar(50) default NULL,
  `ordering` int(11) NOT NULL default '0',
  `access` tinyint(3) unsigned NOT NULL default '0',
  `count` int(11) NOT NULL default '0',
  `params` text NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `cat_idx` (`section`,`published`,`access`),
  KEY `idx_access` (`access`),
  KEY `idx_checkout` (`checked_out`)
) TYPE=MyISAM CHARACTER SET `utf8`;

# --------------------------------------------------------

#
# Table structure for table `#__components`
#

CREATE TABLE `#__components` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(50) NOT NULL default '',
  `link` varchar(255) NOT NULL default '',
  `menuid` int(11) unsigned NOT NULL default '0',
  `parent` int(11) unsigned NOT NULL default '0',
  `admin_menu_link` varchar(255) NOT NULL default '',
  `admin_menu_alt` varchar(255) NOT NULL default '',
  `option` varchar(50) NOT NULL default '',
  `ordering` int(11) NOT NULL default '0',
  `admin_menu_img` varchar(255) NOT NULL default '',
  `iscore` tinyint(4) NOT NULL default '0',
  `params` text NOT NULL,
  `enabled` tinyint(4) NOT NULL default '1',
  PRIMARY KEY  (`id`),
  KEY `parent_option` (`parent`, `option`(32))
) TYPE=MyISAM CHARACTER SET `utf8`;

#
# Dumping data for table `#__components`
#

INSERT INTO `#__components` VALUES (1, 'Banners', '', 0, 0, '', 'Banner Management', 'com_banners', 0, 'js/ThemeOffice/component.png', 0, 'track_impressions=0\ntrack_clicks=0\ntag_prefix=\n\n', 1);
INSERT INTO `#__components` VALUES (2, 'Banners', '', 0, 1, 'option=com_banners', 'Active Banners', 'com_banners', 1, 'js/ThemeOffice/edit.png', 0, '', 1);
INSERT INTO `#__components` VALUES (3, 'Clients', '', 0, 1, 'option=com_banners&c=client', 'Manage Clients', 'com_banners', 2, 'js/ThemeOffice/categories.png', 0, '', 1);
INSERT INTO `#__components` VALUES (4, 'Web Links', 'option=com_weblinks', 0, 0, '', 'Manage Weblinks', 'com_weblinks', 0, 'js/ThemeOffice/component.png', 0, 'show_comp_description=1\ncomp_description=\nshow_link_hits=1\nshow_link_description=1\nshow_other_cats=1\nshow_headings=1\nshow_page_title=1\nlink_target=0\nlink_icons=\n\n', 1);
INSERT INTO `#__components` VALUES (5, 'Links', '', 0, 4, 'option=com_weblinks', 'View existing weblinks', 'com_weblinks', 1, 'js/ThemeOffice/edit.png', 0, '', 1);
INSERT INTO `#__components` VALUES (6, 'Categories', '', 0, 4, 'option=com_categories&section=com_weblinks', 'Manage weblink categories', '', 2, 'js/ThemeOffice/categories.png', 0, '', 1);
INSERT INTO `#__components` VALUES (7, 'Contacts', 'option=com_contact', 0, 0, '', 'Edit contact details', 'com_contact', 0, 'js/ThemeOffice/component.png', 1, 'contact_icons=0\nicon_address=\nicon_email=\nicon_telephone=\nicon_fax=\nicon_misc=\nshow_headings=1\nshow_position=1\nshow_email=0\nshow_telephone=1\nshow_mobile=1\nshow_fax=1\nbannedEmail=\nbannedSubject=\nbannedText=\nsession=1\ncustomReply=0\n\n', 1);
INSERT INTO `#__components` VALUES (8, 'Contacts', '', 0, 7, 'option=com_contact', 'Edit contact details', 'com_contact', 0, 'js/ThemeOffice/edit.png', 1, '', 1);
INSERT INTO `#__components` VALUES (9, 'Categories', '', 0, 7, 'option=com_categories&section=com_contact_details', 'Manage contact categories', '', 2, 'js/ThemeOffice/categories.png', 1, 'contact_icons=0\nicon_address=\nicon_email=\nicon_telephone=\nicon_fax=\nicon_misc=\nshow_headings=1\nshow_position=1\nshow_email=0\nshow_telephone=1\nshow_mobile=1\nshow_fax=1\nbannedEmail=\nbannedSubject=\nbannedText=\nsession=1\ncustomReply=0\n\n', 1);
INSERT INTO `#__components` VALUES (10, 'Polls', 'option=com_poll', 0, 0, 'option=com_poll', 'Manage Polls', 'com_poll', 0, 'js/ThemeOffice/component.png', 0, '', 1);
INSERT INTO `#__components` VALUES (11, 'News Feeds', 'option=com_newsfeeds', 0, 0, '', 'News Feeds Management', 'com_newsfeeds', 0, 'js/ThemeOffice/component.png', 0, '', 1);
INSERT INTO `#__components` VALUES (12, 'Feeds', '', 0, 11, 'option=com_newsfeeds', 'Manage News Feeds', 'com_newsfeeds', 1, 'js/ThemeOffice/edit.png', 0, 'show_headings=1\nshow_name=1\nshow_articles=1\nshow_link=1\nshow_cat_description=1\nshow_cat_items=1\nshow_feed_image=1\nshow_feed_description=1\nshow_item_description=1\nfeed_word_count=0\n\n', 1);
INSERT INTO `#__components` VALUES (13, 'Categories', '', 0, 11, 'option=com_categories&section=com_newsfeeds', 'Manage Categories', '', 2, 'js/ThemeOffice/categories.png', 0, '', 1);
INSERT INTO `#__components` VALUES (14, 'User', 'option=com_user', 0, 0, '', '', 'com_user', 0, '', 1, '', 1);
INSERT INTO `#__components` VALUES (15, 'Search', 'option=com_search', 0, 0, 'option=com_search', 'Search Statistics', 'com_search', 0, 'js/ThemeOffice/component.png', 1, 'enabled=0\n\n', 1);
INSERT INTO `#__components` VALUES (16, 'Categories', '', 0, 1, 'option=com_categories&section=com_banner', 'Categories', '', 3, '', 1, '', 1);
INSERT INTO `#__components` VALUES (17, 'Wrapper', 'option=com_wrapper', 0, 0, '', 'Wrapper', 'com_wrapper', 0, '', 1, '', 1);
INSERT INTO `#__components` VALUES (18, 'Mail To', '', 0, 0, '', '', 'com_mailto', 0, '', 1, '', 1);
INSERT INTO `#__components` VALUES (19, 'Media Manager', '', 0, 0, 'option=com_media', 'Media Manager', 'com_media', 0, '', 1, 'upload_extensions=bmp,csv,doc,epg,gif,ico,jpg,odg,odp,ods,odt,pdf,png,ppt,swf,txt,xcf,xls,BMP,CSV,DOC,EPG,GIF,ICO,JPG,ODG,ODP,ODS,ODT,PDF,PNG,PPT,SWF,TXT,XCF,XLS\nupload_maxsize=10000000\nfile_path=images\nimage_path=images/stories\nrestrict_uploads=1\ncheck_mime=1\nimage_extensions=bmp,gif,jpg,png\nignore_extensions=\nupload_mime=image/jpeg,image/gif,image/png,image/bmp,application/x-shockwave-flash,application/msword,application/excel,application/pdf,application/powerpoint,text/plain,application/x-zip\nupload_mime_illegal=text/html', 1);
INSERT INTO `#__components` VALUES (20, 'Articles', 'option=com_content', 0, 0, '', '', 'com_content', 0, '', 1, 'show_noauth=0\nshow_title=1\nlink_titles=0\nshow_intro=1\nshow_section=0\nlink_section=0\nshow_category=0\nlink_category=0\nshow_author=1\nshow_create_date=1\nshow_modify_date=1\nshow_item_navigation=0\nshow_readmore=1\nshow_vote=0\nshow_icons=1\nshow_pdf_icon=1\nshow_print_icon=1\nshow_email_icon=1\nshow_hits=1\nfeed_summary=0\n\n', 1);
INSERT INTO `#__components` VALUES (21, 'Configuration Manager', '', 0, 0, '', 'Configuration', 'com_config', 0, '', 1, '', 1);
INSERT INTO `#__components` VALUES (22, 'Installation Manager', '', 0, 0, '', 'Installer', 'com_installer', 0, '', 1, '', 1);
INSERT INTO `#__components` VALUES (23, 'Language Manager', '', 0, 0, '', 'Languages', 'com_languages', 0, '', 1, '', 1);
INSERT INTO `#__components` VALUES (24, 'Mass mail', '', 0, 0, '', 'Mass Mail', 'com_massmail', 0, '', 1, 'mailSubjectPrefix=\nmailBodySuffix=\n\n', 1);
INSERT INTO `#__components` VALUES (25, 'Menu Editor', '', 0, 0, '', 'Menu Editor', 'com_menus', 0, '', 1, '', 1);
INSERT INTO `#__components` VALUES (27, 'Messaging', '', 0, 0, '', 'Messages', 'com_messages', 0, '', 1, '', 1);
INSERT INTO `#__components` VALUES (28, 'Modules Manager', '', 0, 0, '', 'Modules', 'com_modules', 0, '', 1, '', 1);
INSERT INTO `#__components` VALUES (29, 'Plugin Manager', '', 0, 0, '', 'Plugins', 'com_plugins', 0, '', 1, '', 1);
INSERT INTO `#__components` VALUES (30, 'Template Manager', '', 0, 0, '', 'Templates', 'com_templates', 0, '', 1, '', 1);
INSERT INTO `#__components` VALUES (31, 'User Manager', '', 0, 0, '', 'Users', 'com_users', 0, '', 1, 'allowUserRegistration=1\nnew_usertype=Registered\nuseractivation=1\nfrontend_userparams=1\n\n', 1);
INSERT INTO `#__components` VALUES (32, 'Cache Manager', '', 0, 0, '', 'Cache', 'com_cache', 0, '', 1, '', 1);
INSERT INTO `#__components` VALUES (33, 'Control Panel', '', 0, 0, '', 'Control Panel', 'com_cpanel', 0, '', 1, '', 1);
INSERT INTO `#__components` VALUES (34,'Manage Points','option=com_userpoints',0,0,'option=com_userpoints','Manage Points','com_userpoints',0,'js/ThemeOffice/component.png',0,'',1);
INSERT INTO `#__components` VALUES (35,'Summary','',0,34,'option=com_userpoints&task=summary','Summary','com_userpoints',0,'js/ThemeOffice/component.png',0,'',1);
INSERT INTO `#__components` VALUES (36,'Answers','option=com_answers',0,0,'option=com_answers','Answers','com_answers',0,'js/ThemeOffice/component.png',0,'',1);
INSERT INTO `#__components` VALUES (37,'Contribute','option=com_contribute',0,0,'','','com_contribute',0,'js/ThemeOffice/component.png',0,'',1);
INSERT INTO `#__components` VALUES (38,'Events','option=com_events',0,0,'option=com_events','Events','com_events',0,'js/ThemeOffice/component.png',0,'',1);
INSERT INTO `#__components` VALUES (39,'Manage Events','',0,38,'option=com_events','Manage Events','com_events',0,'js/ThemeOffice/component.png',0,'',1);
INSERT INTO `#__components` VALUES (40,'Manage Events Categories','',0,38,'option=com_events&task=cats','Manage Events Categories','com_events',1,'js/ThemeOffice/component.png',0,'',1);
INSERT INTO `#__components` VALUES (41,'Edit Config','',0,38,'option=com_events&task=configure','Edit Config','com_events',2,'js/ThemeOffice/component.png',0,'',1);
INSERT INTO `#__components` VALUES (42,'Groups','option=com_groups',0,0,'option=com_groups','Groups','com_groups',0,'js/ThemeOffice/component.png',0,'uploadpath=/site/groups\niconpath=/components/com_groups/images/icons\n\n',1);
INSERT INTO `#__components` VALUES (43,'Topics','option=com_topics',0,0,'','','com_topics',0,'js/ThemeOffice/component.png',0,'',1);
INSERT INTO `#__components` VALUES (44,'My Hub','option=com_myhub',0,0,'option=com_myhub','My Hub','com_myhub',0,'js/ThemeOffice/component.png',0,'allow_customization=0\nposition=myhub\ndefaults=\nstatic=\n\n',1);
INSERT INTO `#__components` VALUES (45,'Usage','option=com_usage',0,0,'option=com_usage','Usage','com_usage',0,'js/ThemeOffice/component.png',0,'statsDBDriver=mysql\nstatsDBHost=localhost\nstatsDBPort=\nstatsDBUsername=\nstatsDBPassword=\nstatsDBDatabase=\nstatsDBPrefix=\nmapsApiKey=ABQIAAAAPq8QOefNUw20Lc6RX2gKqhS52sOfLYQBjNjTIio4_8VA5UX1FxTHIAC1ueUqmMdrjD5WAr8YGW_jVQ\nstats_path=/site/usage\nmaps_path=/site/usage/maps\nplots_path=/site/usage/plots\ncharts_path=/site/usage/charts\n\n',1);
INSERT INTO `#__components` VALUES (46,'Citations','option=com_citations',0,0,'','','com_citations',0,'js/ThemeOffice/component.png',0,'',1);
INSERT INTO `#__components` VALUES (47,'Citations Manager','',0,46,'option=com_citations','Citations Manager','com_citations',0,'js/ThemeOffice/component.png',0,'',1);
INSERT INTO `#__components` VALUES (48,'Feedback','option=com_feedback',0,0,'option=com_feedback','Feedback','com_feedback',0,'js/ThemeOffice/component.png',0,'defaultpic=/components/com_feedback/images/contributor.gif\nuploadpath=/site/quotes\nmaxAllowed=40000000\nfile_ext=jpg,jpeg,jpe,bmp,tif,tiff,png,gif\nblacklist=\nbadwords=viagra, pharmacy, xanax, phentermine, dating, ringtones, tramadol, hydrocodone, levitra, ambien, vicodin, fioricet, diazepam, cash advance, free online, online gambling, online prescriptions, debt consolidation, baccarat, loan, slots, credit, mortgage, casino, slot, texas holdem, teen nude, orgasm, gay, fuck, crap, shit, asshole, cunt, fucker, fuckers, motherfucker, fucking, milf, cocksucker, porno, videosex, sperm, hentai, internet gambling, kasino, kasinos, poker, lottery, texas hold em, texas holdem, fisting\n\n',1);
INSERT INTO `#__components` VALUES (49,'Manage Success Stories','',0,48,'option=com_feedback','Manage Success Stories','com_feedback',0,'js/ThemeOffice/component.png',0,'',1);
INSERT INTO `#__components` VALUES (50,'Hub','option=com_hub',0,0,'option=com_hub','Hub','com_hub',0,'js/ThemeOffice/component.png',0,'',1);
INSERT INTO `#__components` VALUES (51,'Site','',0,50,'option=com_hub&task=site','Site','com_hub',0,'js/ThemeOffice/component.png',0,'',1);
INSERT INTO `#__components` VALUES (52,'Registration','',0,50,'option=com_hub&task=registration','Registration','com_hub',1,'js/ThemeOffice/component.png',0,'',1);
INSERT INTO `#__components` VALUES (53,'Databases','',0,50,'option=com_hub&task=databases','Databases','com_hub',2,'js/ThemeOffice/component.png',0,'',1);
INSERT INTO `#__components` VALUES (54,'Misc. Settings','',0,50,'option=com_hub&task=misc','Misc. Settings','com_hub',3,'js/ThemeOffice/component.png',0,'',1);
INSERT INTO `#__components` VALUES (55,'Components','',0,50,'option=com_hub&task=components','Components','com_hub',4,'js/ThemeOffice/component.png',0,'',1);
INSERT INTO `#__components` VALUES (56,'Middleware','option=com_mw',0,0,'option=com_mw','Middleware','com_mw',0,'js/ThemeOffice/component.png',0,'mw_on=0\nmwDBDriver=mysql\nmwDBHost=localhost\nmwDBPort=\nmwDBUsername=\nmwDBPassword=\nmwDBDatabase=\nmwDBPrefix=\nstoragehost=\nshow_storage=0\n\n',1);
INSERT INTO `#__components` VALUES (57,'Support','option=com_support',0,0,'option=com_support','Support','com_support',0,'js/ThemeOffice/component.png',0,'feed_summary=0\nseverities=critical,major,normal,minor,trivial\nwebpath=/site/tickets\nmaxAllowed=40000000\nfile_ext=jpg,jpeg,jpe,bmp,tif,tiff,png,gif,pdf,zip,mpg,mpeg,avi,mov,wmv,asf,asx,ra,rm,txt,rtf,doc,xsl,html,js,wav,mp3,eps,ppt,pps,swf,tar,tex,gz\ngroup=\n\n',1);
INSERT INTO `#__components` VALUES (58,'Categories','',0,57,'option=com_support&task=categories','Categories','com_support',0,'js/ThemeOffice/component.png',0,'',1);
INSERT INTO `#__components` VALUES (59,'Messages','',0,57,'option=com_support&task=messages','Messages','com_support',1,'js/ThemeOffice/component.png',0,'',1);
INSERT INTO `#__components` VALUES (60,'Resolutions','',0,57,'option=com_support&task=resolutions','Resolutions','com_support',2,'js/ThemeOffice/component.png',0,'',1);
INSERT INTO `#__components` VALUES (61,'Sections','',0,57,'option=com_support&task=sections','Sections','com_support',3,'js/ThemeOffice/component.png',0,'',1);
INSERT INTO `#__components` VALUES (62,'Tickets','',0,57,'option=com_support&task=tickets','Tickets','com_support',4,'js/ThemeOffice/component.png',0,'',1);
INSERT INTO `#__components` VALUES (63,'WhatsNew','option=com_whatsnew',0,0,'','','com_whatsnew',0,'js/ThemeOffice/component.png',0,'',1);
INSERT INTO `#__components` VALUES (64,'XPoll','option=com_xpoll',0,0,'option=com_xpoll','XPoll','com_xpoll',0,'js/ThemeOffice/component.png',0,'',1);
INSERT INTO `#__components` VALUES (65,'Contribtool','option=com_contribtool',0,0,'option=com_contribtool','Contribtool','com_contribtool',0,'js/ThemeOffice/component.png',0,'contribtool_on=0\nadmingroup=apps\ndefault_mw=narwhal\ndefault_vnc=780x600\ndeveloper_url=https://\ndeveloper_site=Forge\ndeveloper_email=\nproject_path=/tools/\ninvokescript_dir=/apps/\nadminscript_dir=/opt/hubzero/contribtool\ndev_suffix=_dev\ngroup_prefix=app-\ndemo_url=\ndoi_service=\nldap_save=1\nldap_read=0\nusedoi=0\nexec_pu=1\nscreenshot_edit=1\n\n',1);
INSERT INTO `#__components` VALUES (66,'Knowledgebase','option=com_kb',0,0,'option=com_kb','Knowledgebase','com_kb',0,'js/ThemeOffice/component.png',0,'',1);
INSERT INTO `#__components` VALUES (67,'Resources','option=com_resources',0,0,'option=com_resources','Resources','com_resources',0,'js/ThemeOffice/component.png',0,'autoapprove=1\nautoapproved_users=\ncc_license=1\nemail_when_approved=0\ndefaultpic=/components/com_resources/images/resource_thumb.gif\ntagstool=screenshots,poweredby,bio,credits,citations,sponsoredby,references,publications\ntagsothr=bio,credits,citations,sponsoredby,references,publications\naccesses=Public,Registered,Special,Protected,Private\nwebpath=/site/resources\ntoolpath=/site/resources/tools\nuploadpath=/site/resources\nmaxAllowed=40000000\nfile_ext=jpg,jpeg,jpe,bmp,tif,tiff,png,gif,pdf,zip,mpg,mpeg,avi,mov,wmv,asf,asx,ra,rm,txt,rtf,doc,xsl,html,js,wav,mp3,eps,ppt,pps,swf,tar,tex,gz\ndoi=\naboutdoi=\nsupportedtag=\nsupportedlink=\nbrowsetags=on\nshow_authors=1\nshow_assocs=1\nshow_ranking=1\nshow_rating=1\nshow_date=3\nshow_metadata=1\nshow_citation=1\n\n',1);
INSERT INTO `#__components` VALUES (68,'Types','',0,67,'option=com_resources&task=viewtypes','Types','com_resources',0,'js/ThemeOffice/component.png',0,'',1);
INSERT INTO `#__components` VALUES (69,'Orphans','',0,67,'option=com_resources&task=orphans','Orphans','com_resources',1,'js/ThemeOffice/component.png',0,'',1);
INSERT INTO `#__components` VALUES (70,'Resources','',0,67,'option=com_resources&task=browse','Resources','com_resources',2,'js/ThemeOffice/component.png',0,'',1);
INSERT INTO `#__components` VALUES (71,'Tags','option=com_tags',0,0,'option=com_tags','Tags','com_tags',0,'js/ThemeOffice/component.png',0,'',1);
INSERT INTO `#__components` VALUES (72,'New Tag','',0,71,'option=com_tags&task=new','New Tag','com_tags',0,'js/ThemeOffice/component.png',0,'',1);
INSERT INTO `#__components` VALUES (73,'Whois','option=com_whois',0,0,'','','com_whois',0,'js/ThemeOffice/component.png',0,'',1);
INSERT INTO `#__components` VALUES (74,'XSearch','option=com_xsearch',0,0,'','','com_xsearch',0,'js/ThemeOffice/component.png',0,'',1);
INSERT INTO `#__components` VALUES (75,'Tools','option=com_tools',0,0,'','','com_tools',0,'js/ThemeOffice/component.png',0,'',1);
INSERT INTO `#__components` VALUES (76,'Members','option=com_members',0,0,'option=com_members','Members','com_members',0,'js/ThemeOffice/component.png',0,'ldapProfileMirror=0,defaultpic=/components/com_members/images/profile.gif\nwebpath=/site/members\nmaxAllowed=40000000\nfile_ext=jpg,jpeg,jpe,bmp,tif,tiff,png,gif\nprivacy=0\naccess_org=0\naccess_orgtype=0\naccess_email=2\naccess_url=0\naccess_phone=2\naccess_tags=0\naccess_bio=0\naccess_countryorigin=0\naccess_countryresident=0\naccess_gender=0\naccess_race=2\naccess_hispanic=2\naccess_disability=2\naccess_optin=2\n\n',1);
INSERT INTO `#__components` VALUES (77,'XFlash','option=com_xflash',0,0,'option=com_xflash','XFlash','com_xflash',0,'js/ThemeOffice/component.png',0,'num_featured=3\nuploadpath=/site/xflash\nmaxAllowed=40000000\nfile_ext=jpg,png,gif\niconpath=templates/azure/images/icons/16x16\n\n',1);
INSERT INTO `#__components` VALUES (78,'Store','option=com_store',0,0,'option=com_store','Store','com_store',0,'js/ThemeOffice/component.png',0,'store_enabled=0\nwebpath=/site/store\nhubaddress_ln1=\nhubaddress_ln2=\nhubaddress_ln3=\nhubaddress_ln4=\nhubaddress_ln5=\nhubemail=\nhubphone=\nheadertext_ln1=\nheadertext_ln2=\nfootertext=\nreceipt_title=Your Order at HUB Store\nreceipt_note=Thank You for contributing to our HUB!\n\n',1);
INSERT INTO `#__components` VALUES (79,'404 SEF','option=com_sef',0,0,'option=com_sef','404 SEF','com_sef',0,'js/ThemeOffice/component.png',0,'enabled=1\n\n',1);
INSERT INTO `#__components` VALUES (80,'Wishlists','option=com_wishlist',0,0,'option=com_wishlist','Wishlists','com_wishlist',0,'js/ThemeOffice/component.png',0,'categories=general, resource, group, user\ngroup=hubdev\nbanking=0\n\n',1);
INSERT INTO `#__components` VALUES (81,'Features','option=com_features',0,0,'','','com_features',0,'js/ThemeOffice/component.png',0,'',1);

# --------------------------------------------------------

#
# Table structure for table `#__contact_details`
#

CREATE TABLE `#__contact_details` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(255) NOT NULL default '',
  `alias` varchar(255) NOT NULL default '',
  `con_position` varchar(255) default NULL,
  `address` text,
  `suburb` varchar(100) default NULL,
  `state` varchar(100) default NULL,
  `country` varchar(100) default NULL,
  `postcode` varchar(100) default NULL,
  `telephone` varchar(255) default NULL,
  `fax` varchar(255) default NULL,
  `misc` mediumtext,
  `image` varchar(255) default NULL,
  `imagepos` varchar(20) default NULL,
  `email_to` varchar(255) default NULL,
  `default_con` tinyint(1) unsigned NOT NULL default '0',
  `published` tinyint(1) unsigned NOT NULL default '0',
  `checked_out` int(11) unsigned NOT NULL default '0',
  `checked_out_time` datetime NOT NULL default '0000-00-00 00:00:00',
  `ordering` int(11) NOT NULL default '0',
  `params` text NOT NULL,
  `user_id` int(11) NOT NULL default '0',
  `catid` int(11) NOT NULL default '0',
  `access` tinyint(3) unsigned NOT NULL default '0',
  `mobile` varchar(255) NOT NULL default '',
  `webpage` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`id`),
  KEY `catid` (`catid`)
) TYPE=MyISAM CHARACTER SET `utf8`;

# --------------------------------------------------------

#
# Table structure for table `#__content`
#

CREATE TABLE `#__content` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `title` varchar(255) NOT NULL default '',
  `alias` varchar(255) NOT NULL default '',
  `title_alias` varchar(255) NOT NULL default '',
  `introtext` mediumtext NOT NULL,
  `fulltext` mediumtext NOT NULL,
  `state` tinyint(3) NOT NULL default '0',
  `sectionid` int(11) unsigned NOT NULL default '0',
  `mask` int(11) unsigned NOT NULL default '0',
  `catid` int(11) unsigned NOT NULL default '0',
  `created` datetime NOT NULL default '0000-00-00 00:00:00',
  `created_by` int(11) unsigned NOT NULL default '0',
  `created_by_alias` varchar(255) NOT NULL default '',
  `modified` datetime NOT NULL default '0000-00-00 00:00:00',
  `modified_by` int(11) unsigned NOT NULL default '0',
  `checked_out` int(11) unsigned NOT NULL default '0',
  `checked_out_time` datetime NOT NULL default '0000-00-00 00:00:00',
  `publish_up` datetime NOT NULL default '0000-00-00 00:00:00',
  `publish_down` datetime NOT NULL default '0000-00-00 00:00:00',
  `images` text NOT NULL,
  `urls` text NOT NULL,
  `attribs` text NOT NULL,
  `version` int(11) unsigned NOT NULL default '1',
  `parentid` int(11) unsigned NOT NULL default '0',
  `ordering` int(11) NOT NULL default '0',
  `metakey` text NOT NULL,
  `metadesc` text NOT NULL,
  `access` int(11) unsigned NOT NULL default '0',
  `hits` int(11) unsigned NOT NULL default '0',
  `metadata` TEXT NOT NULL DEFAULT '',
  PRIMARY KEY  (`id`),
  KEY `idx_section` (`sectionid`),
  KEY `idx_access` (`access`),
  KEY `idx_checkout` (`checked_out`),
  KEY `idx_state` (`state`),
  KEY `idx_catid` (`catid`),
  KEY `idx_createdby` (`created_by`)
) TYPE=MyISAM CHARACTER SET `utf8`;

# --------------------------------------------------------

#
# Table structure for table `#__content_frontpage`
#

CREATE TABLE `#__content_frontpage` (
  `content_id` int(11) NOT NULL default '0',
  `ordering` int(11) NOT NULL default '0',
  PRIMARY KEY  (`content_id`)
) TYPE=MyISAM CHARACTER SET `utf8`;

# --------------------------------------------------------

#
# Table structure for table `#__content_rating`
#

CREATE TABLE `#__content_rating` (
  `content_id` int(11) NOT NULL default '0',
  `rating_sum` int(11) unsigned NOT NULL default '0',
  `rating_count` int(11) unsigned NOT NULL default '0',
  `lastip` varchar(50) NOT NULL default '',
  PRIMARY KEY  (`content_id`)
) TYPE=MyISAM CHARACTER SET `utf8`;

# --------------------------------------------------------

# Table structure for table `#__core_log_items`

CREATE TABLE `#__core_log_items` (
  `time_stamp` date NOT NULL default '0000-00-00',
  `item_table` varchar(50) NOT NULL default '',
  `item_id` int(11) unsigned NOT NULL default '0',
  `hits` int(11) unsigned NOT NULL default '0'
) TYPE=MyISAM CHARACTER SET `utf8`;

# --------------------------------------------------------

# Table structure for table `#__core_log_searches`

CREATE TABLE `#__core_log_searches` (
  `search_term` varchar(128) NOT NULL default '',
  `hits` int(11) unsigned NOT NULL default '0'
) TYPE=MyISAM CHARACTER SET `utf8`;

#
# Table structure for table `#__groups`
#

# --------------------------------------------------------

CREATE TABLE `#__groups` (
  `id` tinyint(3) unsigned NOT NULL default '0',
  `name` varchar(50) NOT NULL default '',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM CHARACTER SET `utf8`;

#
# Dumping data for table `#__groups`
#

INSERT INTO `#__groups` VALUES (0, 'Public');
INSERT INTO `#__groups` VALUES (1, 'Registered');
INSERT INTO `#__groups` VALUES (2, 'Special');

# --------------------------------------------------------

#
# Table structure for table `#__plugins`
#

CREATE TABLE `#__plugins` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(100) NOT NULL default '',
  `element` varchar(100) NOT NULL default '',
  `folder` varchar(100) NOT NULL default '',
  `access` tinyint(3) unsigned NOT NULL default '0',
  `ordering` int(11) NOT NULL default '0',
  `published` tinyint(3) NOT NULL default '0',
  `iscore` tinyint(3) NOT NULL default '0',
  `client_id` tinyint(3) NOT NULL default '0',
  `checked_out` int(11) unsigned NOT NULL default '0',
  `checked_out_time` datetime NOT NULL default '0000-00-00 00:00:00',
  `params` text NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `idx_folder` (`published`,`client_id`,`access`,`folder`)
) TYPE=MyISAM CHARACTER SET `utf8`;

INSERT INTO `#__plugins` VALUES (1, 'Authentication - Joomla', 'joomla', 'authentication', 0, 1, 1, 1, 0, 0, '0000-00-00 00:00:00', '');
INSERT INTO `#__plugins` VALUES (2, 'Authentication - LDAP', 'ldap', 'authentication', 0, 2, 0, 1, 0, 0, '0000-00-00 00:00:00', 'host=\nport=389\nuse_ldapV3=0\nnegotiate_tls=0\nno_referrals=0\nauth_method=bind\nbase_dn=\nsearch_string=\nusers_dn=\nusername=\npassword=\nldap_fullname=fullName\nldap_email=mail\nldap_uid=uid\n\n');
INSERT INTO `#__plugins` VALUES (3, 'Authentication - GMail', 'gmail', 'authentication', 0, 4, 0, 0, 0, 0, '0000-00-00 00:00:00', '');
INSERT INTO `#__plugins` VALUES (4, 'Authentication - OpenID', 'openid', 'authentication', 0, 3, 0, 0, 0, 0, '0000-00-00 00:00:00', '');
INSERT INTO `#__plugins` VALUES (5, 'User - Joomla!', 'joomla', 'user', 0, 0, 1, 0, 0, 0, '0000-00-00 00:00:00', 'autoregister=1\n\n');
INSERT INTO `#__plugins` VALUES (6, 'Search - Content','content','search',0,1,1,1,0,0,'0000-00-00 00:00:00','search_limit=50\nsearch_content=1\nsearch_uncategorised=1\nsearch_archived=1\n\n');
INSERT INTO `#__plugins` VALUES (7, 'Search - Contacts','contacts','search',0,3,1,1,0,0,'0000-00-00 00:00:00','search_limit=50\n\n');
INSERT INTO `#__plugins` VALUES (8, 'Search - Categories', 'categories', 'search', 0, 4, 1, 0, 0, 0, '0000-00-00 00:00:00', 'search_limit=50\n\n');
INSERT INTO `#__plugins` VALUES (9, 'Search - Sections', 'sections', 'search', 0, 5, 1, 0, 0, 0, '0000-00-00 00:00:00', 'search_limit=50\n\n');
INSERT INTO `#__plugins` VALUES (10, 'Search - Newsfeeds', 'newsfeeds', 'search', 0, 6, 1, 0, 0, 0, '0000-00-00 00:00:00', 'search_limit=50\n\n');
INSERT INTO `#__plugins` VALUES (11, 'Search - Weblinks','weblinks','search',0,2,1,1,0,0,'0000-00-00 00:00:00','search_limit=50\n\n');
INSERT INTO `#__plugins` VALUES (12, 'Content - Pagebreak','pagebreak','content',0,10000,1,1,0,0,'0000-00-00 00:00:00','enabled=1\ntitle=1\nmultipage_toc=1\nshowall=1\n\n');
INSERT INTO `#__plugins` VALUES (13, 'Content - Rating','vote','content',0,4,1,1,0,0,'0000-00-00 00:00:00','');
INSERT INTO `#__plugins` VALUES (14, 'Content - Email Cloaking', 'emailcloak', 'content', 0, 5, 1, 0, 0, 0, '0000-00-00 00:00:00', 'mode=1\n\n');
INSERT INTO `#__plugins` VALUES (15, 'Content - Code Hightlighter (GeSHi)', 'geshi', 'content', 0, 5, 0, 0, 0, 0, '0000-00-00 00:00:00', '');
INSERT INTO `#__plugins` VALUES (16, 'Content - Load Module', 'loadmodule', 'content', 0, 6, 1, 0, 0, 0, '0000-00-00 00:00:00', 'enabled=1\nstyle=0\n\n');
INSERT INTO `#__plugins` VALUES (17, 'Content - Page Navigation','pagenavigation','content',0,2,1,1,0,0,'0000-00-00 00:00:00','position=1\n\n');
INSERT INTO `#__plugins` VALUES (18, 'Editor - No Editor','none','editors',0,0,1,1,0,0,'0000-00-00 00:00:00','');
INSERT INTO `#__plugins` VALUES (19, 'Editor - TinyMCE', 'tinymce', 'editors', 0, 0, 1, 1, 0, 0, '0000-00-00 00:00:00', 'theme=advanced\ncleanup=1\ncleanup_startup=0\nautosave=0\ncompressed=0\nrelative_urls=1\ntext_direction=ltr\nlang_mode=0\nlang_code=en\ninvalid_elements=applet\ncontent_css=1\ncontent_css_custom=\nnewlines=0\ntoolbar=top\nhr=1\nsmilies=1\ntable=1\nstyle=1\nlayer=1\nxhtmlxtras=0\ntemplate=0\ndirectionality=1\nfullscreen=1\nhtml_height=550\nhtml_width=750\npreview=1\ninsertdate=1\nformat_date=%Y-%m-%d\ninserttime=1\nformat_time=%H:%M:%S\n\n');
INSERT INTO `#__plugins` VALUES (20, 'Editor - XStandard Lite 2.0', 'xstandard', 'editors', 0, 0, 0, 1, 0, 0, '0000-00-00 00:00:00', '');
INSERT INTO `#__plugins` VALUES (21, 'Editor Button - Image','image','editors-xtd',0,0,1,0,0,0,'0000-00-00 00:00:00','');
INSERT INTO `#__plugins` VALUES (22, 'Editor Button - Pagebreak','pagebreak','editors-xtd',0,0,1,0,0,0,'0000-00-00 00:00:00','');
INSERT INTO `#__plugins` VALUES (23, 'Editor Button - Readmore','readmore','editors-xtd',0,0,1,0,0,0,'0000-00-00 00:00:00','');
INSERT INTO `#__plugins` VALUES (24, 'XML-RPC - Joomla', 'joomla', 'xmlrpc', 0, 7, 0, 1, 0, 0, '0000-00-00 00:00:00', '');
INSERT INTO `#__plugins` VALUES (25, 'XML-RPC - Blogger API', 'blogger', 'xmlrpc', 0, 7, 0, 1, 0, 0, '0000-00-00 00:00:00', 'catid=1\nsectionid=0\n\n');
#INSERT INTO `#__plugins` VALUES (26, 'XML-RPC - MetaWeblog API', 'metaweblog', 'xmlrpc', 0, 7, 0, 1, 0, 0, '0000-00-00 00:00:00', '');
INSERT INTO `#__plugins` VALUES (27, 'System - SEF','sef','system',0,1,1,0,0,0,'0000-00-00 00:00:00','');
INSERT INTO `#__plugins` VALUES (28, 'System - Debug', 'debug', 'system', 0, 2, 1, 0, 0, 0, '0000-00-00 00:00:00', 'queries=1\nmemory=1\nlangauge=1\n\n');
INSERT INTO `#__plugins` VALUES (29, 'System - Legacy', 'legacy', 'system', 0, 3, 0, 1, 0, 0, '0000-00-00 00:00:00', 'route=0\n\n');
INSERT INTO `#__plugins` VALUES (30, 'System - Cache', 'cache', 'system', 0, 4, 0, 1, 0, 0, '0000-00-00 00:00:00', 'browsercache=0\ncachetime=15\n\n');
INSERT INTO `#__plugins` VALUES (31, 'System - Log', 'log', 'system', 0, 5, 0, 1, 0, 0, '0000-00-00 00:00:00', '');
INSERT INTO `#__plugins` VALUES (32, 'System - Remember Me', 'remember', 'system', 0, 6, 1, 1, 0, 0, '0000-00-00 00:00:00', '');
INSERT INTO `#__plugins` VALUES (33, 'System - Backlink', 'backlink', 'system', 0, 7, 0, 1, 0, 0, '0000-00-00 00:00:00', '');
INSERT INTO `#__plugins` VALUES (34,'Authentication - xHUB','xauth','authentication',0,5,1,0,0,0,'0000-00-00 00:00:00','');
INSERT INTO `#__plugins` VALUES (35,'Content - xHubTags','xhubtags','content',0,7,1,0,0,0,'0000-00-00 00:00:00','');
INSERT INTO `#__plugins` VALUES (36,'Groups - Forum','forum','groups',0,3,1,0,0,0,'0000-00-00 00:00:00','');
INSERT INTO `#__plugins` VALUES (37,'Groups - Resources','resources','groups',0,2,1,0,0,0,'0000-00-00 00:00:00','');
INSERT INTO `#__plugins` VALUES (38,'Groups - Members','members','groups',0,0,1,0,0,0,'0000-00-00 00:00:00','');
INSERT INTO `#__plugins` VALUES (39,'Groups - Wiki','wiki','groups',0,1,1,0,0,0,'0000-00-00 00:00:00','');
INSERT INTO `#__plugins` VALUES (40,'Members - Messages','messages','members',0,7,1,0,0,0,'0000-00-00 00:00:00','');
INSERT INTO `#__plugins` VALUES (41,'Members - Usage','usage','members',0,5,1,0,0,0,'0000-00-00 00:00:00','');
INSERT INTO `#__plugins` VALUES (42,'Members - Contributions - Topics','topics','members',0,4,1,0,0,0,'0000-00-00 00:00:00','');
INSERT INTO `#__plugins` VALUES (43,'Members - Contributions - Resources','resources','members',0,3,1,0,0,0,'0000-00-00 00:00:00','');
INSERT INTO `#__plugins` VALUES (44,'Members - Groups','groups','members',0,1,1,0,0,0,'0000-00-00 00:00:00','');
INSERT INTO `#__plugins` VALUES (45,'Members - Favorites','favorites','members',0,6,1,0,0,0,'0000-00-00 00:00:00','');
INSERT INTO `#__plugins` VALUES (46,'Members - Points','points','members',0,2,1,0,0,0,'0000-00-00 00:00:00','');
INSERT INTO `#__plugins` VALUES (47,'Members - Contributions','contributions','members',0,0,1,0,0,0,'0000-00-00 00:00:00','');
INSERT INTO `#__plugins` VALUES (48,'Middleware - About','resource','mw',0,1,1,0,0,0,'0000-00-00 00:00:00','');
INSERT INTO `#__plugins` VALUES (49,'Middleware - Questions','questions','mw',0,0,1,0,0,0,'0000-00-00 00:00:00','');
INSERT INTO `#__plugins` VALUES (50,'Tags - Groups','groups','tags',0,6,1,0,0,0,'0000-00-00 00:00:00','');
INSERT INTO `#__plugins` VALUES (51,'Tags - Support','support','tags',0,4,1,0,0,0,'0000-00-00 00:00:00','');
INSERT INTO `#__plugins` VALUES (52,'Tags - Topics','topics','tags',0,2,1,0,0,0,'0000-00-00 00:00:00','');
INSERT INTO `#__plugins` VALUES (53,'Tags - Answers','answers','tags',0,0,1,0,0,0,'0000-00-00 00:00:00','');
INSERT INTO `#__plugins` VALUES (54,'Tags - Events','events','tags',0,5,1,0,0,0,'0000-00-00 00:00:00','');
INSERT INTO `#__plugins` VALUES (55,'Tags - Members','members','tags',0,3,1,0,0,0,'0000-00-00 00:00:00','');
INSERT INTO `#__plugins` VALUES (56,'Tags - Resources','resources','tags',0,1,1,0,0,0,'0000-00-00 00:00:00','');
INSERT INTO `#__plugins` VALUES (57,'Resources - Share','share','resources',0,8,1,0,0,0,'0000-00-00 00:00:00','icons_limit=3\nshare_facebook=1\nshare_twitter=1\nshare_google=1\nshare_digg=1\nshare_technorati=1\nshare_delicious=1\nshare_reddit=0\nshare_email=0\nshare_print=0\n\n');
INSERT INTO `#__plugins` VALUES (58,'Resources - Favorite','favorite','resources',0,7,1,0,0,0,'0000-00-00 00:00:00','');
INSERT INTO `#__plugins` VALUES (59,'Resources - Versions','versions','resources',0,6,1,0,0,0,'0000-00-00 00:00:00','');
INSERT INTO `#__plugins` VALUES (60,'Resources - Reviews','reviews','resources',0,4,1,0,0,0,'0000-00-00 00:00:00','');
INSERT INTO `#__plugins` VALUES (61,'Resources - Questions','questions','resources',0,1,1,0,0,0,'0000-00-00 00:00:00','');
INSERT INTO `#__plugins` VALUES (62,'Resources - Wishlist','wishlist','resources',0,9,1,0,0,0,'0000-00-00 00:00:00','');
INSERT INTO `#__plugins` VALUES (63,'Resources - Usage','usage','resources',0,5,1,0,0,0,'0000-00-00 00:00:00','');
INSERT INTO `#__plugins` VALUES (64,'Resources - Related','related','resources',0,3,1,0,0,0,'0000-00-00 00:00:00','');
INSERT INTO `#__plugins` VALUES (65,'Resources - Recommendations','recommendations','resources',0,2,0,0,0,0,'0000-00-00 00:00:00','');
INSERT INTO `#__plugins` VALUES (66,'Resources - Citations','citations','resources',0,0,1,0,0,0,'0000-00-00 00:00:00','');
INSERT INTO `#__plugins` VALUES (67,'Support - Comments','comments','support',0,1,1,0,0,0,'0000-00-00 00:00:00','');
INSERT INTO `#__plugins` VALUES (68,'Support - Transfer','transfer','support',0,4,1,0,0,0,'0000-00-00 00:00:00','');
INSERT INTO `#__plugins` VALUES (69,'Support - Wishlist','wishlist','support',0,3,1,0,0,0,'0000-00-00 00:00:00','');
INSERT INTO `#__plugins` VALUES (70,'Support - Resources','resources','support',0,2,1,0,0,0,'0000-00-00 00:00:00','');
INSERT INTO `#__plugins` VALUES (71,'Support - Answers','answers','support',0,0,1,0,0,0,'0000-00-00 00:00:00','');
INSERT INTO `#__plugins` VALUES (72,'System - xHUB','xhub','system',0,8,1,0,0,0,'0000-00-00 00:00:00','');
INSERT INTO `#__plugins` VALUES (73,'System - xFeed','xfeed','system',0,9,1,0,0,0,'0000-00-00 00:00:00','');
INSERT INTO `#__plugins` VALUES (74,'Tag Editor - Auto complete','autocompleter','tageditor',0,0,1,0,0,0,'0000-00-00 00:00:00','');
INSERT INTO `#__plugins` VALUES (75,'Usage - Region','region','usage',0,7,1,0,0,0,'0000-00-00 00:00:00','');
INSERT INTO `#__plugins` VALUES (76,'Usage - Overview','overview','usage',0,6,1,0,0,0,'0000-00-00 00:00:00','');
INSERT INTO `#__plugins` VALUES (77,'Usage - Chart','chart','usage',0,4,1,0,0,0,'0000-00-00 00:00:00','');
INSERT INTO `#__plugins` VALUES (78,'Usage - Partners','partners','usage',0,2,1,0,0,0,'0000-00-00 00:00:00','');
INSERT INTO `#__plugins` VALUES (79,'Usage - Domain Class','domainclass','usage',0,0,1,0,0,0,'0000-00-00 00:00:00','');
INSERT INTO `#__plugins` VALUES (80,'Usage - Domains','domains','usage',0,5,1,0,0,0,'0000-00-00 00:00:00','');
INSERT INTO `#__plugins` VALUES (81,'Usage - Tools','tools','usage',0,3,1,0,0,0,'0000-00-00 00:00:00','');
INSERT INTO `#__plugins` VALUES (82,'Usage - Maps','maps','usage',0,1,1,0,0,0,'0000-00-00 00:00:00','');
INSERT INTO `#__plugins` VALUES (83,'User - xHUB','xusers','user',0,1,1,0,0,0,'0000-00-00 00:00:00','');
INSERT INTO `#__plugins` VALUES (84,'Whatsnew - Topics','topics','whatsnew',0,4,1,0,0,0,'0000-00-00 00:00:00','');
INSERT INTO `#__plugins` VALUES (85,'Whatsnew - Resources','resources','whatsnew',0,3,1,0,0,0,'0000-00-00 00:00:00','');
INSERT INTO `#__plugins` VALUES (86,'Whatsnew - Content','content','whatsnew',0,0,1,0,0,0,'0000-00-00 00:00:00','');
INSERT INTO `#__plugins` VALUES (87,'Whatsnew - Events','events','whatsnew',0,1,1,0,0,0,'0000-00-00 00:00:00','');
INSERT INTO `#__plugins` VALUES (88,'Whatsnew - Knowledge Base','kb','whatsnew',0,2,1,0,0,0,'0000-00-00 00:00:00','');
INSERT INTO `#__plugins` VALUES (89,'xHUB Authentication - Site','hzldap','xauthentication',0,0,1,0,0,0,'0000-00-00 00:00:00','');
INSERT INTO `#__plugins` VALUES (90,'xHUB - Libraries','xlibrary','xhub',0,0,1,0,0,0,'0000-00-00 00:00:00','');
INSERT INTO `#__plugins` VALUES (91,'XMessage - RSS','rss','xmessage',0,4,1,0,0,0,'0000-00-00 00:00:00','');
INSERT INTO `#__plugins` VALUES (92,'XMessage - Internal','internal','xmessage',0,5,1,0,0,0,'0000-00-00 00:00:00','');
INSERT INTO `#__plugins` VALUES (93,'XMessage - SMS TXT','smstxt','xmessage',0,3,1,0,0,0,'0000-00-00 00:00:00','');
INSERT INTO `#__plugins` VALUES (94,'XMessage - Instant Message','im','xmessage',0,2,1,0,0,0,'0000-00-00 00:00:00','');
INSERT INTO `#__plugins` VALUES (95,'XMessage - Handler','handler','xmessage',0,1,1,0,0,0,'0000-00-00 00:00:00','');
INSERT INTO `#__plugins` VALUES (96,'XMessage - Email','email','xmessage',0,0,1,0,0,0,'0000-00-00 00:00:00','');
INSERT INTO `#__plugins` VALUES (97,'XSearch - Groups','groups','xsearch',0,7,1,0,0,0,'0000-00-00 00:00:00','');
INSERT INTO `#__plugins` VALUES (98,'XSearch - Tags','tags','xsearch',0,4,1,0,0,0,'0000-00-00 00:00:00','');
INSERT INTO `#__plugins` VALUES (99,'XSearch - Resources','resources','xsearch',0,3,1,0,0,0,'0000-00-00 00:00:00','');
INSERT INTO `#__plugins` VALUES (100,'XSearch - Knowledge Base','kb','xsearch',0,2,1,0,0,0,'0000-00-00 00:00:00','');
INSERT INTO `#__plugins` VALUES (101,'XSearch - Events','events','xsearch',0,1,1,0,0,0,'0000-00-00 00:00:00','');
INSERT INTO `#__plugins` VALUES (102,'XSearch - Members','members','xsearch',0,6,1,0,0,0,'0000-00-00 00:00:00','');
INSERT INTO `#__plugins` VALUES (103,'XSearch - Topics','topics','xsearch',0,5,1,0,0,0,'0000-00-00 00:00:00','');
INSERT INTO `#__plugins` VALUES (104,'XSearch - Content','content','xsearch',0,0,1,0,0,0,'0000-00-00 00:00:00','');

# --------------------------------------------------------

#
# Table structure for table `#__menu`
#

CREATE TABLE `#__menu` (
  `id` int(11) NOT NULL auto_increment,
  `menutype` varchar(75) default NULL,
  `name` varchar(255) default NULL,
  `alias` varchar(255) NOT NULL default '',
  `link` text,
  `type` varchar(50) NOT NULL default '',
  `published` tinyint(1) NOT NULL default 0,
  `parent` int(11) unsigned NOT NULL default 0,
  `componentid` int(11) unsigned NOT NULL default 0,
  `sublevel` int(11) default 0,
  `ordering` int(11) default 0,
  `checked_out` int(11) unsigned NOT NULL default 0,
  `checked_out_time` datetime NOT NULL default '0000-00-00 00:00:00',
  `pollid` int(11) NOT NULL default 0,
  `browserNav` tinyint(4) default 0,
  `access` tinyint(3) unsigned NOT NULL default 0,
  `utaccess` tinyint(3) unsigned NOT NULL default 0,
  `params` text NOT NULL,
  `lft` int(11) unsigned NOT NULL default 0,
  `rgt` int(11) unsigned NOT NULL default 0,
  `home` INTEGER(1) UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY  (`id`),
  KEY `componentid` (`componentid`,`menutype`,`published`,`access`),
  KEY `menutype` (`menutype`)
) TYPE=MyISAM CHARACTER SET `utf8`;

INSERT INTO `#__menu` VALUES (1, 'mainmenu', 'Home', 'home', 'index.php?option=com_content&view=frontpage', 'component', 1, 0, 20, 0, 1, 0, '0000-00-00 00:00:00', 0, 0, 0, 3, 'num_leading_articles=1\nnum_intro_articles=4\nnum_columns=2\nnum_links=4\norderby_pri=\norderby_sec=front\nshow_pagination=2\nshow_pagination_results=1\nshow_feed_link=1\nshow_noauth=\nshow_title=\nlink_titles=\nshow_intro=\nshow_section=\nlink_section=\nshow_category=\nlink_category=\nshow_author=\nshow_create_date=\nshow_modify_date=\nshow_item_navigation=\nshow_readmore=\nshow_vote=\nshow_icons=\nshow_pdf_icon=\nshow_print_icon=\nshow_email_icon=\nshow_hits=\nfeed_summary=\npage_title=\nshow_page_title=1\npageclass_sfx=\nmenu_image=-1\nsecure=0\n\n', 0, 0, 1);

# --------------------------------------------------------

#
# Table structure for table `#__menu_types`
#

CREATE TABLE `#__menu_types` (
  `id` INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
  `menutype` VARCHAR(75) NOT NULL DEFAULT '',
  `title` VARCHAR(255) NOT NULL DEFAULT '',
  `description` VARCHAR(255) NOT NULL DEFAULT '',
  PRIMARY KEY(`id`),
  UNIQUE `menutype`(`menutype`)
) TYPE=MyISAM CHARACTER SET `utf8`;

INSERT INTO `#__menu_types` VALUES (1, 'mainmenu', 'Main Menu', 'The main menu for the site');

# --------------------------------------------------------

#
# Table structure for table `#__messages`
#

CREATE TABLE `#__messages` (
  `message_id` int(10) unsigned NOT NULL auto_increment,
  `user_id_from` int(10) unsigned NOT NULL default '0',
  `user_id_to` int(10) unsigned NOT NULL default '0',
  `folder_id` int(10) unsigned NOT NULL default '0',
  `date_time` datetime NOT NULL default '0000-00-00 00:00:00',
  `state` int(11) NOT NULL default '0',
  `priority` int(1) unsigned NOT NULL default '0',
  `subject` text NOT NULL default '',
  `message` text NOT NULL,
  PRIMARY KEY  (`message_id`),
  KEY `useridto_state` (`user_id_to`, `state`)
) TYPE=MyISAM CHARACTER SET `utf8`;
# --------------------------------------------------------

#
# Table structure for table `#__messages_cfg`
#

CREATE TABLE `#__messages_cfg` (
  `user_id` int(10) unsigned NOT NULL default '0',
  `cfg_name` varchar(100) NOT NULL default '',
  `cfg_value` varchar(255) NOT NULL default '',
  UNIQUE `idx_user_var_name` (`user_id`,`cfg_name`)
) TYPE=MyISAM CHARACTER SET `utf8`;
# --------------------------------------------------------

#
# Table structure for table `#__modules`
#

CREATE TABLE `#__modules` (
  `id` int(11) NOT NULL auto_increment,
  `title` text NOT NULL,
  `content` text NOT NULL,
  `ordering` int(11) NOT NULL default '0',
  `position` varchar(50) default NULL,
  `checked_out` int(11) unsigned NOT NULL default '0',
  `checked_out_time` datetime NOT NULL default '0000-00-00 00:00:00',
  `published` tinyint(1) NOT NULL default '0',
  `module` varchar(50) default NULL,
  `numnews` int(11) NOT NULL default '0',
  `access` tinyint(3) unsigned NOT NULL default '0',
  `showtitle` tinyint(3) unsigned NOT NULL default '1',
  `params` text NOT NULL,
  `iscore` tinyint(4) NOT NULL default '0',
  `client_id` tinyint(4) NOT NULL default '0',
  `control` TEXT NOT NULL DEFAULT '',
  PRIMARY KEY  (`id`),
  KEY `published` (`published`,`access`),
  KEY `newsfeeds` (`module`,`published`)
) TYPE=MyISAM CHARACTER SET `utf8`;

INSERT INTO `#__modules` VALUES (1, 'Main Menu', '', 1, 'left', 0, '0000-00-00 00:00:00', 1, 'mod_mainmenu', 0, 0, 1, 'menutype=mainmenu\nmoduleclass_sfx=_menu\n', 1, 0, '');
INSERT INTO `#__modules` VALUES (2, 'Login', '', 1, 'login', 0, '0000-00-00 00:00:00', 1, 'mod_login', 0, 0, 1, '', 1, 1, '');
INSERT INTO `#__modules` VALUES (3, 'Popular','',3,'cpanel',0,'0000-00-00 00:00:00',1,'mod_popular',0,2,1,'',0, 1, '');
INSERT INTO `#__modules` VALUES (4, 'Recent added Articles','',4,'cpanel',0,'0000-00-00 00:00:00',1,'mod_latest',0,2,1,'ordering=c_dsc\nuser_id=0\ncache=0\n\n',0, 1, '');
INSERT INTO `#__modules` VALUES (5, 'Menu Stats','',5,'cpanel',0,'0000-00-00 00:00:00',1,'mod_stats',0,2,1,'',0, 1, '');
INSERT INTO `#__modules` VALUES (6, 'Unread Messages','',1,'header',0,'0000-00-00 00:00:00',1,'mod_unread',0,2,1,'',1, 1, '');
INSERT INTO `#__modules` VALUES (7, 'Online Users','',2,'header',0,'0000-00-00 00:00:00',1,'mod_online',0,2,1,'',1, 1, '');
INSERT INTO `#__modules` VALUES (8, 'Toolbar','',1,'toolbar',0,'0000-00-00 00:00:00',1,'mod_toolbar',0,2,1,'',1, 1, '');
INSERT INTO `#__modules` VALUES (9, 'Quick Icons','',1,'icon',0,'0000-00-00 00:00:00',0,'mod_quickicon',0,2,1,'',1,1, '');
INSERT INTO `#__modules` VALUES (10, 'Logged in Users','',2,'cpanel',0,'0000-00-00 00:00:00',1,'mod_logged',0,2,1,'',0,1, '');
INSERT INTO `#__modules` VALUES (11, 'Footer', '', 0, 'footer', 0, '0000-00-00 00:00:00', 1, 'mod_footer', 0, 0, 1, '', 1, 1, '');
INSERT INTO `#__modules` VALUES (12, 'Admin Menu','', 1,'menu', 0,'0000-00-00 00:00:00', 1,'mod_menu', 0, 2, 1, '', 0, 1, '');
INSERT INTO `#__modules` VALUES (13, 'Admin SubMenu','', 1,'submenu', 0,'0000-00-00 00:00:00', 1,'mod_submenu', 0, 2, 1, '', 0, 1, '');
INSERT INTO `#__modules` VALUES (14, 'User Status','', 1,'status', 0,'0000-00-00 00:00:00', 1,'mod_status', 0, 2, 1, '', 0, 1, '');
INSERT INTO `#__modules` VALUES (15, 'Title','', 1,'title', 0,'0000-00-00 00:00:00', 1,'mod_title', 0, 2, 1, '', 0, 1, '');
INSERT INTO `#__modules` VALUES (16, 'My Groups','',4,'myhub',0,'0000-00-00 00:00:00',1,'mod_mygroups',0,0,0,'',0,0,'');
INSERT INTO `#__modules` VALUES (17, 'Browse Content by Tags','',0,'toptags',0,'0000-00-00 00:00:00',1,'mod_toptags',0,0,1,'numtags=25\nmessage=\nsortby=alphabeta\nmorelnk=0\ncache=0\ncache_time=900\n\n',0,0,'');
INSERT INTO `#__modules` VALUES (18, 'My Contributions','',3,'myhub',0,'0000-00-00 00:00:00',1,'mod_mycontributions',0,0,0,'',0,0,'');
INSERT INTO `#__modules` VALUES (19, 'Extended Statistics','',2,'userInfo',0,'0000-00-00 00:00:00',1,'mod_xstats',0,0,0,'counter=1\n',0,0,'');
INSERT INTO `#__modules` VALUES (20, 'Login Form','',1,'force_mod',0,'0000-00-00 00:00:00',1,'mod_xlogin',0,0,0,'',0,0,'');
INSERT INTO `#__modules` VALUES (21, 'HUB Resource Menu','',1,'introblock',0,'0000-00-00 00:00:00',0,'mod_resourcemenu',0,0,0,'',0,0,'');
INSERT INTO `#__modules` VALUES (22, 'Quotes','',1,'quotes',0,'0000-00-00 00:00:00',1,'mod_quotes',0,0,0,'',0,0,'');
INSERT INTO `#__modules` VALUES (23, 'Popular FAQs','',1,'tcleft',0,'0000-00-00 00:00:00',1,'mod_popularfaq',0,0,1,'limit=5\n',0,0,'');
INSERT INTO `#__modules` VALUES (24, 'Notices Module','',1,'notices',0,'0000-00-00 00:00:00',1,'mod_notices',0,0,0,'',0,0,'');
INSERT INTO `#__modules` VALUES (25, 'My Tools','',2,'myhub',0,'0000-00-00 00:00:00',1,'mod_mytools',0,0,0,'',0,0,'');
INSERT INTO `#__modules` VALUES (26, 'Search Module','',1,'search',0,'0000-00-00 00:00:00',1,'mod_xsearch',0,0,0,'',0,0,'');
INSERT INTO `#__modules` VALUES (27, 'XFlash','',1,'banner',0,'0000-00-00 00:00:00',0,'mod_xflash',0,0,0,'',0,0,'');
INSERT INTO `#__modules` VALUES (28, 'Trouble Report','',0,'helppane',0,'0000-00-00 00:00:00',1,'mod_reportproblems',0,0,0,'moduleclass_sfx=\ncache=0\n\n',0,0,'');
INSERT INTO `#__modules` VALUES (29, 'Quick Tips','',2,'right',0,'0000-00-00 00:00:00',0,'mod_quicktips',0,0,0,'',0,0,'');
INSERT INTO `#__modules` VALUES (30, 'XPoll Title','',1,'user1',0,'0000-00-00 00:00:00',1,'mod_polltitle',0,0,0,'',0,0,'');
INSERT INTO `#__modules` VALUES (31, 'My Tickets','',2,'',0,'0000-00-00 00:00:00',1,'mod_mytickets',0,0,1,'',0,0,'');
INSERT INTO `#__modules` VALUES (32, 'Extended Who is Online','',1,'userInfo',0,'0000-00-00 00:00:00',1,'mod_xwhosonline',0,0,0,'online=1\nusers=1',0,0,'');
INSERT INTO `#__modules` VALUES (33, 'XPoll','',1,'',0,'0000-00-00 00:00:00',1,'mod_xpoll',0,0,1,'',0,0,'');
INSERT INTO `#__modules` VALUES (34, 'What\'s New in Resources','',0,'frontLeft',0,'0000-00-00 00:00:00',1,'mod_whatsnew',0,0,1,'moduleid=\ncount=5\nfeed=0\nperiod=resources:month\ntagged=0\n\n',0,0,'');
INSERT INTO `#__modules` VALUES (35, 'Latest Questions','',1,'tcmiddle',0,'0000-00-00 00:00:00',1,'mod_recentquestions',0,0,1,'',0,0,'');
INSERT INTO `#__modules` VALUES (36, 'Popular Questions','',1,'q_popular',0,'0000-00-00 00:00:00',1,'mod_popularquestions',0,0,1,'',0,0,'');
INSERT INTO `#__modules` VALUES (37, 'My Sessions','',1,'myhub',0,'0000-00-00 00:00:00',1,'mod_mysessions',0,0,0,'',0,0,'');
INSERT INTO `#__modules` VALUES (38, 'Latest Events','',3,'left',0,'0000-00-00 00:00:00',0,'mod_events_latest',0,0,0,'moduleclass_sfx=\nstartday=0\nmax_events=10\nmode=2\nannouncements=0\ndays=365\ndisplay_links=0\ndisplay_year=0\ndisplay_date_style=0\ndisplay_title_style=0\nno_repeat=1\ncustom_format_str=<td class=\"event-date\"><span class=\"month\">${eventDate(M)}</span><span class=\"day\">${eventDate(d)}</span></td><td class=\"event-title\">${title}<br />${content}</td>\nchar_limit=150\ncache=0\ncache_time=900\n\n',0,0,'');
INSERT INTO `#__modules` VALUES (39, 'Events Calendar','',2,'left',0,'0000-00-00 00:00:00',0,'mod_events_cal',0,0,1,'',0,0,'');
INSERT INTO `#__modules` VALUES (40, 'Dashboard','',2,'icon',0,'0000-00-00 00:00:00',1,'mod_dashboard',0,0,0,'',0,1,'');
INSERT INTO `#__modules` VALUES (41, 'My Submissions','',5,'myhub',0,'0000-00-00 00:00:00',1,'mod_mysubmissions',0,0,0,'',0,0,'');
INSERT INTO `#__modules` VALUES (42, 'Featured Member','',3,'',0,'0000-00-00 00:00:00',1,'mod_featuredmember',0,0,1,'',0,0,'');
INSERT INTO `#__modules` VALUES (43, 'Featured Resource','',4,'',0,'0000-00-00 00:00:00',1,'mod_featuredresource',0,0,1,'',0,0,'');
INSERT INTO `#__modules` VALUES (44, 'Latest Usage','',5,'',0,'0000-00-00 00:00:00',1,'mod_latestusage',0,0,1,'',0,0,'');
INSERT INTO `#__modules` VALUES (45, 'Featured Question','',6,'',0,'0000-00-00 00:00:00',1,'mod_featuredquestion',0,0,1,'',0,0,'');
INSERT INTO `#__modules` VALUES (46, 'My Questions','',6,'myhub',0,'0000-00-00 00:00:00',1,'mod_myquestions',0,0,0,'',0,0,'');
INSERT INTO `#__modules` VALUES (47, 'My Wishes','',7,'myhub',0,'0000-00-00 00:00:00',1,'mod_mywishes',0,0,0,'',0,0,'');
INSERT INTO `#__modules` VALUES (48, 'My Messages','',8,'myhub',0,'0000-00-00 00:00:00',1,'mod_mymessages',0,0,0,'',0,0,'');

# --------------------------------------------------------

#
# Table structure for table `#__modules_menu`
#

CREATE TABLE `#__modules_menu` (
  `moduleid` int(11) NOT NULL default '0',
  `menuid` int(11) NOT NULL default '0',
  PRIMARY KEY  (`moduleid`,`menuid`)
) TYPE=MyISAM CHARACTER SET `utf8`;

#
# Dumping data for table `#__modules_menu`
#

INSERT INTO `#__modules_menu` VALUES (1,0);

# --------------------------------------------------------

#
# Table structure for table `#__newsfeeds`
#

CREATE TABLE `#__newsfeeds` (
  `catid` int(11) NOT NULL default '0',
  `id` int(11) NOT NULL auto_increment,
  `name` text NOT NULL,
  `alias` varchar(255) NOT NULL default '',
  `link` text NOT NULL,
  `filename` varchar(200) default NULL,
  `published` tinyint(1) NOT NULL default '0',
  `numarticles` int(11) unsigned NOT NULL default '1',
  `cache_time` int(11) unsigned NOT NULL default '3600',
  `checked_out` tinyint(3) unsigned NOT NULL default '0',
  `checked_out_time` datetime NOT NULL default '0000-00-00 00:00:00',
  `ordering` int(11) NOT NULL default '0',
  `rtl` tinyint(4) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `published` (`published`),
  KEY `catid` (`catid`)
) TYPE=MyISAM CHARACTER SET `utf8`;

# --------------------------------------------------------

#
# Table structure for table `#__poll_data`
#

CREATE TABLE `#__poll_data` (
  `id` int(11) NOT NULL auto_increment,
  `pollid` int(11) NOT NULL default '0',
  `text` text NOT NULL default '',
  `hits` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `pollid` (`pollid`,`text`(1))
) TYPE=MyISAM CHARACTER SET `utf8`;

# --------------------------------------------------------

#
# Table structure for table `#__poll_date`
#

CREATE TABLE `#__poll_date` (
  `id` bigint(20) NOT NULL auto_increment,
  `date` datetime NOT NULL default '0000-00-00 00:00:00',
  `vote_id` int(11) NOT NULL default '0',
  `poll_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `poll_id` (`poll_id`)
) TYPE=MyISAM CHARACTER SET `utf8`;

# --------------------------------------------------------

#
# Table structure for table `#__polls`
#

CREATE TABLE `#__polls` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `title` varchar(255) NOT NULL default '',
  `alias` varchar(255) NOT NULL default '',
  `voters` int(9) NOT NULL default '0',
  `checked_out` int(11) NOT NULL default '0',
  `checked_out_time` datetime NOT NULL default '0000-00-00 00:00:00',
  `published` tinyint(1) NOT NULL default '0',
  `access` int(11) NOT NULL default '0',
  `lag` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM CHARACTER SET `utf8`;

# --------------------------------------------------------

#
# Table structure for table `#__poll_menu`
# !!!DEPRECATED!!!
#

CREATE TABLE `#__poll_menu` (
  `pollid` int(11) NOT NULL default '0',
  `menuid` int(11) NOT NULL default '0',
  PRIMARY KEY  (`pollid`,`menuid`)
) TYPE=MyISAM CHARACTER SET `utf8`;

# --------------------------------------------------------

#
# Table structure for table `#__sections`
#

CREATE TABLE `#__sections` (
  `id` int(11) NOT NULL auto_increment,
  `title` varchar(255) NOT NULL default '',
  `name` varchar(255) NOT NULL default '',
  `alias` varchar(255) NOT NULL default '',
  `image` TEXT NOT NULL default '',
  `scope` varchar(50) NOT NULL default '',
  `image_position` varchar(30) NOT NULL default '',
  `description` text NOT NULL,
  `published` tinyint(1) NOT NULL default '0',
  `checked_out` int(11) unsigned NOT NULL default '0',
  `checked_out_time` datetime NOT NULL default '0000-00-00 00:00:00',
  `ordering` int(11) NOT NULL default '0',
  `access` tinyint(3) unsigned NOT NULL default '0',
  `count` int(11) NOT NULL default '0',
  `params` text NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `idx_scope` (`scope`)
) TYPE=MyISAM CHARACTER SET `utf8`;

# --------------------------------------------------------

#
# Table structure for table `#__session`
#

CREATE TABLE `#__session` (
  `username` varchar(150) default '',
  `time` varchar(14) default '',
  `session_id` varchar(200) NOT NULL default '0',
  `guest` tinyint(4) default '1',
  `userid` int(11) default '0',
  `usertype` varchar(50) default '',
  `gid` tinyint(3) unsigned NOT NULL default '0',
  `client_id` tinyint(3) unsigned NOT NULL default '0',
  `data` longtext,
  PRIMARY KEY  (`session_id`(64)),
  KEY `whosonline` (`guest`,`usertype`),
  KEY `userid` (`userid`),
  KEY `time` (`time`)
) TYPE=MyISAM CHARACTER SET `utf8`;

# --------------------------------------------------------

#
# Table structure for table `#__stats_agents`
#

CREATE TABLE `#__stats_agents` (
  `agent` varchar(255) NOT NULL default '',
  `type` tinyint(1) unsigned NOT NULL default '0',
  `hits` int(11) unsigned NOT NULL default '1'
) TYPE=MyISAM CHARACTER SET `utf8`;

# --------------------------------------------------------

#
# Table structure for table `#__templates_menu`
#

CREATE TABLE `#__templates_menu` (
  `template` varchar(255) NOT NULL default '',
  `menuid` int(11) NOT NULL default '0',
  `client_id` tinyint(4) NOT NULL default '0',
  PRIMARY KEY (`menuid`, `client_id`, `template`(255))
) TYPE=MyISAM CHARACTER SET `utf8`;

# Dumping data for table `#__templates_menu`
INSERT INTO `#__templates_menu` VALUES ('rhuk_milkyway', '0', '0');
INSERT INTO `#__templates_menu` VALUES ('khepri', '0', '1');

# --------------------------------------------------------

#
# Table structure for table `#__users`
#

CREATE TABLE `#__users` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(255) NOT NULL default '',
  `username` varchar(150) NOT NULL default '',
  `email` varchar(100) NOT NULL default '',
  `password` varchar(100) NOT NULL default '',
  `usertype` varchar(25) NOT NULL default '',
  `block` tinyint(4) NOT NULL default '0',
  `sendEmail` tinyint(4) default '0',
  `gid` tinyint(3) unsigned NOT NULL default '1',
  `registerDate` datetime NOT NULL default '0000-00-00 00:00:00',
  `lastvisitDate` datetime NOT NULL default '0000-00-00 00:00:00',
  `activation` varchar(100) NOT NULL default '',
  `params` text NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `usertype` (`usertype`),
  KEY `idx_name` (`name`),
  KEY `gid_block` (`gid`, `block`),
  KEY `username` (`username`),
  KEY `email` (`email`)
) TYPE=MyISAM CHARACTER SET `utf8`;

# --------------------------------------------------------

#
# Table structure for table `#__weblinks`
#

CREATE TABLE `#__weblinks` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `catid` int(11) NOT NULL default '0',
  `sid` int(11) NOT NULL default '0',
  `title` varchar(250) NOT NULL default '',
  `alias` varchar(255) NOT NULL default '',
  `url` varchar(250) NOT NULL default '',
  `description` text NOT NULL default '',
  `date` datetime NOT NULL default '0000-00-00 00:00:00',
  `hits` int(11) NOT NULL default '0',
  `published` tinyint(1) NOT NULL default '0',
  `checked_out` int(11) NOT NULL default '0',
  `checked_out_time` datetime NOT NULL default '0000-00-00 00:00:00',
  `ordering` int(11) NOT NULL default '0',
  `archived` tinyint(1) NOT NULL default '0',
  `approved` tinyint(1) NOT NULL default '1',
  `params` text NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `catid` (`catid`,`published`,`archived`)
) TYPE=MyISAM CHARACTER SET `utf8`;

# --------------------------------------------------------

#
# Table structure for table `#__core_acl_aro`
#

CREATE TABLE `#__core_acl_aro` (
  `id` int(11) NOT NULL auto_increment,
  `section_value` varchar(240) NOT NULL default '0',
  `value` varchar(240) NOT NULL default '',
  `order_value` int(11) NOT NULL default '0',
  `name` varchar(255) NOT NULL default '',
  `hidden` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `#__section_value_value_aro` (`section_value`(100),`value`(100)),
  KEY `#__gacl_hidden_aro` (`hidden`)
) TYPE=MyISAM CHARACTER SET `utf8`;

# --------------------------------------------------------

#
# Table structure for table `#__core_acl_aro_map`
#

CREATE TABLE  `#__core_acl_aro_map` (
  `acl_id` int(11) NOT NULL default '0',
  `section_value` varchar(230) NOT NULL default '0',
  `value` varchar(100) NOT NULL,
  PRIMARY KEY  (`acl_id`,`section_value`,`value`)
) TYPE=MyISAM CHARACTER SET `utf8`;

# --------------------------------------------------------

#
# Table structure for table `#__core_acl_aro_groups`
#
CREATE TABLE `#__core_acl_aro_groups` (
  `id` int(11) NOT NULL auto_increment,
  `parent_id` int(11) NOT NULL default '0',
  `name` varchar(255) NOT NULL default '',
  `lft` int(11) NOT NULL default '0',
  `rgt` int(11) NOT NULL default '0',
  `value` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`id`),
  KEY `#__gacl_parent_id_aro_groups` (`parent_id`),
  KEY `#__gacl_lft_rgt_aro_groups` (`lft`,`rgt`)
) TYPE=MyISAM CHARACTER SET `utf8`;

#
# Dumping data for table `#__core_acl_aro_groups`
#

INSERT INTO `#__core_acl_aro_groups` VALUES (17,0,'ROOT',1,22,'ROOT');
INSERT INTO `#__core_acl_aro_groups` VALUES (28,17,'USERS',2,21,'USERS');
INSERT INTO `#__core_acl_aro_groups` VALUES (29,28,'Public Frontend',3,12,'Public Frontend');
INSERT INTO `#__core_acl_aro_groups` VALUES (18,29,'Registered',4,11,'Registered');
INSERT INTO `#__core_acl_aro_groups` VALUES (19,18,'Author',5,10,'Author');
INSERT INTO `#__core_acl_aro_groups` VALUES (20,19,'Editor',6,9,'Editor');
INSERT INTO `#__core_acl_aro_groups` VALUES (21,20,'Publisher',7,8,'Publisher');
INSERT INTO `#__core_acl_aro_groups` VALUES (30,28,'Public Backend',13,20,'Public Backend');
INSERT INTO `#__core_acl_aro_groups` VALUES (23,30,'Manager',14,19,'Manager');
INSERT INTO `#__core_acl_aro_groups` VALUES (24,23,'Administrator',15,18,'Administrator');
INSERT INTO `#__core_acl_aro_groups` VALUES (25,24,'Super Administrator',16,17,'Super Administrator');

# --------------------------------------------------------

#
# Table structure for table `#__core_acl_groups_aro_map`
#
CREATE TABLE `#__core_acl_groups_aro_map` (
  `group_id` int(11) NOT NULL default '0',
  `section_value` varchar(240) NOT NULL default '',
  `aro_id` int(11) NOT NULL default '0',
  UNIQUE KEY `group_id_aro_id_groups_aro_map` (`group_id`,`section_value`,`aro_id`)
) TYPE=MyISAM CHARACTER SET `utf8`;

# --------------------------------------------------------

#
# Table structure for table `#__core_acl_aro_sections`
#
CREATE TABLE `#__core_acl_aro_sections` (
  `id` int(11) NOT NULL auto_increment,
  `value` varchar(230) NOT NULL default '',
  `order_value` int(11) NOT NULL default '0',
  `name` varchar(230) NOT NULL default '',
  `hidden` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `#__gacl_value_aro_sections` (`value`),
  KEY `#__gacl_hidden_aro_sections` (`hidden`)
) TYPE=MyISAM CHARACTER SET `utf8`;

INSERT INTO `#__core_acl_aro_sections` VALUES (10,'users',1,'Users',0);

# --------------------------------------------------------

#
# Table structure for table `#__migration_backlinks`
#
CREATE TABLE `#__migration_backlinks` (
	`itemid` INT(11) NOT NULL,
	`name` VARCHAR(100) NOT NULL,
	`url` TEXT NOT NULL,
	`sefurl` TEXT NOT NULL,
	`newurl` TEXT NOT NULL,
	PRIMARY KEY(`itemid`)
) TYPE=MyISAM CHARACTER SET `utf8`;

# --------------------------------------------------------

#
# Table structure for table `#__feature_history`
#
CREATE TABLE IF NOT EXISTS `#__feature_history` (
  `id` int(11) NOT NULL auto_increment,
  `objectid` int(11) default NULL,
  `featured` datetime default '0000-00-00 00:00:00',
  `tbl` varchar(255) default NULL,
  `note` varchar(255) default NULL,
  PRIMARY KEY  (`id`)
) TYPE=MyISAM CHARACTER SET `utf8`;

# --------------------------------------------------------

#
# Table structure for table `#__xforum`
#
CREATE TABLE IF NOT EXISTS `#__xforum` (
  `id` int(11) NOT NULL auto_increment,
  `topic` varchar(255) default NULL,
  `comment` text,
  `created` datetime NOT NULL default '0000-00-00 00:00:00',
  `created_by` int(11) default '0',
  `state` tinyint(3) NOT NULL default '0',
  `sticky` tinyint(2) NOT NULL default '0',
  `parent` int(11) NOT NULL default '0',
  `hits` int(11) default '0',
  `group` int(11) default '0',
  `access` tinyint(2) default '4',
  `anonymous` tinyint(2) NULL DEFAULT '0',
  PRIMARY KEY  (`id`),
  FULLTEXT KEY `question` (`comment`)
) TYPE=MyISAM CHARACTER SET `utf8`;

# --------------------------------------------------------

#
# Table structure for table `#__wiki_attachments`
#
CREATE TABLE IF NOT EXISTS `#__wiki_attachments` (
  `id` int(11) NOT NULL auto_increment,
  `pageid` int(11) default '0',
  `filename` varchar(255) default NULL,
  `description` tinytext,
  `created` datetime NULL DEFAULT '0000-00-00 00:00:00',
  `created_by` int(11) NULL DEFAULT '0',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM CHARACTER SET `utf8`;

# --------------------------------------------------------

#
# Table structure for table `#__wiki_comments`
#
CREATE TABLE IF NOT EXISTS `#__wiki_comments` (
  `id` int(11) NOT NULL auto_increment,
  `pageid` int(11) NOT NULL default '0',
  `version` int(11) NOT NULL default '0',
  `created` datetime NOT NULL default '0000-00-00 00:00:00',
  `created_by` int(11) NOT NULL default '0',
  `ctext` text,
  `chtml` text,
  `rating` tinyint(1) NOT NULL default '0',
  `anonymous` tinyint(1) NOT NULL default '0',
  `parent` int(11) NOT NULL default '0',
  `status` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM CHARACTER SET `utf8`;

# --------------------------------------------------------

#
# Table structure for table `#__wiki_math`
#
CREATE TABLE IF NOT EXISTS `#__wiki_math` (
  `inputhash` varbinary(16) NOT NULL,
  `outputhash` varbinary(16) NOT NULL,
  `conservativeness` tinyint(4) NOT NULL,
  `html` text,
  `mathml` text,
  `id` int(11) NOT NULL auto_increment,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `inputhash` (`inputhash`)
) TYPE=MyISAM CHARACTER SET `utf8`;

# --------------------------------------------------------

#
# Table structure for table `#__wiki_page`
#
CREATE TABLE IF NOT EXISTS `#__wiki_page` (
  `id` int(11) NOT NULL auto_increment,
  `pagename` varchar(100) default NULL,
  `hits` int(11) NOT NULL default '0',
  `created_by` int(11) NOT NULL default '0',
  `rating` decimal(2,1) NOT NULL default '0.0',
  `times_rated` int(11) NOT NULL default '0',
  `title` varchar(255) default NULL,
  `scope` varchar(255) NOT NULL,
  `params` tinytext,
  `ranking` float default '0',
  `authors` varchar(255) default NULL,
  `access` tinyint(2) default '0',
  `group` varchar(255) default NULL,
  `state` tinyint(2) NULL DEFAULT '0',
  PRIMARY KEY  (`id`),
  FULLTEXT KEY `title` (`title`)
) TYPE=MyISAM CHARACTER SET `utf8`;

# --------------------------------------------------------

#
# Table structure for table `#__wiki_version`
#
CREATE TABLE IF NOT EXISTS `#__wiki_version` (
  `id` int(11) NOT NULL auto_increment,
  `pageid` int(11) NOT NULL default '0',
  `version` int(11) NOT NULL default '0',
  `created` datetime default NULL,
  `created_by` int(11) NOT NULL default '0',
  `minor_edit` int(1) NOT NULL default '0',
  `pagetext` text,
  `pagehtml` text,
  `approved` int(1) NOT NULL default '0',
  `summary` varchar(255) default NULL,
  PRIMARY KEY  (`id`),
  FULLTEXT KEY `pagetext` (`pagetext`)
) TYPE=MyISAM CHARACTER SET `utf8`;

# --------------------------------------------------------

#
# Table structure for table `#__wiki_log`
#
CREATE TABLE IF NOT EXISTS `#__wiki_log` (
  `id`       	int(11) AUTO_INCREMENT NOT NULL,
  `pid`      	int(11) NOT NULL DEFAULT '0',
  `timestamp`	datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `uid`      	int(11) NULL DEFAULT '0',
  `action`   	varchar(50) NULL,
  `comments` 	text NULL,
  `actorid`  	int(11) NULL DEFAULT '0',
  PRIMARY KEY(`id`)
) TYPE=MyISAM CHARACTER SET `utf8`;

# --------------------------------------------------------

#
# Table structure for table `#__myhub`
#
CREATE TABLE IF NOT EXISTS `#__myhub` (
  `uid` INT(11) NOT NULL,
  `prefs` VARCHAR(200),
  `modified` DATETIME NULL DEFAULT '0000-00-00 00:00:00'
) Type=MyISAM DEFAULT CHARSET=utf8;

# --------------------------------------------------------

#
# Table structure for table `#__myhub_params`
#
CREATE TABLE IF NOT EXISTS`#__myhub_params` (
  `uid` INT(11) NOT NULL,
  `mid` INT(11) NOT NULL,
  `params` TEXT
) Type=MyISAM DEFAULT CHARSET=utf8;

# --------------------------------------------------------

#
# Table structure for table `#__sites`
#
CREATE TABLE IF NOT EXISTS `#__sites` (
  `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `title` varchar(100),
  `category` varchar(100),
  `url` varchar(255),
  `image` varchar(255),
  `teaser` varchar(255),
  `description` text,
  `notes` text,
  `checked_out` int(11) NOT NULL default '0',
  `checked_out_time` datetime NOT NULL default '0000-00-00 00:00:00',
  `published` tinyint(1) NOT NULL default '0',
  `published_date` datetime NOT NULL default '0000-00-00 00:00:00',
  `state` varchar(30)
) TYPE=MyISAM CHARACTER SET `utf8`;

# --------------------------------------------------------

#
# Table structure for table `#__cart`
#
CREATE TABLE IF NOT EXISTS `#__cart` (
  `id` int(10) NOT NULL auto_increment,
  `uid` int(11) NOT NULL default '0',
  `itemid` int(11) NOT NULL default '0',
  `type` varchar(20) default NULL,
  `quantity` int(11) NOT NULL default '0',
  `added` datetime NOT NULL default '0000-00-00 00:00:00',
  `selections` text,
  PRIMARY KEY  (`id`)
) TYPE=MyISAM CHARACTER SET `utf8`;

# --------------------------------------------------------

#
# Table structure for table `#__market_history`
#
CREATE TABLE IF NOT EXISTS `#__market_history` (
  `id` int(11) NOT NULL auto_increment,
  `itemid` int(11) NOT NULL default '0',
  `category` varchar(50) default NULL,
  `date` datetime NOT NULL default '0000-00-00 00:00:00',
  `action` varchar(50) default NULL,
  `log` text,
  `market_value` int(11) default '0',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM CHARACTER SET `utf8`;

# --------------------------------------------------------

#
# Table structure for table `#__order_items`
#
CREATE TABLE IF NOT EXISTS `#__order_items` (
  `id` int(10) NOT NULL auto_increment,
  `oid` int(11) NOT NULL default '0',
  `uid` int(11) NOT NULL default '0',
  `itemid` int(11) NOT NULL default '0',
  `price` int(11) NOT NULL default '0',
  `quantity` int(11) NOT NULL default '0',
  `selections` text,
  PRIMARY KEY  (`id`)
) TYPE=MyISAM CHARACTER SET `utf8`;

# --------------------------------------------------------

#
# Table structure for table `#__orders`
#
CREATE TABLE IF NOT EXISTS `#__orders` (
  `id` int(10) NOT NULL auto_increment,
  `uid` int(11) NOT NULL default '0',
  `type` varchar(20) default NULL,
  `total` int(11) default '0',
  `status` int(11) NOT NULL default '0',
  `details` text,
  `email` varchar(150) default NULL,
  `ordered` datetime NOT NULL default '0000-00-00 00:00:00',
  `status_changed` datetime default '0000-00-00 00:00:00',
  `notes` text,
  PRIMARY KEY  (`id`)
) TYPE=MyISAM CHARACTER SET `utf8`;

# --------------------------------------------------------

#
# Table structure for table `#__store`
#
CREATE TABLE IF NOT EXISTS `#__store` (
  `id` int(10) NOT NULL auto_increment,
  `title` varchar(127) NOT NULL default '',
  `price` int(11) NOT NULL default '0',
  `description` text,
  `published` tinyint(1) NOT NULL default '0',
  `featured` tinyint(1) NOT NULL default '0',
  `created` datetime NOT NULL default '0000-00-00 00:00:00',
  `available` int(1) NOT NULL default '0',
  `params` text,
  `special` int(11) default '0',
  `type` int(11) default '1',
  `category` varchar(127) default '',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM CHARACTER SET `utf8`;

# --------------------------------------------------------

#
# Table structure for table `#__xgroups`
#
CREATE TABLE IF NOT EXISTS `#__xgroups` (
  `gidNumber` int(11) NOT NULL auto_increment,
  `cn` varchar(255) default NULL,
  `description` varchar(255) default NULL,
  `published` tinyint(3) default '0',
  `type` tinyint(3) default '0',
  `access` tinyint(3) default '0',
  `public_desc` text default '',
  `private_desc` text default '',
  `restrict_msg` text default '',
  `join_policy` tinyint(3) NULL DEFAULT '0',
  `privacy` tinyint(3) NULL DEFAULT '0',
  PRIMARY KEY (`gidNumber`)
) TYPE=MyISAM CHARACTER SET `utf8`;

# --------------------------------------------------------

#
# Table structure for table `#__xgroups_reasons`
#
CREATE TABLE IF NOT EXISTS `#__xgroups_reasons` (
  `id` int(11) NOT NULL auto_increment,
  `uidNumber` int(11) NOT NULL,
  `gidNumber` int(11) NOT NULL,
  `reason` text,
  `date` datetime default NULL,
  PRIMARY KEY  (`id`)
) TYPE=MyISAM CHARACTER SET `utf8`;

# --------------------------------------------------------

#
# Table structure for table `#__xgroups_members`
#
CREATE TABLE IF NOT EXISTS `#__xgroups_members` (
  `gidNumber` int(11) NOT NULL,
  `uidNumber` int(11) NOT NULL,
  PRIMARY KEY (`gidNumber`,`uidNumber`)
) TYPE=MyISAM CHARACTER SET `utf8`;

# --------------------------------------------------------

#
# Table structure for table `#__xgroups_applicants`
#
CREATE TABLE IF NOT EXISTS `#__xgroups_applicants` (
  `gidNumber` int(11) NOT NULL,
  `uidNumber` int(11) NOT NULL,
  PRIMARY KEY (`gidNumber`,`uidNumber`)
) TYPE=MyISAM CHARACTER SET `utf8`;

# --------------------------------------------------------

#
# Table structure for table `#__xgroups_managers`
#
CREATE TABLE IF NOT EXISTS `#__xgroups_managers` (
  `gidNumber` int(11) NOT NULL,
  `uidNumber` int(11) NOT NULL,
  PRIMARY KEY (`gidNumber`,`uidNumber`)
) TYPE=MyISAM CHARACTER SET `utf8`;

# --------------------------------------------------------

#
# Table structure for table `#__xgroups_invitees`
#
CREATE TABLE IF NOT EXISTS `#__xgroups_invitees` (
  `gidNumber` int(11) NOT NULL,
  `uidNumber` int(11) NOT NULL,
  PRIMARY KEY (`gidNumber`,`uidNumber`)
) TYPE=MyISAM CHARACTER SET `utf8`;

# --------------------------------------------------------

#
# Table structure for table `#__xgroups_log`
#
CREATE TABLE IF NOT EXISTS `#__xgroups_log` (
  `id` int(11) NOT NULL auto_increment,
  `gid` int(11) NOT NULL default '0',
  `timestamp` datetime NOT NULL default '0000-00-00 00:00:00',
  `uid` int(11) default '0',
  `action` varchar(50) default NULL,
  `comments` text,
  `actorid` int(11) default '0',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM CHARACTER SET `utf8`;

# --------------------------------------------------------

#
# Table structure for table `#__tags_group`
#
CREATE TABLE IF NOT EXISTS `#__tags_group` ( 
  `id`      	int(11) AUTO_INCREMENT NOT NULL,
  `groupid` 	int(11) NULL DEFAULT '0',
  `tagid`   	int(11) NULL DEFAULT '0',
  `priority`	int(11) NULL DEFAULT '0',
  PRIMARY KEY(`id`)
) TYPE=MyISAM CHARACTER SET `utf8`;

# --------------------------------------------------------

#
# Table structure for table `#__meetings`
#
CREATE TABLE IF NOT EXISTS `#__meetings` (
  `id` int(12) NOT NULL auto_increment,
  `room_id` int(11) unsigned NOT NULL default '26714',
  `title` varchar(50) NOT NULL default '',
  `url` varchar(50) NOT NULL default '',
  `description` longtext NOT NULL,
  `date_begin` datetime NOT NULL default '0000-00-00 00:00:00',
  `date_end` datetime NOT NULL default '0000-00-00 00:00:00',
  `duration` varchar(15) NOT NULL default '24:00:00',
  `time_zone` varchar(10) NOT NULL default '-05:00',
  `time_zone_A` varchar(50) default 'Eastern Time',
  `date_created` datetime NOT NULL default '0000-00-00 00:00:00',
  `date_modified` datetime NOT NULL default '0000-00-00 00:00:00',
  `date_deleted` datetime NOT NULL default '0000-00-00 00:00:00',
  `owner` varchar(200) NOT NULL default 'nanobreeze',
  `hosts` varchar(200) NOT NULL default '',
  `presenters` text,
  `participants` text,
  `guests` text NOT NULL,
  `expired` varchar(100) NOT NULL default 'false',
  `deleted` varchar(100) NOT NULL default 'false',
  `access` varchar(100) NOT NULL default 'view-hidden',
  `phone` varchar(100) NOT NULL default '',
  `hits` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  FULLTEXT KEY `title` (`title`),
  FULLTEXT KEY `description` (`description`)
) TYPE=MyISAM CHARACTER SET `utf8`;

# --------------------------------------------------------

#
# Table structure for table `#__events`
#
CREATE TABLE IF NOT EXISTS `#__events` (
  `id` int(12) NOT NULL auto_increment,
  `sid` int(11) NOT NULL default '0',
  `catid` int(11) NOT NULL default '1',
  `title` varchar(255) NOT NULL default '',
  `content` longtext NOT NULL default '',
  `adresse_info` VARCHAR(120) NOT NULL default '',
  `contact_info` VARCHAR(120) NOT NULL default '',
  `extra_info` VARCHAR(240) NOT NULL default '',
  `color_bar` VARCHAR(8) NOT NULL default '',
  `useCatColor` TINYINT(1) NOT NULL default '0',
  `state` tinyint(3) NOT NULL default '0',
  `mask` int(11) unsigned NOT NULL default '0',
  `created` datetime NOT NULL default '0000-00-00 00:00:00',
  `created_by` int(11) unsigned NOT NULL default '0',
  `created_by_alias` varchar(100) NOT NULL default '',
  `modified` datetime NOT NULL default '0000-00-00 00:00:00',
  `modified_by` int(11) unsigned NOT NULL default '0',
  `checked_out` int(11) unsigned NOT NULL default '0',
  `checked_out_time` datetime NOT NULL default '0000-00-00 00:00:00',
  `publish_up` datetime NOT NULL default '0000-00-00 00:00:00',
  `publish_down` datetime NOT NULL default '0000-00-00 00:00:00',
  `images` text NOT NULL default '',
  `reccurtype` tinyint(1) NOT NULL default '0',
  `reccurday` varchar(4) NOT NULL default '',
  `reccurweekdays` varchar(20) NOT NULL default '',
  `reccurweeks` varchar(10) NOT NULL default '',
  `approved` tinyint(1) NOT NULL default '1',
  `announcement` tinyint(1) NOT NULL default '0',
  `ordering` int(11) NOT NULL default '0',
  `archived` tinyint(1) NOT NULL default '0',
  `access` int(11) unsigned NOT NULL default '0',
  `hits` int(11) NOT NULL default '0',
  `registerby` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `params` text NULL,
  `restricted` varchar(100) NULL,
  `email` varchar(255) NULL,
  PRIMARY KEY  (`id`),
  FULLTEXT KEY `title` (`title`),
  FULLTEXT KEY `content` (`content`)
) TYPE=MyISAM; 

# --------------------------------------------------------

#
# Table structure for table `#__events_categories`
#
CREATE TABLE IF NOT EXISTS `#__events_categories` (
  `id` INT(12) NOT NULL default '0' PRIMARY KEY,
  `color` VARCHAR(8) NOT NULL default''
) TYPE=MyISAM;

# --------------------------------------------------------

#
# Table structure for table `#__events_config`
#
CREATE TABLE IF NOT EXISTS `#__events_config` (
  `param` varchar(100) default NULL,
  `value` tinytext
) TYPE=MyISAM CHARACTER SET `utf8`;

CREATE TABLE #__events_pages ( 
	id         	int(11) AUTO_INCREMENT NOT NULL,
	event_id   	int(11) NULL DEFAULT '0',
	alias      	varchar(100) NOT NULL,
	title      	varchar(250) NOT NULL,
	pagetext   	text NULL,
	created    	datetime NULL DEFAULT '0000-00-00 00:00:00',
	created_by 	int(11) NULL DEFAULT '0',
	modified   	datetime NULL DEFAULT '0000-00-00 00:00:00',
	modified_by	int(11) NULL DEFAULT '0',
	ordering   	int(2) NULL DEFAULT '0',
	params     	text NULL,
	PRIMARY KEY(id)
) TYPE=MyISAM CHARACTER SET `utf8`;

CREATE TABLE #__events_respondent_race_rel ( 
	respondent_id     	int(11) NULL,
	race              	varchar(255) NULL,
	tribal_affiliation	varchar(255) NULL 
) TYPE=MyISAM CHARACTER SET `utf8`;

CREATE TABLE #__events_respondents ( 
	event_id            	int(11) NOT NULL DEFAULT '0',
	registered          	timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
	first_name          	varchar(50) NOT NULL,
	last_name           	varchar(50) NOT NULL,
	affiliation         	varchar(50) NULL,
	title               	varchar(50) NULL,
	city                	varchar(50) NULL,
	state               	varchar(20) NULL,
	zip                 	varchar(10) NULL,
	country             	varchar(20) NULL,
	telephone           	varchar(20) NULL,
	fax                 	varchar(20) NULL,
	email               	varchar(255) NULL,
	website             	varchar(255) NULL,
	position_description	varchar(50) NULL,
	highest_degree      	varchar(10) NULL,
	gender              	char(1) NULL,
	disability_needs    	tinyint(4) NULL,
	dietary_needs       	varchar(500) NULL,
	attending_dinner    	tinyint(4) NULL,
	abstract            	text NULL,
	comment             	text NULL,
	id                  	int(11) AUTO_INCREMENT NOT NULL,
	arrival             	varchar(50) NULL,
	departure           	varchar(50) NULL,
	PRIMARY KEY(id)
) TYPE=MyISAM CHARACTER SET `utf8`;

# --------------------------------------------------------

#
# Table structure for table `#__modifications`
#
CREATE TABLE IF NOT EXISTS `#__modifications` (
  `id` int(11) NOT NULL auto_increment,
  `component` varchar(31) default NULL,
  `item_label` varchar(100) default NULL,
  `action` varchar(31) default NULL,
  `when` datetime default NULL,
  `who` int(11) default NULL,
  `comments` text,
  PRIMARY KEY  (`id`)
) TYPE=MyISAM CHARACTER SET `utf8`;

# --------------------------------------------------------

#
# Table structure for table `#__doi_mapping`
#
CREATE TABLE IF NOT EXISTS `#__doi_mapping` (
  `local_revision` int(11) NOT NULL,
  `doi_label` int(11) NOT NULL,
  `rid` int(11) NOT NULL,
  `alias` varchar(30) default NULL
) TYPE=MyISAM CHARACTER SET `utf8`;

# --------------------------------------------------------

#
# Table structure for table `#__screenshots`
#
CREATE TABLE IF NOT EXISTS `#__screenshots` (
  `id` int(10) NOT NULL auto_increment,
  `versionid` int(11) default '0',
  `title` varchar(127) default '',
  `ordering` int(11) default '0',
  `filename` varchar(100) NOT NULL,
  `resourceid` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM CHARACTER SET `utf8`;

# --------------------------------------------------------

#
# Table structure for table `#__tool`
#
CREATE TABLE IF NOT EXISTS `#__tool` (
  `id` int(10) NOT NULL auto_increment,
  `toolname` varchar(64) NOT NULL default '',
  `title` varchar(127) NOT NULL default '',
  `version` varchar(15) default NULL,
  `description` text,
  `fulltext` text,
  `license` text,
  `toolaccess` varchar(15) default NULL,
  `codeaccess` varchar(15) default NULL,
  `wikiaccess` varchar(15) default NULL,
  `published` tinyint(1) default '0',
  `state` int(15) default NULL,
  `priority` int(15) default '3',
  `team` text,
  `registered` datetime default NULL,
  `registered_by` varchar(31) default NULL,
  `mw` varchar(31) default NULL,
  `vnc_geometry` varchar(31) default NULL,
  `ticketid` int(15) default NULL,
  `state_changed` datetime default '0000-00-00 00:00:00',
  `revision` int(11) default NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `toolname` (`toolname`)
) TYPE=MyISAM CHARACTER SET `utf8`;

# --------------------------------------------------------

#
# Table structure for `#__tool_authors`
#
CREATE TABLE IF NOT EXISTS `#__tool_authors` (
  `toolname` varchar(50) NOT NULL default '',
  `revision` int(15) NOT NULL default '0',
  `uid` int(11) NOT NULL default '0',
  `ordering` int(11) default '0',
  `version_id` int(11) NOT NULL default '0',
  `name` varchar(255) NULL,
  `organization` varchar(255) NULL,
  PRIMARY KEY  (`toolname`,`revision`,`uid`,`version_id`)
) TYPE=MyISAM CHARACTER SET `utf8`;

# --------------------------------------------------------

#
# Table structure for `#__tool_groups`
#
CREATE TABLE IF NOT EXISTS `#__tool_groups` (
  `cn` varchar(255) NOT NULL default '',
  `toolid` int(11) NOT NULL default '0',
  `role` tinyint(2) NOT NULL default '0',
  PRIMARY KEY  (`cn`,`toolid`,`role`)
) TYPE=MyISAM CHARACTER SET `utf8`;

# --------------------------------------------------------

#
# Table structure for `#__tool_version`
#
CREATE TABLE IF NOT EXISTS `#__tool_version` (
  `id` int(10) NOT NULL auto_increment,
  `toolname` varchar(64) NOT NULL default '',
  `instance` varchar(31) NOT NULL default '',
  `title` varchar(127) NOT NULL default '',
  `description` text,
  `fulltext` text,
  `version` varchar(15) default NULL,
  `revision` int(11) default NULL,
  `toolaccess` varchar(15) default NULL,
  `codeaccess` varchar(15) default NULL,
  `wikiaccess` varchar(15) default NULL,
  `state` int(15) default NULL,
  `released_by` varchar(31) default NULL,
  `released` datetime NULL,
  `unpublished` datetime NULL,
  `exportControl` varchar(16) NULL,
  `license` text,
  `vnc_geometry` varchar(31) default NULL,
  `vnc_depth` int(11) NULL,
  `vnc_timeout` int(11) NULL,
  `vnc_command` varchar(100) default NULL,
  `mw` varchar(31) default NULL,
  `toolid` int(11) default NULL,
  `priority` int(11) NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `toolname` (`toolname`,`instance`)
) TYPE=MyISAM CHARACTER SET `utf8`;

# --------------------------------------------------------

#
# Table structure for `#__tool_statusviews`
#
CREATE TABLE IF NOT EXISTS `#__tool_statusviews` (
  `id` int(10) NOT NULL auto_increment,
  `ticketid` varchar(15) NOT NULL default '',
  `uid` varchar(31) NOT NULL default '',
  `viewed` datetime default '0000-00-00 00:00:00',
  `elapsed` int(11) default '500000',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM CHARACTER SET `utf8`;

CREATE TABLE #__tool_version_alias ( 
	tool_version_id	int(11) NOT NULL,
	alias          	varchar(255) NOT NULL 
) TYPE=MyISAM CHARACTER SET `utf8`;

CREATE TABLE #__tool_version_hostreq ( 
	tool_version_id	int(11) NOT NULL,
	hostreq        	varchar(255) NOT NULL,
	UNIQUE KEY `toolid` (`tool_version_id`,`hostreq`)
) TYPE=MyISAM CHARACTER SET `utf8`;

CREATE TABLE #__tool_version_middleware ( 
	tool_version_id	int(11) NOT NULL,
	middleware     	varchar(255) NOT NULL,
	UNIQUE KEY `toolid` (`tool_version_id`,`middleware`)
) TYPE=MyISAM CHARACTER SET `utf8`;

CREATE TABLE #__tool_version_tracperm ( 
	tool_version_id	int(11) NOT NULL,
	tracperm       	varchar(64) NOT NULL, 
	UNIQUE KEY `toolid` (`tool_version_id`,`tracperm`)
) TYPE=MyISAM CHARACTER SET `utf8`;

# --------------------------------------------------------

#
# Table structure for `#__trac_projects`
#
CREATE TABLE IF NOT EXISTS `#__trac_projects` ( 
	id  	int(11) NOT NULL,
	name	varchar(255) NOT NULL,
	type	int(11) NOT NULL 
	)
TYPE=MyISAM CHARACTER SET `utf8`;

# --------------------------------------------------------

#
# Table structure for `#__users_tracperms`
#
CREATE TABLE IF NOT EXISTS `#__users_tracperms` ( 
	user_id   	int(11) NOT NULL,
	action    	varchar(255) NOT NULL,
	project_id	int(11) NOT NULL,
	PRIMARY KEY(user_id,action)
)
TYPE=MyISAM CHARACTER SET `utf8`;

# --------------------------------------------------------

#
# Table structure for `#__xgroup_tracperm`
#
CREATE TABLE IF NOT EXISTS `#__xgroups_tracperm` ( 
	group_id  	int(11) NOT NULL,
	action    	varchar(255) NOT NULL,
	project_id	int(11) NOT NULL,
	PRIMARY KEY(`group_id`,`action`)
	)
TYPE=MyISAM CHARACTER SET `utf8`;

# --------------------------------------------------------

#
# Table structure for `#__tool_licenses`
#
CREATE TABLE IF NOT EXISTS `#__tool_licenses` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(100) default NULL,
  `text` text,
  `title` varchar(100) default NULL,
  `ordering` int(11) default NULL,
  PRIMARY KEY  (`id`)
) TYPE=MyISAM CHARACTER SET `utf8`;

# --------------------------------------------------------

#
# Table structure for `#__element_urls`
#
CREATE TABLE IF NOT EXISTS element_urls (
   `elementid` int(11) NOT NULL,
   `url` varchar(240) UNIQUE NOT NULL
) TYPE=MyISAM CHARACTER SET `utf8`;

# --------------------------------------------------------

#
# Table structure for `#__elements`
#
CREATE TABLE IF NOT EXISTS elements (
   `id` int(11) UNIQUE AUTO_INCREMENT NOT NULL,
   `type` int(11) NOT NULL,
   `title` varchar(255) UNIQUE NOT NULL,
   `url` varchar(255) NOT NULL,
   `resourceid` int(11) UNIQUE
) TYPE=MyISAM CHARACTER SET `utf8`;

# --------------------------------------------------------

#
# Table structure for `#__element_hierarchy`
#
CREATE TABLE IF NOT EXISTS element_hierarchy (
   `id` int(11) NOT NULL,
   `parentid` int(11) NOT NULL
) TYPE=MyISAM CHARACTER SET `utf8`;

# --------------------------------------------------------

#
# Table structure for `#__element_categories`
#
CREATE TABLE IF NOT EXISTS  element_categories (
   `type` int(11) UNIQUE AUTO_INCREMENT NOT NULL,
   `label` varchar(100) NOT NULL
) TYPE=MyISAM CHARACTER SET `utf8`;

# --------------------------------------------------------

#
# Table structure for `#__element_types`
#
CREATE TABLE IF NOT EXISTS element_types (
   `type` int(11) UNIQUE AUTO_INCREMENT NOT NULL,
   `label` varchar(100) NOT NULL,
   `categoryid` int(11)
) TYPE=MyISAM CHARACTER SET `utf8`;

# --------------------------------------------------------

#
# Table structure for `#__element_rollup_pages`
#
CREATE TABLE IF NOT EXISTS element_rollup_pages (
   `id` int(11) UNIQUE AUTO_INCREMENT NOT NULL,
   `elementid` int NOT NULL,
   `year` smallint NOT NULL,
   `month` smallint NOT NULL,
   `pageviews` int,
   `for` char(1)
) TYPE=MyISAM CHARACTER SET `utf8`;

# --------------------------------------------------------

#
# Table structure for `#__element_rollup_users`
#
CREATE TABLE IF NOT EXISTS element_rollup_users (
   `rollupid` int NOT NULL,
   `year` smallint NOT NULL,
   `month` smallint NOT NULL,
   `period` smallint NOT NULL,
   `count` int,
   `exact` boolean,
   `category` varchar(4) NOT NULL
) TYPE=MyISAM CHARACTER SET `utf8`;

# --------------------------------------------------------

#
# Table structure for `#__element_rollup_user_categories`
#
CREATE TABLE IF NOT EXISTS element_rollup_user_categories (
   `code` varchar(4) UNIQUE NOT NULL,
   `label` varchar(100) NOT NULL
) TYPE=MyISAM CHARACTER SET `utf8`;

# --------------------------------------------------------

#
# Table structure for `ipusers`
#
CREATE TABLE IF NOT EXISTS ipusers (
   `id` int(11) UNIQUE AUTO_INCREMENT NOT NULL,
   `ip` varchar(15) NOT NULL,
   `user` tinytext NOT NULL,
   `ntimes` smallint NOT NULL,
   `from` datetime,
   `to` datetime,
   `orgtype` varchar(4) NOT NULL,
   `countryresident` char(2) NOT NULL, 
   `countrycitizen` char(2) NOT NULL,
   `countryip` char(2) NOT NULL
) TYPE=MyISAM CHARACTER SET `utf8`;

# --------------------------------------------------------

#
# Table structure for `#__author_stats`
#
CREATE TABLE IF NOT EXISTS `#__author_stats` (
  `id` bigint(20) NOT NULL auto_increment,
  `authorid` int(11) NOT NULL,
  `tool_users` bigint(20) default NULL,
  `andmore_users` bigint(20) default NULL,
  `total_users` bigint(20) default NULL,
  `datetime` datetime NOT NULL default '0000-00-00 00:00:00',
  `period` tinyint(4) NOT NULL default '-1',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM CHARACTER SET `utf8`;

# --------------------------------------------------------

#
# Table structure for `#__stats_tops`
#
CREATE TABLE IF NOT EXISTS `#__stats_tops` (
  `id` tinyint(4) NOT NULL default '0',
  `name` varchar(128) NOT NULL default '',
  `valfmt` tinyint(4) NOT NULL default '0',
  `size` tinyint(4) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM CHARACTER SET `utf8`;

# --------------------------------------------------------

#
# Table structure for `#__stats_topvals`
#
CREATE TABLE IF NOT EXISTS `#__stats_topvals` (
  `top` tinyint(4) NOT NULL default '0',
  `datetime` datetime NOT NULL default '0000-00-00 00:00:00',
  `period` tinyint(4) NOT NULL default '1',
  `rank` tinyint(4) NOT NULL default '0',
  `name` varchar(255) default NULL,
  `value` bigint(20) NOT NULL default '0',
  KEY `top` (`top`),
  KEY `top_2` (`top`,`rank`),
  KEY `top_3` (`top`,`datetime`),
  KEY `top_4` (`top`,`datetime`,`rank`),
  KEY `top_5` (`top`,`datetime`,`period`)
) TYPE=MyISAM CHARACTER SET `utf8`;

# --------------------------------------------------------

#
# Table structure for `#__xpoll_data`
#
CREATE TABLE IF NOT EXISTS `#__xpoll_data` (
  `id` int(11) NOT NULL auto_increment,
  `pollid` int(4) NOT NULL default '0',
  `text` text NOT NULL,
  `hits` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `pollid` (`pollid`,`text`(1))
) TYPE=MyISAM CHARACTER SET `utf8`;

# --------------------------------------------------------

#
# Table structure for `#__xpoll_date`
#
CREATE TABLE IF NOT EXISTS `#__xpoll_date` (
  `id` bigint(20) NOT NULL auto_increment,
  `date` datetime NOT NULL default '0000-00-00 00:00:00',
  `vote_id` int(11) NOT NULL default '0',
  `poll_id` int(11) NOT NULL default '0',
  `voter_ip` varchar(50) default NULL,
  PRIMARY KEY  (`id`),
  KEY `poll_id` (`poll_id`)
) TYPE=MyISAM CHARACTER SET `utf8`;

# --------------------------------------------------------

#
# Table structure for `#__xpoll_menu`
#
CREATE TABLE IF NOT EXISTS `#__xpoll_menu` (
  `pollid` int(11) NOT NULL default '0',
  `menuid` int(11) NOT NULL default '0',
  PRIMARY KEY  (`pollid`,`menuid`)
) TYPE=MyISAM CHARACTER SET `utf8`;

# --------------------------------------------------------

#
# Table structure for `#__xpolls`
#
CREATE TABLE IF NOT EXISTS `#__xpolls` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `title` varchar(100) NOT NULL default '',
  `voters` int(9) NOT NULL default '0',
  `checked_out` int(11) NOT NULL default '0',
  `checked_out_time` datetime NOT NULL default '0000-00-00 00:00:00',
  `published` tinyint(1) NOT NULL default '0',
  `access` int(11) NOT NULL default '0',
  `lag` int(11) NOT NULL default '0',
  `open` tinyint(1) NOT NULL default '0',
  `opened` date default NULL,
  `closed` date default NULL,
  PRIMARY KEY  (`id`)
) TYPE=MyISAM CHARACTER SET `utf8`;

# --------------------------------------------------------

#
# Table structure for `#__answers_questions_log`
#
CREATE TABLE IF NOT EXISTS `#__answers_questions_log` (
  `id` int(11) NOT NULL auto_increment,
  `qid` int(11) NOT NULL default '0',
  `expires` datetime NOT NULL default '0000-00-00 00:00:00',
  `voter` int(11) default NULL,
  `ip` varchar(15) default NULL,
  PRIMARY KEY  (`id`)
) TYPE=MyISAM CHARACTER SET `utf8`;

# --------------------------------------------------------

#
# Table structure for `#__answers_log`
#
CREATE TABLE IF NOT EXISTS `#__answers_log` (
  `id` int(11) NOT NULL auto_increment,
  `rid` int(11) NOT NULL default '0',
  `ip` varchar(15) default NULL,
  `helpful` varchar(10) default NULL,
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;

# --------------------------------------------------------

#
# Table structure for `#__answers_questions`
#
CREATE TABLE IF NOT EXISTS `#__answers_questions` (
  `id` int(11) NOT NULL auto_increment,
  `subject` varchar(250) default NULL,
  `question` text,
  `created` datetime NOT NULL default '0000-00-00 00:00:00',
  `created_by` varchar(50) default NULL,
  `state` tinyint(3) NOT NULL default '0',
  `anonymous`  tinyint(2) NOT NULL default '0',
  `email` tinyint(2) default '0',
  `helpful` int(11) NULL DEFAULT '0',
  `reward` tinyint(2) NULL DEFAULT '0',
  PRIMARY KEY  (`id`),
  FULLTEXT KEY `question` (`question`),
  FULLTEXT KEY `subject` (`subject`)
) TYPE=MyISAM;
 

# --------------------------------------------------------

#
# Table structure for `#__answers_responses`
#
CREATE TABLE IF NOT EXISTS `#__answers_responses` (
  `id` int(11) NOT NULL auto_increment,
  `qid` int(11) NOT NULL default '0',
  `answer` text,
  `created_by` varchar(50) default NULL,
  `created` datetime NOT NULL default '0000-00-00 00:00:00',
  `helpful` int(11) NOT NULL default '0',
  `nothelpful` int(11) NOT NULL default '0',
  `state` tinyint(3) NOT NULL default '0',
  `anonymous` tinyint(2) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  FULLTEXT KEY `answer` (`answer`)
) TYPE=MyISAM;

# --------------------------------------------------------

#
# Table structure for `#__answers_tags`
#
CREATE TABLE IF NOT EXISTS `#__answers_tags` (
  `id` int(11) NOT NULL auto_increment,
  `questionid` int(11) NOT NULL default '0',
  `tagid` int(11) NOT NULL default '0',
  `taggerid` varchar(200) default NULL,
  `taggedon` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;

# --------------------------------------------------------

#
# Table structure for `#__comments`
#
CREATE TABLE IF NOT EXISTS `#__comments` (
   `id` int(11) NOT NULL auto_increment,
   `referenceid` varchar(11) default NULL,
   `category` varchar(50) default NULL,
   `comment` text,
   `added` datetime NOT NULL default '0000-00-00 00:00:00',
   `added_by` int(11) default NULL,
   `state` tinyint(3) NOT NULL default '0',
   `anonymous` tinyint(2) NOT NULL default '0',
   `email` tinyint(2) NOT NULL default '0',
   PRIMARY KEY  (`id`),
   FULLTEXT KEY `question` (`comment`),
   FULLTEXT KEY `subject` (`referenceid`)
) TYPE=MyISAM CHARACTER SET `utf8`;

# --------------------------------------------------------

#
# Table structure for `#__resource_assoc`
#
CREATE TABLE IF NOT EXISTS `#__resource_assoc` (
  `parent_id` int(11) NOT NULL default '0',
  `child_id` int(11) NOT NULL default '0',
  `ordering` int(11) NOT NULL default '0',
  `grouping` int(11) NOT NULL default '0'
) TYPE=MyISAM CHARACTER SET `utf8`;

# --------------------------------------------------------

#
# Table structure for `#__resource_ratings`
#
CREATE TABLE IF NOT EXISTS `#__resource_ratings` (
  `id` int(11) NOT NULL auto_increment,
  `resource_id` int(11) NOT NULL default '0',
  `user_id` int(11) NOT NULL default '0',
  `rating` decimal(2,1) NOT NULL default '0.0',
  `comment` text NOT NULL,
  `created` datetime NOT NULL default '0000-00-00 00:00:00',
  `anonymous` tinyint(3) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM CHARACTER SET `utf8`;

# --------------------------------------------------------

#
# Table structure for `#__resource_tags`
#
CREATE TABLE IF NOT EXISTS `#__resource_tags` (
  `id` int(11) NOT NULL auto_increment,
  `resourceid` int(11) default NULL,
  `tagid` int(11) default NULL,
  `strength` tinyint(3) default '0',
  `taggerid` int(11) default '0',
  `taggedon` datetime default '0000-00-00 00:00:00',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM CHARACTER SET `utf8`;

# --------------------------------------------------------

#
# Table structure for `#__resource_types`
#
CREATE TABLE IF NOT EXISTS `#__resource_types` (
  `id` int(11) NOT NULL auto_increment,
  `type` varchar(200) NOT NULL default '',
  `category` int(11) NOT NULL default '0',
  `description` tinytext,
  `contributable` int(2) default '1',
  `customFields` text,
  PRIMARY KEY  (`id`)
) TYPE=MyISAM CHARACTER SET `utf8`;

INSERT IGNORE INTO `#__resource_types` (`id`, `type`, `category`) VALUES (1,'Online Presentations',27);
INSERT IGNORE INTO `#__resource_types` (`id`, `type`, `category`) VALUES (2,'Workshops',27);
INSERT IGNORE INTO `#__resource_types` (`id`, `type`, `category`) VALUES (3,'Publications',27);
INSERT IGNORE INTO `#__resource_types` (`id`, `type`, `category`) VALUES (4,'Learning Modules',27);
INSERT IGNORE INTO `#__resource_types` (`id`, `type`, `category`) VALUES (5,'Animations',27);
INSERT IGNORE INTO `#__resource_types` (`id`, `type`, `category`) VALUES (6,'Courses',27);
INSERT IGNORE INTO `#__resource_types` (`id`, `type`, `category`) VALUES (7,'Tools',27);
INSERT IGNORE INTO `#__resource_types` (`id`, `type`, `category`) VALUES (8,'Simulation Tool Sets',-1);
INSERT IGNORE INTO `#__resource_types` (`id`, `type`, `category`) VALUES (9,'Downloads',27);
INSERT IGNORE INTO `#__resource_types` (`id`, `type`, `category`) VALUES (10,'Notes',27);
INSERT IGNORE INTO `#__resource_types` (`id`, `type`, `category`) VALUES (11,'External Link',30);
INSERT IGNORE INTO `#__resource_types` (`id`, `type`, `category`) VALUES (12,'Internal Link',30);
INSERT IGNORE INTO `#__resource_types` (`id`, `type`, `category`) VALUES (13,'File',30);
INSERT IGNORE INTO `#__resource_types` (`id`, `type`, `category`) VALUES (14,'Presentation Slides',28);
INSERT IGNORE INTO `#__resource_types` (`id`, `type`, `category`) VALUES (15,'Quicktime',30);
INSERT IGNORE INTO `#__resource_types` (`id`, `type`, `category`) VALUES (16,'Examples',29);
INSERT IGNORE INTO `#__resource_types` (`id`, `type`, `category`) VALUES (17,'Exercises',29);
INSERT IGNORE INTO `#__resource_types` (`id`, `type`, `category`) VALUES (18,'References',29);
INSERT IGNORE INTO `#__resource_types` (`id`, `type`, `category`) VALUES (19,'Presentation (without audio)',28);
INSERT IGNORE INTO `#__resource_types` (`id`, `type`, `category`) VALUES (20,'Presentation (with audio)',28);
INSERT IGNORE INTO `#__resource_types` (`id`, `type`, `category`) VALUES (21,'Sub Type',0);
INSERT IGNORE INTO `#__resource_types` (`id`, `type`, `category`) VALUES (22,'Research Seminars',21);
INSERT IGNORE INTO `#__resource_types` (`id`, `type`, `category`) VALUES (23,'Troubleshooting',29);
INSERT IGNORE INTO `#__resource_types` (`id`, `type`, `category`) VALUES (24,'How to ...',29);
INSERT IGNORE INTO `#__resource_types` (`id`, `type`, `category`) VALUES (25,'Advanced Exercises',29);
INSERT IGNORE INTO `#__resource_types` (`id`, `type`, `category`) VALUES (26,'Flash Paper',30);
INSERT IGNORE INTO `#__resource_types` (`id`, `type`, `category`) VALUES (27,'Main Types',0);
INSERT IGNORE INTO `#__resource_types` (`id`, `type`, `category`) VALUES (28,'Logical Type',0);
INSERT IGNORE INTO `#__resource_types` (`id`, `type`, `category`) VALUES (29,'Group',0);
INSERT IGNORE INTO `#__resource_types` (`id`, `type`, `category`) VALUES (30,'Type',0);
INSERT IGNORE INTO `#__resource_types` (`id`, `type`, `category`) VALUES (31,'Series',27);
INSERT IGNORE INTO `#__resource_types` (`id`, `type`, `category`) VALUES (32,'Breeze',30);
INSERT IGNORE INTO `#__resource_types` (`id`, `type`, `category`) VALUES (33,'PDF',30);
INSERT IGNORE INTO `#__resource_types` (`id`, `type`, `category`) VALUES (34,'Quiz',28);
INSERT IGNORE INTO `#__resource_types` (`id`, `type`, `category`) VALUES (35,'PowerPoint',30);
INSERT IGNORE INTO `#__resource_types` (`id`, `type`, `category`) VALUES (36,'Poster',28);
INSERT IGNORE INTO `#__resource_types` (`id`, `type`, `category`) VALUES (37,'Media Player',30);
INSERT IGNORE INTO `#__resource_types` (`id`, `type`, `category`) VALUES (38,'Package',30);
INSERT IGNORE INTO `#__resource_types` (`id`, `type`, `category`) VALUES (39,'Teaching Materials',27);
INSERT IGNORE INTO `#__resource_types` (`id`, `type`, `category`) VALUES (40,'Video Stream',28);
INSERT IGNORE INTO `#__resource_types` (`id`, `type`, `category`) VALUES (41,'Video',28);
INSERT IGNORE INTO `#__resource_types` (`id`, `type`, `category`) VALUES (44,'Nanotechnology',29);
INSERT IGNORE INTO `#__resource_types` (`id`, `type`, `category`) VALUES (45,'Chemistry',29);
INSERT IGNORE INTO `#__resource_types` (`id`, `type`, `category`) VALUES (46,'Semiconductors and Circuits',29);
INSERT IGNORE INTO `#__resource_types` (`id`, `type`, `category`) VALUES (47,'Other Tools',29);
INSERT IGNORE INTO `#__resource_types` (`id`, `type`, `category`) VALUES (48,'Tutorials',21);
INSERT IGNORE INTO `#__resource_types` (`id`, `type`, `category`) VALUES (49,'Podcast (audio)',28);
INSERT IGNORE INTO `#__resource_types` (`id`, `type`, `category`) VALUES (50,'Podcast (video)',28);
INSERT IGNORE INTO `#__resource_types` (`id`, `type`, `category`) VALUES (51,'Homework Assignment',28);
INSERT IGNORE INTO `#__resource_types` (`id`, `type`, `category`) VALUES (52,'MOS Capacitor Examples',29);
INSERT IGNORE INTO `#__resource_types` (`id`, `type`, `category`) VALUES (53,'Dual Gate Examples',29);
INSERT IGNORE INTO `#__resource_types` (`id`, `type`, `category`) VALUES (54,'Course Lectures',21);
INSERT IGNORE INTO `#__resource_types` (`id`, `type`, `category`) VALUES (55,'Ph.D. Thesis',28);
INSERT IGNORE INTO `#__resource_types` (`id`, `type`, `category`) VALUES (56,'Publication Preprint',28);
INSERT IGNORE INTO `#__resource_types` (`id`, `type`, `category`) VALUES (57,'Handout',28);
INSERT IGNORE INTO `#__resource_types` (`id`, `type`, `category`) VALUES (58,'Undergraduate Presentation',21);
INSERT IGNORE INTO `#__resource_types` (`id`, `type`, `category`) VALUES (59,'Manual',28);
INSERT IGNORE INTO `#__resource_types` (`id`, `type`, `category`) VALUES (60,'Software Download',28);
INSERT IGNORE INTO `#__resource_types` (`id`, `type`, `category`) VALUES (61,'Exercise Solutions',29);

# --------------------------------------------------------

#
# Table structure for `#__resources`
#
CREATE TABLE IF NOT EXISTS `#__resources` (
  `id` int(11) NOT NULL auto_increment,
  `title` varchar(250) NOT NULL default '',
  `type` int(11) NOT NULL default '0',
  `logical_type` int(11) NOT NULL default '0',
  `introtext` text NOT NULL,
  `fulltext` text NOT NULL,
  `footertext` text NOT NULL,
  `created` datetime NOT NULL default '0000-00-00 00:00:00',
  `created_by` int(11) NOT NULL default '0',
  `modified` datetime NOT NULL default '0000-00-00 00:00:00',
  `modified_by` int(11) NOT NULL default '0',
  `published` int(1) NOT NULL default '0',
  `publish_up` datetime NOT NULL default '0000-00-00 00:00:00',
  `publish_down` datetime NOT NULL default '0000-00-00 00:00:00',
  `access` int(11) NOT NULL default '0',
  `hits` int(11) NOT NULL default '0',
  `path` varchar(200) NOT NULL default '',
  `checked_out` int(11) NOT NULL default '0',
  `checked_out_time` datetime NOT NULL default '0000-00-00 00:00:00',
  `standalone` tinyint(1) NOT NULL default '0',
  `group_owner` varchar(250) NOT NULL default '',
  `group_access` text,
  `rating` decimal(2,1) NOT NULL default '0.0',
  `times_rated` int(11) NOT NULL default '0',
  `params` text,
  `attribs` text,
  `alias` varchar(100) NOT NULL default '',
  `ranking` float NOT NULL default '0',
  PRIMARY KEY  (`id`),
  FULLTEXT KEY `title` (`title`),
  FULLTEXT KEY `introtext` (`introtext`,`fulltext`)
) TYPE=MyISAM CHARACTER SET `utf8`;

# --------------------------------------------------------

#
# Table structure for `#__author_assoc`
#
CREATE TABLE IF NOT EXISTS `#__author_assoc` (
  `subtable` varchar(50) NOT NULL default '',
  `subid` int(11) NOT NULL default '0',
  `authorid` int(11) NOT NULL default '0',
  `ordering` int(11) default NULL,
  `role` varchar(50) default NULL,
  `name` varchar(255) default NULL,
  `organization` varchar(255) default NULL,
  PRIMARY KEY  (`subtable`,`subid`,`authorid`)
) TYPE=MyISAM CHARACTER SET `utf8`;

# --------------------------------------------------------

#
# Table structure for `#__tool`
#
CREATE TABLE IF NOT EXISTS `#__tool` (
  `id` int(10) NOT NULL auto_increment,
  `toolname` varchar(15) NOT NULL default '',
  `title` varchar(127) NOT NULL default '',
  `version` varchar(15) default NULL,
  `description` text,
  `fulltext` text,
  `license` text,
  `toolaccess` varchar(15) default NULL,
  `codeaccess` varchar(15) default NULL,
  `wikiaccess` varchar(15) default NULL,
  `published` tinyint(1) default '0',
  `state` int(15) default NULL,
  `priority` int(15) default '3',
  `team` text,
  `registered` datetime default NULL,
  `registered_by` varchar(31) default NULL,
  `mw` varchar(31) default NULL,
  `vnc_geometry` varchar(31) default NULL,
  `ticketid` int(15) default NULL,
  `state_changed` datetime default '0000-00-00 00:00:00',
  `revision` int(11) default NULL,
  PRIMARY KEY  (`id`)
) TYPE=MyISAM CHARACTER SET `utf8`;

# --------------------------------------------------------

#
# Table structure for `#__resource_stats`
#
CREATE TABLE IF NOT EXISTS `#__resource_stats` (
  `id` bigint(20) NOT NULL auto_increment,
  `resid` bigint(20) NOT NULL,
  `restype` int(11) NOT NULL,
  `users` bigint(20) default NULL,
  `jobs` bigint(20) default NULL,
  `avg_wall` int(20) default NULL,
  `tot_wall` int(20) default NULL,
  `avg_cpu` int(20) default NULL,
  `tot_cpu` int(20) default NULL,
  `datetime` datetime NOT NULL default '0000-00-00 00:00:00',
  `period` tinyint(4) NOT NULL default '-1',
  UNIQUE KEY `id` (`id`)
) TYPE=MyISAM CHARACTER SET `utf8`;

# --------------------------------------------------------

#
# Table structure for `#__resource_stats_tools`
#
CREATE TABLE IF NOT EXISTS `#__resource_stats_tools` (
  `id` bigint(20) NOT NULL auto_increment,
  `resid` bigint(20) NOT NULL,
  `restype` int(11) NOT NULL,
  `users` bigint(20) default NULL,
  `sessions` bigint(20) default NULL,
  `simulations` bigint(20) default NULL,
  `jobs` bigint(20) default NULL,
  `avg_wall` double unsigned default '0',
  `tot_wall` double unsigned default '0',
  `avg_cpu` double unsigned default '0',
  `tot_cpu` double unsigned default '0',
  `avg_view` double unsigned default '0',
  `tot_view` double unsigned default '0',
  `avg_wait` double unsigned default '0',
  `tot_wait` double unsigned default '0',
  `avg_cpus` int(20) default NULL,
  `tot_cpus` int(20) default NULL,
  `datetime` datetime NOT NULL default '0000-00-00 00:00:00',
  `period` tinyint(4) NOT NULL default '-1',
  UNIQUE KEY `id` (`id`)
) TYPE=MyISAM CHARACTER SET `utf8`;

# --------------------------------------------------------

#
# Table structure for `#__resource_stats_tools_tops`
#
CREATE TABLE IF NOT EXISTS `#__resource_stats_tools_tops` (
  `top` tinyint(4) NOT NULL default '0',
  `name` varchar(128) NOT NULL default '',
  `valfmt` tinyint(4) NOT NULL default '0',
  `size` tinyint(4) NOT NULL default '0',
  PRIMARY KEY  (`top`)
) TYPE=MyISAM CHARACTER SET `utf8`;

# --------------------------------------------------------

#
# Table structure for `#__resource_stats_tools_topvals`
#
CREATE TABLE IF NOT EXISTS `#__resource_stats_tools_topvals` (
  `id` bigint(20) NOT NULL,
  `top` tinyint(4) NOT NULL default '0',
  `rank` tinyint(4) NOT NULL default '0',
  `name` varchar(255) default NULL,
  `value` bigint(20) NOT NULL default '0'
) TYPE=MyISAM CHARACTER SET `utf8`;

# --------------------------------------------------------

#
# Table structure for `#__resource_stats_tools_users`
#
CREATE TABLE IF NOT EXISTS `#__resource_stats_tools_users` (
  `id` bigint(20) NOT NULL auto_increment,
  `resid` bigint(20) NOT NULL,
  `restype` int(11) NOT NULL,
  `user` varchar(32) NOT NULL default '',
  `sessions` bigint(20) default NULL,
  `simulations` bigint(20) default NULL,
  `jobs` bigint(20) default NULL,
  `tot_wall` double unsigned default '0',
  `tot_cpu` double unsigned default '0',
  `tot_view` double unsigned default '0',
  `datetime` datetime NOT NULL default '0000-00-00 00:00:00',
  `period` tinyint(4) NOT NULL default '-1',
  UNIQUE KEY `id` (`id`)
) TYPE=MyISAM CHARACTER SET `utf8`;

# --------------------------------------------------------

#
# Table structure for `#__redirection`
#
CREATE TABLE IF NOT EXISTS `#__redirection` (
`id` int(11) NOT NULL auto_increment,
`cpt` int(11) NOT NULL default '0',
`oldurl` varchar(100) NOT NULL default '',
`newurl` varchar(150) NOT NULL default '',
`dateadd` date NOT NULL default '0000-00-00',
PRIMARY KEY  (`id`),
KEY `newurl` (`newurl`)
) TYPE=MyISAM DEFAULT CHARSET=utf8;

# --------------------------------------------------------

#
# Table structure for `#__wishlist`
#
CREATE TABLE IF NOT EXISTS `#__wishlist` ( 
	id         	int(11) AUTO_INCREMENT NOT NULL,
	category   	varchar(50) NOT NULL,
	referenceid	int(11) NOT NULL DEFAULT '0',
	title      	varchar(150) NOT NULL,
	created_by 	int(11) NOT NULL DEFAULT '0',
	created    	datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
	state      	int(3) NOT NULL DEFAULT '0',
	public     	int(3) NOT NULL DEFAULT '1',
	description	varchar(255) NULL,
	type		int(11) NULL DEFAULT '0',
	PRIMARY KEY(id)
) TYPE=MyISAM CHARACTER SET `utf8`;

# --------------------------------------------------------

#
# Table structure for `#__wishlist_implementation`
#
CREATE TABLE IF NOT EXISTS `#__wishlist_implementation` ( 
	id        	int(11) AUTO_INCREMENT NOT NULL,
	wishid    	int(11) NOT NULL DEFAULT '0',
	version   	int(11) NOT NULL DEFAULT '0',
	created   	datetime NULL,
	created_by	int(11) NOT NULL DEFAULT '0',
	minor_edit	int(1) NOT NULL DEFAULT '0',
	pagetext  	text NULL,
	pagehtml  	text NULL,
	approved  	int(1) NOT NULL DEFAULT '0',
	summary   	varchar(255) NULL,
	FULLTEXT KEY `pagetext` (`pagetext`),
	PRIMARY KEY(id)
) TYPE=MyISAM CHARACTER SET `utf8`;

# --------------------------------------------------------

#
# Table structure for `#__wishlist_item`
#
CREATE TABLE IF NOT EXISTS `#__wishlist_item` ( 
	id         	int(11) AUTO_INCREMENT NOT NULL,
	wishlist   	int(11) NULL DEFAULT '0',
	subject    	varchar(200) NOT NULL,
	about      	text NULL,
	proposed_by	int(11) NULL DEFAULT '0',
	granted_by 	int(11) NULL DEFAULT '0',
	assigned   	int(11) NULL DEFAULT '0',
	granted_vid	int(11) NULL DEFAULT '0',
	proposed   	datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
	granted    	datetime NULL DEFAULT '0000-00-00 00:00:00',
	status     	int(3) NOT NULL DEFAULT '0',
	due        	datetime NULL DEFAULT '0000-00-00 00:00:00',
	anonymous  	int(3) NULL DEFAULT '0',
	ranking    	int(11) NULL DEFAULT '0',
	points     	int(11) NULL DEFAULT '0',
	private    	int(3) NULL DEFAULT '0',
	accepted   	int(3) NULL DEFAULT '0',
	PRIMARY KEY(id)
) TYPE=MyISAM CHARACTER SET `utf8`;

# --------------------------------------------------------

#
# Table structure for `#__wishlist_ownergroups`
#
CREATE TABLE IF NOT EXISTS `#__wishlist_ownergroups` ( 
	id      	int(11) AUTO_INCREMENT NOT NULL,
	wishlist	int(11) NULL DEFAULT '0',
	groupid 	int(11) NULL DEFAULT '0',
	PRIMARY KEY(id)
) TYPE=MyISAM CHARACTER SET `utf8`;

# --------------------------------------------------------

#
# Table structure for `#__wishlist_owners`
#
CREATE TABLE IF NOT EXISTS `#__wishlist_owners` ( 
	id      	int(11) AUTO_INCREMENT NOT NULL,
	wishlist	int(11) NULL DEFAULT '0',
	userid  	int(11) NOT NULL DEFAULT '0',
        type		int(11) NULL DEFAULT '0',
	PRIMARY KEY(id)
) TYPE=MyISAM CHARACTER SET `utf8`;

# --------------------------------------------------------

#
# Table structure for `#__wishlist_vote`
#
CREATE TABLE IF NOT EXISTS `#__wishlist_vote` ( 
	id        	int(11) AUTO_INCREMENT NOT NULL,
	wishid    	int(11) NULL DEFAULT '0',
	userid    	int(11) NOT NULL DEFAULT '0',
	voted     	datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
	importance	int(3) NULL DEFAULT '0',
	effort    	int(3) NULL DEFAULT '0',
	due       	datetime NULL DEFAULT '0000-00-00 00:00:00',
	PRIMARY KEY(id)
) TYPE=MyISAM CHARACTER SET `utf8`;

CREATE TABLE #__wish_attachments ( 
	id         	int(11) AUTO_INCREMENT NOT NULL,
	wish       	int(11) NOT NULL DEFAULT '0',
	filename   	varchar(255) NULL,
	description	varchar(255) NULL,
	PRIMARY KEY(id)
) TYPE=MyISAM CHARACTER SET `utf8`;

# --------------------------------------------------------

#
# Table structure for `#__feedback`
#
CREATE TABLE IF NOT EXISTS `#__feedback` (
  `id` int(11) NOT NULL auto_increment,
  `userid` int(11) default NULL,
  `fullname` varchar(100) default '',
  `org` varchar(100) default '',
  `quote` text,
  `picture` varchar(250) default '',
  `date` datetime default '0000-00-00 00:00:00',
  `publish_ok` tinyint(1) default '0',
  `contact_ok` tinyint(1) default '0',
  `notes` text,
  `short_quote` text,
  PRIMARY KEY  (`id`)
) TYPE=MyISAM CHARACTER SET `utf8`;

# --------------------------------------------------------

#
# Table structure for `#__selected_quotes`
#
CREATE TABLE IF NOT EXISTS `#__selected_quotes` (
  `id` int(11) NOT NULL auto_increment,
  `userid` int(11) default '0',
  `fullname` varchar(100) default '',
  `org` varchar(200) default '',
  `short_quote` text,
  `quote` text,
  `picture` varchar(250) default '',
  `date` datetime default '0000-00-00 00:00:00',
  `flash_rotation` tinyint(1) default '0',
  `notable_quotes` tinyint(1) default '1',
  `notes` text,
  PRIMARY KEY  (`id`)
) TYPE=MyISAM CHARACTER SET `utf8`;

# --------------------------------------------------------

#
# Table structure for `#__citations`
#
CREATE TABLE IF NOT EXISTS `#__citations` (
  `id` int(11) NOT NULL auto_increment,
  `uid` varchar(200) default NULL,
  `affiliated` int(3) NOT NULL default '0',
  `fundedby` int(3) NOT NULL default '0',
  `created` datetime NOT NULL default '0000-00-00 00:00:00',
  `address` varchar(250) default NULL,
  `author` text,
  `booktitle` varchar(250) default NULL,
  `chapter` varchar(250) default NULL,
  `cite` varchar(250) default NULL,
  `edition` varchar(250) default NULL,
  `editor` varchar(250) default NULL,
  `eprint` varchar(250) default NULL,
  `howpublished` varchar(250) default NULL,
  `institution` varchar(250) default NULL,
  `isbn` varchar(50) default NULL,
  `journal` varchar(250) default NULL,
  `key` varchar(250) default NULL,
  `location` varchar(250) default NULL,
  `month` varchar(50) default NULL,
  `note` text,
  `number` varchar(50) default NULL,
  `organization` varchar(250) default NULL,
  `pages` varchar(250) default NULL,
  `publisher` varchar(250) default NULL,
  `series` varchar(250) default NULL,
  `school` varchar(250) default NULL,
  `title` varchar(250) default NULL,
  `type` varchar(30) default NULL,
  `url` varchar(250) default NULL,
  `volume` int(11) default NULL,
  `year` int(4) default NULL,
  `doi` varchar(250) default NULL,
  `ref_type` varchar(50) default NULL,
  `date_submit` datetime NOT NULL default '0000-00-00 00:00:00',
  `date_accept` datetime NOT NULL default '0000-00-00 00:00:00',
  `date_publish` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM CHARACTER SET `utf8`;

# --------------------------------------------------------

#
# Table structure for `#__citations_assoc`
#
CREATE TABLE IF NOT EXISTS `#__citations_assoc` (
  `id` int(11) NOT NULL auto_increment,
  `cid` int(11) default '0',
  `oid` int(11) default '0',
  `type` varchar(50) default NULL,
  `table` varchar(50) default NULL,
  PRIMARY KEY  (`id`)
) TYPE=MyISAM CHARACTER SET `utf8`;

# --------------------------------------------------------

#
# Table structure for `#__citations_authors`
#
CREATE TABLE IF NOT EXISTS `#__citations_authors` (
  `id` int(11) NOT NULL auto_increment,
  `cid` int(11) default '0',
  `author` varchar(64) default NULL,
  `author_uid` bigint(20) default NULL,
  `ordering` int(11) NOT NULL default '0',
  `givenName`      	varchar(255) NOT NULL,
  `middleName`     	varchar(255) NOT NULL,
  `surname`        	varchar(255) NOT NULL,
  `organization`   	varchar(255) NOT NULL,
  `orgtype`        	varchar(255) NOT NULL,
  `countryresident`	char(2) NOT NULL,
  `email`          	varchar(100) NOT NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `cid_auth_uid` (`cid`,`author`,`author_uid`)
) TYPE=MyISAM CHARACTER SET `utf8`;

# --------------------------------------------------------

#
# Table structure for `#__tags`
#
CREATE TABLE IF NOT EXISTS `#__tags` (
  `id` int(11) NOT NULL auto_increment,
  `tag` varchar(100) default NULL,
  `raw_tag` varchar(100) default NULL,
  `alias` varchar(100) default NULL,
  `description` text,
  `admin` tinyint(3) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  FULLTEXT KEY `description` (`description`)
) TYPE=MyISAM DEFAULT CHARSET utf8;

# --------------------------------------------------------

#
# Table structure for `#__tags_object`
#
CREATE TABLE IF NOT EXISTS `#__tags_object` (
  `id` int(11) NOT NULL auto_increment,
  `objectid` int(11) default NULL,
  `tagid` int(11) default NULL,
  `strength` tinyint(3) default '0',
  `taggerid` int(11) default '0',
  `taggedon` datetime default '0000-00-00 00:00:00',
  `tbl` varchar(255) default NULL,
  PRIMARY KEY  (`id`)
) TYPE=MyISAM DEFAULT CHARSET utf8;

# --------------------------------------------------------

#
# Table structure for `#__abuse_reports`
#
CREATE TABLE IF NOT EXISTS `#__abuse_reports` (
  `id` int(11) NOT NULL auto_increment,
  `category` varchar(50) default NULL,
  `referenceid` int(11) default '0',
  `report` text NOT NULL,
  `created_by` int(11) NOT NULL default '0',
  `created` datetime NOT NULL default '0000-00-00 00:00:00',
  `state` int(3) default '0',
  `subject` varchar(150) default NULL,
  PRIMARY KEY  (`id`)
) TYPE=MyISAM CHARACTER SET `utf8`;

# --------------------------------------------------------

#
# Table structure for `#__support_attachements`
#
CREATE TABLE IF NOT EXISTS `#__support_attachments` (
  `id` int(11) NOT NULL auto_increment,
  `ticket` int(11) NOT NULL default '0',
  `filename` varchar(255) default NULL,
  `description` varchar(255) default NULL,
  PRIMARY KEY  (`id`)
) TYPE=MyISAM CHARACTER SET `utf8`;

# --------------------------------------------------------

#
# Table structure for `#__support_categories`
#
CREATE TABLE IF NOT EXISTS `#__support_categories` (
  `id` int(11) NOT NULL auto_increment,
  `section` int(11) default 0,
  `category` varchar(50) default NULL,
  PRIMARY KEY  (`id`)
) TYPE=MyISAM DEFAULT CHARSET=utf8;

# --------------------------------------------------------

#
# Table structure for `#__support_comments`
#
CREATE TABLE IF NOT EXISTS `#__support_comments` (
  `id` int(11) NOT NULL auto_increment,
  `ticket` int(11) NOT NULL default '0',
  `comment` text,
  `created` datetime NOT NULL default '0000-00-00 00:00:00',
  `created_by` varchar(50) default NULL,
  `changelog` text,
  `access` tinyint(3) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM DEFAULT CHARSET=utf8;

# --------------------------------------------------------

#
# Table structure for `#__support_messages`
#
CREATE TABLE IF NOT EXISTS `#__support_messages` (
  `id` int(11) NOT NULL auto_increment,
  `title` varchar(250) default NULL,
  `message` text,
  PRIMARY KEY  (`id`)
) TYPE=MyISAM DEFAULT CHARSET=utf8;

# --------------------------------------------------------

#
# Table structure for `#__support_resolutions`
#
CREATE TABLE IF NOT EXISTS `#__support_resolutions` (
  `id` int(11) NOT NULL auto_increment,
  `title` varchar(100) default NULL,
  `alias` varchar(100) default NULL,
  PRIMARY KEY  (`id`)
) TYPE=MyISAM CHARACTER SET `utf8`;

# --------------------------------------------------------

#
# Table structure for `#__support_sections`
#
CREATE TABLE IF NOT EXISTS `#__support_sections` (
  `id` int(11) NOT NULL auto_increment,
  `section` varchar(50) default NULL,
  PRIMARY KEY  (`id`)
) TYPE=MyISAM DEFAULT CHARSET=utf8;

# --------------------------------------------------------

#
# Table structure for `#__support_tags`
#
CREATE TABLE IF NOT EXISTS `#__support_tags` (
  `id` int(11) NOT NULL auto_increment,
  `ticketid` int(11) default NULL,
  `tagid` int(11) default NULL,
  `strength` tinyint(3) default '0',
  `taggerid` int(11) default '0',
  `taggedon` datetime default '0000-00-00 00:00:00',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM CHARACTER SET `utf8`;

# --------------------------------------------------------

#
# Table structure for `#__support_attachements`
#
CREATE TABLE IF NOT EXISTS `#__support_attachments` (
  `id` int(11) NOT NULL auto_increment,
  `ticket` int(11) NOT NULL default 0,
  `filename` varchar(255) default NULL,
  `description` varchar(255) default NULL,
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;

# --------------------------------------------------------

#
# Table structure for `#__support_tickets`
#
CREATE TABLE IF NOT EXISTS `#__support_tickets` (
  `id` int(11) NOT NULL auto_increment,
  `status` tinyint(3) default '0',
  `created` datetime default '0000-00-00 00:00:00',
  `login` varchar(200) default NULL,
  `severity` varchar(30) default NULL,
  `owner` varchar(50) default NULL,
  `category` varchar(50) default NULL,
  `summary` varchar(250) default NULL,
  `report` text,
  `resolved` varchar(50) default NULL,
  `email` varchar(200) default NULL,
  `name` varchar(200) default NULL,
  `os` varchar(50) default NULL,
  `browser` varchar(50) default NULL,
  `ip` varchar(200) default NULL,
  `hostname` varchar(200) default NULL,
  `uas` varchar(250) default NULL,
  `referrer` varchar(250) default NULL,
  `cookies` tinyint(3) NOT NULL default '0',
  `instances` int(11) NOT NULL default '1',
  `section` int(11) NOT NULL default '1',
  `type` tinyint(3) NOT NULL default '0',
  `group` varchar(250) NULL,
  PRIMARY KEY  (`id`)
) TYPE=MyISAM DEFAULT CHARSET=utf8;

# --------------------------------------------------------

#
# Table structure for `#__support_resolutions`
#
CREATE TABLE IF NOT EXISTS `#__support_resolutions` (
  `id` int(11) NOT NULL auto_increment,
  `title` varchar(100) default NULL,
  `alias` varchar(100) default NULL,
  PRIMARY KEY  (`id`)
) TYPE=MyISAM CHARACTER SET `utf8`;

# --------------------------------------------------------

#
# Table structure for `#__xdomains`
#
CREATE TABLE IF NOT EXISTS `#__xdomains` (
  `domain_id` int(11) NOT NULL auto_increment,
  `domain` varchar(150) NOT NULL default '',
  PRIMARY KEY (`domain_id`)
) TYPE=MyISAM CHARACTER SET `utf8`;

# --------------------------------------------------------

#
# Table structure for `#__xdomain_users`
#
CREATE TABLE IF NOT EXISTS `#__xdomain_users` (
  `domain_id` int(11) NOT NULL,
  `domain_username` varchar(150) NOT NULL default '',
  `uidNumber` int(11) default NULL,
  PRIMARY KEY (`domain_id`,`domain_username`)
) TYPE=MyISAM CHARACTER SET `utf8`;

# --------------------------------------------------------

#
# Table structure for `#__xprofiles`
#
CREATE TABLE IF NOT EXISTS `#__xprofiles` (
	`uidNumber` int(11) NOT NULL,
	`name` varchar(255) NOT NULL default '',
	`username` varchar(150) NOT NULL default '', 
	`email` varchar(100) NOT NULL default '',
	`registerDate` datetime NOT NULL default '0000-00-00 00:00:00',
	`gidNumber` varchar(11) NOT NULL default '',
	`homeDirectory` varchar(255) NOT NULL default '',
	`loginShell` varchar(255) NOT NULL default '',
	`ftpShell` varchar(255) NOT NULL default '',
	`userPassword` varchar(255) NOT NULL default '',
	`gid` varchar(255) NOT NULL default '',
	`orgtype` varchar(255) NOT NULL default '',
	`organization` varchar(255) NOT NULL default '',
	`countryresident` char(2) NOT NULL default '',
	`countryorigin` char(2) NOT NULL default '',
	`gender` varchar(255) NOT NULL default '',
	`url` varchar(255) NOT NULL default '',
	`reason` text NOT NULL default '',
	`mailPreferenceOption` int(11) NOT NULL default 0,
	`usageAgreement` int(11) NOT NULL default 0,
	`jobsAllowed` int(11) NOT NULL default 0,
	`modifiedDate` datetime NOT NULL default '0000-00-00 00:00:00',
	`emailConfirmed` int(11) NOT NULL default 0,
	`regIP` varchar(255) NOT NULL default '',
	`regHost` varchar(255) NOT NULL default '',
	`nativeTribe` varchar(255) NOT NULL default '',
	`phone` varchar(255) NOT NULL default '', 
	`proxyPassword` varchar(255) NOT NULL default '',
	`proxyUidNumber` varchar(255) NOT NULL default '',
	`givenName` varchar(255) NOT NULL default '',
	`middleName` varchar(255) NOT NULL default '',
	`surname` varchar(255) NOT NULL default '',
	`picture` varchar(255) NOT NULL default '',
	`vip` int(11) NOT NULL default 0,
	`public` tinyint(2) NOT NULL default 0,
	`params` text NOT NULL default '',
	`note` text NOT NULL default '',
	`shadowExpire` int(11) NULL,
	PRIMARY KEY (`uidNumber`),
	FULLTEXT KEY `author` (`givenName`,`surname`),
	KEY (`username`)
) TYPE=MyISAM CHARACTER SET `utf8`;

# --------------------------------------------------------

#
# Table structure for `#__xprofiles_bio`
#
CREATE TABLE IF NOT EXISTS `#__xprofiles_bio` (
	`uidNumber` int(11) NOT NULL,
	`bio` text,
	PRIMARY KEY (`uidNumber`)
) TYPE=MyISAM CHARACTER SET `utf8`;

# --------------------------------------------------------

#
# Table structure for `#__xprofiles_role`
#
CREATE TABLE IF NOT EXISTS `#__xprofiles_role` (
	`uidNumber` int(11) NOT NULL,
	`role` varchar(255) default '',
	PRIMARY KEY (`uidNumber`,`role`)
) TYPE=MyISAM CHARACTER SET `utf8`;

# --------------------------------------------------------

#
# Table structure for `#__xprofiles_admin`
#
CREATE TABLE IF NOT EXISTS `#__xprofiles_admin` (
	`uidNumber` int(11) NOT NULL,
	`admin` varchar(255) default '',
	PRIMARY KEY (`uidNumber`,`admin`)
) TYPE=MyISAM CHARACTER SET `utf8`;

# --------------------------------------------------------

#
# Table structure for `#__xprofiles_host`
#
CREATE TABLE IF NOT EXISTS `#__xprofiles_host` (
	`uidNumber` int (11) NOT NULL,
	`host` varchar(255) default '',
	PRIMARY KEY (`uidNumber`,`host`)
) TYPE=MyISAM CHARACTER SET `utf8`;

# --------------------------------------------------------

#
# Table structure for `#__xprofiles_disability`
#
CREATE TABLE IF NOT EXISTS `#__xprofiles_disability` (
	`uidNumber` int (11) NOT NULL,
	`disability` varchar(255) default '',
	PRIMARY KEY (`uidNumber`,`disability`)
) TYPE=MyISAM CHARACTER SET `utf8`;

# --------------------------------------------------------

#
# Table structure for `#__xprofiles_race`
#
CREATE TABLE IF NOT EXISTS `#__xprofiles_race` (
	`uidNumber` int (11) NOT NULL,
	`race` varchar(255) default '',
	PRIMARY KEY (`uidNumber`,`race`)
) TYPE=MyISAM CHARACTER SET `utf8`;

# --------------------------------------------------------

#
# Table structure for `#__xprofiles_hispanic`
#
CREATE TABLE IF NOT EXISTS `#__xprofiles_hispanic` (
	`uidNumber` int(11) NOT NULL,
	`hispanic` varchar(255) NOT NULL,
	PRIMARY KEY (`uidNumber`,`hispanic`)
) TYPE=MyISAM CHARACTER SET `utf8`;

# --------------------------------------------------------

#
# Table structure for `#__xprofiles_edulevel`
#
CREATE TABLE IF NOT EXISTS `#__xprofiles_edulevel` (
	`uidNumber` int(11) NOT NULL,
	`edulevel` varchar(255) default '',
	PRIMARY KEY (`uidNumber`,`edulevel`)
) TYPE=MyISAM CHARACTER SET `utf8`;

# --------------------------------------------------------

#
# Table structure for `#__xprofiles_tags`
#
CREATE TABLE IF NOT EXISTS `#__xprofiles_tags` (
  `id` int(11) NOT NULL auto_increment,
  `uidNumber` int(11) default NULL,
  `tagid` int(11) default NULL,
  `taggerid` int(11) default '0',
  `taggedon` datetime default '0000-00-00 00:00:00',
  PRIMARY KEY  (`id`)
)TYPE=MyISAM CHARACTER SET `utf8`;

# --------------------------------------------------------

#
# Table structure for `#__xprofiles_manager`
#
CREATE TABLE IF NOT EXISTS `#__xprofiles_manager` (
  `uidNumber` int(11) NOT NULL,
  `manager` varchar(255) NOT NULL,
  PRIMARY KEY(uidNumber,manager)
) TYPE=MyISAM CHARACTER SET `utf8`;

# --------------------------------------------------------

#
# Table structure for `#__users_points`
#
CREATE TABLE IF NOT EXISTS `#__users_points` (
  `id` int(11) NOT NULL auto_increment,
  `uid` int(11) NOT NULL default '0',
  `balance` int(11) NOT NULL default '0',
  `earnings` int(11) NOT NULL default '0',
  `credit` int(11) default '0',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM CHARACTER SET `utf8`;

# --------------------------------------------------------

#
# Table structure for `#__users_points_config`
#
CREATE TABLE IF NOT EXISTS `#__users_points_config` (
  `id` int(11) NOT NULL auto_increment,
  `points` int(11) default '0',
  `description` varchar(255) default NULL,
  `alias` varchar(50) default NULL,
  PRIMARY KEY  (`id`)
) TYPE=MyISAM CHARACTER SET `utf8`;

# --------------------------------------------------------

#
# Table structure for `#__users_transactions`
#
CREATE TABLE IF NOT EXISTS `#__users_transactions` (
  `id` int(11) NOT NULL auto_increment,
  `uid` int(11) NOT NULL default '0',
  `type` varchar(20) default NULL,
  `description` varchar(250) default NULL,
  `created` datetime NOT NULL default '0000-00-00 00:00:00',
  `category` varchar(50) default NULL,
  `referenceid` int(11) default '0',
  `amount` int(11) default '0',
  `balance` int(11) default '0',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM CHARACTER SET `utf8`;

# --------------------------------------------------------

#
# Table structure for `#__vote_log`
#
CREATE TABLE IF NOT EXISTS `#__vote_log` (
  `id` int(11) NOT NULL auto_increment,
  `referenceid` int(11) NOT NULL default '0',
  `voted` datetime NOT NULL default '0000-00-00 00:00:00',
  `voter` int(11) default NULL,
  `helpful` varchar(11) default NULL,
  `ip` varchar(15) default NULL,
  `category` varchar(50) default NULL,
  PRIMARY KEY  (`id`)
) TYPE=MyISAM CHARACTER SET `utf8`;

# --------------------------------------------------------

#
# Table structure for `#__xsession`
#
CREATE TABLE IF NOT EXISTS `#__xsession` (
  `session_id` varchar(200) NOT NULL default '0',
  `ip` varchar(15) default NULL,
  `host` varchar(128) default NULL,
  `domain` varchar(128) default NULL,
  `signed` tinyint(3) default '0',
  `countrySHORT` char(2) default NULL,
  `countryLONG` varchar(64) default NULL,
  `ipREGION` varchar(128) default NULL,
  `ipCITY` varchar(128) default NULL,
  `ipLATITUDE` double default NULL,
  `ipLONGITUDE` double default NULL,
  `bot` tinyint(4) default '0',
  PRIMARY KEY  (`session_id`),
  KEY `ip` (`ip`)
) TYPE=MyISAM CHARACTER SET `utf8`;

# --------------------------------------------------------

#
# Table structure for `#__xfavorites`
#
CREATE TABLE IF NOT EXISTS `#__xfavorites` (
  `id` int(11) NOT NULL auto_increment,
  `uid` int(11) default '0',
  `oid` int(11) default '0',
  `tbl` varchar(250) default NULL,
  `faved` datetime default '0000-00-00 00:00:00',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM CHARACTER SET `utf8`;

# --------------------------------------------------------

#
# Table structure for `#__xorganizations`
#
CREATE TABLE IF NOT EXISTS `#__xorganizations` ( 
	id          	int(11) AUTO_INCREMENT NOT NULL,
	organization	varchar(255) NULL,
	PRIMARY KEY(id)
) TYPE=MyISAM CHARACTER SET `utf8`;

# --------------------------------------------------------

#
# Table structure for `app`
#
CREATE TABLE IF NOT EXISTS `app` (
  `appname` varchar(80) NOT NULL default '',
  `geometry` varchar(9) NOT NULL default '',
  `depth` smallint(5) unsigned NOT NULL default '16',
  `hostreq` bigint(20) unsigned NOT NULL default '0',
  `userreq` bigint(20) unsigned NOT NULL default '0',
  `timeout` int(10) unsigned NOT NULL default '0',
  `command` varchar(255) NOT NULL default '',
  `description` varchar(255) NOT NULL default ''
) TYPE=MyISAM CHARACTER SET `utf8`;

# --------------------------------------------------------

#
# Table structure for `displau``
#
CREATE TABLE IF NOT EXISTS `display` (
  `hostname` varchar(40) NOT NULL default '',
  `dispnum` int(10) unsigned default '0',
  `geometry` varchar(9) NOT NULL default '',
  `depth` smallint(5) unsigned NOT NULL default '16',
  `sessnum` bigint(20) unsigned default '0',
  `vncpass` varchar(16) NOT NULL default '',
  `status` varchar(20) NOT NULL default ''
) TYPE=MyISAM CHARACTER SET `utf8`;

# --------------------------------------------------------

#
# Table structure for `fileperm`
#
CREATE TABLE IF NOT EXISTS `fileperm` (
  `sessnum` bigint(20) unsigned NOT NULL default '0',
  `fileuser` varchar(32) NOT NULL default '',
  `fwhost` varchar(40) NOT NULL default '',
  `fwport` smallint(5) unsigned NOT NULL default '0',
  `cookie` varchar(16) NOT NULL default '',
  PRIMARY KEY  (`sessnum`,`fileuser`)
) TYPE=MyISAM CHARACTER SET `utf8`;

# --------------------------------------------------------

#
# Table structure for `host`
#
CREATE TABLE IF NOT EXISTS `host` (
  `hostname` varchar(40) NOT NULL default '',
  `provisions` bigint(20) unsigned NOT NULL default '0',
  `status` varchar(20) NOT NULL default '',
  `uses` smallint(5) unsigned NOT NULL default '0',
  `portbase` int(11) NOT NULL default '0'
) TYPE=MyISAM CHARACTER SET `utf8`;

# --------------------------------------------------------

#
# Table structure for `hosttype`
#
CREATE TABLE IF NOT EXISTS `hosttype` (
  `name` varchar(40) NOT NULL default '',
  `value` bigint(20) unsigned NOT NULL default '0',
  `description` varchar(255) NOT NULL default '',
  PRIMARY KEY (`name`)
) TYPE=MyISAM CHARACTER SET `utf8`;

# --------------------------------------------------------

#
# Table structure for `job`
#
CREATE TABLE IF NOT EXISTS `job` (
  `sessnum` bigint(20) unsigned NOT NULL default '0',
  `jobid` bigint(20) unsigned NOT NULL auto_increment,
  `superjob` bigint(20) unsigned NOT NULL default '0',
  `event` varchar(40) NOT NULL default '',
  `ncpus` smallint(5) unsigned NOT NULL default '0',
  `venue` varchar(80) NOT NULL default '',
  `start` datetime NOT NULL default '0000-00-00 00:00:00',
  `heartbeat` datetime NOT NULL default '0000-00-00 00:00:00',
  UNIQUE KEY `jobid` (`jobid`)
) TYPE=MyISAM CHARACTER SET `utf8`;

# --------------------------------------------------------

#
# Table structure for `joblog`
#
CREATE TABLE IF NOT EXISTS `joblog` (
  `sessnum` bigint(20) unsigned NOT NULL default '0',
  `job` int(10) unsigned NOT NULL default '0',
  `superjob` bigint(20) unsigned NOT NULL default '0',
  `event` varchar(40) NOT NULL default '',
  `start` datetime NOT NULL default '0000-00-00 00:00:00',
  `walltime` float unsigned default '0',
  `cputime` float unsigned default '0',
  `ncpus` smallint(5) unsigned NOT NULL default '0',
  `status` smallint(5) unsigned default '0',
  `venue` varchar(80) NOT NULL default '',
  PRIMARY KEY  (`sessnum`,`job`,`event`),
  KEY `sessnum` (`sessnum`),
  KEY `event` (`event`)
) TYPE=MyISAM CHARACTER SET `utf8`;

# --------------------------------------------------------

#
# Table structure for `session`
#
CREATE TABLE IF NOT EXISTS `session` (
  `sessnum` bigint(20) unsigned NOT NULL auto_increment,
  `username` varchar(32) NOT NULL default '',
  `remoteip` varchar(40) NOT NULL default '',
  `exechost` varchar(40) NOT NULL default '',
  `dispnum` int(10) unsigned default '0',
  `start` datetime NOT NULL default '0000-00-00 00:00:00',
  `accesstime` datetime NOT NULL default '0000-00-00 00:00:00',
  `timeout` int(11) default '86400',
  `appname` varchar(80) NOT NULL default '',
  `sessname` varchar(80) NOT NULL default '',
  `sesstoken` varchar(32) NOT NULL default '',
  PRIMARY KEY  (`sessnum`),
  UNIQUE KEY `sessnum` (`sessnum`)
) TYPE=MyISAM CHARACTER SET `utf8`;

# --------------------------------------------------------

#
# Table structure for `sessionlog`
#
CREATE TABLE IF NOT EXISTS `sessionlog` (
  `sessnum` bigint(20) unsigned NOT NULL auto_increment,
  `username` varchar(32) NOT NULL default '',
  `remoteip` varchar(40) NOT NULL default '',
  `remotehost` varchar(40) NOT NULL default '',
  `exechost` varchar(40) NOT NULL default '',
  `dispnum` int(10) unsigned default '0',
  `start` datetime NOT NULL default '0000-00-00 00:00:00',
  `appname` varchar(80) NOT NULL default '',
  `walltime` float unsigned default '0',
  `viewtime` float unsigned default '0',
  `cputime` float unsigned default '0',
  `status` smallint(5) unsigned default '0',
  PRIMARY KEY  (`sessnum`),
  UNIQUE KEY `sessnum` (`sessnum`)
) TYPE=MyISAM CHARACTER SET `utf8`;

# --------------------------------------------------------

#
# Table structure for `sessionpriv`
#
CREATE TABLE IF NOT EXISTS `sessionpriv` (
  `privid` bigint(20) unsigned NOT NULL auto_increment,
  `sessnum` bigint(20) unsigned NOT NULL default '0',
  `privilege` varchar(40) NOT NULL default '',
  `start` datetime NOT NULL default '0000-00-00 00:00:00',
  UNIQUE KEY `privid` (`privid`)
) TYPE=MyISAM CHARACTER SET `utf8`;

# --------------------------------------------------------

#
# Table structure for `view`
#
CREATE TABLE IF NOT EXISTS `view` (
  `viewid` bigint(20) unsigned NOT NULL auto_increment,
  `sessnum` bigint(20) unsigned NOT NULL default '0',
  `username` varchar(32) NOT NULL default '',
  `remoteip` varchar(40) NOT NULL default '',
  `start` datetime NOT NULL default '0000-00-00 00:00:00',
  `heartbeat` datetime NOT NULL default '0000-00-00 00:00:00',
  UNIQUE KEY `viewid` (`viewid`)
) TYPE=MyISAM CHARACTER SET `utf8`;

# --------------------------------------------------------

#
# Table structure for `viewlog`
#
CREATE TABLE IF NOT EXISTS `viewlog` (
  `sessnum` bigint(20) unsigned NOT NULL default '0',
  `username` varchar(32) NOT NULL default '',
  `remoteip` varchar(40) NOT NULL default '',
  `remotehost` varchar(40) NOT NULL default '',
  `time` datetime NOT NULL default '0000-00-00 00:00:00',
  `duration` float unsigned default '0'
) TYPE=MyISAM CHARACTER SET `utf8`;

# --------------------------------------------------------

#
# Table structure for `viewperm`
#
CREATE TABLE IF NOT EXISTS `viewperm` (
  `sessnum` bigint(20) unsigned NOT NULL default '0',
  `viewuser` varchar(32) NOT NULL default '',
  `viewtoken` varchar(32) NOT NULL default '',
  `geometry` varchar(9) NOT NULL default '0',
  `fwhost` varchar(40) NOT NULL default '',
  `fwport` smallint(5) unsigned NOT NULL default '0',
  `vncpass` varchar(16) NOT NULL default '',
  `readonly` varchar(4) NOT NULL default 'Yes',
  PRIMARY KEY  (`sessnum`,`viewuser`)
) TYPE=MyISAM CHARACTER SET `utf8`;

# --------------------------------------------------------

#
# Table structure for `#__recent_tools`
#
CREATE TABLE IF NOT EXISTS `#__recent_tools` (
  `id` int(11) NOT NULL auto_increment,
  `uid` int(11) NOT NULL default '0',
  `tool` varchar(200) default NULL,
  `created` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM CHARACTER SET `utf8`;

# --------------------------------------------------------

#
# Table structure for `#__licenses`
#
CREATE TABLE IF NOT EXISTS `#__licenses` (
	id         	int(11) AUTO_INCREMENT NOT NULL,
	alias      	varchar(255) NULL,
	description	text NULL,
	created    	datetime NOT NULL,
	modified   	datetime NOT NULL,
	PRIMARY KEY(id)
) TYPE=MyISAM CHARACTER SET `utf8`;

# --------------------------------------------------------

#
# Table structure for `#__licenses_tools`
#
CREATE TABLE IF NOT EXISTS `#__licenses_tools` (
	license_id	int(11) NULL DEFAULT '0',
	tool_id   	int(11) NULL DEFAULT '0',
	created   	datetime NOT NULL 
) TYPE=MyISAM CHARACTER SET `utf8`;

# --------------------------------------------------------

#
# Table structure for `#__licenses_users`
#
CREATE TABLE IF NOT EXISTS `#__licenses_users` (
	license_id	int(11) NULL DEFAULT '0',
	user_id   	int(11) NULL DEFAULT '0',
	created   	datetime NOT NULL,
	PRIMARY KEY `license_id` (`license_id`,`user_id`)
) TYPE=MyISAM CHARACTER SET `utf8`;

# --------------------------------------------------------

#
# Table structure for `#__faq`
#
CREATE TABLE IF NOT EXISTS `#__faq` (
  `id` int(11) NOT NULL auto_increment,
  `title` varchar(250) default NULL,
  `alias` varchar(200) default NULL,
  `introtext` text,
  `fulltext` text,
  `created` datetime default '0000-00-00 00:00:00',
  `created_by` int(11) default '0',
  `modified` datetime default '0000-00-00 00:00:00',
  `modified_by` int(11) default '0',
  `checked_out` int(11) default '0',
  `checked_out_time` datetime default '0000-00-00 00:00:00',
  `state` int(3) default '0',
  `access` tinyint(3) default '0',
  `hits` int(11) default '0',
  `version` int(11) default '0',
  `section` int(11) NOT NULL default '0',
  `category` int(11) default '0',
  `helpful` int(11) NOT NULL default '0',
  `nothelpful` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  FULLTEXT KEY `introtext` (`introtext`),
  FULLTEXT KEY `fulltext` (`fulltext`),
  FULLTEXT KEY `title` (`title`)
) TYPE=MyISAM DEFAULT CHARSET=utf8;

# --------------------------------------------------------

#
# Table structure for `#__faq_categories`
#
CREATE TABLE IF NOT EXISTS `#__faq_categories` (
  `id` int(11) NOT NULL auto_increment,
  `title` varchar(200) default NULL,
  `alias` varchar(200) default NULL,
  `description` text,
  `section` int(11) NOT NULL default '0',
  `state` tinyint(3) NOT NULL default '0',
  `access` tinyint(3) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM DEFAULT CHARSET=utf8;

# --------------------------------------------------------

#
# Table structure for `#__faq_helpful_log`
#
CREATE TABLE IF NOT EXISTS `#__faq_helpful_log` (
  `id` int(11) NOT NULL auto_increment,
  `fid` int(11) default '0',
  `ip` varchar(15) default NULL,
  `helpful` varchar(10) default NULL,
  PRIMARY KEY  (`id`)
) TYPE=MyISAM DEFAULT CHARSET=utf8;

# --------------------------------------------------------

#
# Table structure for `#__xmessage`
#
CREATE TABLE IF NOT EXISTS `#__xmessage` (
  `id` int(11) NOT NULL auto_increment,
  `created` datetime default '0000-00-00 00:00:00',
  `created_by` int(11) default '0',
  `message` mediumtext,
  `subject` varchar(250) default NULL,
  `component` varchar(100) default NULL,
  `type` varchar(100) default NULL,
  `group_id` int(11) NULL DEFAULT '0',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM CHARACTER SET `utf8`;

# --------------------------------------------------------

#
# Table structure for `#__xmessage_action`
#
CREATE TABLE IF NOT EXISTS `#__xmessage_action` (
  `id` int(11) NOT NULL auto_increment,
  `class` varchar(20) NOT NULL default '',
  `element` int(11) unsigned NOT NULL default '0',
  `description` mediumtext,
  KEY `id` (`id`),
  KEY `class` (`class`),
  KEY `element` (`element`)
) TYPE=MyISAM CHARACTER SET `utf8`;

# --------------------------------------------------------

#
# Table structure for `#__xmessage_component`
#
CREATE TABLE IF NOT EXISTS `#__xmessage_component` (
  `id` int(11) NOT NULL auto_increment,
  `component` varchar(50) NOT NULL default '',
  `action` varchar(100) NOT NULL default '',
  `title` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM CHARACTER SET `utf8`;

# --------------------------------------------------------

#
# Table structure for `#__xmessage_notify`
#
CREATE TABLE IF NOT EXISTS `#__xmessage_notify` (
  `id` int(11) NOT NULL auto_increment,
  `uid` int(11) default '0',
  `method` varchar(250) default NULL,
  `type` varchar(250) default NULL,
  `priority` int(2) default NULL,
  PRIMARY KEY  (`id`)
) TYPE=MyISAM CHARACTER SET `utf8`;

# --------------------------------------------------------

#
# Table structure for `#__xmessage_recipient`
#
CREATE TABLE IF NOT EXISTS `#__xmessage_recipient` (
  `id` int(11) NOT NULL auto_increment,
  `mid` int(11) default '0',
  `uid` int(11) default '0',
  `created` datetime default '0000-00-00 00:00:00',
  `expires` datetime default '0000-00-00 00:00:00',
  `actionid` int(11) default '0',
  `state` tinyint(2) default '0',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM CHARACTER SET `utf8`;

# --------------------------------------------------------

#
# Table structure for `#__xmessage_seen`
#
CREATE TABLE IF NOT EXISTS `#__xmessage_seen` (
  `mid` int(11) unsigned NOT NULL default '0',
  `uid` int(11) unsigned NOT NULL default '0',
  `whenseen` datetime default '0000-00-00 00:00:00',
  KEY `mid` (`mid`),
  KEY `uid` (`uid`)
) TYPE=MyISAM CHARACTER SET `utf8`;

CREATE TABLE `#__users_points_subscriptions` (
  `id` int(11) NOT NULL auto_increment,
  `uid` int(11) NOT NULL default '0',
  `serviceid` int(11) NOT NULL default '0',
  `units` int(11) NOT NULL default '1',
  `status` int(11) NOT NULL default '0',
  `pendingunits` int(11) default '0',
  `pendingpayment` float(6,2) default '0.00',
  `totalpaid` float(6,2) default '0.00',
  `installment` int(11) default '0',
  `contact` varchar(20) default '',
  `code` varchar(10) default '',
  `usepoints` tinyint(2) default '0',
  `notes` text,
  `added` datetime NOT NULL,
  `updated` datetime default NULL,
  `expires` datetime default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

CREATE TABLE `#__users_points_services` (
  `id` int(11) NOT NULL auto_increment,
  `title` varchar(250) NOT NULL default '',
  `category` varchar(50) NOT NULL default '',
  `alias` varchar(50) NOT NULL default '',
  `description` varchar(255) NOT NULL default '',
  `unitprice` float(6,2) default '0.00',
  `pointsprice` int(11) default '0',
  `currency` varchar(50) default 'points',
  `maxunits` int(11) default '0',
  `minunits` int(11) default '0',
  `unitsize` int(11) default '0',
  `status` int(11) default '0',
  `restricted` int(11) NULL default '0',
  `ordering` int(11) NULL default '0',
  `params` text,
  `unitmeasure` varchar(200) NOT NULL default '',
  `changed` datetime default NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `alias` (`alias`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

CREATE TABLE `#__jobs_types` (
  `id` int(11) NOT NULL auto_increment,
  `category` varchar(150) NOT NULL default '',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;

INSERT INTO `#__jobs_types` (`id`,`category`) VALUES ('1','Full-time');
INSERT INTO `#__jobs_types` (`id`,`category`) VALUES ('2','Part-time');
INSERT INTO `#__jobs_types` (`id`,`category`) VALUES ('3','Contract');
INSERT INTO `#__jobs_types` (`id`,`category`) VALUES ('4','Internship');
INSERT INTO `#__jobs_types` (`id`,`category`) VALUES ('5','Temporary');

CREATE TABLE `#__jobs_stats` (
  `id` int(11) NOT NULL auto_increment,
  `itemid` int(11) NOT NULL,
  `category` varchar(11) NOT NULL default '',
  `total_viewed` int(11) default '0',
  `total_shared` int(11) default '0',
  `viewed_today` int(11) default '0',
  `lastviewed` datetime default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

CREATE TABLE `#__jobs_shortlist` (
  `id` int(11) NOT NULL auto_increment,
  `emp` int(11) NOT NULL default '0',
  `seeker` int(11) NOT NULL default '0',
  `category` varchar(11) NOT NULL default 'resume',
  `jobid` int(11) default '0',
  `added` datetime default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

CREATE TABLE `#__jobs_seekers` (
  `id` int(11) NOT NULL auto_increment,
  `uid` int(11) NOT NULL default '0',
  `active` int(11) NOT NULL default '0',
  `lookingfor` varchar(255) default '',
  `tagline` varchar(255) default '',
  `linkedin` varchar(255) NULL,
  `url` varchar(255) NULL,
  `updated` datetime NULL DEFAULT '0000-00-00 00:00:00",
  `sought_cid` int(11) default '0',
  `sought_type` int(11) default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

CREATE TABLE `#__jobs_resumes` (
  `id` int(11) NOT NULL auto_increment,
  `uid` int(11) NOT NULL default '0',\
  `created` datetime NOT NULL default '0000-00-00 00:00:00',
  `title` varchar(100) default NULL,
  `filename` varchar(100) default NULL,
  `main` tinyint(2) default '1',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

CREATE TABLE `#__jobs_prefs` (
  `id` int(11) NOT NULL auto_increment,
  `uid` int(10) NOT NULL default '0',
  `category` varchar(20) NOT NULL default 'resume',
  `filters` text,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

CREATE TABLE `#__jobs_openings` (
  `id` int(11) NOT NULL auto_increment,
  `cid` int(11) default '0',
  `employerid` int(11) NOT NULL default '0',
  `code` int(11) NOT NULL default '0',
  `title` varchar(200) NOT NULL default '',
  `companyName` varchar(200) NOT NULL default '',
  `companyLocation` varchar(200) default '',
  `companyLocationCountry` varchar(100) default '',
  `companyWebsite` varchar(200) default '',
  `description` text,
  `addedBy` int(11) NOT NULL default '0',
  `editedBy` int(11) default '0',
  `added` datetime NOT NULL default '0000-00-00 00:00:00',
  `edited` datetime default '0000-00-00 00:00:00',
  `status` int(3) NOT NULL default '0',
  `type` int(3) NOT NULL default '0',
  `closedate` datetime default '0000-00-00 00:00:00',
  `opendate` datetime default '0000-00-00 00:00:00',
  `startdate` datetime default '0000-00-00 00:00:00',
  `applyExternalUrl` varchar(250) default '',
  `applyInternal` int(3) default '0',
  `contactName` varchar(100) default '',
  `contactEmail` varchar(100) default '',
  `contactPhone` varchar(100) default '',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

CREATE TABLE `#__jobs_employers` (
  `id` int(11) NOT NULL auto_increment,
  `uid` int(11) NOT NULL default '0',
  `added` datetime NOT NULL default '0000-00-00 00:00:00',
  `subscriptionid` int(11) NOT NULL default '0',
  `companyName` varchar(250) default '',
  `companyLocation` varchar(250) default '',
  `companyWebsite` varchar(250) default '',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

CREATE TABLE `#__jobs_categories` (
  `id` int(11) NOT NULL auto_increment,
  `category` varchar(150) NOT NULL default '',
  `ordernum` int(11) NOT NULL default '0',
  `description` varchar(255) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

CREATE TABLE `#__jobs_applications` (
  `id` int(11) NOT NULL auto_increment,
  `jid` int(11) NOT NULL default '0',
  `uid` int(11) NOT NULL default '0',
  `applied` datetime NOT NULL default '0000-00-00 00:00:00',
  `withdrawn` datetime default '0000-00-00 00:00:00',
  `cover` text,
  `resumeid` int(11) default '0',
  `status` int(11) default '1',
  `reason` varchar(255) default '',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

CREATE TABLE `#__jobs_admins` (
  `id` int(11) NOT NULL auto_increment,
  `jid` int(11) NOT NULL default '0',
  `uid` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

