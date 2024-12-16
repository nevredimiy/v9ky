<?php

//проверка на вшивость
if (!defined('READFILE')) {exit('Wrong way to file');};


function dump_arr($data) {
  echo '<pre>' . print_r($data, 1) . '</pre>';
}

function dump_arr_first($data) {
  echo '<pre>' . print_r(array_slice($data,0,1,true), 1) . '</pre>';
}

/**
 * Функция дополняет первый массив. все массивы вида [ player_id => [ matches_id => [ name_static => value_static, ... all_statics ] ] ]
 * @param array - массив из БД со статистикой без учета абитых голов
 * @param array - массив и БД только с забитыми голами
 * @return array - массив со всей статистикой.
 */
function megreTwoMainArrays($firstArray, $secondArray, $column = 'count_goals'){

  foreach ($secondArray as $playerId => $matches) {
    if (!isset($firstArray[$playerId])) {
        // Если игрок отсутствует в первом массиве
        $firstArray[$playerId] = array();
    }

    foreach ($matches as $matchId => $stats) {
        if (isset($firstArray[$playerId][$matchId])) {
            // Если матч присутствует у игрока
            $firstArray[$playerId][$matchId][$column] = $stats[$column];
        } else {
            // Если матч отсутствует, создаем с $column = 0
            $firstArray[$playerId][$matchId] = array(
              $column => 0,
            );
        }
    }
  }

  // Проверка для игроков из первого массива, у которых отсутствует $column
  foreach ($firstArray as $playerId => &$matches) {
      foreach ($matches as $matchId => &$matchStats) {
          if (!isset($matchStats[$column])) {
              $matchStats[$column] = 0;
          }
      }
  }
  unset($matches, $matchStats);

  return $firstArray;

}

/**
 * Преобразует массив и сортирует по убыванию по критерию $keySort. Для всех рубрик Топ.
 * @param array - Статистика игроков. Массив вида [ player_id => [match_id => [ name_static => value ] ] ]
 * @param array - Данные игроков. Массив вида [ player_id => [ name_data => value ] ]
 * @param string - $keySort определяет одну и восьми номинаций.
 * @param int - для сортировки в таблице в случае одинаковых начений и количество матчей
 * @return array - 
 */
