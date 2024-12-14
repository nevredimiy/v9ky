<!DOCTYPE html>
<?php
session_start();
$page=2;
//ini_set('error_log', '/error_jeka.txt');
// Define the rate limit settings
$limit = 20; // Number of requests allowed
$period = 60; // Time period (in seconds)

// Get the current timestamp
$now = time();

// Initialize the request count in the session
if (!isset($_SESSION['request_count'])) {
    $_SESSION['request_count'] = 0;
    $_SESSION['start_time'] = $now;
}

// Check if the time period has elapsed; reset the count if it has
if ($now - $_SESSION['start_time'] > $period) {
    $_SESSION['request_count'] = 0;
    $_SESSION['start_time'] = $now;
}

// Check if the request count exceeds the limit
if ($_SESSION['request_count'] >= $limit) {
    // Handle rate limit exceeded (e.g., show an error message, redirect, etc.)
	echo "Перевищена частота запитів. Спробуйте пізніше.";
    //http_response_code(429); // HTTP 429 Too Many Requests
    
    exit;
}
// Increment the request count
$_SESSION['request_count']++;

// Get the User-Agent header from the request
$userAgent = $_SERVER['HTTP_USER_AGENT'];

// List of known malicious User-Agents (add more as needed)
$blacklist = array(
    'BadBot1',
    'MaliciousBot2',
    // Add more malicious User-Agents here
);

// Check if the User-Agent is in the blacklist
if (in_array($userAgent, $blacklist)) {
    // Handle the request from the blacklisted User-Agent (e.g., show an error message, redirect, etc.)
	echo "Forbidden: Your User-Agent is not allowed to access this resource.";
    //http_response_code(403); // HTTP 403 Forbidden
    exit;
}

$start = microtime(true);
?>
<?php
define('READFILE', true);
include_once "config.php";

//error_reporting(E_ALL);
//ini_set('display_errors', 1);

// Назначаем модуль и действие по умолчанию.
$module = 'index';
$action = 'calendar';

//турнир поумолчанию
$recligi = $db->Execute("select name from v9ky_turnir where city=2 and active=1 ORDER BY priority ASC limit 1");
$tournament = $recligi->fields[name];

// Массив параметров из URI запроса.
$params = array();

// Если запрошен любой URI, отличный от корня сайта.
if ($_SERVER['REQUEST_URI'] != '/') {
	try {
		// Для того, что бы через виртуальные адреса можно было также передавать параметры
		// через QUERY_STRING (т.е. через "знак вопроса" - ?param=value),
		// необходимо получить компонент пути - path без QUERY_STRING.
		// Данные, переданные через QUERY_STRING, также как и раньше будут содержаться в
		// суперглобальных массивах $_GET и $_REQUEST.
		$url_path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

		// Разбиваем виртуальный URL по символу "/"
		$uri_parts = explode('/', trim($url_path, ' /'));

		// Если количество частей не кратно 2, значит, в URL присутствует ошибка и такой URL
		// обрабатывать не нужно - кидаем исключение, что бы назначить в блоке catch модуль и действие,
		// отвечающие за показ 404 страницы.
        $tournament = array_shift($uri_parts); // Получили имя турнира
		$module = $uri_parts[0]; // Получили имя модуля
		try {$action = array_shift($uri_parts);} catch (Exception $e) {}


		// Получили имя действия
        $paths = "../../";//находим поднятие по папкам
		// Получили в $params параметры запроса
		for ($i=0; $i < count($uri_parts); $i++) {
			$params[$uri_parts[$i]] = $uri_parts[++$i];
			$paths = $paths."../../";
		}
	} catch (Exception $e) {
		$module = '404';
		$action = 'main';
	}
}

$url = $site_url.'/'.$tournament;
$db_pref = str_replace("-", "_", $tournament)."_v9ky_";

//echo $tournament;

