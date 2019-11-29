<?php

function startHeader()
{
	echo '<!DOCTYPE HTML>
		  <html>
			<head>';
}

function displayHeader($title)
{
	echo '	  <title>' . $title . '</title>
			  <link href="css/master.css" rel="stylesheet" type="text/css" />
			  <script src="js/jquery.min.js"></script>
			  <script src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.9.2/jquery-ui.min.js"></script>
			  <script type="text/javascript">
				<!-- Recolor Logo -->
				$(document).ready(function(){
				  $("#banner").click(function() {
					  $(this).animate({ 
						  "color": "rgb("+ (Math.floor(Math.random() * 256)) +","+ 
										   (Math.floor(Math.random() * 256)) +","+ 
										   (Math.floor(Math.random() * 256)) +")"
					  }, 500);
				  });
				});
			  </script>';
}

function endHeader()
{
	echo '  </head>';
}

function displayContentStart()
{
	echo '	<body>
			  <div id="banner">Image Editor</div>
			  <div id="navbar">
				<a href="filechange.php">File Change</a>
				<a href="resize.php">Resize</a>
				<a href="crop.php">Crop</a>
				<a href="watermark.php">Watermark</a>
			  </div>
			  <div id="content">';
}

function displayContentEnd()
{
	echo '	  </div>
			</body>
		  </html>';
}

function saveImage($im, $newtype, $savelocation)
{
	if ($newtype==0)
	{
		imagegif($im, $savelocation);
	}
	else if ($newtype==1)
	{
		imagejpeg($im, $savelocation, 100);
	}
	else if ($newtype==2)
	{
		imagejpeg($im, $savelocation, 100);
	}
	else if ($newtype==3)
	{
		imagepng($im, $savelocation, 0);
	}
	else
	{
		imagepng($im);
	}
	
	imagedestroy($im);
}

function getBaseFileName($filename)
{
	$ext = pathinfo($filename, PATHINFO_EXTENSION);
    $basename = preg_replace('/\.' . preg_quote($ext, '/') . '$/', '', $filename);
	
	return ($basename);
}

?>