<?php
	ini_set('display_errors','On');
	error_reporting(E_ALL);

	function sanitize($s) {
		return strip_tags(stripslashes(trim($s)));
	}

	include('dmrshark-config.inc.php');

	date_default_timezone_set(DMRSHARK_TIMEZONE);

	header('Access-Control-Allow-Origin: ' . DMRSHARK_ALLOW_ORIGIN);

	$conn = mysqli_connect(DMRSHARK_DB_HOST, DMRSHARK_DB_USER, DMRSHARK_DB_PASSWORD, DMRSHARK_DB_NAME);
	if (!$conn) {
		echo "can't connect to mysql database!\n";
		return;
	}

	$conn->query("set names 'utf8'");
	$conn->query("set charset 'utf8'");

	$showgpspos = (sanitize($_POST['showgpspos']) == 'true');
	$searchfor = sanitize($_POST['searchfor']);
	$searchtoks = explode(' ', $searchfor);
	$search = '';
	for ($i = 0; $i < count($searchtoks); $i++) {
		if ($i == 0)
			$search = 'where ';
		else
			$search .= 'and ';

		$searchtok = $conn->escape_string($searchtoks[$i]);
		$search .= "(`startts` like '%$searchtok%' or " .
			"`endts` like '%$searchtok%' or " .
			"`srcid` like '%$searchtok%' or " .
			"`dmr-db-users-src`.callsign like '%$searchtok%' or " .
			"`dstid` like '%$searchtok%' or " .
			"`dmr-db-users-dst`.callsign like '%$searchtok%' or " .
			"`repeaterid` like '%$searchtok%' or " .
			"`dmr-db-repeaters`.callsign like '%$searchtok%' or " .
			"`timeslot` like '%$searchtok%' or " .
			"`calltype` like '%$searchtok%' or " .
			"`currrssi` like '%$searchtok%' or " .
			"`avgrssi` like '%$searchtok%') ";
	}

	if (!$showgpspos)
		$search .= 'and `datatype` != "gps position"';

	$sorting = sanitize($_GET['jtSorting']);
	$startindex = sanitize($_GET['jtStartIndex']);
	if (!ctype_digit($startindex))
		return;
	$pagesize = sanitize($_GET['jtPageSize']);
	if (!ctype_digit($pagesize))
		return;

	// Getting record count
	$join = 'left join `dmr-db-users` `dmr-db-users-src` on (`dmr-db-users-src`.callsignid = `' . DMRSHARK_DB_TABLE . '`.srcid) ' .
		'left join `dmr-db-users` `dmr-db-users-dst` on (`dmr-db-users-dst`.callsignid = `' . DMRSHARK_DB_TABLE . '`.dstid) ' .
		'left join `dmr-db-repeaters` on (`dmr-db-repeaters`.callsignid = `' . DMRSHARK_DB_TABLE . '`.repeaterid) ';
	$result = $conn->query('select count(*) as `recordcount` from `' . DMRSHARK_DB_TABLE . '` ' . $join . $search);
	$row = $result->fetch_array();
	$recordcount = $row['recordcount'];

	$result = $conn->query('select `' . DMRSHARK_DB_TABLE . '`.*, unix_timestamp(`startts`) as `startts1`, unix_timestamp(`endts`) as `endts1`, ' .
		'`dmr-db-users-src`.`callsign` as `src`, `dmr-db-users-src`.`name` as `srcname`, ' .
		'`dmr-db-users-dst`.`callsign` as `dst`, `dmr-db-users-dst`.`name` as `dstname`, ' .
		'`dmr-db-repeaters`.callsign as `repeater` ' .
		'from `' . DMRSHARK_DB_TABLE . '` '. $join . $search . 'order by ' . $conn->escape_string($sorting) .
		', `endts` desc limit ' . $conn->escape_string($startindex) . ',' . $conn->escape_string($pagesize));

	$rows = array();
	while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
		$row['startts'] = date('H:i:s', $row['startts1']);
		if ($row['endts1'] == 0)
			$row['endts'] = '00:00:00';
		else
			$row['endts'] = date('H:i:s', $row['endts1']);

		if ($row['src'] == '')
			$row['src'] = $row['srcid'];
		if ($row['dst'] == '')
			$row['dst'] = $row['dstid'];
		if ($row['repeater'] == '')
			$row['repeater'] = $row['repeaterid'];

		unset($row['startts1']);
		unset($row['endts1']);
	    $rows[] = $row;
	}

	$jtableresult = array();
	$jtableresult['Result'] = "OK";
	$jtableresult['TotalRecordCount'] = $recordcount;
	$jtableresult['Records'] = $rows;
	echo json_encode($jtableresult);

	$conn->close();
?>
