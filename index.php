<?php
include_once "imageresize.php";
$input_file = "images/test.jpg";
$destination_folder = "images/";
$obj = new resizeImage($input_file,$destination_folder);
$obj->setBackgroundColor(255,255,255);    // for white background
$obj->resize();
echo $obj->destination;                 // newly created image name
