function changeDB ()
{
	if ( document.form.db.value != '' )
	{
		document.form.submit();
		return true;
	}
	document.location.href = 'index.php';
}

function drop_db ( db )
{
	if ( confirm('Are you sure you want to drop database "' + db + '"?') == 1 )
	{
		document.location.href = 'index.php?page=drop_db&db=' + db;
	}
}

function empty_tbl ( tbl, db )
{
	if ( confirm('Are you sure you want to empty table "' + db + '.' + tbl + '"?') == 1 )
	{
		document.location.href = 'index.php?page=table_edit&action=empty_table&db=' + db + '&table=' + tbl;
	}
}

function drop_tbl ( tbl, db )
{
	if ( confirm('Are you sure you want to drop table "' + db + '.' + tbl + '"?') == 1 )
	{
		document.location.href = 'index.php?page=table_edit&action=drop_table&db=' + db + '&table=' + tbl;
	}
}

function drop_column ( col, tbl, db )
{
	if ( confirm('Are you sure you want to drop column "' + col + '" inside "' + db + '.' + tbl + '"?') == 1 )
	{
		document.location.href = 'index.php?page=table_prop&action=drop_column&db=' + db + '&table=' + tbl + '&column=' + col;
	}
}

function create_db ( db )
{
	if ( !db.match(/^[A-Za-z0-9_]+$/) )
	{
		alert('Please enter a valid database name (Letters, digits, and underscores)');
		document.form.database.focus();
		document.form.database.select();
		return false;
	}
	document.form1.submit();
}

function create_table ( table, cols )
{
	if ( !table.match(/^[A-Za-z0-9_]+$/) )
	{
		alert('Please enter a valid table name (Letters, digits, and underscores)');
		document.form2.table.focus();
		document.form2.table.select();
		return false;
	}
	else if ( !cols.match(/^[0-9]+$/) )
	{
		alert('Please enter a valid number of columns');
		document.form2.columns.focus();
		document.form2.columns.select();
		return false;
	}
	document.form2.submit();
}

function rename_db ( db )
{
	if ( !db.match(/^[A-Za-z0-9_]+$/) )
	{
		alert('Please enter a valid database name (Letters, digits, and underscores)');
		document.form.newdatabase.focus();
		document.form.newdatabase.select();
		return false;
	}
	document.form.submit();
}

function checkENUM ( val, row )
{
	if ( val == 'enum' )
	{
		alert('Put all of the ENUM values inside the "Default Value" box, and seperate each element with a comma\n\n' +
		      'To get a comma as one of the possible values, backslash it\n\n' +
		      'ie.\n' +
		      '"there is a comma here\\, did you spot it?,value 2,value 3"');

		var length = document.form1.elements.length;
		for ( i = 0; i < length; i++ )
		{
			if ( document.form1.elements[i].name == row )
			{
				document.form1.elements[i].focus();
				break;
			}
		}
	}
}

