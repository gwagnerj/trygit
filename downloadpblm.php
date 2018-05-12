<?php
require_once "pdo.php";
session_start();

// Guardian: Make sure that problem_id is present
if ( ! isset($_GET['problem_id']) ) {
  $_SESSION['error'] = "Missing problem_id";
  header('Location: index.php');
  return;
}
  
	
    $sql = "SELECT * FROM problem WHERE problem_id = :zip";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(array(':zip' => $_GET['problem_id']));
	$data = $stmt -> fetch();
	//print_r ($data);
	//die ();
	$docxfilenm=$data['docxfilenm'];
	$inputdata=$data['infilenm'];
	
	$file_pathdocx='uploads/'.$docxfilenm;
	$file_pathinput='uploads/'.$inputdata;
	
	echo 'click on files to download';
	echo "<br>";
    echo "<a href='".$file_pathdocx."'>".$docxfilenm."</a>";
	echo "<br>";
    echo "<a href='".$file_pathinput."'>".$inputdata."</a>";
		
		
	
   

?>

<p> </p>
<a href="index.php">Finished or Cancel</a>

<!--

$stmt = $pdo->prepare("SELECT name, users2_id FROM users2 where users2_id = :xyz");
$stmt->execute(array(":xyz" => $_GET['users2_id']));
$row = $stmt->fetch(PDO::FETCH_ASSOC);
if ( $row === false ) {
    $_SESSION['error'] = 'Bad value for users2_id';
    header( 'Location: index.php' ) ;
    return;
} 

?>
<p>Confirm: Download <?= htmlentities($row['name']) ?></p>

<form method="post">
<input type="hidden" name="users2_id" value="<?= $row['users2_id'] ?>">
<input type="submit" value="Download" name="download">
<a href="index.php">Finished or Cancel</a>
</form>
-->