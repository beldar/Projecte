<html>
	<head>
		<title>Proof of Concept</title>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<!-- YUI -->
		<link rel="stylesheet" type="text/css" href="lib/yui/fonts/fonts-min.css" /> 
		<link rel="stylesheet" type="text/css" href="lib/yui/reset/reset-min.css" />
		
		<script type="text/javascript" src="lib/yui/utilities/utilities.js"></script>
		
		<!-- Excanvas -->
		<!--[if IE]><script type="text/javascript" src="lib/excanvas.js"></script><![endif]-->
		
		<!-- WireIt -->
		<script type="text/javascript" src="build/wireit-min.js"></script>

		<script type="text/javascript" src="js/util/DD.js"></script>
		<link rel="stylesheet" type="text/css" href="css/WireIt.css" />
		<script type="text/javascript" src="js/mootools-release-1.11.js"></script>
		<style type="text/css">
			.grid{
				margin: 5 auto;
				width: 960px;
			}
			.cell{background:grey;border:1px solid black;float:left;font-size: 20pt;text-align: center;vertical-align: middle;position:relative;}
			.filled{background:white;border:1px solid black;float:left;font-size: 20pt;text-align: center;vertical-align: middle;position:relative;}
			.wit{
				position: absolute;
				z-index: 100;
				opacity: 0.8;
				top:0;
				left:0;
				filter: alpha(opacity = 80);
				width:100%;
				height: 100%;
				background:white;
			}
			.WireIt-Terminal{margin:30% 40%}
		</style>
	</head>
	<body style="margin:0;padding:0">
	<?
		include_once("ReverseDB.class.php");
		$constr = array('localhost:8889', 'root', 'root');//server,user,pass
		$name =  'imdb';
		$db = new ReverseDB($constr[0], $constr[1], $constr[2]);
		$db->UseDB($name);
		$db->GetTables();
		//print_r($db->data);
		$db->FindKeys();
		$db->Relation();
		$db->RelateTables();
		$db->generateSolutions();
		/*if(isset($_GET['r'])){
			if($_GET['r']==1){
				unlink("out5.txt");
			}
		}
		//$cmd = "/opt/local/bin/swipl -f /Users/beldar/g9.pl  -g resolve,halt > /Applications/MAMP/htdocs/projecte/out5.txt";
		if(!file_exists("out5.txt")){
			$cmd = "sh /Users/beldar/createsolutions.sh";
			system( $cmd );
		}*/
		$size = intval(file_get_contents($name."_n.txt"));
		$size++;
		
		$n=0;
		$lines = file("$name.txt");
		$solutions = array();
		foreach($lines as $line)
		{
			$line = str_replace("p(","['",$line);
			$line = str_replace(",","','",$line);
			$line = str_replace(")","']",$line);
			$line = str_replace("]'","]",$line);
			$line = str_replace("'[","[",$line);
		    $solutions[$n]=$line;
		    $n++;
		}
		if(!isset($_GET['a'])){
			echo "<div style=\"position:absolute;top:10px;left:10px\">$n solutions<br /><a href=\"?a=1\">Next solution ➤</a><br /><br /></div>";
			$a=0;
		}else{
			$a = intval($_GET['a']);
			if($a<($n-1)) echo "<div style=\"position:absolute;top:10px;left:10px\">$n solutions<br /><a href=\"?a=".($a+1)."\">Next solution ➤</a><br /><br /></div>";
			else echo "<div style=\"position:absolute;top:10px;left:10px\">$n solutions<br /><a href=\"?a=0\">Start again ⟳</a></div>";
		}
		echo '<div style="position:absolute;top: 100px;left:10px;width:220px">archi([arco(a,c), arco(b,c), arco(a,d), arco(e,d), arco(e,f), arco(f,h), arco(g,h), arco(h,a)]).</div>';
		$sizex = $size;
		$sizey = $size;
		$widthd = 100/$sizex - 1;
		echo '<div class="grid">';
		for($i=0;$i<$sizey;$i++){
			for($j=0;$j<$sizex;$j++){
				echo "<div class=\"cell\" style=\"width:$widthd%;height:$widthd%\"><div id=\"$j-$i\">&nbsp;</div></div>";
			}
			echo "<br style=\"clear:both\">";
		}
		echo '</div>';
	?>
		<script type="text/javascript">
			var relations = new Array();
			// archi([arco(a,b), arco(b,c), arco(c,d), arco(d,a)]).
			var ents = new Array();	
			var entities= new Array();
			entities=<?=$solutions[$a];?>;
			for(var i in entities){
				cord = entities[i];
				if(document.getElementById(cord[0]+'-'+cord[1])){
					document.getElementById(cord[0]+'-'+cord[1]).innerHTML = cord[2];
					document.getElementById(cord[0]+'-'+cord[1]).className = 'wit';
					ents[cord[2]] = cord[0]+'-'+cord[1];
				}
			}
			
			// archi([arco(a,b), arco(b,c), arco(c,d), arco(d,a), arco(a,c)]).
			//relations = [[ents.a,ents.b],[ents.b,ents.c],[ents.c,ents.d],[ents.d,ents.a],[ents.a,ents.c]];
			//archi([arco(a,c), arco(b,c), arco(a,d), arco(e,d), arco(e,f), arco(f,h), arco(g,h), arco(h,a)]).
			//relations = [[ents.a,ents.c],[ents.b,ents.c],[ents.a,ents.d],[ents.e,ents.d],[ents.e,ents.f],[ents.f,ents.h],[ents.g,ents.h],[ents.h,ents.a]];
			<?
			$relations = 'relations = [';
			$i=0;
			foreach($db->reltbl as $taula1=>$taula2){
				foreach ($taula2 as $index=>$taula3){
					$relations .= "[ents.$taula1, ents.$taula3],";
					$i++;
				}
			}
			$np = strlen($relations)-1;
			$relations = substr($relations,0,$np);
			$relations .= '];';
			echo $relations;
			?>
			var terminals = [];
window.onload = function() {
	document.getElements('div[class="wit"]').each(function(div,i){
		terminals[div.id] = [new WireIt.Terminal(div, {editable: false,direction: [-1,0],offsetPosition:[0,0]}), new WireIt.Terminal(div, {editable: false,direction: [1,0],offsetPosition:[0,0]})];
		new WireIt.util.DD(terminals[div.id],div);
	});
	relations.each(function(itemm, i){
		var w = new WireIt.Wire(terminals[itemm[0]][1], terminals[itemm[1]][0], document.body,{drawingMethod:'arrows'});
		w.redraw();
	});
	
};
		</script>
	</body>
</html>
