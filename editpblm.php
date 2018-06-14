
<?php
require_once "pdo.php";
session_start();

if ( isset($_POST['name']) or isset($_POST['email'])
     or isset($_POST['title']) or isset($_POST['s_name']) ) {

   // Data validation
	
	if ( strlen($_POST['name']) < 1 || strlen($_POST['title']) < 1) {
        $_SESSION['error'] = 'Missing data';
        header("Location: editpblm.php?problem_id=".$_POST['problem_id']);
        return;
    }

    if ( strpos($_POST['email'],'@') === false ) {
        $_SESSION['error'] = 'Bad data';
        header("Location: editpblm.php?problem_id=".$_POST['problem_id']);
        return;
    }
	$problem_id=$_POST['problem_id'];

	
	//Get the filename from the docxfile that was uploaded
	
		if($_FILES['docxfile']['name']) {
			$filename=explode(".",$_FILES['docxfile']['name']); // divides the file into its name and extension puts it into an array
				if ($filename[1]=='docx'){ // this is the extension
					$docxfile=addslashes($_FILES['docxfile']['tmp_name']);
					$docxname=addslashes($_FILES['docxfile']['name']);
					$docxfile=file_get_contents($docxfile);
					
	//this code needs work			
					$docxname = $_FILES['docxfile']['name'];
					$tmp_docxname =  $_FILES['docxfile']['tmp_name'];
					$location = "uploads/"; // This is the local file directory name where the files get saved
				}
				
				
				// insert into problems with temporary file names for the docx, input data and pdf file
				$sql = "UPDATE Problem SET  docxfilenm = :docxfilenm 	
				WHERE problem_id=:problem_id";
						$stmt = $pdo->prepare($sql);
						$stmt->execute(array(':docxfilenm'=> $docxname,	':problem_id' => $_POST['problem_id']));
				
				
				if (fnmatch("P*_d_*",$docxname,FNM_CASEFOLD ) ){ // ignore the case when matching
						$newDocxNm = $docxname;
				}
				else if($docxname!==""){
						$newDocxNm = "P".$problem_id."_d_".$docxname;
				} else {
					$newDocxNm = "P".$problem_id."_d_problemStatement.docx";
				}
				
				$sql = "UPDATE problem SET docxfilenm = :newDocxNm WHERE problem_id = :pblm_num";
				$stmt = $pdo->prepare($sql);
				$stmt->execute(array(
					':newDocxNm' => $newDocxNm,
					':pblm_num' => $_POST['problem_id']));
				
			// now upload docx, input and pdf files
				$pathName = 'uploads/'.$newDocxNm;
				if (move_uploaded_file($_FILES['docxfile']['tmp_name'], $pathName)){
					$_SESSION['success'] = $_SESSION['success'].'DocxFile upload successful';
				}
		}  


//Get the filename from the pdffile (base-case) that was uploaded
		if($_FILES['pdffile']['name']) {
			$filename=explode(".",$_FILES['pdffile']['name']); // divides the file into its name and extension puts it into an array
			if ($filename[1]=='pdf'){ // this is the extension
				$pdffile=addslashes($_FILES['pdffile']['tmp_name']);
				$pdfname=addslashes($_FILES['pdffile']['name']);
				$pdffile=file_get_contents($pdffile);
				$pdfname = $_FILES['pdffile']['name'];
				$tmp_pdfname =  $_FILES['pdffile']['tmp_name'];
				$location = "uploads/"; // This is the local file directory name where the files get saved
			}

			$sql = "UPDATE Problem SET  pdffilenm = :pdffilenm 	
					WHERE problem_id=:problem_id";
			$stmt = $pdo->prepare($sql);
			$stmt->execute(array(':pdffilenm'=> $pdfname,	':problem_id' => $_POST['problem_id']));

			if (fnmatch("P*_p_*",$pdfname,FNM_CASEFOLD ) ){
					$newPdfNm = $pdfname;
			}
			elseif($pdfname !=="" ) {
					$newPdfNm = "P".$problem_id."_p_".$pdfname;
			} else {
					$newPdfNm = "P".$problem_id."_p_basecase.pdf";
			}
		
			$sql = "UPDATE problem SET pdffilenm = :newPdfNm WHERE problem_id = :pblm_num";
			$stmt = $pdo->prepare($sql);
			$stmt->execute(array(
				':newPdfNm' => $newPdfNm,
				':pblm_num' => $_POST['problem_id']));
		
		//upload file
			$pathName = 'uploads/'.$newPdfNm;
			if (move_uploaded_file($_FILES['pdffile']['tmp_name'], $pathName)){
				
				$_SESSION['success'] = $_SESSION['success'].'PdfFile upload successful';
			}
		} 
		
		//Get the filename from the solnfile if it was uploaded
		if($_FILES['solnfile']['name']) {
			$filename=explode(".",$_FILES['solnfile']['name']); // divides the file into its name and extension puts it into an array
			if ($filename[1]=='pdf'){ // this is the extension
				$solnfile=addslashes($_FILES['solnfile']['tmp_name']);
				$solnname=addslashes($_FILES['solnfile']['name']);
				$solnfile=file_get_contents($solnfile);
				$solnname = $_FILES['solnfile']['name'];
				$tmp_solnname =  $_FILES['solnfile']['tmp_name'];
				$location = "uploads/"; // This is the local file directory name where the files get saved
			}
		
			$sql = "UPDATE Problem SET  soln_pblm = :solnfilenm 	
					WHERE problem_id=:problem_id";
			$stmt = $pdo->prepare($sql);
			$stmt->execute(array(':solnfilenm'=> $solnname,	':problem_id' => $_POST['problem_id']));
			
			if (fnmatch("P*_s_*",$solnname,FNM_CASEFOLD ) ){
				$newSolnNm = $solnname;
			}
			else if ($solnname !=="" ){
				$newSolnNm = "P".$problem_id."_s_".$solnname;
			} else {
				$newSolnNm = "P".$problem_id."_s_solnfile.pdf";
			}
	
			$sql = "UPDATE problem SET soln_pblm = :newSolnNm WHERE problem_id = :pblm_num";
			$stmt = $pdo->prepare($sql);
			$stmt->execute(array(
				':newSolnNm' => $newSolnNm,
				':pblm_num' => $_POST['problem_id']));
	
			$pathName = 'uploads/'.$newSolnNm;
			if (move_uploaded_file($_FILES['solnfile']['tmp_name'], $pathName)){
				
				$_SESSION['success'] = $_SESSION['success'].'solnFile upload successful';
			}
		} 
		
		// now get input data
	if($_FILES['inputdata']['name']) {
			$filename=explode(".",$_FILES['inputdata']['name']); // divides the file into its name and extension puts it into an array
			if ($filename[1]=='csv'){ // this is the extension
				$inputdata=addslashes($_FILES['inputdata']['tmp_name']);
				$inputname=addslashes($_FILES['inputdata']['name']);
				$inputdata=file_get_contents($inputdata);
				$inputname = $_FILES['inputdata']['name'];
				$tmp_inputname =  $_FILES['inputdata']['tmp_name'];
				$location = "uploads/"; // This is the local file directory name where the files get saved
			}
		
			$sql = "UPDATE Problem SET  infilenm = :infilenm 	
						WHERE problem_id=:problem_id";
			$stmt = $pdo->prepare($sql);
			$stmt->execute(array(':infilenm'=> $inputname,	':problem_id' => $_POST['problem_id']));

			if (fnmatch("P*_i_*",$inputname,FNM_CASEFOLD ) ){
				$newInputNm = $inputname;
			}
			else if($inputname !==""){
				$newInputNm = "P".$problem_id."_i_".$inputname;
			} else {
				$newInputNm = "P".$problem_id."_i_inputfile.csv";
			}
	
			$sql = "UPDATE problem SET infilenm = :newInputNm WHERE problem_id = :pblm_num";
			$stmt = $pdo->prepare($sql);
			$stmt->execute(array(
				':newInputNm' => $newInputNm,
				':pblm_num' => $_POST['problem_id']));	
			
			$pathName = 'uploads/'.$newInputNm;
			if (move_uploaded_file($_FILES['inputdata']['tmp_name'], $pathName)){
				$_SESSION['success'] = $_SESSION['success'].'Input data file upload successful';
			}
	} 	 
// hint a file
		if($_FILES['hint_aFile']['name']) {
			$filename=explode(".",$_FILES['hint_aFile']['name']); // divides the file into its name and extension puts it into an array
			
			$hint_aFile=addslashes($_FILES['hint_aFile']['tmp_name']);
			$hint_aname=addslashes($_FILES['hint_aFile']['name']);
			$hint_aFile=file_get_contents($hint_aFile);
			$hint_aname = $_FILES['hint_aFile']['name'];
			$tmp_hint_aname =  $_FILES['hint_aFile']['tmp_name'];
			$location = "uploads/"; // This is the local file directory name where the files get saved
			
			$sql = "UPDATE Problem SET  hint_a = :hint_a 	
						WHERE problem_id=:problem_id";
			$stmt = $pdo->prepare($sql);
			$stmt->execute(array(':hint_a'=> $hint_aname,	':problem_id' => $_POST['problem_id']));
			
			if (fnmatch("P*_ha_*",$hint_aname,FNM_CASEFOLD ) ){ // ignore the case when matching
			$newhint_aNm = $hint_aname;
			}
			else if($hint_aname !== ""){
				$newhint_aNm = "P".$problem_id."_ha_".$hint_aname;
			} else {
				$newhint_aNm = "P".$problem_id."_ha_hint_a.html";
			}
			
			$pathName = 'uploads/'.$newhint_aNm;
			if (move_uploaded_file($_FILES['hint_aFile']['tmp_name'], $pathName)){
				$_SESSION['success'] = $_SESSION['success'].'Hint_aFile upload successful';
			}
		}

		
// get the new school_id if it has been updated
		
		
			$stmt = $pdo->prepare("SELECT * FROM school where s_name = :xyz");
			$stmt->execute(array(":xyz" => $_POST['s_name']));
			$row = $stmt->fetch(PDO::FETCH_ASSOC);
			$school_id=$row['school_id'];
	
		
	
			$sql = "UPDATE problem SET hint_a = :newhint_aNm WHERE problem_id = :pblm_num";
			$stmt = $pdo->prepare($sql);
			$stmt->execute(array(
				':newhint_aNm' => $newhint_aNm,
				':pblm_num' => $_POST['problem_id']));
	
			$_SESSION['success'] = 'Record updated';
	
			if($_FILES['Qa']['name']){
					$filename=explode(".",$_FILES['Qa']['name']);
					if($filename[1]=='csv') {  // this is the file extension where the 0 entry is the file name
						$handle = fopen($_FILES['Qa']['tmp_name'], "r");
						$lines=0;  //set this to ignore the header row in the csv file
		
						While($data=fgetcsv($handle)) {
							 If ($lines==0){
								// put the tolerances in the problem table
								$sql = "UPDATE Problem SET tol_a = :tol_a, tol_b = :tol_b,tol_c = :tol_c, tol_d = :tol_d, 
										tol_e = :tol_e, tol_f = :tol_f,tol_g = :tol_g, tol_h = :tol_h,tol_i = :tol_i, tol_j = :tol_j
										WHERE problem_id = :pblm_num";
								$stmt = $pdo->prepare($sql);
								$stmt->execute(array(
										':tol_a' => $data[1],
										':tol_b' => $data[2],
										':tol_c' => $data[3],
										':tol_d' => $data[4],
										':tol_e' => $data[5],
										':tol_f' => $data[6],
										':tol_g' => $data[7],
										':tol_h' => $data[8],
										':tol_i' => $data[9],
										':tol_j' => $data[10],
										':pblm_num' => $_POST['problem_id']));
							} 
							If ($lines==1){
								// put the units in the problem table
								$sql = "UPDATE Problem SET units_a = :units_a, units_b = :units_b,units_c = :units_c, units_d = :units_d, 
										units_e = :units_e, units_f = :units_f,units_g = :units_g, units_h = :units_h,units_i = :units_i, units_j = :units_j
										WHERE problem_id = :pblm_num";
								$stmt = $pdo->prepare($sql);
								$stmt->execute(array(
										':units_a' => $data[1],
										':units_b' => $data[2],
										':units_c' => $data[3],
										':units_d' => $data[4],
										':units_e' => $data[5],
										':units_f' => $data[6],
										':units_g' => $data[7],
										':units_h' => $data[8],
										':units_i' => $data[9],
										':units_j' => $data[10],
										':pblm_num' => $_POST['problem_id']));
							} 
							If ($lines>1){
								
								// put the answer data into the data base
								$sql = "UPDATE Qa SET problem_id = :problem_id, dex = :dex, ans_a = :ans_a, ans_b = :ans_b, ans_c = :ans_c
									,ans_d = :ans_d, ans_e = :ans_e, ans_f = :ans_f, ans_g = :ans_g, ans_h = :ans_h, ans_i = :ans_i, ans_j = :ans_j,g1 = :g1, g2 = :g2, g3 = :g3
									WHERE problem_id = :problem_id AND dex = :dex";
								
								$stmt = $pdo->prepare($sql);
								$stmt->execute(array(
									':problem_id' => $_POST['problem_id'],
									':dex' => $data[0],
									':ans_a' => $data[1],
									':ans_b' => $data[2],
									':ans_c' => $data[3],
									':ans_d' => $data[4],
									':ans_e' => $data[5],
									':ans_f' => $data[6],
									':ans_g' => $data[7],
									':ans_h' => $data[8],
									':ans_i' => $data[9],
									':ans_j' => $data[10],
									':g1' => $data[11],
									':g2' => $data[12],
									':g3' => $data[13]));
				
							}
							$lines = $lines+1;
						}
						fclose($handle);
					}else {$_SESSION['error']=' Answer file is not a csv file';}
				}else {$_SESSION['error']=' Warning - Ans file not loaded';}
				
			// this should conserve the data already input and 
	//die();
	$sql = "SELECT * FROM Problem JOIN School JOIN Qa ON (Problem.school_id=School.school_id AND Qa.problem_id=Problem.problem_id AND Qa.dex=1 )";
	$stmt = $pdo->prepare($sql);
	$stmt->execute(array(
	));
	$row = $stmt->fetch(PDO::FETCH_ASSOC);
	$status = $row['problem.status'];
	
	
	If($row['problem.game_prob_flag']==0){
		IF (($row['problem.docxfilenm']!=="NULL") AND ($row['problem.infilenm']!=="NULL") AND ($row['qa.dex']!=="NULL")){
			$status = "New Compl";
		}
	} Elseif(($row['problem.docxfilenm']!=="NULL") AND ($row['qa.dex']!=="NULL")){
		$status = "New Compl";
		
	}
    $sql = "UPDATE problem SET name = :name,
            email = :email, title = :title,school_id=:school_id,status = :status
            WHERE problem_id = :problem_id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(array(
        ':name' => $_POST['name'],
        ':email' => $_POST['email'],
        ':title' => $_POST['title'],
		':school_id' => $row['school_id'],
        ':problem_id' => $_POST['problem_id'],
		':status' => $status));
    $_SESSION['success'] = 'Record updated';
	
	// If all fields have values we should set the status to new file
	
    header( 'Location: QRPRepo.php' ) ;
    return;
}

