<?php
function Select_Random_Indices($source_array, $count = 1)
{
    if($count >= 0)
    {
        if($count == 1)
        {
            $result = array(array_rand($source_array, $count));
        }
        else
        {
            $result = array_rand($source_array, $count);
        }
    }
    else
    {
        $result = array();
    }

    return $result;
}

// using the above function to pick random values instead of entries
function Select_Random_Entries($source_array, $count = 1)
{
    $result = array();
    $index_array = Select_Random_Indices($source_array, $count);

    foreach($index_array as $index)
    {
        $result[$index] = $source_array[$index];
    }

    return $result;
}
$rand = array(
"Did you know that we have an forum where you can talk with other users? Click <a href=\"http://freetosu.berlios.de/forums\" target=\"_blank\">here</a> to visit it.",
"Did you know that we have a blog? Click <a href=\"http://freetosu.berlios.de/blog\" target=\"_blank\">here</a> to visit it!",
"Did you know that we have an wiki? Click <a href=\"http://freetosu.berlios.de/wiki\" target=\"_blank\">here</a> to visit it!"
);
$randk = Select_Random_Entries($rand);
?>
<HEAD>
<META http-equiv=content-type content="text/html; charset=iso-8859-1">
<link rel="stylesheet" type="text/css" media="all" href="style.css" />
</HEAD>
<DIV id=ipbwrapper style="text-align:center;">
<img src=images/fts.png /><br>
<p><?php
foreach($randk as $a => $b)
echo $b;
?>
</p>
<br>
<img src=pic/fresh.png onclick="window.location='install_fresh.php';"/>
</div></div></div></div>