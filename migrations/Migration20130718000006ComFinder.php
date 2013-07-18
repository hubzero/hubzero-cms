<?php

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for new joomla search/finder tables
 **/
class Migration20130718000006ComFinder extends Hubzero_Migration
{
	/**
	 * Up
	 **/
	protected static function up($db)
	{
		$query = "";

		if (!$db->tableExists('#__finder_filters'))
		{
			$query .= "CREATE  TABLE IF NOT EXISTS `#__finder_filters` (
							`filter_id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT ,
							`title` VARCHAR(255) NOT NULL ,
							`alias` VARCHAR(255) NOT NULL ,
							`state` TINYINT(1) NOT NULL DEFAULT '1' ,
							`created` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' ,
							`created_by` INT(10) UNSIGNED NOT NULL ,
							`created_by_alias` VARCHAR(255) NOT NULL ,
							`modified` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' ,
							`modified_by` INT(10) UNSIGNED NOT NULL DEFAULT '0' ,
							`checked_out` INT(10) UNSIGNED NOT NULL DEFAULT '0' ,
							`checked_out_time` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' ,
							`map_count` INT(10) UNSIGNED NOT NULL DEFAULT '0' ,
							`data` TEXT NOT NULL ,
							`params` MEDIUMTEXT NULL DEFAULT NULL ,
							PRIMARY KEY (`filter_id`) )
						ENGINE = InnoDB
						DEFAULT CHARACTER SET = utf8
						COLLATE = utf8_general_ci;\n";
		}

		if (!$db->tableExists('#__finder_links'))
		{
			$query .= "CREATE  TABLE IF NOT EXISTS `#__finder_links` (
							`link_id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT ,
							`url` VARCHAR(255) NOT NULL ,
							`route` VARCHAR(255) NOT NULL ,
							`title` VARCHAR(255) NULL DEFAULT NULL ,
							`description` VARCHAR(255) NULL DEFAULT NULL ,
							`indexdate` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' ,
							`md5sum` VARCHAR(32) NULL DEFAULT NULL ,
							`published` TINYINT(1) NOT NULL DEFAULT '1' ,
							`state` INT(5) NULL DEFAULT '1' ,
							`access` INT(5) NULL DEFAULT '0' ,
							`language` VARCHAR(8) NOT NULL ,
							`publish_start_date` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' ,
							`publish_end_date` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' ,
							`start_date` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' ,
							`end_date` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' ,
							`list_price` DOUBLE UNSIGNED NOT NULL DEFAULT '0' ,
							`sale_price` DOUBLE UNSIGNED NOT NULL DEFAULT '0' ,
							`type_id` INT(11) NOT NULL ,
							`object` MEDIUMBLOB NOT NULL ,
							PRIMARY KEY (`link_id`) ,
							INDEX `idx_type` (`type_id` ASC) ,
							INDEX `idx_title` (`title` ASC) ,
							INDEX `idx_md5` (`md5sum` ASC) ,
							INDEX `idx_url` (`url`(75) ASC) ,
							INDEX `idx_published_list` (`published` ASC, `state` ASC, `access` ASC, `publish_start_date` ASC, `publish_end_date` ASC, `list_price` ASC) ,
							INDEX `idx_published_sale` (`published` ASC, `state` ASC, `access` ASC, `publish_start_date` ASC, `publish_end_date` ASC, `sale_price` ASC) )
						ENGINE = InnoDB
						DEFAULT CHARACTER SET = utf8
						COLLATE = utf8_general_ci;\n";
		}

		if (!$db->tableExists('#__finder_links_terms0'))
		{
			$query .= "CREATE  TABLE IF NOT EXISTS `#__finder_links_terms0` (
							`link_id` INT(10) UNSIGNED NOT NULL ,
							`term_id` INT(10) UNSIGNED NOT NULL ,
							`weight` FLOAT(10) UNSIGNED NOT NULL ,
							PRIMARY KEY (`link_id`, `term_id`) ,
							INDEX `idx_term_weight` (`term_id` ASC, `weight` ASC) ,
							INDEX `idx_link_term_weight` (`link_id` ASC, `term_id` ASC, `weight` ASC) )
						ENGINE = InnoDB
						DEFAULT CHARACTER SET = utf8
						COLLATE = utf8_general_ci;\n";
		}

