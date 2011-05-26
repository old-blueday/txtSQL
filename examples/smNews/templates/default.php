<?php
// index.php TEMPLATES =======================================
$TPL['template'] = <<<EOF
{{POST}}
{{COMMENTSFORM}}
EOF;

$TPL['post'] = <<<EOF
<table width="300" cellpadding="3" cellspacing="0" border="0">
 <tr>
  <td bgcolor="F7F7F7">
  :: <b>{{SUBJECT}}</b> <font size="1">By: <a href="mailto:{{AUTHORMAIL}}">{{AUTHOR}}</a> {{DATE}}</font>
  </td>
 </tr>
 <tr>
  <td height="1" bgcolor="404040">
  </td>
 </tr>
 <tr>
  <td>
   {{ARTICLE}}
  </td>
 </tr>
 <tr>
  <td align="right">
   {{COMMENTSLINK}}
  </td>
 </tr>
 <tr>
  <td>
   {{COMMENTS}}
  </td>
 </tr>
</table>
<br>
EOF;

$TPL['comments'] = <<<EOF
<li><a href="mailto:{{EMAIL}}">{{USERNAME}}</a> <font size="1">{{DATE}}</font>- {{COMMENT}}
EOF;

$TPL['commentsform'] = <<<EOF
<form method=post action="index.php?smA=post&newsid={{NEWSID}}">
<textarea name="comment" cols=25 rows=4 onFocus="if(this.value=='[insert comment here]'){this.value='';}" onBlur="if(this.value==''){this.value='[insert comment here]';}">[insert comment here]</textarea><br>
<input type="submit" value="Post Comment"> {{LOGOUT}}
</form>
EOF;

$TPL['headlines'] = <<<EOF
<li> <a href="{{LINK}}">{{SUBJECT}}</a><br>
EOF;

// register.php TEMPLATES =====================================
$TPL['register']['form'] = <<<EOF
<form method=post action="register.php?action=register">
<table width="300" cellspacing="0" cellpadding="3">
 <tr>
  <td colspan="2" align="center"><font color="red"><b>{{ERROR}}</b></font></td>
 </tr>
 <tr>
  <td>Username*</td><td><input type="text" name="username" value="{{USERNAME}}"></td>
 </tr>
 <tr>
  <td>Password*</td><td><input type="password" name="password" value="{{PASSWORD}}"></td>
 </tr>
 <tr>
  <td>Email</td><td><input type="email" name="email" VALUE="{{EMAIL}}"></td>
 </tr>
 <tr>
  <td colspan="2" align="center">
  <input type="submit" value="Register">
  </td>
 </tr>
</table>
</form>
EOF;

// login.php TEMPLATES =======================================
$TPL['login']['form'] = <<<EOF
<form method=post action="login.php?action=login">
<table width="300" cellspacing="0" cellpadding="3">
 <tr>
  <td colspan="2" align="center">
   <font color="red"><b>{{ERROR}}</b></font>
  </td>
 </tr>
 <tr>
  <td colspan="2" align="center">
   Login to smNews
  </td>
 </tr>
 <tr>
  <td>Username</td><td><input type="text" name="username" value="{{USERNAME}}"></td>
 </tr>
 <tr>
  <td>Password</td><td><input type="password" name="password"></td>
 </tr>
 <tr>
  <td colspan="2" align="center">
   <input type="submit" value="Login">
  </td>
 </tr>
</table>
EOF;
?>