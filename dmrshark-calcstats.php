#!/usr/bin/php
<?php
	ini_set('display_errors','On');
	error_reporting(E_ALL);

	include('dmrshark-config.inc.php');

	$conn = mysql_connect(DMRSHARK_DB_HOST, DMRSHARK_DB_USER, DMRSHARK_DB_PASSWORD);
	if (!$conn) {
		echo "can't connect to mysql database!\n";
		return 1;
	}

	$db = mysql_select_db(DMRSHARK_DB_NAME, $conn);
	if (!$db) {
		mysql_close($conn);
		echo "can't connect to mysql database!\n";
		return 1;
	}

	mysql_query("set names 'utf8'");
	mysql_query("set charset 'utf8'");

	// The timestamp of last script execution is stored in the table with id 0.
	$result = mysql_query('select `talktime` from `' . DMRSHARK_STATS_DB_TABLE . '` where `id` = 0');
	$lastcalcts = 0;
	if ($result) {
		$row = mysql_fetch_row($result);
		if ($row)
			$lastcalcts = $row[0];
	}
	echo "last calc ts is $lastcalcts\n";

	$result = mysql_query('select `srcid`, sum(unix_timestamp(`endts`)-unix_timestamp(`startts`)) as `talktime` ' .
		'from `' . DMRSHARK_DB_TABLE . '` where unix_timestamp(`startts`) > ' . $lastcalcts . ' group by `srcid`');
	if ($result) {
		while ($row = mysql_fetch_assoc($result)) {
			if ($row['talktime'] < 0 || $row['srcid'] == 0)
				continue;

			mysql_query('insert into `' . DMRSHARK_STATS_DB_TABLE . '` (`id`, `date`, `talktime`) ' .
				'values (' . $row['srcid'] . ', now(), `talktime`+' . $row['talktime'] . ') ' .
				'on duplicate key update `talktime`=`talktime` + ' . $row['talktime']);

			echo $row['srcid'] . ' talked ' . $row['talktime'] . " seconds\n";
		}
	}

	// Storing the current timestamp in the table.
	mysql_query('replace into `' . DMRSHARK_STATS_DB_TABLE . '` (`id`, `date`, `talktime`) ' .
		'values (0, 0, unix_timestamp())');

	// Removing entries older than 1 day.
	mysql_query('delete from `' . DMRSHARK_DB_TABLE . '` where unix_timestamp()-unix_timestamp(`startts`) > 86400 or `startts` = NULL');

	mysql_close($conn);
?>
