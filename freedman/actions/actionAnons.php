<?php


// Увімкнення відображення помилок
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

// Встановлення рівня звітності помилок
error_reporting(E_ALL);


  
require_once __DIR__ . '/../../../freedman/config.php';
require_once __DIR__ . '/../functions.php';
require_once __DIR__ . '/../helpers.php';

$data = json_decode(file_get_contents('php://input'), true);


if($data['match_id'] && $data['tur'] && $data['turnir'] ) { 
    

    $dataCurrentTur = getDataCurrentTur($data['turnir'], $data['tur']);

    // Добавляем два элемента в массивы - форматированная дата и время матча.
    $dataCurrentTurWithDate = getArrayWithFormattedDate($dataCurrentTur);

    $dataAnons = [];
    
    foreach ($dataCurrentTurWithDate as $dataMatc) {
        if($dataMatc['id'] == $data['match_id']){
            $dataAnons['team1_name'] = $dataMatc['team1_name'];
            $dataAnons['team1_photo'] = $dataMatc['team1_photo'];
            $dataAnons['team2_name'] = $dataMatc['team2_name'];
            $dataAnons['team2_photo'] = $dataMatc['team2_photo'];
            $dataAnons['anons'] = $dataMatc['anons'];
            $dataAnons['goals1'] = $dataMatc['goals1'];
            $dataAnons['goals2'] = $dataMatc['goals2'];
            break;
        }
    }

    require_once "../views/anons.tpl.php";
    die;
}