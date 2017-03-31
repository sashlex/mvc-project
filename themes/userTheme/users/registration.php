<?php # registration page ?>
<?=$message;?>
<h4>Страница регистрации</h4>
<form action="/registration" method="post">
<label>Login <input name ="login" type="text"/></label><br>
<label>Email <input name ="email" type="text"/></label><br>
<label>Password: <input name ="password" type="password"/></label><br>
<label>Con Pass: <input name ="confirmPassword" type="password"/></label><br>
<input type="submit" name='submit'>
</form>
