<div class="calendar-of-matches__head">
            <h2 class="calendar-of-matches__title title">Календар матчів</h2>
        </div><!-- calendar-of-matches__head -->

        <div class="calendar-of-matches__head-nav">

            <div class="swiper swiper-month-controls swiper-initialized swiper-horizontal">
                <div class="swiper-wrapper swiper-wrapper-month-controls" id="swiper-wrapper-2894150f39673565"
                    aria-live="polite" style="transition-duration: 0ms; transform: translate3d(0px, 0px, 0px);">
                    <?php foreach($dateTurs as $dateTur): ?>
                    <div class="swiper-slide swiper-slide-month-controls swiper-slide-active" role="group"
                        aria-label="1 / 15" style="margin-right: 5px;">
                        <a data-turnir="<?= $turnir ?>" data-lasttur="<?= $lastTur?>"
                            <?= $currentTur != $dateTur['tur'] ? "data-tur='" . $dateTur['tur'] ."'" : '' ?> class="
                            month-controls__button
                            <?= $dateTur['tur'] <= $lastTur ? 'month-controls__button--past ' : '' ?>
                            <?= $currentTur ==  $dateTur['tur'] ? 'month-controls__button--current' : '' ?>"
                            <?= $currentTur != $dateTur['tur'] ? "href='" . $site_url ."/". $tournament ."?tur=" . $dateTur['tur'] . "&foo=foo'" : '' ?>>
                            <p><?= date_translate($dateTur['month_min_name']) ?></p>
                            <p><?= $dateTur['day_min']?></p>
                        </a>
                    </div>
                    <?php endforeach ?>
                </div>

                <div class="swiper-scrollbar swiper-scrollbar-horizontal">
                    <div class="swiper-scrollbar-drag"
                        style="transform: translate3d(0px, 0px, 0px); width: 70px; transition-duration: 0ms;"></div>
                </div>
                <span class="swiper-notification" aria-live="assertive" aria-atomic="true"></span>
            </div>
        </div> <!-- swiper  -->

        <div class="calendar-of-matches__aside">
            <div class="swiper-matches swiper swiper-initialized swiper-horizontal swiper-backface-hidden">
                <div class="swiper-wrapper" id="swiper-wrapper-1b58efbe70e5fba9" aria-live="polite"
                    style="transform: translate3d(0px, 0px, 0px);">

                    <?php foreach($dataCurrentTurWithDate as $match): ?>

                    <div class="swiper-slide swiper-slide-active" role="group" aria-label="1 / 5"
                        style="margin-right: 5px;">
                        <div class="card-of-matches">
                            <div class="card-of-matches__title-match">
                                <img class="card-of-matches__shirt card-of-matches__shirt--left"
                                    src="/css/components/card-of-matches/assets/images/yellow-shirt.svg" alt="yellow">

                                <img class="card-of-matches__team-logo card-of-matches__team-logo--left"
                                    src="<?= $team_logo_path ?>/<?= $match['team1_photo'] ?>" alt="Логотип команди">

                                <div class="card-of-matches__score">
                                    <?php if( $match['goals1'] != null ): ?>
                                    <span><?= $match['goals1'] ?></span>
                                    :
                                    <span><?= $match['goals2'] ?></span>
                                    <?php else:?>
                                    <span>VS</span>
                                    <?php endif?>
                                </div>

                                <img class="card-of-matches__team-logo card-of-matches__team-logo--right"
                                    src="<?= $team_logo_path ?>/<?= $match['team2_photo'] ?>" alt="leicester">

                                <p class="card-of-matches__team card-of-matches__team--left"><?= $match['team1_name'] ?>
                                </p>

                                <div class="card-of-matches__date-and-time">
                                    <div class="card-of-matches__date"><?= $match['match_day'] ?></div>
                                    <div class="card-of-matches__time"><?= $match['match_time'] ?></div>
                                </div>

                                <p class="card-of-matches__team card-of-matches__team--right">
                                    <?= $match['team2_name'] ?></p>

                                <img class="card-of-matches__shirt card-of-matches__shirt--right"
                                    src="/css/components/card-of-matches/assets/images/blue-shirt.svg" alt="blue">

                                <div class="card-of-matches__marks">
                                    <a class="card-of-matches__mark"><span><?= $match['turnir_name'] ?></span></a>
                                    <a class="card-of-matches__mark"><span><?= $currentTur ?> тур</span></a>
                                    <a class="card-of-matches__mark"><span><?= $match['field_name'] ?></span></a>
                                </div>
                            </div>

                            <div class="card-of-matches__controls">
                                <a href="#"><img src="/css/components/card-of-matches/assets/images/edit-icon.svg"
                                        alt=""></a>
                                <a href="#"><img src="/css/components/card-of-matches/assets/images/scale-icon.svg"
                                        alt=""></a>
                                <a href="#"><img src="/css/components/card-of-matches/assets/images/cut-icon.svg"
                                        alt=""></a>
                                <a href="#"><img src="/css/components/card-of-matches/assets/images/hd-icon.svg"
                                        alt=""></a>
                                <a href="#"><img src="/css/components/card-of-matches/assets/images/red-icon.svg"
                                        alt=""></a>
                                <a href="#"><img src="/css/components/card-of-matches/assets/images/photo-icon.svg"
                                        alt=""></a>
                            </div>

                            <div class="card-of-matches__status">МАТЧ ЗАВЕРШЕНО</div>

                            <a class="card-of-matches__share-button" href="#">
                                <img src="/css/components/card-of-matches/assets/images/share-icon.svg" alt="share">
                            </a>
                        </div>
                    </div>
                    <?php endforeach ?>

                </div>

                <div class="swiper-bg-scroll"></div>
                <div class="swiper-scrollbar swiper-scrollbar-horizontal swiper-scrollbar-lock">
                    <div class="swiper-scrollbar-drag" style="transform: translate3d(0px, 0px, 0px); width: 70px;">
                    </div>
                </div>
                <span class="swiper-notification" aria-live="assertive" aria-atomic="true"></span>
            </div><!-- calendar-of-matches__aside -->
        </div><!-- calendar-of-matches__head-nav -->

        <div class="calendar-of-matches__dynamic-content">
            <section class="green-zone">
                <div class="green-zone__current">
                    <h2 class="green-zone__title title">ЗБІРНА ТУРУ</h2>
                    <div class=" <?= $currentTur <= $lastTur ? 'green-zone__players' : '' ?>">

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

                        <div class="green-zone__footer-title">
                            <img src="/css/components/green-zone/assets/images/v9ku-logo-on-white-back.png"
                                alt="v9ku-logo">

                            <h3><?= $dataCurrentTur[0]['season'] ?>&nbsp;&nbsp;ӏ&nbsp;&nbsp;<?= $dataCurrentTur[0]['turnir_name'] ?>&nbsp;&nbsp;ӏ&nbsp;&nbsp;5Х5&nbsp;&nbsp;ӏ&nbsp;&nbsp;<?= $dataCurrentTur[0]['tur'] ?>
                            </h3>

                            <img src="/css/components/green-zone/assets/images/v9ku-logo-on-white-back.png"
                                alt="v9ku-logo">
                        </div>

                    </div>

                </div>
            </section>
        </div> <!-- calendar-of-matches__dynamic-content -->