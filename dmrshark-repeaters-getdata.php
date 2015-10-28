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

	$searchfor = sanitize($_POST['searchfor']);
	$searchtoks = explode(' ', $searchfor);
	$search = '';
	for ($i = 0; $i < count($searchtoks); $i++) {
		if ($i == 0)
			$search = 'where ';
		else
			$search .= 'and ';

		$searchtok = $conn->escape_string($searchtoks[$i]);
		$search .= "(`callsign` like '%$searchtok%' or " .
			"`id` like '%$searchtok%' or " .
			"`type` like '%$searchtok%' or " .
			"`fwversion` like '%$searchtok%' or " .
			"`dlfreq` like '%$searchtok%' or " .
			"`ulfreq` like '%$searchtok%' or " .
			"`psuvoltage` like '%$searchtok%' or " .
			"`patemperature` like '%$searchtok%' or " .
			"`vswr` like '%$searchtok%' or " .
			"`txfwdpower` like '%$searchtok%' or " .
			"`txrefpower` like '%$searchtok%' or " .
			"`lastactive` like '%$searchtok%') ";
	}

	$sorting = sanitize($_GET['jtSorting']);
	$startindex = sanitize($_GET['jtStartIndex']);
	if (!ctype_digit($startindex))
		return;
	$pagesize = sanitize($_GET['jtPageSize']);
	if (!ctype_digit($pagesize))
		return;

	$result = $conn->query('select count(*) as `recordcount` from `' . DMRSHARK_REPEATERS_DB_TABLE . '` ' . $search);
	$row = $result->fetch_array();
	$recordcount = $row['recordcount'];

	$result = $conn->query('select *, unix_timestamp(`lastactive`) as `lastactivets` from `' . DMRSHARK_REPEATERS_DB_TABLE . '` ' .
		$search . 'order by ' . $conn->escape_string($sorting) .
		' limit ' . $conn->escape_string($startindex) . ',' . $conn->escape_string($pagesize));

	$rows = array();
	while ($row = $result->fetch_array( MYSQLI_ASSOC)) {
		$row['lastactive'] = date('H:i:s', $row['lastactivets']);
		unset($row['lastactivets']);

	    $rows[] = $row;
	}

	$jtableresult = array();
	$jtableresult['Result'] = "OK";
	$jtableresult['TotalRecordCount'] = $recordcount;
	$jtableresult['Records'] = $rows;
	echo json_encode($jtableresult);

	$conn->close();
?>
