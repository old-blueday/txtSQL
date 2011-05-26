<?php
setDefault('$_GET["action"]');
setDefault('$_GET["newcolname"]');
setDefault('$_GET["newtablename"]');

// Rename the table collectively
if ( $_GET['action'] == 'rename_table' )
{
	execute('alter table', array(
		'db'     => $_GET['db'],
		'table'  => $_GET['table'],
		'name'   => $_GET['newtablename'],
		'action' => 'rename table'
	));

	header('Location: index.php?page=table_prop&db='.$_GET['db'].'&table='.$_GET['newtablename']);
	exit;
}

// Insert a column
elseif ( $_GET['action'] == 'insert' )
{
	require('./includes/insert_column.php');
	exit;
}

// Edit a column
elseif ( $_GET['action'] == 'edit_column' )
{
	require('./includes/edit_column.php');
	exit;
}

// Drop a column
elseif ( $_GET['action'] == 'drop_column' )
{
	require('./includes/drop_column.php');
	exit;
}

// Remove a primary key
elseif ( $_GET['action'] == 'dropkey' )
{
	require('./includes/dropkey.php');
	exit;
}

// Add a primary key
elseif ( $_GET['action'] == 'addkey' )
{
	require('./includes/addkey.php');
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

						<form name="form1" method="get" action="index.php">
						<input type="hidden" name="page" value="table_prop">
						<input type="hidden" name="db" value="<?php echo $_GET['db']; ?>">
						<input type="hidden" name="table" value="<?php echo $_GET['table']; ?>">
						<input type="hidden" name="action" value="insert">
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
								<td><b>Column</b></td>
								<td><b>Maximum Length</b></td>
								<td><b>Default</b></td>
								<td><b>Type</b></td>
								<td><b>Auto Increment</b></td>
								<td><b>Permanent</b></td>
								<td colspan="3"><b>Action</b></td>
							</tr>
							<tr>
								<td colspan="9" style="padding-top:0;padding-bottom:0;height:1px;" bgcolor="aeaeae">
								</td>
							</tr><?
							echo "\n";

							$bgcolor = 'BFBFBF';
							$columns =
							execute('describe', array(
								'db'    => $_GET['db'],
								'table' => $_GET['table']
							));

							foreach ( $columns as $key => $value )
							{
								if ( $key != 'primary' )
								{
									$name           = $key;
									$max            = $value['max'] == 0            ? '<i>no limit</i>'                                : $value['max'];
									$default        = empty($value['default'])      ? '<i>null</i>'                                    : cutString($value['default'], 13);
									$auto_increment = $value['auto_increment'] == 1 ? '<b><font color="firebrick">x</font></b>'        : '-';
									$permanent      = $value['permanent'] == 1      ? '<b><font color="firebrick">x</font></b>'        : '-';
									$type           = $value['type'];

									if ( strtolower($type) == 'enum' )
									{
										$default = $value['enum_val'][count($value['enum_val']) - 1];
									}
									if ( $columns['primary'] == $key )
									{
										$key = '<u><font color="firebrick" title="Primary Key" style="cursor: default">';
										$key .= $name.'</font></u>';
									}

									echo "\t\t\t\t\t\t\t<tr align=\"center\" bgcolor=\"#".( $bgcolor = $bgcolor === 'BFBFBF' ? 'E1E1E1' : 'BFBFBF' )."\">\n";
									echo "\t\t\t\t\t\t\t\t<td align=\"left\"><b>> $key</b></td>\n";
									echo "\t\t\t\t\t\t\t\t<td>$max</td></td>\n";
									echo "\t\t\t\t\t\t\t\t<td>$default</td>\n";
									echo "\t\t\t\t\t\t\t\t<td>$type</td>\n";
									echo "\t\t\t\t\t\t\t\t<td>$auto_increment</td>\n";
									echo "\t\t\t\t\t\t\t\t<td>$permanent</td>\n";
									echo "\t\t\t\t\t\t\t\t<td><a href=\"index.php?page=table_prop&action=edit_column&db={$_GET['db']}&table={$_GET['table']}&column={$name}\"><img src=\"images/small_edit.png\" border=\"0\" alt=\"Edit Field, {$name}\"></a></td>\n";
									echo "\t\t\t\t\t\t\t\t<td><a href=\"javascript: drop_column('{$name}', '{$_GET['table']}', '{$_GET['db']}');\"><img src=\"images/small_drop.gif\" border=\"0\" alt=\"Drop Field, {$name}\"></a></td>\n";
									echo "\t\t\t\t\t\t\t\t<td>";

									if ( $value['auto_increment'] == 1 && $value['type'] == 'int' )
									{
										echo "<a href=\"index.php?page=table_prop&action=".( $columns['primary'] == $name ? 'drop' : 'add' )."key&db={$_GET['db']}&table={$_GET['table']}&column={$name}\"><img src=\"images/small_primary.png\" border=\"0\" alt=\"".( $columns['primary'] == $name ? 'Remove' : 'Set as' )." primary key, {$name}\"></a>";
									}
									else
									{
										echo "<img src=\"images/small_noprimary.png\" border=\"0\">";
									}

									echo "</td>\n";
									echo "\t\t\t\t\t\t\t</tr>\n";
								}
							}
							?>
							<tr>
								<td colspan="9" align="left"><b>Total</b>: <?php echo count($columns) - 1; ?> Column(s)</td>
							</tr>
						</table>

						<br>
						<hr size="2" color="#B0B0B0" width="100%" align="center">

						<table width="100%" cellspacing="0" cellpadding="0" border="0">
							<tr>
								<td valign="top">
									<fieldset style="padding: 5; border: 1px solid; width: 275px; border-color: #B0B0B0; height: 70">
									<legend><b>Add New Column</legend>
									<input type="text" name="newcolname">
									After</b>
									<select name="aftercol" class="sel">
									<option value="primary">--Add as first<?php
									$count = 0;
									foreach ( $columns as $key => $value )
									{
										if ( $key != 'primary' )
										{
											echo "\t\t\t\t\t\t\t\t\t<option value=\"$key\"".( $count == count($columns) - 1 ? ' selected' : '').">$key\n";
										}
										$count++;
									}
									?>
									</select><br><br><center>
									<input type="button" class="btn" value="Add Column" onclick="add_column(document.form1.newcolname.value);">
									</center>
									</fieldset>
								</td>
								<td valign="top" align="left">
									<fieldset style="padding: 5; border: 1px solid; width: 275px; border-color: #B0B0B0; height: 70">
									<legend><b>Rename table from <i><?php echo $_GET['table']; ?></i> to</b></legend>
									<input type="text" name="newtablename"><br><br><center>
									<input type="button" class="btn" value="Rename" onclick="rename_tbl(document.form1.newtablename.value);">
									</center>
									</frameset>
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