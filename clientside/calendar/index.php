<?php
include "calendar.php";

$datad_c=new DHTML_Calendar('datad');
print $datad_c->load_files();
echo $datad_c->make_input_field();
?>