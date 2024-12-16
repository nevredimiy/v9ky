<?php
// Увімкнення відображення помилок
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

// Встановлення рівня звітності помилок
error_reporting(E_ALL);


include_once "dates.php";
include_once "freedman/head.php";
include_once "slider_spons.php";
include_once "freedman/menu.php";
include_once "run_line.php";
include_once "freedman/ligi.php";

include_once "freedman/helpers.php";

$queryDateTurs = $db->Execute("SELECT 
    tur, 
    MIN(date) AS min_date, 
    MAX(date) AS max_date,
    MONTHNAME(MIN(date)) AS month_name,
    DATE_FORMAT(MIN(date), '%d') AS day_min, 
    DATE_FORMAT(MAX(date), '%d') AS day_max 
FROM 
    v9ky_match
WHERE `turnir` = $turnir
GROUP BY 
    tur
ORDER BY 
    tur ASC;");

$dateTurs = [];

while(!$queryDateTurs->EOF){

    $dateTurs [] = $queryDateTurs->fields;

    $queryDateTurs->MoveNext();
}



?>

<section class="calendar-of-matches">
    <div class="calendar-of-matches__grid-container">
        <div class="calendar-of-matches__head">
            <h2 class="calendar-of-matches__title title">Календар матчів</h2>
        </div><!-- calendar-of-matches__head -->

        <div class="calendar-of-matches__head-nav">

            <div class="swiper swiper-month-controls swiper-initialized swiper-horizontal">
                <div class="swiper-wrapper swiper-wrapper-month-controls" id="swiper-wrapper-2894150f39673565" aria-live="polite" style="transition-duration: 0ms; transform: translate3d(0px, 0px, 0px);">
                    <?php foreach($dateTurs as $dateTur): ?>
                        <div class="swiper-slide swiper-slide-month-controls swiper-slide-active" role="group" aria-label="1 / 15" style="margin-right: 5px;">
                            <a class="month-controls__button month-controls__button--past" href="#">
                                <p><?= $dateTur['month_name'] ?></p>
                                <p><?= $dateTur['day_min'] == $dateTur['day_max'] ? $dateTur['day_min'] : $dateTur['day_min'] .'-'. $dateTur['day_max'] ?></p>
                            </a>
                        </div>
                    <?php endforeach ?>                  
                </div>

                <div class="swiper-scrollbar swiper-scrollbar-horizontal"><div class="swiper-scrollbar-drag" style="transform: translate3d(0px, 0px, 0px); width: 70px; transition-duration: 0ms;"></div></div>
                <span class="swiper-notification" aria-live="assertive" aria-atomic="true"></span></div>
            </div> <!-- swiper  -->

            <div class="calendar-of-matches__aside">
                <div class="swiper-matches swiper swiper-initialized swiper-horizontal swiper-backface-hidden">
                    <div class="swiper-wrapper" id="swiper-wrapper-1b58efbe70e5fba9" aria-live="polite" style="transform: translate3d(0px, 0px, 0px);">
                        <div class="swiper-slide swiper-slide-active" role="group" aria-label="1 / 5" style="margin-right: 5px;">
                        <div class="card-of-matches">
                            <div class="card-of-matches__title-match">
                            <img class="card-of-matches__shirt card-of-matches__shirt--left" src="/css/components/card-of-matches/assets/images/yellow-shirt.svg" alt="yellow">

                            <img class="card-of-matches__team-logo card-of-matches__team-logo--left" src="/css/components/card-of-matches/assets/images/chelsea-logo.svg" alt="chelsea">
                    
                            <div class="card-of-matches__score">
                                <span>1</span>
                                :
                                <span>0</span>
                            </div>
                    
                            <img class="card-of-matches__team-logo card-of-matches__team-logo--right" src="/css/components/card-of-matches/assets/images/leicester-logo.svg" alt="leicester">

                            <p class="card-of-matches__team card-of-matches__team--left">ФК Челсі</p>
                    
                            <div class="card-of-matches__date-and-time">
                                <div class="card-of-matches__date">13 серпня (сб)</div>
                                <div class="card-of-matches__time">16:00</div>
                            </div>
                    
                            <p class="card-of-matches__team card-of-matches__team--right">Лестер</p>

                            <img class="card-of-matches__shirt card-of-matches__shirt--right" src="/css/components/card-of-matches/assets/images/blue-shirt.svg" alt="blue">

                            <div class="card-of-matches__marks">
                                <a class="card-of-matches__mark"><span>Прем'єр-Ліга</span></a>
                                <a class="card-of-matches__mark"><span>5 тур</span></a>
                                <a class="card-of-matches__mark"><span>X-Park</span></a>
                            </div>
                            </div>
                            
                            <div class="card-of-matches__controls">
                            <a href="#"><img src="/css/components/card-of-matches/assets/images/edit-icon.svg" alt=""></a>
                            <a href="#"><img src="/css/components/card-of-matches/assets/images/scale-icon.svg" alt=""></a>
                            <a href="#"><img src="/css/components/card-of-matches/assets/images/cut-icon.svg" alt=""></a>
                            <a href="#"><img src="/css/components/card-of-matches/assets/images/hd-icon.svg" alt=""></a>
                            <a href="#"><img src="/css/components/card-of-matches/assets/images/red-icon.svg" alt=""></a>
                            <a href="#"><img src="/css/components/card-of-matches/assets/images/photo-icon.svg" alt=""></a>
                            </div>

                            <div class="card-of-matches__status">МАТЧ ЗАВЕРШЕНО</div>

                            <a class="card-of-matches__share-button" href="#">
                            <img src="/css/components/card-of-matches/assets/images/share-icon.svg" alt="share">
                            </a>
                        </div>
                        </div>

                        <div class="swiper-slide swiper-slide-next" role="group" aria-label="2 / 5" style="margin-right: 5px;">
                        <div class="card-of-matches">
                            <div class="card-of-matches__title-match">
                            <img class="card-of-matches__shirt card-of-matches__shirt--left" src="/css/components/card-of-matches/assets/images/yellow-shirt.svg" alt="yellow">

                            <img class="card-of-matches__team-logo card-of-matches__team-logo--left" src="/css/components/card-of-matches/assets/images/chelsea-logo.svg" alt="chelsea">
                    
                            <div class="card-of-matches__score">
                                <span>1</span>
                                :
                                <span>0</span>
                            </div>
                    
                            <img class="card-of-matches__team-logo card-of-matches__team-logo--right" src="/css/components/card-of-matches/assets/images/leicester-logo.svg" alt="leicester">

                            <p class="card-of-matches__team card-of-matches__team--left">ФК Челсі</p>
                    
                            <div class="card-of-matches__date-and-time">
                                <div class="card-of-matches__date">13 серпня (сб)</div>
                                <div class="card-of-matches__time">16:00</div>
                            </div>
                    
                            <p class="card-of-matches__team card-of-matches__team--right">Лестер</p>

                            <img class="card-of-matches__shirt card-of-matches__shirt--right" src="/css/components/card-of-matches/assets/images/blue-shirt.svg" alt="blue">

                            <div class="card-of-matches__marks">
                                <a class="card-of-matches__mark"><span>Прем'єр-Ліга</span></a>
                                <a class="card-of-matches__mark"><span>5 тур</span></a>
                                <a class="card-of-matches__mark"><span>X-Park</span></a>
                            </div>
                            </div>
                            
                            <div class="card-of-matches__controls">
                            <a href="#"><img src="/css/components/card-of-matches/assets/images/edit-icon.svg" alt=""></a>
                            <a href="#"><img src="/css/components/card-of-matches/assets/images/scale-icon.svg" alt=""></a>
                            <a href="#"><img src="/css/components/card-of-matches/assets/images/cut-icon.svg" alt=""></a>
                            <a href="#"><img src="/css/components/card-of-matches/assets/images/hd-icon.svg" alt=""></a>
                            <a href="#"><img src="/css/components/card-of-matches/assets/images/red-icon.svg" alt=""></a>
                            <a href="#"><img src="/css/components/card-of-matches/assets/images/photo-icon.svg" alt=""></a>
                            </div>

                            <div class="card-of-matches__status">МАТЧ ЗАВЕРШЕНО</div>

                            <a class="card-of-matches__share-button" href="#">
                            <img src="/css/components/card-of-matches/assets/images/share-icon.svg" alt="share">
                            </a>
                        </div>
                        </div>

                        <div class="swiper-slide" role="group" aria-label="3 / 5" style="margin-right: 5px;">
                        <div class="card-of-matches">
                            <div class="card-of-matches__title-match">
                            <img class="card-of-matches__shirt card-of-matches__shirt--left" src="/css/components/card-of-matches/assets/images/yellow-shirt.svg" alt="yellow">

                            <img class="card-of-matches__team-logo card-of-matches__team-logo--left" src="/css/components/card-of-matches/assets/images/chelsea-logo.svg" alt="chelsea">
                    
                            <div class="card-of-matches__score">
                                <span>1</span>
                                :
                                <span>0</span>
                            </div>
                    
                            <img class="card-of-matches__team-logo card-of-matches__team-logo--right" src="/css/components/card-of-matches/assets/images/leicester-logo.svg" alt="leicester">

                            <p class="card-of-matches__team card-of-matches__team--left">ФК Челсі</p>
                    
                            <div class="card-of-matches__date-and-time">
                                <div class="card-of-matches__date">13 серпня (сб)</div>
                                <div class="card-of-matches__time">16:00</div>
                            </div>
                    
                            <p class="card-of-matches__team card-of-matches__team--right">Лестер</p>

                            <img class="card-of-matches__shirt card-of-matches__shirt--right" src="/css/components/card-of-matches/assets/images/blue-shirt.svg" alt="blue">

                            <div class="card-of-matches__marks">
                                <a class="card-of-matches__mark"><span>Прем'єр-Ліга</span></a>
                                <a class="card-of-matches__mark"><span>5 тур</span></a>
                                <a class="card-of-matches__mark"><span>X-Park</span></a>
                            </div>
                            </div>
                            
                            <div class="card-of-matches__controls">
                            <a href="#"><img src="/css/components/card-of-matches/assets/images/edit-icon.svg" alt=""></a>
                            <a href="#"><img src="/css/components/card-of-matches/assets/images/scale-icon.svg" alt=""></a>
                            <a href="#"><img src="/css/components/card-of-matches/assets/images/cut-icon.svg" alt=""></a>
                            <a href="#"><img src="/css/components/card-of-matches/assets/images/hd-icon.svg" alt=""></a>
                            <a href="#"><img src="/css/components/card-of-matches/assets/images/red-icon.svg" alt=""></a>
                            <a href="#"><img src="/css/components/card-of-matches/assets/images/photo-icon.svg" alt=""></a>
                            </div>

                            <div class="card-of-matches__status">МАТЧ ЗАВЕРШЕНО</div>

                            <a class="card-of-matches__share-button" href="#">
                            <img src="/css/components/card-of-matches/assets/images/share-icon.svg" alt="share">
                            </a>
                        </div>
                        </div>

                        <div class="swiper-slide" role="group" aria-label="4 / 5" style="margin-right: 5px;">
                        <div class="card-of-matches">
                            <div class="card-of-matches__title-match">
                            <img class="card-of-matches__shirt card-of-matches__shirt--left" src="/css/components/card-of-matches/assets/images/yellow-shirt.svg" alt="yellow">

                            <img class="card-of-matches__team-logo card-of-matches__team-logo--left" src="/css/components/card-of-matches/assets/images/chelsea-logo.svg" alt="chelsea">
                    
                            <div class="card-of-matches__score">
                                <span>1</span>
                                :
                                <span>0</span>
                            </div>
                    
                            <img class="card-of-matches__team-logo card-of-matches__team-logo--right" src="/css/components/card-of-matches/assets/images/leicester-logo.svg" alt="leicester">

                            <p class="card-of-matches__team card-of-matches__team--left">ФК Челсі</p>
                    
                            <div class="card-of-matches__date-and-time">
                                <div class="card-of-matches__date">13 серпня (сб)</div>
                                <div class="card-of-matches__time">16:00</div>
                            </div>
                    
                            <p class="card-of-matches__team card-of-matches__team--right">Лестер</p>

                            <img class="card-of-matches__shirt card-of-matches__shirt--right" src="/css/components/card-of-matches/assets/images/blue-shirt.svg" alt="blue">

                            <div class="card-of-matches__marks">
                                <a class="card-of-matches__mark"><span>Прем'єр-Ліга</span></a>
                                <a class="card-of-matches__mark"><span>5 тур</span></a>
                                <a class="card-of-matches__mark"><span>X-Park</span></a>
                            </div>
                            </div>
                            
                            <div class="card-of-matches__controls">
                            <a href="#"><img src="/css/components/card-of-matches/assets/images/edit-icon.svg" alt=""></a>
                            <a href="#"><img src="/css/components/card-of-matches/assets/images/scale-icon.svg" alt=""></a>
                            <a href="#"><img src="/css/components/card-of-matches/assets/images/cut-icon.svg" alt=""></a>
                            <a href="#"><img src="/css/components/card-of-matches/assets/images/hd-icon.svg" alt=""></a>
                            <a href="#"><img src="/css/components/card-of-matches/assets/images/red-icon.svg" alt=""></a>
                            <a href="#"><img src="/css/components/card-of-matches/assets/images/photo-icon.svg" alt=""></a>
                            </div>

                            <div class="card-of-matches__status">МАТЧ ЗАВЕРШЕНО</div>

                            <a class="card-of-matches__share-button" href="#">
                            <img src="/css/components/card-of-matches/assets/images/share-icon.svg" alt="share">
                            </a>
                        </div>
                        </div>

                        <div class="swiper-slide" role="group" aria-label="5 / 5" style="margin-right: 5px;">
                        <div class="card-of-matches">
                            <div class="card-of-matches__title-match">
                            <img class="card-of-matches__shirt card-of-matches__shirt--left" src="/css/components/card-of-matches/assets/images/yellow-shirt.svg" alt="yellow">

                            <img class="card-of-matches__team-logo card-of-matches__team-logo--left" src="/css/components/card-of-matches/assets/images/chelsea-logo.svg" alt="chelsea">
                    
                            <div class="card-of-matches__score">
                                <span>1</span>
                                :
                                <span>0</span>
                            </div>
                    
                            <img class="card-of-matches__team-logo card-of-matches__team-logo--right" src="/css/components/card-of-matches/assets/images/leicester-logo.svg" alt="leicester">

                            <p class="card-of-matches__team card-of-matches__team--left">ФК Челсі</p>
                    
                            <div class="card-of-matches__date-and-time">
                                <div class="card-of-matches__date">13 серпня (сб)</div>
                                <div class="card-of-matches__time">16:00</div>
                            </div>
                    
                            <p class="card-of-matches__team card-of-matches__team--right">Лестер</p>

                            <img class="card-of-matches__shirt card-of-matches__shirt--right" src="/css/components/card-of-matches/assets/images/blue-shirt.svg" alt="blue">

                            <div class="card-of-matches__marks">
                                <a class="card-of-matches__mark"><span>Прем'єр-Ліга</span></a>
                                <a class="card-of-matches__mark"><span>5 тур</span></a>
                                <a class="card-of-matches__mark"><span>X-Park</span></a>
                            </div>
                            </div>
                            
                            <div class="card-of-matches__controls">
                            <a href="#"><img src="/css/components/card-of-matches/assets/images/edit-icon.svg" alt=""></a>
                            <a href="#"><img src="/css/components/card-of-matches/assets/images/scale-icon.svg" alt=""></a>
                            <a href="#"><img src="/css/components/card-of-matches/assets/images/cut-icon.svg" alt=""></a>
                            <a href="#"><img src="/css/components/card-of-matches/assets/images/hd-icon.svg" alt=""></a>
                            <a href="#"><img src="/css/components/card-of-matches/assets/images/red-icon.svg" alt=""></a>
                            <a href="#"><img src="/css/components/card-of-matches/assets/images/photo-icon.svg" alt=""></a>
                            </div>

                            <div class="card-of-matches__status">МАТЧ ЗАВЕРШЕНО</div>

                            <a class="card-of-matches__share-button" href="#">
                            <img src="/css/components/card-of-matches/assets/images/share-icon.svg" alt="share">
                            </a>
                        </div>
                        </div>
                    </div>

                <div class="swiper-bg-scroll"></div>
                <div class="swiper-scrollbar swiper-scrollbar-horizontal swiper-scrollbar-lock">
                    <div class="swiper-scrollbar-drag" style="transform: translate3d(0px, 0px, 0px); width: 70px;"></div>
                </div>
                <span class="swiper-notification" aria-live="assertive" aria-atomic="true"></span>
            </div><!-- calendar-of-matches__aside -->
        </div><!-- calendar-of-matches__head-nav -->

        <div class="calendar-of-matches__dynamic-content">
            <section class="green-zone">
            <div class="green-zone__current">
                <h2 class="green-zone__title title">ЗБІРНА ТУРУ</h2>
        
                <div class="green-zone__players">
                <div class="player-card">
                    <div class="player-card__photo-container">
                    <div class="player-card__left-icon">
                        <img src="/css/components/player-card/assets/images/star-icon.png" alt="star">
                        <span>178</span>
                    </div>
        
                    <img class="player-card__right-icon" src="/css/components/player-card/assets/images/sparta-logo.png" alt="sparta">
        
                    <img class="player-card__photo" src="/css/components/player-card/assets/images/yarmol.png" alt="yarmol">
                    </div>
        
                    <div class="player-card__role">Топ-гравець</div>
                    <div class="player-card__club">AFC SPARTA</div>
                    <div class="player-card__name">Максим Мамедов</div>
        
                    <a href="#" class="player-card__link">
                    <span>Таблиця</span>
                    <img src="/css/components/player-card/assets/images/arrow-icon.svg" alt="arrow">
                    </a>
                </div>
                
                <div class="player-card">
                    <div class="player-card__photo-container">
                    <div class="player-card__left-icon">
                        <img src="/css/components/player-card/assets/images/gloves-icon.png" alt="star">
                        <span>178</span>
                    </div>
        
                    <img class="player-card__right-icon" src="/css/components/player-card/assets/images/sparta-logo.png" alt="sparta">
        
                    <img class="player-card__photo" src="/css/components/player-card/assets/images/yarmol.png" alt="yarmol">
                    </div>
        
                    <div class="player-card__role">Топ-голкіпер</div>
                    <div class="player-card__club">AFC SPARTA</div>
                    <div class="player-card__name">Максим Мамедов</div>
        
                    <a href="#" class="player-card__link">
                    <span>Таблиця</span>
                    <img src="/css/components/player-card/assets/images/arrow-icon.svg" alt="arrow">
                    </a>
                </div>
        
                <div class="player-card">
                    <div class="player-card__photo-container">
                    <div class="player-card__left-icon">
                        <img src="/css/components/player-card/assets/images/football-icon.png" alt="star">
                        <span>178</span>
                    </div>
        
                    <img class="player-card__right-icon" src="/css/components/player-card/assets/images/sparta-logo.png" alt="sparta">
        
                    <img class="player-card__photo" src="/css/components/player-card/assets/images/yarmol.png" alt="yarmol">
                    </div>
        
                    <div class="player-card__role">Топ-бомбардир</div>
                    <div class="player-card__club">AFC SPARTA</div>
                    <div class="player-card__name">Максим Мамедов</div>
        
                    <a href="#" class="player-card__link">
                    <span>Таблиця</span>
                    <img src="/css/components/player-card/assets/images/arrow-icon.svg" alt="arrow">
                    </a>
                </div>
        
                <div class="player-card">
                    <div class="player-card__photo-container">
                    <div class="player-card__left-icon">
                        <img src="/css/components/player-card/assets/images/boots-icon.svg" alt="star">
                        <span>178</span>
                    </div>
        
                    <img class="player-card__right-icon" src="/css/components/player-card/assets/images/sparta-logo.png" alt="sparta">
        
                    <img class="player-card__photo" src="/css/components/player-card/assets/images/yarmol.png" alt="yarmol">
                    </div>
        
                    <div class="player-card__role">Топ-асистент</div>
                    <div class="player-card__club">AFC SPARTA</div>
                    <div class="player-card__name">Максим Мамедов</div>
        
                    <a href="#" class="player-card__link">
                    <span>Таблиця</span>
                    <img src="/css/components/player-card/assets/images/arrow-icon.svg" alt="arrow">
                    </a>
                </div>
        
                <div class="player-card">
                    <div class="player-card__photo-container">
                    <div class="player-card__left-icon">
                        <img src="/css/components/player-card/assets/images/pitt-icon.svg" alt="star">
                        <span>178</span>
                    </div>
        
                    <img class="player-card__right-icon" src="/css/components/player-card/assets/images/sparta-logo.png" alt="sparta">
        
                    <img class="player-card__photo" src="/css/components/player-card/assets/images/yarmol.png" alt="yarmol">
                    </div>
        
                    <div class="player-card__role">Топ-захисник</div>
                    <div class="player-card__club">AFC SPARTA</div>
                    <div class="player-card__name">Максим Мамедов</div>
        
                    <a href="#" class="player-card__link">
                    <span>Таблиця</span>
                    <img src="/css/components/player-card/assets/images/arrow-icon.svg" alt="arrow">
                    </a>
                </div>
                
                <div class="player-card">
                    <div class="player-card__photo-container">
                    <div class="player-card__left-icon">
                        <img src="/css/components/player-card/assets/images/player-icon.svg" alt="star">
                        <span>178</span>
                    </div>
        
                    <img class="player-card__right-icon" src="/css/components/player-card/assets/images/sparta-logo.png" alt="sparta">
        
                    <img class="player-card__photo" src="/css/components/player-card/assets/images/yarmol.png" alt="yarmol">
                    </div>
        
                    <div class="player-card__role">Топ-дриблінг</div>
                    <div class="player-card__club">AFC SPARTA</div>
                    <div class="player-card__name">Максим Мамедов</div>
        
                    <a href="#" class="player-card__link">
                    <span>Таблиця</span>
                    <img src="/css/components/player-card/assets/images/arrow-icon.svg" alt="arrow">
                    </a>
                </div>
        
                <div class="player-card">
                    <div class="player-card__photo-container">
                    <div class="player-card__left-icon">
                        <img src="/css/components/player-card/assets/images/rocket-ball-icon.png" alt="star">
                        <span>178</span>
                    </div>
        
                    <img class="player-card__right-icon" src="/css/components/player-card/assets/images/sparta-logo.png" alt="sparta">
        
                    <img class="player-card__photo" src="/css/components/player-card/assets/images/yarmol.png" alt="yarmol">
                    </div>
        
                    <div class="player-card__role">Топ-удар</div>
                    <div class="player-card__club">AFC SPARTA</div>
                    <div class="player-card__name">Максим Мамедов</div>
        
                    <a href="#" class="player-card__link">
                    <span>Таблиця</span>
                    <img src="/css/components/player-card/assets/images/arrow-icon.svg" alt="arrow">
                    </a>
                </div>
        
                <div class="player-card">
                    <div class="player-card__photo-container">
                    <div class="player-card__left-icon">
                        <img src="/css/components/player-card/assets/images/ball-icon.png" alt="star">
                        <span>178</span>
                    </div>
        
                    <img class="player-card__right-icon" src="/css/components/player-card/assets/images/sparta-logo.png" alt="sparta">
        
                    <img class="player-card__photo" src="/css/components/player-card/assets/images/yarmol.png" alt="yarmol">
                    </div>
        
                    <div class="player-card__role">Топ-пас</div>
                    <div class="player-card__club">AFC SPARTA</div>
                    <div class="player-card__name">Максим Мамедов</div>
        
                    <a href="#" class="player-card__link">
                    <span>Таблиця</span>
                    <img src="/css/components/player-card/assets/images/arrow-icon.svg" alt="arrow">
                    </a>
                </div>
                
                <div class="green-zone__footer-title">
                    <img src="/css/components/green-zone/assets/images/v9ku-logo-on-white-back.png" alt="v9ku-logo">
        
                    <h3>СЕЗОН «ЛІТО 2022»&nbsp;&nbsp;ӏ&nbsp;&nbsp;ПРЕМ’ЄР ЛІГА&nbsp;&nbsp;ӏ&nbsp;&nbsp;8Х8&nbsp;&nbsp;ӏ&nbsp;&nbsp;5 ТУР</h3>
        
                    <img src="/css/components/green-zone/assets/images/v9ku-logo-on-white-back.png" alt="v9ku-logo">
                </div>
                </div>
        
            </div>
            </section>
        </div> <!-- calendar-of-matches__dynamic-content -->
    </div> <!-- calendar-of-matches__grid-container -->
</section>

<?php 
    include_once "freedman/footer.php";
?>