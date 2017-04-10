<?php # registration page ?>
<?=$message;?>
<h4>Страница авторизации</h4>
<form action="/login" method="post">
<label>Login <input name ="login" type="text"/></label><br>
<label>Password: <input name ="password" type="password"/></label><br>
<input type="submit" name='submit'>
</form>
