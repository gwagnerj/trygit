<?php
 session_start();
   $_SESSION['score'] = "0";
	
	//$_SESSION['count'] = 0;
	
Require_once "pdo.php";

// first do some error checking on the input.  If it is not OK set the session failure and send them back to QRPIndex.
if ( ! isset($_GET['problem_id']) ) {
  $_SESSION['error'] = "Missing problem number";
  header('Location: QRPindex.php');
  return;
}
if ( ! isset($_GET['dex_num']) ) {
  $_SESSION['error'] = "Missing index number";
  header('Location: QRPindex.php');
  return;
} 
 
if ($_GET['problem_id']<1 or $_GET['problem_id']>1000000)  {
  $_SESSION['error'] = "problem number out of range";
  header('Location: QRPindex.php');
  return;
}
if ($_GET['dex_num']<2 or $_GET['dex_num']>200)  {
  $_SESSION['error'] = "Index number out of range";
  header('Location: QRPindex.php');
  return;
}

$_SESSION['index'] = $_GET['dex_num'];


if ( isset($_GET['problem_id']) ) {
	$_SESSION['problem_id'] = $_GET['problem_id'];
}

if ( isset($_GET['dex_num']) ) {
	$_SESSION['index'] = $_GET['dex_num'];
}


// Next check the Qa table and see which values have non null values - for those 

$stmt = $pdo->prepare("SELECT * FROM Qa where problem_id = :problem_id AND dex = :dex");
$stmt->execute(array(":problem_id" => $_SESSION['problem_id'], ":dex" => $_SESSION['index']));
//$row = $stmt->fetch(PDO::FETCH_ASSOC);
$row = $stmt -> fetch();
if ( $row === false ) {
    $_SESSION['error'] = 'Bad value for problem_id';
    header( 'Location: QRPindex.php' ) ;
    return;
}

// initialize some variables
$soln = array_slice($row,6);
/* for($i=3;$i<=12;$i++){
	$soln[$i-3]=$row[$i];
} */

/* print_r($soln);
$k=0;
echo ('<br>');
echo $soln[$k];
echo ('<br>'); */

$probParts=0;
$partsFlag = array('a'=>false,'b'=>false,'c'=>false,'d'=>false,'e'=>false,'f'=>false,'g'=>false,'h'=>false,'i'=>false,'j'=>false);
$resp = array('a'=>"r",'b'=>"",'c'=>"",'d'=>"",'e'=>"",'f'=>"",'g'=>"",'h'=>"",'i'=>"",'j'=>"");
$corr = array('a'=>"n",'b'=>"",'c'=>"",'d'=>"",'e'=>"",'f'=>"",'g'=>"",'h'=>"",'i'=>"",'j'=>"");
$unit = array('a'=>"n",'b'=>"",'c'=>"",'d'=>"",'e'=>"",'f'=>"",'g'=>"",'h'=>"",'i'=>"",'j'=>"");
$tol=array('a'=>0.02,'b'=>0.02,'c'=>0.02,'d'=>0.02,'e'=>0.02,'f'=>0.02,'g'=>0.02,'h'=>0.02,'i'=>0.02,'j'=>0.02);
$wrongCount = array('a'=>0,'b'=>0,'c'=>0,'d'=>0,'e'=>0,'f'=>0,'g'=>0,'h'=>0,'i'=>0,'j'=>0);

$count='';  // counts the times the check button is placed
$score=0.0;

for ($i = 0;$i<=9; $i++){  // this would mean the database would always be in the same form
	if ($soln[$i]!=="Null") {
		$probParts = $probParts+1;
		$partsFlag[$i]=true;	
	
	}
	
//echo $partsFlag[$i].' ';
//echo $soln[$i];
//echo "<br>";
//echo array_keys($corr)[0];	
	
}
//echo "the number of parts for this problem is ". $probParts;	

