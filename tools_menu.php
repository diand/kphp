<?php
	
	 
function menu($option)
{
$out = "";
$out .= "
<div class=\"navi\">
    <ul class=\"main-nav\">";
    foreach ($option as $key => $value) {
    	if(empty($key) and empty($_SESSION['sess_level'])){ 
    		foreach ($value as $keyM => $valueM) { 
    			if(is_array($valueM['url']))
    			{
    				$cnt=0;
    				for($arx=0;$arx<count($valueM['url']);$arx++)
    				{    				
    					$ac = array_search($_GET['m'], $valueM['url'][$arx]);
    					if(!empty($ac)){
    						$cnt++;
    					}
    				}
    					
    					if($cnt>0){
							$actop = "class=\"active\"";
							$opc = "open";
    					}else{
    						$actop = "";
    						$opc = "closed";
    					}

    					$out .= " 
    					<li ".$actop.">                             
                            <a class='light toggle-collapsed' href=\"#\" >
                                <div class=\"ico\"><i class=\"".$valueM['ico']." icon-white\"></i></div>
                                ".$valueM['label']."
                                <img src=\"img/toggle-subnav-down.png\" alt=\"\">
                            </a>
                            <ul class='collapsed-nav ".$opc."'>                            
                            ";
                            foreach ($valueM['url'] as $keyMC => $valueMC) {
                            	if($valueMC['url']==$_GET['m']){
				    				$act = "class=\"active\"";
				    			}else{
				    				$act = "";
				    			}
                            	$out .= " 
									<li ".$act.">	
				                        <a href=\"index.php?m=".$valueMC['url']."\">
				                            <div class=\"ico\"></div>
				                            ".$valueMC['label']."
				                        </a>
				                    </li>";	
                            }
                        $out .= "
                        	</ul>
                        </li>";

    			}else{
	    			if($valueM['url']==$_GET['m']){
	    				$act = "class=\"active\"";
	    			}else{
	    				$act = "";
	    			}

					$out .= " <li ".$act.">	
	                        <a class='light' href=\"index.php?m=".$valueM['url']."\">
	                            <div class=\"ico\"><i class=\"".$valueM['ico']." icon-white\"></i></div>
	                            ".$valueM['label']."
	                        </a>
	                    </li>";	
    			}
    			
    		}
    	}elseif(!empty($key) and !empty($_SESSION['sess_level']) and $key == $_SESSION['sess_level']){
    		
    		foreach ($value as $keyM => $valueM) {
    			
    			if(is_array($valueM['url']))
    			{
    				$cnt=0;
    				for($arx=0;$arx<count($valueM['url']);$arx++)
    				{    			
    					if(!empty($_GET['m'])){	
	    					$ac = array_search($_GET['m'], $valueM['url'][$arx]);
	    					if(!empty($ac)){
	    						$cnt++;
	    					}
    					}
    				}
    					
    					if($cnt>0){
							$actop = "class=\"active\"";
							$opc = "open";
    					}else{
    						$actop = "";
    						$opc = "closed";
    					}

    					$out .= " 
    					<li ".$actop.">                             
                            <a class='light toggle-collapsed' href=\"#\" >
                                <div class=\"ico\"><i class=\"".$valueM['ico']." icon-white\"></i></div>
                                ".$valueM['label']."
                                <img src=\"img/toggle-subnav-down.png\" alt=\"\">
                            </a>
                            <ul class='collapsed-nav ".$opc."'>                            
                            ";
                            foreach ($valueM['url'] as $keyMC => $valueMC) {
                            	if(!empty($_GET['m']) and $valueMC['url']==$_GET['m']){
				    				$act = "class=\"active\"";
				    			}else{
				    				$act = "";
				    			}
                            	$out .= " 
									<li ".$act.">	
				                        <a href=\"index.php?m=".$valueMC['url']."\">
				                            <div class=\"ico\"></div>
				                            ".$valueMC['label']."
				                        </a>
				                    </li>";	
                            }
                        $out .= "
                        	</ul>
                        </li>";

    			}else{

	    			if(!empty($_GET['m']) and $valueM['url']==$_GET['m']){
	    				$act = "class=\"active\"";
	    			}else{
	    				$act = "";
	    			}

						$out .= " 
						<li ".$act.">	
	                        <a class='light' href=\"index.php?m=".$valueM['url']."\">
	                            <div class=\"ico\"><i class=\"".$valueM['ico']." icon-white\"></i></div>
	                            ".$valueM['label']."
	                        </a>
	                    </li>";	
    			}
    		
    		}
    	}
    }
    			
         
$out .= "
	</ul> 
</div>";                    
       

		return $out;
	}
?>
