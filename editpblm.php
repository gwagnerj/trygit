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
			$student_id=$row['school_id'];
	
	
	//Print_r ($docxname);
		//Print_r ($school_id);
		//Print_r ($_POST['problem_id']);
	//	die ();
	// insert into problems with temporary file names for the docx and input data
	$sql = "UPDATE problem SET name = :name, email= :email, title = :title, docxfilenm = :docxfilenm, infilenm = :infilenm, school_id = :school_id	
	WHERE problem_id=:problem_id";
			$stmt = $pdo->prepare($sql);
			$stmt->execute(array(
				':name' => $_POST['name'],
				':email' => $_POST['email'],
				':title' => $_POST['title'],
				':docxfilenm'=> $docxname,
				':infilenm'=> $inputname,
				':school_id'=> $school_id,
				':problem_id' => $_POST['problem_id']));
					
			// now replace the file name with the actual file name with the location build in
			// first get the new name complete with pathname	
				$problem_id=$_POST['problem_id'];
			$newDocxNm = "P".$problem_id."_d_".$docxname;
			$newInputNm= "P".$problem_id."_i_".$inputname;			
	
	
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
	
			$_SESSION['success'] = 'Record updated';
						
			// now upload docx and input files
			$pathName = 'uploads/'.$newDocxNm;
			
			
			if (move_uploaded_file($_FILES['docxfile']['tmp_name'], $pathName)){
				
				$_SESSION['success'] = $_SESSION['success'].'DocxFile upload successful';
			}
			
			$pathName = 'uploads/'.$newInputNm;
			if (move_uploaded_file($_FILES['inputdata']['tmp_name'], $pathName)){
				
				$_SESSION['success'] = $_SESSION['success'].'Input data file upload successful';
			}
	
	
	
			if($_FILES['Qa']['name']){
					$filename=explode(".",$_FILES['Qa']['name']);
					if($filename[1]=='csv') {  // this is the file extension where the 0 entry is the file name
						$handle = fopen($_FILES['Qa']['tmp_name'], "r");
						$lines=0;  //set this to ignore the header row in the csv file
						
										
						While($data=fgetcsv($handle)) {
							If ($lines>0){
								// put the data into the 
								$sql = "INSERT INTO Qa (problem_ID, dex, ans_a,ans_b,ans_c,ans_d,ans_e,ans_f,ans_g,ans_h,ans_i,ans_j)	
								VALUES (:problem_ID, :dex, :ans_a,:ans_b,:ans_c,:ans_d,:ans_e,:ans_f,:ans_g,:ans_h,:ans_i,:ans_j)";
								$stmt = $pdo->prepare($sql);
								$stmt->execute(array(
									':problem_ID' => $_POST['problem_id'],
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
									':ans_j' => $data[10]));
				
							}
							$lines = $lines+1;
						}
					}else {$_SESSION['error']=' Answer file is not a csv file';}
				}else {$_SESSION['error']=' Ans file not loaded';}
				
			// this should conserve the data already input and 
	
	
	
	
	
	
	
    $sql = "UPDATE problem SET name = :name,
            email = :email, title = :title,school_id=:school_id
            WHERE problem_id = :problem_id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(array(
        ':name' => $_POST['name'],
        ':email' => $_POST['email'],
        ':title' => $_POST['title'],
		':school_id' => $row['school_id'],
        ':problem_id' => $_POST['problem_id']));
    $_SESSION['success'] = 'Record updated';
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
<p>Answers a CSV File: <input type='file' name='Qa'/></p>
<p><strong><font color="red">Input values a CSV File: </font></strong><input type='file' name='inputdata'/></p>
<p>Problem statement a Docx file: <input type='file' name='docxfile'/></p>

<input type="hidden" name="problem_id" value="<?= $problem_id ?>">
<p><input type="submit" value="Update"/>
<a href="index.php">Cancel</a></p>
</form>
