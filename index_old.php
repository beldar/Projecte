<?
session_start();
if(isset($_POST['server']) && isset($_POST['user']) && isset($_POST['pass'])) setcookie('constr', $_POST['server'].'-'.$_POST['user'].'-'.$_POST['pass'], time()+3600);
include_once("ReverseDB.class.php");
if(isset($_POST['a'])) $a = intval($_POST['a']);
if(isset($_GET['a'])) $a = intval($_GET['a']);
else $a=-1;
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html>
 <head>
  <title>Reverse Data Base (ReverseDB)</title>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
 
<!-- YUI -->
<link rel="stylesheet" type="text/css" href="js/yui/fonts/fonts-min.css" /> 
<link rel="stylesheet" type="text/css" href="js/yui/reset/reset-min.css" />

<script type="text/javascript" src="js/yui/yahoo-dom-event/yahoo-dom-event.js"></script>
<script type="text/javascript" src="js/yui/dragdrop/dragdrop-min.js"></script>
<script type="text/javascript" src="js/yui/animation/animation-min.js"></script>

<!-- Excanvas -->
<!--[if IE]><script type="text/javascript" src="../lib/excanvas.js"></script><![endif]-->

<!-- WireIt -->
<script type="text/javascript" src="js/WireIt.js"></script>
<script type="text/javascript" src="js/Wire.js"></script>
<script type="text/javascript" src="js/Terminal.js"></script>
<script type="text/javascript" src="js/util/Anim.js"></script>
<script type="text/javascript" src="js/util/DD.js"></script>
<script type="text/javascript" src="js/mootools1.11.js"></script>
<link rel="stylesheet" type="text/css" href="css/WireIt.css" />
<link rel="stylesheet" type="text/css" href="css/stylazo.css" />
<? if((isset($_POST['dbs']) && isset($_POST['a']) && $_POST['a']==1)||(isset($_POST['a']) && $_POST['a']==1) ||(isset($_GET['a']) && $_GET['a']==1)){ ?>
<script>

var terminals = [];
window.onload = function() {
	document.getElements('div[id!=""]').each(function(div,i){
		terminals[div.id] = [new WireIt.Terminal(div, {direction: [-1,0], offsetPosition: [-16,0]}), new WireIt.Terminal(div, {direction: [1,0], offsetPosition: [185,0]})];
		new WireIt.util.DD(terminals[div.id],div);
	});
	ReverseIt();
	
};
<? }else{ ?>
</head>
<body>
<?
}
switch($a){
		case 0:
			if(isset($_POST['server']) && isset($_POST['user']) && isset($_POST['pass'])){
				$db = new ReverseDB($_POST['server'], $_POST['user'], $_POST['pass']);
				$dbs = $db->GetDBs();
				echo '<div class="centro"><form action="'.$_SERVER['PHP_SELF'].'" method="POST" id="db"> <label for="dbs">Select Data Base: </label><select name="dbs">';
				while($row = mysql_fetch_object($dbs)){
					echo '<option value="'.$row->Database.'">'.$row->Database.'</option>';
				}
				echo '</select><br /><input style="float:right" type="hidden" name="a" value="2" /><input type="submit" value="Enter" style="margin-left: 100px; margin-top: 10px;text-align: center;" /></form>';
			}else{
				echo '<p>Has d\'omplir tots els camps</p><br /><a href="'.$_SERVER['PHP_SELF'].'">Tornar</a>';
			}
			break;
		
		case 1:
			if(isset($_POST['dbs']) || isset($_GET['d'])){
				if(isset($_GET['d']) && $_GET['d']=="demo"){
					$constr = array('localhost', 'beldar_imdb', 'imdb');//server,user,pass
					$name =  'beldar_imdb';
				}else{
					$constr = explode("-", $_COOKIE['constr']);
					$name =  $_POST['dbs'];
				}
				$db = new ReverseDB($constr[0], $constr[1], $constr[2]);
				$db->UseDB($name);
				$db->GetTables();
				$db->FindKeys();
				$db->Relation();
				$db->RelateTables();
				echo 'var relations = new Array();';
				$i = 0;
				foreach($db->reltbl as $taula1=>$taula2){
					foreach ($taula2 as $index=>$taula3){
						echo "relations[$i]= ['$taula1', '$taula3'];";
						$i++;
					}
				}
				

?>

function ReverseIt(){
	relations.each(function(itemm, i){
		var w = new WireIt.Wire(terminals[itemm[0]][1], terminals[itemm[1]][0], document.body);
		w.redraw();
	});

}
</script>
</head>
 <body>

<?
				//$db->Draw();
				//$db->DrawSchema();
				$db->DrawKeys();
			}else echo 'Algo falla';
			break;
		case 2:
?>
		<div class="centro">
			<form action="<?=$_SERVER['PHP_SELF']?>" method="get">		
				<laber for="a">Generate</label>
				<select name="a">
					<option value="1">Relational Model</option>
					<option value="3">Schema</option>
				</select>
				<!--<input type="hidden" name="dbs" value="beldar_imdb">-->
				<input type="hidden" name="d" value="demo">
				<input type="submit" value="Process" style="margin-left: 100px; margin-top: 10px;text-align: center;">
			</form>
		</div>	
<?
		break;
		case 3:
			if(isset($_POST['dbs']) || isset($_GET['d'])){
				if(isset($_GET['d']) && $_GET['d']=="demo"){
					$constr = array('localhost', 'beldar_imdb', 'imdb');//server,user,pass
					$name =  'beldar_imdb';
				}else{
					$constr = explode("-", $_COOKIE['constr']);
					$name =  $_POST['dbs'];
				}
				$db = new ReverseDB($constr[0], $constr[1], $constr[2]);
				
				$db->UseDB($name);
				$db->GetTables();
				$db->FindKeys();
				$db->Relation();
				$db->RelateTables();
				echo '<div class="centro" style="border:none">';
				echo '<div class="centro" style="float:left;height:auto;margin-top:0px;text-align:left;">';
				$db->DrawSchema();
				echo '</div>';
				echo '</div>';
				
			}
		break;
		default:
?>
		<div class="centro">
			<form action="<?=$_SERVER['PHP_SELF']?>" method="POST" id="db">
				<label for="server">Server: </label><input type="text" name="server" /><br />
				<label for="user">User: </label><input type="text" name="user" /><br />
				<label for="pass">Password: </label><input type="password" name="pass" /><br />
				<input type="hidden" name="a" value="0" />
				<input type="submit" value="Send" style="margin-left: 100px; margin-top: 10px;text-align: center;" />
			</form>
		</div>
<?
			break;
} 
?>
 </body>
 <div id="footer"><a href="doc/ReverseDB.html">See the documentation</a> | <a href="ReverseDB.class.php.zip">Download</a></div>
</html>