function getTopPlayers($allStaticPlayers, $dataAllPlayers, $keySort, $lastTur = 0){  

   // Преобразование массива
  $topPlayers = [];

  foreach ($allStaticPlayers as $playerId => $matches) {
      $matchCount = count($matches); // Количество матчей
      $totalKeySort = calculateArrayByColumn($keySort, $matches); // Сумма всех значений по ключу $keySort
      $countGoals = array_sum(array_column($matches, 'count_goals'));
      $countAsists = array_sum(array_column($matches, 'count_asists'));
      $countGolevoypas = array_sum(array_column($matches, 'golevoypas'));
      $countYellowCards = array_sum(array_column($matches, 'yellow_cards'));
      $countYellowRedCards = array_sum(array_column($matches, 'yellow_red_cards'));
      $countRed_cards = array_sum(array_column($matches, 'red_cards'));
      $matchIdKeys = array_keys($matches);
      $countBestPlayerOfMatch = array_sum( array_column( $matches, 'count_best_player_of_match' ) );
      
      
      // Инициализируем строку для таблицы. Для Бомбардиров и Асистентов
      if(!is_array($totalKeySort)) {
        
        $keySortPerMatch = $matchCount > 0 ? $totalKeySort / $matchCount : 0; // Среднее значений по ключу $keySort за матч
        
        $row = [
            'player_id' => $playerId,
            'match_count' => $matchCount,
            'total_key' => $totalKeySort,
            'key_per_match' => round($keySortPerMatch, 2), // Округляем до 2 знаков
            'match_ids' => implode(" ", $matchIdKeys),
 
            'last_name' => isset($dataAllPlayers[$playerId]['last_name']) ? $dataAllPlayers[$playerId]['last_name'] : '',
            'first_name' => isset($dataAllPlayers[$playerId]['first_name']) ? $dataAllPlayers[$playerId]['first_name'] : '',
            'player_photo' => isset($dataAllPlayers[$playerId]['player_photo']) ? $dataAllPlayers[$playerId]['player_photo'] : '',
            'team_photo' => isset($dataAllPlayers[$playerId]['team_photo']) ? $dataAllPlayers[$playerId]['team_photo'] : '',
            'team_name' => isset($dataAllPlayers[$playerId]['team_name']) ? $dataAllPlayers[$playerId]['team_name'] : '',
            'team_id' => isset($dataAllPlayers[$playerId]['team_id']) ? $dataAllPlayers[$playerId]['team_id'] : '',

            'count_goals' => $countGoals,
            'count_asists' => $countAsists,
            'golevoypas' => $countGolevoypas, 
            'yellow_cards' => $countYellowCards,
            'yellow_red_cards' => $countYellowRedCards,
            'red_cards' => $countRed_cards,
            'count_best_player_of_match' => $countBestPlayerOfMatch,
        ];

      } 

      if ($keySort == "dribling" && is_array($totalKeySort)) {

        $keySortPerMatch = $matchCount > 0 ? $totalKeySort['total_value'] / $matchCount : 0; // Среднее значений по ключу $keySort за матч
        
        $row = [
          'player_id' => $playerId,
          'match_count' => $matchCount,
          'total_key' => $totalKeySort['total_value'],
          'key_per_match' => round($keySortPerMatch, 2), // Округляем до 2 знаков
          'obvodkaplus' => $totalKeySort['obvodka_plus'],
          'obvodkaminus' => $totalKeySort['obvodka_minus'],

          'last_name' => isset($dataAllPlayers[$playerId]['last_name']) ? $dataAllPlayers[$playerId]['last_name'] : '',
          'first_name' => isset($dataAllPlayers[$playerId]['first_name']) ? $dataAllPlayers[$playerId]['first_name'] : '',
          'player_photo' => isset($dataAllPlayers[$playerId]['player_photo']) ? $dataAllPlayers[$playerId]['player_photo'] : '',
          'team_photo' => isset($dataAllPlayers[$playerId]['team_photo']) ? $dataAllPlayers[$playerId]['team_photo'] : '',
          'team_name' => isset($dataAllPlayers[$playerId]['team_name']) ? $dataAllPlayers[$playerId]['team_name'] : '',
          'team_id' => isset($dataAllPlayers[$playerId]['team_id']) ? $dataAllPlayers[$playerId]['team_id'] : '',
        ];

        // Добавляем значение для каждого матча
        foreach ($matches as $matchId => $stats) {
          if(isset($stats[ 'tur' ])) {
            $row["match_{$stats[ 'tur' ]}_key"] = $stats[ 'obvodkaplus' ] - $stats[ 'obvodkaminus' ];
          }

        }
        
      }

      if ($keySort == "udar" && is_array($totalKeySort)) {

        $keySortPerMatch = $matchCount > 0 ? $totalKeySort['total_value'] / $matchCount : 0; // Среднее значений по ключу $keySort за матч
        
        $row = [
          'player_id' => $playerId,
          'match_count' => $matchCount,
          'total_key' => $totalKeySort['total_value'],
          'key_per_match' => round($keySortPerMatch, 2), // Округляем до 2 знаков
          'udarplus' => $totalKeySort['udar_plus'],
          'udarminus' => $totalKeySort['udar_minus'],

          'last_name' => isset($dataAllPlayers[$playerId]['last_name']) ? $dataAllPlayers[$playerId]['last_name'] : '',
          'first_name' => isset($dataAllPlayers[$playerId]['first_name']) ? $dataAllPlayers[$playerId]['first_name'] : '',
          'player_photo' => isset($dataAllPlayers[$playerId]['player_photo']) ? $dataAllPlayers[$playerId]['player_photo'] : '',
          'team_photo' => isset($dataAllPlayers[$playerId]['team_photo']) ? $dataAllPlayers[$playerId]['team_photo'] : '',
          'team_name' => isset($dataAllPlayers[$playerId]['team_name']) ? $dataAllPlayers[$playerId]['team_name'] : '',
          'team_id' => isset($dataAllPlayers[$playerId]['team_id']) ? $dataAllPlayers[$playerId]['team_id'] : '',
        ];

        // Добавляем значение для каждого матча
        foreach ($matches as $matchId => $stats) {
          if( isset( $stats[ 'tur' ] ) ) {
            $row["match_{$stats[ 'tur' ]}_key"] = $stats[ 'vstvor' ] - $stats[ 'mimo' ];
          }
        }
        
      }

      if ($keySort == "pas" && is_array($totalKeySort)) {

        $keySortPerMatch = $matchCount > 0 ? $totalKeySort['total_value'] / $matchCount : 0; // Среднее значений по ключу $keySort за матч
        
        $row = [
          'player_id' => $playerId,
          'match_count' => $matchCount,
          'total_key' => $totalKeySort['total_value'],
          'key_per_match' => round($keySortPerMatch, 2), // Округляем до 2 знаков
          'obvodkaplus' => isset($totalKeySort['obvodka_plus']) ? $totalKeySort['obvodka_plus'] : 0,
          'obvodkaminus' => isset($totalKeySort['obvodka_minus']) ? $totalKeySort['obvodka_minus'] : 0,
          'zagostrennia' => isset($totalKeySort['zagostrennia']) ? $totalKeySort['zagostrennia'] : 0,
          'pasplus' => isset($totalKeySort['pasplus']) ? $totalKeySort['pasplus'] : 0,
          'pasminus' => isset($totalKeySort['pasminus']) ? $totalKeySort['pasminus'] : 0,

          'last_name' => isset($dataAllPlayers[$playerId]['last_name']) ? $dataAllPlayers[$playerId]['last_name'] : '',
          'first_name' => isset($dataAllPlayers[$playerId]['first_name']) ? $dataAllPlayers[$playerId]['first_name'] : '',
          'player_photo' => isset($dataAllPlayers[$playerId]['player_photo']) ? $dataAllPlayers[$playerId]['player_photo'] : '',
          'team_photo' => isset($dataAllPlayers[$playerId]['team_photo']) ? $dataAllPlayers[$playerId]['team_photo'] : '',
          'team_name' => isset($dataAllPlayers[$playerId]['team_name']) ? $dataAllPlayers[$playerId]['team_name'] : '',
          'team_id' => isset($dataAllPlayers[$playerId]['team_id']) ? $dataAllPlayers[$playerId]['team_id'] : '',
        ];
        
        // Добавляем значение для каждого матча
        foreach ($matches as $matchId => $stats) {
          if(isset($stats[ 'tur' ])){
            $row["match_{$stats[ 'tur' ]}_key"] = ($stats[ 'zagostrennia' ] * 5 + $stats[ 'pasplus' ]) - $stats[ 'pasminus' ] * 3;
          }
        }
        
      }

      if ($keySort == "golkiper" && is_array($totalKeySort)) {

        $keySortPerMatch = $matchCount > 0 ? $totalKeySort['total_value'] / $matchCount : 0; // Среднее значений по ключу $keySort за матч
        
        $row = [
          'player_id' => $playerId,
          'match_count' => $matchCount,
          'total_key' => $totalKeySort['total_value'],
          'key_per_match' => round($keySortPerMatch, 2), // Округляем до 2 знаков
          'seyv' => $totalKeySort['seyv'],
          'seyvmin' => $totalKeySort['seyvmin'],
          'team_id' => isset($dataAllPlayers[$playerId]['team_id']) ? $dataAllPlayers[$playerId]['team_id'] : '',

          'last_name' => isset($dataAllPlayers[$playerId]['last_name']) ? $dataAllPlayers[$playerId]['last_name'] : '',
          'first_name' => isset($dataAllPlayers[$playerId]['first_name']) ? $dataAllPlayers[$playerId]['first_name'] : '',
          'player_photo' => isset($dataAllPlayers[$playerId]['player_photo']) ? $dataAllPlayers[$playerId]['player_photo'] : '',
          'team_photo' => isset($dataAllPlayers[$playerId]['team_photo']) ? $dataAllPlayers[$playerId]['team_photo'] : '',
          'team_name' => isset($dataAllPlayers[$playerId]['team_name']) ? $dataAllPlayers[$playerId]['team_name'] : '',
        ];

        // Добавляем значение для каждого матча
        
        foreach ($matches as $matchId => $stats) {
          $seyv = isset($stats['seyv']) ? $stats['seyv'] : 0;
          $seyvmin = isset($stats['seyvmin']) ? $stats['seyvmin'] : 0;
          $denominator = $seyv + $seyvmin; // Знаменатель
          
          if(isset($stats[ 'tur' ])) {
            $row["match_{$stats[ 'tur' ]}_key"] = $denominator == 0 
              ? 0 
              : round(( 100 / $denominator ) * $stats[ 'pasminus' ], 1);
          }
        }
        
      }

      if( $keySort == 'count_goals' || $keySort == 'golevoypas' || $keySort == 'count_asists'){
        
        // Добавляем значение для каждого матча
        foreach ($matches as $matchId => $stats) {
          if(isset($stats[ 'tur' ] )) {
            $row["match_{$stats[ 'tur' ]}_key"] = $stats[ $keySort ];         
          }
        }

      }

      if( $keySort == 'zahusnuk') {

        // Добавляем значение для каждого матча
        foreach ($matches as $matchId => $stats) {
          if(isset($stats[ 'tur' ] )) {
            $row["match_{$stats[ 'tur' ]}_key"] = $stats[ 'otbor' ] + $stats[ 'blok' ];
          }
        }

      }


      if( $keySort == 'topgravetc' ) {
        
        // Добавляем значение для каждого матча
        $i = 1;
        foreach ($matches as $matchId => $stats) {
          if(isset($stats['tur'])){
            $row["match_{$stats[ 'tur' ]}_key"] = $stats['count_goals'] * 15 + $stats['golevoypas'] * 10 +  $stats['zagostrennia'] * 10 +
            + $stats['pasplus'] * 3 - $stats['pasminus'] * 3 - $stats['vtrata'] * 3 +
            + $stats['vstvor'] * 7 - $stats['mimo'] * 4 +  $stats['obvodkaplus'] * 5 -
            + $stats['obvodkaminus'] * 3 + $stats['otbor'] * 8 -  $stats['otbormin'] * 5 +
            + $stats['blok'] * 4 + $stats['seyv'] * 15 - $stats['seyvmin'] * 7;
          }

            $i++;
        }

      }

      if( $keySort == 'trainer' ){

        $row = [
          'player_id' => $playerId,
          'last_name' => isset($dataAllPlayers[$playerId]['last_name']) ? $dataAllPlayers[$playerId]['last_name'] : '',
          'first_name' => isset($dataAllPlayers[$playerId]['first_name']) ? $dataAllPlayers[$playerId]['first_name'] : '',
          'player_photo' => isset($dataAllPlayers[$playerId]['player_photo']) ? $dataAllPlayers[$playerId]['player_photo'] : '',
          'team_photo' => isset($dataAllPlayers[$playerId]['team_photo']) ? $dataAllPlayers[$playerId]['team_photo'] : '',
          'team_name' => isset($dataAllPlayers[$playerId]['team_name']) ? $dataAllPlayers[$playerId]['team_name'] : '',
          'team_id' => isset($dataAllPlayers[$playerId]['team_id']) ? $dataAllPlayers[$playerId]['team_id'] : '',
          'amplua' => isset($dataAllPlayers[$playerId]['amplua']) ? $dataAllPlayers[$playerId]['amplua'] : '',
        ];

      }
      
      $topPlayers[] = $row;
  }


  // Проверка для Тренера
  
    // Сортируем игроков
    usort($topPlayers, function ($a, $b) use ($lastTur) {
      // 1. Сортировка по (total_key)
      if ($a['total_key'] != $b['total_key']) {
          return ($b['total_key'] > $a['total_key']) ? 1 : -1; // По убыванию
      }

      // 2. Сортировка по «Матчів» (count_matches)
      if ($a['match_count'] != $b['match_count']) {
          return ($b['match_count'] > $a['match_count']) ? 1 : -1; // По убыванию
      }
      // 3. Сортировка по последнему сыгранному матчу (total_3_match)
      if(isset($a["match_{$lastTur}_key"]) && isset($b["match_{$lastTur}_key"])) {

        if ($a["match_{$lastTur}_key"] != $b["match_{$lastTur}_key"]) {
            return ($b["match_{$lastTur}_key"] > $a["match_{$lastTur}_key"]) ? 1 : -1; // По убыванию
        }

      }

      // Если все значения равны, оставить текущий порядок
      return 0;
    });

    // Присваиваем позиции
    $rank = 1; // Начальный порядковый номер
    foreach ($topPlayers as $index => &$player) {

      // если в последнем туре не играли оба савниваемых игрока
      if( isset( $topPlayers[$index - 1]["match_{$lastTur}_key"] ) && isset( $player["match_{$lastTur}_key"] ) ) {
       
        // Если это не первый игрок и текущий игрок имеет те же значения, что и предыдущий
        if (
            $index > 0 &&
            $topPlayers[$index - 1]['total_key'] === $player['total_key'] &&
            $topPlayers[$index - 1]['match_count'] === $player['match_count'] &&
            $topPlayers[$index - 1]["match_{$lastTur}_key"] === $player["match_{$lastTur}_key"]
        ) {
            $player['rank'] = isset( $topPlayers[$index - 1]['rank'] ) ? $topPlayers[$index - 1]['rank'] : $rank; // Присваиваем тот же ранг
        } else {
            $player['rank'] = $rank; // Новый ранг
        }

      } else {
        // Если это не первый игрок и текущий игрок имеет те же значения, что и предыдущий
        if (
          $index > 0 &&
          $topPlayers[$index - 1]['total_key'] === $player['total_key'] &&
          $topPlayers[$index - 1]['match_count'] === $player['match_count']
        ) {
            $player['rank'] = isset( $topPlayers[$index - 1]['rank'] ) ? $topPlayers[$index - 1]['rank'] : $rank; // Присваиваем тот же ранг
        } else {
            $player['rank'] = $rank; // Новый ранг
        }
      }
        $rank++; // Увеличиваем счетчик
    }

  

  return $topPlayers;
}

