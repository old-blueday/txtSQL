<?php
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

						<form name="form1" method="post" action="index.php?page=export_database&db=<?php echo $_GET['db']; ?>&export=1">
					</td>
					<td valign="top">
						<b><font size="5">Exporting table <i><?php echo $_GET['table']; ?></i></font><br />
						   <font size="3">From database <i><?php echo $_GET['db']; ?></i></font>
						<br/></br/>
						<table width="100%" cellpadding="3" cellspacing="1">
							<tr>
								<td style="padding-top:0;padding-bottom:0;height:1px;" bgcolor=aeaeae>
								</td>
							</tr>
							<tr>
								<td align=center bgcolor=e1e1e1>
									<b><a href="index.php?page=table_prop&db=<?php echo $_GET['db']; ?>&table=<?php echo $_GET['table']; ?>">Structure</a> &nbsp;&nbsp;&nbsp;&nbsp;
									   <a href="index.php?page=table_edit&action=browse_table&db=<?php echo $_GET['db']; ?>&table=<?php echo $_GET['table']; ?>">Browse</a> &nbsp;&nbsp;&nbsp;&nbsp;
									   <a href="index.php?page=search&db=<?php echo $_GET['db']; ?>&table=<?php echo $_GET['table']; ?>">Search</a> &nbsp;&nbsp;&nbsp;&nbsp;
									   <a href="index.php?page=table_edit&action=insert_row&db=<?php echo $_GET['db']; ?>&table=<?php echo $_GET['table']; ?>">Insert</a> &nbsp;&nbsp;&nbsp;&nbsp;
									   <a href="index.php?page=export_database&db=<?php echo $_GET['db']; ?>&table=<?php echo $_GET['table']; ?>&export=-1">Export</a> &nbsp;&nbsp;&nbsp;&nbsp;
									   <a href="javascript: empty_tbl('<?php echo $_GET['table']; ?>', '<?php echo $_GET['db']; ?>');">Empty</a>&nbsp;&nbsp;&nbsp;&nbsp;
									   <a href="javascript: drop_tbl('<?php echo $_GET['table']; ?>', '<?php echo $_GET['db']; ?>');">Drop</a> &nbsp;&nbsp;&nbsp;&nbsp;
									</b>
								</td>
							</tr>
							<tr>
								<td style="padding-top:0;padding-bottom:0;height:1px;" bgcolor="aeaeae">
								</td>
							</tr>
						</table>
						<br>
						<table width="100%" cellspacing="1" cellpadding="3" border="0"><?php
						echo "\n";

						if ( $sql->table_count($_GET['table'], $_GET['db']) <= 0 )
						{
							$indent = "\t\t\t\t\t\t\t";
							echo "{$indent}<tr align=\"center\">\n";
							echo "{$indent}<td><font color=\"red\"><b>Warning</font>: There is no data in this table</b></td>\n";
							echo "{$indent}</tr>\n";
							echo "\t\t\t\t\t\t</table>\n";
						}
						else
						{
						?>
							<tr align="center">
								<td width="200"><b>Option</b></td>
								<td><b>Value</b></td>
							</tr>
							<tr>
								<td colspan="2" style="padding-top:0;padding-bottom:0;height:1px;" bgcolor="aeaeae">
								</td>
							</tr>
							<tr bgcolor="E1E1E1">
								<td valign="top" width="200"><b>> Export Tables <font size="1">( <a href="index.php?page=export_database&db=<?php echo $_GET['db']; ?>&export=-1">Export All</a> )</font></b></td>
								<td>
									<input type="checkbox" name="export_tables[<?php echo $_GET['table']; ?>]" style="border: 0px solid; background-color: #E1E1E1" checked><a href="index.php?page=export_database&db=<?php echo $_GET['db']; ?>&table=<?php echo $_GET['table']; ?>&export=-1"><?php echo $_GET['table']; ?></a><br>
								</td>
							</tr>
							<tr bgcolor="#BFBFBF">
								<td width="200"><b>> Seperate columns with</b></td>
								<td> <input type="text" name="col_seperator" value="|"></td>
							</tr>
							<tr bgcolor="#E1E1E1">
								<td width="200"><b>> Enclose columns with</b></td>
								<td> <input type="text" name="col_enclose" value="&quot;"></td>
							</tr>
							<tr bgcolor="#BFBFBF">
								<td width="200"><b>> Escape columns with</b></td>
								<td> <input type="text" name="col_escape" value="\"></td>
							</tr>
							<tr bgcolor="#E1E1E1">
								<td width="200"><b>> Terminate lines with</b></td>
								<td> <input type="text" name="line_ending" value="\r\n"></td>
							</tr>
							<tr bgcolor="#BFBFBF">
								<td width="200"><b>> Show column names</b></td>
								<td> <input type="checkbox" name="show_cols" checked style="border: 0px solid; background-color: #BFBFBF"></td>
							</tr>
							<tr bgcolor="#E1E1E1">
								<td width="200"><b>> Number of rows to select</b></td>
								<td> <input type="text" name="offset[]" value="<?php echo $sql->table_count($_GET['table'], $_GET['db']) + 1; ?>"> starting at offset <input type="text" name="offset[]" value="0"></td>
							</tr>
							<tr bgcolor="#BFBFBF">
								<td width="200"><b>> Download the output</b></td>
								<td> <input type="checkbox" name="download_output" checked style="border: 0px solid; background-color: #BFBFBF"></td>
							</tr>
							<tr>
								<td colspan="2" align="center"><input type="submit" value="Export Database"> <input type="reset" onclick="if ( confirm('Are you sure you want to reset the form?') == false ) return false;"></td>
							</tr>
						</table><?php
						}
						?>

					</td>
				</tr>
			</table>
		</td>
	</tr>
</table>

<?php
require('./includes/footer.php');
?>