// Guardian: Make sure that problem_id is present
if ( ! isset($_GET['problem_id']) ) {
  $_SESSION['error'] = "Missing problem_id";
  header('Location: QRPRepo.php');
  return;
}


$stmt = $pdo->prepare("SELECT * FROM problem where problem_id = :xyz");
$stmt->execute(array(":xyz" => $_GET['problem_id']));
$row = $stmt->fetch(PDO::FETCH_ASSOC);
if ( $row === false ) {
    $_SESSION['error'] = 'Bad value for problem_id';
    header( 'Location: QRPRepo.php' ) ;
    return;
}

// Flash pattern
if ( isset($_SESSION['error']) ) {
    echo '<p style="color:red">'.$_SESSION['error']."</p>\n";
    unset($_SESSION['error']);
}

$n = htmlentities($row['name']);
$e = htmlentities($row['email']);
$p = htmlentities($row['title']);
$gf = htmlentities($row['game_prob_flag']);
//print_r($gf);
$in = htmlentities($row['infilenm']);

$df = htmlentities($row['docxfilenm']);
$problem_id = $row['problem_id'];
$school_id= $row['school_id'];

// now get the current school name
$stmt = $pdo->prepare("SELECT * FROM school where school_id = :xyz");
$stmt->execute(array(":xyz" => $school_id));
$row = $stmt->fetch(PDO::FETCH_ASSOC);
$s = htmlentities($row['s_name']);

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


