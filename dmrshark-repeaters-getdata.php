<?php
	ini_set('display_errors','On');
	error_reporting(E_ALL);

	function sanitize($s) {
		return strip_tags(stripslashes(trim($s)));
	}

	include('dmrshark-config.inc.php');

	header('Access-Control-Allow-Origin: ' . DMRSHARK_ALLOW_ORIGIN);

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
		$search .= "(`callsign` like '%$searchtok%' or " .
			"`id` like '%$searchtok%' or " .
			"`type` like '%$searchtok%' or " .
			"`fwversion` like '%$searchtok%' or " .
			"`dlfreq` like '%$searchtok%' or " .
			"`ulfreq` like '%$searchtok%' or " .
			"`lastactive` like '%$searchtok%') ";
	}

	$sorting = sanitize($_GET['jtSorting']);
	$startindex = sanitize($_GET['jtStartIndex']);
	if (!ctype_digit($startindex))
		return;
	$pagesize = sanitize($_GET['jtPageSize']);
	if (!ctype_digit($pagesize))
		return;

	$result = mysql_query('select count(*) as `recordcount` from `' . DMRSHARK_REPEATERS_DB_TABLE . '` ' . $search);
	$row = mysql_fetch_array($result);
	$recordcount = $row['recordcount'];

	$result = mysql_query('select *, unix_timestamp(`lastactive`) as `lastactivets` from `' . DMRSHARK_REPEATERS_DB_TABLE . '` ' .
		$search . 'order by ' . mysql_real_escape_string($sorting) .
		' limit ' . mysql_real_escape_string($startindex) . ',' . mysql_real_escape_string($pagesize));

	$rows = array();
	while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
		$row['lastactive'] = date('H:i:s', $row['lastactivets']);
		unset($row['lastactivets']);

	    $rows[] = $row;
	}

	$jtableresult = array();
	$jtableresult['Result'] = "OK";
	$jtableresult['TotalRecordCount'] = $recordcount;
	$jtableresult['Records'] = $rows;
	echo json_encode($jtableresult);

	mysql_close($conn);
?>
