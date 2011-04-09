<?
echo "<h1>Расширенный поиск</h1>";
?>
<form method=post action="?page_id=29">

	
<table width="100%" ><tbody><tr bgcolor="#C1E0FF">
<td>
	<table cellpadding="4"><tbody><tr>
	<td style="vertical-align:top;width=15%;white-space:nowrap;"><b>Найти результаты</b></td>
	<td width="85%">
		
		<table width="100%" cellpadding="4" cellspacing="0"><tbody>

		<tr>
			<td style="text-align:right;">со <b>всеми</b> словами</td>
			<td><input type="text" value="" name="cs" size="25"></td>
			<td valign="top" rowspan="4"><input type="submit" name="search" value="&nbsp;Искать&nbsp;"></td></tr>
		<tr>
			<td style="text-align:right;">с <b>точной фразой</b></td>
			<td colspan="2"><input type="text" value="" name="cs_exact" size="25"></td></tr>
		<tr>
			<td style="text-align:right;">c <b>любым</b> из слов</td>
			<td colspan="2"><input type="text" size="25" value="" name="cs_any"></td></tr>
		</tbody></table>

	</td></tr></tbody></table>
</td></tr><tr bgcolor="#ffffff">

<td>
	<table width="100%" cellspacing="0" cellpadding="4"><tbody>
		<tr>
			<td width="15%"><b>Автор</b></td>
			<td width="40%">Выбор изображений по автору</td>
			<td><? echo (get_artists_list());?></td></tr></tbody>
	</table>
</td></tr><tr bgcolor="#ffffff"><td>

<table width="100%" cellspacing="0" cellpadding="4"><tbody>
	<tr>
		<td width="15%"><b>Категория</b></td>
		<td width="40%">Выбор изображения по категориям</td>
		<td><? echo (get_category_list()); ?></td></tr>
	<tr>
		<td width="15%">&nbsp;</td>
		<td width="40%">Включая "рабочий стол"</td>
		<td><input type="checkbox" name="666"></td></tr></tbody></table>

</td></tr>

<tr bgcolor="#ffffff">
	<td><table width="100%" cellspacing="0" cellpadding="4"><tbody><tr>
	<td width="15%"><b>Цветность</b></td>
	<td colspan="2"><input id="sfio" type="radio" checked="" value="all" name="color"> <label for="sfio">Без фильтрации</label>&nbsp;&nbsp;<input id="ss" type="radio" value="color" name="color"> <label for="ss">Только цветные</label>&nbsp;&nbsp;<input id="ss" type="radio" value="bw" name="color"> <label for="ss">Только чёрно-белые</label>&nbsp;&nbsp;</td></tr></tbody></table></td></tr></tbody></table>
</form>


<?
function get_artists_list()
{
	// Get the Brand (author) data
	$brands_sql = "SELECT id, name FROM `wp_product_brands` where active = 1 order by name";
	$brands_result  = $GLOBALS['wpdb']->get_results($brands_sql,ARRAY_A);

	// all authors dropdown
	$authors = "<select name='brand'><option value=''>все авторы</option>";

	foreach ($brands_result as $brand)
	{
		$authors .= "<option value=".$brand['id'].">".$brand['name']."</option>";
	}
	$authors .= "</select>";

	return $authors;
}

function get_category_list()
{
	// Get the Category data


	$categories_sql = "SELECT * FROM `wp_product_categories` WHERE `active`='1' AND `category_parent` = '0' ORDER BY `order` ASC";
	$categories_result  = $GLOBALS['wpdb']->get_results($categories_sql,ARRAY_A);

	// all authors dropdown
	$categories = "<select name='category'><option value=''>все категории</option>";



	foreach ($categories_result as $category)
	{
		$categories .= "<option value=".$category['id'].">".$category['name']."</option>";
	}
	$categories .= "</select>";

	return $categories;
}


?>