<?php
require_once "pdo.php";
session_start();

if(isset($_POST['title'])){	
	if ( isset($_POST['title']) and isset($_POST['name']) and isset($_POST['email'])and isset($_POST['s_name'])) {

		// Data validation
		if ( strlen($_POST['title']) < 5 ) {
			$_SESSION['error'] = 'Please include a longer title';
			header("Location: index.php");
			return;
		}
// need to get the school_id either from the post data or from a query
						
						$sql = " SELECT school_id FROM School where s_name = :s_name";
						$stmt = $pdo->prepare($sql);
						$stmt->execute(array(
						':s_name' => $_POST['s_name']));
						$row = $stmt->fetch(PDO::FETCH_ASSOC);
						$school_id=$row['school_id'];
	  
	  $sql = "INSERT INTO problem (name, email, title,school_id,status)	VALUES (:name, :email, :title, :school_id,:status)";
			$stmt = $pdo->prepare($sql);
			$stmt->execute(array(
				':name' => $_POST['name'],
				':email' => $_POST['email'],
				':title' => $_POST['title'],	
				':school_id' => $school_id,
				':status' => 'num issued'));
				
			$pblm_num=$pdo->lastInsertId();
				$_SESSION['success'] = 'your problem number is '.$pblm_num;
				header( 'Location: index.php' ) ;
				return;
				
				
	 
	}
	else {
			$_SESSION['error'] = 'All inputs are required';
			header("Location: index.php");
			return;
	}


	// Flash pattern
	if ( isset($_SESSION['error']) ) {
		echo '<p style="color:red">'.$_SESSION['error']."</p>\n";
		unset($_SESSION['error']);
	}

}

// Get the school names from the database so we can use them in drop down selection box
$sql="SELECT DISTINCT s_name from School ORDER BY s_name";
$stmt = $pdo->query($sql);
// I'm pretty sure this is not the best way but I/m just going to read it into an array variable 
$i=0;
while ( $row = $stmt->fetch(PDO::FETCH_ASSOC) ) {
	$s_name[$i]=htmlentities($row['s_name']);
	$i=$i+1;
}

?>



<p>Please provide your:</p>
<form method="post">
<p></p>
<p>Contributor Name:
<input type="text" name="name" ></p>

<p>Contributor Email address:
<input type="text" name="email" ></p>

<p>a Provisional Title:
<input type="text" name="title" ></p>
<label> School:
		<select required name = "s_name">
			<option> --Select the School or Organization (Required)--</option>
			<?php foreach ($s_name as $values){?>
			<option><?php echo $values;?></option>
			<?php }?>
		</select>
	</label> 
<p></p>
<p><input type="submit" value="Get Problem Number"/>
<a href="index.php">Cancel</a></p>
</form>