/**
 * Расчитывает рейтинг игрока по заданному критерию. Для рубрик Топ-Бомбардир, Топ-Ассистент и т.д.
 * @param string
 * @param array
 * @return integer|array
 */
function calculateArrayByColumn($column, $array) {

  if (empty($array)) {
    throw new Exception("The input array is empty");
  }

  $totalValue = 0;
  if ( $column == 'count_goals' || $column == 'golevoypas' || $column == 'count_asists' ){
    $totalValue = array_sum(array_column($array, $column));
  }

  if( $column == 'zahusnuk' ) {
    $totalOtbor = array_sum(array_column($array, 'otbor'));
    $totalBlok = array_sum(array_column($array, 'blok'));
    $totalValue = $totalOtbor + $totalBlok;
  }

  if( $column == "dribling") {
    $totalObvodkaplus = array_sum(array_column($array, 'obvodkaplus'));
    $totalObvodkaminus = array_sum(array_column($array, 'obvodkaminus'));
    $totalValue = $totalObvodkaplus - $totalObvodkaminus;
    $totalValue = ['total_value' => $totalValue, 'obvodka_plus' => $totalObvodkaplus, 'obvodka_minus' => $totalObvodkaminus];
  }

  if( $column == "udar") {
    $totalObvodkaplus = array_sum(array_column($array, 'vstvor'));
    $totalObvodkaminus = array_sum(array_column($array, 'mimo'));
    $totalValue = $totalObvodkaplus - $totalObvodkaminus;
    $totalValue = ['total_value' => $totalValue, 'udar_plus' => $totalObvodkaplus, 'udar_minus' => $totalObvodkaminus];
  }

  if( $column == "pas") {
    $totalZagostrennia = array_sum(array_column($array, 'zagostrennia'));
    $totalPasplus = array_sum(array_column($array, 'pasplus'));
    $totalPasminus = array_sum(array_column($array, 'pasminus'));
    $totalValue = ( $totalZagostrennia * 5 + $totalPasplus ) - $totalPasminus * 3 ;
    $totalValue = ['total_value' => $totalValue, 'zagostrennia' => $totalZagostrennia, 'pasplus' => $totalPasplus, 'pasminus' => $totalPasminus];
  }

  if( $column == "golkiper") {
    $totalSeyv = array_sum(array_column($array, 'seyv'));
    $totalSeyvmin = array_sum(array_column($array, 'seyvmin'));
    $totalValue = $totalSeyv + $totalSeyvmin == 0 ? 0 : 100 / ( $totalSeyv + $totalSeyvmin ) * $totalSeyv ;
    $totalValue = ['total_value' => round($totalValue, 1), 'seyv' => $totalSeyv, 'seyvmin' => $totalSeyvmin];
  }

  if( $column == "topgravetc") {
    $totalGoals = array_sum(array_column($array, 'count_goals'));
    $totalGolevoypas = array_sum(array_column($array, 'golevoypas'));
    $totalZagostrennia = array_sum(array_column($array, 'zagostrennia'));
    $totalPasplus = array_sum(array_column($array, 'pasplus'));
    $totalPasminus= array_sum(array_column($array, 'pasminus'));
    $totalVtrata = array_sum(array_column($array, 'vtrata'));
    $totalVstvor = array_sum(array_column($array, 'vstvor'));
    $totalMimo = array_sum(array_column($array, 'mimo'));
    $totalObvodkaplus = array_sum(array_column($array, 'obvodkaplus'));
    $totalObvodkaminus = array_sum(array_column($array, 'obvodkaminus'));
    $totalOtbor = array_sum(array_column($array, 'otbor'));
    $totalOtbormin = array_sum(array_column($array, 'otbormin'));
    $totalBlok = array_sum(array_column($array, 'blok'));
    $totalSeyv = array_sum(array_column($array, 'seyv'));
    $totalSeyvmin = array_sum(array_column($array, 'seyvmin'));

    $totalValue = $totalGoals * 15 + $totalGolevoypas * 10 +  $totalZagostrennia * 10 +
    + $totalPasplus * 3 - $totalPasminus * 3 - $totalVtrata * 3 +
    + $totalVstvor * 7 - $totalMimo * 4 +  $totalObvodkaplus * 5 -
    + $totalObvodkaminus * 3 + $totalOtbor * 8 -  $totalOtbormin * 5 +
    + $totalBlok * 4 + $totalSeyv * 15 - $totalSeyv * 7;
    $totalValue = $totalValue;
  }
  
  return $totalValue;
}

