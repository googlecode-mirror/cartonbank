<?php
/**
 * Основные параметры WordPress.
 *
 * Этот файл содержит следующие параметры: настройки MySQL, префикс таблиц,
 * секретные ключи, язык WordPress и ABSPATH. Дополнительную информацию можно найти
 * на странице {@link http://codex.wordpress.org/Editing_wp-config.php Editing
 * wp-config.php} Кодекса. Настройки MySQL можно узнать у хостинг-провайдера.
 *
 * Этот файл используется сценарием создания wp-config.php в процессе установки.
 * Необязательно использовать веб-интерфейс, можно скопировать этот файл
 * с именем "wp-config.php" и заполнить значения.
 *
 * @package WordPress
 */

// ** Настройки MySQL: Эту информацию можно получить у вашего хостинг-провайдера ** //
/** Имя базы данных для WordPress 
define('DB_NAME', 'database_name_here');
define('DB_NAME', 'z58365_cbru3');*/
define('DB_NAME', 'cartoonbankru');

/** Имя пользователя MySQL 
define('DB_USER', 'username_here');*/
define('DB_USER', 'z58365_cbru3');

/** Пароль пользователя MySQL 
define('DB_PASSWORD', 'password_here');*/
define('DB_PASSWORD', 'greenbat');

/** Адрес сервера MySQL 
define('DB_HOST', 'localhost');*/
define('DB_HOST', 'localhost');

/** Кодировка базы данных при создании таблиц. */
define('DB_CHARSET', 'utf8');

/** Схема сопоставления. Не меняйте, если не уверены. */
define('DB_COLLATE', '');

/**#@+
 * Уникальные ключи и соли для аутентификации.
 *
 * Смените значение каждой константы на уникальную фразу.
 * Можно сгенерировать их с помощью {@link https://api.wordpress.org/secret-key/1.1/salt/ сервиса ключей на WordPress.org}
 * Можно изменить их, чтобы сделать существующие файлы cookies недействительными. Пользователям потребуется снова авторизоваться.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         'впникальную фразу');
define('SECURE_AUTH_KEY',  'впишите сюльную фразу');
define('LOGGED_IN_KEY',    'впишьную фразу');
define('NONCE_KEY',        'никальную фразу');
define('AUTH_SALT',        'впишите сюда уникальну');
define('SECURE_AUTH_SALT', 'впишите сюдзу');
define('LOGGED_IN_SALT',   'впишиу');
define('NONCE_SALT',       'ишите сюда уникальную фра');

/**#@-*/

/**
 * Префикс таблиц в базе данных WordPress.
 *
 * Можно установить несколько блогов в одну базу данных, если вы будете использовать
 * разные префиксы. Пожалуйста, указывайте только цифры, буквы и знак подчеркивания.
 */
$table_prefix  = 'wp_';

/**
 * Язык локализации WordPress, по умолчанию английский.
 *
 * Измените этот параметр, чтобы настроить локализацию. Соответствующий MO-файл
 * для выбранного языка должен быть установлен в wp-content/languages.
 */
define ('WPLANG', 'ru_RU');

/**
 * Для разработчиков: Режим отладки WordPress.
 *
 * Измените это значение на true, чтобы включить отображение уведомлений при разработке.
 * Настоятельно рекомендуется, чтобы разработчики плагинов и тем использовали WP_DEBUG
 * в своём рабочем окружении.
 */
define('WP_DEBUG', false);

/* Это всё, дальше не редактируем. Успехов! */

/** Абсолютный путь к директории WordPress. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

define('SITEURL','http://cartoonbank.ru/');
define('SHOPPINGCARTURL','http://cartoonbank.ru/wp-content/plugins/wp-shopping-cart/');
define('ROOTDIR','/home/www/cb3/');
    
/** Инициализирует переменные WordPress и подключает файлы. */
require_once(ABSPATH . 'wp-settings.php');
?>
