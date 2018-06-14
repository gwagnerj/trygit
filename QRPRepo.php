<?php
require_once "pdo.php";
session_start();
?>
 <!DOCTYPE html>
<html lang = "en">
<head>
<link rel="icon" type="image/png" href="McKetta.png" />  
<meta Charset = "utf-8">
<title>QRP Repo</title>
<meta name="viewport" content="width=device-width, initial-scale=1" /> 
</head>

<body>
<header>
<h2>Quick Response Problems</h2>
</header>

<?php
if ( isset($_SESSION['error']) ) {
    echo '<p style="color:red">'.$_SESSION['error']."</p>\n";
    unset($_SESSION['error']);
}
if ( isset($_SESSION['success']) ) {
    echo '<p style="color:green">'.$_SESSION['success']."</p>\n";
    unset($_SESSION['success']);
}

$preview="Null";
//if they request the file then set the $preview variable to the name of the file
	if (isset($_POST['preview']) ){
		$preview='uploads/'.htmlentities($_POST['preview']);
	}
	if (isset($_POST['soln_preview']) ){
			$preview='uploads/'.htmlentities($_POST['soln_preview']);
		}


echo('<table border="1">'."\n");
	echo("</td><td>");
	echo('<b>Problem Num</b>');
	echo("</td><td>");
	echo('<b>Contributor Name</b>');
    echo("</td><td>");
    echo('<b>Contributor Email</b>');
	 echo("</td><td>");
	 echo('<b>University</b>');
    echo("</td><td>");
    echo('<b>Pblm Title</b>');
    echo("</td><td>");
	echo('<b>Status</b>');
    echo("</td><td>");
	echo('<b>Game?</b>');
    echo("</td><td>");
	echo('<b>Orig Author</b>');
    echo("</td><td>");
	 echo('<b>Functions</b>');
	   echo("</td><td>");
	 echo('<b>Base-Case</b>');
    echo("</td><td>");
	 echo('<b>Soln</b>');
	echo("</td></tr>\n");
$qstmnt="SELECT problem.problem_id AS problem_id,problem.name AS name,problem.email as email,problem.title as title,problem.status as status, problem.soln_pblm as soln_pblm,problem.game_prob_flag as game_prob_flag, problem.nm_author as nm_author,problem.docxfilenm as docxfilenm,problem.infilenm as infilenm,problem.pdffilenm as pdffilenm, School.s_name as s_name
FROM problem LEFT JOIN School ON problem.school_id=School.school_id;";
$stmt = $pdo->query($qstmnt);
while ( $row = $stmt->fetch(PDO::FETCH_ASSOC) ) {
    echo "<tr><td>";
	echo(htmlentities($row['problem_id']));
    echo("</td><td>");	
	echo(htmlentities($row['name']));
    echo("</td><td>");
    echo(htmlentities($row['email']));
	echo("</td><td>");  
	echo(htmlentities($row['s_name']));
    echo("</td><td>");
    echo(htmlentities($row['title']));
    echo("</td><td>");
	echo($row['status']);
    echo("</td><td>");
	echo(htmlentities($row['game_prob_flag']));
    echo("</td><td>");
	echo(htmlentities($row['nm_author']));
    
    echo("</td><td>");
    echo('<a href="editpblm.php?problem_id='.$row['problem_id'].'">Edit</a> / ');
    echo('<a href="deletepblm.php?problem_id='.$row['problem_id'].'">Del</a> / ');
	echo('<a href="downloadpblm.php?problem_id='.$row['problem_id'].'">Download</a>');
	  echo("</td><td>");
	echo('<form action = "QRPRepo.php" method = "POST" > <input type = "hidden" name = "preview" value ="'.$row['pdffilenm'].'"><input type = "submit" value ="PreView"></form>');
   	  echo("</td><td>");
	echo('<form action = "QRPRepo.php" method = "POST" > <input type = "hidden" name = "soln_preview" value ="'.$row['soln_pblm'].'"><input type = "submit" value ="PreView"></form>');

   echo("</td></tr>\n");
	
}
//echo ('"'.$preview.'"');
?>
</table>
<p></p>
<!-- <p><a href="add.php">Add New Manual</a></P> -->
<!--<a href="addPblm.php">Add Data and Pblm Files</a> -->
<p></p>
<a href="requestPblmNum.php"><b>Request New Problem Number</b></a>


<!-- <object data=<?php// echo('"'.$preveiw.'"'); ?> 
type= "application/pdf" width="100%" Height="50%"> -->
<?php 
if($preview !== "uploads/" and $preview !== "Null") {
	echo ('<iframe src="'.$preview.'"'.'width="100%" Height = "40%">');

	echo ('</iframe>');
}
?>
<!-- </object> -->
</body>
</html>