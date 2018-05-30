<?php
require_once "pdo.php";
session_start();

if ( isset($_POST['delete']) && isset($_POST['problem_id']) ) {
  
  // Delete the word file from the upload file folder
	$sql = "SELECT * FROM problem WHERE problem_id = :zip";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(array(':zip' => $_POST['problem_id']));
	$data = $stmt -> fetch();
	$docxfilenm=$data['docxfilenm'];  // 
	$inputfilenm=$data['infilenm'];  // these are the name of the files we need to delete
	$pdffilenm=$data['pdffilenm'];
	
// Now delete the row from the database
	$sql = "DELETE FROM problem WHERE problem_id = :zip";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(array(':zip' => $_POST['problem_id']));
    
	if(unlink('uploads/'.$docxfilenm)){ // unlink is the command to delet a file
		$_SESSION['success'] = 'Docxfile deleted';
	}
	if(unlink('uploads/'.$inputfilenm)){
	
		$_SESSION['success'] = 'Input data file deleted';
	}
	if(unlink('uploads/'.$pdffilenm)){ // unlink is the command to delete a file
		$_SESSION['success'] = 'pdffile deleted';
	}
    header( 'Location: index.php' ) ;
    return;
}

// Guardian: Make sure that user_id is present
if ( ! isset($_GET['problem_id']) ) {
  $_SESSION['error'] = "Missing user_id";
  header('Location: index.php');
  return;
}

$stmt = $pdo->prepare("SELECT title, problem_id FROM problem where problem_id = :xyz");
$stmt->execute(array(":xyz" => $_GET['problem_id']));
$row = $stmt->fetch(PDO::FETCH_ASSOC);
if ( $row === false ) {
    $_SESSION['error'] = 'Bad value for user_id';
    header( 'Location: index.php' ) ;
    return;
}

?>
<p>Confirm: Deleting <?= htmlentities($row['title']) ?></p>

<form method="post">
<input type="hidden" name="problem_id" value="<?= $row['problem_id'] ?>">
<input type="submit" value="Delete" name="delete">
<a href="index.php">Cancel</a>
</form>
