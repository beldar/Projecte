<?
/**
* ReverseDB
*
* An API class for printing the structure of
* any remote or local Data Base
*
* @author 	Marti Planellas 
* @copyright	Licensed under GPLv3
* @version	3.0
* @link		http://radleb.net
* @since  	January 2008
*/

class ReverseDB{
	var $server;
	var $user;
	var $pass;
	var $db;
	var $data = array();
	var $keys = array();
	var $rels = array();
	var $reltbl = array();
	
	/**
	* Connects to the Data Base server
	*
	* @param string $server_ server to connect
	* @param string $user_ user of the DB
	* @param string $pass_ password of the DB
	* @see ConnectDB()
	*/
	function ReverseDB($server_, $user_, $pass_){
		$this->server = $server_;
		$this->user = $user_;
		$this->pass = $pass_;
		$this->ConnectDB();
	}
	
	/**
	* Makes the actual connection for the stored parameters
	*
	*/
	function ConnectDB(){
		@mysql_connect($this->server, $this->user, $this->pass)or die("Can't connect to the data base server, try again");
	}
	
	/**
	* Gets the list of the Data Bases that the given user can access
	*
	* @return array $list List of the data bases
	*/
	function GetDBs(){
		$list = @mysql_list_dbs();
		return $list;
	}
	
	/**
	* Uses the selected Data Base 
	*
	*/
	function UseDB($name){
		$this->db = $name;
		@mysql_select_db($name);
	}
	
	/**
	* Gets all the tables from the selected Data Base
	*
	* @return array $data An array with all the tables in the DB
	* @see UseDB()
	*/
	function GetTables(){
		$list = @mysql_list_tables($this->db);
		while($row = @mysql_fetch_row($list)){
			$this->data[$row[0]] = $this->GetFields($row[0]);
		}
		return $this->data;
	}
	
	/**
	* Gets all the Fields and Field characteristics from target table
	*
	* @param string $table Target table
	* @return array $fields Field parameters from that table
	*/
	function GetFields($table){
		$r = @mysql_query("SHOW COLUMNS FROM ".$table);
		if(@mysql_num_rows($r)>0){
			while($row = @mysql_fetch_assoc($r)){
				$fields[$row['Field']] = $row;
			}
		}
		return $fields;
	}
	
