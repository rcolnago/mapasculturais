<?php
/**
 * Street Name Search
 * Search for a street name beginning with the string param from the master
 * address table.
 * 
 * @param 		string 		$streetname 	street name to search for
 * @param 		string		$format			output format, xml or json
 * @return 		string		- resulting json or xml string
 */

# Includes
require_once("../inc/error.inc.php");
require_once("../inc/database.inc.php");
require_once("../inc/security.inc.php");

# Set arguments for error email 
$err_user_name = "Tobin";
$err_email = "tobin.bradley@mecklenburgcountync.gov";


# Retrive URL arguments
try {
	$streetname = $_REQUEST['streetname'];
	$format = trim($_REQUEST['format']);
} 
catch (Exception $e) {
    trigger_error("Caught Exception: " . $e->getMessage(), E_USER_ERROR);
}

# Performs the query and returns XML or JSON
try {
	$sql = sanitizeSQL("select street_name from street_names where street_name like '" . strtoupper($streetname) . "%' ");
	$pgconn = pgConnection();

    /*** fetch into an PDOStatement object ***/
    $recordSet = $pgconn->prepare($sql);
    $recordSet->execute();

	if ($format == 'xml') {
		require_once("../inc/xml.pdo.inc.php");
		header("Content-Type: text/xml");
		echo rs2xml($recordSet);
	}
	elseif ($format == 'json') {
		require_once("../inc/json.pdo.inc.php");
		header("Content-Type: application/json");
		echo rs2json($recordSet);
	}
	elseif ($format == "text") {
		header("Content-Type: application/text");
		while($line = $recordSet->fetch(PDO::FETCH_ASSOC))
		{
			foreach ($line as $col_key => $col_val)
			{
				echo $col_val . "\n";
			}
		}
	}
	else {
		trigger_error("Caught Exception: format must be xml or json.", E_USER_ERROR);
	}
}
catch (Exception $e) {
	trigger_error("Caught Exception: " . $e->getMessage(), E_USER_ERROR);
}

?>