/**
 * Находит сумму статистики всех игроков в команде по одному показателю. Например, сума забитых голов в команде.
 * @param array - массив статистики по заданному показателю. Реультат функции getTopPlayers() - Например, TopBombardir, можно любой топ
 * @param int - идентификато команды
 * @return int 
 */
function getTotalStaticByTeam($staticPlayers, $teamId, $column = 'total_key'){

  $countStatic = 0;
  foreach ($staticPlayers as $player) {
    if ($player['team_id'] === $teamId){
      $countStatic += $player[$column];
    }
  }
  return $countStatic;
}

/**
 * находит лучший показатель команды/игрока в рейтинге. Место в рейтинге. Например лучший бомбардир в команде занимает 5 место. Все остальные ниже. Выводится цыфра 5
 * @param array - массив статистики по заданому показателю. Реультат функции getTopPlayers(). Например, TopBombardir
 * @param int - идентификато команды или игрока
 * @param string - ключ элемента искомого поля. Если это колманда - team_id, если это игрок - player_id
 * @return int 
 */
function getBestPlayer($staticPlayers, $id, $column = 'team_id') {
  
  $position = 1000; // сппецально задано большое число для поииска найменьшего

  // Фильтрация массива для получения позиций
  $filteredKeys = array_keys(array_column($staticPlayers, $column), $id);


  // Проверка наличия элемента
  if (!empty($filteredKeys)) {
      // $position = array_search($filteredKeys[0], array_keys($staticPlayers));
      $position = $filteredKeys[0] + 1;
  } 

  if ($column == 'player_id' && $position == 1000){
    $position = 0;
  }

  return $position;

}


