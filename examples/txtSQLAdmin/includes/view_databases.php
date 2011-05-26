<?php
include('./includes/header.php');
?>

<form method="get" name="form" action="index.php">
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
						<?php include('./includes/left.php'); ?>

					</td>
					<td valign="top">
						<b><font size="5">Viewing all Databases <i><?php echo $_GET['db'];?></i></font><br/>
						   <font size="2">Database Folder: <?php echo $CFG['txtsql']['data_path']; ?></font>
						<br/></br/>

						<table width="100%" cellspacing="1" cellpadding="4" border="0">
							<tr align="center">
								<td width="50%"><b><font color="515151">Database Name</font></b></td>
								<td colspan="5"><b><font color="515151">Action</font></b></td>
								<td><b><font color="515151">Tables</font></b></td>
							</tr>
							<tr>
								<td height="1" bgcolor="C0C0C0" colspan="9"></td>
							</tr><?php
							
							$bg          = "E1E1E1";
							$totalTables = 0;

							if ( empty($databases) )
							{
								echo "\t\t\t\t\t\t\t<tr>\n";
								echo "\t\t\t\t\t\t\t\t<td bgcolor=\"E1E1E1\" colspan=\"7\" align=\"center\"><b><font color=\"515151\">No Databases Found</font></b></td>\n";
								echo "\t\t\t\t\t\t\t</tr>";
							}
							else
							{
								foreach ( $databases as $key => $value )
								{
									$tables       = count(execute('show tables', array('db' => $value)));
									$totalTables += $tables;

									echo "\t\t\t\t\t\t\t<tr bgcolor=\"".($bgcolor = $bgcolor == 'E1E1E1' ? 'BFBFBF' : 'E1E1E1' )."\" align=\"center\">\n";
									echo "\t\t\t\t\t\t\t\t<td align=\"left\">> <a>$value</a></td>\n";
									echo "\t\t\t\t\t\t\t\t<td width=\"20\"><a href=\"index.php?page=view_database&db=$value\"><img src=\"images/small_databases.gif\" border=\"0\" alt=\"View Tables\"></a></td>\n";
									echo "\t\t\t\t\t\t\t\t<td width=\"20\"><a href=\"index.php?page=rename_db&db=$value\"><img src=\"images/small_rename_database.png\" border=\"0\" alt=\"Rename Database\"></a></td>\n";
									echo "\t\t\t\t\t\t\t\t<td width=\"20\"><a href=\"index.php?page=export_database&db=$value\"><img src=\"images/small_exportdb.gif\" border=\"0\" alt=\"Export Database\"></td>\n";
									echo "\t\t\t\t\t\t\t\t<td width=\"20\"><a href=\"javascript:drop_db('$value')\"><img src=\"images/small_drop.gif\" border=\"0\" alt=\"Drop Database\"></a></td>\n";
									echo "\t\t\t\t\t\t\t\t<td width=\"20\"><a href=\"index.php?page=".( $sql->isLocked($value) ? 'unlock' : 'lock' )."_database&db=$value\"><img src=\"images/small_".( $sql->isLocked($value) ? 'unlock' : 'lock' ).".gif\" border=\"0\" alt=\"".ucfirst( $sql->isLocked($value) ? 'unlock' : 'lock' )." Database\"></a></td>\n";
									echo "\t\t\t\t\t\t\t\t<td>".$tables."</td>\n";
									echo "\t\t\t\t\t\t\t</tr>\n";
								}
							}

							echo "\t\t\t\t\t\t\t<tr>\n";
							echo "\t\t\t\t\t\t\t\t<td colspan=\"6\" align=\"left\"><b>".count($databases)." Databases(s)</b></td>\n";
							echo "\t\t\t\t\t\t\t\t<td align=\"center\"><b>$totalTables Tables(s)</b></td>\n";
							echo "\t\t\t\t\t\t\t</tr>\n";
							?>
						</table>
						</form>
						<form method="get" action="index.php" name="form1" onsubmit="create_db(this.database.value); return false;">
						<input type="hidden" name="page" value="create_database">
						<hr size="2" color="B0B0B0">

						<font color="<?php echo (!empty($_GET['action']) && $_GET['action'] == 'create_database' ) ? 'red' : ''; ?>"><b>Create a new database</b></font><br />
						Name: <input type="text" name="database"> <input type="submit" value="Go" class="btn">
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