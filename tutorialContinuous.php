<!DOCTYPE html>
<html>

<head>
<title>Tutorial</title>
<script src="http://ajax.aspnetcdn.com/ajax/jQuery/jquery-1.11.2.min.js"></script>
</head>
<body>

<h1>Tutorial</h1>
<h3>Test the interface and click continue when you are ready to proceed with the main experiment.</h3>

	<?php
	echo '<form name="next" method="post" action="continuous.php" >'."\n";
	echo '<input type="hidden" name="userID" value='.$_POST['userID'].'>'."\n"; 
	echo '<input type="hidden" name="completedVideos" value='.$_POST['completedVideos'].'>'."\n"; 
	echo '<input type="hidden" name="video" value="'.$_POST['video'].'">'."\n"; 
    echo '<input  name="submit" type="submit" value="Continue">'."\n";
	echo '</form>';
	?>

<table>

<tr>
<td colspan=2>
<h4>Tutorial instructions</h4>
<ul>
<li>Click on the blue circle to start or resume the anotation
<li>Move the cursor within the circular annotation area following the changes in arousal and valence seen on the video
<li>Click on the green circle to pause the anotation
</ul>
</td>

</tr>

<tr>
<td><video id="video1" width="500">
  <?php 
  			if(strpos($_SERVER['HTTP_USER_AGENT'],'Chrome')!=false) 
  				echo '<source src="'.$_POST['videoTutorial'].'.webm" type="video/webm">'; 
  			else if(strpos($_SERVER['HTTP_USER_AGENT'],'Safari')!=false) 
  				echo '<source src="'.$_POST['videoTutorial'].'.mp4" type="video/mp4">'; 
  			else
  				echo '<source src="'.$_POST['videoTutorial'].'.webm" type="video/webm">';
  ?> 

  <!--<source src=<?php echo '"'.$_POST['videoTutorial'].'.ogv"';?> type="video/ogg">-->
  Your browser does not support the video tag.
</video>
</td>
<td><canvas id="myCanvas" width="500" height="500"></canvas>
</td></tr>
</table>



<script> 

var myVideo = document.getElementById("video1"); 
var canvas = document.getElementById('myCanvas');
var context = canvas.getContext('2d');  
  
var centerX = canvas.width / 2;
var centerY = canvas.height / 2;

var areaRadius = (canvas.width/2)*0.9;
      
var started = false;
var x = [0,0,0,0,0];
var y = [0,0,0,0,0];
var margin =30;
var radius = 4;
var currentA = [0,0];
var arousal = [];
var valence = [];
var anotime = [];
	
	


var prevVideoTime;
var loading = true;

var offsetX = 0;
var offsetY = 0;



