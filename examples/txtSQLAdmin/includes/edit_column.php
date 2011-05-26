<?php
setDefault('$_GET["column"]');
setDefault('$_POST["finish"]');

if ( $_POST['finish'] == 1 )
{
	if ( empty($_POST['column']) || empty($_POST['cols'][0]['name']) )
	{
		error('You forgot to input the column\'s name', E_USER_ERROR);
	}

	setDefault('$_POST["cols"][0]["max"]', 0);
	setDefault('$_POST["cols"][0]["default"]');
	setDefault('$_POST["cols"][0]["type"]', 'string');
	setDefault('$_POST["cols"][0]["auto_increment"]', 0);
	setDefault('$_POST["cols"][0]["permanent"]', 0);

	$_POST['cols'][0]['auto_increment'] = str_replace('on', 1, $_POST['cols'][0]['auto_increment']);
	$_POST['cols'][0]['permanent']      = str_replace('on', 1, $_POST['cols'][0]['permanent']);

	if ( $_POST['cols'][0]['type'] == 'enum' )
	{
		if ( !empty($_POST['cols'][0]['default']) )
		{
			$_POST['cols'][0]['default'] = str_replace(array('%', '\,'), array('%%PERCENT%%', '%%COMMA%%'), $_POST['cols'][0]['default']);
			$_POST['cols'][0]['default'] = explode(',', $_POST['cols'][0]['default']);

			foreach ( $_POST['cols'][0]['default'] as $key => $value )
			{
				$_POST['cols'][0]['default'][$key] = str_replace(array('%%PERCENT%%', '%%COMMA%%'), array('%', ','), $_POST['cols'][0]['default'][$key]);
			}

			$_POST['cols'][0]['enum_val'] = $_POST['cols'][0]['default'];
			$_POST['cols'][0]['default']  = '';
		}
	}

	$name = $_POST['cols'][0]['name'];
	unset($_POST['cols'][0]['name']);

	execute('alter table', array(
		'db'     => $_GET['db'],
		'table'  => $_GET['table'],
		'name'   => $_POST['column'],
		'values' => $_POST['cols'][0],
		'action' => 'modify',
	));


	if ( $_POST['cols'][0]['name'] != $_POST['column'] )
	{
		execute('alter table', array(
			'db'     => $_GET['db'],
			'table'  => $_GET['table'],
			'name'   => $_POST['column'],
			'action' => 'rename col',
			'values' => array(
				'name' => $name
			)
		));
	}

	header('Location: index.php?page=table_prop&db='.$_GET['db'].'&table='.$_GET['table']);
	exit;
}
else
{
	include('./includes/header.php');

	$columns =
	$sql->describe(array(
		'db'    => $_GET['db'],
		'table' => $_GET['table']
	));

	if ( !isset($columns[$_GET['column']]) )
	{
		error('Column "'.$_GET['column'].'" doesn\'t exist', E_USER_ERROR);
	}
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

						<form name="form1" method="post" action="index.php?page=table_prop&action=edit_column&db=<?php echo $_GET['db']; ?>&table=<?php echo $_GET['table']; ?>" onsubmit="checkCreateTableForm(); return false;">
						<input type="hidden" name="finish" value="1">
						<input type="hidden" name="column" value="<?php echo $_GET['column']; ?>">
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
								<td><b>Column Name</b></td>
								<td><b>Maximum Length</b></td>
								<td><b>Default value</b></td>
								<td><b>Col Type</b></td>
								<td><b>Auto</b></td>
								<td><b>Permanent</b></td>
							</tr>
							<tr>
								<td colspan="6" style="padding-top:0;padding-bottom:0;height:1px;" bgcolor="aeaeae">
								</td>
							</tr>
							<tr bgcolor="#E1E1E1" align="center"><?php
							echo "\n";
							$selectBox = '';
							$types = array('string', 'text', 'int', 'bool', 'enum', 'date');

							foreach ( $types as $key => $value )
							{
								$selectBox .= "<option value=\"{$value}\"".( $columns[$_GET['column']]['type'] == $value ? ' selected' : '' ).">{$value}";
							}

							if ( $columns[$_GET['column']]['type'] == 'enum' )
							{
								foreach ( $columns[$_GET['column']]['enum_val'] as $key => $value )
								{
									$columns[$_GET['column']]['enum_val'][$key] = str_replace(',', '\,', $value);
								}
								$columns[$_GET['column']]['default'] = join(',', $columns[$_GET['column']]['enum_val']);
							}
							?>
								<td><input type="text" name=cols[0][name] size="15" value="<?php echo $_GET['column']; ?>"></td>
								<td><input type="text" name=cols[0][max] size="15" value="<?php echo $columns[$_GET['column']]['max']; ?>"></td>
								<td><input type="text" name=cols[0][default] size="15" value="<?php echo $columns[$_GET['column']]['default']; ?>"></td>
								<td><select class="sel" name="cols[0][type]" onchange="checkENUM(this.value, 'cols[0][default]')"><?php echo $selectBox; ?></select></td>
								<td><input type="checkbox" name=cols[0][auto_increment]<?php echo $columns[$_GET['column']]['auto_increment'] == 1 ? ' checked' : ''; ?> style="background-color: #E1E1E1; border:0px solid;"></td>
								<td><input type="checkbox" name=cols[0][permanent]<?php echo $columns[$_GET['column']]['permanent'] == 1 ? ' checked' : ''; ?> style="background-color: #E1E1E1; border:0px solid;"></td>
							</tr>
							<tr>
								<td colspan="6" align="center"><input type="submit" value="Submit Changes"></td>
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