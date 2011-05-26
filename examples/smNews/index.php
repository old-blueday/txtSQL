<?php
require_once('./config.php');

// Show all the posts made by the user so far
if ( empty($_GET['smA']) )
{
	$_GET['newsid'] = isset($_GET['newsid']) ? $_GET['newsid'] : 0;
	$_GET['c']      = isset($_GET['c'])      ? $_GET['c']      : 0;

	$articles = execute('select',
		      array('table'   => 'articles',
		            'where'   => array(($_GET['newsid'] != 0) ? 'id = '.$_GET['newsid'] : 'id =~ ^[0-9]+$'),
			    'orderby' => array('date', ($postsOrder == TRUE ? 'DESC' : 'ASC')),
			    'limit'   => array(0, ($maxShow - 1) )));

	foreach ( $articles as $key => $value )
	{
		// Grab the author's username and email adress
		$userInfo = execute('select',
			      array('table'  => 'users',
				    'select' => array('username', 'email'),
				    'where'  => array('id = '.$value['posterid']),
				    'limit'  => array(0,0)));
		$userInfo = $userInfo[0];

		// Get the comments for this post
		$comments = execute('select',
			      array('table'   => 'comments',
			            'where'   => array('newsid = '.$value['id']),
			            'orderby' => array('date', 'desc')));

		// Create the date with the user-specified format
		$value['date'] = date($dateFormat, $value['date']);
		$comm          = '';
		$form          = '';

		// Show the comments for this post if specified to do so
		if ( $_GET['c'] == 1 )
		{
			foreach ( $comments as $key1 => $value1 )
			{
				$userInfo1 = execute('select',
					       array('table'   => 'users',
						     'select'  => array('username', 'email'),
						     'where'   => array('id = '.$value1['posterid']),
						     'limit'   => array(0,0)));
				$userInfo1      = $userInfo1[0];
				$value1['date'] = date($dateFormat, $value1['date']);

				// Apply the comment-information to the template
				$comm .= str_replace(array('{{EMAIL}}', '{{USERNAME}}', '{{COMMENT}}', '{{DATE}}'),
				                    array($userInfo1['email'], $userInfo1['username'], $value1['comment'], $value1['date']),
				                    $TPL['comments']);
			}

			// Show the form for the comments
			$form .= str_replace(array('{{LOGOUT}}', '{{NEWSID}}'),
					     array('<a href="login.php?action=logout">'.$LANG['logout'].'</a>', $value['id']),
					     $TPL['commentsform']);

			// If the user is not logged in, then put link for registration or to log-in
			if ( !isset($_COOKIE['userid']) )
			{
				$form = sprintf($LANG['notloggedin'],
				                '<a href="register.php">'.$LANG['register'].'</a>',
				                '<a href="login.php">'.$LANG['login'].'</a>');
			}
		}

		// Apply the information to the template
		$template = str_replace(array('{{SUBJECT}}', '{{DATE}}', '{{ARTICLE}}', '{{AUTHOR}}', '{{AUTHORMAIL}}', '{{COMMENTSLINK}}', '{{COMMENTS}}'),
					array($value['subject'], $value['date'], $value['article'], $userInfo['username'], $userInfo['email'], '<a href="?newsid='.$value['id'].'&c=1">'.$LANG['comments'].'('.count($comments).')</a>', $comm),
					$TPL['post']);

		$template = str_replace(array('{{POST}}', '{{COMMENTSFORM}}'), array($template, $form), $TPL['template']);
		echo $template;
	}
	exit;
}

// Post a comment
if ( $_GET['smA'] == 'post' )
{
	if ( !isset($_COOKIE['userid']) )
	{
		showError('Not logged in', E_USER_ERROR);
	}
	elseif ( empty($_GET['newsid']) || !is_numeric($_GET['newsid']) )
	{
		showError("Invalid news id", E_USER_ERROR);
	}

	execute('insert',
	  array('table'  => 'comments',
	  	'values' => array('newsid'   => $_GET['newsid'],
	  		          'posterid' => $_COOKIE['userid'],
	  		          'date'     => time(),
	  		          'comment'  => htmlentities($_POST['comment']))));

	header('Location: index.php?newsid='.$_GET['newsid'].'&c=1');
	exit();
}

// Show only the headlines for the posts made so far
if ( $_GET['smA'] == 'headlines' )
{
	$headlines = execute('select',
		       array('table'   => 'articles',
		             'select'  => array('id', 'subject', 'date'),
		             'limit'   => array(0, ($maxShowH-1)),
		             'orderby' => array('date', ($postsOrder == TRUE ? 'DESC' : 'ASC'))));

	foreach ( $headlines as $key => $value )
	{
		// Grab the user information for this post
		$userInfo = execute('select',
			      array('table'  => 'users',
			            'select' => array('username', 'email'),
			            'where'  => array('id = '.$value['posterid']),
			            'limit'  => array(0,0)));
		$userInfo = $userInfo[0];

		$template = str_replace(array('{{SUBJECT}}', '{{LINK}}'),
					array($value['subject'], '?newsid='.$value['id'].'&c=1'),
					$TPL['headlines']);
		echo $template;
	}
}
?>