<?php
function getBestPlayerOfTurForAjax($turnir, $tur)
{
    global $mysqli;
    
    $queryCurrentTur = "SELECT 
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
    WHERE m.`turnir` = :turnir AND m.`tur` = :tur 
    ORDER BY 
        m.id";
        
    // Делаем запрос в БД на игроков которые "вибули"
    $stmt = $mysqli->prepare($queryCurrentTur);
    $stmt->bindParam(':turnir', $turnir, PDO::PARAM_INT);
    $stmt->bindParam(':tur', $tur, PDO::PARAM_INT);
    $stmt->execute();
    $bestPlayers = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    return $bestPlayers;
}