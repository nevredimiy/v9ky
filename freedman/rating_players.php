<?php

// TODO: freedman  Получаем данные для карточек игроков - "Место в лиге" и топ-таблиц
        
// Подключаю свой файл-помощник
include_once('freedman/helpers.php');
  
// Получаем данные из БД. Статискика всех игроков учавствуюих в текущей лиге. Статистика вся, кроме забитых голов
$queryStaticPlayers = $db->Execute( 
  "SELECT p.team,m.tur,s.player,s.matc,s.seyv, s.seyvmin, s.vstvor, s.mimo, s.pasplus, s.pasminus, s.otbor, s.otbormin, s.obvodkaplus, s.obvodkaminus, s.golevoypas, s.zagostrennia, s.vkid, s.vkidmin, s.blok, s.vtrata
  FROM `v9ky_sostav` s 
  LEFT JOIN `v9ky_match` m ON m.id = s.matc 
  LEFT JOIN `v9ky_player` p ON p.id = s.player
  WHERE `player` IN 
    (SELECT `id` FROM `v9ky_player` WHERE `team` IN 
    (SELECT `id` FROM `v9ky_team` WHERE `turnir` = $turnir ))"
);

$queryBestPlayerOfMatch = $db->Execute(
  "SELECT `best_player`, id, tur FROM `v9ky_match` WHERE `turnir`=523 and `best_player`>0 ORDER by tur"
);
          
  
// Получаем данные из БД. Статистика забитых голов игроков.
$queryGoals = $db->Execute( 
  "SELECT `matc`, `player` FROM `v9ky_gol` WHERE `player` IN  
  (SELECT `id` FROM `v9ky_player` WHERE `team` IN 
  (SELECT `id` FROM `v9ky_team` WHERE `turnir` = '" . $turnir . "' ))"
);

// Получаем данные из БД. Статистика желтых карточек игроков.
$queryAsist = $db->Execute( 
  "SELECT player, matc, COUNT(*) AS count_asists FROM v9ky_asist WHERE player IN (
      SELECT id FROM v9ky_player WHERE team IN (
              SELECT id FROM v9ky_team WHERE turnir = '" . $turnir . "'
              )
      )
  GROUP BY player;"
);

// Получаем данные из БД. Статистика желтых карточек игроков.
$queryYellowCards = $db->Execute( 
  "SELECT player, matc, COUNT(*) AS yellow_cards FROM v9ky_yellow WHERE player IN (
      SELECT id FROM v9ky_player WHERE team IN (
              SELECT id FROM v9ky_team WHERE turnir = '" . $turnir . "'
              )
      )
  GROUP BY player;"
);

// Получаем данные из БД. Статистика желтых карточек игроков.
$queryYellowRedCards = $db->Execute( 
  "SELECT player, matc, COUNT(*) AS yellow_red_cards FROM v9ky_yellow_red WHERE player IN (
      SELECT id FROM v9ky_player WHERE team IN (
              SELECT id FROM v9ky_team WHERE turnir = '" . $turnir . "'
              )
      )
  GROUP BY player;"
);

// Получаем данные из БД. Статистика красных карточек игроков.
$queryRedCards = $db->Execute( 
  "SELECT player, matc, COUNT(*) AS red_cards FROM v9ky_red WHERE player IN (
      SELECT id FROM v9ky_player WHERE team IN (
              SELECT id FROM v9ky_team WHERE turnir = '" . $turnir . "'
              )
      )
  GROUP BY player;"
);

// Получаем количество сыграных туров в турнире. Это нужно для отображения в таблице знака вопроса для несыграных матчей. 
$queryLastTur = $db->Execute(
  "SELECT tur as last_tur FROM `v9ky_match` WHERE `canseled`=1 and `turnir` = $turnir order by tur desc limit 1"
);

// Последний тур в турнире (в лиге).
$lastTur = intval($queryLastTur->fields[0]);

// Массив для лучшего игрока матча (иконка ведочка)
$nominationPlayerOfMatch = [];

// Заполняем массив лучшего игрока матча
while(!$queryBestPlayerOfMatch->EOF){

  foreach($queryBestPlayerOfMatch as $value){

    $player = $value['best_player'];
    $match = $value['id'];    
    
    // Заполняем массив. Проверки не нужно. Так как в одном матче лучший игрок может быть только один.
    $nominationPlayerOfMatch[$player][$match]['count_best_player_of_match'] = 1;
    
  }
  $queryBestPlayerOfMatch->MoveNext();
}

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


// Массив для статистики забитых голов в каждом матче отдельно
$playerMatchesGoals = array();

