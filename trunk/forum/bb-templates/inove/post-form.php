<?php if ( !is_topic() ) : ?>
<p>
	<label for="topic">Название темы: (должно быть кратким, но содержательным)
		<input name="topic" type="text" id="topic" size="50" maxlength="80" tabindex="1" />
	</label>
</p>
<?php endif; do_action( 'post_form_pre_post' ); ?>
<p>
	<label for="post_content">Сообщение:
		<textarea name="post_content" cols="50" rows="8" id="post_content" tabindex="3"></textarea>
	</label>
</p>
<?php if ( !is_topic() ) : ?>
<p>
	<label for="tags-input"><?php printf('Введите несколько слов (<a href=\"%s\">меток</a>), разделенных запятыми для помощи в поиске Вашей темы:', bb_get_tag_page_link()) ?>
		<input id="tags-input" name="tags" type="text" size="50" maxlength="100" value="<?php bb_tag_name(); ?> " tabindex="4" />
	</label>
</p>
<?php endif; ?>
<?php if ( is_bb_tag() || is_front() ) : ?>
<p>
	<label for="forum-id">Выберите раздел:
		<?php bb_new_topic_forum_dropdown(); ?>
	</label>
</p>
<?php endif; ?>
<p class="submit">
  <input type="submit" id="postformsub" name="Submit" value="<?php echo attribute_escape( 'Отправить сообщение &raquo;' ); ?>" tabindex="4" />
</p>

<p>Разрешенные коды: <code><?php allowed_markup(); ?></code>. <br />Вы также можете вставлять код между апострофами ( <code>`</code> ).</p>
