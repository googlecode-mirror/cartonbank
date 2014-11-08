<?
function _L($term){
static $_L = array(
	'CLEAR_SEARCH' => 'Очистить поле ввода',
	'PAGE_TITLE' => 'Поиск карикатур',
	'DISPLAYING' => 'Показаны',
	'OF' => 'из',
	'CARTOONS' => 'карикатуры',
	'CARTOONSOF' => 'карикатур',
	'CARTOON' => 'карикатура',
	'MORE' => 'ещё карикатур',
	'ENTER_SEARCH_WORD' => 'введите поисковое слово...',
	'ALL_ARTISTS' => 'Все авторы',
	'SEARCH_BTN' => 'Искать',
	'EXTEND_YOUR_SEARCH' => 'Расширьте поиск этими ключевыми словами',
	

	
	'NO_PHOTO' => 'No photo\'s available',
    'NEW_MEMBER' => 'This user is new'


);
return (!array_key_exists($term,$_L)) ? $term : $_L[$term];
}

?>