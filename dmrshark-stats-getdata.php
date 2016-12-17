<?php
	ini_set('display_errors','On');
	error_reporting(E_ALL);

	function sanitize($s) {
		return strip_tags(stripslashes(trim($s)));
	}

	include('dmrshark-config.inc.php');

	$conn = mysqli_connect(DMRSHARK_DB_HOST, DMRSHARK_DB_USER, DMRSHARK_DB_PASSWORD, DMRSHARK_DB_NAME);
	if (!$conn) {
		echo "can't connect to mysql database!\n";
		return;
	}

	$conn->query("set names 'utf8'");
	$conn->query("set charset 'utf8'");

	$searchfor = sanitize($_POST['searchfor']);
	$startts = sanitize(@$_POST['startts']);
	$endts = sanitize(@$_POST['endts']);
	if (empty($startts))
		$startts = 0;
	if (empty($endts))
		$endts = time();

	$searchtoks = explode(' ', $searchfor);
	$search = 'where (unix_timestamp(`date`) >= ' . $conn->escape_string($startts) .
		' and unix_timestamp(`date`) <= ' . $conn->escape_string($endts) . ') ';

	for ($i = 0; $i < count($searchtoks); $i++) {
		$search .= 'and ';

		$searchtok = $conn->escape_string($searchtoks[$i]);
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
	$result = $conn->query('select count(*) as `recordcount` from (select count(*) from `' . DMRSHARK_STATS_DB_TABLE . '` ' . $join . $search . 'group by `id`) as `count`');
	$row = $result->fetch_array();
	$recordcount = $row['recordcount'];

	$result = $conn->query('select `' . DMRSHARK_STATS_DB_TABLE . '`.`id`, sum(`' . DMRSHARK_STATS_DB_TABLE . '`.talktime) as `talktime`, ' .
		'`dmr-db-users`.`callsign` as `callsign`, `dmr-db-users`.`name` as `name` ' .
		'from `' . DMRSHARK_STATS_DB_TABLE . '` '. $join . $search . 'group by `id` order by ' . $conn->escape_string($sorting) .
		' limit ' . $conn->escape_string($startindex) . ',' . $conn->escape_string($pagesize));

	$rows = array();
	while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
		if ($row['id'] == 0) // Ignore last calc timestamp which is stored in ID 0.
			continue;
		if ($row['callsign'] == '')
			continue;
	    $rows[] = $row;
	}

	$jtableresult = array();
	$jtableresult['Result'] = "OK";
	$jtableresult['TotalRecordCount'] = $recordcount;
	$jtableresult['Records'] = $rows;
	echo json_encode($jtableresult);

	$conn->close();
?>
