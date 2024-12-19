<?php if($currentTur <= $lastTur) : ?>
<?php foreach($bestPlayersForTable as $player) : ?>

<div class="player-card">
    <div class="player-card__photo-container">
        <div class="player-card__left-icon">
            <img src="/css/components/player-card/assets/images/<?= $labels[$player['best_player']]['icon'] ?>"
                alt="star">
            <span><?= $player['count_points'] ?></span>
        </div>

        <img class="player-card__right-icon" src="<?= $team_logo_path ?>/<?= $player['team_photo'] ?>"
            alt="Логотип команды">

        <img class="player-card__photo" src="<?= $player_face_path ?>/<?= $player['player_photo'] ?>" alt="yarmol">
    </div>

    <div class="player-card__role"><?= $labels[$player['best_player']]['role'] ?></div>
    <div class="player-card__club"><?= $player['team_name'] ?></div>
    <div class="player-card__name"><?= $player['first_name'] ?> <?= $player['last_name'] ?>
    </div>

    <a href="#" class="player-card__link">
        <span>Таблиця</span>
        <img src="/css/components/player-card/assets/images/arrow-icon.svg" alt="arrow">
    </a>
</div>

<?php endforeach ?>
<?php else: ?>
<h2 class="green-zone__title text-center">Цей турнір ще не відбувся, або дані турніру не внесені
    адміністратором </h2>
<?php endif ?>