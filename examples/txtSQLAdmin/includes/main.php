<?php
include('./includes/header.php');
?>

<form method="get" name="form" action="index.php" onsubmit="if ( document.form.page.value == 'view_database' ) form.submit(); else { create_db(this.database.value); return false; }">
<input type="hidden" name="page" value="view_database">
<table width="100%" cellspacing="0" cellpadding="0" border="0" align="center">
	<tr>
		<td align="right">
			<b>Welcome to txtSQL Administrator 2.0.0</b><br>
			<font color="black">Connected to txtSQL <?php echo $sql->version(); ?> running on localhost as <?php echo $_SESSION['txtsqladmin']['username']; ?>@localhost</font>
		</td>
	</tr>
</table>
<table width="100%" height="80%" cellspacing="3" cellpadding="5" align="center" border="0">
	<tr bgcolor="EFEFEF">
		<td valign="top">
			<table width="100%" height="100%" cellspacing="0" cellpadding="3" border="0">
				<tr>
					<td width="150" valign="top" bgcolor="E5E5E5">
						<?php include('./includes/left.php') ?>

						<script>document.form.page.value = 'create_database';</script>
					</td>
					<td valign="top">
						<b><font size="5">txtSQLAdmin v2.0</font><br/>
						Connected to txtSQL <?php echo $sql->version(); ?> running on localhost as <?php echo $_SESSION['txtsqladmin']['username']; ?>@localhost
						</b>
						<br/></br/><?php
						echo "\n";
						if ( isset($_GET['msg']) )
						{
							$msg = array('Password was changed successfully',
								     'User was added successfully',
								     'User was removed successfully',
								     'User was edited successfully');

							switch ( $_GET['msg'] )
							{
								case '001':
								case '002':
								case '003':
								case '004':
									echo "\n\t\t\t\t\t\t<table width=\"80%\" cellspacing=\"1\" cellpadding=\"3\" border=\"0\" style=\"border:1px solid; border-color:AEAEAE\">\n";
									echo "\t\t\t\t\t\t\t<tr>\n";
									echo "\t\t\t\t\t\t\t\t<td align=\"center\" bgcolor=\"E1E1E1\"><b><font color=\"red\">{$msg[ (int) $_GET['msg'] - 1 ]}</font></b></td>\n";
									echo "\t\t\t\t\t\t\t</tr>\n";
									echo "\t\t\t\t\t\t</table><br>\n";

									if ( !empty($_GET['redir']) )
									{
										echo "<meta http-equiv=\"refresh\" content=\"2; url=index.php?page={$_GET['redir']}\">";
									}
							}
						}
						?>

						<table width="80%" cellspacing="2" cellpadding="3">
							<tr>
								<td valign="top" width="50%">
									<table width="100%" cellspacing="0" cellpadding="3">
										<tr>
											<td background="images/gradient.gif">
												<b><font color="black">txtSQL <?php echo $sql->version(); ?></font></b><br />
											</td>
										</tr>
										<tr>
											<td height="1" bgcolor="AEAEAE">
											</td>
										</tr>
									</table>
								</td>
								<td valign="top" width="50%">
									<table width="100%" cellspacing="0" cellpadding="3">
										<tr>
											<td background="images/gradient.gif">
												<b><font color="black">txtSQL Administrator</font></b>
											</td>
										</tr>
										<tr>
											<td height="1" bgcolor="AEAEAE">
											</td>
										</tr>
									</table>
								</td>
							</tr>
							<tr>
								<td valign="top">
									<b>> Create a new database</b><br />
									<input type="text" name="database"> <input type="submit" class="btn" value="Create">
									<hr size="2" color="B0B0B0">
									> <a href="index.php?page=view_databases">View all databases</a><br>
									> <a href="index.php?page=export_database">Export a database</a><br>
									> <a href="index.php?page=change_pass">Change your password</a><br>
									> <a href="index.php?page=users">Add/Edit users</a><br>
								</td>
								<td valign="top">
									> <a href="http://txtsql.sourceforge.net">Official txtSQL Homepage</a><br>
									> <a href="http://chibiguy.dotgeek.org">Official Support Forums</a><br>
									> <a href="http://txtsql.sourceforge.net/index.php?p=docs&title=Documentation">Official Documentation</a><br>
									<hr size="2" color="B0B0B0">
									> <a href="index.php?page=php_info">Show PHP Information</a>
								</td>
							</tr>
						</table>
					</td>
				</tr>
			</table>
		</td>
	</tr>
</table>
</form>

<?php
include('./includes/footer.php');
?>