// Заполняем массив $queryGoals статистикой забитых голов. Массив [$player_id => $count_goals]
while(!$queryGoals->EOF){

  // $queryGoals->fields - массив содержит данные забитых голов. Одна запись = одному голу.
  // Создаем ассоциативный массив $playerGoals - [$player_id => (int) $count_goals]
  // Создаем ассоциативный массив $playerMatchesGoals - [ [$player_id] => [$match_id => (int) $count_goals] ]
  foreach ( $queryGoals as $key => $value ) {                
                    
    // Начало записи массива $playerMatchesGoals
    // Получаем player и matc
    $player = $value['player'];
    $match = $value['matc'];

    // Если такого player еще нет в итоговом массиве, создаем запись
    if (!isset($playerMatchesGoals[$player])) {
      $playerMatchesGoals[$player] = array();
    }

    // Если такой матч уже существует для игрока, увеличиваем его счетчик
    if (isset($playerMatchesGoals[$player][$match])) {
      $playerMatchesGoals[$player][$match]['count_goals'] ++;
    } else {
        // Если матч не существует, добавляем его с начальным значением 1
        $playerMatchesGoals[$player][$match]['count_goals'] = 1;
    }

  }

  $queryGoals->MoveNext();
}

$countAsist = [];

while(!$queryAsist->EOF){

  foreach ( $queryAsist as $value ){

    // Получаем player и matc
    $player = $value['player'];
    $match = $value['matc'];
    
    $countAsist[$player][$match]['count_asists'] = $value['count_asists'];
    
  }
  
  $queryAsist->MoveNext();
}

//массив для желтых карточек
$yellowCards = [];

// Цикл из запроса по желтым карточкам
while(!$queryYellowCards->EOF){


  foreach ( $queryYellowCards as $value ){

    // Получаем player и matc
    $player = $value['player'];
    $match = $value['matc'];
    
    $yellowCards[$player][$match]['yellow_cards'] = $value['yellow_cards'];
    
  }
  $queryYellowCards->MoveNext();
}

//массив для желто-красных карточек
$yellowRedCards = [];

// Цикл из запроса по желтым карточкам
while(!$queryYellowRedCards->EOF){


  foreach ( $queryYellowRedCards as $value ){

    // Получаем player и matc
    $player = $value['player'];
    $match = $value['matc'];
    
    $yellowRedCards[$player][$match]['yellow_red_cards'] = $value['yellow_red_cards'];
    
  }
  $queryYellowRedCards->MoveNext();
}


//массив для красных карточек
$redCards = [];

// Цикл из запроса по карточкам карточкам
while(!$queryRedCards->EOF){


  foreach ( $queryYellowCards as $value ){

    // Получаем player и matc
    $player = $value['player'];
    $match = $value['matc'];

    if(!isset($yellowCards[$player][$match])) {
      
      $yellowCards[$player][$match]['red_cards'] = $value['red_cards'];

    }
    
    
  }
  $queryRedCards->MoveNext();
}


// Получаем общий массив. Добавляем в массив с основной статистикой, статистику  забитых мячей
$allStaticPlayers = megreTwoMainArrays($allStaticPlayers, $playerMatchesGoals);

// Получаем общий массив. Добавляем в массив с основной статистикой, статистику  красных карточек
$allStaticPlayers = megreTwoMainArrays($allStaticPlayers, $countAsist, 'count_asists');

// Получаем общий массив. Добавляем в массив с основной статистикой, статистику  желтых карточек
$allStaticPlayers = megreTwoMainArrays($allStaticPlayers, $yellowCards, 'yellow_cards');

// Получаем общий массив. Добавляем в массив с основной статистикой, статистику  желтых карточек
$allStaticPlayers = megreTwoMainArrays($allStaticPlayers, $yellowRedCards, 'yellow_red_cards');

// Получаем общий массив. Добавляем в массив с основной статистикой, статистику  красных карточек
$allStaticPlayers = megreTwoMainArrays($allStaticPlayers, $queryRedCards, 'red_cards');

// Получаем общий массив. Добавляем в основной массив статистику лучший игрок матча.
$allStaticPlayers = megreTwoMainArrays($allStaticPlayers, $nominationPlayerOfMatch, 'count_best_player_of_match');

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

// dump_arr_first($dataAllPlayers);

// Отсортированный массив по рубрике Топ-Гравець
// $trainer = getTopPlayers( $allStaticPlayers, $dataAllPlayers, 'trainer', $lastTur );

// Отсортированный массив по рубрике Топ-Гравець
$topGravetc = getTopPlayers($allStaticPlayers, $dataAllPlayers, 'topgravetc', $lastTur);

