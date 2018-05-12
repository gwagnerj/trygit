<?php
require_once "pdo.php";
session_start();

if(isset($_POST['title'])){	
	if ( isset($_POST['title']) and isset($_POST['name']) and isset($_POST['email'])) {

		// Data validation
		if ( strlen($_POST['title']) < 5 ) {
			$_SESSION['error'] = 'Please include a longer title';
			header("Location: index.php");
			return;
		}

	  $sql = "INSERT INTO problem (name, email, title)	VALUES (:name, :email, :title)";
			$stmt = $pdo->prepare($sql);
			$stmt->execute(array(
				':name' => $_POST['name'],
				':email' => $_POST['email'],
				':title' => $_POST['title']	));
				
			$pblm_num=$pdo->lastInsertId();
				$_SESSION['success'] = 'your problem number is '.$pblm_num;
				header( 'Location: index.php' ) ;
				return;
				
				
	   /*  $sql = "INSERT INTO problem  = :name,
				email = :email, title = :title
				WHERE problem_id = :problem_id";
		$stmt = $pdo->prepare($sql);
		$stmt->execute(array(
			':name' => $_POST['name'],
			':email' => $_POST['email'],
			':title' => $_POST['title'],
			':problem_id' => $_POST['problem_id']));
		$_SESSION['success'] = 'Record updated';
		header( 'Location: index.php' ) ;
		return; */
	}
	else {
			$_SESSION['error'] = 'All three inputs are required';
			header("Location: index.php");
			return;
	}



	// Guardian: Make sure that problem_id is present
	/* if ( ! isset($_GET['problem_id']) ) {
	  $_SESSION['error'] = "Missing problem_id";
	  header('Location: index.php');
	  return;
	}

	$stmt = $pdo->prepare("SELECT * FROM problem where problem_id = :xyz");
	$stmt->execute(array(":xyz" => $_GET['problem_id']));
	$row = $stmt->fetch(PDO::FETCH_ASSOC);
	if ( $row === false ) {
		$_SESSION['error'] = 'Bad value for problem_id';
		header( 'Location: index.php' ) ;
		return;
	} */

	// Flash pattern
	if ( isset($_SESSION['error']) ) {
		echo '<p style="color:red">'.$_SESSION['error']."</p>\n";
		unset($_SESSION['error']);
	}

/* $n = htmlentities($row['name']);
$e = htmlentities($row['email']);
$p = htmlentities($row['title']);
$f = 'filename';
$problem_id = $row['problem_id']; */
}
?>



<p>Please provide your:</p>
<form method="post">
<p></p>
<p>name:
<input type="text" name="name" ></p>

<p>email address:
<input type="text" name="email" ></p>

<p>a provisional title:
<input type="text" name="title" ></p>
<p></p>
<p><input type="submit" value="Get Problem Number"/>
<a href="index.php">Cancel</a></p>
</form>
