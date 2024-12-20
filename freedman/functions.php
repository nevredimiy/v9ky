<?php


/**
 * 
 */

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

/**
 * Получает всю статистику игроков в турнире.
 * @param integer - идентификатора турнира
 * @return array - массив со статистикой. [ $player_id => [ $match_id => [ ...statics ] ] ]
 */
function getAllStaticPlayers($turnir) 
{

    global $mysqli;
    // Получаем данные из БД. Статискика всех игроков учавствуюих в текущей лиге. Статистика вся, кроме забитых голов
    $queryStaticPlayers = 
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
                WHERE `turnir` = :turnir
            )
        )";

    // Делаем запрос в БД на игроков которые "вибули"
    $stmt = $mysqli->prepare($queryStaticPlayers);
    $stmt->bindParam(':turnir', $turnir, PDO::PARAM_INT);
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $allStaticPlayers = [];

    foreach ( $result as $key => $value ) {
        $allStaticPlayers[$value['player']][$value['matc']] = $value;
    }
  
    return $allStaticPlayers;

}

/**
 * Получает массив данных игроков - ФИО, фото и т.д.
 * @param array - строка идентификаторов игроков для получения ФИО, фото и т.д
 * @return array
 */
function getDataPlayers($allStaticPlayers) 
{
    
    global $mysqli;

    // Массив только c идентификаторами игроков
    $arrPlayersId = array_keys($allStaticPlayers);
  
    // Подготовляем список идентификаторов для SQL-запроса  
    $placeholders = implode(',', array_fill(0, count($arrPlayersId), '?'));  

    $queryAllPlayersData = 
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
            p.id IN ($placeholders)  
      ";  

    // Делаем запрос в БД на игроков которые "вибули"
    $stmt = $mysqli->prepare($queryAllPlayersData);
    // $stmt->bindParam(':strAllPlayersId', $strAllPlayersId);
    $stmt->execute($arrPlayersId);
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $dataAllPlayers = [];

    // Меняем структуру массива - для удобства работы с ним
    foreach ( $result as $key => $value ) {
        
        $playerId = $value['player_id'];

        if(!isset($dataAllPlayers[$playerId])) {      
            $dataAllPlayers[$playerId] = $value;             
        }
    }  
    return $dataAllPlayers;
}

/**
 * @param integer - идентификатора турнира
 * @param integer - текущий тур
 * @return array 
 */
function getDataCurrentTur($turnir, $currentTur)
{
    global $mysqli;

    $queryDataCurrentTur = 
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
    WHERE m.`turnir` = :turnir AND m.`tur` = :currentTur 
    ORDER BY 
        m.id";

    // Делаем запрос в БД на игроков которые "вибули"
    $stmt = $mysqli->prepare($queryDataCurrentTur);
    $stmt->bindParam(':turnir', $turnir, PDO::PARAM_INT);
    $stmt->bindParam(':currentTur', $currentTur, PDO::PARAM_INT);
    $stmt->execute();
    $dataCurrentTur = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    return $dataCurrentTur;
}

/**
 * @param integer - идентификатора турнира
 * @return array 
 */
function getDateTurs($turnir)
{

    global $mysqli;

    $queryDateTurs = "SELECT 
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
WHERE `turnir` = :turnir
GROUP BY 
    tur
ORDER BY 
    tur ASC;";

    // Делаем запрос в БД на игроков которые "вибули"
    $stmt = $mysqli->prepare($queryDateTurs);
    $stmt->bindParam(':turnir', $turnir, PDO::PARAM_INT);    
    $stmt->execute();
    $dateTurs = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    return $dateTurs;
}

/**
 * 
 */
function getTournament()
{
    $sql = "SELECT * FROM `v9ky_turnir` WHERE `seasons` = (SELECT id FROM `v9ky_seasons` ORDER BY id DESC LIMIT 1)";
    $stmt = $mysqli->prepare($sql);
    $stmt->execute();
    $dataTurnir = $stmt->fetchAll(PDO::FETCH_ASSOC);
    print_r($dataTurnir);
    $tournament = $dataTurnir['name'];
    return $tournament;
}