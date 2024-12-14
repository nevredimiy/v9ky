<? 
define('READFILE', true);

include_once "dates.php";
include_once "head.php";
include_once "slider_spons.php";
include_once "menu.php";
include_once "run_line.php";
include_once "ligi.php";

if(isset($_GET['foo'])){
    include_once "rating_players.php";
}


// $cachefile = 'jeka_cashe/head_tables/cached-'.$tournament.'.html';
//$cachetime = 900;

//$golupdate = $db->Execute("select updatet, active from v9ky_turnir where id='".$turnir."'");

//if ((file_exists($cachefile) && ((strtotime($golupdate->fields[updatet]) < filemtime($cachefile)) or ($golupdate->fields[active]==0)))) {
//    echo "<!-- Cached copy, generated ".date('H:i', filemtime($cachefile))." -->\n";
//    include($cachefile);
//} else {ob_start(); 
if (!isset($_GET['foo'])) {

    include_once "head_tables.php";


    //$cached = fopen($cachefile, 'w');
    //fwrite($cached, ob_get_contents());
    //fclose($cached);
    //ob_end_flush(); 
    //}

    include_once "center_buttons.php";

    include_once "left.php";
    include_once "news_buttons.php";

}
?>