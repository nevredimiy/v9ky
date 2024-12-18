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
        list($goals1, $goals2) = explode(':', $score);

        $stats[$team1_id]['games']++;
        $stats[$team2_id]['games']++;

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
    }
}

// Сортировка по очкам
uasort($stats, function ($a, $b) {
    return $b['points'] - $a['points'];
});

// Отображение таблицы
echo '<table class="table-league__table"><tbody>';

// Заголовок таблицы
echo '<tr>';
echo '<th><span>М</span></th>';
echo '<th><span class="cell--team-logo"></span></th>';
echo '<th><span class="cell--team">Команда</span></th>';
$ii = 1;
foreach ($teams as $team_id => $team_data) {
    echo '<th><span class="cell--score">' . $ii . $team_data['name']  . '</span></th>';
    $ii++;
}
echo '<th><span class="cell cell--games">І</span></th>';
echo '<th><span class="cell cell--win">В</span></th>';
echo '<th><span class="cell cell--draw">Н</span></th>';
echo '<th><span class="cell cell--defeat">П</span></th>';
echo '<th><span class="cell cell--total">О</span></th>';
echo '</tr>';

// Строки таблицы
$position = 1;
foreach ($stats as $team_id => $data) {
    $position_class = $position == 1 ? 'cell--gold' : ($position == 2 ? 'cell--silver' : ($position == 3 ? 'cell--bronze' : ($position > count($stats) - 2 ? 'cell--outsider' : '')));

    echo '<tr>';
    echo '<td><span class="cell ' . $position_class . '">' . $position . '</span></td>';
    echo '<td><img class="cell--team-logo" src="' . htmlspecialchars($data['logo']) . '"></td>';
    echo '<td><span class="cell--team">' . htmlspecialchars($data['name']) . '</span></td>';

    foreach ($teams as $opponent_id => $opponent_data) {
        if ($team_id === $opponent_id) {
            echo '<td><span class="cell--score cell--own"></span></td>';
        } else {
            $score = isset($matches[$team_id][$opponent_id]) ? $matches[$team_id][$opponent_id] : '-';
            echo '<td><span class="cell--score">' . $score . '</span></td>';
        }
    }

    echo '<td><span class="cell cell--games">' . $data['games'] . '</span></td>';
    echo '<td><span class="cell cell--win">' . $data['wins'] . '</span></td>';
    echo '<td><span class="cell cell--draw">' . $data['draws'] . '</span></td>';
    echo '<td><span class="cell cell--defeat">' . $data['losses'] . '</span></td>';
    echo '<td><span class="cell cell--total">' . $data['points'] . '</span></td>';
    echo '</tr>';

    $position++;
}

echo '</tbody></table>';
?>