switch($tournament) {
    
	case "rules": $url = 'http://v9ky.in.ua/'.$tournament; require_once("rules.php");break;
        case "spasibo_za_zakaz": $url='http://v9ky.in.ua/'.$tournament; require_once("spasibo_za_zakaz.php"); break;
    default: 
        switch($module) {
			case "poliv": $title="Заявити команду"; require_once("poliv.php"); break;
			case "viberhook": $title="Заявити команду"; require_once("viberhook.php"); break;
			case "addmyteam": $title="Заявити команду"; require_once("addmyteam.php"); break;
			case "addmyteam1": $title="Заявити команду"; require_once("addmyteam1.php"); break;
			case "addmyteam2": $title="Заявити команду"; require_once("addmyteam2.php"); break;
			case "addmyteam3": $title="Заявити команду"; require_once("addmyteam3.php"); break;
			case "addme1": $title="Анкета вільного гравця"; require_once("addme1.php"); break;
			case "gol_turu": $title="Гол туру"; require_once("gol_turu.php"); break;
			case "loyalty": $title="Програма лояльності"; require_once("loyalty.php"); break;
			case "sbornaya": $title="Збірна туру"; require_once("sbornaya.php"); break;
			case "post_game": $title="Відео після гри"; require_once("post_game.php"); break;
            case "contacts": $title="Контакти"; require_once("contacts.php"); break;
			case "anons": $title="Анонс матчу"; require_once("anons.php"); break;
			case "players_match_stat": $title="Індивідуальна статистика гравців"; require_once("players_match_stat.php"); break;
			case "teams_match_stat": $title="Статистика матчу"; require_once("teams_match_stat.php"); break;
			case "3time": $title="Відео третій тайм"; require_once("3time.php"); break;
			case "teams": $title="Команди ліги"; require_once("teams.php"); break;
			case "team": $title="Команда з мініфутболу"; require_once("team.php"); break;
			case "team_jeka": $title="Команда з мініфутболу"; require_once("team_jeka.php"); break;
			case "city_news": $title="Новини турніру"; require_once("city_news.php"); break;
			case "news_read": $title="Новини турніру"; require_once("news_read.php"); break;
			case "rules": $title="Правила мініфутболу"; require_once("rules.php"); break;
			case "calendar": $title="Календар матчів міста"; require_once("calendar.php"); break;
			case "photo": $title="Фото матчу"; require_once("photo.php"); break;
            case "transfer": $title="Трансфери гравців"; require_once("transfer.php"); break;
            case "onlines": $title="Онлайн відео трансляції"; require_once("onlines.php"); break;
			
			case "index_2023": $title="Всеукраїнський турнір з мініфутболу"; require_once("index_2023.php"); break;
			
			case "bombardir": $title="Бомбардири ліги"; require_once("bombardir.php"); break;

			case "top_bombardir": $title="Топ-Бомбардири ліги"; require_once("top_players/top_bombardir.php"); break;
			
			case "violators": $title="Порушники ліги"; require_once("violators.php"); break;
            case "violators_new": $title="Порушники ліги"; require_once("violators_new.php"); break;
			case "video": $title="Відео матчу з мініфутболу"; require_once("video.php"); break;
			case "video_hd": $title="Відео HD матчу з мініфутболу"; require_once("video.php"); break;
			case "table": $title="Турнірна таблиця ліги"; require_once("table.php"); break;
            
            case "cabinet": $title="Page under constraction"; require_once("auth/cabinet.php"); break;
			case "signup": $title="Page under constraction"; require_once("auth/signup.php"); break;
            case "login": $title="Page under constraction"; require_once("auth/login.php"); break;
            case "oauth": $title="Page under constraction"; require_once("auth/fb.php"); break;
			//case "match": require_once("match.php"); break;
			case "arhiv": $title="Архів турнірів V9KY"; require_once("arhiv.php"); break;
			case "live": $title="Мини футбол LIVE V9KY"; require_once("live.php"); break;
			case "assist": $title="Ассистенти гольових передач"; require_once("assist.php"); break;
			case "player": $title="Гравець"; require_once("player.php"); break;
			case "rating": $title="Рейтинг команд"; require_once("rating.php"); break;
			case "rating1": $title="Рейтинг команд"; require_once("rating1.php"); break;
			case "orenda": $title="Оренда футбольного поля"; require_once("inde.php"); break;
                        
			default: $title="Всеукраїнський турнір з мініфутболу 5х5"; require_once("inde.php"); break;
        }
	break;
}

?>