<p>Edit Problem Meta Data</p>
<form action="" method="post" enctype="multipart/form-data">
<p>Name:
<input type="text" name="name" value="<?= $n ?>"></p>
<p>Email:
<input type="text" name="email" value="<?= $e ?>"></p>
<p>title:
<input type="text" name="title" value="<?= $p ?>"></p>
<p>
<label> School or Organization:
		<select required name = "s_name">
			<option selected = "selected"> <?php echo $s ?></option>
			<?php foreach ($s_name as $values){?>
			<option><?php echo $values;?></option>
			<?php }?>
		</select>
	</label> 

</p>
<p>Answers File: <input type='file' accept='.csv' name='Qa'/></p>

<?php if(!$gf){ // only have this input if it is not a game problem
	?>  
<p><font color="black">Input File: </font><input type='file' accept='.csv'  name='inputdata'/></p>
<?php } 
?>

<p>Problem statement file: <input type='file' accept='.docx' name='docxfile'/></p>
<p>Base-case  file: <input type='file' accept='.pdf' name='pdffile'/></p>
<p>Worked out Solution  file: <input type='file' accept='.pdf' name='solnfile'/></p>
<p><hr></p>
<p>hint_a file: <input type='file' accept='.html' name='hint_aFile'/></p>

<input type="hidden" name="problem_id" value="<?= $problem_id ?>">
<p><input type="submit" value="Update"/>
<a href="QRPRepo.php">Cancel</a></p>
</form>
</body>
</html>
