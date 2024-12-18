<?php

$turnir_id = 523; // Укажите ID турнира

// SQL-запрос
$sql = "SELECT 
    t1.id AS team1_id, 
    t1.name AS team1_name,
    t2.id AS team2_id, 
    t2.name AS team2_name,
    m.gols1 AS team1_goals,
    m.gols2 AS team2_goals
FROM 
    v9ky_match m
JOIN 
    v9ky_team t1 ON m.team1 = t1.id
JOIN 
    v9ky_team t2 ON m.team2 = t2.id
WHERE 
    m.turnir = :turnir AND m.canseled = '1'
ORDER BY 
    t1.name ASC, t2.name ASC
";

$stmt = $mysqli->prepare($sql);
$stmt->bindParam(':turnir', $turnir_id, PDO::PARAM_INT);
$stmt->execute();
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);

dump_arr($result);
if (!$result) {
    die('Ошибка выполнения запроса: ' . $mysqli->error);
}

// Инициализация данных
$teams = [];
$matches = [];

// Преобразуем результат запроса в массив
$rows = $result;

// Обработка результата через foreach
foreach ($rows as $row) {
    $team1_id = $row['team1_id'];
    $team2_id = $row['team2_id'];
    $teams[$team1_id] = $row['team1_name'];
    $teams[$team2_id] = $row['team2_name'];
    $matches[$team1_id][$team2_id] = $row['team1_goals'] . ':' . $row['team2_goals'];
    $matches[$team2_id][$team1_id] = $row['team2_goals'] . ':' . $row['team1_goals'];
}


// Инициализация турнирной таблицы
$stats = [];
foreach ($teams as $team_id => $team_name) {
    $stats[$team_id] = [
        'name' => $team_name,
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
    if ($a['points'] == $b['points']) {
        return 0;
    }
    return ($a['points'] > $b['points']) ? -1 : 1; // Сортировка по убыванию
});


// Вывод таблицы
echo "<table border='1'>";
echo "<tr><th>М</th><th>Команда</th>";
foreach ($teams as $team_id => $team_name) {
    echo "<th>{$team_name}</th>";
}
echo "<th>И</th><th>В</th><th>Н</th><th>П</th><th>О</th></tr>";

$position = 1;
foreach ($stats as $team_id => $data) {
    echo "<tr>";
    echo "<td>{$position}</td>";
    echo "<td>{$data['name']}</td>";
    foreach ($teams as $opponent_id => $opponent_name) {
        if ($team_id === $opponent_id) {
            echo "<td>-</td>";
        } else {
            echo "<td>" . (isset($matches[$team_id][$opponent_id]) ? $matches[$team_id][$opponent_id] : '-') . "</td>";
        }
    }
    
    echo "<td>{$data['games']}</td>";
    echo "<td>{$data['wins']}</td>";
    echo "<td>{$data['draws']}</td>";
    echo "<td>{$data['losses']}</td>";
    echo "<td>{$data['points']}</td>";
    echo "</tr>";
    $position++;
}
echo "</table>";

