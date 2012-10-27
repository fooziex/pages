<?php
include("config.inc.php");

if(!isset($_POST['id']) || !preg_match('/^[a-z0-9]+$/',$_POST['id'])) {
	die("Incorrect URL. Please use 'satan account pay' command.");
}

$dbh = mysql_connect(DB_HOST, DB_USER, DB_PASS);
if(!$dbh) {
        die("Cannot connect to database!");
}

mysql_select_db(DB_NAME, $dbh);

$request_query = mysql_query("
	SELECT uid, user_name, price
	FROM requests
	WHERE pay_key='".mysql_real_escape_string($_POST['id'])."'"
);

if(!$request_query) {
        die("Invalid query: ".mysql_error());
}

$user = mysql_fetch_array($request_query);

if(empty($user)) {
	die("Incorrect URL. Please use 'satan account pay' command.");
}

$ts = time();
$client_ip = $_SERVER['REMOTE_ADDR'];
$session_id = $user['user_name'].'_'.$ts;
$language = 'en';

$fund = $_POST['fund'];
if(!preg_match('/^[0-9]+$/', $fund)) {
	die("Incorrect fund value");
}

$price = $user['price'] + $fund;
$price = $price*1.23;

# Save user data
$user_query = mysql_query("INSERT INTO users (uid, first_name, last_name, street, postcode, city, country, mail, invoice) VALUES('"
	    . mysql_real_escape_string($user['uid'])."','"
	    . mysql_real_escape_string($_POST['first_name'])."','"
	    . mysql_real_escape_string($_POST['last_name'])."','"
	    . mysql_real_escape_string($_POST['street'])."','"
	    . mysql_real_escape_string($_POST['postcode'])."','"
	    . mysql_real_escape_string($_POST['city'])."','"
	    . mysql_real_escape_string($_POST['country'])."','"
	    . mysql_real_escape_string($_POST['mail'])."','"
            . mysql_real_escape_string($_POST['invoice'])."')"
);

if(!$user_query) {
	die("Cannot add data to database: ".mysql_error());
}

$sig = md5(PAYU_POS_ID
     . $session_id
     . PAYU_POS_AUTH_KEY
     . $price
     . $user['uid']
     . $_POST['first_name']
     . $_POST['last_name']
     . $_POST['street']
     . $_POST['city']
     . $_POST['postcode']
     . $_POST['country']
     . $_POST['mail']
     . $language
     . $client_ip
     . $ts
     . PAYU_KEY_SEND
);
     
$url = "https://www.platnosci.pl/paygw/UTF/NewPayment"
     . "?pos_id="      . PAYU_POS_ID
     . "&session_id="  . $session_id
     . "&pos_auth_key=". PAYU_POS_AUTH_KEY
     . "&amount="      . $price
     . "&desc="        . $user['uid']
     . "&first_name="  . urlencode($_POST['first_name'])
     . "&last_name="   . urlencode($_POST['last_name'])
     . "&street="      . urlencode($_POST['street'])
     . "&city="        . urlencode($_POST['city'])
     . "&post_code="   . urlencode($_POST['postcode'])
     . "&country="     . urlencode($_POST['country'])
     . "&email="       . urlencode($_POST['mail'])
     . "&language="    . $language
     . "&client_ip="   . $client_ip
     . "&ts="          . $ts
     . "&sig="         . $sig;

header("Location: $url");
?>
