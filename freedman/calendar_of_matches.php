<?php

include_once "freedman/helpers.php";

// Получаем количество сыграных туров в турнире. Это нужно для отображения в таблице знака вопроса для несыграных матчей. 
$queryLastTur = $db->Execute(
    "SELECT tur as last_tur FROM `v9ky_match` WHERE `canseled`=1 and `turnir` = $turnir order by tur desc limit 1"
    );
    
// Последний тур в турнире (в лиге).
$lastTur = intval($queryLastTur->fields[0]);

// Получаем данные из БД. Статискика всех игроков учавствуюих в текущей лиге. Статистика вся, кроме забитых голов
$queryStaticPlayers = $db->Execute( 
    "SELECT 
      m.tur, 
      s.player, 
      s.matc, 
      s.seyv, 
      s.seyvmin, 
      s.vstvor, 
      s.mimo, 
      s.pasplus, 
      s.pasminus, 
      s.otbor, 
      s.otbormin, 
      s.obvodkaplus, 
      s.obvodkaminus, 
      s.golevoypas, 
      s.zagostrennia, 
      s.vkid, 
      s.vkidmin, 
      s.blok, 
      s.vtrata,
      (SELECT COUNT(*) AS count_goals FROM v9ky_gol g WHERE g.player= s.player and g.matc = s.matc) AS count_goals,
      (SELECT COUNT(*) AS count_asists FROM v9ky_asist a WHERE a.player= s.player and a.matc = s.matc) AS count_asists,
      (SELECT COUNT(*) AS yellow_cards FROM v9ky_yellow y WHERE y.player= s.player and y.matc = s.matc) AS yellow_cards,
      (SELECT COUNT(*) AS yellow_red_cards FROM v9ky_yellow_red yr WHERE yr.player= s.player and yr.matc = s.matc) AS yellow_red_cards,
      (SELECT COUNT(*) AS red_cards FROM v9ky_red r WHERE r.player= s.player and r.matc = s.matc) AS red_cards
  FROM `v9ky_sostav` s
  LEFT JOIN `v9ky_match` m ON m.id = s.matc
  WHERE s.`player` IN (
      SELECT `id` 
      FROM `v9ky_player` 
      WHERE `team` IN (
          SELECT `id` 
          FROM `v9ky_team` 
          WHERE `turnir` = $turnir
      )
  )"
  );
  
  // Массив для cтастистики игроков учавствуюих в текущей лиге
  $allStaticPlayers = array(); 
  
  // Заполняем массив статистикой игроков, кроме статистики забитых голов
  while(!$queryStaticPlayers->EOF){
  
    foreach ( $queryStaticPlayers->fields as $key => $value ) {
      if (is_string($key)){
        $allStaticPlayers[$queryStaticPlayers->fields['player']][$queryStaticPlayers->fields['matc']][$key] = $value;
      }
    }
  
    $queryStaticPlayers->MoveNext();
  }
  
  // Массив только идентификаторов игроков
  $allPlayersId = array_keys($allStaticPlayers);
  
  // Делаем строку для апроса в БД.
  $strAllPlayersId = implode(", ", $allPlayersId);
  
  // Получаем данные по id - ФИО, фото,  и т.д.
  $queryAllPlayersData = $db->Execute(
    "SELECT 
        p.id AS player_id,
        p.team AS team_id,
        p.man AS man_id,
        p.amplua AS amplua,
        p.v9ky AS v9ky,
        p.dubler AS dubler,
        p.vibuv AS vibuv,
        m.name1 AS last_name,
        m.name2 AS first_name,
        (SELECT mf.pict 
            FROM v9ky_man_face mf 
            WHERE mf.man = p.man 
            ORDER BY mf.id DESC LIMIT 1) AS player_photo,
        t.pict AS team_photo,
        t.name AS team_name
    FROM 
        v9ky_player p
    LEFT JOIN 
        v9ky_man m ON p.man = m.id
    LEFT JOIN 
        v9ky_man_face mf ON m.id = mf.man
    LEFT JOIN 
        v9ky_team t ON p.team = t.id
    WHERE 
        p.id IN ($strAllPlayersId)  
  ");  
  
  // Данные всех игроков типа Имя, Фамилия, Фото и т.д
  $dataAllPlayers = [];  
  
  // Меняем структуру массива - для удобства работы с ним
  while(!$queryAllPlayersData->EOF){
    foreach ($queryAllPlayersData as $key => $value) {
      $playerId = $value['player_id'];
      if(!isset($dataAllPlayers[$playerId])) {      
          $dataAllPlayers[$playerId] = $value;             
      }
    }
    $queryAllPlayersData->MoveNext();
  }  
  
  // Берем даты проведения туров.
  $queryDateTurs = $db->Execute("SELECT 
    tur, 
    MIN(date) AS min_date, 
    MAX(date) AS max_date,
    MONTHNAME(MIN(date)) AS month_min_name,
    MONTHNAME(MAX(date)) AS month_max_name,
    DATE_FORMAT(MIN(date), '%d') AS day_min, 
    DATE_FORMAT(MAX(date), '%d') AS day_max,
    DATE_FORMAT(MIN(date), '%m') AS month_min, 
    DATE_FORMAT(MAX(date), '%m') AS month_max
FROM 
    v9ky_match
WHERE `turnir` = $turnir
GROUP BY 
    tur
ORDER BY 
    tur ASC;");

$dateTurs = [];

while(!$queryDateTurs->EOF){
    
    $dateTurs [] = $queryDateTurs->fields;
    
    $queryDateTurs->MoveNext();
}

// Выбранный тур
$currentTur = $lastTur != '' ? $lastTur : 1;
if(isset($_GET['tur'])){
    // Берем тур из адресной строки
    $currentTur = $_GET['tur'];
}

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

$queryDataCurrentTur = $db->Execute(
    "SELECT 
    t.season,
    m.date,
    m.tur, 
    m.team1,
    t1.name AS team1_name,
    t1.pict AS team1_photo,
    m.team2,    
    t2.name AS team2_name,
    t2.pict AS team2_photo,
    m.field,
    f.name AS field_name,
    m.canseled,
    m.gols1 AS goals1,
    m.gols2 AS goals2,
    t.ru AS turnir_name
FROM 
    v9ky_match m
LEFT JOIN 
	`v9ky_team` t1 ON t1.id = m.team1
LEFT JOIN
	`v9ky_team` t2 ON t2.id = m.team2
LEFT JOIN
    `v9ky_turnir` t ON t.id = m.turnir
LEFT JOIN
    `v9ky_fields` f ON f.id = m.field
WHERE m.`turnir` = $turnir AND m.`tur` = $currentTur 
ORDER BY 
    m.id"
);

$dataCurrentTur = [];
while(!$queryDataCurrentTur->EOF){
    $dataCurrentTur [] = $queryDataCurrentTur->fields;
    $queryDataCurrentTur->MoveNext();
}

// Добавляем два элемента в массивы - форматированная дата и время матча.
$dataCurrentTur = getArrayWithFormattedData($dataCurrentTur);

include_once "views/calendar_of_matches.tpl.php";