<?php
setDefault('$_POST["finish"]');
setDefault('$_GET["aftercol"]');

if ( $_POST['finish'] == 1 )
{
	setDefault('$_POST["cols"][0]["max"]', 0);
	setDefault('$_POST["cols"][0]["default"]');
	setDefault('$_POST["cols"][0]["type"]', 'string');
	setDefault('$_POST["cols"][0]["auto_increment"]', 0);
	setDefault('$_POST["cols"][0]["permanent"]', 0);
	setDefault('$_POST["aftercol"]', 0);
	setDefault('$_POST["primary"]', 0);

	if ( empty($_POST['cols'][0]['name']) )
	{
		error('Forgot to input the column\'s name', E_USER_ERROR);
	}

	$newcolumn = array(
		'max'            => $_POST['cols'][0]['max'],
		'default'        => $_POST['cols'][0]['default'],
		'type'           => $_POST['cols'][0]['type'],
		'auto_increment' => str_replace('on', 1, $_POST['cols'][0]['auto_increment']),
		'permanent'      => str_replace('on', 1, $_POST['cols'][0]['permanent']),
		'primary'        => str_replace('on', 1, $_POST['primary']),
		'enum_val'       => array()
	);

	if ( strtolower($newcolumn['type']) == 'enum' )
	{
		if ( !empty($newcolumn['default']) )
		{
			$newcolumn['default'] = str_replace(array('%', '\,'), array('%%PERCENT%%', '%%COMMA%%'), $newcolumn['default']);
			$newcolumn['default'] = explode(',', $newcolumn['default']);

			foreach ( $newcolumn['default'] as $key => $value )
			{
				$newcolumn['default'][$key] = str_replace(array('%%PERCENT%%', '%%COMMA%%'), array('%', ','), $newcolumn['default'][$key]);
			}

			$newcolumn['enum_val'] = $newcolumn['default'];
			$newcolumn['default']  = '';
		}
	}

	$results =
	execute('alter table', array(
		'db'     => $_GET['db'],
		'table'  => $_GET['table'],
		'action' => 'insert',
		'name'   => $_POST['cols'][0]['name'],
		'after'  => $_POST['aftercol'],
		'values' => $newcolumn
	));

	header('Location: index.php?page=table_prop&db='.$_GET['db'].'&table='.$_GET['table']);
	exit;
}
else
{
	include('./includes/header.php');

	$columns =
	execute('describe', array(
		'db'    => $_GET['db'],
		'table' => $_GET['table']
	));
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

						<form name="form1" method="post" action="index.php?page=table_prop&db=<?php echo $_GET['db']; ?>&table=<?php echo $_GET['table']; ?>&action=insert" onsubmit="checkCreateTableForm(); return false;">
						<input type="hidden" name="finish" value="1">
						<input type="hidden" name="aftercol" value="<?php echo $_GET['aftercol']; ?>">
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
							</tr>
							<tr bgcolor="#E1E1E1" align="center">
								<td><input type="checkbox" name="primary" <?php echo !empty($columns['primary']) ? 'disabled' : ''; ?> style="border: 0px solid; background-color: #E1E1E1"></td>
								<td><input type="hidden" name=cols[0][name] value="<?php echo $_GET['newcolname']; ?>"><?php echo $_GET['newcolname']; ?></td>
								<td><input type="text" name=cols[0][max] size="15"></td>
								<td><input type="text" name=cols[0][default] size="15"></td>
								<td><select class="sel" name="cols[0][type]" onchange="checkENUM(this.value, 'cols[0][default]')"><option value="string">String<option value="text">Text<option value="int">Integer<option value="bool">Boolean<option value="enum">ENUM<option value="date">Date</select></td>
								<td><input type="checkbox" name=cols[0][auto_increment] size="15" style="border: 0px solid; background-color: #E1E1E1;"></td>
								<td><input type="checkbox" name=cols[0][permanent] size="15" style="border: 0px solid; background-color: #E1E1E1;"></td>
							</tr>
							<tr>
								<td colspan="7" align="center"><input type="submit" value="Add Column"></td>
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