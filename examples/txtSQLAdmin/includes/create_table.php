<?php
setDefault('$_GET["action"]');
setDefault('$_GET["columns"]');
setDefault('$_POST["primary"]', -1);
$_GET['columns'] = ( int ) $_GET['columns'];

if ( $_GET['action'] == 'finish' )
{
	$colsToInsert = array();

	if ( empty($_POST['cols']) || !is_array($_POST['cols']) )
	{
		error('Invalid column list given', E_USER_ERROR);
	}
	else
	{
		foreach ( $_POST['cols'] as $key => $value )
		{
			if ( !isset($value['name']) )
			{
				error('Invalid column list given; no name for column '.( $key + 1 ), E_USER_ERROR);
			}

			$colsToInsert[$value['name']] = array(
				'enum_val'       => array(),
				'default'        => empty($value['default'])        ? ''       : $value['default'],
				'type'           => empty($value['type'])           ? 'string' : $value['type'],
				'max'            => empty($value['max'])            ? 0        : $value['max'],
				'permanent'      => isset($value['permanent'])      ? 1        : 0,
				'auto_increment' => isset($value['auto_increment']) ? 1        : 0,
				'primary'        => $_POST['primary'] == $key + 1   ? 1        : 0
			);

			if ( !empty($value['type']) && $value['type'] == 'enum' )
			{
				if ( !empty($value['default']) )
				{
					$value['default'] = str_replace(array('%', '\,'), array('%%PERCENT%%', '%%COMMA%%'), $value['default']);
					$value['default'] = explode(',', $value['default']);

					foreach ( $value['default'] as $key1 => $value1 )
					{
						$value['default'][$key1] = str_replace(array('%%PERCENT%%', '%%COMMA%%'), array('%', ','), $value1);
					}

					$colsToInsert[$value['name']]['default']  = '';
					$colsToInsert[$value['name']]['enum_val'] = $value['default'];
				}
			}
		}
	}

	/* Columns have been constructed, now insert them */
	execute('create table', array(
		'db'      => $_POST['db'],
		'table'   => $_POST['table'],
		'columns' => $colsToInsert,
	));

	header('Location: index.php?page=table_prop&db='.$_POST['db'].'&table='.$_POST['table']);
	exit;
}


/* Do some error checking against the number of columns and the table name */
include('./includes/header.php');
if ( $_GET['columns'] <= 0 )
{
	error('Invalid number of columns; only positive numbers!', E_USER_ERROR);
}
elseif ( $sql->table_exists($_GET['table'], $_GET['db']) )
{
	error('Table, \''.$_GET['table'].'\', already exists!', E_USER_ERROR);
}
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

						<form method="post" name="form1" action="index.php?page=create_table&action=finish" onsubmit="checkCreateTableForm(<?php echo $_GET['columns']; ?>); return false;">
						<input type="hidden" name="db" value="<?php echo $_GET['db'];?>">
						<input type="hidden" name="table" value="<?php echo $_GET['table'];?>">
					</td>
					<td valign="top">
						<b><font size="5">Viewing database <i><?php echo $_GET['db'];?></i></font><br />
						   <font size="2">Creating Table: <?php echo $_GET['table'];?></font></b>
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
									   <a href="index.php?page=<?php echo $sql->isLocked($_GET['db']) ? 'lock' : 'unlock';?>db&db=<?php echo $_GET['db']; ?>&go=index.php?page=view_database%26amp;db={$_GET['db']}"><?php echo $sql->isLocked($_GET['db']) ? 'Unlock' : 'Lock'; ?> DB</a>
									</b>
								</td>
							</tr>
							<tr>
								<td style="padding-top:0;padding-bottom:0;height:1px;" bgcolor="aeaeae">
								</td>
							</tr>
						</table>
						<br/>

						<table width="100%" cellspacing="1" cellpadding="3">
							<tr align="center">
								<td width="5"><b>Primary</b></td>
								<td><b>Column Name</b></td>
								<td><b>Maximum Length</b></td>
								<td><b>Default value</b></td>
								<td><b>Col Type</b></td>
								<td><b>Auto</b></td>
								<td><b>Permanent</b></td>
							</tr>
							<tr>
								<td colspan="7" style="padding-top:0;padding-bottom:0;height:1px;" bgcolor="aeaeae">
								</td>
							</tr><?
							echo "\n";

							$bg             = 'BFBFBF';
							$colTypesSelect = '<option value="string">String<option value="text">Text<option value="int">Integer<option value="bool">Boolean<option value="enum">ENUM<option value="date">Date';
							for ( $i = 0; $i < $_GET['columns']; $i++ )
							{
								echo "\t\t\t\t\t\t\t<tr align=\"center\" bgcolor=\"#".( $bg = ( $bg == 'E1E1E1' ) ? 'BFBFBF' : 'E1E1E1' )."\">\n";
								echo "\t\t\t\t\t\t\t\t<td><input type=\"radio\" name=\"primary\" value=\"".( $i + 1 )."\" style=\"background-color: #$bg; border:0px solid;\"></td>\n";
								echo "\t\t\t\t\t\t\t\t<td><input type=\"text\" size=\"15\" name=\"cols[$i][name]\" tabindex=\"".( $i + 1 )."\"></td>\n";
								echo "\t\t\t\t\t\t\t\t<td><input type=\"text\" size=\"15\" name=\"cols[$i][max]\"></td>\n";
								echo "\t\t\t\t\t\t\t\t<td><input type=\"text\" size=\"15\" name=\"cols[$i][default]\"></td>\n";
								echo "\t\t\t\t\t\t\t\t<td><select class=\"sel\" name=\"cols[$i][type]\" onchange=\"checkENUM(this.value, 'cols[$i][default]')\">$colTypesSelect</select></td>\n";
								echo "\t\t\t\t\t\t\t\t<td><input type=\"checkbox\" name=\"cols[$i][auto_increment]\" style=\"border: 0px solid; background-color: #$bg\"></td>\n";
								echo "\t\t\t\t\t\t\t\t<td><input type=\"checkbox\" name=\"cols[$i][permanent]\" style=\"border: 0px solid; background-color: #$bg\"></td>\n";
								echo "\t\t\t\t\t\t\t</tr>\n";
							}
							?>
							<tr>
								<td colspan="7" align="left"><?php echo $_GET['columns'];?> Column(s)</td>
							</tr>
							<tr align="center">
								<td colspan="7">
									<input type="submit" class="btn" value="Create Table '<?php echo cutString($_GET['table'], 13);?>'">
									<input type="reset" class="btn" value="Reset Form" onclick="if ( confirm('Are you sure you want to reset the form?') == 1 ) document.form1.reset(); return false;">
								</td>
							</tr>
						</table>
					</td>
				</tr>
			</table>
		</td>
	</tr>
</table>

<?php
include('./includes/footer.php');
?>