function checkCreateTableForm ( numCols )
{
	var name    = '';
	var length  = document.form1.elements.length;
	var columns = new Array();

	for ( i = 0; i < length; i++ )
	{
		name  = document.form1.elements[i].name;
		value = document.form1.elements[i].value;


		if ( name.match(/^cols\[[0-9]+\]\[name\]$/) )
		{
			if ( value == '' )
			{
				alert('Forgot to input the column\'s name');
				document.form1.elements[i].focus();
				document.form1.elements[i].select();
				return false;
			}
			else if ( !value.match(/^[A-Za-z0-9_]+$/) )
			{
				alert('Can only have letters, numbers and underscores as a column name');
				document.form1.elements[i].focus();
				document.form1.elements[i].select();
				return false;
			}
			else if ( value.toLowerCase() == 'primary' )
			{
				alert('Cannot have a column name as "primary"');
				document.form1.elements[i].focus();
				document.form1.elements[i].select();
				return false;
			}
			else if ( columns[value] )
			{
				alert('Duplicate column name found!');
				document.form1.elements[i].focus();
				document.form1.elements[i].select();
				return false;
			}
			else
			{
				columns[value] = value;
			}
		}
		else if ( name.match(/^cols\[[0-9]+\]\[max\]$/) )
		{
			if ( ( value != '' && !value.match(/^[0-9]+$/) ) || value < 0 || value > 1000000 )
			{
				alert('Maximum value must be an integer greater than or equal to 0 AND less than 1,000,000');
				document.form1.elements[i].focus();
				document.form1.elements[i].select();
				return false;
			}
		}
		else if ( name.match(/^cols\[[0-9]+\]\[auto_increment\]$/) )
		{
			if ( document.form1.elements[i].checked )
			{
				if ( document.form1.elements[i - 1].value != 'int' )
				{
					alert('auto_increment Fields have to be of type "integer"');
					document.form1.elements[i -1].focus();
					return false;
				}
			}
		}
		else if ( name.match(/^cols\[[0-9]+\]\[type\]$/) )
		{
			if ( value == 'enum' )
			{
				if ( document.form1.elements[i - 1].value == '' )
				{
					alert('Missing ENUM list of values');
					document.form1.elements[i - 1].focus();
					return false;
				}
			}
		}
	}

	primaryChecked = false;
	for ( i = 0; i < numCols; i++ )
	{
		if ( primaryChecked == false )
		{
			if ( document.form1.elements['primary'][i].checked == true )
			{
				if ( document.form1.elements['cols[' + i + '][type]'].value != 'int' )
				{
					alert('Primary key has to be an integer');
					document.form1.elements['cols[' + i + '][type]'].focus();
					return false;
				}
				else if ( document.form1.elements['cols[' + i + '][auto_increment]'].checked == false )
				{
					alert('Primary key has to be auto_increment');
					document.form1.elements['cols[' + i + '][auto_increment]'].checked = true;
					return false;
				}
				primaryChecked = true;
			}
		}
	}

	if ( primaryChecked == false && numCols > 0 )
	{
		if ( confirm('There is no primary key defined. This will disallow any relationship among any other tables. Are you sure you want this? \n( Tip: You can always assign a primary key later )') == false )
		{
			return false;
		}
	}

	document.form1.submit();
}

function add_column ( col )
{
	if ( !col.match(/^[A-Za-z0-9_]+$/) )
	{
		alert('Please enter a valid column name (Letters, digits and underscores)');
		document.form1.newcolname.focus();
		document.form1.newcolname.select();
		return false;
	}
	else
	{
		var length = document.form1.elements[6].options.length;
		for ( i = 0; i < length; i++ )
		{
			if ( document.form1.elements[6].options[i].value.toLowerCase() == col.toLowerCase() )
			{
				if ( col.toLowerCase() == 'primary' )
				{
					alert('Cannot have a column name as "primary"');
				}
				else
				{
					alert('Column "' + col + '" already exists');
				}

				document.form1.newcolname.focus();
				document.form1.newcolname.select();
				return false;
			}
		}
	}
	document.form1.submit();
}

function rename_tbl ( tbl )
{
	if ( !tbl.match(/^[A-Za-z0-9_]+$/) )
	{
		alert('Please enter a valid table name (Letters, digits and underscores)');
		document.form1.newtablename.focus();
		document.form1.newtablename.select();
		return false;
	}
	document.form1.elements[3].value = 'rename_table';
	document.form1.submit();
}

function submitTableEditForm ( action, clause, db, table, erow )
{
	if ( ( action == 'drop' && confirm('Are you sure you want to drop this record?') != false ) || action == 'edit')
	{
		document.form1.elements['erow'].value = erow;
		document.form1.elements['whereClause'].value = clause;
		document.form1.action = 'index.php?page=table_edit&db=' + db + '&table=' + table + '&action=' + action + '_row';
		document.form1.submit();
	}
}

