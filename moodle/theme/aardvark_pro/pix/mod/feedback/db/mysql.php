<?PHP

function feedback_upgrade($oldversion) {
/// This function does anything necessary to upgrade 
/// older versions to match current functionality 

    global $CFG;

   if ($oldversion < 2005050907) {
      execute_sql(" ALTER TABLE `{$CFG->prefix}feedback` ADD `anonymous` int(1) NOT NULL DEFAULT '0' AFTER `summary` ");
   }
   if ($oldversion < 2005050909) {
      execute_sql(" ALTER TABLE `{$CFG->prefix}feedback_item` ADD `required` int(1) NOT NULL DEFAULT '0' AFTER `position` ");
   }
   if ($oldversion < 2005050910) {
      execute_sql(" ALTER TABLE `{$CFG->prefix}feedback` CHANGE `anonymous` `anonymous` INT( 1 ) DEFAULT '1' NOT NULL  ");
   }
   if ($oldversion < 2005072405) {
      execute_sql(" ALTER TABLE `{$CFG->prefix}feedback_item` ADD `hasvalue` int(1) NOT NULL DEFAULT '0' AFTER `typ` ");
      execute_sql(" ALTER TABLE `{$CFG->prefix}feedback_item` ADD `typname` varchar(255) NOT NULL DEFAULT '' AFTER `typ` ");
      execute_sql(" UPDATE `{$CFG->prefix}feedback_item` SET `typname` = 'label' WHERE `typ` = 0 ");
      execute_sql(" UPDATE `{$CFG->prefix}feedback_item` SET `typname` = 'textfield', `hasvalue` = 1 WHERE `typ` = 1 ");
      execute_sql(" UPDATE `{$CFG->prefix}feedback_item` SET `typname` = 'textarea', `hasvalue` = 1 WHERE `typ` = 2 ");
      execute_sql(" UPDATE `{$CFG->prefix}feedback_item` SET `typname` = 'radio', `hasvalue` = 1 WHERE `typ` = 3 ");
      execute_sql(" UPDATE `{$CFG->prefix}feedback_item` SET `typname` = 'check', `hasvalue` = 1 WHERE `typ` = 4 ");
      execute_sql(" UPDATE `{$CFG->prefix}feedback_item` SET `typname` = 'dropdown', `hasvalue` = 1 WHERE `typ` = 5 ");
      execute_sql(" ALTER TABLE `{$CFG->prefix}feedback_item` DROP `typ` ");
      execute_sql(" ALTER TABLE `{$CFG->prefix}feedback_item` CHANGE `typname` `typ` varchar(255) NOT NULL DEFAULT '' ");
   }
   if ($oldversion < 2005080212) {
      execute_sql(" ALTER TABLE `{$CFG->prefix}feedback` ADD `email_notification` int(1) NOT NULL DEFAULT '1' AFTER `anonymous` ");
   }
   
   if ($oldversion < 2005090100) {
      execute_sql(" ALTER TABLE `{$CFG->prefix}feedback` ADD `multiple_submit` int(1) NOT NULL DEFAULT '0' AFTER `email_notification` ");
      execute_sql(" ALTER TABLE `{$CFG->prefix}feedback_value` ADD `tmp_completed` int(10) NOT NULL DEFAULT '0' AFTER `completed` ");
      execute_sql("  CREATE TABLE `{$CFG->prefix}feedback_tracking` (
                        `id` int(10) unsigned NOT NULL auto_increment,
                        `userid` int(10) NOT NULL default '0',
                        `feedback` int(10) NOT NULL default '0',
                        `completed` int(10) NOT NULL default '0',
                        `tmp_completed` int(10) NOT NULL default '0',
                        `count` int(1) NOT NULL default '0',
                        PRIMARY KEY  (`id`),
                        KEY `userid` (`userid`),
                        KEY `feedback` (`feedback`),
                        KEY `completed` (`completed`)
                     ) TYPE=MyISAM COMMENT='feedback trackingdata'
      ");
   }
   return true;
}

?>
