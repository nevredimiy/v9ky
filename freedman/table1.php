<?php

// Идентификатор турнира
$turnirId = 523;

// Выполнение SQL-запроса
$sql = <<<SQL
SELECT
    m.team1 AS team_id,
    t.name AS team_name,
    t.pict AS team_logo,
    m.gols1 AS goals_scored,
    m.gols2 AS goals_conceded,
    m.tur AS match_tur,
    'home' AS match_type
FROM
    v9ky_match m
JOIN
    v9ky_team t ON m.team1 = t.id
WHERE
    m.turnir = :turnir AND m.canseled = 1

UNION ALL

SELECT
    m.team2 AS team_id,
    t.name AS team_name,
    t.pict AS team_logo,
    m.gols2 AS goals_scored,
    m.gols1 AS goals_conceded,
    m.tur AS match_tur,
    'away' AS match_type
FROM
    v9ky_match m
JOIN
    v9ky_team t ON m.team2 = t.id
WHERE
    m.turnir = :turnir AND m.canseled = 1
ORDER BY
    team_name;
SQL;

$stmt = $mysqli->prepare($sql);
$stmt->bindParam(':turnir', $turnirId, PDO::PARAM_INT);
$stmt->execute();
$matches = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Инициализация турнирной таблицы
$table = [];

dump_arr($matches);

// Обработка данных
foreach ($matches as $match) {
    $teamId = $match['team_id'];

    // Если команда отсутствует в таблице, инициализируем её
    if (!isset($table[$teamId])) {
        $table[$teamId] = [
            'team_name' => $match['team_name'],
            'team_logo' => $match['team_logo'],
            'matches' => 0,
            'wins' => 0,
            'draws' => 0,
            'losses' => 0,
            'points' => 0,
            'goals_scored' => 0,
            'goals_conceded' => 0,
        ];
    }

    // Обновляем статистику команды
    $table[$teamId]['matches']++;
    $table[$teamId]['goals_scored'] += $match['goals_scored'];
    $table[$teamId]['goals_conceded'] += $match['goals_conceded'];

    if ($match['goals_scored'] > $match['goals_conceded']) {
        $table[$teamId]['wins']++;
        $table[$teamId]['points'] += 3; // Победа: 3 очка
    } elseif ($match['goals_scored'] == $match['goals_conceded']) {
        $table[$teamId]['draws']++;
        $table[$teamId]['points'] += 1; // Ничья: 1 очко
    } else {
        $table[$teamId]['losses']++;
    }
}

// Сортируем турнирную таблицу по очкам и другим критериям
usort($table, function ($a, $b) {
    if ($a['points'] == $b['points']) {
        // Если очки равны, сортируем по разнице голов
        $goalDifferenceA = $a['goals_scored'] - $a['goals_conceded'];
        $goalDifferenceB = $b['goals_scored'] - $b['goals_conceded'];
        if ($goalDifferenceA == $goalDifferenceB) {
            return $b['goals_scored'] - $a['goals_scored']; // Если разница голов равна, сортируем по забитым голам
        }
        return $goalDifferenceB - $goalDifferenceA;
    }
    return $b['points'] - $a['points']; // Сортировка по очкам
});


// Добавляем пустые ячейки для счетов матчей
foreach ($table as $teamIndex => &$team) {
    foreach ($table as $opponentIndex => $opponent) {
        // Если команда играет сама с собой, оставляем ячейку пустой
        if ($teamIndex === $opponentIndex) {
            $team['match_' . ($opponentIndex + 1)] = '';
        } else {
            // Ищем результат матча между командами
            $match = array_filter($matches, function ($m) use ($team, $opponent) {
                return ($m['team1'] === $team['team_id'] && $m['team2'] === $opponent['team_id']) ||
                       ($m['team1'] === $opponent['team_id'] && $m['team2'] === $team['team_id']);
            });

            // Если матч найден, записываем счет
            if (!empty($match)) {
                $match = reset($match);
                $score = $match['team1'] === $team['team_id'] 
                    ? "{$match['goals1']}:{$match['goals2']}" 
                    : "{$match['goals2']}:{$match['goals1']}";
                $team['match_' . ($opponentIndex + 1)] = $score;
            } else {
                $team['match_' . ($opponentIndex + 1)] = '-'; // Если матч не найден
            }
        }
    }
}

// Вывод таблицы
echo "<table border='1'>";
echo "<tr><th>М</th><th>Команда</th><th>И</th><th>В</th><th>Н</th><th>П</th><th>О</th>";

// Динамически добавляем заголовки для столбцов 1, 2, ...
foreach ($table as $index => $team) {
    echo "<th>" . ($index + 1) . "</th>";
}
echo "</tr>";

// Выводим строки таблицы
foreach ($table as $teamIndex => $team) {
    echo "<tr>";
    echo "<td>{$team['position']}</td>";
    echo "<td>" . htmlspecialchars($team['team_name'], ENT_QUOTES, 'UTF-8') . "</td>";
    echo "<td>{$team['matches']}</td>";
    echo "<td>{$team['wins']}</td>";
    echo "<td>{$team['draws']}</td>";
    echo "<td>{$team['losses']}</td>";
    echo "<td>{$team['points']}</td>";

    // Динамически добавляем значения для столбцов 1, 2, ...
    foreach ($table as $opponentIndex => $opponent) {
        echo "<td>{$team['match_' . ($opponentIndex + 1)]}</td>";
    }
    echo "</tr>";
}
echo "</table>";
?>
