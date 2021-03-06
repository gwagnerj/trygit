Sub BringEmIn()

Dim Suggest_WBname As String
Dim WordTempLoc As FileDialog
Dim FirstRow As Long
Dim DocLoc, firstOne As String
Dim Workbook_name As Variant
Dim i As Integer
Dim WordDoc, WordApp, oRng  As Object
' using late binding

 

Set WordTempLoc = Application.FileDialog(msoFileDialogFilePicker)

With WordTempLoc
    .Title = "Select Word problem statement file with ##variables##"
    .Filters.Add "Word Type Files", "*.docx,*.doc", 1
    If .Show <> -1 Then GoTo NoSelection
    Worksheets("Problem").Range("Word_File").Value = Dir(.SelectedItems(1)) 'Document Name
    Worksheets("Problem").Range("Word_Path").Value = .SelectedItems(1) 'Document Pathway
End With


' the above was modified from Randy Austin and is ExcelFreeLancersGroup




'Dim WordContent As Word.Range
With Worksheets("Problem")
  
	  If .Range("Word_Path").Value = Empty Then
		MsgBox "No Word document in Word_Path"
		.Range("Word_Path").Select
		Exit Sub
	  End If
		
		DocLoc = Range("Word_Path").Value 'Word Document Filename and Path
		
		'Open Word Template
		On Error Resume Next 'If Word is already running
		Set WordApp = GetObject(, "Word.Application")
		If Err.Number <> 0 Then
		'Launch a new instance of Word
		Err.Clear
		'On Error GoTo Error_Handler
		Set WordApp = CreateObject("Word.Application")
		WordApp.Visible = True 'Make the application visible to the user
		End If
	 
	'advance the current step
	 Worksheets("Problem").Range("Current_Step").Value = 2
	 
	Suggest_WBname = Replace(Worksheets("Problem").Range("Word_File").Value, ".docx", "")
	Workbook_name = Application.GetSaveAsFilename(InitialFileName:=Suggest_WBname, FileFilter:="Excel Macro-Enabled Workbook,*.xlsm")

		If Workbook_name <> False Then
				ActiveWorkbook.SaveAs FileName:=Workbook_name, FileFormat:=52, CreateBackup:=False
		End If
	 
	 
	 
	 Application.ScreenUpdating = False
	   
		 Set WordDoc = WordApp.Documents.Open(FileName:=DocLoc, ReadOnly:=False)  'Open Template
				 Set oRng = WordDoc.Range
			   
			   'first get rid of all of the directions to the contributors marked within +== and ==+
				 With oRng.Find
					.ClearFormatting
					.Text = "+==*==+"   '[ is a speical find character but \ will escape that
					 .Forward = True
					 .MatchWildcards = True
					.Replacement.ClearFormatting
					 .Replacement.Text = ""
					 .Execute Replace:=wdReplaceAll, Forward:=True, _
					 Wrap:=wdFindContinue
				 End With
				
				 ' now find the variables
				 i = 0
				 Do While (i < 30)

					With oRng.Find
					   .Text = "##*##"   '[ is a speical find character but \ will escape that
						.Forward = True
						.MatchWildcards = True
						If .Execute Then
						   Worksheets("Problem").Range("A" & i + 8).Value = Trim(Replace(oRng.Text, "##", ""))
					   End If
					End With
					i = i + 1
				 Loop


				 'process the duplicate varaible names and getting the optional parameters in a separate column
				 Worksheets("Problem").Range("A8:A37").RemoveDuplicates Columns:=Array(1)
				 Worksheets("Problem").Range("A8:A37").TextToColumns comma:=True, Destination:=Range("A8:C37")
				 'variable names (only get 13 non duplicate variables)
				 Worksheets("Problem").Range("A8:A20").Copy
				 Worksheets("Problem").Range("O22").PasteSpecial Transpose:=True, Paste:=xlPasteValues ' past the varaible names where you want them
				 Worksheets("Problem").Range("A8:A37").Clear
				 'variable type
				  Worksheets("Problem").Range("B8:B20").Copy
				 Worksheets("Problem").Range("O14").PasteSpecial Transpose:=True, Paste:=xlPasteValues  ' Paste the varable type where you want them
				 Worksheets("Problem").Range("B8:B37").Clear
				 'base-case values - later we will make a table and a helper vector for now just copy them in
				  Worksheets("Problem").Range("C8:E20").Copy
				 Worksheets("Problem").Range("O18").PasteSpecial Transpose:=True, Paste:=xlPasteValues   ' Paste the basecase values where you want them
				 Worksheets("Problem").Range("C1:C30").Clear
				 Application.CutCopyMode = False
				
				
			 'Paste an image of the word document into the Excel sheet
				 
				 
				
				 Set oRng = WordDoc.Range
				With WordDoc.Range
					.CopyAsPicture
				   ' .Collapse Direction:=wdCollapseEnd
				
				   ' .PasteSpecial DataType:=wdPasteMetaFilePicture
			 
				 End With
				  Range("B7").Select
				 
				ActiveSheet.PasteSpecial Format:="Picture (Enhanced Metafile)", Link:=False _
					, DisplayAsIcon:=False
				Range("A1").Select
		  
			
			WordDoc.Close
		  
	  Set oRng = Nothing
	  Set WordDoc = Nothing
	 Set WordApp = Nothing
     
 End With
   Application.ScreenUpdating = True
NoSelection:

End Sub