// test to see if the instructor put in the code to get the answers					
					
	$dispAns=substr($_POST['dex_num'],0,7);

	if($dispAns=="McKetta" or ($_POST['dex_num']==1 and $disBasecaseAns))
	{
		if($disBasecaseAns and $_POST['dex_num']==1)
		{
			$index = $_POST['dex_num']+0;
		}
		else
		{
		$index=substr($_POST['dex_num'],7)+0;
		}
		$dispAnsflag=True;
	
	}
	else
	{

		$dispAnsflag=False;
		$index = $_POST['dex_num']+0;
	}	

	// keep track of the number of tries the student makes
if(!($_SESSION['count'])){
	$_SESSION['count'] = 1;

	$count=1;
}else{
	$count = $_SESSION['count'] + 1;
	$_SESSION['count'] = $count;

}
// read the student responses into an array
	$resp['a']=$_POST['a']+0;
	$resp['b']=floatval($_POST['b'])+0;
	$resp['c']=floatval($_POST['c'])+0;
	$resp['d']=floatval($_POST['d'])+0;
	$resp['e']=floatval($_POST['e'])+0;
	$resp['f']=floatval($_POST['f'])+0;
	$resp['g']=floatval($_POST['g'])+0;
	$resp['h']=floatval($_POST['h'])+0;
	$resp['i']=floatval($_POST['i'])+0;
	$resp['j']=floatval($_POST['j'])+0;

	//get the tolerance for each part - only really need to do this once on the get request - change if it is slow
	$stmt = $pdo->prepare("SELECT * FROM Problem where problem_id = :problem_id");
	$stmt->execute(array(":problem_id" => $_SESSION['problem_id']));
	//$row = $stmt->fetch(PDO::FETCH_ASSOC);
	$row = $stmt -> fetch();
	if ( $row === false ) {
		$_SESSION['error'] = 'Bad value for problem_id';
		header( 'Location: QRPindex.php' ) ;
		return;
	}	
	$probData=$row;	
	//echo $probData['tol_a'];
	
	$tol['a']=$probData['tol_a']*0.01;	
	$tol['b']=$probData['tol_b']*0.01;
	$tol['c']=$probData['tol_c']*0.01;	
	$tol['d']=$probData['tol_d']*0.01;
	$tol['e']=$probData['tol_e']*0.01;	
	$tol['f']=$probData['tol_f']*0.01;
	$tol['g']=$probData['tol_g']*0.01;	
	$tol['h']=$probData['tol_h']*0.01;
	$tol['i']=$probData['tol_i']*0.01;	
	$tol['j']=$probData['tol_j']*0.01;	
	
	$unit = array_slice($row,22,20);
	//print_r($unit);

	$tol_key=array_keys($tol);
	$resp_key=array_keys($resp);
	$corr_key=array_keys($corr);
	//$unit_key=array_keys($unit);
	
	For ($j=0; $j<10; $j++) {
	
	/* echo($soln[$j]);
	echo("<br>");
	echo($tol[$tol_key[$j]]);
	echo("<br>");
	echo($resp[$resp_key[$j]]);
	echo("<br>"); */
	
		If ($soln[$j]>((1-$tol[$tol_key[$j]])*$resp[$resp_key[$j]]) and ($soln[$j]<((1+$tol[$tol_key[$j]]))*($resp[$resp_key[$j]]))) //if the correct value is within the response plus or minus the tolerance
					{
							
							$corr[$corr_key[$j]]='Correct';
							$score=$score+1;		
					}
			Else
			{
			
					
				//count the number of times they got this part wrong if they entered a nonzero value
				
					if(!(isset($_SESSION['wrongCount[$j]'])))
					{
						
						$_SESSION['wrongCount[$j]'] = 0;
						$wrongCount[$j]=0;
						echo $_SESSION['wrongCount[$j]'];
					//	echo ($aWrongCount);
						
					}
					elseif ($resp[$resp_key[$j]]!==0)
					{
						
						$wrongCount[$j] = $_SESSION['wrongCount[$j]'] + 1;
						$_SESSION['wrongCount[$j]'] = $wrongCount[$j];
						$corr[$corr_key[$j]]='Not Correct';
					//	echo ($wrongCount[$j]);
					}
					else
					{
					$wrongCount[$j] = $_SESSION['wrongCount[$j]'];
						$_SESSION['wrongCount[$j]'] = $wrongCount[$j];
						$corr[$corr_key[$j]]='';
					//	echo ($wrongCount[$j]);	
					}
			}		

	}

	
	/* echo($resp[$resp_key[0]]);
	echo("<br>");
	echo($tol[$tol_key[0]]);
	echo("<br>");
	echo($resp['a']);
	echo("<br>");
	echo($corr['a']);
	echo("<br>");
	echo($soln['ans_a']);
	echo("<br>"); */
	
	  // we are coming through the first time
	
	$PScore=$score/$probParts*100;  
	$rand= rand(100000,999999);  // sets up the rtn code on other page
	$rand2=rand(0,9);				// sets up the rtn code on the other page
	$_SESSION['rand']=$rand;
	$_SESSION['rand2']=$rand2;

	
