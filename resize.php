<?php

include('includes/constants.php');
include('includes/functions.php');

$downloadlink = '';

startHeader();
displayHeader('Image Editor - Resizer');
endHeader();

displayContentStart();

// If Uploading
if (isset($_REQUEST['do']))
{
	$do = $_REQUEST['do'];
	
	if (($do=='upload')&&(isset($_REQUEST['submit'])))
	{
		// No Image Errors
		if ($_FILES["image"]["error"]==0)
		{
			// Get File Info
			$filename = $_FILES["image"]["name"];
			$filetype = $_FILES["image"]["type"];
			$filesize = $_FILES["image"]["size"];
			$file_tmpname = $_FILES["image"]["tmp_name"];
			$basename = getBaseFileName($filename);
			
			// Save Image To Server
			move_uploaded_file($file_tmpname, $currentdir . 'uploadedimages/' . $_FILES["image"]["name"]);
			
			// Get Image Type ID
			$file_extID = array_search($filetype, $allowedIMGtype);
			
			// Get Image Type Extension
			$fileEXT = $allowedEXTs[$file_extID];
			
			// Set New File Location/Name
			$newFileLocation = "$currentdir" . "uploadedimages/$basename" . "_resize.$fileEXT";
			
			// Get Current Image Type
			if ($file_extID==0)
			{
				$im = imagecreatefromgif($currentdir . 'uploadedimages/' . $_FILES["image"]["name"]);
			}
			else if (($file_extID==1)||($file_extID==2))
			{
				$im = imagecreatefromjpeg($currentdir . 'uploadedimages/' . $_FILES["image"]["name"]);
			}
			else if ($file_extID==3)
			{
				$im = imagecreatefrompng($currentdir . 'uploadedimages/' . $_FILES["image"]["name"]);
			}
			
			imagealphablending($im, true);
			imagesavealpha($im, true);
			
			$resizemethod = $_REQUEST['resizemethod'];
			list($width, $height) = getimagesize($currentdir . 'uploadedimages/' . $_FILES["image"]["name"]);
			
			// Resize By Pixel
			if ($resizemethod==0)
			{
				$newheight = $_REQUEST['newheight'];
				$newwidth = $_REQUEST['newwidth'];
			}
			// Resize By Percent
			else
			{
				$percentchange = $_REQUEST['percentchange']*.01;
				$newwidth = $width * $percentchange;
				$newheight = $height * $percentchange;
			}
			
			$image_new = imagecreatetruecolor($newwidth, $newheight);
			
			imagecopyresampled($image_new, $im, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);
			
			saveImage($image_new, $file_extID, $newFileLocation);
			
			imagedestroy($im);
			
			$downloadlink = "<strong>Download Link:</strong> <a href=\"uploadedimages/$basename" . "_resize.$fileEXT\" download=\"$basename" . "_resize.$fileEXT\">$basename" . "_resize.$fileEXT</a><br />";
		}
		else
		{
			echo '<p class="badwarning">There is an error with the image.</p>';
		}
	}
}

echo '<form action="?do=upload" method="POST" enctype="multipart/form-data" style="width:475px; float:right;">
		<div class="centered">
		  <div id="formheader">
			Resize Method
		  </div>
		  <div id="formimginput">
			Image: <input type="file" name="image" id="image" value="" accept="image/*" /><br />
		  </div>
		  <div id="resizemethods">
			<div class="resizeoptions">
			  <input type="radio" name="resizemethod" id="pixelchange" value="0" checked="checked" /><label for="pixelchange">By Pixel</label><br />
			  New Height: <input type="text" name="newheight" value="" size="10" /> px<br />
			  New Width: <input type="text" name="newwidth" value="" size="10" /> px<br />
			</div>
			<div class="resizeoptions">
			  <input type="radio" name="resizemethod" id="percentchange" value="1" /><label for="percentchange">By Percentage</label><br />
			  Percentage: <input type="text" name="percentchange" value="100" size="10" /> %<br />
			</div>
		  </div>
		  <div id="resizebtn">
			<input type="submit" name="submit" value="Resize" class="centered" />
		  </div>
		</div>
	  </form>
	  <p>
		Remember, this is a web site, not Photoshop. While it will resize the images to the correct size you want, quality may drastically decrease the further you get from the original size. Always keep backups of your original images!
	  </p>
	  <p>
		To use the resizer tool, upload your image to the right. You have two options when resizing your image.
	  </p>
	  <p>
		Resize by pixel allows you to enter custom numbers and will not maintain the aspect ratio.
	  </p>
	  <p>
		Resize by percentage will increase/decrease the image size based on the percentage and will maintain the aspect ratio of the image.
	  </p>
	  ' . $downloadlink . '
	  <br />';

displayContentEnd();

?>