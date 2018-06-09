<?php
 session_start();
   $_SESSION['score'] = "0";
	$_SESSION['index'] = "0";
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
//echo $_GET['problem_id'];
//echo "<br>";
//echo $_GET['dex_num'];

// Next check the Qa table and see which values have non null values - for those 

$stmt = $pdo->prepare("SELECT * FROM Qa where problem_id = :problem_id AND dex = :dex");
$stmt->execute(array(":problem_id" => $_GET['problem_id'], ":dex" => $_GET['dex_num']));
//$row = $stmt->fetch(PDO::FETCH_ASSOC);
$row = $stmt -> fetch();
if ( $row === false ) {
    $_SESSION['error'] = 'Bad value for problem_id';
    header( 'Location: QRPindex.php' ) ;
    return;
}

// initialize some variables
$soln=$row;
$probParts=0;
$partsFlag = array('a'=>false,'b'=>false,'c'=>false,'d'=>false,'e'=>false,'f'=>false,'g'=>false,'h'=>false,'i'=>false,'j'=>false);
$resp = array('a'=>"",'b'=>"",'c'=>"",'d'=>"",'e'=>"",'f'=>"",'g'=>"",'h'=>"",'i'=>"",'j'=>"");
$correct = array('a'=>"",'b'=>"",'c'=>"",'d'=>"",'e'=>"",'f'=>"",'g'=>"",'h'=>"",'i'=>"",'j'=>"");
$tol=array('a'=>0.02,'b'=>0.02,'c'=>0.02,'d'=>0.02,'e'=>0.02,'f'=>0.02,'g'=>0.02,'h'=>0.02,'i'=>0.02,'j'=>0.02);
$wrongCount = array('a'=>0,'b'=>0,'c'=>0,'d'=>0,'e'=>0,'f'=>0,'g'=>0,'h'=>0,'i'=>0,'j'=>0);

$count='';  // counts the times the check button is placed
$score=0.0;

for ($i = 3;$i<=12; $i++){  // this would mean the database would always be in the same form
	if ($soln[$i]!=="Null") {
		$probParts = $probParts+1;
		$partsFlag[$i-3]=true;	
	
	
	}
//echo $partsFlag[$i-3].' ';
//echo $soln[$i];
//echo "<br>";
	
	
}
echo "the number of parts for this problem is ". $probParts;	

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
	$resp['b']=$_POST['b']+0;
	$resp['c']=$_POST['c']+0;
	$resp['d']=$_POST['d']+0;
	$resp['e']=$_POST['e']+0;
	$resp['f']=$_POST['f']+0;
	$resp['g']=$_POST['g']+0;
	$resp['h']=$_POST['h']+0;
	$resp['i']=$_POST['i']+0;
	$resp['j']=$_POST['j']+0;

	//get the tolerance for each part
	$stmt = $pdo->prepare("SELECT * FROM Problem where problem_id = :problem_id");
	$stmt->execute(array(":problem_id" => $_GET['problem_id']));
	//$row = $stmt->fetch(PDO::FETCH_ASSOC);
	$row = $stmt -> fetch();
	if ( $row === false ) {
		$_SESSION['error'] = 'Bad value for problem_id';
		header( 'Location: QRPindex.php' ) ;
		return;
	}	
	$probData=$row;	
	$tol['a']=$probData['a'];	
	$tol['b']=$probData['b'];
	$tol['c']=$probData['c'];	
	$tol['d']=$probData['d'];
	$tol['e']=$probData['e'];	
	$tol['f']=$probData['f'];
	$tol['g']=$probData['g'];	
	$tol['h']=$probData['h'];
	$tol['i']=$probData['i'];	
	$tol['j']=$probData['j'];	


	For ($j=0; $j<10; $j++) {
	
	
		If ($soln[$j]>(1-$tol[$j])*$resp[$j] and $soln[$j]<(1+$tol[$j])*$resp[$j]) //if the correct value is within the response plus or minus the tolerance
					{
							
							$correct[$j]='Correct';
							$score=$score+1;		
					}
			Else
			{
			
					
				//count the number of times they got this part wrong if they entered a nonzero value
				
					if(!(isset($_SESSION['$wrongCount[$j]'])))
					{
						
						$_SESSION['wrongCount[$j]'] = 0;
						$wrongCount[$j]=0;
					//	echo ($aWrongCount);
						
					}
					elseif ($resp[$j]!==0)
					{
						
						$wrongCount[$j] = $_SESSION['wrongCount[$j]'] + 1;
						$_SESSION['wrongCount[$j]'] = $wrongCount[$j];
						$correct[$j]='Not Correct';
					//	echo ($wrongCount[$j]);
					}
					else
					{
					$wrongCount[$j] = $_SESSION['wrongCount[$j]'];
						$_SESSION['wrongCount[$j]'] = $wrongCount[$j];
						$correct[$j]='';
					//	echo ($wrongCount[$j]);	
					}
			}		

	}




?>

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
</main>
</body>
</html>