<?php
 session_start();
  
 
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
<!--<h1>this is an application that gets the return code from the score</h1>-->
</header>
<main>
<p><b><font size=5><p>Your Score:<font color = "blue"> <?php echo( $_SESSION['score'])?>%</font></font></b></p> 

<p><br></p>

<a href="QRGameindex.php"><b> New Problem</b></a>

</main>



<footer>
<!--<p>This is the footer</p> -->
</footer>
</body>
</html>