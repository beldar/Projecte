				<div class="centro">
					<form action="<?=$_SERVER['PHP_SELF']?>" method="POST" id="db"> 
						<label for="dbs">Select Data Base <br />or <a href="#">Create one</a> </label>
						<select name="dbs">
						<?php
							print_r($dbs);
							while($row = mysql_fetch_object($dbs)){
								if($row->Database != 'information_schema') echo '<option value="'.$row->Database.'">'.$row->Database.'</option>';
							}
						?>
						</select><br /><br/><br />
						<label for="generate">Try to auto arrange tables</label>
						<input id="generate" title="Warning! Only if there are few relations, it may take a while." type="checkbox" name="generate" />
						<input type="submit" value="Continue" style="margin-left: 100px; margin-top: 10px;text-align: center;" />
					</form>
				</div>
				<script type="text/javascript">
					$('#generate').tooltip({ 
					    track: true, 
					    delay: 0, 
					    showURL: false, 
					    showBody: " - ",
					    extraClass: "warning",
					    fade: 250 
					});
				</script>