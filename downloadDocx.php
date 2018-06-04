<?php
require_once "pdo.php";
session_start();

 $file_name=$_SESSION['file_name'];
 $pblm_num=$_SESSION['success'];
 $game_prob_flag=$_SESSION['game_prob_flag'];
 
 echo '<p>'.$game_prob_flag.'</p>';
 echo '<p>'.$pblm_num.'</p>';
 echo "<br>"; 
	echo '<font color=red>'."Click below to download the template for this problem - you may want to create a directory for it using the problem number".'</font>';
	echo "<br>";
	echo "<br>";
if ($game_prob_flag==0) {
    echo '<a href="downloads/QR Template v500C.docx" download="'.$file_name. '">'.$file_name.'.docx </a>';
}
else {
	 echo '<a href="downloads/QR Game Template v500C.docx" download="'.$file_name. '">'.$file_name.'.docx </a>';
}
	echo "<br>";
	 echo "<br>"; 
	  echo "<br>"; 
    echo 'The latest QR Solver excel macro enabled template is below - you may have to enable macros to use it.';
	echo "<br>";
	echo "<br>";
    echo '<a href="downloads/QRP solver A500C.xltm"> QRP solver A500C.xltm </a>';
	echo "<br>";
		
	
   

?>

<p> </p>
<a href="index.php">Finished or Cancel</a>

