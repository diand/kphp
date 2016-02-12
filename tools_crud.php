<?php
	
	 
	function crud($option)
	{
		foreach ($option['slev'] as $key => $value) {
			switch ($key) {
				case 'create':
					echo crudCreate($option);
					break;
				case 'read':
					echo crudRead($option);
					break;
				case 'update':
					echo crudUpdate($option);
					break;
				case 'delete':
					echo crudDelete($option);
					break;
				case 'detail':
					echo crudDetail($option);
					break;
				default:
					# code...
					break;
			}		
		}
		 
	}
	 

	function crudRead($read) 
	{
		$out="";
		//initiate the Tables
		if(empty($_GET['op']))
		{
			$col  = "";
			$tbl  = "";
			$pk   = "";
			$slev = "";
			foreach ($read as $key => $value) {
				switch ($key) {
					case 'col':
						$col = $value;
						break;
					case 'tbl': 
						$tbl = $value;
						break;
					case 'pk':
						$pk = $value;
						break;
					case 'slev':
						$slev = $value;
						break;
					default:
						# code...
						break;
				}
			}

			$sql = "select $col from $tbl";
			$query = mysql_query($sql);
    
			$out .= "
			<div class=\"row-fluid\">
				<div class=\"span12\">
					<div class=\"box\">
				<div class=\"box-head tabs\">
					<h3>Data ".ucwords(str_replace("_", " ", $_GET['m']))."</h3>							
					<ul class='nav nav-tabs'>
						<li>";
						if(is_array($read['slev']['create'])){
							$allow=0;
							if(is_numeric(array_search($_SESSION['sess_level'], $read['slev']['create']))) { $cc = 1; }else{ $cc = 0; }
							$allow = $cc;
							if($allow > 0){
								$out .= "	<a class='btn' href='index.php?m=".$_GET['m']."&op=add'><img src='img/icons/essen/16/plus.png'></a>";
							} 
						}else{
							if(is_bool($read['slev']['create']) and $read['slev']['create']==true){
								$out .= "	<a class='btn' href='index.php?m=".$_GET['m']."&op=add'><img src='img/icons/essen/16/plus.png'></a>";
							}elseif(is_bool($read['slev']['create']) and $read['slev']['create']==false){
								$out .= "";
							}else{
								$out .= "	<a class='btn' href='index.php?m=".$_GET['m']."&op=add'><img src='img/icons/essen/16/plus.png'></a>";	
							}
							
						}

			$out .="	</li>
					</ul>

				</div>
				<div class=\"box-content box-nomargin\">
					<div class=\"tab-content\">
						<div class=\"tab-pane active\" id=\"basic\">
							<table class='table table-striped dataTable table-bordered'> 
						   	<thead>
						    	<tr>
						    	<th>NO</th>
				    ";	
				    	//cols
				    	$i=0;
				    	while ($i < mysql_num_fields($query)) {
				    		$meta = mysql_fetch_field($query,$i);
					    	if (!$meta) {
				    		    $out .=  "No information available<br />\n";
					    	}else{	    	
					    		if($meta->name!=$pk){ 
					    			//check if the relation is exist
					    			if(array_key_exists($meta->name, $read['rel']['1_n'])){
					    				$colName = str_replace("_"," ",strtoupper($read['rel']['1_n'][$meta->name]['display']));
					    			}else{
					    				$colName = str_replace("_"," ",strtoupper($meta->name));
					    			}
					    			$out .= "<th>".$colName."</th>";        	
				    	    	}
				        	}
					    	$i++;
				        }
						//set Header Level who can Update Delete and Detail
						if(is_array($read['slev']['update']) or is_array($read['slev']['delete']) or is_array($read['slev']['detail']) ){
							$allow=0;
							if(is_array($read['slev']['update']) and is_numeric(array_search($_SESSION['sess_level'], $read['slev']['update']))) { $cu = 1; }else{ $cu = 0; }
							if(is_array($read['slev']['delete']) and is_numeric(array_search($_SESSION['sess_level'], $read['slev']['delete']))) { $cd = 1; }else{ $cd = 0; }
							if(is_array($read['slev']['detail']) and is_numeric(array_search($_SESSION['sess_level'], $read['slev']['detail']))) { $cde = 1; }else{ $cde = 0; } 
							$allow =  $cu + $cd + $cde;

							if($allow > 0){
								$out .= "	<th></th> ";
							} 
						}else{
							if((is_bool($read['slev']['update']) and $read['slev']['update']==true) or (is_bool($read['slev']['delete']) and $read['slev']['delete']==true) or (is_bool($read['slev']['detail']) and $read['slev']['detail']==true) ){
								$out .= "	<th></th> ";
							}elseif((is_bool($read['slev']['update']) and $read['slev']['update']==false) and (is_bool($read['slev']['delete']) and $read['slev']['delete']==false) and (is_bool($read['slev']['detail']) and $read['slev']['detail']==false) ){
								$out .= "";
							}else{
								$out .= "	<th></th> ";								
							}
						}

						

				$out .= "       
				        </tr>
				  	</thead>
				    <tbody>";
				    	
				    	//rows
				    	$no=1;	
				    	while($rows = mysql_fetch_array($query)){
				    		$out .=  "<tr>";
				    		$out .=  "<td>$no</td>";
				    		$i=0;
				    		while ($i < mysql_num_fields($query)) {
				    			$meta = mysql_fetch_field($query,$i);
						    	if (!$meta) {
				    			    $out .=  "No information available<br />\n";
					    		}else{	   
					    			if($meta->name!=$pk){
					    			
						    			//check if the relation is exist
						    			if(array_key_exists($meta->name, $read['rel']['1_n'])){
						    				$dtNew = mysql_fetch_array(mysql_query("select 
						    							".$read['rel']['1_n'][$meta->name]['display']." 
						    							 from ".$read['rel']['1_n'][$meta->name]['tbl']." 
						    							 where 
						    							 	".$read['rel']['1_n'][$meta->name]['id_col']." = '".$rows[$meta->name]."'"));

						    				$val = $dtNew[$read['rel']['1_n'][$meta->name]['display']];
						    			}else{
						    				//check is a file or image
						    				$fileName = explode(".", strtolower($rows[$meta->name]));
						    				$extFile = $fileName[count($fileName)-1];
					    					if($extFile == 'jpg' or $extFile == 'png' or $extFile == 'gif' or $extFile == 'jpeg')
					    					{
					    						$val = "<center><a href=\"files/".$rows[$meta->name]."\" class='preview fancy'><img src=\"files/".$rows[$meta->name]."\" width='64px' alt='' title=\"".$rows[$meta->name]."\"></a></center>";
					    					}elseif($extFile == 'docx' or $extFile == 'doc' or $extFile == 'pdf'){
					    						$val = "<center><a href=\"files/".$rows[$meta->name]."\"><img src=\"img/icons/fugue/arrow-270.png\" alt='' title=\"".$rows[$meta->name]."\"></a></center>";
					    					}else{
					    						$val = $rows[$meta->name];
					    					}
						    			}
						    			 
				    		    		$out .=  "<td>".$val."</td>";           	
				    		    	} 		
					        	}	        	
						    	$i++;
				        	}
				        //set Body Level who can Update Delete and Detail
				        if(is_array($read['slev']['update']) or is_array($read['slev']['delete']) or is_array($read['slev']['detail']) ){
				        	$allow=0;
							if(is_array($read['slev']['update']) and is_numeric(array_search($_SESSION['sess_level'], $read['slev']['update']))) { $cu = 1; }else{ $cu = 0; }
							if(is_array($read['slev']['delete']) and is_numeric(array_search($_SESSION['sess_level'], $read['slev']['delete']))) { $cd = 1; }else{ $cd = 0; }
							if(is_array($read['slev']['detail']) and is_numeric(array_search($_SESSION['sess_level'], $read['slev']['detail']))) { $cde = 1; }else{ $cde = 0; } 
							$allow =  $cu + $cd + $cde;
							if($allow > 0){
								$out .= "       
									<td class='actions_big'>
						        		<div class=\"btn-group\">";
						        	if(is_array($read['slev']['detail']) and is_numeric(array_search($_SESSION['sess_level'], $read['slev']['detail']))) {
						        		$out .= "<a href='index.php?m=".$_GET['m']."&op=detail&id=".$rows[$pk]."' class='btn btn-mini tip' title='Detail'><img src='img/icons/essen/16/search.png'></a>";
						        	}
						        	if(is_array($read['slev']['update']) and is_numeric(array_search($_SESSION['sess_level'], $read['slev']['update']))) {
						        		$out .= "<a href='index.php?m=".$_GET['m']."&op=edit&id=".$rows[$pk]."' class='btn btn-mini tip' title='Edit'><img src='img/icons/essen/16/pencil.png'></a>";
						        	}
						        	if(is_array($read['slev']['delete']) and is_numeric(array_search($_SESSION['sess_level'], $read['slev']['delete']))) {
						        		$out .= "<a href='index.php?m=".$_GET['m']."&op=hapus&id=".$rows[$pk]."' class='btn btn-mini tip' title='Hapus'><img src='img/icons/essen/16/busy.png'></a>";
						        	}

						        $out .= "
						        		</div>
					        		</td>
							        ";  
							} 
						}else{

							
				        	
				        	$allow=0;

							if(is_bool($read['slev']['detail']) and $read['slev']['detail']==true) { $cu = 1; }else{ $cu = 0; }
							if(is_bool($read['slev']['update']) and $read['slev']['update']==true) { $cd = 1; }else{ $cd = 0; }
							if(is_bool($read['slev']['delete']) and $read['slev']['delete']==true) { $cde = 1; }else{ $cde = 0; } 
							$allow =  $cu + $cd + $cde;

							if($allow > 0){
								$out .= "<td class='actions_big'><div class=\"btn-group\">";
							}

							if(is_bool($read['slev']['detail']) and $read['slev']['detail']==true) {
				        		$out .= "<a href='index.php?m=".$_GET['m']."&op=detail&id=".$rows[$pk]."' class='btn btn-mini tip' title='Detail'><img src='img/icons/essen/16/search.png'></a>";
				        	}elseif(is_bool($read['slev']['detail']) and $read['slev']['detail']==false) {
				        		$out .= "";
				        	}
				        	if(is_bool($read['slev']['update']) and $read['slev']['update']==true) {
				        		$out .= "<a href='index.php?m=".$_GET['m']."&op=edit&id=".$rows[$pk]."' class='btn btn-mini tip' title='Edit'><img src='img/icons/essen/16/pencil.png'></a>";
				        	}elseif(is_bool($read['slev']['update']) and $read['slev']['update']==false) {
				        		$out .= "";
				        	}
				        	if(is_bool($read['slev']['delete']) and  $read['slev']['delete']==true) {
				        		$out .= "<a href='index.php?m=".$_GET['m']."&op=hapus&id=".$rows[$pk]."' class='btn btn-mini tip' title='Hapus'><img src='img/icons/essen/16/busy.png'></a>";
				        	}elseif(is_bool($read['slev']['update']) and $read['slev']['update']==false) {
				        	 	$out .="";
				        	}

				        	if($allow > 0){
								 $out .= "</div></td>";  
							}
				        	 
							
						}


				if(!empty($_SESSION['sess_level']) and $_SESSION['sess_level']==$slev){
						$out .= "       
						<td class='actions_big'>
			        		<div class=\"btn-group\">
			        		<a href='index.php?m=".$_GET['m']."&op=detail&id=".$rows[$pk]."' class='btn btn-mini tip' title='Detail'><img src='img/icons/essen/16/search.png'></a>
			        		<a href='index.php?m=".$_GET['m']."&op=edit&id=".$rows[$pk]."' class='btn btn-mini tip' title='Edit'><img src='img/icons/essen/16/pencil.png'></a>
			        		<a href='index.php?m=".$_GET['m']."&op=hapus&id=".$rows[$pk]."' class='btn btn-mini tip' title='Hapus'><img src='img/icons/essen/16/busy.png'></a>
			        		</div>
		        		</td>
				        ";        	
				}

				    	$out .=  "</tr>"; 
				    	$no++;   		
				    	}
				    	   
			$out .= "          		
				    </tbody>                                        
				</table>
					</div>	 
					</div>
				</div>
			</div>                
 				";

		}

		if(is_array($read['slev']['read'])){
			if(is_numeric(array_search($_SESSION['sess_level'], $read['slev']['read']))){
				return $out;	
			}
		}else{
			return $out;	
		}
			
	}


	function crudCreate($create) 
	{
		$out ="";
		if(!empty($_GET['op']) and $_GET['op']=='add')
		{

			$col  = "";
			$tbl  = "";
			$pk   = "";
			$slev = "";
			foreach ($create as $key => $value) {
				switch ($key) {
					case 'col':
						$col = $value;
						break;
					case 'tbl':
						$tbl = $value;
						break;
					case 'pk':
						$pk = $value;
						break;
					case 'slev':
						$slev = $value;
						break;
					default:
						# code...
						break;
				}
			}
			$sql = "select $col from $tbl";
			$query = mysql_query($sql);
		
			$out .= "<div class=\"row-fluid\">
						<div class=\"span12\">
							<div class=\"box\">
								<div class=\"box-head\">
								<h3>Tambah Data ".ucwords(str_replace("_", " ", $_GET['m']))."</h3>
							</div>
						<div class=\"box-content\">
        			";
			if(isset($_POST['simpan'])){
				$i=0;
				$cols="";
				$vals="";
		    	while ($i < mysql_num_fields($query)) {
		    		$meta = mysql_fetch_field($query,$i);
		    		if($meta->name=='image' or $meta->name=='file' or $meta->name=='photo' or $meta->name=='gambar' )
		    		{
		    			upload($meta->name,$_SESSION['sess_login']."_".date('dmyHis')."_","files");
		    			$cols .= $meta->name.",";	    	    	
						$vals .= "'".$_SESSION['sess_login']."_".date('dmyHis')."_".$_FILES[$meta->name]['name']."',";    				    	
					}else{
						$cols .= $meta->name.",";	    	    	
						$vals .= "'".$_POST[$meta->name]."',";    				    	
					}	
		    			 
			    	$i++;
		        }
		        $all_cols = substr($cols,0,strlen($cols)-1);
		        $all_vals = substr($vals,0,strlen($vals)-1);        
		        $ins = mysql_query("insert into $tbl($all_cols) values($all_vals)");
		        if($ins){
		        	$out .=  alert_success();
		        }else{
		        	$out .=  alert_failed();
		        }
			}

			$out .= "
			<form action='' class='form-horizontal form validate' method='post' enctype='multipart/form-data'>
			<fieldset>
				 ";				 
			    	//cols
			    	$i=0;
			    	while ($i < mysql_num_fields($query)) { 
			    		$meta = mysql_fetch_field($query,$i);
			    		
			    		$flags = mysql_field_flags($query, $i);	
				    	if (!$meta) {
			    		    $out .=  "No information available<br />\n";
				    	}else{	    	

				    			if($meta->not_null==1){ $required="required"; }else{ $required=""; }					    			
				    			//tampilkan berdasarkan nama kolom pada query
				    			switch($meta->name)
				    			{
				    				case $pk:
				    					$code = date("dhis");
				    					$label = $meta->name;	    					
				    					$inp = form_input($pk,"text",$code,"readonly class = '".$required."'");					
				    				break;
				    				case "password":  
				    					$label = $meta->name;					
				    					$inp = form_input($meta->name,"password","","class = '".$required."'");					
				    				break;
				    				case "email": 
				    					$label = $meta->name; 					
				    					$inp = form_input($meta->name,"email","","class = '".$required." email'");					
				    				break;
				    				case "file":  
				    					$label = $meta->name;					
				    					$inp = form_input($meta->name,"file","","class = '".$required." uniform'");					
				    				break;
				    				case "photo": 
				    					$label = $meta->name; 					
				    					$inp = form_input($meta->name,"file","","class = '".$required." uniform'");					
				    				break;
				    				case "gambar":  					
				    					$label = $meta->name;
				    					$inp = form_input($meta->name,"file","","class = '".$required." uniform'");					
				    				break;
				    				case "image":
				    					$label = $meta->name;  					
				    					$inp = form_input($meta->name,"file","","class = '".$required." uniform'");					
				    				break;
				    				default:	

				    					//check relation
						    			if(array_key_exists($meta->name, $create['rel']['1_n'])){
						    				$label = $create['rel']['1_n'][$meta->name]['display'];					
						    				$inp = form_dropdown_table(
						    						$create['rel']['1_n'][$meta->name]['tbl'],
						    						array($create['rel']['1_n'][$meta->name]['id_col'],$create['rel']['1_n'][$meta->name]['display']),
						    						$create['rel']['1_n'][$meta->name]['id_col'],
						    						null,null,null);
						    			}else{  
								    			
						    					//tampilkan UI berdasarkan type pada database
						    					switch ($meta->type) {
						    						case 'blob':
						    							$label = $meta->name;
						    							$inp = form_textarea($meta->name);
						    							break;
						    						case 'string':
						    							switch ($flags) {
						    								case 'not_null enum':
						    									$label = $meta->name;
						    									$inp = form_dropdown_enum($tbl,$meta->name,null,"class = '".$required." uniform'");					
						    								break;
						    								default:
						    									$label = $meta->name;
							    								$inp = form_input($meta->name,'text',null,""."class = '".$required." uniform'");					
							    							break;
						    							}					    							
						    							break;
						    						case 'date':
						    							$label = $meta->name;
						    							$inp = form_input($meta->name,'text',date('Y-m-d')," "."class = 'datepick ".$required." uniform'");					
						    							break;
						    						case 'time':
						    							$label = $meta->name;
						    							$inp = form_input($meta->name,'text',date('H:i:s')," "."class = 'timepicker ".$required." uniform'");					
						    							break;
						    						default:
						    							$label = $meta->name;
						    							$inp = form_input($meta->name,'text',null," "."class = '".$required." uniform'");					
						    							break;
						    					}
				    					}
						    			
				    			}

			    	    		$out .=  "<div class=\"control-group\">
			    	    			<label for=\"".$meta->name."\" class=\"control-label\">".str_replace("_"," ",strtoupper($label))."</label>
			    	    				<div class=\"controls\">
			    	    				$inp
			    	    				</div>
				    	    		</div>";        	
				    	    	
				        	}
					    	$i++;
				        }
				        $out .= "	                   
				            <div class=\"form-actions\">
				            	<button type=\"button\" onclick = \"javascript:location.replace('index.php?m=".$_GET['m']."')\" class=\"btn btn-danger\"  name='batal'>Batal</button>
				            	<button type=\"submit\" class=\"btn btn-primary\"  name='simpan'>Submit</button>
				            	
				            </div>                
				        </div>
					</div>
					</fieldset>									
				</form>
				</div>	";
		
		mysql_free_result($query);
		} 
		return $out;
	}
	
	function crudUpdate($update) 
	{
		$out="";
		if(!empty($_GET['op']) and $_GET['op']=='edit'){
			$col  = "";
			$tbl  = "";
			$pk   = "";
			$slev = "";
			foreach ($update as $key => $value) {
				switch ($key) {
					case 'col':
						$col = $value;
						break;
					case 'tbl':
						$tbl = $value;
						break;
					case 'pk':
						$pk = $value;
						break;
					case 'slev':
						$slev = $value;
						break;
					default:
						# code...
						break;
				}
			}
			 
		
		$wh = $pk." = '$_GET[id]'";
		$sql = "select $col from $tbl where $wh";
		$query = mysql_query($sql);
		
		$out .= "<div class=\"row-fluid\">
					<div class=\"span12\">
						<div class=\"box\">
							<div class=\"box-head\">
							<h3>Edit Data ".ucwords(str_replace("_", " ", $_GET['m']))."</h3>
						</div>
					<div class=\"box-content\">
				";
		
		//insert to database
		if(isset($_POST['simpan'])){
			$i=0;
			$set="";
	    	while ($i < mysql_num_fields($query)) {
	    		$meta = mysql_fetch_field($query,$i);
	    		if($meta->name=='image' or $meta->name=='file' or $meta->name=='photo' or $meta->name=='gambar' )
	    		{
	    			upload($meta->name,$_SESSION['sess_login']."_".date('dmyHis')."_","files");
					$set .= $meta->name."='".$_SESSION['sess_login']."_".date('dmyHis')."_".$_FILES[$meta->name]['name']."',";
				}else{
					$set .= $meta->name."='".$_POST[$meta->name]."',";
				}
		    	$i++;
	        }
	        $all_set = substr($set,0,strlen($set)-1);
	        
	       $ins = mysql_query("update $tbl set $all_set where $wh");
	        if($ins){
	        	$out .= alert_success();
	        }else{
	        	$out .= alert_failed();
	        }
	       
		}
		
		$out .= "
		<form action='' class='form-horizontal validate' method='post' enctype='multipart/form-data'>
		<fieldset>
			 ";		
		
	    	$f = mysql_fetch_array($query);
	    	//cols
	    	$i=0;
	    	while ($i < mysql_num_fields($query)) {
	    		$meta = mysql_fetch_field($query,$i);
	    		$flags = mysql_field_flags($query, $i);	
		    	if (!$meta) {
	    		    echo "No information available<br />\n";
		    	}else{	    	
		    		 
		    		//required
	    			//echo $meta->name."=".$meta->not_null;
	    			if($meta->not_null==1){ $required="required"; }else{ $required=""; }					    			
	    			//tampilkan berdasarkan nama kolom pada query
	    			switch($meta->name)
	    			{
	    				case $pk: 	
	    					$label = $meta->name;
	    					$inp = form_input($pk,"text",$f[$meta->name],"readonly class = '".$required."'");					
	    				break;
	    				case "password": 
	    					$label = $meta->name; 					
	    					$inp = form_input($meta->name,"password",$f[$meta->name],"class = '".$required."'");					
	    				break;
	    				case "email":  		
	    					$label = $meta->name;			
	    					$inp = form_input($meta->name,"email",$f[$meta->name],"class = '".$required." email'");					
	    				break;
	    				case "file":  	
	    					$label = $meta->name;	 
	    					//check is a file or image
		    				$fileName = explode(".", strtolower($f[$meta->name]));
		    				$extFile = $fileName[count($fileName)-1];
	    					if($extFile == 'jpg' or $extFile == 'png' or $extFile == 'gif' or $extFile == 'jpeg')
	    					{
	    						$disp = "<a href=\"files/".$f[$meta->name]."\" class='preview fancy'><img src=\"files/".$f[$meta->name]."\" width='64px' alt='' title=\"".$f[$meta->name]."\"></a>";
	    					}elseif($extFile == 'docx' or $extFile == 'doc' or $extFile == 'pdf'){
	    						$disp = "<a href=\"files/".$f[$meta->name]."\"><img src=\"img/icons/fugue/arrow-270.png\" alt='' title=\"".$f[$meta->name]."\"></a>";
	    					}else{
	    						$disp = $f[$meta->name];
	    					}		
	    					$inp = $disp.form_input($meta->name,"file",$f[$meta->name],"class = '".$required." uniform'");					
	    				break;
	    				case "photo":  					
	    					$label = $meta->name; 
	    					//check is a file or image
		    				$fileName = explode(".", strtolower($f[$meta->name]));
		    				$extFile = $fileName[count($fileName)-1];
	    					if($extFile == 'jpg' or $extFile == 'png' or $extFile == 'gif' or $extFile == 'jpeg')
	    					{
	    						$disp = "<a href=\"files/".$f[$meta->name]."\" class='preview fancy'><img src=\"files/".$f[$meta->name]."\" width='64px' alt='' title=\"".$f[$meta->name]."\"></a>";
	    					}elseif($extFile == 'docx' or $extFile == 'doc' or $extFile == 'pdf'){
	    						$disp = "<a href=\"files/".$f[$meta->name]."\"><img src=\"img/icons/fugue/arrow-270.png\" alt='' title=\"".$f[$meta->name]."\"></a>";
	    					}else{
	    						$disp = $f[$meta->name];
	    					}		
	    					$inp = $disp.form_input($meta->name,"file",$f[$meta->name],"class = '".$required." uniform'");					
	    				break;
	    				case "gambar":  	
	    					$label = $meta->name; 	
	    					//check is a file or image
		    				$fileName = explode(".", strtolower($f[$meta->name]));
		    				$extFile = $fileName[count($fileName)-1];
	    					if($extFile == 'jpg' or $extFile == 'png' or $extFile == 'gif' or $extFile == 'jpeg')
	    					{
	    						$disp = "<a href=\"files/".$f[$meta->name]."\" class='preview fancy'><img src=\"files/".$f[$meta->name]."\" width='64px' alt='' title=\"".$f[$meta->name]."\"></a>";
	    					}elseif($extFile == 'docx' or $extFile == 'doc' or $extFile == 'pdf'){
	    						$disp = "<a href=\"files/".$f[$meta->name]."\"><img src=\"img/icons/fugue/arrow-270.png\" alt='' title=\"".$f[$meta->name]."\"></a>";
	    					}else{
	    						$disp = $f[$meta->name];
	    					}		
	    					$inp = $disp.form_input($meta->name,"file",$f[$meta->name],"class = '".$required." uniform'");					
	    				break;
	    				case "image":  					
	    					$label = $meta->name;	
	    					//check is a file or image
		    				$fileName = explode(".", strtolower($f[$meta->name]));
		    				$extFile = $fileName[count($fileName)-1];
	    					if($extFile == 'jpg' or $extFile == 'png' or $extFile == 'gif' or $extFile == 'jpeg')
	    					{
	    						$disp = "<a href=\"files/".$f[$meta->name]."\" class='preview fancy'><img src=\"files/".$f[$meta->name]."\" width='64px' alt='' title=\"".$f[$meta->name]."\"></a>";
	    					}elseif($extFile == 'docx' or $extFile == 'doc' or $extFile == 'pdf'){
	    						$disp = "<a href=\"files/".$f[$meta->name]."\"><img src=\"img/icons/fugue/arrow-270.png\" alt='' title=\"".$f[$meta->name]."\"></a>";
	    					}else{
	    						$disp = $f[$meta->name];
	    					}		
	    					$inp = $disp.form_input($meta->name,"file",$f[$meta->name],"class = '".$required." uniform'");					
	    				break;
	    				default:	

	    					//check relation
			    			if(array_key_exists($meta->name, $update['rel']['1_n'])){
			    				$label = $update['rel']['1_n'][$meta->name]['display'];					
			    				$inp = form_dropdown_table(
			    						$update['rel']['1_n'][$meta->name]['tbl'],
			    						array($update['rel']['1_n'][$meta->name]['id_col'],$update['rel']['1_n'][$meta->name]['display']),
			    						$update['rel']['1_n'][$meta->name]['id_col'],
			    						null,$f[$update['rel']['1_n'][$meta->name]['id_col']],null);
			    			}else{  
		    					//tampilkan UI berdasarkan type pada database
		    					switch ($meta->type) {
		    						case 'blob':
		    							$label = $meta->name;			
		    							$inp = form_textarea($meta->name,$f[$meta->name]);
		    							break;
		    						case 'string':
		    							switch ($flags) {
		    								case 'not_null enum':
		    								$label = $meta->name;			
		    								$inp = form_dropdown_enum($tbl,$meta->name,$f[$meta->name],"class = '".$required." uniform'");					
		    								break;
		    								default:
		    								$label = $meta->name;			
			    							$inp = form_input($meta->name,'text',$f[$meta->name],""."class = '".$required." uniform'");					
			    							break;
		    							}					    							
		    							break;
		    						case 'date':
		    							$label = $meta->name;			
		    							$inp = form_input($meta->name,'text',$f[$meta->name]," "."class = 'datepick ".$required." uniform'");					
		    							break;
		    						case 'time':
		    							$label = $meta->name;			
		    							$inp = form_input($meta->name,'text',$f[$meta->name]," "."class = 'timepicker ".$required." uniform'");					
		    							break;
		    						default:
		    							$label = $meta->name;			
		    							$inp = form_input($meta->name,'text',$f[$meta->name]," "."class = '".$required." uniform'");					
		    							break;
		    					}
	    					}
			    			
	    			}

	    	    		 

		    			$out .=  "<div class=\"control-group\">
			    	    			<label for=\"".$meta->name."\" class=\"control-label\">".str_replace("_"," ",strtoupper($label))."</label>
			    	    				<div class=\"controls\">
			    	    				$inp
			    	    				</div>
				    	    		</div>";    

	    	    	}
	        	
		    	$i++;
	        }
	        $out .= "	                   
				            <div class=\"form-actions\">
				            	<button type=\"button\" onclick = \"javascript:location.replace('index.php?m=".$_GET['m']."')\" class=\"btn btn-danger\"  name='batal'>Batal</button>
				            	<button type=\"submit\" class=\"btn btn-primary\"  name='simpan'>Submit</button>
				            	
				            </div>                
				        </div>
					</div>
					</fieldset>										
				</form>
				</div>	";
		
		mysql_free_result($query);
		} 
		return $out;
	}

	function crudDelete($delete) 
	{
		$out="";
		if(!empty($_GET['op']) and $_GET['op']=='hapus'){
			$col  = "";
			$tbl  = "";
			$pk   = "";
			$slev = "";
			foreach ($delete as $key => $value) {
				switch ($key) {
					case 'col':
						$col = $value;
						break;
					case 'tbl':
						$tbl = $value;
						break;
					case 'pk':
						$pk = $value;
						break;
					case 'slev':
						$slev = $value;
						break;
					default:
						# code...
						break;
				}
			}
			 
	$out .= "<div class=\"row-fluid\">
				<div class=\"span12\">
					<div class=\"box\">
						<div class=\"box-head\">
						<h3>Hapus Data ".ucwords(str_replace("_", " ", $_GET['m']))."</h3>
					</div>
				<div class=\"box-content\">
				<div class=\"alert alert-info alert-block\">
					<a class=\"close\" data-dismiss=\"alert\" href=\"#\">×</a>
						<h4 class=\"alert-heading\">Information!</h4>
						Jika proses Hapus di lakukan maka, Informasi berikut akan di Hilang dari database!
				</div>	
			"; 

	$wh = $pk." = '$_GET[id]'";
	$sql = "select $col from $tbl where $wh";
	$query = mysql_query($sql);
	
	//insert to database
	if(isset($_POST['hapus'])){		      
       $ins = mysql_query("delete from $tbl where $wh");
        if($ins){
        	$out .= alert_success();
        }else{
        	$out .= alert_failed();
        }
	}
	
	$out .= "
	<form action='' class='form-horizontal' method='post'>
		<fieldset> ";
    	$f = mysql_fetch_array($query);
    	//cols
    	$i=0;
    	while ($i < mysql_num_fields($query)) {
    		$meta = mysql_fetch_field($query,$i);
	    	if (!$meta) {
    		    echo "No information available<br />\n";
	    	}else{	    	
	    		 
	    		 //check if the relation is exist
    			if(array_key_exists($meta->name, $delete['rel']['1_n'])){
    				$dtNew = mysql_fetch_array(mysql_query("select 
    							".$delete['rel']['1_n'][$meta->name]['display']." 
    							 from ".$delete['rel']['1_n'][$meta->name]['tbl']." 
    							 where 
    							 	".$delete['rel']['1_n'][$meta->name]['id_col']." = '".$f[$meta->name]."'"));
    				$label = $delete['rel']['1_n'][$meta->name]['display'];					    				
    				$val = $dtNew[$delete['rel']['1_n'][$meta->name]['display']];
    			}else{
    				$label = $meta->name; 	
					//check is a file or image
    				$fileName = explode(".", strtolower($f[$meta->name]));
    				$extFile = $fileName[count($fileName)-1];
					if($extFile == 'jpg' or $extFile == 'png' or $extFile == 'gif' or $extFile == 'jpeg')
					{
						$val = "<a href=\"files/".$f[$meta->name]."\" class='preview fancy'><img src=\"files/".$f[$meta->name]."\" width='64px' alt='' title=\"".$f[$meta->name]."\"></a>";
					}elseif($extFile == 'docx' or $extFile == 'doc' or $extFile == 'pdf'){
						$val = "<a href=\"files/".$f[$meta->name]."\"><img src=\"img/icons/fugue/arrow-270.png\" alt='' title=\"".$f[$meta->name]."\"></a>";
					}else{
						$val =  $f[$meta->name];
					}		

					
    			}
	    	

    			$out .= "<div class=\"control-group\">
							<label for=\"".$meta->name."\" class=\"control-label\">".str_replace("_"," ",strtoupper($label))."</label>
							<div class=\"controls\">
								$val
							</div>
						</div>";
	    		 
        	}
	    	$i++;
        }
        
            $out .= "	                   
	            <div class=\"form-actions\">
	            	<button type=\"button\" onclick = \"javascript:location.replace('index.php?m=".$_GET['m']."')\" class=\"btn btn-danger\"  name='batal'>Batal</button>
	            	<button type=\"submit\" class=\"btn btn-primary\"  name='hapus'>Hapus</button>	            	
	            </div>                
	        </div>
		</div>
		</fieldset>					
	</form>
	</div>	"; 
		mysql_free_result($query);
		
		}
		return $out;
	}

	function crudDetail($detail) 
	{
		$out="";
		if(!empty($_GET['op']) and $_GET['op']=='detail'){
			$col  = "";
			$tbl  = "";
			$pk   = "";
			$slev = "";
			foreach ($detail as $key => $value) {
				switch ($key) {
					case 'col':
						$col = $value;
						break;
					case 'tbl':
						$tbl = $value;
						break;
					case 'pk':
						$pk = $value;
						break;
					case 'slev':
						$slev = $value;
						break;
					default:
						# code...
						break;
				}
			}
			 
	$out .= "<div class=\"row-fluid\">
				<div class=\"span12\">
					<div class=\"box\">
						<div class=\"box-head\">
						<h3>Detail Data ".ucwords(str_replace("_", " ", $_GET['m']))."</h3>
					</div>
				<div class=\"box-content\"> 
			"; 

	$wh = $pk." = '$_GET[id]'";
	$sql = "select $col from $tbl where $wh";
	$query = mysql_query($sql);
	
	 
	$out .= "
	<form action='' class='form-horizontal' method='post'>
		<fieldset> ";
    	$f = mysql_fetch_array($query);
    	//cols
    	$i=0;
    	while ($i < mysql_num_fields($query)) {
    		$meta = mysql_fetch_field($query,$i);
	    	if (!$meta) {
    		    echo "No information available<br />\n";
	    	}else{	    	
	    		 
    			 //check if the relation is exist
    			if(array_key_exists($meta->name, $detail['rel']['1_n'])){
    				$dtNew = mysql_fetch_array(mysql_query("select 
    							".$detail['rel']['1_n'][$meta->name]['display']." 
    							 from ".$detail['rel']['1_n'][$meta->name]['tbl']." 
    							 where 
    							 	".$detail['rel']['1_n'][$meta->name]['id_col']." = '".$f[$meta->name]."'"));
    				$label = $detail['rel']['1_n'][$meta->name]['display'];					    				
    				$val = $dtNew[$detail['rel']['1_n'][$meta->name]['display']];
    			}else{
    				$label = $meta->name; 	
					//check is a file or image
    				$fileName = explode(".", strtolower($f[$meta->name]));
    				$extFile = $fileName[count($fileName)-1];
					if($extFile == 'jpg' or $extFile == 'png' or $extFile == 'gif' or $extFile == 'jpeg')
					{
						$val = "<a href=\"files/".$f[$meta->name]."\" class='preview fancy'><img src=\"files/".$f[$meta->name]."\" width='64px' alt='' title=\"".$f[$meta->name]."\"></a>";
					}elseif($extFile == 'docx' or $extFile == 'doc' or $extFile == 'pdf'){
						$val = "<a href=\"files/".$f[$meta->name]."\"><img src=\"img/icons/fugue/arrow-270.png\" alt='' title=\"".$f[$meta->name]."\"></a>";
					}else{
						$val =  $f[$meta->name];
					}	
    			}

    			$out .= "<div class=\"control-group\">
							<label for=\"".$meta->name."\" class=\"control-label\">".str_replace("_"," ",strtoupper($label))."</label>
							<div class=\"controls\">
								$val
							</div>
						</div>";
	    		 
        	}
	    	$i++;
        }
        
            $out .= "	                   
	            <div class=\"form-actions\">
	            	<button type=\"button\" onclick = \"javascript:location.replace('index.php?m=".$_GET['m']."')\" class=\"btn btn-danger\"  name='batal'>Kembali</button>
	            	
	            </div>                
	        </div>
		</div>
		</fieldset>								
	</form>
	</div>	"; 
		mysql_free_result($query);
		
		}
		return $out;
	}
?>
