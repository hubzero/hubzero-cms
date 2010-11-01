<?php
/**
 * JDate constructor tests
 *
 * @package Joomla
 * @subpackage UnitTest
 * @version $Id: JDate-helper-dataset.php 14408 2010-01-26 15:00:08Z louis $
 * @author Alan Langford <instance1@gmail.com>
 */


class JDateTest_DataSet
{
	/**
	 * Test values and results. setDate string, array of values by type, local
	 * flag (or null).
	 */
	static public $tests = array(
		array(
			'desc' => 'Invalid data',
			'src' => 'garbage',
			'utc' => null,
			'local' => null,
		),
		array(
			'desc' => 'Invalid data -- bad month',
			'src' => 'Mon, 15 Zok 2007 00:00:00 -0100',
			'utc' => null,
			'local' => null,
		),
		array(
			'jver_min' => '1.6.0',
			'desc' => 'Unix epoch with negative offset',
			'src' => 'Mon, 01 Jan 1970 00:00:00 -0000',
			'localOffset' => -2,
			'utc' => array(
				'ts' => 0,
				'Format' => '1970-01-01 00:00:00',
				'ISO8601' => '1970-01-01T00:00:00+0000',
				'MySql' => '1970-01-01 00:00:00',
				'RFC822' => 'Thu, 01 Jan 1970 00:00:00 +0000'
			),
			'local' => array(
				'ts' => 0,
				'Format' => '1969-12-31 22:00:00',
				'ISO8601' => '1969-12-31T22:00:00-0200',
				'MySql' => '1970-01-01 00:00:00',
				'RFC822' => 'Wed, 31 Dec 1969 22:00:00 -0200'
			),
		),
		array(
			'jver_min' => '1.6.0',
			'desc' => 'Unix epoch, lowercase with positive offset',
			'src' => 'mon, 01 jan 1970 00:00:00 -0000',
			'localOffset' => 2,
			'utc' => array(
				'ts' => 0,
				'Format' => '1970-01-01 00:00:00',
				'ISO8601' => '1970-01-01T00:00:00+0000',
				'MySql' => '1970-01-01 00:00:00',
				'RFC822' => 'Thu, 01 Jan 1970 00:00:00 +0000'
			),
			'local' => array(
				'ts' => 0,
				'Format' => '1970-01-01 02:00:00',
				'ISO8601' => '1970-01-01T02:00:00+0200',
				'MySql' => '1970-01-01 00:00:00',
				'RFC822' => 'Thu, 01 Jan 1970 02:00:00 +0200'
			),
		),
		array(
			'jver_min' => '1.6.0',
			'desc' => 'Unix epoch, uppercase with fractional negative offset',
			'src' => 'MON, 01 JAN 1970 00:00:00 -0000',
			'localOffset' => -0.5,
			'utc' => array(
				'ts' => 0,
				'Format' => '1970-01-01 00:00:00',
				'ISO8601' => '1970-01-01T00:00:00+0000',
				'MySql' => '1970-01-01 00:00:00',
				'RFC822' => 'Thu, 01 Jan 1970 00:00:00 +0000'
			),
			'local' => array(
				'ts' => 0,
				'Format' => '1969-12-31 23:30:00',
				'ISO8601' => '1969-12-31T23:30:00-0030',
				'MySql' => '1970-01-01 00:00:00',
				'RFC822' => 'Wed, 31 Dec 1969 23:30:00 -0030'
			),
		),
		array(
			// February, fractional positive offset
			'jver_min' => '1.6.0',
			'desc' => 'February, fractional positive offset',
			'src' => 'Foo, 27 feb 1971 00:00:00 -0000',
			'localOffset' => 0.5,
			'utc' => array(
				'ts' => 36460800,
				'Format' => '1971-02-27 00:00:00',
				'ISO8601' => '1971-02-27T00:00:00+0000',
				'MySql' => '1971-02-27 00:00:00',
				'RFC822' => 'Sat, 27 Feb 1971 00:00:00 +0000'
			),
			'local' => array(
				'ts' => 36460800,
				'Format' => '1971-02-27 00:30:00',
				'ISO8601' => '1971-02-27T00:30:00+0030',
				'MySql' => '1971-02-27 00:00:00',
				'RFC822' => 'Sat, 27 Feb 1971 00:30:00 +0030'
			),
		),
		/*
		 * RFC  tests that walk a 1 through the time, most significant digit
		 * first. When sign is relevant, that is iterated as well.
		 */
		array(
			'jver_min' => '1.6.0',
			'desc' => 'RFC822 1am',
			'src' => 'Foo, 27 Mar 1972 01:00:00 -0000',
			'utc' => array(
				'ts' => 70506000,
				'Format' => '1972-03-27 01:00:00',
				'ISO8601' => '1972-03-27T01:00:00+0000',
				'MySql' => '1972-03-27 01:00:00',
				'RFC822' => 'Mon, 27 Mar 1972 01:00:00 +0000'
			),
		),
		array(
			'jver_min' => '1.6.0',
			'desc' => 'RFC822 12:01am',
			'src' => 'Foo, 27 Apr 1973 00:01:00 -0000',
			'utc' => array(
				'ts' => 104716860,
				'Format' => '1973-04-27 00:01:00',
				'ISO8601' => '1973-04-27T00:01:00+0000',
				'MySql' => '1973-04-27 00:01:00',
				'RFC822' => 'Fri, 27 Apr 1973 00:01:00 +0000'
			),
		),
		array(
			'jver_min' => '1.6.0',
			'desc' => 'RFC822 12:00:01am',
			'src' => 'Foo, 27 May 1974 00:00:01 -0000',
			'utc' => array(
				'ts' => 138844801,
				'Format' => '1974-05-27 00:00:01',
				'ISO8601' => '1974-05-27T00:00:01+0000',
				'MySql' => '1974-05-27 00:00:01',
				'RFC822' => 'Mon, 27 May 1974 00:00:01 +0000'
			),
		),
		array(
			'jver_min' => '1.6.0',
			'desc' => 'RFC822 midnight +10:00',
			'src' => 'Foo, 27 Jun 1975 00:00:00 +1000',
			'utc' => array(
				'ts' => 173023200,
				'Format' => '1975-06-26 14:00:00',
				'ISO8601' => '1975-06-26T14:00:00+0000',
				'MySql' => '1975-06-26 14:00:00',
				'RFC822' => 'Thu, 26 Jun 1975 14:00:00 +0000'
			),
		),
		array(
			'jver_min' => '1.6.0',
			'desc' => 'RFC822 midnight -10:00',
			'src' => 'Foo, 27 Jun 1975 00:00:00 -1000',
			'utc' => array(
				'ts' => 173095200,
				'Format' => '1975-06-27 10:00:00',
				'ISO8601' => '1975-06-27T10:00:00+0000',
				'MySql' => '1975-06-27 10:00:00',
				'RFC822' => 'Fri, 27 Jun 1975 10:00:00 +0000'
			),
		),
		array(
			'jver_min' => '1.6.0',
			'desc' => 'RFC822 midnight -1:00',
			'src' => 'Foo, 27 Jul 1976 00:00:00 -0100',
			'utc' => array(
				'ts' => 207277200,
				'Format' => '1976-07-27 01:00:00',
				'ISO8601' => '1976-07-27T01:00:00+0000',
				'MySql' => '1976-07-27 01:00:00',
				'RFC822' => 'Tue, 27 Jul 1976 01:00:00 +0000'
			),
		),
		array(
			'jver_min' => '1.6.0',
			'desc' => 'RFC822 midnight +00:10',
			'src' => 'Foo, 27 Aug 1977 00:00:00 +0010',
			'utc' => array(
				'ts' => 241487400,
				'Format' => '1977-08-26 23:50:00',
				'ISO8601' => '1977-08-26T23:50:00+0000',
				'MySql' => '1977-08-26 23:50:00',
				'RFC822' => 'Fri, 26 Aug 1977 23:50:00 +0000'
			),
		),
		array(
			'jver_min' => '1.6.0',
			'desc' => 'RFC822 midnight -00:10',
			'src' => 'Foo, 27 Aug 1977 00:00:00 -0010',
			'utc' => array(
				'ts' => 241488600,
				'Format' => '1977-08-27 00:10:00',
				'ISO8601' => '1977-08-27T00:10:00+0000',
				'MySql' => '1977-08-27 00:10:00',
				'RFC822' => 'Sat, 27 Aug 1977 00:10:00 +0000'
			),
		),
		array(
			'jver_min' => '1.6.0',
			'desc' => 'RFC822 midnight -00:01',
			'src' => 'Foo, 27 Sep 1978 00:00:00 -0001',
			'utc' => array(
				'ts' => 275702460,
				'Format' => '1978-09-27 00:01:00',
				'ISO8601' => '1978-09-27T00:01:00+0000',
				'MySql' => '1978-09-27 00:01:00',
				'RFC822' => 'Wed, 27 Sep 1978 00:01:00 +0000'
			),
		),
		array(
			// See how we handle out of range values
			'jver_min' => '1.6.0',
			'desc' => 'RFC822 range check +35:00',
			'src' => 'Foo, 35 Oct 1971 00:71:99 +3500',
			'utc' => array(
				'ts' => 57939159,
				'Format' => '1971-11-02 14:12:39',
				'ISO8601' => '1971-11-02T14:12:39+0000',
				'MySql' => '1971-11-02 14:12:39',
				'RFC822' => 'Tue, 02 Nov 1971 14:12:39 +0000'
			),
		),
		/*
		 * ISO tests
		 */
		array(
			'jver_min' => '1.6.0',
			'desc' => 'ISO8601 Unix epoch, local offset -2',
			'src' => '1970-01-01T00:00:00-0000',
			'localOffset' => -2,
			'utc' => array(
				'ts' => 0,
				'Format' => '1970-01-01 00:00:00',
				'ISO8601' => '1970-01-01T00:00:00+0000',
				'MySql' => '1970-01-01 00:00:00',
				'RFC822' => 'Thu, 01 Jan 1970 00:00:00 +0000'
			),
			'local' => array(
				'ts' => 0,
				'Format' => '1969-12-31 22:00:00',
				'ISO8601' => '1969-12-31T22:00:00-0200',
				'MySql' => '1970-01-01 00:00:00',
				'RFC822' => 'Wed, 31 Dec 1969 22:00:00 -0200'
			),
		),
		array(
			'jver_min' => '1.6.0',
			'desc' => 'ISO8601, local offset +2',
			'src' => '1971-02-27T00:00:00+0000',
			'localOffset' => 2,
			'utc' => array(
				'ts' => 36460800,
				'Format' => '1971-02-27 00:00:00',
				'ISO8601' => '1971-02-27T00:00:00+0000',
				'MySql' => '1971-02-27 00:00:00',
				'RFC822' => 'Sat, 27 Feb 1971 00:00:00 +0000'
			),
			'local' => array(
				'ts' => 36460800,
				'Format' => '1971-02-27 02:00:00',
				'ISO8601' => '1971-02-27T02:00:00+0200',
				'MySql' => '1971-02-27 00:00:00',
				'RFC822' => 'Sat, 27 Feb 1971 02:00:00 +0200'
			),
		),
		array(
			'jver_min' => '1.6.0',
			'desc' => 'ISO8601, local offset -0.5',
			'src' => '1972-03-27T01:00:00+0000',
			'localOffset' => -0.5,
			'utc' => array(
				'ts' => 70506000,
				'Format' => '1972-03-27 01:00:00',
				'ISO8601' => '1972-03-27T01:00:00+0000',
				'MySql' => '1972-03-27 01:00:00',
				'RFC822' => 'Mon, 27 Mar 1972 01:00:00 +0000'
			),
			'local' => array(
				'ts' => 70506000,
				'Format' => '1972-03-27 00:30:00',
				'ISO8601' => '1972-03-27T00:30:00-0030',
				'MySql' => '1972-03-27 01:00:00',
				'RFC822' => 'Mon, 27 Mar 1972 00:30:00 -0030'
			),
		),
		array(
			'jver_min' => '1.6.0',
			'desc' => 'ISO8601, local offset +0.5',
			'src' => '1975-06-27T00:00:00+1000',
			'localOffset' => 0.5,
			'utc' => array(
				'ts' => 173023200,
				'Format' => '1975-06-26 14:00:00',
				'ISO8601' => '1975-06-26T14:00:00+0000',
				'MySql' => '1975-06-26 14:00:00',
				'RFC822' => 'Thu, 26 Jun 1975 14:00:00 +0000'
			),
			'local' => array(
				'ts' => 173023200,
				'Format' => '1975-06-26 14:30:00',
				'ISO8601' => '1975-06-26T14:30:00+0030',
				'MySql' => '1975-06-26 14:00:00',
				'RFC822' => 'Thu, 26 Jun 1975 14:30:00 +0030'
			),
		),
		array(
			'jver_min' => '1.6.0',
			'desc' => 'ISO8601, internal offset -10:00',
			'src' => '1975-06-27T00:00:00-1000',
			'utc' => array(
				'ts' => 173095200,
				'Format' => '1975-06-27 10:00:00',
				'ISO8601' => '1975-06-27T10:00:00+0000',
				'MySql' => '1975-06-27 10:00:00',
				'RFC822' => 'Fri, 27 Jun 1975 10:00:00 +0000'
			),
		),
		array(
			'jver_min' => '1.6.0',
			'desc' => 'ISO8601, internal offset -01:00',
			'src' => '1976-07-27T00:00:00-0100',
			'utc' => array(
				'ts' => 207277200,
				'Format' => '1976-07-27 01:00:00',
				'ISO8601' => '1976-07-27T01:00:00+0000',
				'MySql' => '1976-07-27 01:00:00',
				'RFC822' => 'Tue, 27 Jul 1976 01:00:00 +0000'
			),
		),
		array(
			'jver_min' => '1.6.0',
			'desc' => 'ISO8601, internal offset -00:10',
			'src' => '1977-08-27T00:00:00-0010',
			'utc' => array(
				'ts' => 241488600,
				'Format' => '1977-08-27 00:10:00',
				'ISO8601' => '1977-08-27T00:10:00+0000',
				'MySql' => '1977-08-27 00:10:00',
				'RFC822' => 'Sat, 27 Aug 1977 00:10:00 +0000'
			),
		),
		array(
			'jver_min' => '1.6.0',
			'desc' => 'ISO8601, internal offset -00:01',
			'src' => '1978-09-27T00:00:00-0001',
			'utc' => array(
				'ts' => 275702460,
				'Format' => '1978-09-27 00:01:00',
				'ISO8601' => '1978-09-27T00:01:00+0000',
				'MySql' => '1978-09-27 00:01:00',
				'RFC822' => 'Wed, 27 Sep 1978 00:01:00 +0000'
			),
		),
		array(
			'jver_min' => '1.6.0',
			'desc' => 'ISO8601, internal offset +35:00',
			'src' => '1971-10-35T00:71:99+3500',
			'utc' => array(
				'ts' => 57939159,
				'Format' => '1971-11-02 14:12:39',
				'ISO8601' => '1971-11-02T14:12:39+0000',
				'MySql' => '1971-11-02 14:12:39',
				'RFC822' => 'Tue, 02 Nov 1971 14:12:39 +0000'
			),
		),
		array(
			'jver_min' => '1.6.0',
			'desc' => 'ISO8601, internal offset +00:10',
			'src' => '1977-08-27T00:00:00+0010',
			'utc' => array(
				'ts' => 241487400,
				'Format' => '1977-08-26 23:50:00',
				'ISO8601' => '1977-08-26T23:50:00+0000',
				'MySql' => '1977-08-26 23:50:00',
				'RFC822' => 'Fri, 26 Aug 1977 23:50:00 +0000'
			),
		),
		array(
			// RFC-like dates with separate offsets
		   'jver_min' => '1.6.0',
			'desc' => 'RFC822, source -2, local -2',
			'src' => '01 Jan 1970 00:00:00',
			'srcOffset' => -2,
			'localOffset' => -2,
			'utc' => array(
				'ts' => 7200,
				'Format' => '1970-01-01 02:00:00',
				'ISO8601' => '1970-01-01T02:00:00+0000',
				'MySql' => '1970-01-01 02:00:00',
				'RFC822' => 'Thu, 01 Jan 1970 02:00:00 +0000'
			),
			'local' => array(
				'ts' => 7200,
				'Format' => '1970-01-01 00:00:00',
				'ISO8601' => '1970-01-01T00:00:00-0200',
				'MySql' => '1970-01-01 02:00:00',
				'RFC822' => 'Thu, 01 Jan 1970 00:00:00 -0200'
			),
		),
		array(
			// RFC-like dates with separate offsets
			'jver_min' => '1.6.0',
			'desc' => 'RFC822, source +2, local -2',
			'src' => '01 Jan 1970 00:00:00',
			'srcOffset' => 2,
			'localOffset' => -2,
			'utc' => array(
				'ts' => -7200,
				'Format' => '1969-12-31 22:00:00',
				'ISO8601' => '1969-12-31T22:00:00+0000',
				'MySql' => '1969-12-31 22:00:00',
				'RFC822' => 'Wed, 31 Dec 1969 22:00:00 +0000'
			),
			'local' => array(
				'ts' => -7200,
				'Format' => '1969-12-31 20:00:00',
				'ISO8601' => '1969-12-31T20:00:00-0200',
				'MySql' => '1969-12-31 22:00:00',
				'RFC822' => 'Wed, 31 Dec 1969 20:00:00 -0200'
			),
		),
		array(
			// RFC-like dates with separate offsets
			'jver_min' => '1.6.0',
			'desc' => 'RFC822, source -0.5, local -2',
			'src' => '01 Jan 1970 00:00:00',
			'srcOffset' => -0.5,
			'localOffset' => -2,
			'utc' => array(
				'ts' => 1800,
				'Format' => '1970-01-01 00:30:00',
				'ISO8601' => '1970-01-01T00:30:00+0000',
				'MySql' => '1970-01-01 00:30:00',
				'RFC822' => 'Thu, 01 Jan 1970 00:30:00 +0000'
			),
			'local' => array(
				'ts' => 1800,
				'Format' => '1969-12-31 22:30:00',
				'ISO8601' => '1969-12-31T22:30:00-0200',
				'MySql' => '1970-01-01 00:30:00',
				'RFC822' => 'Wed, 31 Dec 1969 22:30:00 -0200'
			),
		),
		array(
			// RFC-like dates with separate offsets
			'jver_min' => '1.6.0',
			'desc' => 'RFC822, source +0.5, local -2',
			'src' => '01 Jan 1970 00:00:00',
			'srcOffset' => 0.5,
			'localOffset' => -2,
			'utc' => array(
				'ts' => -1800,
				'Format' => '1969-12-31 23:30:00',
				'ISO8601' => '1969-12-31T23:30:00+0000',
				'MySql' => '1969-12-31 23:30:00',
				'RFC822' => 'Wed, 31 Dec 1969 23:30:00 +0000'
			),
			'local' => array(
				'ts' => -1800,
				'Format' => '1969-12-31 21:30:00',
				'ISO8601' => '1969-12-31T21:30:00-0200',
				'MySql' => '1969-12-31 23:30:00',
				'RFC822' => 'Wed, 31 Dec 1969 21:30:00 -0200'
			),
		),
	);

	function message($jd, $subSet, $key, $dataSet, $actual) {
		$msg = isset($dataSet['desc']) ? $dataSet['desc'] . ' ' : '';
		$msg .= 'Value "' . $dataSet['src'] . '" as ' . $key . ' ' . $subSet;
		$list = $dataSet[$subSet];
		if (! is_null($list) && isset($list[$key])) {
			$expect = $list[$key];
			$pass = ($expect == $actual);
		} else {
			$expect = null;
			$pass = is_null($actual);
		}
		if (! $pass) {
			$msg .= ' expected: ' . (is_null($expect) ? 'null' : $expect);
			if ($key != 'ts') {
				$msg .= ' (' . (
					is_null($list['ts'])
					? 'null' : $list['ts']
				) . ')';
			}
			$msg .= ' got: ';
		} else {
			$msg .= ' passed: ';
		}
		$msg .= (is_null($actual) ? 'null' : $actual);
		if ($key != 'ts') {
			$msg .= ' (' . (
				is_null($jd -> toUnix())
				? 'null'
				: $jd -> toUnix()
			) . ')';
		}
		return $msg;
	}

}