	/**
	* Calls and set dimensions for the prolog process to generate the solutions
	* 
	* @return array $solutions 
	*/
	function generateSolutions(){
		$dbname = $this->db;
		$filename = $dbname.'_pre.pl';
		if (copy('generateSols.pl', $filename)) {
			//Nodi
			$ents = '
			nodi([';
			$n = count($this->data);
			$i = 0;
			foreach($this->data as $taula=>$value){
				$i++;
				if($i==$n){
					$ents .= "'$taula']).";
				}else{
					$ents .= " '$taula', ";
				}
			}
			//Archi
			$arc = '
			archi([';
			
			$i = 0;
			foreach($this->reltbl as $taula1=>$taula2){
				foreach ($taula2 as $index=>$taula3){
					$arc .= " arco('$taula1', '$taula3') ,";
					$i++;
				}
			}
			$np = strlen($arc)-1;
			$arc = substr($arc,0,$np);
			$arc .= ']).';
			if (is_writable($filename)) {
			    if (!$handle = fopen($filename, 'a')) {
			         echo "Cannot open file ($filename)";
			         exit;
			    }
				
			    if (fwrite($handle,$ents) === FALSE) {
			        echo "Cannot write to file ($filename)";
			        exit;
			    }else{
			    	fwrite($handle,$arc);
			    }

			    fclose($handle);
			} else {
			    echo "The file $filename is not writable";
			}
			if(file_exists($filename)){
				$cmd = "sh /Users/beldar/createsolutions.sh $dbname";
				system( $cmd );
			}
			while(filesize($dbname.".txt")==0){}
			
		}
	}
	/**
	* Draws the tables, their fields and the characteristics of every field
	* in form of square floating divs
	*
	*@see GetTables()
	*/
	function Draw(){
		
		foreach($this->data as $key=>$value){
			echo '<div id="'.$key.'" style="z-index:5;position:relative;float:left;width: 200px;text-align:center;cursor:move;background-color: rgb(255,200,200);opacity: 0.8;border:1px #000 solid;margin:20px;">';
			echo '<div style="font-size:16; font-weight:bold;width:100%;position:relative: float:left;border-bottom:1px #000 solid;">'.$key.'</div>';
			foreach($value as $camp=>$caract){
				echo '<div style="font-size:12;font-weight:bold;width:100%;position:relative: float:left;">'.$camp.'</div>';
				foreach($caract as $nom=>$valor){
					if($nom!="Field"){
						echo '<div style="font-size:10;width:90%;position:relative: float:left;">'.$nom.' = '.$valor.'</div>';
					}
				}
			}
			echo '</div>';
		}
	}
	
	/**
	* Draws the schema of the Data Base
	*
	*/
	function DrawSchema(){
		foreach ($this->data as $key=>$value){
			echo "<b>$key</b>(";
			$attr = '';
			foreach ($value as $camp=>$caract){
					if($caract['Key']=='PRI') $attr .='<u>';
					if(preg_match('/^FK_/', $camp)!=0){
						$attr .= substr($camp, 3).' (FK), ';
					}else $attr .= $camp.', ';
					if($caract['Key']=='PRI') $attr .= '</u>';
			}
			//$attr = substr($attr, 0, strlen($attr)-2);
			echo $attr.')<br />';
		}
	}
	
	/**
	* Gets all the keys (Primarys and Foreings) of the tables who has them
	*
	* @return array $keys array for [table]=>[key1, key2, ...]
	*/
	function FindKeys(){
		foreach($this->data as $key=>$value){
			$i=0;
			foreach($value as $camp=>$caract){
				if($caract['Key']=='PRI' || preg_match('/^FK_/', $camp)!=0){
					$this->keys[$key][$i]=$camp; 
					$i++;
				}
			}
		}
	}
	
	/**
	* Draws the array of keys
	*
	* @see FindKeys()
	*/
	function DrawKeys(){
		foreach ($this->keys as $key=>$value){
			echo '<div id="'.$key.'" style="z-index:5;position:relative;float:left;width: 200px;text-align:center;cursor:move;background-color: rgb(255,200,200);opacity: 0.8;border:1px #000 solid;margin:20px;">';
			echo '<div style="font-size:16; font-weight:bold;padding:5px;position:relative: float:left;border-bottom:1px #000 solid;">'.$key.'</div>';
			foreach ($value as $index=>$camp){
				echo '<div style="font-size:12;font-weight:bold;padding:5px;position:relative: float:left;">'.$camp.'</div>';
			}
			echo '</div>';
		}
	}
	
	/**
	* Makes the relations between the keys and the tables who have the same keys
	*
	* @return array $rels array for [key]=>[table1, table2, ...]
	*/
	function Relation(){
		foreach($this->keys as $key=>$value){
			foreach($value as $index=>$clau){
				$this->rels[$clau] = array();
				array_push($this->rels[$clau], $key);	
				foreach($this->keys as $taula=>$claus){
					if($taula!=$key){
						foreach($claus as $indexx=>$clau2){
							if($clau==$clau2){
								array_push($this->rels[$clau], $taula);
							}
						}
					}				
				}
			}
		}
	}
	
	
	/**
	* Makes the relation between the relational tables and the entity tables
	*
	* @return array $reltbl array in pairs for the wires [table1]=>[table2] 
	* @see Relation()
	*/
	function RelateTables(){
		foreach($this->rels as $clau=>$taules){
			if(preg_match('/^FK_/', $clau)!=0){
				$claupri = substr($clau, 3);
				foreach ($taules as $index=>$taula){
					if(!@is_array($this->reltbl[$taula])) $this->reltbl[$taula] = array();
					if(!empty($this->rels[$claupri][0]) && !in_array($this->rels[$claupri][0], $this->reltbl[$taula])){
							array_push($this->reltbl[$taula], $this->rels[$claupri][0]);
					}
				}
			}
		}
		foreach($this->keys as $taula=>$claus){
			if(preg_match('/^GEN_/', $taula)!=0){
				if(!@is_array($this->reltbl[$taula])) $this->reltbl[$taula] = array();
				$ttaula = explode("_", $taula);
				array_push($this->reltbl[$taula], $ttaula[1]);
			}
		}
	}
	

}

?>