if(isset($_POST['dex_num']) && $index<=200 && $index>0 && $dispAnsflag)
	{
	echo "<table>";
	echo "Answers:";
	echo '<table border="1">';


	echo "<tr><th>index</th>";
	echo	"<th>a)</th>";
	echo	"<th>b)</th>";
	echo	"<th>c)</th>";
	echo	"<th>d)</th>";
	echo	"<th>e)</th>";
	echo	"<th>f)</th>";
	echo	"<th>g)</th>";
	echo	"<th>h)</th>";
	echo	"<th>i)</th>";
	echo	"<th>j)</th></tr>";
	echo	"<tr>";



	
		
		echo "<tr><td>";
		echo ($_SESSION['index']);
		echo ("</td><td>");
		echo ($soln['ans_a']);
		echo ("</td><td>");
		echo ($soln['ans_b']);
		echo ("</td><td>");
		echo ($soln['ans_c']);
		echo ("</td><td>");
		echo ($soln['ans_d']);
		echo ("</td><td>");
		echo ($soln['ans_e']);
		echo ("</td><td>");
		echo ($soln['ans_f']);
		echo ("</td><td>");
		echo ($soln['ans_g']);
		echo ("</td><td>");
		echo ($soln['ans_h']);
		echo ("</td><td>");
		echo ($soln['ans_i']);
		echo ("</td><td>");
		echo ($soln['ans_j']);
		echo ("</td>");
		
	}
?>
</table>



<!DOCTYPE html>
<html lang = "en">
<head>
<link rel="icon" type="image/png" href="McKetta.png" />  
<meta Charset = "utf-8">
<title>QRChecker</title>
<meta name="viewport" content="width=device-width, initial-scale=1" /> 
</head>

<body>
<header>
<h1>QRProblem Checker</h1>
</header>
<main>





<form autocomplete="off" method="POST" >
<!-- <p>Problem Number: <input type="text" name="problem_number" ></p> -->
<!-- <p> Please put in your index number </p> -->
<p><font color=#003399>Index Number: </font><input type="text" name="dex_num" size=3 value="<?php echo (htmlentities($_SESSION['index']))?>"  ></p>
<p> <strong> Fill in - then select "Check" </strong></p>

<!--<p> a): <input [ type=number]{width: 5%;} name="a" size = 10% value="<?php echo (htmlentities($resp['a']))?>" > <?php echo($unit[0]) ?> &nbsp - <b><?php echo ($corr['a']) ?> </b><?php if (isset($_POST['dex_num']) and @$wrongCount[0]>$hintLimit and $corr['a']=="Not Correct"){echo '<a href="hints/parta/parta.html" target = "_blank"> hints for this part </a>';} ?>  </p> -->