/**
 * определяет лучший показатель игрока и 8 номинаций. 
 * @param int
 * @return int
 */
function getCategoryPlayerBest ($player_id) {
  // Отсортированный массив по рубрике Топ-Бомбардир
  $topBombardi = getTopPlayers($allStaticPlayers, $dataAllPlayers, 'count_goals');

  // Отсортированный массив по рубрике Топ-Асистент
  $topAsists = getTopPlayers($allStaticPlayers, $dataAllPlayers, 'golevoypas');

  // Отсортированный массив по рубрике Топ-Захистник
  $topZhusnuk = getTopPlayers($allStaticPlayers, $dataAllPlayers, 'zahusnuk');

  // Отсортированный массив по рубрике Топ-Дриблинг
  $topDribling = getTopPlayers($allStaticPlayers, $dataAllPlayers, 'dribling');

  // Отсортированный массив по рубрике Топ-Дриблинг
  $topUdar = getTopPlayers($allStaticPlayers, $dataAllPlayers, 'udar');

  // Отсортированный массив по рубрике Топ-Дриблинг
  $topPas = getTopPlayers($allStaticPlayers, $dataAllPlayers, 'pas');

  // Отсортированный массив по рубрике Топ-Дриблинг
  $topGolkiper = getTopPlayers($allStaticPlayers, $dataAllPlayers, 'golkiper');

  // Отсортированный массив по рубрике Топ-Дриблинг
  $topGravetc = getTopPlayers($allStaticPlayers, $dataAllPlayers, 'topgravetc');

  // Лучшие в команде 
  $bestGravetc = getBestPlayer($topGravetc, $player_id);
  $bestGolkiper = getBestPlayer($topGolkiper, $player_id);
  $bestBombardi = getBestPlayer($topBombardi, $player_id);
  $bestAssist = getBestPlayer($topAsists, $player_id);
  $bestZhusnuk = getBestPlayer($topZhusnuk, $player_id);
  $bestDribling = getBestPlayer($topDribling, $player_id);
  $bestUdar = getBestPlayer($topUdar, $player_id);
  $bestPas = getBestPlayer($topPas, $player_id);

  $arr = [$bestGravetc, $bestGolkiper, $bestBombardi, $bestAssist, $bestZhusnuk, $bestDribling, $bestUdar, $bestPas ];

  

  // Найти наименьшее число
  $minValue = min($arr);

  // Найти индекс наименьшего числа
  $minIndex = array_search($minValue, $arr);

  return $minIndex;

}

