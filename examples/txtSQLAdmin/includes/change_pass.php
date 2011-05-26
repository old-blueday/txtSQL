<?php
setDefault('$_GET["finish"]');
require('./includes/header.php');

if ( $_GET['finish'] == 1 )
{
	if ( $_POST['newpass'] != $_POST['newpass1'] )
	{
		error('The two passwords do not match!');
	}
	elseif ( !$sql->grant_permissions('edit', $_SESSION['txtsqladmin']['username'], $_POST['oldpass'], $_POST['newpass1']) )
	{
		error('Error while trying to change your password; txtSQL said: <b>'.$sql->get_last_error().'</b>', E_USER_ERROR);
	}

	$_SESSION['txtsqladmin']['password'] = $_POST['newpass'];
	header('Location: index.php?msg=001');
	exit();
}
?>

<form method="get" name="form" action="index.php" onsubmit="rename_db(this.newdatabase.value); return false;">
<input type="hidden" name="page" value="rename_database">
<input type="hidden" name="action" value="finish">
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
						</form>

						<form name="form1" method="post" action="index.php?page=change_pass&finish=1" onsubmit="checkPasswords(); return false;">
					</td>
					<td valign="top">
						<b><font size="5">Add/Edit Users <i><?php echo $_GET['db'];?></i></font><br />
						   <font size="2">Changing Password</font></b>
						<br/></br/>

						<table width="100%" cellpadding="3" cellspacing="1" border="0">
							<tr>
								<td colspan="2" align="center"><b>Change your password</b></b></td>
							</tr>
							<tr>
								<td style="padding-top:0;padding-bottom:0;height:1px;" bgcolor="AEAEAE" colspan="2">
								</td>
							</tr>
							<tr bgcolor="E1E1E1">
								<td width="150"> > Current password: </td>
								<td><input type="password" name="oldpass"></td>
							</tr>
							<tr bgcolor="BFBFBF">
								<td width="150"> > New Password: </td>
								<td><input type="password" name="newpass"></td>
							</tr>
							<tr bgcolor="E1E1E1">
								<td width="150"> > New Password (confirm): </td>
								<td><input type="password" name="newpass1"></td>
							</tr>
							<tr>
								<td colspan="2" align="center"><input type="submit" value="Submit Changes"> <input type="reset" value="Start Over" onClick="if ( confirm('Are you sure you want to reset the form?') != true ) return false;"></td>
							</tr>
						</table>
					</td>
				</tr>
			</table>
		</td>
	</tr>
</table>

<?php
require('./includes/footer.php');
?>