<section class="anons">
    <div class="anons__container">
        <div class="anons__share">
            <button class="anons__share-btn"><img src="css/components/card-of-matches/assets/images/share-icon.svg" alt="Зберегти зображення"></button>
        </div>
        <div class="anons__head">
            <div class="anons__teams">
                <div class="anons__teams-content">
                    <div class="anons__logo logo-team1"><img src="<?= $team_logo_path ?>/<?= $dataAnons['team1_photo'] ?>"></div>
                    <div class="match-state state">
                        <?php if($dataAnons['goals1'] != null) :?>
                        <div class="state__score"><?= $dataAnons['goals1'] ?></div>
                        <div class="state__score-middle">:</div>
                        <div class="state__score"><?= $dataAnons['goals2'] ?></div>
                        <?php else: ?>
                            <div class="state__score-middle grey-text">VS</div>
                        <?php endif ?>
                    </div>
                    <div class="anons__logo logo-team2"><img src="<?= $team_logo_path ?>/<?= $dataAnons['team2_photo'] ?>"></div>
                </div>
                <?php if($dataAnons['goals1'] != null) :?>
                <div class="state__text">Матч завершено</div>
                <?php endif ?>
            </div>
        </div>
        <div class="anons__body">
            <h2 class="anons__title">Анонс</h2>
            <div class="anons__text"><?= $dataAnons['anons'] ?></div>
            <div class="anons__history-meet">
                <table class="table">
                        <div class="anons__history-meet table-title">Історія зустрічей між собою</div>
                    <thead>
                        <tr>
                            <th>Сезон</th>
                            <th>Ліга</th>
                            <th>Матчі</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Літо 2024</td>
                            <td>Ліга Голд</td>
                            <td>Фенербахче
                                <span class="text-red">4:0</span>
                                Словацько</td>
                        </tr>
                        <tr>
                            <td>Літо 2019</td>
                            <td>Ліга Голд</td>
                            <td>Фенербахче  <span class="text-red">2:1</span> Словацько</td>
                        </tr>
                    </tbody>
                </table>
                <table class="table__count">
                    <tbody>
                        <tr>
                            <td>3</td>
                            <td>Перемог</td>
                            <td>4</td>
                        </tr>
                        <tr>
                            <td>5</td>
                            <td>Нічиїх</td>
                            <td>5</td>
                        </tr>
                        <tr>
                            <td>13</td>
                            <td>Забитих м'ячів</td>
                            <td>24</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="anons__totalizator">
                <div class="totalizator totalizator-text">Шанси команд на думку редакції</div>
                <div class="totalizator totalizator-t1">
                    <div class="totalizator__item value">П1</div>
                    <div class="totalizator__item percent">70%</div>
                </div>
                <div class="totalizator totalizator-x">
                    <div class="totalizator__item value">Х</div>
                    <div class="totalizator__item percent">20%</div>
                </div>
                <div class="totalizator totalizator-t2">
                    <div class="totalizator__item value">П2</div>
                    <div class="totalizator__item percent">10%</div>
                </div>
            </div>
        </div>
    </div>
</section>

