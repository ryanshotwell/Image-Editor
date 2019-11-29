<?php

include('includes/constants.php');
include('includes/functions.php');

$message = '';

startHeader();
displayHeader('Image Editor - Watermark');
endHeader();

displayContentStart();

if (isset($_REQUEST['do']))
{
	$do = $_REQUEST['do'];
	
	// If Uploading
	if ($do=='upload')
	{
		// No Image Errors
		if ($_FILES["baseimage"]["error"]==0)
		{
			// Get Base Image Info
			$baseimagename = $_FILES["baseimage"]["name"];
			$baseimagetype = $_FILES["baseimage"]["type"];
			$baseimage_tmpname = $_FILES["baseimage"]["tmp_name"];
			$baseimagename_basename = getBaseFileName($baseimagename);
			
			// Get Watermark Image Info
			$watermarkname = $_FILES["watermark"]["name"];
			$watermarktype = $_FILES["watermark"]["type"];
			$watermark_tmpname = $_FILES["watermark"]["tmp_name"];
			
			// If Current File Type is Allowed
			if (in_array($baseimagetype, $allowedIMGtype))
			{
				// If Current File Type is Allowed
				if (in_array($watermarktype, $allowedIMGtype))
				{
					// Save Images To Server
					move_uploaded_file($baseimage_tmpname, $currentdir . 'uploadedimages/' . $_FILES["baseimage"]["name"]);
					move_uploaded_file($watermark_tmpname, $currentdir . 'uploadedimages/' . $_FILES["watermark"]["name"]);
					
					// Get New Image Type ID
					$baseimage_extID = array_search($baseimagetype, $allowedIMGtype);
					$watermark_extID = array_search($watermarktype, $allowedIMGtype);
					
					// Get Image Type Extension
					$baseimage_fileEXT = $allowedEXTs[$baseimage_extID];
					
					// Import Base Image into GD
					if ($baseimage_extID==0)
					{
						$baseimage = imagecreatefromgif($currentdir . 'uploadedimages/' . $baseimagename);
					}
					else if (($baseimage_extID==1)||($baseimage_extID==2))
					{
						$baseimage = imagecreatefromjpeg($currentdir . 'uploadedimages/' . $baseimagename);
					}
					else if ($baseimage_extID==3)
					{
						$baseimage = imagecreatefrompng($currentdir . 'uploadedimages/' . $baseimagename);
					}
					
					// Import Watermark into GD
					if ($watermark_extID==0)
					{
						$watermark = imagecreatefromgif($currentdir . 'uploadedimages/' . $watermarkname);
					}
					else if (($watermark_extID==1)||($watermark_extID==2))
					{
						$watermark = imagecreatefromjpeg($currentdir . 'uploadedimages/' . $watermarkname);
					}
					else if ($watermark_extID==3)
					{
						$watermark = imagecreatefrompng($currentdir . 'uploadedimages/' . $watermarkname);
					}
					
					$margin_right = $marge_bottom = 10;
					$baseimagex = imagesx($baseimage);
					$baseimagey = imagesy($baseimage);
					$watermarkx = imagesx($watermark);
					$watermarky = imagesy($watermark);
					
					$dest_x = $baseimagex - $watermarkx - $margin_right;
					$dest_y = $baseimagey - $watermarky - $marge_bottom;
					
					imagecopy($baseimage, $watermark, $dest_x, $dest_y, 0, 0, $watermarkx, $watermarky);
					saveImage($baseimage, $baseimage_extID, $currentdir . "uploadedimages/$baseimagename_basename" . "_watermark.$baseimage_fileEXT");
					
					imagedestroy($watermark);
					
					$message = "<strong>Download Link:</strong> <a href=\"uploadedimages/$baseimagename_basename" . "_watermark.$baseimage_fileEXT\" download=\"$baseimagename_basename" . "_watermark.$baseimage_fileEXT\">$baseimagename_basename" . "_watermark.$baseimage_fileEXT</a><br />";
				}
				else
				{
					$message = '<p class="badwarning">Sorry, we do not accept this file type for your watermark image.</p>';
				}
			}
			else
			{
				$message = '<p class="badwarning">Sorry, we do not accept this file type for your base image.</p>';
			}
		}
		else
		{
			$message = '<p class="badwarning">There is an error with the image.</p>';
		}
	}
}
else
{
	$message = '';
}

echo '<form action="?do=upload" method="POST" enctype="multipart/form-data" style="width:475px; float:right;">
		<div class="centered">
		  <div id="formheader">
			Insert Your Watermark
		  </div>
		  <div id="formimginput">
			Base Image: <input type="file" name="baseimage" id="baseimage" value="" accept="image/*" />
		  </div>
		  <div id="formimginput">
			Watermark: <input type="file" name="watermark" id="watermark" value="" accept="image/*" /><br />
		  </div>
		  <div id="resizebtn">
			<input type="submit" name="submit" value="Upload" />
		  </div>
		</div>
	  </form>
	  <p>
		Watermarks are placed on images to show who may have created or taken the image and show ownership.
	  </p>
	  <p>
		The base image is the image that you want to have the watermark placed on. It should be bigger than the watermark image. For the watermark, transparent images should work as intented and preserve the transparency.
	  </p>
	  ' . $message;

displayContentEnd();

?>