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
			else{$_SESSION['error']='Docx file not loaded';
				header( 'Location: index.php' ) ;
				return;	
			}
		}
		else {$_SESSION['error']='Docxfile not loaded';
			header( 'Location: index.php' ) ;
			return;	
		} 
	//Get the filename from the pdffile (base-case) that was uploaded
		if($_FILES['pdffile']['name']) {
			$filename=explode(".",$_FILES['pdffile']['name']); // divides the file into its name and extension puts it into an array
			if ($filename[1]=='pdf'){ // this is the extension
		/* print_r ($filename[1]);
		die; */

		
				$pdffile=addslashes($_FILES['pdffile']['tmp_name']);
				$pdfname=addslashes($_FILES['pdffile']['name']);
				$pdffile=file_get_contents($pdffile);
				
//this code needs work			
				$pdfname = $_FILES['pdffile']['name'];
				$tmp_pdfname =  $_FILES['pdffile']['tmp_name'];
				$location = "uploads/"; // This is the local file directory name where the files get saved
			}
			else{$_SESSION['error']='pdf (base-case) file not loaded';
				header( 'Location: index.php' ) ;
				return;	
			}
		}
		else {$_SESSION['error']='pdffile (Basecase) not loaded';
			header( 'Location: index.php' ) ;
			return;	
		}
		
		// now get input data
	if($_FILES['inputdata']['name']) {
			$filename=explode(".",$_FILES['inputdata']['name']); // divides the file into its name and extension puts it into an array
			if ($filename[1]=='csv'){ // this is the extension
				$inputdata=addslashes($_FILES['inputdata']['tmp_name']);
				$inputname=addslashes($_FILES['inputdata']['name']);
				$inputdata=file_get_contents($inputdata);
				
//this code needs work			
				$inputname = $_FILES['inputdata']['name'];
				$tmp_inputname =  $_FILES['inputdata']['tmp_name'];
				$location = "uploads/"; // This is the local file directory name where the files get saved
			}
			else{$_SESSION['error']='input data file not loaded';
				header( 'Location: index.php' ) ;
				return;	
			}
		}
		else {$_SESSION['error']='input file not loaded';
			header( 'Location: index.php' ) ;
			return;	
		} 	
		
