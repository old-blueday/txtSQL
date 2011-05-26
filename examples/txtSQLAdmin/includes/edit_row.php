<?php
setDefault('$_POST["whereClause"]');
setDefault('$_GET["finish"]');

if ( empty($_POST['whereClause']) )
{
	error('Missing a where clause!', E_USER_ERROR);
}

if ( $_GET['finish'] == 1 )
{
	execute('update', array(
		'db'     => $_GET['db'],
		'table'  => $_GET['table'],
		'where'  => unserialize($_POST['whereClause']),
		'values' => removeSlashes($_POST['values']),
		'limit'  => array(1)
	));

	header('Location: index.php?page=table_edit&action=browse_table&db='.$_GET['db'].'&table='.$_GET['table'].'&s='.$_POST['s'].'&sdir='.$_POST['sdir'].'&scolumn='.$_POST['scolumn'].'&erow='.$_POST['erow']);
	exit;
}

include('./includes/header.php');
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

						<form name="form1" method="post" action="index.php?page=table_edit&action=edit_row&db=<?php echo $_GET['db']; ?>&table=<?php echo $_GET['table']; ?>&finish=1">
						<input type="hidden" name="whereClause" value="<?php echo htmlentities($_POST['whereClause']); ?>">
						<input type="hidden" name="s" value="<?php echo $_POST['s']; ?>">
						<input type="hidden" name="scolumn" value="<?php echo $_POST['scolumn']; ?>">
						<input type="hidden" name="sdir" value="<?php echo $_POST['sdir']; ?>">
						<input type="hidden" name="erow" value="<?php echo $_POST['erow']; ?>">
					</td>
					<td valign="top">
						<b><font size="5">Editing Record</font><br />
						   <font size="2">Viewing table structure: <?php echo $_GET['table'];?> ( <?php echo $_GET['db']; ?> )</font></b>
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
						<br/><?php
						echo "\n";

						$cols = $sql->execute('describe', array(
							'db'    => $_GET['db'],
							'table' => $_GET['table']
						));

						$row = execute('select', array(
							'db'    => $_GET['db'],
							'table' => $_GET['table'],
							'where' => unserialize($_POST['whereClause']),
							'limit' => array(0, 0)
						));

						if ( !empty($row) )
						{
							echo "\t\t\t\t\t\t<table width=\"98%\" cellspacing=\"1\" cellpadding=\"3\" border=\"0\">\n";
							echo "\t\t\t\t\t\t\t<tr align=\"center\">\n";
							echo "\t\t\t\t\t\t\t\t<td><b>Column</b></td>\n";
							echo "\t\t\t\t\t\t\t\t<td><b>Type</b></td>\n";
							echo "\t\t\t\t\t\t\t\t<td><b>Value</b></td>\n";
							echo "\t\t\t\t\t\t\t</tr>\n";
							echo "\t\t\t\t\t\t\t<tr>\n";
							echo "\t\t\t\t\t\t\t\t<td colspan=\"3\" style=\"padding-top:0;padding-bottom:0;height:1px;\" bgcolor=\"aeaeae\"></td>\n";
							echo "\t\t\t\t\t\t\t</tr>\n";

							foreach ( $row[0] as $key => $value )
							{
								echo "\t\t\t\t\t\t\t<tr valign=\"top\" bgcolor=\"".( $bgcolor = $bgcolor == 'E1E1E1' ? 'BFBFBF' : 'E1E1E1' )."\">\n";
								echo "\t\t\t\t\t\t\t\t<td>> <a>$key</a></td>\n";
								echo "\t\t\t\t\t\t\t\t<td>";

								switch ( $cols[$key]['type'] )
								{
									case 'int':  echo 'Integer'; break;
									case 'bool': echo 'Boolean'; break;
									case 'enum': echo 'ENUM'; break;
									default:     echo ucfirst($cols[$key]['type']); break;
								}

								if ( $cols['primary'] == $key )
								{
									echo "<br><font size=\"1\" color=\"firebrick\">- Primary Key</font></b>";
								}
								if ( $cols[$key]['auto_increment'] == 1 )
								{
									echo "<br><font size=\"1\">- Auto Increment</font></b>";
								}
								if ( $cols[$key]['permanent'] == 1 )
								{
									echo "<br><font size=\"1\">- Permanent</font></b>";
								}

								echo "</td>\n";
								echo "\t\t\t\t\t\t\t\t<td>";

								if ( $cols[$key]['type'] == 'text' )
								{
									echo "<textarea style=\"overflow: auto\" name=\"values[$key]\" rows=\"8\" cols=\"65\"".( $cols[$key]['permanent'] == 1 ? ' disabled' : '' ).">";
									echo htmlentities($value);
									echo "</textarea>";
								}

								elseif ( $cols[$key]['type'] == 'bool' )
								{
									echo "<select name=\"values[$key]\" class=\"sel\"".( $cols[$key]['permanent'] == 1 ? ' disabled' : '' ).">";
									echo "<option value=\"0\"".  ( $value == 0 ? ' selected' : '' ).">0</option>";
									echo "<option value=\"1\"".  ( $value == 1 ? ' selected' : '' ).">1</option></select>";
								}

								elseif ( $cols[$key]['type'] == 'date' )
								{
									echo "<input name=\"values[$key]\" size=\"66\" type=\"text\" value=\"".time()."\"".( $cols[$key]['permanent'] == 1 ? ' disabled' : '' ).">";
								}

								elseif ( $cols[$key]['type'] == 'enum' )
								{
									echo "<select name=\"values[$key]\" class=\"sel\"".( $cols[$key]['permanent'] == 1 ? ' disabled' : '' ).">";

									foreach ( $cols[$key]['enum_val'] as $key1 => $value1 )
									{
										echo "<option value=\"".htmlentities($value1)."\" ".( $value1 == $value ? ' selected' : '' ).">".htmlentities($value1)."</option>";
									}

									echo "</select>";
								}

								else
								{
									echo "<input name=\"values[$key]\" size=\"66\" type=\"text\" value=\"".htmlentities($value)."\"".( ( $cols[$key]['permanent'] == 1 || $cols[$key]['auto_increment'] == 1 ) ? ' disabled' : '' ).">";
								}

								echo "</td>\n";
								echo "\t\t\t\t\t\t\t</tr>\n";
							}

							echo "\t\t\t\t\t\t\t<tr>\n";
							echo "\t\t\t\t\t\t\t\t<td align=\"center\" colspan=\"3\"><br>";
							echo "<input type=\"submit\" value=\"Save Changes\"> ";
							echo "<input type=\"reset\" value=\"Reset Form\" onClick=\"if ( confirm('Are you sure you want to reset the form?') != 1 ) return false;\">";
							echo "</td>\n";
							echo "\t\t\t\t\t\t\t</tr>\n";
							echo "\t\t\t\t\t\t</table>\n";
						}
						?>
					</td>
				</tr>
			</table>
		</td>
	</tr>
</table>

<?php
include('./includes/footer.php');
?>