<h1>Календарь Картунбанка</h2>
Праздники, события, темы дня, достойные внимания СМИ.

<?php 
//if ( current_user_can('manage_options') )
//{
?>
<div id="add_event" style="margin-bottom:12px;">
<table style="border:1px silver solid;padding:4px;width:100%;"><tr><td valign="top" width="400" style="padding-left:10px;">
<span style="text-align:right;width:200px;">Название события: </span><input type="text" name="text" id="text" style="width:300px;" value="авария на луне" onfocus="this.value=''"><br>
<span style="text-align:right;width:200px;">Ключевое слово: </span><input type="text" name="keyword" id="keyword" style="width:300px;" value="луна" onfocus="this.value=''"><br>
<span style="text-align:right;width:200px;">Ссылка на сайте: </span><input type="text" name="details" id="details" style="width:300px;" value="http://cartoonbank.ru/?page_id=29&cs=%D0%BB%D1%83%D0%BD%D0%B0" onfocus="this.value=''">
</td><td style="text-align:center;vertical-align:middle;width:300px;">
<input type="submit" style="margin:4px;background-color:#66CC99;" value=" Создать событие " onclick="javascript:createCalendarEvent();return false;">
</td></table>
</div>
<?
//}
?>

<iframe src="https://www.google.com/calendar/b/0/embed?showTitle=0&amp;showPrint=0&amp;height=600&amp;wkst=2&amp;hl=ru&amp;bgcolor=%23ffffff&amp;src=9ats0457qmvp1mv5kecdut2uhs%40group.calendar.google.com&amp;color=%23182C57&amp;src=ru.russian%23holiday%40group.v.calendar.google.com&amp;color=%23856508&amp;ctz=Europe%2FMoscow" style=" border-width:0 " width="750" height="800" frameborder="0" scrolling="no"></iframe>