function createSelectPage ( pages, onPage, tableCount, perpage )
{
	var s        = 0;
	var selected = '';

	for ( i = 1; i <= pages; i++ )
	{
		s = i * ( perpage + 1 ) - perpage - 1;

		if ( s > tableCount )
		{
			break;
		}

		selected = ( onPage + 1 == i ) ? ' selected' : '';
		document.write('<option value=\"' + s + '\"' + selected + '>' + i + '</option>');
	}
}

function checkFunc ( val, obj )
{
	var length = document.form1.elements.length;

	for ( i = 0; i < length; i++ )
	{
		if ( document.form1.elements[i] == obj )
		{
			switch ( val )
			{
				case 'isDir':
				case '!isDir':
				case 'isNumeric':
				case '!isNumeric':
				case 'isFile':
				case '!isFile':
				case 'isString':
				case '!isString':
					document.form1.elements[i + 2].disabled = true;
					document.form1.elements[i + 3].disabled = true;
					return true;
				default:
					document.form1.elements[i + 2].disabled = false;
					document.form1.elements[i + 3].disabled = false;
			}
		}
	}
}

function checkNegatedFuncs ()
{
	var length = document.form1.elements.length;

	for ( i = 0; i < length; i++ )
	{
		name = document.form1.elements[i].name;

		if ( name.match(/^search\[\d+\]\[func\]$/) )
		{
			switch ( document.form1.elements[i].value )
			{
				case 'isDir':
				case 'isFile':
				case 'isNumeric':
				case 'isString':
					document.form1.elements[i + 2].selectedIndex = 0;
					document.form1.elements[i + 3].value = '';
			}
		}
	}

	document.form1.submit();
}

function checkPasswords ()
{
	if ( document.form1.elements['newpass'].value != document.form1.elements['newpass1'].value )
	{
		alert('The two new passwords do not match!');
		document.form1.elements['newpass'].focus();
		document.form1.elements['newpass'].select();
		return false;
	}
	else if ( document.form1.elements['newpass'].value == '' )
	{
		if ( confirm('Are you sure you want to have no password?') != true )
		{
			return false;
		}
	}

	document.form1.submit();
}

function checkNewUserForm ()
{
	if ( !document.form1.elements['new_username'].value.match(/^[A-Za-z0-9_]+$/) )
	{
		alert('Invalid username! Only letters, numbers and underscores. Try again');
		document.form1.elements['new_username'].focus();
		document.form1.elements['new_username'].select();
		return false;
	}
	if ( document.form1.elements['new_password'].value == '' )
	{
		if ( confirm('Are you sure you want a blank password?') == false )
		{
			return false;
		}
	}

	document.form1.submit();
}

function getPassForUserDrop ( user )
{
	var pass = prompt("Enter the correct password for this user", '');

	if ( pass != null )
	{
		document.form1.elements['pass'].value = pass;
		document.form1.action = 'index.php?page=users&action=drop&user=' + user;
		document.form1.submit();
	}
}

function getInfoForUserEdit ( user )
{
	var currentpass;
	var newpass;
	var newpass1;

	if ( ( currentpass = prompt("Enter the current password for this user", '') ) != null )
	{
		if ( ( newpass = prompt("Enter a new password", '') ) != null )
		{
			if ( ( newpass1 = prompt("Enter a new password ( confirm )", '') ) != null )
			{
				if ( newpass != newpass1 )
				{
					alert('Two password do not match!');

					if ( confirm('Try Again?') == true )
					{
						getInfoForUserEdit(user);
					}
					else
					{
					}
				}
				else
				{
					document.form1.elements['pass'].value = currentpass;
					document.form1.elements['newpass'].value = newpass;
					document.form1.elements['newpass1'].value = newpass1;
					document.form1.action = 'index.php?page=users&action=edit&user=' + user;
					document.form1.submit();
				}
			}
		}
	}
}