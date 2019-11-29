<?php

include('includes/constants.php');
include('includes/functions.php');

startHeader();
displayHeader('Image Editor');
endHeader();

displayContentStart();

echo '<p>
		The following tools are free, basic image manipulation tools. We are not responsible for any unwanted alterations that may occur to the images you upload. Always keep a backup! Any images you upload will be saved on the server and not deleted.
	  </p>
	  <p>
		Everything you see was created by Ryan Shotwell using <a href="http://www.w3.org/html/">HTML</a>, <a href="http://www.w3.org/Style/CSS/Overview.en.html">CSS</a>, <a href="http://php.net/">PHP</a>, and <a href="http://jquery.com/">jQuery</a>, with the exception of <a href="http://deepliquid.com/content/Jcrop.html" target="_blank">Jcrop</a>. The Jcrop plugin was used to create a visual way of selecting parts of an image you want to crop out.
	  </p>
	  <p>
		If you are bored, you can click the Image Editor logo and it will change colors.
	  </p>';

displayContentEnd();

?>