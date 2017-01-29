 <?php

     function error_routine($info = ""){ 
            echo "An error has occurred.<br/>".$info."<br/>"; 
            die();
     } 

           $link = new mysqli('hectorpmartinezcom.ipagemysql.com', 'annotator_2', 'annotator_2','emotion_annotation123'); 
           if (mysqli_connect_errno()) { 
               error_routine('Connection failed. Please try again in few minutes.');
           }
                      
           $percent = $_POST['percent'];           
           $completedVideos = $_POST['completedVideos'];
           $userID = $_POST['userID'];


           
           $query = 'UPDATE Users SET '.
           			'CurrentPercent = '.$percent.','.
           			'FinishedVideos ='.$completedVideos.
           			' WHERE ID = '. $userID;
           			

		   
		  // echo $query;
 
           $link->query($query);
           
           if ($link->error){
                    $info = $link->error;
                    $link->close();
                    error_routine($query.'**'.$info);

           }
           
	  		
?>