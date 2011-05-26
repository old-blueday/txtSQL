<?php
if ( empty($_GET['db']) )
{
	header('Location: index.php?page=view_databases');
	exit;
}
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
						<?php include('./includes/left.php') ?>
					</td>
					<td valign="top">
						<b><font size="5">Viewing database <i><?php echo $_GET['db'];?></i></font><br />
						   <font size="2">Viewing all tables</font></b>
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
									   <a href="index.php?page=<?php echo $sql->isLocked($_GET['db']) ? 'unlock' : 'lock';?>_database&db=<?php echo $_GET['db']; ?>"><?php echo $sql->isLocked($_GET['db']) ? 'Unlock' : 'Lock'; ?> DB</a>
									</b>
								</td>
							</tr>
							<tr>
								<td style="padding-top:0;padding-bottom:0;height:1px;" bgcolor=aeaeae>
								</td>
							</tr>
						</table>
						<br/>

						<table width="100%" cellspacing="1" cellpadding="4" border="0">
							<tr align="center">
								<td width="40%"><b><font color="515151">Table Name</font></b></td>
								<td colspan="6"><b><font color="515151">Action</font></b></td>
								<td><b><font color="515151">Records</font></b></td>
								<td><b><font color="515151">Size</font></b></td>
							</tr>
							<tr>
								<td height="1" bgcolor="C0C0C0" colspan="9"></td>
							</tr><?

							$bg           = "E1E1E1";
							$totalSize    = 0;
							$totalRecords = 0;
							echo "\n";

							if ( empty($tables) )
							{
								echo "\t\t\t\t\t\t\t<tr>\n";
								echo "\t\t\t\t\t\t\t\t<td bgcolor=\"E1E1E1\" colspan=\"9\" align=\"center\"><b><font color=\"515151\">No Tables Found</font></b></td>\n";
								echo "\t\t\t\t\t\t\t</tr>\n";
							}
							else
							{
								foreach ( $tables as $key => $value )
								{
									$size          = filesize("{$CFG['txtsql']['data_path']}/{$_GET['db']}/$value.MYD");
									$totalSize    += $size;
									$size          = ( $size > 1000 ) ? round($size / 1000)." kb" : round($size)." bytes";
									$records       = $sql->table_count($value, $_GET['db']);
									$totalRecords += $records;

									echo "\t\t\t\t\t\t\t<tr bgcolor=\"".($bgcolor = $bgcolor == 'E1E1E1' ? 'BFBFBF' : 'E1E1E1' )."\" align=\"center\">\n";
									echo "\t\t\t\t\t\t\t\t<td align=\"left\">> <a href=\"index.php?page=tbl_edit&db={$_GET['db']}&table=$value&action=browse\">$value</a></td>\n";
									echo "\t\t\t\t\t\t\t\t<td><a href=\"index.php?page=table_prop&db={$_GET['db']}&table=$value\"><img src=\"images/small_tbl_properties.png\" border=\"0\" alt=\"View Table Properties\"></a></td>\n";
									echo "\t\t\t\t\t\t\t\t<td><a href=\"index.php?page=table_edit&db={$_GET['db']}&table=$value&action=browse_table\"><img src=\"images/small_tbl.gif\" border=\"0\" alt=\"Browse Table\"></a></td>\n";
									echo "\t\t\t\t\t\t\t\t<td><a href=\"index.php?page=search&db={$_GET['db']}&table=$value\"><img src=\"images/small_search.png\" border=\"0\" alt=\"Search Table\"></a></td>\n";
									echo "\t\t\t\t\t\t\t\t<td><a href=\"index.php?page=table_edit&db={$_GET['db']}&table=$value&action=insert_row\"><img src=\"images/small_insert.png\" border=\"0\" alt=\"Insert a Row\"></a></td>\n";
									echo "\t\t\t\t\t\t\t\t<td><a href=\"javascript: drop_tbl('$value', '{$_GET['db']}')\" style=\"cursor:hand\"><img src=\"images/small_drop.gif\" border=\"0\" alt=\"Drop Table\"></a></td>\n";
									echo "\t\t\t\t\t\t\t\t<td><a href=\"javascript: empty_tbl('$value', '{$_GET['db']}')\" style=\"cursor:hand\"><img src=\"images/small_empty.gif\" border=\"0\" alt=\"Empty Table\"></a></td>\n";
									echo "\t\t\t\t\t\t\t\t<td>".$records."</td>\n";
									echo "\t\t\t\t\t\t\t\t<td align=\"center\">".$size."</td>\n";
									echo "\t\t\t\t\t\t\t</tr>\n";
								}
							}
							$totalSize = ( $totalSize > 1000 ) ? round($totalSize/1000)." kb" : $totalSize." bytes";

							echo "\t\t\t\t\t\t\t<tr>\n";
							echo "\t\t\t\t\t\t\t\t<td colspan=\"7\" align=\"left\"><b>".count($tables)." Table(s)</b></td>\n";
							echo "\t\t\t\t\t\t\t\t<td align=\"center\"><b>$totalRecords Record(s)</b></td>\n";
							echo "\t\t\t\t\t\t\t\t<td align=\"center\"><b>$totalSize</b></td>\n";
							echo "\t\t\t\t\t\t\t</tr>\n";
							?>
						</table>
						</form>
						<form method="get" action="index.php" name="form2" onsubmit="create_table(this.table.value, this.columns.value); return false;">
						<hr size="2" color="B0B0B0">
						<input type="hidden" name="page" value="create_table">
						<input type="hidden" name="db" value="<?php echo $_GET['db']; ?>">

						<font color="<?php echo (!empty($_GET['action']) && $_GET['action'] == 'create_table' ) ? 'red' : ''; ?>"><b>Create a new table in: <?php echo $_GET['db']; ?></font><br />
						Name: <input type="text" name="table"><br />
						Fields: <input type="text" name="columns" size="2"> <input type="submit" value="Go" class="btn">
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