// get the new school_id if it has been updated
		
		
			$stmt = $pdo->prepare("SELECT * FROM school where s_name = :xyz");
			$stmt->execute(array(":xyz" => $_POST['s_name']));
			$row = $stmt->fetch(PDO::FETCH_ASSOC);
			$school_id=$row['school_id'];
	
		
	//Print_r ($docxname);
		//Print_r ($school_id);
		//Print_r ($_POST['problem_id']);
		//die ();
	// insert into problems with temporary file names for the docx, input data and pdf file
	$sql = "UPDATE Problem SET name = :name, email= :email, title = :title, docxfilenm = :docxfilenm, infilenm = :infilenm, pdffilenm=:pdffilenm, school_id = :school_id	
	WHERE problem_id=:problem_id";
			$stmt = $pdo->prepare($sql);
			$stmt->execute(array(
				':name' => $_POST['name'],
				':email' => $_POST['email'],
				':title' => $_POST['title'],
				':docxfilenm'=> $docxname,
				':infilenm'=> $inputname,
				':pdffilenm'=> $pdfname,
				':school_id'=> $school_id,
				':problem_id' => $_POST['problem_id']));
				
					
			// now replace the file name with the actual file name with the location build in
			// first get the new name complete with pathname.  this will be if the form P##_
			
			$problem_id=$_POST['problem_id'];
			if (fnmatch("P*_d_*",$docxname,FNM_CASEFOLD ) ){ // ignore the case when matching
				$newDocxNm = $docxname;
			}
			else {
				$newDocxNm = "P".$problem_id."_d_".$docxname;
			}
			if (fnmatch("P*_i_*",$inputname,FNM_CASEFOLD ) ){
				$newInputNm = $inputname;
			}
			else {
				$newInputNm = "P".$problem_id."_i_".$inputname;
			}
			if (fnmatch("P*_p_*",$pdfname,FNM_CASEFOLD ) ){
				$newPdfNm = $pdfname;
			}
			else {
				$newPdfNm = "P".$problem_id."_p_".$pdfname;
			}
			
	
	// these will need if statements to see if this has been loaded
	
			$sql = "UPDATE problem SET docxfilenm = :newDocxNm WHERE problem_id = :pblm_num";
			$stmt = $pdo->prepare($sql);
			$stmt->execute(array(
				':newDocxNm' => $newDocxNm,
				':pblm_num' => $_POST['problem_id']));
				
			$sql = "UPDATE problem SET infilenm = :newInputNm WHERE problem_id = :pblm_num";
			$stmt = $pdo->prepare($sql);
			$stmt->execute(array(
				':newInputNm' => $newInputNm,
				':pblm_num' => $_POST['problem_id']));	
				
			$sql = "UPDATE problem SET pdffilenm = :newPdfNm WHERE problem_id = :pblm_num";
			$stmt = $pdo->prepare($sql);
			$stmt->execute(array(
				':newPdfNm' => $newPdfNm,
				':pblm_num' => $_POST['problem_id']));
	
			$_SESSION['success'] = 'Record updated';
						
			// now upload docx, input and pdf files
			$pathName = 'uploads/'.$newDocxNm;
			if (move_uploaded_file($_FILES['docxfile']['tmp_name'], $pathName)){
				$_SESSION['success'] = $_SESSION['success'].'DocxFile upload successful';
			}
			
			$pathName = 'uploads/'.$newInputNm;
			if (move_uploaded_file($_FILES['inputdata']['tmp_name'], $pathName)){
				$_SESSION['success'] = $_SESSION['success'].'Input data file upload successful';
			}
			
			$pathName = 'uploads/'.$newPdfNm;
			if (move_uploaded_file($_FILES['pdffile']['tmp_name'], $pathName)){
				
				$_SESSION['success'] = $_SESSION['success'].'PdfFile upload successful';
			}
	
		/*   	$row = 1;
		if($_FILES['Qa']['name']){
					$filename=explode(".",$_FILES['Qa']['name']);
					if($filename[1]=='csv') {  // this is the file extension where the 0 entry is the file name
		
		
						if (($handle = fopen($_FILES['Qa']['tmp_name'], "r")) !== FALSE) {
							while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
								$num = count($data);
								echo "<p> $num fields in line $row: <br /></p>\n";
								$row++;
								for ($c=0; $c < $num; $c++) {
									echo $data[$c] . "<br />\n";
								}
							}
							fclose($handle);
						} 
					}
		}		  */
			if($_FILES['Qa']['name']){
					$filename=explode(".",$_FILES['Qa']['name']);
					if($filename[1]=='csv') {  // this is the file extension where the 0 entry is the file name
						$handle = fopen($_FILES['Qa']['tmp_name'], "r");
						$lines=0;  //set this to ignore the header row in the csv file
		
						While($data=fgetcsv($handle)) {
							 If ($lines==0){
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
								//$sql = "INSERT INTO Qa (problem_id, dex, ans_a,ans_b,ans_c,ans_d,ans_e,ans_f,ans_g,ans_h,ans_i,ans_j,g1,g2,g3)	
								//VALUES (:problem_id, :dex, :ans_a,:ans_b,:ans_c,:ans_d,:ans_e,:ans_f,:ans_g,:ans_h,:ans_i,:ans_j,:g1,:g2,:g3)";
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
				}else {$_SESSION['error']=' Ans file not loaded';}
				
			// this should conserve the data already input and 
	//die();
	$sql = "SELECT * FROM Problem JOIN School JOIN Qa ON (Problem.school_id=School.school_id AND Qa.problem_id=Problem.problem_id AND Qa.dex=1 )";
	$stmt = $pdo->prepare($sql);
	$stmt->execute(array(
	));
	$row = $stmt->fetch(PDO::FETCH_ASSOC);
	$status = $row['problem.status'];
	IF (($row['problem.docxfilenm']!=="NULL") AND ($row['problem.infilenm']!=="NULL") AND ($row['qa.dex']!=="NULL")){
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
	
    header( 'Location: index.php' ) ;
    return;
}

// Guardian: Make sure that problem_id is present
if ( ! isset($_GET['problem_id']) ) {
  $_SESSION['error'] = "Missing problem_id";
  header('Location: index.php');
  return;
}


$stmt = $pdo->prepare("SELECT * FROM problem where problem_id = :xyz");
$stmt->execute(array(":xyz" => $_GET['problem_id']));
$row = $stmt->fetch(PDO::FETCH_ASSOC);
if ( $row === false ) {
    $_SESSION['error'] = 'Bad value for problem_id';
    header( 'Location: index.php' ) ;
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
<p><font color="black">Input File: </font><input type='file' accept='.csv' name='inputdata'/></p>
<p>Problem statement file: <input type='file' accept='.docx' name='docxfile'/></p>
<p>Base-case  file: <input type='file' accept='.pdf' name='pdffile'/></p>

<input type="hidden" name="problem_id" value="<?= $problem_id ?>">
<p><input type="submit" value="Update"/>
<a href="index.php">Cancel</a></p>
</form>
