<?php
 session_start();
   $_SESSION['score'] = "0";
	$_SESSION['index'] = "0";
	//$_SESSION['count'] = 0;
	
Require_once "pdo.php";

// first do some error checking on the input.  If it is not OK set the session failure and send them back to QRPIndex.
if ( ! isset($_POST['problem_id']) ) {
  $_SESSION['error'] = "Missing problem number";
  header('Location: QRPindex.php');
  return;
}
if ( ! isset($_POST['dex_num']) ) {
  $_SESSION['error'] = "Missing index number";
  header('Location: QRPindex.php');
  return;
} 
 
if ($_POST['problem_id']<1 or $_POST['problem_id']>1000000)  {
  $_SESSION['error'] = "problem number out of range";
  header('Location: QRPindex.php');
  return;
}
if ($_POST['dex_num']<2 or $_POST['dex_num']>200)  {
  $_SESSION['error'] = "Index number out of range";
  header('Location: QRPindex.php');
  return;
}

// Next check the Qa table and see which values have non null values - for those 

echo $_POST['problem_id'];
echo "<br>";
echo $_POST['dex_num'];





?>

<!DOCTYPE html>
<html lang = "en">
<head>
<link rel="icon" type="image/png" href="McKetta.png" />  
<meta Charset = "utf-8">
<title>QRChecker</title>
<meta name="viewport" content="width=device-width, initial-scale=1" /> 
</head>

<body>
<header>
<h1>QRProblem Checker</h1>
</header>
<main>
</main>
</body>
</html>