		if (!$db->tableExists('#__finder_links_terms1'))
		{
			$query .= "CREATE  TABLE IF NOT EXISTS `#__finder_links_terms1` (
							`link_id` INT(10) UNSIGNED NOT NULL ,
							`term_id` INT(10) UNSIGNED NOT NULL ,
							`weight` FLOAT(10) UNSIGNED NOT NULL ,
							PRIMARY KEY (`link_id`, `term_id`) ,
							INDEX `idx_term_weight` (`term_id` ASC, `weight` ASC) ,
							INDEX `idx_link_term_weight` (`link_id` ASC, `term_id` ASC, `weight` ASC) )
						ENGINE = InnoDB
						DEFAULT CHARACTER SET = utf8
						COLLATE = utf8_general_ci;\n";
		}

		if (!$db->tableExists('#__finder_links_terms2'))
		{
			$query .= "CREATE  TABLE IF NOT EXISTS `#__finder_links_terms2` (
							`link_id` INT(10) UNSIGNED NOT NULL ,
							`term_id` INT(10) UNSIGNED NOT NULL ,
							`weight` FLOAT(10) UNSIGNED NOT NULL ,
							PRIMARY KEY (`link_id`, `term_id`) ,
							INDEX `idx_term_weight` (`term_id` ASC, `weight` ASC) ,
							INDEX `idx_link_term_weight` (`link_id` ASC, `term_id` ASC, `weight` ASC) )
						ENGINE = InnoDB
						DEFAULT CHARACTER SET = utf8
						COLLATE = utf8_general_ci;\n";
		}

		if (!$db->tableExists('#__finder_links_terms3'))
		{
			$query .= "CREATE  TABLE IF NOT EXISTS `#__finder_links_terms3` (
							`link_id` INT(10) UNSIGNED NOT NULL ,
							`term_id` INT(10) UNSIGNED NOT NULL ,
							`weight` FLOAT(10) UNSIGNED NOT NULL ,
							PRIMARY KEY (`link_id`, `term_id`) ,
							INDEX `idx_term_weight` (`term_id` ASC, `weight` ASC) ,
							INDEX `idx_link_term_weight` (`link_id` ASC, `term_id` ASC, `weight` ASC) )
						ENGINE = InnoDB
						DEFAULT CHARACTER SET = utf8
						COLLATE = utf8_general_ci;\n";
		}

		if (!$db->tableExists('#__finder_links_terms4'))
		{
			$query .= "CREATE  TABLE IF NOT EXISTS `#__finder_links_terms4` (
							`link_id` INT(10) UNSIGNED NOT NULL ,
							`term_id` INT(10) UNSIGNED NOT NULL ,
							`weight` FLOAT(10) UNSIGNED NOT NULL ,
							PRIMARY KEY (`link_id`, `term_id`) ,
							INDEX `idx_term_weight` (`term_id` ASC, `weight` ASC) ,
							INDEX `idx_link_term_weight` (`link_id` ASC, `term_id` ASC, `weight` ASC) )
						ENGINE = InnoDB
						DEFAULT CHARACTER SET = utf8
						COLLATE = utf8_general_ci;\n";
		}

		if (!$db->tableExists('#__finder_links_terms5'))
		{
			$query .= "CREATE  TABLE IF NOT EXISTS `#__finder_links_terms5` (
							`link_id` INT(10) UNSIGNED NOT NULL ,
							`term_id` INT(10) UNSIGNED NOT NULL ,
							`weight` FLOAT(10) UNSIGNED NOT NULL ,
							PRIMARY KEY (`link_id`, `term_id`) ,
							INDEX `idx_term_weight` (`term_id` ASC, `weight` ASC) ,
							INDEX `idx_link_term_weight` (`link_id` ASC, `term_id` ASC, `weight` ASC) )
						ENGINE = InnoDB
						DEFAULT CHARACTER SET = utf8
						COLLATE = utf8_general_ci;\n";
		}

		if (!$db->tableExists('#__finder_links_terms6'))
		{
			$query .= "CREATE  TABLE IF NOT EXISTS `#__finder_links_terms6` (
							`link_id` INT(10) UNSIGNED NOT NULL ,
							`term_id` INT(10) UNSIGNED NOT NULL ,
							`weight` FLOAT(10) UNSIGNED NOT NULL ,
							PRIMARY KEY (`link_id`, `term_id`) ,
							INDEX `idx_term_weight` (`term_id` ASC, `weight` ASC) ,
							INDEX `idx_link_term_weight` (`link_id` ASC, `term_id` ASC, `weight` ASC) )
						ENGINE = InnoDB
						DEFAULT CHARACTER SET = utf8
						COLLATE = utf8_general_ci;\n";
		}

		if (!$db->tableExists('#__finder_links_terms7'))
		{
			$query .= "CREATE  TABLE IF NOT EXISTS `#__finder_links_terms7` (
							`link_id` INT(10) UNSIGNED NOT NULL ,
							`term_id` INT(10) UNSIGNED NOT NULL ,
							`weight` FLOAT(10) UNSIGNED NOT NULL ,
							PRIMARY KEY (`link_id`, `term_id`) ,
							INDEX `idx_term_weight` (`term_id` ASC, `weight` ASC) ,
							INDEX `idx_link_term_weight` (`link_id` ASC, `term_id` ASC, `weight` ASC) )
						ENGINE = InnoDB
						DEFAULT CHARACTER SET = utf8
						COLLATE = utf8_general_ci;\n";
		}

		if (!$db->tableExists('#__finder_links_terms8'))
		{
			$query .= "CREATE  TABLE IF NOT EXISTS `#__finder_links_terms8` (
							`link_id` INT(10) UNSIGNED NOT NULL ,
							`term_id` INT(10) UNSIGNED NOT NULL ,
							`weight` FLOAT(10) UNSIGNED NOT NULL ,
							PRIMARY KEY (`link_id`, `term_id`) ,
							INDEX `idx_term_weight` (`term_id` ASC, `weight` ASC) ,
							INDEX `idx_link_term_weight` (`link_id` ASC, `term_id` ASC, `weight` ASC) )
						ENGINE = InnoDB
						DEFAULT CHARACTER SET = utf8
						COLLATE = utf8_general_ci;\n";
		}

		if (!$db->tableExists('#__finder_links_terms9'))
		{
			$query .= "CREATE  TABLE IF NOT EXISTS `#__finder_links_terms9` (
							`link_id` INT(10) UNSIGNED NOT NULL ,
							`term_id` INT(10) UNSIGNED NOT NULL ,
							`weight` FLOAT(10) UNSIGNED NOT NULL ,
							PRIMARY KEY (`link_id`, `term_id`) ,
							INDEX `idx_term_weight` (`term_id` ASC, `weight` ASC) ,
							INDEX `idx_link_term_weight` (`link_id` ASC, `term_id` ASC, `weight` ASC) )
						ENGINE = InnoDB
						DEFAULT CHARACTER SET = utf8
						COLLATE = utf8_general_ci;\n";
		}

		if (!$db->tableExists('#__finder_links_termsa'))
		{
			$query .= "CREATE  TABLE IF NOT EXISTS `#__finder_links_termsa` (
							`link_id` INT(10) UNSIGNED NOT NULL ,
							`term_id` INT(10) UNSIGNED NOT NULL ,
							`weight` FLOAT(10) UNSIGNED NOT NULL ,
							PRIMARY KEY (`link_id`, `term_id`) ,
							INDEX `idx_term_weight` (`term_id` ASC, `weight` ASC) ,
							INDEX `idx_link_term_weight` (`link_id` ASC, `term_id` ASC, `weight` ASC) )
						ENGINE = InnoDB
						DEFAULT CHARACTER SET = utf8
						COLLATE = utf8_general_ci;\n";
		}

		if (!$db->tableExists('#__finder_links_termsb'))
		{
			$query .= "CREATE  TABLE IF NOT EXISTS `#__finder_links_termsb` (
							`link_id` INT(10) UNSIGNED NOT NULL ,
							`term_id` INT(10) UNSIGNED NOT NULL ,
							`weight` FLOAT(10) UNSIGNED NOT NULL ,
							PRIMARY KEY (`link_id`, `term_id`) ,
							INDEX `idx_term_weight` (`term_id` ASC, `weight` ASC) ,
							INDEX `idx_link_term_weight` (`link_id` ASC, `term_id` ASC, `weight` ASC) )
						ENGINE = InnoDB
						DEFAULT CHARACTER SET = utf8
						COLLATE = utf8_general_ci;\n";
		}

		if (!$db->tableExists('#__finder_links_termsc'))
		{
			$query .= "CREATE  TABLE IF NOT EXISTS `#__finder_links_termsc` (
							`link_id` INT(10) UNSIGNED NOT NULL ,
							`term_id` INT(10) UNSIGNED NOT NULL ,
							`weight` FLOAT(10) UNSIGNED NOT NULL ,
							PRIMARY KEY (`link_id`, `term_id`) ,
							INDEX `idx_term_weight` (`term_id` ASC, `weight` ASC) ,
							INDEX `idx_link_term_weight` (`link_id` ASC, `term_id` ASC, `weight` ASC) )
						ENGINE = InnoDB
						DEFAULT CHARACTER SET = utf8
						COLLATE = utf8_general_ci;\n";
		}

		if (!$db->tableExists('#__finder_links_termsd'))
		{
			$query .= "CREATE  TABLE IF NOT EXISTS `#__finder_links_termsd` (
							`link_id` INT(10) UNSIGNED NOT NULL ,
							`term_id` INT(10) UNSIGNED NOT NULL ,
							`weight` FLOAT(10) UNSIGNED NOT NULL ,
							PRIMARY KEY (`link_id`, `term_id`) ,
							INDEX `idx_term_weight` (`term_id` ASC, `weight` ASC) ,
							INDEX `idx_link_term_weight` (`link_id` ASC, `term_id` ASC, `weight` ASC) )
						ENGINE = InnoDB
						DEFAULT CHARACTER SET = utf8
						COLLATE = utf8_general_ci;\n";
		}

		if (!$db->tableExists('#__finder_links_termse'))
		{
			$query .= "CREATE  TABLE IF NOT EXISTS `#__finder_links_termse` (
							`link_id` INT(10) UNSIGNED NOT NULL ,
							`term_id` INT(10) UNSIGNED NOT NULL ,
							`weight` FLOAT(10) UNSIGNED NOT NULL ,
							PRIMARY KEY (`link_id`, `term_id`) ,
							INDEX `idx_term_weight` (`term_id` ASC, `weight` ASC) ,
							INDEX `idx_link_term_weight` (`link_id` ASC, `term_id` ASC, `weight` ASC) )
						ENGINE = InnoDB
						DEFAULT CHARACTER SET = utf8
						COLLATE = utf8_general_ci;\n";
		}

		if (!$db->tableExists('#__finder_links_termsf'))
		{
			$query .= "CREATE  TABLE IF NOT EXISTS `#__finder_links_termsf` (
							`link_id` INT(10) UNSIGNED NOT NULL ,
							`term_id` INT(10) UNSIGNED NOT NULL ,
							`weight` FLOAT(10) UNSIGNED NOT NULL ,
							PRIMARY KEY (`link_id`, `term_id`) ,
							INDEX `idx_term_weight` (`term_id` ASC, `weight` ASC) ,
							INDEX `idx_link_term_weight` (`link_id` ASC, `term_id` ASC, `weight` ASC) )
						ENGINE = InnoDB
						DEFAULT CHARACTER SET = utf8
						COLLATE = utf8_general_ci;\n";
		}

		if (!$db->tableExists('#__finder_taxonomy'))
		{
			$query .= "CREATE  TABLE IF NOT EXISTS `#__finder_taxonomy` (
						`id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT ,
						`parent_id` INT(10) UNSIGNED NOT NULL DEFAULT '0' ,
						`title` VARCHAR(255) NOT NULL ,
						`state` TINYINT(1) UNSIGNED NOT NULL DEFAULT '1' ,
						`access` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0' ,
						`ordering` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0' ,
						PRIMARY KEY (`id`) ,
						INDEX `parent_id` (`parent_id` ASC) ,
						INDEX `state` (`state` ASC) ,
						INDEX `ordering` (`ordering` ASC) ,
						INDEX `access` (`access` ASC) ,
						INDEX `idx_parent_published` (`parent_id` ASC, `state` ASC, `access` ASC) )
						ENGINE = InnoDB
						DEFAULT CHARACTER SET = utf8
						COLLATE = utf8_general_ci;\n";
		}

		if (!$db->tableExists('#__finder_taxonomy_map'))
		{
			$query .= "CREATE  TABLE IF NOT EXISTS `#__finder_taxonomy_map` (
						`link_id` INT(10) UNSIGNED NOT NULL ,
						`node_id` INT(10) UNSIGNED NOT NULL ,
						PRIMARY KEY (`link_id`, `node_id`) ,
						INDEX `link_id` (`link_id` ASC) ,
						INDEX `node_id` (`node_id` ASC) )
						ENGINE = InnoDB
						DEFAULT CHARACTER SET = utf8
						COLLATE = utf8_general_ci;\n";
		}

		if (!$db->tableExists('#__finder_terms'))
		{
			$query .= "CREATE  TABLE IF NOT EXISTS `#__finder_terms` (
						`term_id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT ,
						`term` VARCHAR(75) NOT NULL ,
						`stem` VARCHAR(75) NOT NULL ,
						`common` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0' ,
						`phrase` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0' ,
						`weight` FLOAT(10) UNSIGNED NOT NULL DEFAULT '0' ,
						`soundex` VARCHAR(75) NOT NULL ,
						`links` INT(10) NOT NULL DEFAULT '0' ,
						PRIMARY KEY (`term_id`) ,
						UNIQUE INDEX `idx_term` (`term` ASC) ,
						INDEX `idx_term_phrase` (`term` ASC, `phrase` ASC) ,
						INDEX `idx_stem_phrase` (`stem` ASC, `phrase` ASC) ,
						INDEX `idx_soundex_phrase` (`soundex` ASC, `phrase` ASC) )
						ENGINE = InnoDB
						DEFAULT CHARACTER SET = utf8
						COLLATE = utf8_general_ci;\n";
		}

		if (!$db->tableExists('#__finder_terms_common'))
		{
			$query .= "CREATE  TABLE IF NOT EXISTS `#__finder_terms_common` (
						`term` VARCHAR(75) NOT NULL ,
						`language` VARCHAR(3) NOT NULL ,
						INDEX `idx_word_lang` (`term` ASC, `language` ASC) ,
						INDEX `idx_lang` (`language` ASC) )
						ENGINE = InnoDB
						DEFAULT CHARACTER SET = utf8
						COLLATE = utf8_general_ci;\n";
		}

		if (!$db->tableExists('#__finder_tokens'))
		{
			$query .= "CREATE  TABLE IF NOT EXISTS `#__finder_tokens` (
						`term` VARCHAR(75) NOT NULL ,
						`stem` VARCHAR(75) NOT NULL ,
						`common` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0' ,
						`phrase` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0' ,
						`weight` FLOAT(10) UNSIGNED NOT NULL DEFAULT '1' ,
						`context` TINYINT(1) UNSIGNED NOT NULL DEFAULT '2' ,
						INDEX `idx_word` (`term` ASC) ,
						INDEX `idx_context` (`context` ASC) )
						ENGINE = MEMORY
						DEFAULT CHARACTER SET = utf8
						COLLATE = utf8_general_ci;\n";
		}

		if (!$db->tableExists('#__finder_tokens_aggregate'))
		{
			$query .= "CREATE  TABLE IF NOT EXISTS `#__finder_tokens_aggregate` (
						`term_id` INT(10) UNSIGNED NOT NULL ,
						`map_suffix` CHAR(1) NOT NULL ,
						`term` VARCHAR(75) NOT NULL ,
						`stem` VARCHAR(75) NOT NULL ,
						`common` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0' ,
						`phrase` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0' ,
						`term_weight` FLOAT(10) UNSIGNED NOT NULL ,
						`context` TINYINT(1) UNSIGNED NOT NULL DEFAULT '2' ,
						`context_weight` FLOAT(10) UNSIGNED NOT NULL ,
						`total_weight` FLOAT(10) UNSIGNED NOT NULL ,
						INDEX `token` (`term` ASC) ,
						INDEX `keyword_id` (`term_id` ASC) )
						ENGINE = MEMORY
						DEFAULT CHARACTER SET = utf8
						COLLATE = utf8_general_ci;\n";
		}

		if (!$db->tableExists('#__finder_types'))
		{
			$query .= "CREATE  TABLE IF NOT EXISTS `#__finder_types` (
						`id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT ,
						`title` VARCHAR(100) NOT NULL ,
						`mime` VARCHAR(100) NOT NULL ,
						PRIMARY KEY (`id`) ,
						UNIQUE INDEX `title` (`title` ASC) )
						ENGINE = InnoDB
						DEFAULT CHARACTER SET = utf8
						COLLATE = utf8_general_ci;\n";
		}

		if (!empty($query))
		{
			$db->setQuery($query);
			$db->query();
		}
	}
}