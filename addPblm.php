<?php
require_once "pdo.php";
session_start();

if ( isset($_POST['submit']))  {
		// add the CSV data and word filepath to the database
	if(isset($_FILES['Qa']) and $_FILES['inputdata'] and $_FILES['docxfile'] and $_POST['name'] and $_POST['email'] and $_POST['title'] and $_POST['s_name']){

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
		
						// now put the rest of the data in the main problem table and store the school ID there but with a temporary file name and file location
						// need to get the school_id either from the post data or from a query
						
						$sql = " SELECT school_id FROM School where s_name = :s_name";
						$stmt = $pdo->prepare($sql);
						$stmt->execute(array(
						':s_name' => $_POST['s_name']));
						$row = $stmt->fetch(PDO::FETCH_ASSOC);
						$school_id=$row['school_id'];
						
												
						
						
						$sql = "INSERT INTO problem (name, email, title, docxfilenm, infilenm, school_id)	VALUES (:name, :email, :title, :docxfilenm, :infilenm, :school_id)";
						$stmt = $pdo->prepare($sql);
						$stmt->execute(array(
							':name' => $_POST['name'],
							':email' => $_POST['email'],
							':title' => $_POST['title'],
							':docxfilenm'=> $docxname,
							':infilenm'=> $inputname,
							':school_id'=> $school_id));
					
						$pblm_num=$pdo->lastInsertId();
						
						// now replace the file name with the actual file name with the location build in
						// first get the new name complete with pathname		
						$newDocxNm = "P".$pblm_num."_d_".$docxname;
						$newInputNm= "P".$pblm_num."_i_".$inputname;
						
						$sql = "UPDATE problem SET docxfilenm = :newDocxNm WHERE problem_id = :pblm_num";
						$stmt = $pdo->prepare($sql);
						$stmt->execute(array(
							':newDocxNm' => $newDocxNm,
							':pblm_num' => $pblm_num));
							
						$sql = "UPDATE problem SET infilenm = :newInputNm WHERE problem_id = :pblm_num";
						$stmt = $pdo->prepare($sql);
						$stmt->execute(array(
							':newInputNm' => $newInputNm,
							':pblm_num' => $pblm_num));	
						
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
		
		// now put the data from the answer files in the Qa table
		
		if($_FILES['Qa']['name']){
			$filename=explode(".",$_FILES['Qa']['name']);
			if($filename[1]=='csv') {  // this is the file extension where the 0 entry is the file name
				$handle = fopen($_FILES['Qa']['tmp_name'], "r");
				$lines=0;  //set this to ignore the header row in the csv file
				
				$data[9]='Null';
				$data[10]='Null';
				
				While($data=fgetcsv($handle)) {
					If ($lines>0){
						// put the data into the 
						$sql = "INSERT INTO Qa (problem_ID, dex, ans_a,ans_b,ans_c,ans_d,ans_e,ans_f,ans_g,ans_h,ans_i,ans_j)	
						VALUES (:problem_ID, :dex, :ans_a,:ans_b,:ans_c,:ans_d,:ans_e,:ans_f,:ans_g,:ans_h,:ans_i,:ans_j)";
						$stmt = $pdo->prepare($sql);
						$stmt->execute(array(
							':problem_ID' => $pblm_num,
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

	}	
	else {$_SESSION['error']=' Error - all files must be loaded';
	// this should conserve the data already input and 
	}
	header( 'Location: index.php' ) ;
	return;	
		
}	

// Flash pattern
if ( isset($_SESSION['error']) ) {
    echo '<p style="color:red">'.$_SESSION['error']."</p>\n";
    unset($_SESSION['error']);
}

// Get the school names from the database so we can use them in drop down selection box
$sql="SELECT DISTINCT s_name from School ORDER BY s_name";
$stmt = $pdo->query($sql);
// I'm pretty sure this is not the best way but I/m just going to read it into an array variable 
$i=0;
while ( $row = $stmt->fetch(PDO::FETCH_ASSOC) ) {
	$s_name[$i]=htmlentities($row['s_name']);
	$i=$i+1;
}

?>


<p>Add A New User</p>
<form action="" method="post" enctype="multipart/form-data">
	<p> Contributor Name: <input type = 'text' name = 'name'></p>
	<p> Contributor email: <input type = 'text' name = 'email'></p>
	<p> Problem title: <input type = 'text' name = 'title'></p>
	
	<label> School:
		<select required name = "s_name">
			<option> --Select the school (Required)--</option>
			<?php foreach ($s_name as $values){?>
			<option><?php echo $values;?></option>
			<?php }?>
		</select>
	</label> 
<p>Answers a CSV File: <input type='file' name='Qa'/></p>
<p><strong><font color="red">Input values a CSV File: </font></strong><input type='file' name='inputdata'/></p>
<p>Problem statement a Docx file: <input type='file' name='docxfile'/></p>
<p><input type = 'submit' name="submit" value = 'Submit'> 
</form>


<a href="index.php">Cancel</a></p>

