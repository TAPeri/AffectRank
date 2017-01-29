 <?php

     function error_routine($info = ""){ 
            echo "An error has occurred.<br/>".$info."<br/>"; 
            die();
     } 

           $link = new mysqli('hectorpmartinezcom.ipagemysql.com', 'annotator_2', 'annotator_2','emotion_annotation123'); 
           if (mysqli_connect_errno()) { 
               error_routine('Connection failed. Please try again in few minutes.');
           }
           
           
           $userID = $_POST['userID'];           
           $continuous = $_POST['continuous'];
           $videoName = $_POST['videoName'];


			$query = 'SELECT VideoTime,Arousal,Valence FROM Annotations '.
           			'WHERE UserID = '. $userID.
           			' AND ContinuousTask = '.$continuous.
           			' AND VideoName = "'.$videoName.'"'.
           			' ORDER BY ID DESC LIMIT 1;';


		              
           if($result = $link->query($query)){
                    
                    $row = $result->fetch_row();
                    
                    if(count($row)==3)
                    	echo $row[0].','.$row[1].','.$row[2];
                    	
                    else
                    	echo 'first';
                    
                    
           }else {
                $link->close();
                 error_routine("Connection failed. Please try again in few minutes. (No min count) ".$query);            
           }   
                    
           
	  		
?>