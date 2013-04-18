DROP TABLE IF EXISTS `#__oaipmh_dcspecs`;

CREATE TABLE IF NOT EXISTS `#__oaipmh_dcspecs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `query` text NOT NULL,
  `display` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=17 ;
 
INSERT INTO `#__oaipmh_dcspecs` (`id`, `name`, `query`, `display`) VALUES
(1, 'resource IDs', 'SELECT id FROM #__resources WHERE standalone = 1', 1),
(2, 'specify sets', 'SELECT alias, type, description FROM #__resource_types WHERE category = 27', 1),
(3, 'title', 'SELECT title FROM #__resources WHERE id = $id', 1),
(4, 'creator', 'SELECT u.name FROM #__users u, #__resources r WHERE r.created_by = u.id AND r.id = $id', 1),
(5, 'subject', 'SELECT t.raw_tag FROM jos_tags t, jos_tags_object tos \r\nWHERE t.id = tos.tagid AND tos.objectid = $id ORDER BY t.raw_tag', 1),
(6, 'date', 'SELECT created FROM #__resources WHERE id = $id', 1),
(7, 'identifier', 'resources/$id', 1),
(8, 'description', 'SELECT introtext FROM #__resources WHERE id = $id', 1),
(9, 'type', 'SELECT rt.type FROM #__resource_types rt, #__resources r WHERE r.type = rt.id AND r.id = $id', 1),
(10, 'publisher', 'Purdue University Research Repository', 1),
(11, 'rights', 'SELECT params FROM #__resources WHERE id = $id', 1),
(12, 'contributor', '', 1),
(13, 'relation', 'SELECT r.title FROM #__resources r, #__resource_assoc ra WHERE ra.parent_id = $id AND ra.child_id = r.id', 1),
(14, 'format', '', 1),
(15, 'coverage', '', 1),
(16, 'language', '', 1),
(17, 'source', '', 1);