<? 
define('READFILE', true);

$nenadacss = 1;
include_once "head.php";
include_once "slider_spons.php";

if(!$_GET['foo']) {
    
    include_once "menu.php";
    //include_once "run_line.php";
    include_once "goroda.php";
    include_once "glavnaya.php";
    include_once "footer.php";

} 

if($_GET['foo']) {

    include_once "freedman/menu.php";
    include_once "freedman/ligi.php";
    include_once "freedman/rating_players.php";
    include_once "freedman/main.php";
    include_once "freedman/footer.php";

}

?>