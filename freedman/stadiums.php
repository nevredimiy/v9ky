<?php

// Увімкнення відображення помилок
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

// Встановлення рівня звітності помилок
error_reporting(E_ALL);

require_once 'freedman/head.php';
require_once 'freedman/menu.php';

$fields = getFields();

require_once 'views/stadiums.tpl.php';

require_once 'freedman/footer.php';