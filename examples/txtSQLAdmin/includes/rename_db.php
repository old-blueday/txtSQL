<?php
if ( isset($_GET['action']) && $_GET['action'] == 'finish' )
{
	$_GET['db']          = isset($_GET['db'])          ? $_GET['db']          : '';
	$_GET['newdatabase'] = isset($_GET['newdatabase']) ? $_GET['newdatabase'] : '';

	execute('rename database', array($_GET['db'], $_GET['newdatabase']));
	header('Location: index.php?page=view_database&db='.$_GET['newdatabase']);

	exit;
}

include('./includes/header.php');
?>

<form method="get" name="form" action="index.php" onsubmit="rename_db(this.newdatabase.value); return false;">
<input type="hidden" name="page" value="rename_db">
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
					</td>
					<td valign="top">
						<b><font size="5">Viewing database <i><?php echo $_GET['db'];?></i></font><br />
						   <font size="2">Renaming database</font></b>
						<br/></br/>
						<table width="100%" cellpadding="3" cellspacing="1">
							<tr>
								<td style="padding-top:0;padding-bottom:0;height:1px;" bgcolor=aeaeae>
								</td>
							</tr>
							<tr>
								<td align=center bgcolor=e1e1e1>
									<b><a href="index.php?page=view_databases&db=<?php echo $_GET['db']; ?>&action=create_database">Create DB</a> &nbsp;&nbsp;&nbsp;&nbsp;
									   <a href="index.php?page=view_database&db=<?php echo $_GET['db']; ?>&action=create_table">Create Table</a> &nbsp;&nbsp;&nbsp;&nbsp;
									   <a href="javascript:drop_db('<?php echo $_GET['db']; ?>')">Drop DB</a> &nbsp;&nbsp;&nbsp;&nbsp;
									   <a href="index.php?page=export_database&db=<?php echo $_GET['db'];?>&export=-1">Export DB</a> &nbsp;&nbsp;&nbsp;&nbsp;
									   <a href="index.php?page=rename_db&db=<?php echo $_GET['db']; ?>">Rename DB</a> &nbsp;&nbsp;&nbsp;&nbsp;
									   <a href="index.php?page=<?php echo $sql->isLocked($_GET['db']) ? 'unlock' : 'lock';?>_database&db=<?php echo $_GET['db']; ?>&go=index.php?page=view_database%26amp;db={$_GET['db']}"><?php echo $sql->isLocked($_GET['db']) ? 'Unlock' : 'Lock'; ?> DB</a>
									</b>
								</td>
							</tr>
							<tr>
								<td style="padding-top:0;padding-bottom:0;height:1px;" bgcolor=aeaeae>
								</td>
							</tr>
						</table>
						<br/>

						<b>Rename database from <i><?php echo $_GET['db']; ?></i> to<br />
						<input type="text" name="newdatabase" value="<?php $_GET['db']; ?>">
						<input type="submit" value="Rename" class="btn">
					</td>
				</tr>
			</table>
		</td>
	</tr>
</table>

<?php
include('./includes/footer.php');
?>