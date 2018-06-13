<?php
require_once "pdo.php";
session_start();

if(isset($_POST['title'])){	
	if ( isset($_POST['title']) and isset($_POST['name']) and isset($_POST['email'])and isset($_POST['s_name'])) {

		// Data validation
		if ( strlen($_POST['title']) < 5 ) {
			$_SESSION['error'] = 'Please include a longer title';
			header("Location: QRPRepo.php");
			return;
		}
		if(isset($_POST['game'])){
			$game_prob_flag=1;	
		}
		else {
			$game_prob_flag=0;
		}
		if(isset($_POST['nm_author'])){
			$nm_author=$_POST['nm_author'];	
		}
		else {
			$nm_author="Null";
		}
		
		
		
// need to get the school_id either from the post data or from a query
						
						$sql = " SELECT school_id FROM School where s_name = :s_name";
						$stmt = $pdo->prepare($sql);
						$stmt->execute(array(
						':s_name' => $_POST['s_name']));
						$row = $stmt->fetch(PDO::FETCH_ASSOC);
						$school_id=$row['school_id'];
	  
	  $sql = "INSERT INTO problem (name, email, title,nm_author, game_prob_flag,school_id,status)	VALUES (:name, :email, :title,:nm_author, :game_prob_flag,:school_id,:status)";
			$stmt = $pdo->prepare($sql);
			$stmt->execute(array(
				':name' => $_POST['name'],
				':email' => $_POST['email'],
				':title' => $_POST['title'],
				':nm_author' => $nm_author,				
				':game_prob_flag' => $game_prob_flag,
				':school_id' => $school_id,
				':status' => 'num issued'));
				
			$pblm_num=$pdo->lastInsertId();
			
			
			// reserve the values in Qa table for the problem so all subsequent edits will be updates the other values will initialize to null in sql
			for ($i = 1; $i <= 200; $i++) {
					$sql = "INSERT INTO Qa (problem_id, dex)	
						VALUES (:problem_id, :dex)";
					$stmt = $pdo->prepare($sql);
					$stmt->execute(array(
						':problem_id'=> $pblm_num,
						':dex' => $i));
			
			}
			
			
				$_SESSION['success'] = 'your problem number is '.$pblm_num;
				$_SESSION['game_prob_flag']=$game_prob_flag;
				$file_name = 'p'.$pblm_num.'_'.$game_prob_flag.'_'.$_POST['title'];
				$_SESSION['file_name']=$file_name;
				header( 'Location: downloadDocx.php' ) ;
				return;
				
				
	 
	}
	else {
			$_SESSION['error'] = 'All inputs are required';
			header("Location: QRPRepo.php");
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
<!DOCTYPE html>
<html lang = "en">
<head>
<link rel="icon" type="image/png" href="McKetta.png" />  
<meta Charset = "utf-8">
<title>QRProblems</title>
<meta name="viewport" content="width=device-width, initial-scale=1" /> 
</head>

<body>
<header>
<h2>Quick Response Problems</h2>
</header>


<p>Please provide your:</p>
<form  method="POST" >
<p></p>
<p>Contributor Name:
<input type="text" name="name" ></p>

<p>Contributor Email address:
<input type="text" name="email" ></p>

<p>a Provisional Title:
<input type="text" name="title" ></p>
<p> The Author of the Base-Case (if different than Contributor):
<input type="text" name="nm_author" ></p>
<p>
<input type="checkbox" name="game" Value = "checked"> This is a Game Problem</p>
<label> School:
		<select required name = "s_name">
			<option> --Select the School or Organization (Required)--</option>
			<?php foreach ($s_name as $values){?>
			<option><?php echo $values;?></option>
			<?php }?>
		</select>
	</label> 
<p></p>
<p><input  type="submit" value="Get Problem Number"/>

<a href="QRPRepo.php">Cancel</a></p>
</form>
</body>
</html>