<?php
setDefault('$_GET["s"]', 0);
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

						<form name="form1" method="post" action="index.php?page=search&db=<?php echo $_GET['db']; ?>&table=<?php echo $_GET['table']; ?>&finish=1" onsubmit="checkNegatedFuncs();">
						<input type="hidden" name="erow" value="-1">
						<input type="hidden" name="whereClause" value="">
					</td>
					<td valign="top">
						<b><font size="5">Searching table <i><?php echo $_GET['db'];?>.<?php echo $_GET['table'];?></i></font><br />
						   <font size="2">Viewing search results</font></b>
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

						if ( !isset($_SESSION['txtsqladmin']['search']['results']) )
						{
							$timetook = getTime();
							$rows = execute('select', array(
								'db'    => $_GET['db'],
								'table' => $_GET['table'],
								'where' => $whereClause
							));
							$timetook = getTime() - $timetook;

							$numrows = count($rows);
							$_SESSION['txtsqladmin']['search']['results']  = $rows;
							$_SESSION['txtsqladmin']['search']['clause']   = $whereClause;
							$_SESSION['txtsqladmin']['search']['numrows']  = $numrows;
							$_SESSION['txtsqladmin']['search']['timetook'] = $timetook;
						}
						else
						{
							$rows        = $_SESSION['txtsqladmin']['search']['results'];
							$whereClause = $_SESSION['txtsqladmin']['search']['clause'];
							$numrows     = $_SESSION['txtsqladmin']['search']['numrows'];
							$timetook    = $_SESSION['txtsqladmin']['search']['timetook'];
						}

						$cols    = execute('describe', array(
							'db'    => $_GET['db'],
							'table' => $_GET['table']
						));
						$indent   = "\t\t\t\t\t\t";
						$stop     = ( $_GET['s'] + 25 > $numrows ) ? $numrows : $_GET['s'] + 25;
						$rows     = array_splice($rows, $_GET['s'], 25);
						$pages    = ceil($numrows / 25);
						$onPage   = round($_GET['s'] / 26);
						$where    = constructWhere($rows, $cols);
						$bgcolor  = 'BFBFBF';
						$timetook = round($timetook, 4);

						echo "\n";
						echo "{$indent}<table width=\"100%\" cellspacing=\"1\" cellpadding=\"3\" border=\"0\">\n";
						echo "{$indent}\t<tr>\n";
						echo "{$indent}\t\t<td align=\"left\"><b>Your Query</b>: <i>\"".htmlentities(implode(' ', $whereClause))."\"</i> returned {$numrows} record(s)<br />";
						echo "Search took {$timetook} seconds</td>\n";
						echo "{$indent}\t\t<td align=\"right\">Go to page <select class=\"sel\" onchange=\"document.location.href='index.php?page=search_results&db={$_GET['db']}&table={$_GET['table']}&s=' + this.value\">";
						echo "<script>createSelectPage({$pages}, {$onPage}, {$numrows}, 25);</script>";
						echo "</select><br>\n";
						echo "{$indent}\t\t\tShowing ".( $_GET['s'] + 1 )." to {$stop} of {$numrows}</td>\n";
						echo "{$indent}\t</tr>\n";
						echo "{$indent}</table>\n";

						if ( !empty($rows) )
						{
							echo "{$indent}<table width=\"100%\" cellspacing=\"1\" cellpadding=\"3\" border=\"0\">\n";
							echo "{$indent}\t<tr align=\"center\">\n";
							echo "{$indent}\t\t<td colspan=\"2\"></td>\n";

							foreach ( $cols as $key => $value )
							{
								if ( $key != 'primary' )
								{
									echo "{$indent}\t\t<td><b>";
									echo $cols['primary'] == $key ? '<u><font color="firebrick" style="cursor: default;" title="Primary Key">'.$key.'</font></u>' : $key;
									echo "</b></td>\n";
								}
							}

							echo "{$indent}\t</tr>\n";
							echo "{$indent}\t<tr>\n";
							echo "{$indent}\t\t<td colspan=\"".( count($cols) + 1 )."\" style=\"padding-top:0;padding-bottom:0;height:1px;\" bgcolor=\"aeaeae\">\n";
							echo "{$indent}\t</tr>\n";

							foreach ( $rows as $key => $value )
							{
								echo "{$indent}\t<tr bgcolor=\"".( $bgcolor = $bgcolor == 'E1E1E1' ? 'BFBFBF' : 'E1E1E1' )."\">\n";
								echo "{$indent}\t\t<td align=\"center\"><a href=\"javascript: submitTableEditForm('edit', whereClauses[{$key}], '{$_GET['db']}', '{$_GET['table']}', $key);\"><img src=\"images/small_edit.png\" title=\"Edit Row\" border=\"0\"></a></td>\n";
								echo "{$indent}\t\t<td align=\"center\"><a href=\"javascript: submitTableEditForm('drop', whereClauses[{$key}], '{$_GET['db']}', '{$_GET['table']}', $key);\"><img src=\"images/small_drop.gif\" title=\"Drop Row\" border=\"0\"></a></td>\n";

								foreach ( $value as $key1 => $value1 )
								{
									echo "{$indent}\t\t<td>";
									echo cutString(htmlentities($value1), 35);
									echo "</td>\n";
								}

								echo "{$indent}\t</tr>\n";
							}
							echo "{$indent}\t<tr>\n";
							echo "{$indent}\t\t<td colspan=\"".( count($cols) + 1 )."\" align=\"center\">\n";

							if ( $_GET['s'] > 0 )
							{
								$last = ( $_GET['s'] - 26 < 0 ) ? 0 : $_GET['s'] - 26;
								echo "\t\t\t\t\t\t";
								echo "<input type=\"button\" value=\"Last Page\" onclick=\"document.location.href='index.php?page=search_results&db={$_GET['db']}&table={$_GET['table']}&s={$last}'\">\n";
							}

							if ( $_GET['s'] + 25 < $numrows )
							{
								$next = $_GET['s'] + 26;
								echo "\t\t\t\t\t\t";
								echo "<input type=\"button\" value=\"Next Page\" onclick=\"document.location.href='index.php?page=search_results&db={$_GET['db']}&table={$_GET['table']}&s={$next}'\">\n";
							}

							echo "{$indent}\t\t</td>\n";
							echo "{$indent}</table>\n";
							echo "{$where}";
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