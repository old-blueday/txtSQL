
						<div align="right"><a href="index.php">Home</a> | <a href="javascript:location.reload()">Refresh</a> | <a href="javascript:window.close()">Close</a></div>

						<table width="100%" cellspacing="0" cellpadding="0" style="border:3px solid;border-color:#cecece;" align="center">
							<tr>
								<td colspan="2">
									<a href="index.php" target=_parent><img src="images/logo.gif" border=0 alt="click here to go to main page"></a><div align=left>
								</td>
							</tr>
							<tr>
								<td background="images/gradient.gif" align="right">
									&nbsp;&nbsp;<select class="sel" name="db" onchange="document.form.page.value='view_database';form.submit()"><option value="">- Choose a DB-</option><?php
									echo "\n";

									if ( empty($_GET['page']) || strtolower($_GET['page']) == 'main' || isset($_GET['db']) )
									{
										$databases = execute('show databases');

										foreach ( $databases as $key => $value )
										{
											$selected = '';
											if ( isset($_GET['db']) && $_GET['db'] == $value )
											{
												$selected = ' SELECTED';
											}

											echo "\t\t\t\t\t\t\t\t\t<option value=\"$value\"$selected>$value</option>\n";
										}
										echo "\t\t\t\t\t\t\t\t\t</select><br />\n";
									}?>
								</td>
							</tr>
						</table>
						<table width="100%">
							<tr>
								<td align="right">
									<a href="javascript:if ( confirm('Are you sure you want to logout?') == 1 ) document.location.href='index.php?action=logout';"><font size="1">Logout</font></a>
								</td>
							</tr>
						</table><?php
						if ( !empty($_GET['db']) )
						{
							echo "\n\t\t\t\t\t\t<hr size=\"2\" color=\"B0B0B0\">\n";
							echo "\t\t\t\t\t\t<b><font color=\"515151\">Tables Found in DB: <a href=\"index.php?page=view_database&db={$_GET['db']}\" style=\"text-decoration:underline;\">{$_GET['db']}</a></font></b><br>\n";
							echo "\t\t\t\t\t\t<hr size=\"2\" color=\"B0B0B0\">\n";

							$tables = execute('show tables', array(
								'db' => $_GET['db']
							));

							if ( empty($tables) )
							{
								echo "\t\t\t\t\t\t<center>-No Tables-</center>";
							}
							else
							{
								foreach ( $tables as $key => $value )
								{
									echo "\t\t\t\t\t\t<a href=\"index.php?page=table_edit&action=browse_table&db={$_GET['db']}&table=$value\"><img src=\"images/small_tbl.gif\" border=\"0\"></a> ";
									echo "<a href=\"index.php?page=table_prop&db={$_GET['db']}&table=$value\">$value</a> <BR>\n";
								}
							}
						}
						?>