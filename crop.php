<?php

include('includes/constants.php');
include('includes/functions.php');

$message = '';

startHeader();
displayHeader('Image Editor - Crop');
echo '<!-- Jcrop gives a GUI for users to pick what you want to crop out -->
	  <!-- Jcrop START -->
	  <script src="js/jquery.Jcrop.min.js"></script>
	  <script type="text/javascript">
		jQuery(function($){
	  
		  var jcrop_api;
	  
		  $("#target").Jcrop({
			onChange:   showCoords,
			onSelect:   showCoords,
			onRelease:  clearCoords
		  },function(){
			jcrop_api = this;
		  });
	  
		  $("#coords").on("change","input",function(e){
			var x1 = $("#x1").val(),
				x2 = $("#x2").val(),
				y1 = $("#y1").val(),
				y2 = $("#y2").val();
			jcrop_api.setSelect([x1,y1,x2,y2]);
		  });
	  
		});
	  
		// Simple event handler, called from onChange and onSelect
		// event handlers, as per the Jcrop invocation above
		function showCoords(c)
		{
		  $("#x1").val(c.x);
		  $("#y1").val(c.y);
		  $("#x2").val(c.x2);
		  $("#y2").val(c.y2);
		  $("#w").val(c.w);
		  $("#h").val(c.h);
		};
	  
		function clearCoords()
		{
		  $(".coordsinput").val("");
		};
	  </script>
	  <link rel="stylesheet" href="css/jquery.Jcrop.min.css" type="text/css" />
	  <!-- Jcrop END -->';
endHeader();

displayContentStart();

if (isset($_REQUEST['do']))
{
	$do = $_REQUEST['do'];
	
	// If Uploading
	if ($do=='upload')
	{
		// No Image Errors
		if ($_FILES["image"]["error"]==0)
		{
			// Get File Info
			$filename = $_FILES["image"]["name"];
			$filetype = $_FILES["image"]["type"];
			$file_tmpname = $_FILES["image"]["tmp_name"];
			
			// If Current File Type is Allowed
			if (in_array($filetype, $allowedIMGtype))
			{
				// Save Image To Server
				move_uploaded_file($file_tmpname, $currentdir . 'uploadedimages/' . $_FILES["image"]["name"]);
				
				$imagelocation = 'uploadedimages/' . $_FILES["image"]["name"];
				$displayCropForm = TRUE;
				$displayUploadForm = FALSE;
			}
			else
			{
				$message = '<p class="badwarning">Sorry, we do not accept this file type.</p>';
				$displayCropForm = FALSE;
				$displayUploadForm = TRUE;
			}
		}
		else
		{
			$message = '<p class="badwarning">There is an error with the image.</p>';
			$displayCropForm = FALSE;
			$displayUploadForm = TRUE;
		}
	}
	// If Cropping
	else if ($do=='crop')
	{
		$x1 = $_REQUEST['x1'];
		$x2 = $_REQUEST['x2'];
		$y1 = $_REQUEST['y1'];
		$y2 = $_REQUEST['y2'];
		$croppedheight = $_REQUEST['h'];
		$croppedwidth = $_REQUEST['w'];
		$imgname = $_REQUEST['imgname'];
		
		if (file_exists("uploadedimages/$imgname"))
		{
			$extension = pathinfo($currentdir . 'uploadedimages/' . $imgname, PATHINFO_EXTENSION);
			
			// Get New Image Type ID
			$file_extID = array_search($extension, $allowedEXTs);
			
			// Get Current Image Type
			if ($file_extID==0)
			{
				$src = imagecreatefromgif($currentdir . 'uploadedimages/' . $imgname);
			}
			else if (($file_extID==1)||($file_extID==2))
			{
				$src = imagecreatefromjpeg($currentdir . 'uploadedimages/' . $imgname);
			}
			else if ($file_extID==3)
			{
				$src = imagecreatefrompng($currentdir . 'uploadedimages/' . $imgname);
			}
			$dest = imagecreatetruecolor($croppedwidth, $croppedheight);
			
			imagecopy($dest, $src, 0, 0, $x1, $y1, $croppedwidth, $croppedheight);
			saveImage($dest, $file_extID, $currentdir . 'uploadedimages/' . $imgname);
			
			imagedestroy($src);
			
			$downloadlink = "<a href=\"uploadedimages/$imgname\" download=\"$imgname\">$imgname</a>";
			
			$displayCropForm = FALSE;
			$displayUploadForm = FALSE;
		}
		else
		{
			$message = '<p class="badwarning">Unable to find your image. Please <a href="crop.php">reupload</a> it.</p>';
			$displayCropForm = FALSE;
			$displayUploadForm = TRUE;
		}
	}
	else
	{
		$displayCropForm = FALSE;
		$displayUploadForm = TRUE;
	}
}
else
{
	$displayCropForm = FALSE;
	$displayUploadForm = TRUE;
}

if ($displayCropForm==TRUE)
{
	echo '<div id="formheader">
			Crop Image
		  </div>
		  <img src="' . $imagelocation . '" alt="User Image" id="target" class="centered" />
		  <form action="?do=crop" method="POST" id="coords" class="coords">
			<input type="hidden" name="imgname" value="' . $_FILES["image"]["name"] . '" />
			<div style="float:right;">
			  <input type="submit" name="submit" value="Crop Image" />
			</div>
			<div id="crop-inline-labels">
			  <label>X1 <input type="text" size="4" id="x1" name="x1" class="coordsinput" value="" /></label>
			  <label>Y1 <input type="text" size="4" id="y1" name="y1" class="coordsinput" value="" /></label>
			  <label>X2 <input type="text" size="4" id="x2" name="x2" class="coordsinput" value="" /></label>
			  <label>Y2 <input type="text" size="4" id="y2" name="y2" class="coordsinput" value="" /></label>
			  <label>W <input type="text" size="4" id="w" name="w" class="coordsinput" value="" /></label>
			  <label>H <input type="text" size="4" id="h" name="h" class="coordsinput" value="" /></label>
			</div>
		  </form>';
}
else if (isset($downloadlink))
{
	echo "<p>
			Your image has successfully been cropped! You can download it to your computer below.
		  </p>
		  <p>
			<strong>Download Link:</strong> $downloadlink
		  </p>
		  <p>
			<img src=\"uploadedimages/$imgname\" alt=\"$imgname\" />
		  </p>";
}
else
{
	echo '<form action="?do=upload" method="POST" enctype="multipart/form-data" style="float:right;">
			<div id="formheader">
			  Crop Image
			</div>
			<div id="formimginput">
			  Image: <input type="file" name="image" id="image" value="" accept="image/*" /><br />
			  <input type="submit" name="submit" value="Upload" />
			</div>
		  </form>
		  <p>
			Cropping an image allows you to decrease its size by cutting out parts of the image. Please read the following in its entirety before continuing.
		  </p>
		  <p>
			This script takes place in two steps.<br />
			The first step requires you to upload the image from your computer to the site so it has the information needed for you to crop it.<br />
			<br />
			Then, with help from <a href="http://deepliquid.com/content/Jcrop.html" target="_blank">Jcrop</a>, you can visually select the area of the image that you want to crop out. For your convenience, you can see what the new height and width of the image will be. When you are ready to crop, just click the crop button and the server does the rest.
		  </p>
		  ' . $message;
}

displayContentEnd();

?>