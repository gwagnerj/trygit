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
    
	echo("</td></tr>\n");
$qstmnt="SELECT problem.problem_id AS problem_id,problem.name AS name,problem.email as email,problem.title as title,problem.status as status, problem.docxfilenm as docxfilenm,problem.infilenm as infilenm,School.s_name as s_name
FROM problem LEFT JOIN School ON problem.school_id=School.school_id;";
 
// $qstmnt="SELECT * FROM problem LEFT JOIN School ON problem.school_id=School.school_id;";
 
$stmt = $pdo->query($qstmnt);



while ( $row = $stmt->fetch(PDO::FETCH_ASSOC) ) {
    echo "<tr><td>";
    //Print_r($row);
	//die();
	echo(htmlentities($row['problem_id'])*10);
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
	echo('<form action = "index.php" method = "post"> <botton type = "submit" name = "preview" value ="'.$row['problem_id'].'">Preview</button> </form>');
   
   echo("</td></tr>\n");
	
}
?>
</table>
<p></p>
<!-- <p><a href="add.php">Add New Manual</a></P> -->
<!--<a href="addPblm.php">Add Data and Pblm Files</a> -->
<p></p>
<a href="requestPblmNum.php"><b>Request New Problem Number</b></a>
<object data="uploads/P27_p_P10_base_street covers example problem.pdf" 
type= "application/pdf" width="100%" Height="50%">
<iframe src="uploads/P27_p_P10_base_street covers example problem.pdf#page=1" width="100%" Height = "50%">

</iframe>
</object> 
</body>
</html>