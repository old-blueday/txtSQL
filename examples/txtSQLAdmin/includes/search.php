<?php
setDefault('$_GET["finish"]');

if ( empty($_GET['db']) )
{
	error('Must specify a database to search', E_USER_ERROR);
}
elseif ( empty($_GET['table']) )
{
	error('Must specify a table to search', E_USER_ERROR);
}
if ( $_GET['finish'] == 1 )
{
	$whereClause = array();
	$search      = $_POST['search'];

	foreach ( $search as $key => $value )
	{
		if ( !empty($value['logOP']) || ( empty($whereClause) && ( !empty($value['val']) || !empty($value['func']) ) ) )
		{
			if ( !empty($whereClause) )
			{
				$whereClause[] = $value['logOP'];
			}

			if ( !empty($value['func']) )
			{
				switch ( strtolower($value['func']) )
				{
					case 'isdir':
					case '!isdir':
					case 'isfile':
					case '!isfile':
					case 'isnumeric':
					case '!isnumeric':
					case 'isstring':
					case '!isstring':
						$whereClause[] = "{$value['func']}({$value['col']})";
						break;
					default:
						$whereClause[] = "{$value['func']}({$value['col']}) {$value['op']} {$value['val']}";
				}
			}
			else
			{
				$whereClause[] = "{$value['col']} {$value['op']} {$value['val']}";
			}
		}
	}

	if ( !empty($whereClause) )
	{
		require('./includes/search_results.php');
		exit;
	}
}

$indent    = "\t\t\t\t\t\t";
$ops       = array('=', '!=', '<', '<=', '>', '>=', '=~', '!~', '?', '!?');
$functions = array('strUpper', 'strLower', 'chop', 'rtrim', 'ltrim', 'trim',
		   'md5', 'stripSlash', 'strLength', 'strReverse', 'ucFirst',
		   'ucWords', 'bin2hex', 'entEncode', 'entDecode', 'soundex',
		   'ceil', 'floor', 'round', 'isNumeric', '!isNumeric', 'isString',
		   '!isString', 'isFile', '!isFile', 'isDir', '!isDir');
unset($_SESSION['txtsqladmin']['search']);
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
					</td>
					<td valign="top">
						<b><font size="5">Viewing database <i><?php echo $_GET['db'];?></i></font><br />
						   <font size="2">Searching table: <?php echo $_GET['table'];?></font></b>
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
						<?php

						$cols = execute('describe', array(
							'db'    => $_GET['db'],
							'table' => $_GET['table']
						));

						echo "<table width=\"100%\" cellspacing=\"1\" cellpadding=\"3\" border=\"0\">\n";
						echo "{$indent}<tr align=\"center\">\n";
						echo "{$indent}\t<td></td>\n";
						echo "{$indent}\t<td><b>Function</b></td>\n";
						echo "{$indent}\t<td><b>Column</b></td>\n";
						echo "{$indent}\t<td><b>Type</b></td>\n";
						echo "{$indent}\t<td><b>Op</b></td>\n";
						echo "{$indent}\t<td><b>Value</b></td>\n";
						echo "{$indent}</tr>\n";
						echo "{$indent}<tr>\n";
						echo "{$indent}\t<td colspan=\"6\" style=\"padding-top:0;padding-bottom:0;height:1px;\" bgcolor=\"aeaeae\">\n";
						echo "{$indent}\t</td>\n";
						echo "{$indent}</tr>\n";

						$c = 0;
						foreach ( $cols as $key => $value )
						{
							if ( $key != 'primary' )
							{
								if ( $value['max'] == 0 )
								{
									$value['max'] = '';
								}

								echo "{$indent}<tr bgcolor=\"#".( $bgcolor = $bgcolor == 'E1E1E1' ? 'BFBFBF' : 'E1E1E1' )."\">\n";
								echo "{$indent}\t<td align=\"center\">";
								echo "<select class=\"sel\" name=\"search[{$c}][logOP]\"".( key($cols) == 'primary' ? ' disabled' : '' )."><option value=\"\"></option><option value=\"AND\">and</option><option value=\"OR\">or</option><option value=\"XOR\">xor</option></select>";
								echo "</td>\n";
								echo "{$indent}\t<td>";
								echo "<select class=\"sel\" name=\"search[{$c}][func]\" onchange=\"checkFunc(this.value, this);\"><option value=\"\"></option>";
								next($cols);

								foreach ( $functions as $key1 => $value1 )
								{
									echo "<option value=\"{$value1}\">{$value1}</option>";
								}

								echo "</select></td>\n";
								echo "{$indent}\t<td><b> {$key}</b></td>\n";
								echo "{$indent}\t<td>{$value['type']}({$value['max']})</td>\n";
								echo "{$indent}\t<td><input type=\"hidden\" name=\"search[{$c}][col]\" value=\"{$key}\"><select onchange=\"document.form1.elements['search[{$c}][val]'].focus();document.form1.elements['search[{$c}][val]'].select();\" class=\"sel\" name=\"search[{$c}][op]\">";

								foreach ( $ops as $key1 => $value1 )
								{
									echo "<option value=\"{$value1}\">{$value1}</option>";
								}

								echo "</select>\n{$indent}\t<td>";

								if ( $value['type'] == 'enum' )
								{
									echo "<select class=\"sel\" name=\"search[{$c}][val]\"><option value=\"\"></option>";

									foreach ( $value['enum_val'] as $key => $value )
									{
										echo "<option value=\"".htmlentities($value)."\">".htmlentities($value)."</option>";
									}

									echo "</select>";
								}
								else
								{
									echo "<input type=\"text\" size=\"35\" name=\"search[{$c}][val]\">";
								}

								echo "</td>\n";
								echo "{$indent}</tr>\n";
							}
							$c++;
						}

						echo "{$indent}<tr>\n";
						echo "{$indent}\t<td colspan=\"6\" align=\"center\">";
						echo "<br><input type=\"submit\" value=\"Search\"> <input type=\"reset\" value=\"Reset\" onclick=\"if ( confirm('Are you sure you want to reset the form?') != true ) return false;\"></td>\n";
						echo "{$indent}</tr>\n";
						echo "\t\t\t\t\t</table>\n";
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