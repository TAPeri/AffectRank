<!DOCTYPE html>
<html>
<head>
<script src="http://ajax.aspnetcdn.com/ajax/jQuery/jquery-1.11.2.min.js"></script>
</head>
<body>

<h1>Progress</h1>


     <?php
     
     	   $tutorialVideo = 'garyTutorial';
     	   $videos = array('video/01','video/02','video/03','video/04','video/05');

           $link = new mysqli('hectorpmartinezcom.ipagemysql.com', 'annotator_2', 'annotator_2','emotion_annotation123'); 
           if (mysqli_connect_errno()) { 
               error_routine('Connection failed. Please try again in few minutes.');
           }
           
           if($_POST['username']){
           
           		$query_find_user = 'SELECT ID'
               		. ' from Users '
               		. ' WHERE Users . Name = "'.$_POST['username'].'";' ;


				$contFirst = 1;

           		if($result = $link->query($query_find_user)){
                    $row = $result->fetch_row();
                    
                    if(count($row)==1){
                    	
                    	$userID = $row[0];   
                    	
                    }else{
                    	

						include 'countUsers.php';   
                    	
                 		$query = 'INSERT INTO Users(' . 
                 		'Name,' .
                 		'ContinuousFirst,' . 
                 		'CurrentPercent,' . 
                 		'FinishedVideos) VALUES ('.
                 		'"' . $_POST['username'].'"' .
                 		', '. $contFirst .
                 		',0.0' .
                 		',0);';
                 		
                 		$link->query($query);                 		

                		if ($link->error){
                    		$info = $link->error;
                    		$link->close();
                    		error_routine('What?'.$query.'**'.$info);

               			}else{
                    		$userID = $link->insert_id;
                		}                    	
                    }
                    
                    $result->close(); 
           		} else {
                	$link->close();
                 	error_routine("Connection failed. Please try again in few minutes. (No min count) ".$query_find_user);            
           		}            
           }else{
           	
           	$userID = $_GET['userID'];
           }
           
           $query = 'SELECT *'
               . ' from Users '
               . ' WHERE Users . ID = '.$userID ;
           
           
           if($result = $link->query($query)){
                    $row = $result->fetch_row();
           
           			$username = $row[1];
           			$contFirst = $row[2];
           			$percent = $row[3];
           			$completedVideos = $row[4];
           
           
           } else {
                $link->close();
                 error_routine("FAIL".$query);            
           }
           
           
           echo '<h2>Hello '.$username.'!</h2>';
           
           
           echo '<table>';

           echo '<tr><td colspan=2><h3>Task 1</h3></td>';
           
           if($completedVideos<count($videos)){
 		    if($contFirst==1){
			  
			  	 echo '<td><form name="next" method="post" action="tutorialContinuous.php" >'."\n";
			}else{
			  	
			  	  echo '<td><form name="next" method="post" action="tutorialOrdinal.php" >'."\n";
			}        
			echo '<input type="hidden" name="userID" value='.$userID.'>'."\n"; 
			echo '<input type="hidden" name="completedVideos" value='.$completedVideos.'>'."\n"; 
			echo '<input type="hidden" name="video" value='.$videos[$completedVideos].'>'."\n"; 			 			
			echo '<input type="hidden" name="videoTutorial" value='.$tutorialVideo.'>'."\n"; 
       		echo '<input  name="submit" type="submit" value="Tutorial">'."\n";
			echo '</form>';			
           }
           echo '</td></tr>';			

		   for($i=0;$i<count($videos);$i++){
				
				echo '<tr><td>Video '.($i+1).'</td>';
				
				if($i<$completedVideos){
				
					echo '<td>100%</td>';	
					
					if($contFirst==1){
			  
			  	 		echo '<td><form name="next" method="post" action="continuous.php" >'."\n";
			  		}else{
			  	
			  			echo '<td><form name="next" method="post" action="ordinal.php" >'."\n";
			  		}
			  		
			  		echo '<input type="hidden" name="userID" value='.$userID.'>'."\n"; 
			  		echo '<input type="hidden" name="redo" value=1>'."\n"; 
			 		echo '<input type="hidden" name="completedVideos" value='.$i.'>'."\n"; 
			 		echo '<input type="hidden" name="video" value='.$videos[$i].'>'."\n"; 			 
       		 		echo '<input  name="submit" type="submit" value="Redo">'."\n";
			 		echo '</form></td>';
					
					
					
					
					
				}else if($i>$completedVideos){
					
					echo '<td>0%</td><td></td>';	

				}else{
					
					echo '<td>'.(100*$percent).'%</td><td>';	
					
					if(($i>0) || ($percent>0)){

					if($contFirst==1){
			  
			  	 		echo '<form name="next" method="post" action="continuous.php" >'."\n";
			  		}else{
			  	
			  			echo '<form name="next" method="post" action="ordinal.php" >'."\n";
			  		}
			  		
			  		echo '<input type="hidden" name="userID" value='.$userID.'>'."\n"; 
			 		echo '<input type="hidden" name="completedVideos" value='.$completedVideos.'>'."\n"; 
			 		echo '<input type="hidden" name="video" value='.$videos[$i].'>'."\n"; 			 
       		 		echo '<input  name="submit" type="submit" value="Continue">'."\n";
			 		echo '</form>';
					}
					echo '</td>';

				}
				echo '</tr>';
				
			}
           
           echo '<tr><td colspan=2><h3>Task 2</h3></td>';
           
           if(($completedVideos>=count($videos))&&($completedVideos<(2*count($videos)))){

           if($contFirst==1){
			   echo '<td><form name="next" method="post" action="tutorialOrdinal.php" >'."\n";

			}else{
			  	echo '<td><form name="next" method="post" action="tutorialContinuous.php" >'."\n";

			}        
			echo '<input type="hidden" name="userID" value='.$userID.'>'."\n"; 
			echo '<input type="hidden" name="videoTutorial" value='.$tutorialVideo.'>'."\n"; 
			echo '<input type="hidden" name="completedVideos" value='.$completedVideos.'>'."\n"; 
			echo '<input type="hidden" name="video" value='.$videos[($completedVideos-count($videos))].'>'."\n"; 			 			
       		echo '<input  name="submit" type="submit" value="Tutorial">'."\n";
			echo '</form>';		
           }
           echo '</td></tr>';
           
		   for($i=0;$i<count($videos);$i++){
				
				echo '<tr><td>Video '.($i+1+count($videos)).'</td>';
				
				if(($i+count($videos))<$completedVideos){
				
					echo '<td>100%</td>';	
					
					if($contFirst==1){
			  			 echo '<td><form name="next" method="post" action="ordinal.php" >'."\n";

			  		}else{
			  			echo '<td><form name="next" method="post" action="continuous.php" >'."\n";

			  		}
			  		
			  		echo '<input type="hidden" name="userID" value='.$userID.'>'."\n"; 
			  		echo '<input type="hidden" name="redo" value=1>'."\n"; 
			 		echo '<input type="hidden" name="completedVideos" value='.$i.'>'."\n"; 
			 		echo '<input type="hidden" name="video" value='.$videos[$i].'>'."\n"; 			 
       		 		echo '<input  name="submit" type="submit" value="Redo">'."\n";
			 		echo '</form></td>';
					
					
					
					
				}else if(($i+count($videos))>$completedVideos){
					
					echo '<td>0%</td><td></td>';	

				}else{
					
					echo '<td>'.(100*$percent).'%</td><td>';	
					
					if(($i>0) || ($percent>0)){
					if($contFirst==1){
			  			echo '<form name="next" method="post" action="ordinal.php" >'."\n";

			  		}else{
			  			echo '<form name="next" method="post" action="continuous.php" >'."\n";

			  		}
			  		
			  		echo '<input type="hidden" name="userID" value='.$userID.'>'."\n"; 
			 		echo '<input type="hidden" name="completedVideos" value='.$completedVideos.'>'."\n"; 
			 		echo '<input type="hidden" name="video" value='.$videos[$i].'>'."\n"; 			 
       		 		echo '<input  name="submit" type="submit" value="Continue">'."\n";
			 		echo '</form>';
					}
					echo '</td>';

				}
				echo '</tr>';
				
			}          
            echo '</table>';

        
             function error_routine($info = ""){ 
            echo "An error has occurred.<br/>".$info."<br/>"; 
            die();
     } 
        
    ?>


</body>
</html>