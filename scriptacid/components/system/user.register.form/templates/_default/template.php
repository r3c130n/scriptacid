<?php namespace ScriptAcid;
if(!defined("KERNEL_INCLUDED") || KERNEL_INCLUDED!==true)die();?>
<?php ShowError($arResult["ERRORS"])?>
<?php if(!$USER->IsAuthorized()):?>
<?php //if($USER->IsAdmin()):?>
<form method="post" action="">
	<table>
		<tr>
			<td>Логин<span class="required">*</span>:</td>
			<td>
				<input type="text" id="USER_LOGIN" name="LOGIN" value=""/>
			</td>
		</tr>
		<tr>
			<td>Имя:</td>
			<td>
				<input type="text" id="USER_NAME" name="NAME" value=""/>
			</td>
		</tr>
		<tr>
			<td>Фамилия:</td>
			<td>
				<input type="text" id="USER_LAST_NAME" name="LAST_NAME" value=""/>
			</td>
		</tr>
		<tr>
			<td>E-mail<span class="required">*</span>:</td>
			<td>
				<input type="text" id="USER_EMAIL" name="EMAIL" value=""/>
			</td>
		</tr>
		<tr>
			<td>Пароль<span class="required">*</span>:</td>
			<td>
				<input type="password" id="USER_PASSWORD" name="PASSWORD" value=""/>
			</td>
		</tr>
		<tr>
			<td>Повторите пароль<span class="required">*</span>:</td>
			<td>
				<input type="password" id="USER_PASSWORD_RT" name="PASSWORD_RT" value=""/>
			</td>
		</tr>
		
		<tr>
			<td></td>
			<td><input type="submit" name="register_btn" value="Зарегистрироваться"/></td>
		</tr>
	</table>
</form>
<?php else:?>
	<?php ShowError("Вы авторизованы. Сначала выйдите из системы!")?>
	<a href="?logout=Y">Выйти</a>
<?php endif?>