<?php
setDefault('$_GET["export"]', -1);

if ( !empty($_GET['db']) && !empty($_GET['table']) )
{
	require('./includes/export_table.php');
	exit;
}

elseif ( $_GET['export'] == 1 )
{
	foreach ( $_POST as $key => $value )
	{
		if ( is_string($value) && $key != 'line_ending' && $key != 'col_escape' && $key != 'col_enclose' && $key != 'col_seperator' )
		{
			$_POST[$key] = stripslashes($value);
		}
	}

	echo ( $_POST['download_output'] != 'on' ) ? '<pre>' : '';
	echo "############################################################################\n";
	echo "# txtSQL data dump of database: {$_GET['db']}\n";
	echo "############################################################################\n";

	$_POST['line_ending'] = str_replace(array('\n', '\r', '\t'),
					    array("\n", "\r", "\t"),
					    $_POST['line_ending']);

	foreach ( $_POST['export_tables'] as $key => $value )
	{
		if ( isset($_POST['offset']) )
		{
			$rows = execute('select', array(
				'db'    => $_GET['db'],
				'table' => $key,
				'limit' => array($_POST['offset'][1], $_POST['offset'][0] + $_POST['offset'][1] - 1)
			));
		}
		else
		{
			$rows = execute('select', array(
				'db'    => $_GET['db'],
				'table' => $key
			));
		}

		$col_count = count($value1) - 1;
		$row_count = count($rows) - 1;
		if ( empty($rows) )
		{
			echo "!! EMPTY RESULT-SET RETURNED !!\n";
		}
		else
		{
			echo "\n############################################################################\n";
			echo "# txtSQL data dump of table: $key\n";
			echo "############################################################################\n";

			foreach ( $rows as $key1 => $value1 )
			{
				if ( $key1 == 0 && $_POST['show_cols'] == 'on' )
				{
					$implode = $_POST['col_enclose'] . $_POST['col_seperator'] . $_POST['col_enclose'];
					$implode = implode($implode, array_keys($value1));

					echo $_POST['col_enclose'];
					echo $implode;
					echo $_POST['col_enclose'];
					echo $_POST['line_ending'];
				}

				foreach ( $value1 as $key2 => $value2 )
				{
					if ( !empty($_POST['col_enclose']) )
					{
						$value2 = str_replace($_POST['col_enclose'],
								      $_POST['col_escape'].$_POST['col_enclose'],
								      $value2);
					}

					$value1[$key2] = str_replace($_POST['col_seperator'],
								     $_POST['col_escape'].$_POST['col_seperator'],
								     $_POST['col_enclose'].$value2.$_POST['col_enclose']);
				}

				$val = implode($_POST['col_seperator'], $value1).$_POST['line_ending'];
				echo ( $_POST['download_output'] != 'on' ) ? htmlentities($val) : $val;
			}
		}
	}

	if ( $_POST['download_output'] == 'on' )
	{
		header("Content-Type: application/octet-stream");
		header("Content-Disposition: attachment; filename=\"txtsql_{$_GET['db']}_dump.txt\"");
		flush();
		exit;
	}

	echo "</pre>";
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

						<form name="form1" method="post" action="index.php?page=export_database&db=<?php echo $_GET['db']; ?>&export=1">
					</td>
					<td valign="top">
						<b><font size="5">Exporting database <i><?php echo $_GET['db'];?></i></b></font><br />
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
								<td style="padding-top:0;padding-bottom:0;height:1px;" bgcolor="aeaeae">
								</td>
							</tr>
						</table>
						<br/><?php
						echo "\n";
						$indent = "\t\t\t\t\t\t\t";

						if ( !empty($_GET['db']) )
						{
							$tables = execute('show tables', array(
								'db' => $_GET['db']
							));

							if ( empty($tables) )
							{
								echo '<center><font color="red">Warning:</font> There are no tables to export</center>';
							}
							else
							{
								echo "\t\t\t\t\t\t<table width=\"100%\" cellspacing=\"1\" cellpadding=\"3\" border=\"0\">\n";
								echo "{$indent}<tr align=\"center\">\n";
								echo "{$indent}\t<td width=\"200\"><b>Option</b></td>\n";
								echo "{$indent}\t<td><b>Value</b></td>\n";
								echo "{$indent}</tr>\n";
								echo "{$indent}<tr>\n";
								echo "{$indent}\t<td colspan=\"2\" style=\"padding-top:0;padding-bottom:0;height:1px;\" bgcolor=\"aeaeae\">\n";
								echo "{$indent}\t</td>\n";
								echo "{$indent}</tr>\n";
								echo "{$indent}<tr bgcolor=\"E1E1E1\">\n";
								echo "{$indent}\t<td valign=\"top\" width=\"200\"><b>> Export Tables</b></td>\n";
								echo "{$indent}\t<td>\n";

								foreach ( $tables as $key => $value )
								{
									echo "{$indent}\t\t<input type=\"checkbox\" name=\"export_tables[$value]\" style=\"border: 0px solid; background-color: #E1E1E1\" checked> <a href=\"index.php?page=export_database&db={$_GET['db']}&table=$value&export=-1\">$value</a><br>\n";
								}

								echo "{$indent}\t</td>\n";
								echo "{$indent}</tr>\n";
								echo "{$indent}<tr bgcolor=\"#BFBFBF\">\n";
								echo "{$indent}\t<td width=\"200\"><b>> Seperate columns with</b></td>\n";
								echo "{$indent}\t<td> <input type=\"text\" name=\"col_seperator\" value=\"|\"></td>\n";
								echo "{$indent}</tr>\n";
								echo "{$indent}<tr bgcolor=\"#E1E1E1\">\n";
								echo "{$indent}\t<td width=\"200\"><b>> Enclose columns with</b></td>\n";
								echo "{$indent}\t<td> <input type=\"text\" name=\"col_enclose\" value=\"&quot;\"></td>\n";
								echo "{$indent}</tr>\n";
								echo "{$indent}<tr bgcolor=\"#BFBFBF\">\n";
								echo "{$indent}\t<td width=\"200\"><b>> Escape columns with</b></td>\n";
								echo "{$indent}\t<td> <input type=\"text\" name=\"col_escape\" value=\"\\\"></td>\n";
								echo "{$indent}</tr>\n";
								echo "{$indent}<tr bgcolor=\"#E1E1E1\">\n";
								echo "{$indent}\t<td width=\"200\"><b>> Terminate lines with</b></td>\n";
								echo "{$indent}\t<td> <input type=\"text\" name=\"line_ending\" value=\"\\r\\n\"></td>\n";
								echo "{$indent}</tr>\n";
								echo "{$indent}<tr bgcolor=\"#BFBFBF\">\n";
								echo "{$indent}\t<td width=\"200\"><b>> Show column names</b></td>\n";
								echo "{$indent}\t<td> <input type=\"checkbox\" name=\"show_cols\" checked style=\"border: 0px solid; background-color: #BFBFBF\"></td>\n";
								echo "{$indent}</tr>\n";
								echo "{$indent}<tr bgcolor=\"#E1E1E1\">\n";
								echo "{$indent}\t<td width=\"200\"><b>> Download the output</b></td>\n";
								echo "{$indent}\t<td> <input type=\"checkbox\" name=\"download_output\" checked style=\"border: 0px solid; background-color: #E1E1E1\"> </td>\n";
								echo "{$indent}</tr>\n";
								echo "{$indent}<tr>\n";
								echo "{$indent}\t<td colspan=\"2\" align=\"center\">";
								echo "<input type=\"submit\" value=\"Export Database\"> ";
								echo "<input type=\"reset\" onclick=\"if ( confirm('Are you sure you want to reset the form?') == false ) return false;\"></td>\n";
								echo "{$indent}</tr>\n";
								echo "\t\t\t\t\t\t</table>\n";
							}
						}
						else
						{
							$databases = execute('show databases');

							if ( empty($databases) )
							{
								echo "NO DATABASES TO EXPORT";
							}
							else
							{
								echo "\t\t\t\t\t\t<b>Choose a database to export</b>\n";

								foreach ( $databases as $key => $value )
								{
									echo "\t\t\t\t\t\t<li><a href=\"index.php?page=export_database&db={$value}\">{$value}</a>\n";
								}
							}

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