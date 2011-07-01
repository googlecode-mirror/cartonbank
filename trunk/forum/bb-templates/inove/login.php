<?php bb_get_header(); ?>

<h3 class="bbcrumb"><a href="<?php bb_option('uri'); ?>"><?php bb_option('name'); ?></a> &raquo; Войти</h3>

<h2 id="userlogin"><?php isset($_POST['user_login']) ? _e('Неудачный вход в систему') : _e('Войти') ; ?></h2>

<form method="post" action="<?php bb_option('uri'); ?>bb-login.php">
<fieldset>
<table>
<?php if ( $user_exists ) : ?>
	<tr valign="top">
		<th scope="row">Имя пользователя:</th>
		<td><input name="user_login" type="text" value="<?php echo $user_login; ?>" /></td>
	</tr>
	<tr valign="top" class="error">
		<th scope="row">Пароль:</th>
		<td><input name="password" type="password" /><br />
		Некорректный пароль.</td>
	</tr>
<?php elseif ( isset($_POST['user_login']) ) : ?>
	<tr valign="top" class="error">
		<th scope="row">Имя пользователя:</th>
		<td><input name="user_login" type="text" value="<?php echo $user_login; ?>" /><br />
		Имя пользователя не существует. <a href="<?php bb_option('uri'); ?>register.php?user=<?php echo $user_login; ?>">Зарегистрируетесь?</a></td>
	</tr>
	<tr valign="top">
		<th scope="row">Пароль:</th>
		<td><input name="password" type="password" /></td>
	</tr>
<?php else : ?>
	<tr valign="top" class="error">
		<th scope="row">Имя пользователя:</th>
		<td><input name="user_login" type="text" /><br />
	</tr>
	<tr valign="top">
		<th scope="row">Пароль:</th>
		<td><input name="password" type="password" /></td>
	</tr>
<?php endif; ?>
	<tr valign="top">
		<th scope="row">Запомнить меня:</th>
		<td><input name="remember" type="checkbox" id="remember" value="1"<?php echo $remember_checked; ?> /></td>
	</tr>
	<tr>
		<th scope="row">&nbsp;</th>
		<td>
			<input name="re" type="hidden" value="<?php echo $redirect_to; ?>" />
			<input type="submit" value="<?php echo attribute_escape( isset($_POST['user_login']) ? 'Попробуйте еще раз &raquo;': 'Войти &raquo;' ); ?>" />
			<?php wp_referer_field(); ?>
		</td>
	</tr>
</table>
</fieldset>
</form>

<?php if ( $user_exists ) : ?>
<form method="post" action="<?php bb_option('uri'); ?>bb-reset-password.php">
<fieldset>
	<p>Если Вы хотите восстановить пароль для этой учетной записи, нажмите  кнопку ниже, чтобы запустить процедуру восстановления:</p>
	<table>
		<tr>
			<th></th>
			<td>
				<input name="user_login" type="hidden" value="<?php echo $user_login; ?>" />
				<input type="submit" value="<?php echo attribute_escape( 'Восстановить пароль &raquo;' ); ?>" />
			</td>
		</tr>
	</table>
</fieldset>
</form>
<?php endif; ?>

<?php bb_get_footer(); ?>
