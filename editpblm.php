<?php
require_once "pdo.php";
session_start();

if ( isset($_POST['name']) or isset($_POST['email'])
     or isset($_POST['title']) or isset($_POST['s_name']) ) {

   // Data validation
	
	if ( strlen($_POST['name']) < 1 || strlen($_POST['title']) < 1) {
        $_SESSION['error'] = 'Missing data';
        header("Location: edit.php?problem_id=".$_POST['problem_id']);
        return;
    }

    if ( strpos($_POST['email'],'@') === false ) {
        $_SESSION['error'] = 'Bad data';
        header("Location: edit.php?problem_id=".$_POST['problem_id']);
        return;
    }

// get the new school_id	
	$stmt = $pdo->prepare("SELECT * FROM school where s_name = :xyz");
	$stmt->execute(array(":xyz" => $_POST['s_name']));
	$row = $stmt->fetch(PDO::FETCH_ASSOC);
	
	
	
    $sql = "UPDATE problem SET name = :name,
            email = :email, title = :title,school_id=:school_id
            WHERE problem_id = :problem_id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(array(
        ':name' => $_POST['name'],
        ':email' => $_POST['email'],
        ':title' => $_POST['title'],
		':school_id' => $row['school_id'],
        ':problem_id' => $_POST['problem_id']));
    $_SESSION['success'] = 'Record updated';
    header( 'Location: index.php' ) ;
    return;
}

// Guardian: Make sure that problem_id is present
if ( ! isset($_GET['problem_id']) ) {
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
}

// Flash pattern
if ( isset($_SESSION['error']) ) {
    echo '<p style="color:red">'.$_SESSION['error']."</p>\n";
    unset($_SESSION['error']);
}

$n = htmlentities($row['name']);
$e = htmlentities($row['email']);
$p = htmlentities($row['title']);
$problem_id = $row['problem_id'];
$school_id= $row['school_id'];

// now get the current school name
$stmt = $pdo->prepare("SELECT * FROM school where school_id = :xyz");
$stmt->execute(array(":xyz" => $school_id));
$row = $stmt->fetch(PDO::FETCH_ASSOC);
$s = htmlentities($row['s_name']);

$sql="SELECT DISTINCT s_name from School ORDER BY s_name";
$stmt = $pdo->query($sql);
// I'm pretty sure this is not the best way but I/m just going to read it into an array variable 
$i=0;
while ( $row = $stmt->fetch(PDO::FETCH_ASSOC) ) {
	$s_name[$i]=htmlentities($row['s_name']);
	$i=$i+1;
}


?>
<p>Edit Problem Meta Data</p>
<form method="post">
<p>Name:
<input type="text" name="name" value="<?= $n ?>"></p>
<p>Email:
<input type="text" name="email" value="<?= $e ?>"></p>
<p>title:
<input type="text" name="title" value="<?= $p ?>"></p>
<p>
<label> School or Organization:
		<select required name = "s_name">
			<option selected = "selected"> <?php echo $s ?></option>
			<?php foreach ($s_name as $values){?>
			<option><?php echo $values;?></option>
			<?php }?>
		</select>
	</label> 

</p>
<input type="hidden" name="problem_id" value="<?= $problem_id ?>">
<p><input type="submit" value="Update"/>
<a href="index.php">Cancel</a></p>
</form>
