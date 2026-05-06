<?php
/**
 * Point d'entrée unique — CreatorSpace (architecture MVC simplifiée)
 *   /
 *   ├── index.php       (ce fichier : config + bootstrap + routes)
 *   ├── Models/         (entités + repositories)
 *   ├── Views/          (frontoffice/ + assets/)
 *   └── Controllers/    (logique + helpers + APIs externes)
 */

define('ROOT', __DIR__);

// ───────── Configuration BDD ─────────
define('DB_HOST',    '127.0.0.1');
define('DB_PORT',    '3306');
define('DB_NAME',    'creatorspeace');
define('DB_USER',    'root');
define('DB_PASS',    '');
define('DB_CHARSET', 'utf8mb4');

// ───────── Application ─────────
define('APP_NAME', 'CreatorSpace');
define('APP_ENV',  'dev');

// ───────── URL de base (auto) ─────────
if (!defined('BASE_URL')) {
    $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host   = $_SERVER['HTTP_HOST'] ?? 'localhost';
    $script = $_SERVER['SCRIPT_NAME'] ?? '/index.php';
    $base   = rtrim(str_replace('\\', '/', dirname($script)), '/');
    define('BASE_URL', $scheme . '://' . $host . $base);
}

// ───────── SMTP ─────────
define('SMTP_HOST',      'smtp.gmail.com');
define('SMTP_PORT',      587);
define('SMTP_USER',      'fouedgafsi40@gmail.com');
define('SMTP_PASS',      'ijhu vjoo vpab ivpc');
define('SMTP_SECURE',    'tls');
define('SMTP_FROM',      'fouedgafsi40@gmail.com');
define('SMTP_FROM_NAME', 'CreatorSpace');

// ───────── LibreTranslate ─────────
define('LIBRETRANSLATE_URL', 'https://libretranslate.com');
define('LIBRETRANSLATE_KEY', '');

// ───────── APIs externes (clés : variables d’environnement ou remplir ci‑dessous en local) ─────────
// Clé OpenAI : préfixe sk-… — https://platform.openai.com/api-keys
define('OPENAI_API_KEY', getenv('OPENAI_API_KEY') ?: '');
define('OPENAI_MODEL', getenv('OPENAI_MODEL') ?: 'gpt-4o-mini');
// Anthropic / Claude — https://console.anthropic.com/
define('ANTHROPIC_API_KEY', getenv('ANTHROPIC_API_KEY') ?: '');
define('ANTHROPIC_MODEL', getenv('ANTHROPIC_MODEL') ?: 'claude-3-5-sonnet-20241022');
// Google Gemini — https://aistudio.google.com/apikey
define('GEMINI_API_KEY', getenv('GEMINI_API_KEY') ?: 'AIzaSyDb3d2-Sbayho_AhuEc4hmUwzcqMRK9ZFE');
define('GEMINI_MODEL', getenv('GEMINI_MODEL') ?: 'gemini-flash-latest');
// Fournisseur prioritaire : openai | anthropic | gemini (sinon secours sur les autres clés définies)
define('AI_PROVIDER', getenv('AI_PROVIDER') ?: 'openai');
define('WEATHER_CITY',   'Tunis');
define('DISQUS_SHORTNAME', 'creatorspace-2026');

// ───────── Erreurs ─────────
if (APP_ENV === 'dev') {
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
} else {
    error_reporting(0);
    ini_set('display_errors', '0');
}

// ───────── Core (tout est dans Controllers/) ─────────
require_once ROOT . '/Controllers/Router.php';
require_once ROOT . '/Controllers/BaseController.php';
require_once ROOT . '/Controllers/Model.php';
require_once ROOT . '/Controllers/Validator.php';
require_once ROOT . '/Controllers/Pagination.php';

// ───────── Session ─────────
session_set_cookie_params([
    'lifetime' => 0,
    'path'     => '/',
    'httponly' => true,
    'samesite' => 'Lax',
]);
session_start();

// ───────── Routes ─────────
$router = new Router();

