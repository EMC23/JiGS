
 
<?php defined( '_JEXEC' ) or die( 'Restricted access' ); 
?>


<div id="papers" style="
width:49%;
float:left;
">


<div id="b_i_t_title" style="
text-transform:uppercase;
font-weight:bold;
background:#333;
color:#000;
text-align:center;
margin:0 0 10px 0;
">
* * * * For Sale * * * *</div>

<table id="building_papers_table" class="shade-table">
<tbody>
<tr>

</tr>
</tbody>
</table>
</div>

<div style="
width:49%;
float:right;"
>
<div id="m_i_t_title" style="
text-transform:uppercase;
font-weight:bold;
background:#333;
color:#000;
text-align:center;
margin:0 0 10px 0;
">
* * * * My Blueprints * * * *</div>

<table id="my_papers"class="shade-table">
<tbody>
<tr>

</tr>
</tbody>
</table>






</div><!--end inventory-->











<script type='text/javascript'>
function request_inventory(id){
	 var all = '<table class="shade-table"><tbody>';
		var details = this.details;
	
	var a = new Request.JSON({
    url: "index.php?option=com_battle&format=raw&task=action&action=get_shop_blueprints&building_id=<?php echo $this->buildings->id ; ?>", 
    onSuccess: function(result){
       	    	
   for (i = 0; i < result.length; ++ i){
  var row = "<tr class=\"d" + (i & 1) + "\"><td> Blueprint for " + (i+1) + ": " + result[i].name + ":</td><td>$" + result[i].sell_price + "<a href='#' class='buy' id='" + result[i].object + "'>[BUY]</a></td></tr>"; 
  all= all + row;  
        	}
all= all + '</tbody></table>';
   	$('building_papers_table').innerHTML = all;	
   	   	 $$('.buy').addEvent('click', function(){
  	
 		 var itemID = this.get('id');
 		 		  buy(itemID);
 		 });
   	
   }	
    	    }).get();

}

function request_inventory2(){
	
 var all = '<table class="shade-table"><tbody>';
		var details = this.details;
	
	var a = new Request.JSON({
    url: "index.php?option=com_battle&format=raw&task=action&action=get_blueprints", 
    onSuccess: function(result){
       	    	
   for (i = 0; i < result.length; ++ i){
  var row = "<tr class=\"d" + (i & 1) + "\"><td> Blueprint for " + result[i].name + "</td></tr>"; 
  all= all + row;  
    	}
    	
    	
    all= all + '</tbody></table>';	
    	
    	
    	$('my_papers').innerHTML = all;	
    	
    	  	
   	   	 
    }	
    	
    }).get();

}


request_inventory2.periodical(1000);
request_inventory.periodical(1000);



function buy(itemID){
 
	var a = new Request.JSON({
    url: "index.php?option=com_battle&format=raw&task=action&action=buy_blueprints&building_id=<?php echo $this->buildings->id ; ?>&item=" + itemID, 
    onSuccess: function(result){
    	    
    	}
    }).get();
 
}



</script>