// dump_arr($topGravetc);

// Отсортированный массив по рубрике Топ-Голкипер
$topGolkiper = getTopPlayers($allStaticPlayers, $dataAllPlayers, 'golkiper', $lastTur);


// Отсортированный массив по рубрике Топ-Бомбардир
$topBombardi = getTopPlayers($allStaticPlayers, $dataAllPlayers, 'count_goals', $lastTur);

// Отсортированный массив по рубрике Топ-Асистент
$topAsists = getTopPlayers($allStaticPlayers, $dataAllPlayers, 'count_asists', $lastTur);

// Отсортированный массив по рубрике Топ-Захистник
$topZhusnuk = getTopPlayers($allStaticPlayers, $dataAllPlayers, 'zahusnuk', $lastTur);

// Отсортированный массив по рубрике Топ-Дриблинг
$topDribling = getTopPlayers($allStaticPlayers, $dataAllPlayers, 'dribling', $lastTur);

// Отсортированный массив по рубрике Топ-Удар
$topUdar = getTopPlayers($allStaticPlayers, $dataAllPlayers, 'udar', $lastTur);

// Отсортированный массив по рубрике Топ-Пас
$topPas = getTopPlayers($allStaticPlayers, $dataAllPlayers, 'pas', $lastTur);

// Берем первый элемент массива - топ-игрок
$topTopGravetc = reset($topGravetc);

// Берем первый элемент массива - топ-игрок
$topTopGolkiper = reset($topGolkiper);

// Берем первый элемент массива - топ-игрок
$topTopBombardi = reset($topBombardi);

// Берем первый элемент массива - топ-игрок
$topTopAsists = reset($topAsists);

// Берем первый элемент массива - топ-игрок
$topTopZhusnuk = reset($topZhusnuk);

// Берем первый элемент массива - топ-игрок
$topTopDribling = reset($topDribling);

// Берем первый элемент массива - топ-игрок
$topTopUdar = reset($topUdar);

// Берем первый элемент массива - топ-игрок
$topTopPas = reset($topPas);



// Получаем идентификатор команды из адресной строки
if (isset($params['id'])) {
  $teamId = $params['id'];
}

// Сумма статистики команды
$totalGoalsByTeam = getTotalStaticByTeam($topBombardi, $teamId);
$totalAsistByTeam = getTotalStaticByTeam($topAsists, $teamId);
$totalMatchesByTeam = getTotalStaticByTeam($topAsists, $teamId, 'match_count');
$totalYellowByTeam = getTotalStaticByTeam($topAsists, $teamId, 'yellow_cards');
$totalYellowRedByTeam = getTotalStaticByTeam($topAsists, $teamId, 'yellow_red_cards');
$totalRedByTeam = getTotalStaticByTeam($topAsists, $teamId, 'red_cards');
$totalBestPlayerByTeam = getTotalStaticByTeam($topAsists, $teamId, 'count_best_player_of_match');

// Лучшие показатели в команде 
$bestGravetc = getBestPlayer($topGravetc, $teamId);
$bestGolkiper = getBestPlayer($topGolkiper, $teamId);
$bestBombardi = getBestPlayer($topBombardi, $teamId);
$bestAssist = getBestPlayer($topAsists, $teamId);
$bestZhusnuk = getBestPlayer($topZhusnuk, $teamId);
$bestDribling = getBestPlayer($topDribling, $teamId);
$bestUdar = getBestPlayer($topUdar, $teamId);
$bestPas = getBestPlayer($topPas, $teamId);

$requestUri = $_SERVER['REQUEST_URI'];

// Разделение пути по "/"
$partsUri = explode('/', $requestUri);

// Извлечение нужной части адресной строки
// $season = isset($parts[1]) ? $parts[1] : null;

// echo $teamId;
// $rreess = getBestPlayerOfTur($allStaticPlayers, $lastTur, $teamId);
// dump_arr($rreess);

// ?>

