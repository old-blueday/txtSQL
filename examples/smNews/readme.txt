NOTE: This script is only an example, it is meant to assist you in getting started
with txtSQL.

Installation
===================================================================================
To install the script, first create a database using txtSQLAdmin that smnews can
use. Then, run install.php in your browser.

Adding news
===================================================================================
If you want to add articles, you can do so by utilizing the txtSQLAdmin script (also
in the examples directory). Just open up txtSQLAdmin, click the smNews database on
the left, and then browse the table labeled 'articles'. Click 'insert row', and enter
the news.

Displaying the articles
===================================================================================
To show the news in your webpage, you can do so by including it in your php code.
<?php require('path/to/smNews/index.php'); ?>

If you want to show just headlines instead of full news, use the following:
	<?php
	$_GET['smA'] = 'headlines';
	require('path/to/smNews/index.php');
	?>

Templates/Languages
===================================================================================
This script makes use of templates to display news. You can add templates as you please.
create a file <template name>.php, where <template name> is the name of the template,
with the following data in it,
	<?php
	// index.php TEMPLATES =======================================
	$TPL['template'] = <<<EOF
	EOF;

	$TPL['post'] = <<<EOF
	EOF;

	$TPL['comments'] = <<<EOF
	EOF;

	$TPL['headlines'] = <<<EOF
	EOF;

	$TPL['commentsform'] = <<<EOF
	</form>
	EOF;

	// register.php TEMPLATES =====================================
	$TPL['register']['form'] = <<<EOF
	EOF;

	// login.php TEMPLATES =======================================
	$TPL['login']['form'] = <<<EOF
	EOF;
	?>

You can use the default template, default.php, as a guide to creating a template.
Same thing goes for creating a new language template. Make sure you save the
settings for which template and language to use in the smNews/config.php file
however.