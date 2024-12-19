<?php 

// Увімкнення відображення помилок
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

// Встановлення рівня звітності помилок
error_reporting(E_ALL);


  
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/functions.php';

$data = json_decode(file_get_contents('php://input'), true);

print_r($data);
var_dump($data);



if($data['tur'] && $data['turnir']) {
    $bestPlayersForTable = getBestPlayerOfTurForAjax($data['turnir'], $data['tur']);

    print_r($bestPlayersForTable);

    $lastTur = $data['lasttur'];
    $currentTur = $data['tur'];
    $turnir = $data['turnir'];
    
    
    require_once 'views/calendar_of_matches_content.tpl.php';
    
    die;
}