<section class="ratings">
    <h2 class="title">Рейтинги гравців ліги</h2>

    <div class="-ratings ratings__container">

        <div class="-wrap ratings__wrap containers">

          <div class="-slide ratings__player player-card">
            <div class="player-card">
              <div class="player-card__photo-container">
                <div class="player-card__left-icon">
                  <img src="/css/components/player-card/assets/images/star-icon.png" alt="star">
                  <span><?= $topTopGravetc['total_key']?></span>
                </div>
    
                <img class="player-card__right-icon" src="<?= $team_logo_path; ?>/<?= $topTopGravetc['team_photo']; ?>" alt="Логотип команди">
    
                <img class="player-card__photo" src="<?= $player_face_path; ?>/<?= $topTopGravetc['player_photo']; ?>" alt="yarmol">
              </div>
    
              <div class="player-card__role">Топ-гравець</div>
              <div class="player-card__club"><?= $topTopGravetc['team_name']?></div>
              <div class="player-card__name"><?= $topTopGravetc['first_name']?> <?= $topTopGravetc['last_name']?></div>
    
              <a href="<?= $site_url ?>/<?= $partsUri[1] ?>/top_gravetc" class="player-card__link scroll-link">
                <span>Таблиця</span>
                <img src="/css/components/player-card/assets/images/arrow-icon.svg" alt="arrow">
              </a>
            </div>
          </div>

          <div class="-slide ratings__player player-card">
            <div class="player-card">
              <div class="player-card__photo-container">
                <div class="player-card__left-icon">
                  <img src="/css/components/player-card/assets/images/gloves-icon.png" alt="star">
                  <span><?= $topTopGolkiper['total_key']?></span>
                </div>
    
                <img class="player-card__right-icon" src="<?=$team_logo_path?>/<?= $topTopGolkiper['team_photo']?>" alt="sparta">
    
                <img class="player-card__photo" src="<?=$player_face_path?>/<?= $topTopGolkiper['player_photo']?>" alt="yarmol">
              </div>
    
              <div class="player-card__role">Топ-голкіпер</div>
              <div class="player-card__club"><?= $topTopGolkiper['team_name']?></div>
              <div class="player-card__name"><?= $topTopGolkiper['first_name']?> <?= $topTopGolkiper['last_name']?></div>
    
              <a href="<?= $site_url ?>/<?= $partsUri[1] ?>/top_golkiper" class="player-card__link scroll-link">
                <span>Таблиця</span>
                <img src="/css/components/player-card/assets/images/arrow-icon.svg" alt="arrow">
              </a>
            </div>
          </div>         
          
          <div class="-slide ratings__player player-card">
            <div class="player-card">
              <div class="player-card__photo-container">
                <div class="player-card__left-icon">
                  <img src="/css/components/player-card/assets/images/football-icon.png" alt="star">
                  <span><?= $topTopBombardi['total_key']?></span>
                </div>
    
                <img class="player-card__right-icon" src="<?=$team_logo_path?>/<?= $topTopBombardi['team_photo']?>" alt="sparta">
    
                <img class="player-card__photo" src="<?=$player_face_path?>/<?= $topTopBombardi['player_photo']?>" alt="yarmol">
              </div>
              
              <div class="player-card__role">Топ-Бомбардир</div>
              <div class="player-card__club"><?= $topTopBombardi['team_name']?></div>
              <div class="player-card__name"><?= $topTopBombardi['first_name']?> <?= $topTopBombardi['last_name']?> </div>
    
              <a href="<?= $site_url ?>/<?= $partsUri[1] ?>/top_bombardir" class="player-card__link scroll-link">
                <span>Таблиця</span>
                <img src="/css/components/player-card/assets/images/arrow-icon.svg" alt="arrow">
              </a>
            </div>
          </div>

          <div class="-slide ratings__player player-card">
            <div class="player-card">
              <div class="player-card__photo-container">
                <div class="player-card__left-icon">
                  <img src="/css/components/player-card/assets/images/boots-icon.svg" alt="star">
                  <span><?= $topTopAsists['total_key']?></span>
                </div>
    
                <img class="player-card__right-icon" src="<?=$team_logo_path?>/<?= $topTopAsists['team_photo']?>" alt="sparta">
    
                <img class="player-card__photo" src="<?=$player_face_path?>/<?= $topTopAsists['player_photo']?>" alt="yarmol">
              </div>
              
              <div class="player-card__role">Топ-Асистент</div>
              <div class="player-card__club"><?= $topTopAsists['team_name']?></div>
              <div class="player-card__name"><?= $topTopAsists['first_name']?> <?= $topTopAsists['last_name']?> </div>
    
              <a href="<?= $site_url ?>/<?= $partsUri[1] ?>/top_asist" class="player-card__link scroll-link">
                <span>Таблиця</span>
                <img src="/css/components/player-card/assets/images/arrow-icon.svg" alt="arrow">
              </a>
            </div>
          </div>
                  
          <div class="-slide ratings__player player-card">
            <div class="player-card">
              <div class="player-card__photo-container">
                <div class="player-card__left-icon">
                  <img src="/css/components/player-card/assets/images/pitt-icon.svg" alt="star">
                  <span><?= $topTopZhusnuk['total_key'] ?></span>
                </div>
    
                <img class="player-card__right-icon" src="<?=$team_logo_path?>/<?= $topTopZhusnuk['team_photo'] ?>" alt="sparta">
    
                <img class="player-card__photo" src="<?=$player_face_path?>/<?= $topTopZhusnuk['player_photo'] ?>" alt="yarmol">
              </div>
    
              <div class="player-card__role">Топ-захисник</div>
              <div class="player-card__club"><?= $topTopZhusnuk['team_name'] ?></div>
              <div class="player-card__name"><?= $topTopZhusnuk['first_name'] ?> <?= $topTopZhusnuk['last_name'] ?></div>
    
              <a href="<?= $site_url ?>/<?= $partsUri[1] ?>/top_zahusnuk" class="player-card__link scroll-link">
                <span>Таблиця</span>
                <img src="/css/components/player-card/assets/images/arrow-icon.svg" alt="arrow">
              </a>
            </div>
          </div>

          <div class="-slide ratings__player player-card">
            <div class="player-card">
              <div class="player-card__photo-container">
                <div class="player-card__left-icon">
                  <img src="/css/components/player-card/assets/images/player-icon.svg" alt="star">
                  <span><?= $topTopDribling['total_key'] ?></span>
                </div>
    
                <img class="player-card__right-icon" src="<?=$team_logo_path?>/<?= $topTopDribling['team_photo'] ?>" alt="sparta">
    
                <img class="player-card__photo" src="<?=$player_face_path?>/<?= $topTopDribling['player_photo'] ?>" alt="yarmol">
              </div>
    
              <div class="player-card__role">Топ-дриблінг</div>
              <div class="player-card__club"><?= $topTopDribling['team_name'] ?></div>
              <div class="player-card__name"><?= $topTopDribling['first_name'] ?> <?= $topTopDribling['last_name'] ?></div>
    
              <a href="<?= $site_url ?>/<?= $partsUri[1] ?>/top_dribling" class="player-card__link scroll-link">
                <span>Таблиця</span>
                <img src="/css/components/player-card/assets/images/arrow-icon.svg" alt="arrow">
              </a>
            </div>
          </div>

          <div class="-slide ratings__player player-card">
            <div class="player-card">
              <div class="player-card__photo-container">
                <div class="player-card__left-icon">
                  <img src="/css/components/player-card/assets/images/rocket-ball-icon.png" alt="star">
                  <span><?= $topTopUdar['total_key'] ?></span>
                </div>
    
                <img class="player-card__right-icon" src="<?=$team_logo_path?>/<?= $topTopUdar['team_photo'] ?>" alt="sparta">
    
                <img class="player-card__photo" src="<?=$player_face_path?>/<?= $topTopUdar['player_photo'] ?>" alt="yarmol">
              </div>
    
              <div class="player-card__role">Топ-удар</div>
              <div class="player-card__club"><?= $topTopUdar['team_name'] ?></div>
              <div class="player-card__name"><?= $topTopUdar['first_name'] ?> <?= $topTopUdar['last_name'] ?></div>
    
              <a href="<?= $site_url ?>/<?= $partsUri[1] ?>/top_udar" class="player-card__link scroll-link">
                <span>Таблиця</span>
                <img src="/css/components/player-card/assets/images/arrow-icon.svg" alt="arrow">
              </a>
            </div>
          </div>

          <div class="-slide ratings__player player-card">
            <div class="player-card">
              <div class="player-card__photo-container">
                <div class="player-card__left-icon">
                  <img src="/css/components/player-card/assets/images/ball-icon.png" alt="star">
                  <span><?= $topTopPas['total_key'] ?></span>
                </div>
    
                <img class="player-card__right-icon" src="<?=$team_logo_path?>/<?= $topTopPas['team_photo'] ?>" alt="sparta">
    
                <img class="player-card__photo" src="<?=$player_face_path?>/<?= $topTopPas['player_photo'] ?>" alt="yarmol">
              </div>
    
              <div class="player-card__role">Топ-пас</div>
              <div class="player-card__club"><?= $topTopPas['team_name'] ?></div>
              <div class="player-card__name"><?= $topTopPas['first_name'] ?> <?= $topTopPas['last_name'] ?></div>
    
              <a href="<?= $site_url ?>/<?= $partsUri[1] ?>/top_pas" class="player-card__link scroll-link">
                <span>Таблиця</span>
                <img src="/css/components/player-card/assets/images/arrow-icon.svg" alt="arrow">
              </a>
            </div>
          </div>

        </div>
         <!-- If we need scrollbar -->
        <div class="-ratings-scrollbar"></div>

    </div>

</section>
