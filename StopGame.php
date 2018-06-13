<?php
 session_start();
$elapTime = $_SESSION['time']-$_SESSION['startTime'];
 $minutes = floor(($elapTime / 60) % 60);
$seconds = $elapTime % 60;


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



<p><b><font size=7><p>Your Score:<font color = "blue"> <?php echo (round( $_SESSION['score']))?>%</font></font></b></p> 
<p><b><font size=7><p>Your Points:<font color = "blue"> <?php echo (round( $_SESSION['points']))?></font></font></b></p> 
<p><b><font size=7><p>Your Time:<font color = "blue"> <?php echo "$minutes:$seconds"?></font></font></b></p> 
<p><b><font size=7><p>Number of Tries:<font color = "blue"> <?php echo ($_SESSION['count'])?></font></font></b></p> 
<p><br></p>

<a href="QRGameindex.php"><b><font size = 6> New Problem </font></b></a>

</main>



<footer>
<!--<p>This is the footer</p> -->
</footer>
</body>
</html>