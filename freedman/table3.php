<?php
$turnir_id = 523;

// SQL-запрос для получения данных
$sql = "SELECT 
    t1.id AS team1_id,
    t1.name AS team1_name,
    t1.pict AS team1_logo,
    t2.id AS team2_id,
    t2.name AS team2_name,
    t2.pict AS team2_logo,
    m.gols1 AS team1_goals,
    m.gols2 AS team2_goals
FROM 
    v9ky_match m
JOIN 
    v9ky_team t1 ON m.team1 = t1.id
JOIN 
    v9ky_team t2 ON m.team2 = t2.id
WHERE 
    m.turnir = :turnir AND canseled = 1
ORDER BY 
    t1.name ASC, t2.name ASC";

$stmt = $mysqli->prepare($sql);
$stmt->bindParam(':turnir', $turnir_id, PDO::PARAM_INT);
$stmt->execute();
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (!$result) {
    die('Ошибка выполнения запроса: ' . $mysqli->error);
}

$matches = [];
$teams = [];

// Преобразуем результат запроса в массив
$rows = $result;

// Обработка результата через foreach
foreach ($rows as $row) {
    $matches[$row['team1_id']][$row['team2_id']] = $row['team1_goals'] . ':' . $row['team2_goals'];
    $matches[$row['team2_id']][$row['team1_id']] = $row['team2_goals'] . ':' . $row['team1_goals'];

    $teams[$row['team1_id']] = [
        'name' => $row['team1_name'],
        'logo' => $row['team1_logo'],
    ];
    $teams[$row['team2_id']] = [
        'name' => $row['team2_name'],
        'logo' => $row['team2_logo'],
    ];
}

// Инициализация турнирной таблицы
$stats = [];
foreach ($teams as $team_id => $team_data) {
    $stats[$team_id] = [
        'name' => $team_data['name'],
        'logo' => $team_data['logo'],
        'games' => 0,
        'wins' => 0,
        'draws' => 0,
        'losses' => 0,
        'points' => 0,
    ];
}

// Подсчет статистики
foreach ($matches as $team1_id => $opponents) {
    foreach ($opponents as $team2_id => $score) {
        // Проверяем, чтобы не было двойного подсчета
        if (!isset($processed_matches["$team1_id-$team2_id"]) && !isset($processed_matches["$team2_id-$team1_id"])) {
            list($goals1, $goals2) = explode(':', $score);

            // Увеличиваем количество игр
            $stats[$team1_id]['games']++;
            $stats[$team2_id]['games']++;

            // Обновляем статистику побед, ничьих, поражений и очков
            if ($goals1 > $goals2) {
                $stats[$team1_id]['wins']++;
                $stats[$team2_id]['losses']++;
                $stats[$team1_id]['points'] += 3;
            } elseif ($goals1 < $goals2) {
                $stats[$team2_id]['wins']++;
                $stats[$team1_id]['losses']++;
                $stats[$team2_id]['points'] += 3;
            } else {
                $stats[$team1_id]['draws']++;
                $stats[$team2_id]['draws']++;
                $stats[$team1_id]['points'] += 1;
                $stats[$team2_id]['points'] += 1;
            }

            // Отмечаем матч как обработанный
            $processed_matches["$team1_id-$team2_id"] = true;
        }
    }
}

// Сортировка по очкам
uasort($stats, function ($a, $b) {
    return $b['points'] - $a['points'];
});


?>

<section class="table-league">
      <h2 class="table-league__title title title--inverse ">
        <span>Турнірна таблиця</span>
        <span>Прем’єр-ліги</span>
      </h2>

      <div class="swiper swiper-table swiper-initialized swiper-horizontal swiper-backface-hidden">
        <div class="swiper-wrapper" id="swiper-wrapper-fc59b90f88ba8bbe" aria-live="polite" style="transform: translate3d(0px, 0px, 0px);">
          <div class="swiper-slide swiper-slide--table swiper-slide-active" role="group" aria-label="1 / 1" style="margin-right: 5px;">
            <table class="table-league__table">
                <tbody>
                    <tr>
                        <th><span>М</span></th>
                        <th><span class="cell--team-logo"></span></th>
                        <th><span class="cell--team">Команда</span></th>
                        <?php for( $i = 1; $i <= count($stats); $i++ ): ?>
                            <th><span class="cell--score"><?= $i ?></span></th>
                        <?php endfor ?>
                        <th><span class="cell cell--games">І</span></th>
                        <th><span class="cell cell--win">В</span></th>
                        <th><span class="cell cell--draw">Н</span></th>
                        <th><span class="cell cell--defeat">П</span></th>
                        <th><span class="cell cell--total">О</span></th>
                    </tr>
                    <?php $position = 1; ?>
                    <?php foreach($stats as $team_id => $stat): ?>
                        <tr>
                            <td><span class="cell cell--gold"><?= $position?></span></td>
                            <td><img width="15" height="15" class="cell--team-logo" src="<?= $team_logo_path ?>/<?= $stat['logo']?>"></td>
                            <td><span class="cell--team"><?= $stat['name']?></span></td>
                            <!-- <td><span class="cell--score cell--own"></span></td> -->

                            <?php foreach ($stats as $key => $value) : ?>
                                
                                    <?php if($key === $team_id) :?>  
                                        <td><span class="cell--score cell--own"></span></td> 
                                    <?php else :?>
                                        
                                        <td><span class="cell--score"><?= isset($matches[$team_id][$key]) ? $matches[$team_id][$key] : '-' ?></span></td>
                                    <?php endif ?>
                                
                            <?php endforeach ?>
                            <td><span class="cell cell--games"><?= $stat['games']?></span></td>
                            <td><span class="cell cell--win"><?= $stat['wins']?></span></td>
                            <td><span class="cell cell--draw"><?= $stat['draws']?></span></td>
                            <td><span class="cell cell--defeat"><?= $stat['losses']?></span></td>
                            <td><span class="cell cell--total"><?= $stat['points']?></span></td>
                        </tr>
                    <?php $position++ ?>
                    <?php endforeach ?>
                </tbody>
            </table>
          </div>
        </div>
        
        <div class="swiper-scrollbar swiper-scrollbar-horizontal swiper-scrollbar-lock" style="display: none;"><div class="swiper-scrollbar-drag" style="transform: translate3d(0px, 0px, 0px); width: 70px;"></div></div>
      <span class="swiper-notification" aria-live="assertive" aria-atomic="true"></span></div>
    </section>
