<?php
if (isset($_REQUEST["code"]))
{
	$code=trim($_REQUEST["code"]);
	fw ($code);
}
else
{
	$code = 'QBf8NZkGokBqk_zJFhlqggV-bBqv1xL-FJMrGPm0yaE.eyJpdiI6IkN0ZkZBVTdDeE1fUU1iWXpMN0NlaFEifQ.zH5YTazRlWZiJEmoNvwmVNfIanjUzDXJ8U0wKOD_JPVg5i27XhOGI2wgErcjcfD1Dw5q5BEj60sV0jMOe8j90tBHgb08uXqDarisYSJepKhh7HDcvmlznzrf_cUpH_BJ';
	fw ($code);
}

function fw($text)
{
$fp = fopen('code.txt', 'w');
fwrite($fp, $text);
fclose($fp);
}


?>