<?php
if ($partsFlag[0]){ ?> 
<p> a): <input [ type=number]{width: 5%;} name="a" size = 10% value="<?php echo (htmlentities($resp['a']))?>" > <?php echo($unit[0]) ?> &nbsp - <b><?php echo ($corr['a']) ?> </b><?php if (isset($_POST['dex_num']) and @$wrongCount[0]>$hintLimit and $corr['a']=="Not Correct"){echo '<a href="hints/parta/parta.html" target = "_blank"> hints for this part </a>';} ?>  </p>
<?php } 
if ($partsFlag[1]){ ?> 
<p> b): <input [ type=number]{width: 5%;} name="b" size = 10% value="<?php echo (htmlentities($resp['b']))?>" > <?php echo($unit[1]) ?> &nbsp - <b><?php echo ($corr['b']) ?> </b><?php if (isset($_POST['dex_num']) and @$wrongCount[1]>$hintLimit and $corr['b']=="Not Correct"){echo '<a href="hints/partb/partb.html" target = "_blank"> hints for this part </a>';} ?>  </p>
<?php } 
if ($partsFlag[2]){ ?> 
<p> c): <input [ type=number]{width: 5%;} name="c" size = 10% value="<?php echo (htmlentities($resp['c']))?>" > <?php echo($unit[2]) ?> &nbsp - <b><?php echo ($corr['c']) ?> </b><?php if (isset($_POST['dex_num']) and @$wrongCount[2]>$hintLimit and $corr['c']=="Not Correct"){echo '<a href="hints/partc/partc.html" target = "_blank"> hints for this part </a>';} ?>  </p>
<?php } 
if ($partsFlag[3]){ ?> 
<p> d): <input [ type=number]{width: 5%;} name="d" size = 10% value="<?php echo (htmlentities($resp['d']))?>" > <?php echo($unit[3]) ?> &nbsp - <b><?php echo ($corr['d']) ?> </b><?php if (isset($_POST['dex_num']) and @$wrongCount[3]>$hintLimit and $corr['d']=="Not Correct"){echo '<a href="hints/partd/partd.html" target = "_blank"> hints for this part </a>';} ?>  </p>
<?php } 
if ($partsFlag[4]){ ?> 
<p> e): <input [ type=number]{width: 5%;} name="e" size = 10% value="<?php echo (htmlentities($resp['e']))?>" > <?php echo($unit[4]) ?> &nbsp - <b><?php echo ($corr['e']) ?> </b><?php if (isset($_POST['dex_num']) and @$wrongCount[4]>$hintLimit and $corr['e']=="Not Correct"){echo '<a href="hints/parte/parte.html" target = "_blank"> hints for this part </a>';} ?>  </p>
<?php } 
if ($partsFlag[5]){ ?> 
<p> f): <input [ type=number]{width: 5%;} name="f" size = 10% value="<?php echo (htmlentities($resp['f']))?>" > <?php echo($unit[5]) ?> &nbsp - <b><?php echo ($corr['f']) ?> </b><?php if (isset($_POST['dex_num']) and @$wrongCount[5]>$hintLimit and $corr['f']=="Not Correct"){echo '<a href="hints/partf/partf.html" target = "_blank"> hints for this part </a>';} ?>  </p>
<?php } 
if ($partsFlag[6]){ ?> 
<p> g): <input [ type=number]{width: 5%;} name="g" size = 10% value="<?php echo (htmlentities($resp['g']))?>" > <?php echo($unit[6]) ?> &nbsp - <b><?php echo ($corr['g']) ?> </b><?php if (isset($_POST['dex_num']) and @$wrongCount[6]>$hintLimit and $corr['g']=="Not Correct"){echo '<a href="hints/partg/partg.html" target = "_blank"> hints for this part </a>';} ?>  </p>
<?php } 
if ($partsFlag[7]){ ?> 
<p> h): <input [ type=number]{width: 5%;} name="h" size = 10% value="<?php echo (htmlentities($resp['h']))?>" > <?php echo($unit[7]) ?> &nbsp - <b><?php echo ($corr['h']) ?> </b><?php if (isset($_POST['dex_num']) and @$wrongCount[7]>$hintLimit and $corr['h']=="Not Correct"){echo '<a href="hints/parth/parth.html" target = "_blank"> hints for this part </a>';} ?>  </p>
<?php } 
if ($partsFlag[8]){ ?> 
<p> i): <input [ type=number]{width: 5%;} name="i" size = 10% value="<?php echo (htmlentities($resp['i']))?>" > <?php echo($unit[8]) ?> &nbsp - <b><?php echo ($corr['i']) ?> </b><?php if (isset($_POST['dex_num']) and @$wrongCount[8]>$hintLimit and $corr['i']=="Not Correct"){echo '<a href="hints/parti/parti.html" target = "_blank"> hints for this part </a>';} ?>  </p>
<?php } 
if ($partsFlag[9]){ ?> 
<p> j): <input [ type=number]{width: 5%;} name="j" size = 10% value="<?php echo (htmlentities($resp['j']))?>" > <?php echo($unit[9]) ?> &nbsp - <b><?php echo ($corr['j']) ?> </b><?php if (isset($_POST['dex_num']) and @$wrongCount[9]>$hintLimit and $corr['j']=="Not Correct"){echo '<a href="hints/partj/partj.html" target = "_blank"> hints for this part </a>';} ?>  </p>
<?php } 

