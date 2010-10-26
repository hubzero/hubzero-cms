<?php

class plgYSearchProjects extends YSearchPlugin
{
	const USER = 'snyder13';
	const PASS = 'snyder13';
	const CONN_STR = 'ORCLDEV';

	public static function onYSearch($request, &$results)
	{
		$terms = $request->get_term_ar();

		foreach (array('mandatory', 'forbidden', 'optional') as $type)
			$terms[$type] = array_unique(array_merge($terms[$type], array_map('stem', $terms[$type])));

		$where = array();
		foreach ($terms['mandatory'] as $mand)
			$where[] = '(regexp_like(project.title || project.nsftitle, \''.preg_quote($mand).'\', \'i\')'.
				'or dbms_lob.instr(project.description, \''.$mand.'\') > 0)';
		if ($terms['forbidden'])
		{
			$block = '(not regexp_like(project.title || project.nsftitle, \'('.join('|', array_map('preg_quote', $terms['forbidden'])).')\', \'i\')';
			foreach ($terms['forbidden'] as $forb)
				$block .= ' and dbms_lob.instr(project.description, \''.$forb.'\') = 0';
			$where[] = $block.')';
		}

		$opt_where = array();
		$positive = array_unique(array_merge($terms['mandatory'], $terms['optional']));
		$opt_where[] = 'regexp_like(project.title || project.nsftitle, \'('.join('|', array_map('preg_quote', $positive)).')\', \'i\')';
		foreach ($positive as $pos)
			$opt_where[] = 'dbms_lob.instr(project.description, \''.$pos.'\') > 0';

		$juser =& JFactory::getUser();
		$where[] = $juser->guest ? 'upper(project.viewable) = \'PUBLIC\'' : 'upper(project.viewable) in (\'PUBLIC\', \'USERS\')';

		$dbh = oci_connect(self::USER, self::PASS, self::CONN_STR);
		$query = 'select title, description, projid from project where ('.join(' and ', $where).') and ('.join(' or ', $opt_where).')';
		$sth = oci_parse($dbh, $query);
		oci_execute($sth);
		while (($row = oci_fetch_assoc($sth)))
		{
			$descr_len = $row['DESCRIPTION']->size();
			$descr = $descr_len > 0 ? $row['DESCRIPTION']->read($descr_len) : '';
			$results->add(array(
				'title' => $row['TITLE'],
				'link' => '/warehouse/project/'.$row['PROJID'],
				'description' => $descr
			));
		}
	}
}
