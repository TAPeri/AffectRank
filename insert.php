 <?php

     function error_routine($info = ""){ 
            echo "An error has occurred.<br/>".$info."<br/>"; 
            die();
     } 

           $link = new mysqli('hectorpmartinezcom.ipagemysql.com', 'annotator_2', 'annotator_2','emotion_annotation123'); 
           if (mysqli_connect_errno()) { 
               error_routine('Connection failed. Please try again in few minutes.');
           }
           
		   date_default_timezone_set('CET'); 
           
           $userID = $_POST['userID'];           

           $contTask = $_POST['contTask'];
           $video = $_POST['video'];
           $anoDate = date("Y-m-d H:i:s");


           $arousal = $_POST['data1'];
           $valence = $_POST['data2'];
           $anotime = $_POST['anotime'];


           
           $query = 'INSERT INTO Annotations('.
           			'UserID,'.
           			'ContinuousTask,'.
           			'VideoTime,'.
           			'Arousal,'.
           			'Valence,'.
           			'AnnotationTime,'.
           			'VideoName) values ';
           			
           
           
           for($i = 0;$i<count($arousal);$i++){
           			
           			$query .= '('.$userID.','.
           			$contTask.','.
           			$anotime[$i] .','.
           			$arousal[$i] .','.
           			$valence[$i] .','.
           			'"'.$anoDate.'",'.
           			'"'.$video.'")';
           			
           			if(count($arousal)>1){

           				if($i<(count($arousal)-1))
	  						$query.=',';

	  					
           			}

           }


           $query.=';';

 
           $link->query($query);
           
           if ($link->error){
                    $info = $link->error;
                    $link->close();
                    error_routine($query.'**'.$info);

           }else{
           		echo $query;
           }
           
	  		
?>