/* <p> c): <input [ type=number]{width: 5%;} name="c" size = 10% value="<?php echo (htmlentities($c))?>" > bar &nbsp - <b><?php echo ($ccorr) ?></b><?php if (isset($_POST['dex_num']) and @$cWrongCount>$hintLimit and $ccorr=="Not Correct"){echo '<a href="hints/partc/partcg.html" target = "_blank"> hints for this part </a>';} ?> </p>
<p> d): <input [ type=number]{width: 5%;} name="d" size = 10% value="<?php echo (htmlentities($d))?>" > kJ/kg &nbsp - <b><?php echo ($dcorr) ?></b><?php if (isset($_POST['dex_num']) and @$dWrongCount>$hintLimit and $dcorr=="Not Correct"){echo '<a href="hints/partd/partdh.html" target = "_blank"> hints for this part </a>';} ?> </p>
<p> e): <input [ type=number]{width: 5%;} name="e" size = 10% value="<?php echo (htmlentities($e))?>" > kg/min &nbsp - <b><?php echo ($ecorr) ?></b><?php if (isset($_POST['dex_num']) and @$eWrongCount>$hintLimit and $ecorr=="Not Correct"){echo '<a href="hints/parte/parte.html" target = "_blank"> hints for this part </a>';} ?> </p>
<p> f): <input [ type=number]{width: 5%;} name="f" size = 10% value="<?php echo (htmlentities($f))?>" > deg C &nbsp - <b><?php echo ($fcorr) ?></b><?php if (isset($_POST['dex_num']) and @$fWrongCount>$hintLimit and $fcorr=="Not Correct"){echo '<a href="hints/partf/partf.html" target = "_blank"> hints for this part </a>';} ?> </p>
<p> g): <input [ type=number]{width: 5%;} name="g" size = 10% value="<?php echo (htmlentities($g))?>" > bar &nbsp - <b><?php echo ($gcorr) ?></b><?php if (isset($_POST['dex_num']) and @$gWrongCount>$hintLimit and $gcorr=="Not Correct"){echo '<a href="hints/partc/partcg.html" target = "_blank"> hints for this part </a>';} ?> </p>
<p> h): <input [ type=number]{width: 5%;} name="h" size = 10% value="<?php echo (htmlentities($h))?>" > kW &nbsp - <b><?php echo ($hcorr) ?></b><?php if (isset($_POST['dex_num']) and @$hWrongCount>$hintLimit and $hcorr=="Not Correct"){echo '<a href="hints/partd/partdh.html" target = "_blank"> hints for this part </a>';} ?> </p>
  */
?>

<!--<p>Grading Scheme: <input type="text" name="grade_scheme" ></p> -->
<p><input type = "submit" value="Check" size="10" style = "width: 30%; background-color: #003399; color: white"/> &nbsp &nbsp <b> <font size="4" color="Navy">Score:  <?php echo ($PScore) ?>%</font></b></p>

</form>

<p> Count: <?php echo ($count) ?> </p>

<!-- <form method="get" >
<p><input type = "submit" value="Finished"/> </p>
</form> -->

<form action="rtnCode.php" method="POST">
 <hr>
<p><b><font Color="red">When Finished:</font></b></p>
  <!--<input type="hidden" name="score" value=<?php echo ($score) ?> /> -->
  <?php $_SESSION['score'] = $PScore; $_SESSION['index'] = $index; $_SESSION['count'] = $count; ?>
 <b><input type="submit" value="Get rtn Code" style = "width: 30%; background-color:yellow "></b>
 <p><br> </p>
 <hr>
</form>






</main>
</body>
</html>