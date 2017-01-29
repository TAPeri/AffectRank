 <?php

     
           $link = new mysqli('hectorpmartinezcom.ipagemysql.com', 'annotator_2', 'annotator_2','emotion_annotation123'); 
           if (mysqli_connect_errno()) { 
               error_routine('Connection failed. Please try again in few minutes.');
           }
                      

           
           	$query = 'SELECT ContinuousFirst, COUNT( ContinuousFirst )'.
					 ' FROM  Users' .
					 ' GROUP BY ContinuousFirst;';
           			
      //echo $query;

           if($result = $link->query($query)){
           	
           		  $row1 = $result->fetch_row();
           		  $row2 = $result->fetch_row();
           		  

           		  if(is_null($row1)){//First user

           		  	 $contFirst = 1;
           		  }else if (is_null($row2)){//only one type of user

           		  	$contFirst = 1-$row1[0];
           		  }else{

           		  	if($row1[1]> $row2[1]){
           		  		$contFirst = $row2[0];
           		  	}else{
           		  		$contFirst = $row1[0];

           		  	}
           		  	
           		  }

           		  
           }else {
                $link->close();
                 error_routine("Connection failed. Please try again in few minutes. (No min count) ".$query);            
           }
           
	  		
?>