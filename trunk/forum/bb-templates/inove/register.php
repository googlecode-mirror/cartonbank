<?php 
exit();
bb_get_header(); ?>

<h3 class="bbcrumb"><a href="<?php bb_option('uri'); ?>"><?php bb_option('name'); ?></a> &raquo; Зарегистрироваться</h3>

<h2 id="register">Регистрация</h2>

<?php if ( !bb_is_user_logged_in() ) : ?>
<form method="post" action="<?php bb_option('uri'); ?>register.php">
<fieldset>
<legend>Данные профиля</legend>
<p>Ваш пароль будет отправлен по указанному Вами адресу.</p>
<table width="100%">
<?php if ( $user_safe === false ) : ?>
<tr class="error">
<th scope="row"><?php _e('Username:'); ?></th>
<td><input name="user_login" type="text" id="user_login" size="30" maxlength="30" /><br />
Ваше имя пользователя некорректное. Пожалуйста, попробуйте снова.</td>
</tr>
<?php else : ?>
<tr class="required">
<th scope="row"><sup class="required">*</sup> <?php _e('Username:'); ?></th>
<td><input name="user_login" type="text" id="user_login" size="30" maxlength="30" value="<?php if (!is_bool($user_login)) echo $user_login; ?>" /></td>
</tr>
<?php endif; ?>
<?php if ( is_array($profile_info_keys) ) : foreach ( $profile_info_keys as $key => $label ) : ?>
<tr<?php if ( $label[0] ) { echo ' class="required"'; $label[1] = '<sup class="required">*</sup> ' . $label[1]; } ?>>
  <th scope="row"><?php echo $label[1]; ?>:</th>
  <td><input name="<?php echo $key; ?>" type="text" id="<?php echo $key; ?>" size="30" maxlength="140" value="<?php echo $$key; ?>" /><?php
if ( $$key === false ) :
	if ( $key == 'user_email' )
		_e('<br />Возникла проблема с Вашим email. Пожалуйста проверьте это.');
	else
		_e('<br />Расположенное выше поле обязательно для заполнения.');
endif;
?></td>
</tr>
<?php endforeach; endif; ?>
</table>
<p><sup class="required">*</sup> Эти поля <span class=\"required\">обязательны</span> для заполнения.</p>
</fieldset>

<?php do_action('extra_profile_info', $user); ?>

<p class="submit">
  <input type="submit" name="Submit" value="<?php echo attribute_escape( 'Зарегистрироваться &raquo;' ); ?>" />
</p>
</form>
<?php else : ?>
<p>Вы уже вошли в систему. Зачем Вы пытаетесь зарегистрироваться?</p>
<?php endif; ?>

<?php bb_get_footer(); ?>
