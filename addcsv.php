<?php
require_once "pdo.php";
session_start();

if ( isset($_POST['submit']))  {
		// add the CSV data and word filepath to the database
	if(isset($_FILES['file']) and $_FILES['docxfile']){
		//Get the filename from the docxfile that was uploaded
		if($_FILES['docxfile']['name']) {
			$filename=explode(".",$_FILES['docxfile']['name']); // divides the file into its name and extension puts it into an array
			if ($filename[1]=='docx'){ // this is the extension
				$docxfile=addslashes($_FILES['docxfile']['tmp_name']);
				$name=addslashes($_FILES['docxfile']['name']);
				$docxfile=file_get_contents($docxfile);
				$uploadFlag=0;
//this code needs work			
				$name = $_FILES['docxfile']['name'];
				$tmp_name =  $_FILES['docxfile']['tmp_name'];
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
		if($_FILES['file']['name']){
			$filename=explode(".",$_FILES['file']['name']);
			if($filename[1]=='csv') {  // this is the file extension where the 0 entry is the file name
				$handle = fopen($_FILES['file']['tmp_name'], "r");
				$lines=0;  //set this to ignore the header row in the csv file
				While($data=fgetcsv($handle)) {
					If ($lines==1){
						// put the school name in the school data table and get the id for that entry
						
					   $sql = "INSERT INTO school (s_name)	VALUES (:s_name)";
						$stmt = $pdo->prepare($sql);
						$stmt->execute(array(
							':s_name' => $data[3]));
						
						$school_id=$pdo->lastInsertId();
						
						// now put the rest of the data in the main users2 table and store the school ID there but with a temporary file name and file location
						
						$sql = "INSERT INTO users2 (name, email, password, docxfilenm, school_id)	VALUES (:name, :email, :password, :new_name, :school_id)";
						$stmt = $pdo->prepare($sql);
						$stmt->execute(array(
							':name' => $data[0],
							':email' => $data[1],
							':password' => $data[2],
							':new_name'=> $name,
							':school_id'=> $school_id));
					
						$pblm_num=$pdo->lastInsertId();
						
						// now replace the file name with the actual file name with the location build in
						// first get the new name complete with pathname		
						$newDocxNm = "P".$pblm_num."_d_".$name;
						
						$sql = "UPDATE users2 SET docxfilenm = :newDocxNm WHERE users2_id = :pblm_num";
						$stmt = $pdo->prepare($sql);
						$stmt->execute(array(
							':newDocxNm' => $newDocxNm,
							':pblm_num' => $pblm_num));
						
						$_SESSION['success'] = 'Record updated';
					}
					$lines=$lines+1;
				}
				$_SESSION['success'] = 'Data entry successful';
				$pathName = 'uploads/'.$newDocxNm;
				if (move_uploaded_file($tmp_name, $pathName)){
					$uploadFlag=1;
					$_SESSION['success'] = 'File upload successful';
				}
			} 
			else {$_SESSION['error']='csv has wrong extension';
				header( 'Location: index.php' ) ;
				return;	
			}	
		} 
		else {$_SESSION['error']='CSV file not successfully loaded';
			header( 'Location: index.php' ) ;
			return;	
		}
	}	
	else {$_SESSION['error']=' Error - both files must be loaded';
	
	}
	header( 'Location: index.php' ) ;
	return;	
		
}	

// Flash pattern
if ( isset($_SESSION['error']) ) {
    echo '<p style="color:red">'.$_SESSION['error']."</p>\n";
    unset($_SESSION['error']);
}
?>
<p>Add A New User</p>
<form action="" method="post" enctype="multipart/form-data">
<p>Meta/Ans CSV File to DB: <input type='file' name='file'/></p>
<p><strong><font color="red">Input CSV File to folder: </font></strong><input type='file' name='inputdata'/></p>
<p>Docx File to folder: <input type='file' name='docxfile'/></p>
<p><input type = 'submit' name="submit" value = 'Upload'> 
</form>




<a href="index.php">Cancel</a></p>

