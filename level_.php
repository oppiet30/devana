<?php include "antet.php"; include "func.php";

if (isset($_POST["name"], $_POST["level"]))
 if (isset($_SESSION["user"][0]))
  if (($_SESSION["user"][4]>3)&&($_SESSION["user"][2]==md5(clean($_POST["pass"]))))
  {
			$_POST["name"]=clean($_POST["name"]); $_POST["level"]=clean($_POST["level"]);
   $usr=user_($_POST["name"]);
   if ($_SESSION["user"][4]>$usr[4]) ban($_POST["name"], $_POST["level"]);
   else msg($lang['levelLow']);
  }
  else msg($lang['notAdmin']);
 else msg($lang['accessDenied']);
else msg($lang['noInput']);
?>