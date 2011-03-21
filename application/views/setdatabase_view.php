		<div class="centro">
			<p>Before you can make any action you must connect to a DataBase, this can be remote or local, just log in and continue. <br /><small>(All fields are required)</small></p><br />
			<form action="<?=$_SERVER['PHP_SELF']?>" method="POST" id="db">
				<label for="server">Server: </label><input type="text" name="server" /><br />
				<label for="user">User: </label><input type="text" name="user" /><br />
				<label for="pass">Password: </label><input type="password" name="pass" /><br />
				<input type="submit" value="Continue" style="margin-left: 100px; margin-top: 10px;text-align: center;" />
			</form>
		</div>
