<?php
	ini_set('display_errors','On');
	error_reporting(E_ALL);

	function sanitize($s) {
		return strip_tags(stripslashes(trim($s)));
	}

	include('dmrshark-config.inc.php');

	$conn = mysql_connect(DMRSHARK_DB_HOST, DMRSHARK_DB_USER, DMRSHARK_DB_PASSWORD);
	if (!$conn) {
		echo "can't connect to mysql database!\n";
		return;
	}

	$db = mysql_select_db(DMRSHARK_DB_NAME, $conn);
	if (!$db) {
		mysql_close($conn);
		echo "can't connect to mysql database!\n";
		return;
	}

	mysql_query("set names 'utf8'");
	mysql_query("set charset 'utf8'");

	$searchfor = sanitize($_POST['searchfor']);
	$searchtoks = explode(' ', $searchfor);
	$search = '';
	for ($i = 0; $i < count($searchtoks); $i++) {
		if ($i == 0)
			$search = 'where ';
		else
			$search .= 'and ';

		$searchtok = mysql_real_escape_string($searchtoks[$i]);
		$search .= "(`id` like '%$searchtok%' or " .
			"`date` like '%$searchtok%' or " .
			"`dmr-db-users`.callsign like '%$searchtok%' or " .
			"`dmr-db-users`.name like '%$searchtok%') ";
	}

	$sorting = sanitize($_GET['jtSorting']);
	$startindex = sanitize($_GET['jtStartIndex']);
	if (!ctype_digit($startindex))
		return;
	$pagesize = sanitize($_GET['jtPageSize']);
	if (!ctype_digit($pagesize))
		return;

	// Getting record count
	$join = 'left join `dmr-db-users` on (`dmr-db-users`.callsignid = `' . DMRSHARK_STATS_DB_TABLE . '`.id) ';
	$result = mysql_query('select count(*) as `recordcount` from (select count(*) from `' . DMRSHARK_STATS_DB_TABLE . '` ' . $join . $search . 'group by `id`) as `count`');
	$row = mysql_fetch_array($result);
	$recordcount = $row['recordcount'];

	$result = mysql_query('select `' . DMRSHARK_STATS_DB_TABLE . '`.`id`, sum(`' . DMRSHARK_STATS_DB_TABLE . '`.talktime) as `talktime`, ' .
		'`dmr-db-users`.`callsign` as `callsign`, `dmr-db-users`.`name` as `name` ' .
		'from `' . DMRSHARK_STATS_DB_TABLE . '` '. $join . $search . 'group by `id` order by ' . mysql_real_escape_string($sorting) .
		' limit ' . mysql_real_escape_string($startindex) . ',' . mysql_real_escape_string($pagesize));

	$rows = array();
	while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
		if ($row['id'] == 0) // Ignore last calc timestamp which is stored in ID 0.
			continue;
	    $rows[] = $row;
	}

	$jtableresult = array();
	$jtableresult['Result'] = "OK";
	$jtableresult['TotalRecordCount'] = $recordcount;
	$jtableresult['Records'] = $rows;
	echo json_encode($jtableresult);

	mysql_close($conn);
?>
