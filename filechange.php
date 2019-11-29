<?php

include('includes/constants.php');
include('includes/functions.php');

$message = '';

startHeader();
displayHeader('Image Editor - Image Type Changer');
endHeader();

displayContentStart();

if (isset($_REQUEST['do']))
{
	$do = $_REQUEST['do'];
	
	// If Uploading
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
			
			// Get New Image Type
			$newtype = $_REQUEST['newtype'];
			
			// If Current File Type is Allowed
			if (in_array($filetype, $allowedIMGtype))
			{
				// Save Image To Server
				move_uploaded_file($file_tmpname, $currentdir . 'uploadedimages/' . $_FILES["image"]["name"]);
				
				// Get New Image Type ID
				$file_extID = array_search($filetype, $allowedIMGtype);
				
				// Get New Image Type Extension
				$newFileEXT = $allowedEXTs[$newtype];
				
				// Set New File Location/Name
				$newFileLocation = "$currentdir" . "uploadedimages/$basename.$newFileEXT";
				
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
				
				saveImage($im, $newtype, $newFileLocation);
				
				$message = "<p><strong>Download Link:</strong> <a href=\"uploadedimages/$basename.$newFileEXT\" download=\"$basename.$newFileEXT\">$basename.$newFileEXT</a></p>";
			}
		}
		else
		{
			$message = '<p class="badwarning">There is an error with the image.</p>';
		}
	}
}

echo '<form action="?do=upload" method="POST" enctype="multipart/form-data" style="float:right;">
		<div id="formheader">
		  File Change
		</div>
		<div id="formimginput" style="width:375px">
		  Image: <input type="file" name="image" id="image" value="" accept="image/*" /><br />
		</div>
		<div id="formimginput" style="width:375px">
		  Image Type: <select name="newtype" id="newtype">
						<option value="0">GIF</option>
						<option value="1">JPG</option>
						<option value="2">JPEG</option>
						<option value="3">PNG</option>
					  </select>
		  <input type="submit" name="submit" value="Change!" style="margin-left:6px;" />
		</div>
	  </form>
	  <p>
		Image file formats are how we store visual images as digital data. They may be uncompressed, compressed, or vector formats.
	  </p>
	  ' . $message . '
	  <p>
		<strong>GIF</strong><br />
		GIF (Graphics Interchange Format) is limited to an 8-bit palette, or 256 colors. This makes the GIF format suitable for storing graphics with relatively few colors such as simple diagrams, shapes, logos and cartoon style images.
	  </p>
	  <p>
		<strong>JPG/JPEG</strong><br />
		JPEG (Joint Photographic Experts Group) is a compression method JPEG-compressed images are usually stored in the JFIF (JPEG File Interchange Format) file format. JPEG compression is (in most cases) lossy compression. The JPEG/JFIF filename extension is JPG or JPEG.
	  </p>
	  <p>
		<strong>PNG</strong><br />
		PNG (Portable Network Graphics) file format was created as the free, open-source successor to GIF. The PNG file format supports 8 bit paletted images (with optional transparency for all palette colors) and 24 bit truecolor (16 million colors) or 48 bit truecolor with and without alpha channel - while GIF supports only 256 colors and a single transparent color.
	  </p>';

displayContentEnd();

?>