function init () {
	
	if(navigator.userAgent.indexOf("Firefox")!=-1){
        offsetX = canvas.getBoundingClientRect().left;
        offsetY = canvas.getBoundingClientRect().top;
    }
	// Attach the mousemove event handler
	canvas.addEventListener('mousemove', ev_mousemove, false);
	canvas.addEventListener('mousedown', pause, false);
	
	

			
				for	(index = 0; index < x.length; index++) {

					x[index]= centerX; 
					y[index]= centerY; 
				}
	
				currentA[0] = centerX;
				currentA[1] = centerY;
			
				//alert( "first annotation:" );
				loading = false;
			
	
	window.setInterval(function(){
	
			x.pop();
			y.pop();
			x.unshift(currentA[0])
			y.unshift(currentA[1])
			
			redraw();

	
	}, 100);
	
	redraw();
	

}


	function pause(ev){
	
		if(loading) return;
		var click = [0,0];
		
		if(navigator.userAgent.indexOf("Chrome")!=-1){
			click[0] = ev.offsetX;;
			click[1] = ev.offsetY; 
    	}else{
			click[0] = ev.layerX-offsetX;;
			click[1] = ev.layerY-offsetY;    		
    	}
		

		
		//alert((click[0])+' '+(click[1])+') ('+ ev.target.offsetLeft +' '+ ev.target.offsetTop+') ('+ ev.target.scrollLeft +' '+ ev.target.scrollTop+') ('+ev.clientX+' '+ev.clientY+') ('+ev.offsetX+' '+ev.offsetY);
		
		var clicked = false;

		if(  (  ((click[0]-x[0])*(click[0]-x[0]))+ ((click[1]-y[0])*(click[1]-y[0]))  )  <  ((radius*5)*(radius*5))  ){
	
			if (myVideo.paused){
	    	    myVideo.play(); 
	    	    //myVideo.currentTime = prevVideoTime;
			}else{		
				pauseRoutine();
			}
		}
	
	}

	function pauseRoutine(){
				   		myVideo.pause(); 
		   		//prevVideoTime = myVideo.currentTime;
	
				arousal.length = 0;
				valence.length = 0;
				anotime.length = 0;
				
		
		
	}

	function redraw(){
		
		context.clearRect(0, 0, canvas.width, canvas.height);

      	context.lineWidth = 2;
      	context.strokeStyle = 'gray';

		context.beginPath();
	    context.moveTo(0, canvas.height/2);
		context.lineTo(canvas.width, canvas.height/2);
		context.stroke();
		context.closePath();	

	   	context.beginPath();
	    context.moveTo(canvas.width/2, 0);
		context.lineTo(canvas.width/2, canvas.height);
		context.stroke();
		context.closePath();	
	  
	  	context.beginPath();
		context.arc(centerX, centerY, (canvas.width/2)-margin, 0, 2 * Math.PI, false);

      	//context.rect(margin, margin, canvas.width-(2*margin), canvas.height-(2*margin));
      	context.stroke();
	  	context.closePath();
	  
	  
	  	for	(index = x.length-1; index >=0; index--) {

      		context.beginPath();
      		context.arc(x[index], y[index], radius*(5-index), 0, 2 * Math.PI, false);
      		if(myVideo.paused)
	      		context.fillStyle = 'blue';
    		else
	      		context.fillStyle = 'green';
    
      		context.fill();
     		context.lineWidth = 3;
      		context.strokeStyle = '#003300';
      		context.stroke();
	 		context.closePath();	
	  	}
	  	
	  	
	  	
	    context.fillStyle= '#000000';
		context.font = "bold 30px Verdana";
	  	context.fillText('+',canvas.width-30, centerY-2);//Valence
	  	context.fillText('-', 0, centerY-2);//Valence
	  	context.fillText('+',centerX-25, 20);
	  	context.fillText('-',centerX-20, canvas.height);

	  	context.font = "normal 15px Verdana";	  	
	  	context.fillText('(pleasant',canvas.width-70, centerY+20);//Valence
	  	context.fillText('valence)',canvas.width-65, centerY+35);//Valence
	  	context.fillText('(unpleasant', 0, centerY+20);//Valence
	  	context.fillText('valence)', 0, centerY+35);//Valence
	  	context.fillText('(active arousal)',centerX+2, 17);
	  	context.fillText('(inactive arousal)',centerX+2, canvas.height-5);	  	
	  	
	  	

	  	context.font = "20px Verdana";
	  	context.fillText('Active',canvas.width-67, 17);
	  	context.fillText('Pleasant',canvas.width-85, 36);


	  	context.fillText('Inactive',0, canvas.height-36);
	  	context.fillText('Unpleasant',0, canvas.height-17);


	  	context.fillText('Inactive',canvas.width-79, canvas.height-36);
	  	context.fillText('Pleasant',canvas.width-85, canvas.height-17);


	  	context.fillText('Active',0, 17);
	  	context.fillText('Unpleasant',0, 36);

	  	

		
	}
	
	function ev_mousemove (ev) {
		

		if (myVideo.paused){
		
		
		}else{	
			  

			if(navigator.userAgent.indexOf("Chrome")!=-1){
				currentA[0] = ev.offsetX;;
				currentA[1] = ev.offsetY; 
    		}else{
				currentA[0] = ev.layerX-offsetX;;
				currentA[1] = ev.layerY-offsetY;    		
    		}
			
	  
	  		x[0] = currentA[0];
	  		y[0] = currentA[1];	
	  		
	  		arousal.push(1-((currentA[1]/canvas.width)*2));
	  		valence.push(((currentA[0]/canvas.height)*2)-1);
	  		anotime.push(myVideo.currentTime);
	  		

			arousal.length = 0;
			valence.length = 0;
			anotime.length = 0;
				
	  		
	  		
	  		redraw();
	  		
	  			  		
	  		//if(   (x[0] < margin) || (y[0]<margin)  || (x[0] > (canvas.width-(margin)))|| (y[0] > (canvas.height-(margin)))   ){
	  		if(   (((x[0] -centerX)*(x[0] -centerX))+   ((y[0] -centerY)*(y[0] -centerY)))>(  (canvas.width/2)-margin  )*((canvas.width/2)-margin)     ){
	  		
	  			pauseRoutine(); 
		
	  		}
	  		
	  	}
	}

init();
 

</script> 

</body>
</html>
