<?php # restore password page ?>
<?=$message;?>
<h4>Страница восстаовления пароля</h4>
<form action="/restore" method="post">
<label>Email <input name ="email" type="text"/></label><br>
<input type="submit" name='submit'>
</form>
