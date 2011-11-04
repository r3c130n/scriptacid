<?php namespace ScriptAcid;
if(!defined("KERNEL_INCLUDED") || KERNEL_INCLUDED!==true)die();?>
<?php ShowError($arResult["ERRORS"])?>
<form method="post" action="">
	<table>
		<tr>
			<td>Логин:</td>
			<td>
				<input type="text" id="USER_LOGIN" name="LOGIN" value=""/>
			</td>
		</tr>
		<tr>
			<td>Пароль:</td>
			<td>
				<input type="password" id="USER_PASSWORD" name="PASSWORD" value=""/>
			</td>
		</tr>
		<tr>
			<td></td>
			<td><input type="submit" name="login_btn" value="Войти"/></td>
		</tr>
	</table>
</form>