// Auth
$router->add('GET',  '/',          'AuthController@loginPage');
$router->add('POST', '/login',     'AuthController@login');
$router->add('GET',  '/logout',    'AuthController@logout');
$router->add('GET',  '/register',  'AuthController@registerPage');
$router->add('POST', '/register',  'AuthController@register');

// Dashboard
$router->add('GET',  '/dashboard',          'DashboardController@index');
$router->add('GET',  '/dashboard/chart',    'DashboardController@chartData');

// Users
$router->add('GET',  '/users',              'UserController@index');
$router->add('GET',  '/users/create',       'UserController@create');
$router->add('POST', '/users/store',        'UserController@store');
$router->add('GET',  '/users/edit/(\d+)',   'UserController@edit');
$router->add('POST', '/users/update/(\d+)', 'UserController@update');
$router->add('POST', '/users/delete/(\d+)', 'UserController@delete');

// Contrats
$router->add('GET',  '/contrats',                'ContratController@index');
$router->add('GET',  '/contrats/create',         'ContratController@create');
$router->add('POST', '/contrats/store',          'ContratController@store');
$router->add('GET',  '/contrats/show/(\d+)',     'ContratController@show');
$router->add('GET',  '/contrats/edit/(\d+)',     'ContratController@edit');
$router->add('POST', '/contrats/update/(\d+)',   'ContratController@update');
$router->add('POST', '/contrats/delete/(\d+)',   'ContratController@delete');
$router->add('POST', '/contrats/statut/(\d+)',   'ContratController@statut');
$router->add('GET',  '/contrats/pdf/(\d+)',      'ContratController@pdf');
$router->add('GET',  '/contrats/translate/(\d+)','ContratController@translate');
$router->add('POST', '/contrats/email/(\d+)',    'ContratController@email');

// Rules
$router->add('GET',  '/rules',                  'RuleController@index');
$router->add('GET',  '/rules/create',           'RuleController@create');
$router->add('POST', '/rules/store',            'RuleController@store');
$router->add('GET',  '/rules/add',              'RuleController@addPage');
$router->add('POST', '/rules/save-batch',       'RuleController@saveBatch');
$router->add('POST', '/rules/import',           'RuleController@import');
$router->add('GET',  '/rules/edit/(\d+)',       'RuleController@edit');
$router->add('POST', '/rules/update/(\d+)',     'RuleController@update');
$router->add('POST', '/rules/delete/(\d+)',     'RuleController@delete');
$router->add('GET',  '/rules/translate/(\d+)',  'RuleController@translate');

// Demandes
$router->add('GET',  '/requests',               'RequestController@index');
$router->add('GET',  '/requests/view/(\d+)',    'RequestController@view');
$router->add('POST', '/requests/approve/(\d+)', 'RequestController@approve');
$router->add('POST', '/requests/reject/(\d+)',  'RequestController@reject');

// Profils
$router->add('GET',  '/profiles',              'ProfileController@index');
$router->add('GET',  '/profiles/view/(\d+)',   'ProfileController@view');
$router->add('POST', '/profiles/update/(\d+)', 'ProfileController@update');

// Configuration
$router->add('GET',  '/config/roles',              'ConfigController@roles');
$router->add('POST', '/config/roles/create',       'ConfigController@createRole');
$router->add('POST', '/config/roles/delete/(\d+)', 'ConfigController@deleteRole');
$router->add('GET',  '/config/settings',           'ConfigController@settings');
$router->add('POST', '/config/settings/update',    'ConfigController@updateSettings');
$router->add('GET',  '/config/history',            'ConfigController@history');

// APIs externes ajoutées
$router->add('GET',  '/weather',          'WeatherController@current');
$router->add('GET',  '/weather/json',     'WeatherController@apiJson');
$router->add('POST', '/ai/summarize',     'AiController@summarize');
$router->add('POST', '/ai/generate',      'AiController@generate');
$router->add('POST', '/ai/rule',          'AiController@rule');
$router->add('POST', '/ai/rules-batch',   'AiController@rulesBatch');

$router->dispatch();
