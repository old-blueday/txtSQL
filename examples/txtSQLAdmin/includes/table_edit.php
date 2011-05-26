<?php
setDefault('$_GET["action"]');
setDefault('$_GET["s"]', 0);
setDefault('$_GET["scolumn"]');
setDefault('$_GET["sdir"]', 'ASC');

if ( $_GET['action'] == 'edit_row' )
{
	require('./includes/edit_row.php');
	exit();
}

elseif ( $_GET['action'] == 'drop_row' )
{
	require('./includes/drop_row.php');
	exit();
}

elseif ( $_GET['action'] == 'insert_row' )
{
	require('./includes/insert_row.php');
	exit();
}

elseif ( $_GET['action'] == 'empty_table' )
{
	require('./includes/empty_table.php');
	exit();
}

elseif ( $_GET['action'] == 'drop_table' )
{
	require('./includes/drop_table.php');
	exit();
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

						<form name="form1" method="post" action="index.php?page=table_edit&db=<?php echo $_GET['db']; ?>&table=<?php echo $_GET['table']; ?>&action=edit&s=<?php echo $_GET['s']; ?>">
						<input type="hidden" name="whereClause" value="">
						<input type="hidden" name="s" value="<?php echo $_GET['s']; ?>">
						<input type="hidden" name="scolumn" value="<?php echo $_GET['scolumn']; ?>">
						<input type="hidden" name="sdir" value="<?php echo $_GET['sdir']; ?>">
						<input type="hidden" name="erow" value="">
					</td>
					<td valign="top">
						<b><font size="5">Viewing database <i><?php echo $_GET['db'];?></i></font><br />
						   <font size="2">Viewing table structure: <?php echo $_GET['table'];?></font></b>
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
						<br/>

						<table width="98%" cellspacing="1" cellpadding="3" align="center">
							<tr align="center">
								<td align="right" colspan="<?php

							$cols =
							execute('describe', array(
								'db'    => $_GET['db'],
								'table' => $_GET['table']
							));

							$select = array(
								'db'      => $_GET['db'],
								'table'   => $_GET['table'],
								'limit'   => array($_GET['s'], $_GET['s'] + 25)
							);

							echo ( count($cols) + 1 )."\">\n";
							echo "\t\t\t\t\t\t\t\t\t<table width=\"100%\">\n";
							echo "\t\t\t\t\t\t\t\t\t\t<tr align=\"right\" valign=\"top\">\n";
							echo "\t\t\t\t\t\t\t\t\t\t\t<td align=\"left\">\n";
							echo "\t\t\t\t\t\t\t\t\t\t\t\tSort by column: \n";

							echo "\t\t\t\t\t\t\t\t\t\t\t\t";
							echo "<select class=\"sel\" name=\"sortcolumn\">";

							foreach ( $cols as $key => $value )
							{
								if ( $key == 'primary' )
								{
									$key = '';
								}
								echo "<option value=\"".htmlentities($key)."\">".htmlentities($key)."</option>";
							}

							echo "</select>\n";
							echo "\t\t\t\t\t\t\t\t\t\t\t\t<select class=\"sel\" name=\"sortdir\"><option value=\"ASC\">ASC</option><option value=\"DESC\">DESC</option></select>\n";
							echo "\t\t\t\t\t\t\t\t\t\t\t\t<input type=\"button\" onclick=\"document.location.href='index.php?page=table_edit&action=browse_table&db={$_GET['db']}&table={$_GET['table']}&s={$_GET['s']}&sdir=' + document.form1.elements['sortdir'].value + '&scolumn=' + document.form1.elements['sortcolumn'].value;\" value=\"Sort\"><br>\n";

							if ( !empty($_GET['scolumn']) && !empty($_GET['sdir']) )
							{
								echo "\t\t\t\t\t\t\t\t\t\t\t\tSorting by column: {$_GET['scolumn']} ( ".( $_GET['sdir'] == 'ASC' ? 'Ascending' : 'Descending' )." )\n";
							}

							$tableCount = $sql->table_count($_GET['table'], $_GET['db']);
							$pages      = ceil($tableCount / 25);
							$onPage     = round($_GET['s'] / 26);
							$stop       = ( $_GET['s'] + 25  >= $tableCount ) ? $tableCount : $_GET['s'] + 25;
							$indent     = "\t\t\t\t\t\t\t";

							echo "{$indent}\t\t\t\t</td>\n";
							echo "{$indent}\t\t\t\t<td>\n";
							echo "{$indent}\t\t\t\t\tGo to page: <select name=\"s\" onchange=\"document.location.href='index.php?page=table_edit&action=browse_table&db={$_GET['db']}&table={$_GET['table']}&scolumn={$_GET['scolumn']}&sdir={$_GET['sdir']}&s=' + ( this.value );\" class=\"sel\">\n";
							echo "{$indent}\t\t\t\t\t<script>createSelectPage({$pages}, {$onPage}, {$tableCount}, 25);</script>\n";
							echo "{$indent}\t\t\t\t\t</select><br>\n";
							echo "{$indent}\t\t\t\t\tShowing: ".$_GET['s']." to ".$stop." of ".number_format($tableCount)."<BR>\n";
							echo "{$indent}\t\t\t\t</td>\n";
							echo "{$indent}\t\t\t</tr>\n";
							echo "{$indent}\t\t</table>\n";
							echo "{$indent}\t</td>\n";
							echo "{$indent}</tr>\n";
							echo "{$indent}<tr align=\"center\">\n";
							echo "{$indent}\t<td colspan=\"2\">\n";
							echo "{$indent}\t</td>\n";

							if ( !empty($_GET['scolumn']) && !empty($_GET['sdir']) )
							{
								$select['orderby'] = array($_GET['scolumn'], $_GET['sdir']);
							}

							$rows =	execute('select', $select);

							if ( !empty($rows) )
							{
								$where = constructWhere($rows, $cols);

								foreach ( $cols as $key => $value )
								{
									if ( $key != 'primary' )
									{
										$direction = 'ASC';

										if ( !empty($_GET['scolumn']) && !empty($_GET['sdir']) && $_GET['scolumn'] == $key )
										{
											$direction = ( strtoupper($_GET['sdir']) == 'ASC' ) ? 'DESC' : 'ASC';
										}
										echo "\t\t\t\t\t\t\t\t<td><b>";
										echo "<a href=\"index.php?page=table_edit&db={$_GET['db']}&table={$_GET['table']}&s={$_GET['s']}&scolumn={$key}&sdir=$direction\" title=\"Sort Column; $direction\">";
										echo ( $key == $cols['primary'] ) ? "<u><font color=\"firebrick\" title=\"Primary Key\">$key</font></u>" : "$key";
										echo "</b>";

										if ( !empty($_GET['scolumn']) && !empty($_GET['sdir']) && $_GET['scolumn'] == $key )
										{
											echo "<img src=\"images/sort_$direction.gif\" border=\"0\">";
										}

										echo "</a></td>\n";
									}
								}

								echo "\n";
								echo "\t\t\t\t\t\t\t</tr>\n";
								echo "\t\t\t\t\t\t\t<tr>\n";
								echo "\t\t\t\t\t\t\t\t<td colspan=\"".( count($cols) + 1 )."\" style=\"padding-top:0;padding-bottom:0;height:1px;\" bgcolor=\"aeaeae\">\n";
								echo "\t\t\t\t\t\t\t\t</td>\n";
								echo "\t\t\t\t\t\t\t</tr>\n";

								$bgcolor = 'BFBFBF';
								foreach ( $rows as $key => $value )
								{
									echo "\t\t\t\t\t\t\t<tr bgcolor=\"#";
									echo ( isset($_GET['erow']) && $_GET['erow'] == $key ) ? 'ADD8E6' : ( $bgcolor = $bgcolor == 'BFBFBF' ? 'E1E1E1' : 'BFBFBF' );
									echo "\">\n";
									echo "\t\t\t\t\t\t\t\t<td align=\"center\">";
									echo "<a href=\"javascript: submitTableEditForm('edit', whereClauses[$key], '{$_GET['db']}', '{$_GET['table']}', '{$key}');\"><img src=\"images/small_edit.png\" border=\"0\" alt=\"Edit Row\"></a></td>\n";
									echo "\t\t\t\t\t\t\t\t<td align=\"center\">";
									echo "<a href=\"javascript: submitTableEditForm('drop', whereClauses[$key], '{$_GET['db']}', '{$_GET['table']}', '{$key}');\"><img src=\"images/small_drop.gif\" border=\"0\" alt=\"Drop Row\"></a></td>\n";

									foreach ( $value as $key1 => $value1 )
									{
										$alignment = ( $cols[$key1]['type'] == 'int' ) ? 'right' : 'left';

										echo "\t\t\t\t\t\t\t\t";
										echo "<td align=\"$alignment\" width=\""/*.( 1 / ( count($cols) - 1 ) * 100 )."%*/."\">";
										echo cutString(htmlentities($value1), 35);
										echo "</b></td>\n";
									}

									echo "\t\t\t\t\t\t\t</tr>\n";
								}

								echo "\t\t\t\t\t\t</table>\n";
								echo "\t\t\t\t\t\t<center><br>";

								if ( $_GET['s'] > 0 )
								{
									$last = ( $_GET['s'] - 26 < 0 ) ? 0 : $_GET['s'] - 26;
									echo "\t\t\t\t\t\t";
									echo "<input type=\"button\" value=\"Last Page\" onclick=\"document.location.href='index.php?page=table_edit&action=browse_table&db={$_GET['db']}&table={$_GET['table']}&sdir={$_GET['sdir']}&scolumn={$_GET['scolumn']}&s={$last}'\">\n";
								}

								if ( $_GET['s'] + 25 < $sql->table_count($_GET['table'], $_GET['db']) )
								{
									$next = $_GET['s'] + 26;
									echo "\t\t\t\t\t\t";
									echo "<input type=\"button\" value=\"Next Page\" onclick=\"document.location.href='index.php?page=table_edit&action=browse_table&db={$_GET['db']}&table={$_GET['table']}&sdir={$_GET['sdir']}&scolumn={$_GET['scolumn']}&s={$next}'\">\n";
								}

								echo "</center>\n{$where}";
							}
							else
							{
								echo "\t\t\t\t\t\t\t\t</td>\n";
								echo "\t\t\t\t\t\t\t</tr>\n";
								echo "\t\t\t\t\t\t\t<tr>\n";
								echo "\t\t\t\t\t\t\t\t<td style=\"padding-top:0;padding-bottom:0;height:1px;\" bgcolor=\"aeaeae\"></td>\n";
								echo "\t\t\t\t\t\t\t</tr>\n";
								echo "\t\t\t\t\t\t\t<tr>\n";
								echo "\t\t\t\t\t\t\t\t<td align=\"center\" bgcolor=\"#E1E1E1\"><b>Empty Result-Set Returned</b></td>\n";
								echo "\t\t\t\t\t\t\t</tr>\n";
								echo "\t\t\t\t\t\t\t\t<td style=\"padding-top:0;padding-bottom:0;height:1px;\" bgcolor=\"aeaeae\"></td>\n";
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
require('./includes/footer.php');
?>