<?php
setDefault('$_GET["action"]');
setDefault('$_GET["finish"]', 0);

if ( $_GET['action'] == 'edit' )
{
	require('./includes/edit_user.php');
	exit;
}

elseif ( $_GET['action'] == 'add' )
{
	require('./includes/add_user.php');
	exit;
}

elseif ( $_GET['action'] == 'drop' )
{
	require('./includes/drop_user.php');
	exit;
}

require('./includes/header.php');
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

						<form name="form1" method="post" action="index.php?page=users&action=add" onsubmit="checkNewUserForm(); return false;">
						<input type="hidden" name="pass" value="">
						<input type="hidden" name="newpass" value="">
						<input type="hidden" name="newpass1" value="">
					</td>
					<td valign="top">
						<b><font size="5">Add/Edit Users <i><?php echo $_GET['db'];?></i></font><br />
						   <font size="2">Viewing All</font></b>
						<br/></br/>

						<table width="80%" cellpadding="3" cellspacing="1" border="0"><?php
						echo "\n";
						$indent = "\t\t\t\t\t\t\t";
						$users  = $sql->getUsers();

						if ( empty($users) )
						{
							echo "{$indent}<tr>\n";
							echo "{$indent}\t<td style=\"padding-top:0;padding-bottom:0;height:1px;\" bgcolor=\"AEAEAE\" colspan=\"2\">\n";
							echo "{$indent}\t</td>\n";
							echo "{$indent}</tr>\n";
							echo "{$indent}<tr bgcolor=\"E1E1E1\">\n";
							echo "{$indent}\t<td align=\"center\">";
							echo "<b>No Users Found!</b></td>\n";
							echo "{$indent}</tr>\n";
							echo "{$indent}<tr>\n";
							echo "{$indent}\t<td style=\"padding-top:0;padding-bottom:0;height:1px;\" bgcolor=\"AEAEAE\" colspan=\"2\">\n";
							echo "{$indent}\t</td>\n";
							echo "{$indent}</tr>\n";
						}
						else
						{
							echo "{$indent}<tr>\n";
							echo "{$indent}\t<td colspan=\"2\" align=\"center\" width=\"20\"><b>Action</b></td>\n";
							echo "{$indent}\t<td align=\"center\"><b>Username</b></td>\n";
							echo "{$indent}</tr>\n";
							echo "{$indent}<tr>\n";
							echo "{$indent}\t<td style=\"padding-top:0;padding-bottom:0;height:1px;\" bgcolor=\"AEAEAE\" colspan=\"3\">\n";
							echo "{$indent}\t</td>\n";
							echo "{$indent}</tr>\n";

							foreach ( $users as $key => $value )
							{
								$link       = ( $_SESSION['txtsqladmin']['username'] == $value ) ? 'index.php?page=change_pass' : 'javascript: getInfoForUserEdit(\''.$value.'\');';
								$link_drop  = ( $_SESSION['txtsqladmin']['username'] == $value ) ? '' : '<a href="javascript: getPassForUserDrop(\''.$value.'\');">';
								$link_dropA = !empty($link_drop) ? '</a>' : '';

								echo "{$indent}<tr bgcolor=\"".( $bgcolor = $bgcolor == 'E1E1E1' ? 'BFBFBF' : 'E1E1E1' )."\">\n";
								echo "{$indent}\t<td align=\"center\" width=\"10\"><a href=\"{$link}\"><img src=\"images/small_edit.png\" alt=\"Edit user: {$value}\" border=\"0\"></a></td>\n";
								echo "{$indent}\t<td align=\"center\" width=\"10\">{$link_drop}<img src=\"images/small_".( $_SESSION['txtsqladmin']['username'] == $value ? 'no' : '' )."drop.gif\" alt=\"Drop user: {$value}\" border=\"0\">{$link_dropA}</td>\n";
								echo "{$indent}\t<td width=\"\"> > {$value}</td>\n";
								echo "{$indent}</tr>\n";
							}
						}
						?>
						</table>
						<br><br>
						<hr size="2" color="B0B0B0" width="80%" align="left">
						<input type="hidden" name="page" value="create_table">
						<input type="hidden" name="db" value="TestDatabase">

						<b>Create a new user<br />
						Name: &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="text" name="new_username"><br />
						Password: <input type="password" name="new_password"><br />
						<input type="submit" value="Go" class="btn">
					</td>
				</tr>
			</table>
		</td>
	</tr>
</table>

<?php
require('./includes/footer.php');
?>