<?php
// Увімкнення відображення помилок
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

// Встановлення рівня звітності помилок
error_reporting(E_ALL);

if (isset($params['match'])) {
    $baza = '/match/' . $params['match'];
}
// session_start();

include_once "dates.php";
include_once "freedman/head.php";
include_once "slider_spons.php";
include_once "freedman/menu.php";
include_once "run_line.php";
include_once "freedman/ligi.php";
include_once "freedman/rating_players.php";

?>

<div class="statistic">
    <div class="container">
    <table id="top-bombardir">
      <caption>
        ТОП-Бомбардир
        <button>
          <img src="/css/components/statistic/assets/images/button-exit.svg" alt="exit">
        </button>
      </caption>
      <thead>
        <tr>
          <th>№</th>
          <th class="th_s" data-label="Ф">ФОТО</th>
          <th class="th_s" data-label="Л">ЛОГО</th>
          <th class="th_s" data-label="Г">ГРАВЕЦЬ</th>
          <th class="th_s" data-label="Г">Голів</th>
          <th class="th_s" data-label="М">Матчів</th>
          <th class="th_s" data-label="Г/М">Г/М</th>
          <?php for($i = 1; $i <= 10; $i++): ?>
              <th data-label="<?= $i ?>}"><?= $i ?></th>
          <?php endfor ?>
        </tr>
      </thead>
      <tbody>
        <?php foreach($topBombardi as $player): ?>
        <tr>
          <td><?= isset($player['rank']) ? $player['rank'] : 1 ?></td>
          <td><img src="<?=$player_face_path?>/<?= $player['player_photo'] ?>" alt="team-logo"></td>
          <td><img src="<?=$team_logo_path?>/<?= $player['team_photo'] ?>" alt="team-logo"></td>
          <td class="name-cell"><?= $player['last_name'] ?> <?= $player['first_name'] ?></td>
          <td><?= $player['total_key'] ?></td>
          <td><?= $player['match_count'] ?></td>
          <td><?= $player['key_per_match'] ?></td>
          <?php for ($i = 1; $i <= 10; $i++): ?>
            <?php $stub = $i > $lastTur ? '?' : '-' ?>
            <td class="turs" <?= $i > $lastTur ? 'style="opacity:0.5"' : '' ?> ><?= isset($player["match_{$i}_key"]) ? $player["match_{$i}_key"]  : $stub  ?></td>
          <?php endfor ?>
        </tr>
        <?php endforeach ?>
      </tbody>
    </table>
    </div>
</div>