/**
 * Получение индивидуальной статистики игрока по всем матчам турнира (лиги)
 * @param array Статистика игроков. Массив вида [ player_id => [match_id => [ name_static => value ] ] ]
 * @param string - идентификатор игрока
 * @return array - Массив со всей статистикой игрока
 */
function getIndStaticPlayer($allStaticPlayers, $player_id){

  $indStatPlayer = [];

  if(isset($allStaticPlayers[$player_id]) && is_array($allStaticPlayers[$player_id])) {
    
    $coutnGoals = 0;
    // Иконка зведочка
    $countNominationPlayerOfMatch = 0;
    // Иконка футболка
    $countInTour = 0;
    // Иконка бутса
    $countAsists = 0;
    // Иконка поле
    $countMatches = 0;
    $yellowCards = 0;
    $yellowRedCards = 0;
    $redCards = 0;

    // Точность удара
    $accuracyOfKicking = 0;
    //Точность паса
    $accuracyOfPassing = 0;
    //Удачные обводки
    $accuracyOfDribbles = 0;
    //Количество обострений за матч
    $countOfAggravations = 0;
    //Количество отборов
    $accuracyOfTackles = 0;

    // dump_arr($allStaticPlayers[$player_id]);
    foreach($allStaticPlayers[$player_id] as $matches => $stats){      

        // Индивид. статистика игрока. Для карточки игрока где иконки, мяч, звездочка и т.д.
        // Иконка мяч
        $countGoals += $stats['count_goals'];
        // Иконка зведочка
        $countBestPlayerOfMatch += isset($stats['count_best_player_of_match']) ? $stats['count_best_player_of_match'] : 0;

        // Иконка футболка
        $countInTour += isset($stats['count_in_tour']) ? $stats['count_in_tour'] : 0;
        // Иконка бутса
        $countAsists += $stats['count_asists'];
        // иконка поле
        $countMatches++;
        $yellowCards += $stats['yellow_cards'];
        $yellowRedCards += $stats['yellow_red_cards'];
        $redCards += $stats['red_cards'];
        
        // Точность удара
        if ( isset($stats['vstvor']) && isset($stats['mimo'])) {
          $vstvor += $stats['vstvor'];
          $mimo += $stats['mimo'];
          $accuracyOfKicking = $vstvor + $mimo == 0 ? 0 : ( 100 / ( $vstvor + $mimo ) ) * $vstvor ;
        }

        //Точность паса
        if ( isset($stats['pasplus']) && isset($stats['pasminus'])) {
          $pasplus += $stats['pasplus'];
          $pasminus += $stats['pasminus'];
          $accuracyOfPassing = $pasplus +  $pasminus == 0 ? 0 : ( 100 / ( $pasplus + $pasminus ) ) * $pasplus ;
        }

        //Удачные обводки
        if ( isset($stats['obvodkaplus']) && isset($stats['obvodkaminus'])) {
          $obvodkaplus += $stats['obvodkaplus'];
          $obvodkaminus += $stats['obvodkaminus'];
          $accuracyOfDribbles = $obvodkaplus +  $obvodkaminus == 0 ? 0 : ( 100 / ( $obvodkaplus + $obvodkaminus ) ) * $obvodkaplus ;
        }

        //Количество обострений за матч
        if ( isset($stats['zagostrennia'])) {
          $zagostrennia += $stats['zagostrennia'];
          $countOfAggravations = number_format( round( $zagostrennia / $countMatches ), 1 );
        }

        //Количество отборов
        if ( isset($stats['otbor']) && isset($stats['blok'])) {
          $otbor += $stats['otbor'];
          $blok += $stats['blok'];
          $accuracyOfTackles = number_format( round( ( $otbor +  $blok ) / $countMatches, 1 ), 1  );
        }
      }
      
    } 

    $indStatPlayer = [
      'count_goals' => $countGoals != '' ? $countGoals : 0,
      'count_best_player_of_match' => $countBestPlayerOfMatch != '' ? $countBestPlayerOfMatch : 0,
      'count_in_tour' => $countInTour != '' ? $countInTour : 0,
      'count_asists' => $countAsists != '' ? $countAsists : 0,
      'count_matches' => $countMatches != '' ? $countMatches : 0,
      'yellow_cards' => $yellowCards != '' ? $yellowCards : 0,
      'yellow_red_cards' => $yellowRedCards != '' ? $yellowRedCards : 0,
      'red_cards' => $redCards != '' ? $redCards : 0,
      'accuracy_of_kicking' => round($accuracyOfKicking, 1, PHP_ROUND_HALF_UP),
      'accuracy_of_passing' => round($accuracyOfPassing, 1, PHP_ROUND_HALF_UP),
      'accuracy_of_dribbles' => round($accuracyOfDribbles, 1, PHP_ROUND_HALF_UP),
      'count_of_aggravations' => $countOfAggravations != '' ? $countOfAggravations : 0,
      'accuracy_of_tackles' => $accuracyOfTackles != '' ? $accuracyOfTackles : 0,
    ];

  return $indStatPlayer;

}

