<!DOCTYPE HTML>
<html>
<head>
<title>Отправка карикатур</title>
<meta charset="UTF-8" />

<link rel="stylesheet" href="css/main.css"/>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.4.4/jquery.min.js"></script>
<script charset="utf-8" src="js/jquery.client.js"></script>



</head>

<body>

<div class="errormsg">*</div>
<div class="result"> empty result </div>
		
<form action="index.php" class="regForm" method="POST" action="http://109.120.143.27/cb/ales/upload/savefiles.php">
		
		<input type="submit" value="Отправить на сервер">	
			<input type="file" id="fileUpload0" multiple="true" size="60">
		<input type="submit" value="Отправить на сервер">	
				
</form>
		


</body>
</html>


