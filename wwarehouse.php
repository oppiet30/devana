<?php include "antet.php"; include "func.php";

if (isset($_SESSION["user"][0], $_GET["town"]))
{
 $_GET["town"]=clean($_GET["town"]);
 check_r($_GET["town"]);
 $town=town($_GET["town"]); if ($town[1]!=$_SESSION["user"][0]) {header('Location: login.php'); die();}
 $faction=faction($_SESSION["user"][10]); $r=$faction[3];
 $buildings=buildings($_SESSION["user"][10]);
 $weapons=weapons($_SESSION["user"][10]);
 $c_status=get_con($_GET["town"]);
 
 $data=explode("-", $town[8]); $res=explode("-", $town[10]); $prod=explode("-", $town[9]); $lim=explode("-", $town[11]); $out=explode("-", $buildings[21][5]); $weaps=explode("-", $town[6]);
}
else {header('Location: login.php'); die();}
?>
<html>
<?php echo "<link rel='stylesheet' type='text/css' href='".$imgs.$fimgs."default.css'>"; ?>
<script src="func.js" type="text/javascript"></script>
<script type="text/javascript">
var res_start0=<?php echo floor($res[0]);?>; var res_start1=<?php echo floor($res[1]);?>; var res_start2=<?php echo floor($res[2]);?>; var res_start3=<?php echo floor($res[3]);?>; var res_start4=<?php echo floor($res[4]);?>;
var res_limit0=<?php echo $lim[0];?>; var res_limit1=<?php echo $lim[1];?>; var res_limit2=<?php echo $lim[1];?>; var res_limit3=<?php echo $lim[1];?>; var res_limit4=<?php echo $lim[2];?>;
var res_ph0=<?php echo ($prod[0]-$town[3]-$town[12]);?>; var res_ph1=<?php echo ($prod[1]);?>; var res_ph2=<?php echo ($prod[2]);?>; var res_ph3=<?php echo ($prod[3]);?>; var res_ph4=<?php echo ($prod[4]);?>;
var res_sec0=0; var res_sec1=0; var res_sec2=0; var res_sec3=0; var res_sec4=0;
</script> 

<head>
<title><?php echo $title." - ".$buildings[21][2]; ?></title>
</head>

<body class="q_body" onLoad="startres()">

<div align="center">
<?php echo $top_ad; ?>

<table class="q_table">
	<tr>
		<td class="td_logo">
		<?php logo($title); ?></td>
	</tr>
	<tr>
		<td class="td_top_menu"><?php menu_up(); ?></td>
	</tr>
	<tr>
		<td class="td_content" style='height: 200'>
<?php
if ($data[21])
{
 echo "<img src='".$imgs.$fimgs."00.gif' title='".$lang['crop']."'><span id=\"res0\"></span>/".$lim[0]." <img src='".$imgs.$fimgs."01.gif' title='".$lang['lumber']."'><span id=\"res1\"></span>/".$lim[1]." <img src='".$imgs.$fimgs."02.gif' title='".$lang['stone']."'><span id=\"res2\"></span>/".$lim[1]." <img src='".$imgs.$fimgs."03.gif' title='".$lang['iron']."'><span id=\"res3\"></span>/".$lim[1]." <img src='".$imgs.$fimgs."04.gif' title='".$lang['gold']."'><span id=\"res4\"></span>/".$lim[2]."</br></br>";
 echo $buildings[21][8]."</br></br>";
 echo $lang['currentStorCap'].": ".$lim[12]." ".$lang['type']."</br>".$lang['weapStock'].":<table class='q_table' style='border-collapse: collapse; text-indent: 0; margin-left:auto; margin-right:auto;' width='600' border='1'><tr style='text-align: center'>";
 for ($i=0; $i<count($weapons); $i++) echo "<td><img src='".$imgs.$fimgs."1".$i.".gif' title='".$weapons[$i][2]."'></td>";
 echo "</tr><tr style='text-align: center'>";
 for ($i=0; $i<count($weaps); $i++) echo "<td>".$weaps[$i]."</td>";
 echo "</tr></table>------------------------------------------</br>";
 if (!$c_status[21])
		if ($data[21]<10)
		{
			$dur=explode("-", $buildings[21][6]); $upk=explode("-", $buildings[21][7]); $cost=explode("-", $buildings[21][4]); $dur[$data[21]]=explode(":", $dur[$data[21]]);
			$tag="<a class='q_link' href='build.php?town=".$town[0]."&b=".$buildings[21][0]."&subB=-1'>".$lang['upgrade']." ".$buildings[21][2]." ".$lang['toLevel']." ".($data[21]+1)."</a>";
			$tag=$tag."</br>".$lang['cost'].": <img src='".$imgs.$fimgs."00.gif' title='".$lang['crop']."'>".floor($cost[0]*pow($r, $data[21]))." <img src='".$imgs.$fimgs."01.gif' title='".$lang['lumber']."'>".floor($cost[1]*pow($r, $data[21]))." <img src='".$imgs.$fimgs."02.gif' title='".$lang['stone']."'>".floor($cost[2]*pow($r, $data[21]))." <img src='".$imgs.$fimgs."03.gif' title='".$lang['iron']."'>".floor($cost[3]*pow($r, $data[21]))." <img src='".$imgs.$fimgs."04.gif' title='".$lang['gold']."'>".floor($cost[4]*pow($r, $data[21]))."</br>".$lang['duration'].": ".($dur[$data[21]][0]*$lim[4]/100).":".($dur[$data[21]][1]*$lim[4]/100)."</br>".$lang['upkeep'].": ".$upk[$data[21]]."</br>".$lang['expSpace'].": ".$out[$data[21]];
			echo $tag;
			if ($town[12]+$town[3]+$upk[$data[21]]>$lim[3]) label("</br>".$lang['noHouses']);
			if (!(($res[0]>=$cost[0]*pow($r, $data[21]))&&($res[1]>=$cost[1]*pow($r, $data[21]))&&($res[2]>=$cost[2]*pow($r, $data[21]))&&($res[3]>=$cost[3]*pow($r, $data[21]))&&($res[4]>=$cost[4]*pow($r, $data[21])))) label("</br>".$lang['noResources']);
			echo "</br>------------------------------------------</br>";
		}
		else echo $lang['buildingMaxLvl']."</br></br></br></br></br></br>";
 else echo $lang['beingUpgraded']."</br></br></br></br></br></br>";
}
else echo $lang['constrBuilding'];
?>
          </td>
	</tr>
	<tr>
		<td class="td_bottom_menu">
		<?php menu_down(); ?></td>
	</tr>
</table>

<?php echo $bottom_ad; ?>
<p><?php about(); ?></div>

</body>

</html>