<?php
require_once "pdo.php";
session_start();
?>
<html>
<head></head><body>
<?php
if ( isset($_SESSION['error']) ) {
    echo '<p style="color:red">'.$_SESSION['error']."</p>\n";
    unset($_SESSION['error']);
}
if ( isset($_SESSION['success']) ) {
    echo '<p style="color:green">'.$_SESSION['success']."</p>\n";
    unset($_SESSION['success']);
}



echo('<table border="1">'."\n");
	echo("</td><td>");
	echo('<b>Problem Num</b>');
	echo("</td><td>");
	echo('<b>Contributor Name</b>');
    echo("</td><td>");
    echo('<b>Contributor Email</b>');
    echo("</td><td>");
    echo('<b>Pblm Title</b>');
    echo("</td><td>");
	echo('<b>Status</b>');
    echo("</td><td>");
	echo('<b>word document</b>');
    echo("</td><td>");
	echo('<b>input data</b>');
    echo("</td><td>");
	 echo('<b>University</b>');
    echo("</td><td>");
	 echo('<b>Functions</b>');
    echo("</td><td>");
	 echo('<b>Base-Case</b>');
	echo("</td></tr>\n");
$qstmnt="SELECT problem.problem_id AS problem_id,problem.name AS name,problem.email as email,problem.title as title,problem.status as status, problem.docxfilenm as docxfilenm,problem.infilenm as infilenm,problem.pdffilenm as pdffilenm, School.s_name as s_name
FROM problem LEFT JOIN School ON problem.school_id=School.school_id;";
 
// $qstmnt="SELECT * FROM problem LEFT JOIN School ON problem.school_id=School.school_id;";
 
$stmt = $pdo->query($qstmnt);

$preview="Null";

while ( $row = $stmt->fetch(PDO::FETCH_ASSOC) ) {
    echo "<tr><td>";
    //Print_r($row);
	//die();
	//if they request the file then set the $preview variable to the name of the file
	if (isset($_POST['preview']) and $_POST['preview']= htmlentities($row['problem_id'])){
		$preview='uploads/'.htmlentities($row['pdffilenm']);
	}

	
	echo(htmlentities($row['problem_id']));
    echo("</td><td>");	
	echo(htmlentities($row['name']));
    echo("</td><td>");
    echo(htmlentities($row['email']));
    echo("</td><td>");
    echo(htmlentities($row['title']));
    echo("</td><td>");
	echo($row['status']);
    echo("</td><td>");
	echo(htmlentities($row['docxfilenm']));
    echo("</td><td>");
	echo(htmlentities($row['infilenm']));
    echo("</td><td>");  
	echo(htmlentities($row['s_name']));
    echo("</td><td>");
    echo('<a href="editpblm.php?problem_id='.$row['problem_id'].'">Edit</a> / ');
    echo('<a href="deletepblm.php?problem_id='.$row['problem_id'].'">Del</a> / ');
	echo('<a href="downloadpblm.php?problem_id='.$row['problem_id'].'">Download</a>');
	  echo("</td><td>");
	echo('<form action = "index.php" method = "post"> <input type = "hidden" name = "preview" value ="'.$row['problem_id'].'"><input type = "submit" value ="PreView"></form>');
 
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