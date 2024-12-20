<?php 

// // Увімкнення відображення помилок
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);

// // Встановлення рівня звітності помилок
// error_reporting(E_ALL);


  
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/functions.php';
require_once 'helpers.php';

$data = json_decode(file_get_contents('php://input'), true);


if($data['tur'] && $data['turnir']) {

    // Когда tournament пустая это ознчает, что в адресной строке нет названия тура. Обычно это надпись после слеша в адресной строке
    // Если переменная tournament пустая, то заполняем ее из последнего сезона первым туром
    if (!$tournament) {
    
        $tournament = mb_substr( strstr( $_SERVER["REQUEST_URI"], "?", true ), 1 );

        if(!$tournament) {
            $tournament = getTournament();
        }

    }
     

    $lastTur = $data['lasttur'];
    $currentTur = $data['tur'];
    $turnir = $data['turnir'];
    
    $allStaticPlayers = getAllStaticPlayers($turnir);
    $dataAllPlayers = getDataPlayers($allStaticPlayers);
    $dateTurs = getDateTurs($turnir);

    if($currentTur <= $lastTur) {
        // Все игроки из выбранного тура
        $bestPlayers = getPlayersOfTur($allStaticPlayers, $currentTur);

        // Лучшие игроки - отфильтрованные
        $bestPlayersForTable = mergeStaticAndData($bestPlayers, $dataAllPlayers);


        $labels = [
            'topgravetc' => ['icon' => 'star-icon.png', 'role' => 'Топ-Гравець'], 
            'golkiper' => ['icon' => 'gloves-icon.png', 'role' => 'Топ-Голкіпер'], 
            'bombardir' => ['icon' => 'football-icon.png', 'role' => 'Топ-Бомбардир'], 
            'asistent' => ['icon' => 'boots-icon.svg', 'role' => 'Топ-Асистент'],
            'zahusnuk' => ['icon' => 'pitt-icon.svg', 'role' => 'Топ-Захисник'],
            'dribling' => ['icon' => 'player-icon.svg', 'role' => 'Топ-Дриблінг'],
            'udar' => ['icon' => 'rocket-ball-icon.png', 'role' => 'Топ-Удар'],
            'pas' => ['icon' => 'ball-icon.png', 'role' => 'Топ-Пас'],
        ];
    }

    $dataCurrentTur = getDataCurrentTur($turnir, $currentTur);

    // Добавляем два элемента в массивы - форматированная дата и время матча.
    $dataCurrentTurWithDate = getArrayWithFormattedDate($dataCurrentTur);   
    
    require_once 'views/calendar_of_matches.tpl.php';
    
    die;
}