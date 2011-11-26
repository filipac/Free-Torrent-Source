<?php
 #############################################################################
 # IMDBPHP                              © Giorgos Giagas & Itzchak Rehberg #
 # written by Giorgos Giagas                                                 #
 # extended & maintained by Itzchak Rehberg <izzysoft@qumran.org>            #
 # [url=http://www.qumran.org/homes/izzy/]http://www.qumran.org/homes/izzy/[/url]                                         #
 # ------------------------------------------------------------------------- #
 # This program is free software; you can redistribute and/or modify it      #
 # under the terms of the GNU General Public License (see doc/LICENSE)       #
 #############################################################################

 //=== updated and moded for tbdev by snuggles :P
 
 /* $Id: imdb.php,v 1.2 2007/02/20 21:28:38 izzy Exp $ */

 ob_start();
$root = ".";
$rootpath = './';
require_once("$root/include/bittorrent.php");

loggedinorreturn();
$userid=$CURUSER[id];
 
stdhead("Upload");
 
require ("include/imdb/imdb.class.php");
$movie = new imdb ($HTTP_GET_VARS["mid"]);
if (isset ($HTTP_GET_VARS["mid"])) {
  $movieid = $HTTP_GET_VARS["mid"];
  $movie->setid ($movieid);

  $imdb_link = "http://www.imdb.com/title/tt$movieid";

//=== if film not found in db...
if ($movieid){  
  
//=== Title
$title = $movie->title();
  flush();

//=== Photo

$poster =  '[img]'.$movie->photo().'[/img]';


//=== AKAs
  foreach ( $movie->alsoknow() as $ak){
  
$akas .=  $ak["title"].": ".$ak["year"].", ".$ak["country"]." (".$ak["comment"].")";
$akas = str_replace(array("<a", ">", "()", ",", "class=\"tn15more\"", "<", "href=\"/rg/title-tease/akas/title", "/a"), array("", "]", "", "", "", "[", "[url=\"http://www.imdb.com/rg/title-tease/akas/title", "/url"), $akas);
$akas = str_replace('"','',$akas); 
$akas .=  " \n";  
  }

  flush();

//=== Year 
$year = $movie->year();

$runtime =  $movie->runtime ().' minutes';
  flush();

//=== MPAA lol
  foreach ($movie->mpaa() as $key=>$mpaa) {
$mpaa_ratings .=  "$key: $mpaa\n";
  }

//=== Country
  $country = $movie->country();
  for ($i = 0; $i + 1 < count($country); $i++) {
$film_country .= $country[$i].', ';
  }
$country2 =  "$film_country $country[$i]";

//=== Main Genre
$main_genre =  $movie->genre();

//=== All Genres
  $gen = $movie->genres();
  for ($i = 0; $i + 1 < count($gen); $i++) {
$all_genres .= $gen[$i].', ';
  }
$genres =  "$all_genres $gen[$i]"; 

//=== Colors
  $col = $movie->colors();
  for ($i = 0; $i + 1 < count($col); $i++) {
$color .= $col[$i].', ';
  }
$colors =  "$color $col[$i]"; 

  flush();

//=== Sound
  $sound = $movie->sound ();
  for ($i = 0; $i + 1 < count($sound); $i++) {
$sound2 .= $sound[$i].', ';
  }
$sound =  "$sound2 $sound[$i]"; 

//=== tagline
$tagline = $movie->tagline();

//=== Plot outline
$plotoutline = $movie->plotoutline();

  flush();
  
//=== movie plots
  $plot = $movie->plot();
  for ($i = 0; $i < count($plot); $i++) {
  $plot = str_replace(array("<", ">", "a href=\"/SearchPlotWriters", "/a"), array("[", "]", "url=http://www.imdb.com/SearchPlotWriters", "/url"), $plot);
  $plot = str_replace('"','',$plot); 
$all_plot .= "[*]".$plot[$i]." \n";
  }

  flush();
  
//=====[ Staff ]==

//=== director(s)
  $director = $movie->director();
if($director){
for ($i = 0; $i < count($director); $i++) {
$directors .= '[url=http://us.imdb.com/Name?';
$directors .= $director[$i]["imdb"];
$directors .= ']';
$directors .= $director[$i]["name"];
$directors .= "[/url] ";
$directors .= $director[$i]["role"];
$directors .= " \n";
  }
  }


//=== Story
  $write = $movie->writing();
		if($write){
     for ($i = 0; $i < count ($write); $i++) {
$writer .= $write[$i]["name"];
$writer .= " ";
$writer .= $write[$i]["role"];
if ($i < count)
$writer .= ", ";
     }
	 }

  flush();

//=== Producer
  $produce = $movie->producer();
  for ($i = 0; $i < count($produce); $i++) {
$producers .= '[url=http://us.imdb.com/Name?'.$produce[$i]["imdb"].']';
$producers .= $produce[$i]["name"].'[/url] ';
$producers .= $produce[$i]["role"]." \n";
  }

//=== Music
  $compose = $movie->composer();
  for ($i = 0; $i < count($compose); $i++) {
$composers .= '[url=http://us.imdb.com/Name?'.$compose[$i]["imdb"].']';
$composers .= $compose[$i]["name"]."[/url] \n";
  }

  flush();

//=== Cast
  $cast = $movie->cast();
  for ($i = 0; $i < count($cast); $i++) {
$all_cast .= '[url=http://us.imdb.com/Name?'.$cast[$i]["imdb"].']';
$all_cast .= $cast[$i]["name"].'[/url] ';
$all_cast .= $cast[$i]["role"]." \n";
  }

  flush();

}//=== end of info
}//=== end if not found at imdb

