<?php
// Увімкнення відображення помилок
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

// Встановлення рівня звітності помилок
error_reporting(E_ALL);



include_once "dates.php";
include_once "freedman/head.php";
include_once "slider_spons.php";
include_once "freedman/menu.php";
include_once "run_line.php";
include_once "freedman/ligi.php";

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
        mf.pict AS player_photo,
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
    MONTHNAME(MIN(date)) AS month_name,
    DATE_FORMAT(MIN(date), '%d') AS day_min, 
    DATE_FORMAT(MAX(date), '%d') AS day_max 
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
$currentTur = 1;
if(isset($_GET['tur'])){
    // Берем тур из адресной строки
    $currentTur = $_GET['tur'];
}

// Все игроки из выбранного тура
$bestPlayers = getPlayersOfTur($allStaticPlayers, $currentTur);

// Лучшие игроки - отфильтрованные
$bestPlayersForTable = mergeStaticAndData($bestPlayers, $dataAllPlayers);

// dump_arr_first($bestPlayersForTable);

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

?>

<section class="calendar-of-matches">
    <div class="calendar-of-matches__grid-container">
        <div class="calendar-of-matches__head">
            <h2 class="calendar-of-matches__title title">Календар матчів</h2>
        </div><!-- calendar-of-matches__head -->

        <div class="calendar-of-matches__head-nav">

            <div class="swiper swiper-month-controls swiper-initialized swiper-horizontal">
                <div class="swiper-wrapper swiper-wrapper-month-controls" id="swiper-wrapper-2894150f39673565" aria-live="polite" style="transition-duration: 0ms; transform: translate3d(0px, 0px, 0px);">
                    <?php foreach($dateTurs as $dateTur): ?>
                        <div class="swiper-slide swiper-slide-month-controls swiper-slide-active" role="group" aria-label="1 / 15" style="margin-right: 5px;">
                            <a  class="month-controls__button <?= $dateTur['tur'] <= $lastTur ? 'month-controls__button--past ' : '' ?> <?= $currentTur ==  $dateTur['tur'] ? 'month-controls__button--current' : '' ?>" 
                                href="<?= $url ?>/team_tur?tur=<?=$dateTur['tur']?>
                            ">
                                <p><?= date_translate($dateTur['month_name']) ?></p>
                                <p><?= $dateTur['day_min'] == $dateTur['day_max'] ? $dateTur['day_min'] : $dateTur['day_min'] .'-'. $dateTur['day_max'] ?></p>
                            </a>
                        </div>
                    <?php endforeach ?>                  
                </div>

                <div class="swiper-scrollbar swiper-scrollbar-horizontal"><div class="swiper-scrollbar-drag" style="transform: translate3d(0px, 0px, 0px); width: 70px; transition-duration: 0ms;"></div></div>
                <span class="swiper-notification" aria-live="assertive" aria-atomic="true"></span></div>
            </div> <!-- swiper  -->

            <div class="calendar-of-matches__aside">
                <div class="swiper-matches swiper swiper-initialized swiper-horizontal swiper-backface-hidden">
                    <div class="swiper-wrapper" id="swiper-wrapper-1b58efbe70e5fba9" aria-live="polite" style="transform: translate3d(0px, 0px, 0px);">
                        <div class="swiper-slide swiper-slide-active" role="group" aria-label="1 / 5" style="margin-right: 5px;">
                        <div class="card-of-matches">
                            <div class="card-of-matches__title-match">
                            <img class="card-of-matches__shirt card-of-matches__shirt--left" src="/css/components/card-of-matches/assets/images/yellow-shirt.svg" alt="yellow">

                            <img class="card-of-matches__team-logo card-of-matches__team-logo--left" src="/css/components/card-of-matches/assets/images/chelsea-logo.svg" alt="chelsea">
                    
                            <div class="card-of-matches__score">
                                <span>1</span>
                                :
                                <span>0</span>
                            </div>
                    
                            <img class="card-of-matches__team-logo card-of-matches__team-logo--right" src="/css/components/card-of-matches/assets/images/leicester-logo.svg" alt="leicester">

                            <p class="card-of-matches__team card-of-matches__team--left">ФК Челсі</p>
                    
                            <div class="card-of-matches__date-and-time">
                                <div class="card-of-matches__date">13 серпня (сб)</div>
                                <div class="card-of-matches__time">16:00</div>
                            </div>
                    
                            <p class="card-of-matches__team card-of-matches__team--right">Лестер</p>

                            <img class="card-of-matches__shirt card-of-matches__shirt--right" src="/css/components/card-of-matches/assets/images/blue-shirt.svg" alt="blue">

                            <div class="card-of-matches__marks">
                                <a class="card-of-matches__mark"><span>Прем'єр-Ліга</span></a>
                                <a class="card-of-matches__mark"><span>5 тур</span></a>
                                <a class="card-of-matches__mark"><span>X-Park</span></a>
                            </div>
                            </div>
                            
                            <div class="card-of-matches__controls">
                            <a href="#"><img src="/css/components/card-of-matches/assets/images/edit-icon.svg" alt=""></a>
                            <a href="#"><img src="/css/components/card-of-matches/assets/images/scale-icon.svg" alt=""></a>
                            <a href="#"><img src="/css/components/card-of-matches/assets/images/cut-icon.svg" alt=""></a>
                            <a href="#"><img src="/css/components/card-of-matches/assets/images/hd-icon.svg" alt=""></a>
                            <a href="#"><img src="/css/components/card-of-matches/assets/images/red-icon.svg" alt=""></a>
                            <a href="#"><img src="/css/components/card-of-matches/assets/images/photo-icon.svg" alt=""></a>
                            </div>

                            <div class="card-of-matches__status">МАТЧ ЗАВЕРШЕНО</div>

                            <a class="card-of-matches__share-button" href="#">
                            <img src="/css/components/card-of-matches/assets/images/share-icon.svg" alt="share">
                            </a>
                        </div>
                        </div>

                        <div class="swiper-slide swiper-slide-next" role="group" aria-label="2 / 5" style="margin-right: 5px;">
                        <div class="card-of-matches">
                            <div class="card-of-matches__title-match">
                            <img class="card-of-matches__shirt card-of-matches__shirt--left" src="/css/components/card-of-matches/assets/images/yellow-shirt.svg" alt="yellow">

                            <img class="card-of-matches__team-logo card-of-matches__team-logo--left" src="/css/components/card-of-matches/assets/images/chelsea-logo.svg" alt="chelsea">
                    
                            <div class="card-of-matches__score">
                                <span>1</span>
                                :
                                <span>0</span>
                            </div>
                    
                            <img class="card-of-matches__team-logo card-of-matches__team-logo--right" src="/css/components/card-of-matches/assets/images/leicester-logo.svg" alt="leicester">

                            <p class="card-of-matches__team card-of-matches__team--left">ФК Челсі</p>
                    
                            <div class="card-of-matches__date-and-time">
                                <div class="card-of-matches__date">13 серпня (сб)</div>
                                <div class="card-of-matches__time">16:00</div>
                            </div>
                    
                            <p class="card-of-matches__team card-of-matches__team--right">Лестер</p>

                            <img class="card-of-matches__shirt card-of-matches__shirt--right" src="/css/components/card-of-matches/assets/images/blue-shirt.svg" alt="blue">

                            <div class="card-of-matches__marks">
                                <a class="card-of-matches__mark"><span>Прем'єр-Ліга</span></a>
                                <a class="card-of-matches__mark"><span>5 тур</span></a>
                                <a class="card-of-matches__mark"><span>X-Park</span></a>
                            </div>
                            </div>
                            
                            <div class="card-of-matches__controls">
                            <a href="#"><img src="/css/components/card-of-matches/assets/images/edit-icon.svg" alt=""></a>
                            <a href="#"><img src="/css/components/card-of-matches/assets/images/scale-icon.svg" alt=""></a>
                            <a href="#"><img src="/css/components/card-of-matches/assets/images/cut-icon.svg" alt=""></a>
                            <a href="#"><img src="/css/components/card-of-matches/assets/images/hd-icon.svg" alt=""></a>
                            <a href="#"><img src="/css/components/card-of-matches/assets/images/red-icon.svg" alt=""></a>
                            <a href="#"><img src="/css/components/card-of-matches/assets/images/photo-icon.svg" alt=""></a>
                            </div>

                            <div class="card-of-matches__status">МАТЧ ЗАВЕРШЕНО</div>

                            <a class="card-of-matches__share-button" href="#">
                            <img src="/css/components/card-of-matches/assets/images/share-icon.svg" alt="share">
                            </a>
                        </div>
                        </div>

                        <div class="swiper-slide" role="group" aria-label="3 / 5" style="margin-right: 5px;">
                        <div class="card-of-matches">
                            <div class="card-of-matches__title-match">
                            <img class="card-of-matches__shirt card-of-matches__shirt--left" src="/css/components/card-of-matches/assets/images/yellow-shirt.svg" alt="yellow">

                            <img class="card-of-matches__team-logo card-of-matches__team-logo--left" src="/css/components/card-of-matches/assets/images/chelsea-logo.svg" alt="chelsea">
                    
                            <div class="card-of-matches__score">
                                <span>1</span>
                                :
                                <span>0</span>
                            </div>
                    
                            <img class="card-of-matches__team-logo card-of-matches__team-logo--right" src="/css/components/card-of-matches/assets/images/leicester-logo.svg" alt="leicester">

                            <p class="card-of-matches__team card-of-matches__team--left">ФК Челсі</p>
                    
                            <div class="card-of-matches__date-and-time">
                                <div class="card-of-matches__date">13 серпня (сб)</div>
                                <div class="card-of-matches__time">16:00</div>
                            </div>
                    
                            <p class="card-of-matches__team card-of-matches__team--right">Лестер</p>

                            <img class="card-of-matches__shirt card-of-matches__shirt--right" src="/css/components/card-of-matches/assets/images/blue-shirt.svg" alt="blue">

                            <div class="card-of-matches__marks">
                                <a class="card-of-matches__mark"><span>Прем'єр-Ліга</span></a>
                                <a class="card-of-matches__mark"><span>5 тур</span></a>
                                <a class="card-of-matches__mark"><span>X-Park</span></a>
                            </div>
                            </div>
                            
                            <div class="card-of-matches__controls">
                            <a href="#"><img src="/css/components/card-of-matches/assets/images/edit-icon.svg" alt=""></a>
                            <a href="#"><img src="/css/components/card-of-matches/assets/images/scale-icon.svg" alt=""></a>
                            <a href="#"><img src="/css/components/card-of-matches/assets/images/cut-icon.svg" alt=""></a>
                            <a href="#"><img src="/css/components/card-of-matches/assets/images/hd-icon.svg" alt=""></a>
                            <a href="#"><img src="/css/components/card-of-matches/assets/images/red-icon.svg" alt=""></a>
                            <a href="#"><img src="/css/components/card-of-matches/assets/images/photo-icon.svg" alt=""></a>
                            </div>

                            <div class="card-of-matches__status">МАТЧ ЗАВЕРШЕНО</div>

                            <a class="card-of-matches__share-button" href="#">
                            <img src="/css/components/card-of-matches/assets/images/share-icon.svg" alt="share">
                            </a>
                        </div>
                        </div>

                        <div class="swiper-slide" role="group" aria-label="4 / 5" style="margin-right: 5px;">
                        <div class="card-of-matches">
                            <div class="card-of-matches__title-match">
                            <img class="card-of-matches__shirt card-of-matches__shirt--left" src="/css/components/card-of-matches/assets/images/yellow-shirt.svg" alt="yellow">

                            <img class="card-of-matches__team-logo card-of-matches__team-logo--left" src="/css/components/card-of-matches/assets/images/chelsea-logo.svg" alt="chelsea">
                    
                            <div class="card-of-matches__score">
                                <span>1</span>
                                :
                                <span>0</span>
                            </div>
                    
                            <img class="card-of-matches__team-logo card-of-matches__team-logo--right" src="/css/components/card-of-matches/assets/images/leicester-logo.svg" alt="leicester">

                            <p class="card-of-matches__team card-of-matches__team--left">ФК Челсі</p>
                    
                            <div class="card-of-matches__date-and-time">
                                <div class="card-of-matches__date">13 серпня (сб)</div>
                                <div class="card-of-matches__time">16:00</div>
                            </div>
                    
                            <p class="card-of-matches__team card-of-matches__team--right">Лестер</p>

                            <img class="card-of-matches__shirt card-of-matches__shirt--right" src="/css/components/card-of-matches/assets/images/blue-shirt.svg" alt="blue">

                            <div class="card-of-matches__marks">
                                <a class="card-of-matches__mark"><span>Прем'єр-Ліга</span></a>
                                <a class="card-of-matches__mark"><span>5 тур</span></a>
                                <a class="card-of-matches__mark"><span>X-Park</span></a>
                            </div>
                            </div>
                            
                            <div class="card-of-matches__controls">
                            <a href="#"><img src="/css/components/card-of-matches/assets/images/edit-icon.svg" alt=""></a>
                            <a href="#"><img src="/css/components/card-of-matches/assets/images/scale-icon.svg" alt=""></a>
                            <a href="#"><img src="/css/components/card-of-matches/assets/images/cut-icon.svg" alt=""></a>
                            <a href="#"><img src="/css/components/card-of-matches/assets/images/hd-icon.svg" alt=""></a>
                            <a href="#"><img src="/css/components/card-of-matches/assets/images/red-icon.svg" alt=""></a>
                            <a href="#"><img src="/css/components/card-of-matches/assets/images/photo-icon.svg" alt=""></a>
                            </div>

                            <div class="card-of-matches__status">МАТЧ ЗАВЕРШЕНО</div>

                            <a class="card-of-matches__share-button" href="#">
                            <img src="/css/components/card-of-matches/assets/images/share-icon.svg" alt="share">
                            </a>
                        </div>
                        </div>

                        <div class="swiper-slide" role="group" aria-label="5 / 5" style="margin-right: 5px;">
                        <div class="card-of-matches">
                            <div class="card-of-matches__title-match">
                            <img class="card-of-matches__shirt card-of-matches__shirt--left" src="/css/components/card-of-matches/assets/images/yellow-shirt.svg" alt="yellow">

                            <img class="card-of-matches__team-logo card-of-matches__team-logo--left" src="/css/components/card-of-matches/assets/images/chelsea-logo.svg" alt="chelsea">
                    
                            <div class="card-of-matches__score">
                                <span>1</span>
                                :
                                <span>0</span>
                            </div>
                    
                            <img class="card-of-matches__team-logo card-of-matches__team-logo--right" src="/css/components/card-of-matches/assets/images/leicester-logo.svg" alt="leicester">

                            <p class="card-of-matches__team card-of-matches__team--left">ФК Челсі</p>
                    
                            <div class="card-of-matches__date-and-time">
                                <div class="card-of-matches__date">13 серпня (сб)</div>
                                <div class="card-of-matches__time">16:00</div>
                            </div>
                    
                            <p class="card-of-matches__team card-of-matches__team--right">Лестер</p>

                            <img class="card-of-matches__shirt card-of-matches__shirt--right" src="/css/components/card-of-matches/assets/images/blue-shirt.svg" alt="blue">

                            <div class="card-of-matches__marks">
                                <a class="card-of-matches__mark"><span>Прем'єр-Ліга</span></a>
                                <a class="card-of-matches__mark"><span>5 тур</span></a>
                                <a class="card-of-matches__mark"><span>X-Park</span></a>
                            </div>
                            </div>
                            
                            <div class="card-of-matches__controls">
                            <a href="#"><img src="/css/components/card-of-matches/assets/images/edit-icon.svg" alt=""></a>
                            <a href="#"><img src="/css/components/card-of-matches/assets/images/scale-icon.svg" alt=""></a>
                            <a href="#"><img src="/css/components/card-of-matches/assets/images/cut-icon.svg" alt=""></a>
                            <a href="#"><img src="/css/components/card-of-matches/assets/images/hd-icon.svg" alt=""></a>
                            <a href="#"><img src="/css/components/card-of-matches/assets/images/red-icon.svg" alt=""></a>
                            <a href="#"><img src="/css/components/card-of-matches/assets/images/photo-icon.svg" alt=""></a>
                            </div>

                            <div class="card-of-matches__status">МАТЧ ЗАВЕРШЕНО</div>

                            <a class="card-of-matches__share-button" href="#">
                            <img src="/css/components/card-of-matches/assets/images/share-icon.svg" alt="share">
                            </a>
                        </div>
                        </div>
                    </div>

                <div class="swiper-bg-scroll"></div>
                <div class="swiper-scrollbar swiper-scrollbar-horizontal swiper-scrollbar-lock">
                    <div class="swiper-scrollbar-drag" style="transform: translate3d(0px, 0px, 0px); width: 70px;"></div>
                </div>
                <span class="swiper-notification" aria-live="assertive" aria-atomic="true"></span>
            </div><!-- calendar-of-matches__aside -->
        </div><!-- calendar-of-matches__head-nav -->

        <div class="calendar-of-matches__dynamic-content">
            <section class="green-zone">
            <div class="green-zone__current">
                <h2 class="green-zone__title title">ЗБІРНА ТУРУ</h2>
        
                <div class="green-zone__players">
                

                    <?php foreach($bestPlayersForTable as $player) : ?>
            
                        <div class="player-card">
                            <div class="player-card__photo-container">
                                <div class="player-card__left-icon">
                                    <img src="/css/components/player-card/assets/images/<?= $labels[$player['best_player']]['icon'] ?>" alt="star">
                                    <span><?= $player['count_points'] ?></span>
                                </div>
                    
                                <img class="player-card__right-icon" src="<?= $team_logo_path ?>/<?= $player['team_photo'] ?>" alt="Логотип команды">
                    
                                <img class="player-card__photo" src="<?= $player_face_path ?>/<?= $player['player_photo'] ?>" alt="yarmol">
                            </div>
                
                            <div class="player-card__role"><?= $labels[$player['best_player']]['role'] ?></div>
                            <div class="player-card__club"><?= $player['team_name'] ?></div>
                            <div class="player-card__name"><?= $player['first_name'] ?> <?= $player['last_name'] ?></div>
                
                            <a href="#" class="player-card__link">
                            <span>Таблиця</span>
                            <img src="/css/components/player-card/assets/images/arrow-icon.svg" alt="arrow">
                            </a>
                        </div>

                    <?php endforeach ?>

                    <div class="green-zone__footer-title">
                        <img src="/css/components/green-zone/assets/images/v9ku-logo-on-white-back.png" alt="v9ku-logo">
            
                        <h3>СЕЗОН «ЛІТО 2022»&nbsp;&nbsp;ӏ&nbsp;&nbsp;ПРЕМ’ЄР ЛІГА&nbsp;&nbsp;ӏ&nbsp;&nbsp;8Х8&nbsp;&nbsp;ӏ&nbsp;&nbsp;5 ТУР</h3>
            
                        <img src="/css/components/green-zone/assets/images/v9ku-logo-on-white-back.png" alt="v9ku-logo">
                    </div>
                </div>
        
            </div>
            </section>
        </div> <!-- calendar-of-matches__dynamic-content -->
    </div> <!-- calendar-of-matches__grid-container -->
</section>

<?php 
    include_once "freedman/footer.php";
?>