/**
 * Возвращает статистику игрока по матчу в одной из восьми рубрик. Для отображения таблицы Топ-игроков.
 * @param integer
 * @param integer
 * @param integer|string
 * @param string
 * @return string
 */

function checkingCurrentTur( $indexIteration, $lastTur=0, $totalValue=0, $sufix='' ){
  // если матч состоялся
  if($indexIteration <= $lastTur) {
    // возвращаем значение или пропуск
    return $totalValue ? $totalValue . $sufix : "-";
    // если матча еще не было
  } else {
    return '?';
  }
  
}

/**
 * 
 */

 function getBestPlayerOfTur($allStaticPlayers, $lastTur, $teamId){

  $countInTour = [];
  $playerOfTur = [];

  for( $i = 1; $i <= $lastTur; $i++ ){
    foreach ( $allStaticPlayers as $player_id => $match ){
      foreach ( $match as $match_id => $value ){
        if ( $value['tur'] == $i) {
          $countInTour[] = $value;
          
        }
      }
    }
  
    usort($countInTour, function ($a, $b) {
      // 1. Сортировка по (total_key)
      if ($a['count_goals'] != $b['count_goals']) {
          return ($b['count_goals'] > $a['count_goals']) ? 1 : -1; // По убыванию
      }
  
      // Если все значения равны, оставить текущий порядок
      return 0;
    });
    $playerOfTur[] = $countInTour[0];
  }
  
  $arrK = 1000;
  foreach($playerOfTur as $key => $value){
    if($value['team'] == $teamId){
      $arrK = $key;
    }
  }
 
  if($arrK != 1000) {
    return $playerOfTur[$arrK];
  }
  

  return $playerOfTur;

 }

 /**
  * 
  */
  function date_translate($date){

    $translate = array(
      "Monday" => "Понеділок",
      "Mon" => "Пн",
      "Tuesday" => "Вівторок",
      "Tue" => "Вт",
      "Wednesday" => "Середа",
      "Wed" => "Ср",
      "Thursday" => "Четвер",
      "Thu" => "Чт",
      "Friday" => "П'ятниця",
      "Fri" => "Пт",
      "Saturday" => "Субота",
      "Sat" => "Сб",
      "Sunday" => "Неділя",
      "Sun" => "Нд",
      "January" => "Січня",
      "Jan" => "Січ",
      "February" => "Лютого",
      "Feb" => "Лют",
      "March" => "Березня",
      "Mar" => "Бер",
      "April" => "Квітня",
      "Apr" => "Кві",
      "May" => "Травня",
      "May" => "Травня",
      "June" => "Червня",
      "Jun" => "Чер",
      "July" => "Липня",
      "Jul" => "Лип",
      "August" => "Серпня",
      "Aug" => "Сер",
      "September" => "Вересня",
      "Sep" => "Вер",
      "October" => "Жовтня",
      "Oct" => "Жов",
      "November" => "Листопада",
      "Nov" => "Лис",
      "December" => "Грудня",
      "Dec" => "Гру",
      "st" => "ое",
      "nd" => "ое",
      "rd" => "е",
      "th" => "ое"
      );

  }

  /**
   * 
   */
  function getCountMatchesOfTurnir($allStaticPlayers, $teamId) {

    $countMatches = 0;
    foreach($allStaticPlayers as $matches){
      foreach ($matches as $staticMatch) {
        if($staticMatch['team'] == $teamId) {
          if($staticMatch['tur'] > $countMatches){
            $countMatches = $staticMatch['tur'];
          }
        }
      }
    }
    return $countMatches;
  }