//=== give them some style change to match your site & bb code
if ($title)
$title = "[size=6]".$title."[/size]";
if ($poster) 
$poster = "$poster";
if ($akas) 
$akas = "[b]AKAs:[/b] \n $akas";
if ($year) 
$year = "[b]Year:[/b] [ $year ]";
if ($runtime) 
$runtime = "[b]Runtime:[/b] $runtime";
if ($mpaa_ratings) 
$mpaa_ratings = "[b]MPAA Ratings:[/b] \n $mpaa_ratings";
if ($rating) 
$rating = "[b]IMDB Rating:[/b] $rating"; 
if ($votes) 
$votes = "[b]Number of Votes:[/b] $votes"; 
if ($languages2) 
$languages2 = "[b]Film Language(s):[/b] $languages2"; 
if ($country2) 
$country2  = "[b]Film Country(s):[/b] $country2";
if ($main_genre) 
$main_genre  = "[b]Main Genre:[/b] $main_genre";
if ($genres) 
$genres  = "[b]Other Genre(s): [/b] $genres";
if ($colors) 
$colors  = "[b]Filmed in:[/b] $colors";
if ($sound) 
$sound  = "[b]Sound:[/b] $sound ";
if ($tagline) 
$tagline  = "[b]Tagline:[/b]  $tagline ";
if ($taglines) 
$taglines  = "[b]Tagline(s):[/b] $taglines";
if ($plotoutline) 
$plotoutline  = "[b]Plot Outline:[/b] $plotoutline";
if ($all_plot) 
$all_plot = "[b]More:[/b] $all_plot";
if ($directors) 
$directors  = "[b]Director(s):[/b] $directors";
if ($writer) 
$writer  = "[b]Writer(s):[/b] $writer";
if ($producers) 
$producers  = "[b]Producer(s):[/b] \n $producers";
if ($composers) 
$composers  = "[b]Composer(s):[/b] $composers";
if ($all_cast)
$all_cast = "[b]Cast:[/b] \n $all_cast";

$rep = array(
'[size=6]','[/size]','[b]','[/b]','[ ',']','Year:'
);
$torna = str_replace($rep,'',$title);
$toryr = str_replace($rep,'',$year);
$value1 = "$torna ($toryr)";
$value2 = "$title
$poster
$akas 
$year 
$runtime 
$mpaa_ratings
$rating 
$votes 
$languages2 
$country2 
$main_genre 
$genres 
$colors 
$sound 
$tagline 
$taglines 
$plotoutline 
$directors 
$writer 
$producers 
$composers 
$all_cast 
[b]imdb link:[/b] $imdb_link";
upload_form($value1,$value2,$movie->photo());
stdfoot();
?>