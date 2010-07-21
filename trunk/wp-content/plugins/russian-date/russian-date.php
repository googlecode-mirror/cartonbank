<?php
/*
Plugin Name: Russian Date
Plugin URI: http://maxsite.org/
Description: Russian Date 
Version: 1.01
Author: MAX
Author URI: http://maxsite.org/russian-date
*/

function maxsite_the_russian_time($tdate = '') {
	if ( substr_count($tdate , '---') > 0 ) return str_replace('---', '', $tdate);

	$treplace = array (
	"������" => "������",
	"�������" => "�������",
	"����" => "�����",
	"������" => "������",
	"���" => "���",
	"����" => "����",
	"����" => "����",
	"������" => "�������",
	"��������" => "��������",
	"�������" => "�������",
	"������" => "������",
	"�������" => "�������",
	"th" => "",
	"st" => "",
	"nd" => "",
	"rd" => ""
	);
   	return strtr($tdate, $treplace);
}

add_filter('the_time', 'maxsite_the_russian_time');
add_filter('get_comment_date', 'maxsite_the_russian_time');
?>