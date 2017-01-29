<!DOCTYPE html>
<html>

<head>

<title>Annotation experiment</title>
<script src="http://ajax.aspnetcdn.com/ajax/jQuery/jquery-1.11.2.min.js"></script>
</head>
<body>


<?php
	echo '<h1>Video '.($_POST['completedVideos']+1).'</h1>';

?>

<table>

<!--<tr>-->
<!--<td colspan=2>-->
<!--<ul>-->
<!--<li>Click on the green circle to (re)start the anotation-->
<!--<li>Click again to pause the anotation-->
<!--</ul>-->
<!--</td>-->
<!--</tr>-->

<tr>
<td><video id="video1" width="500">
  <?php 
  			if(strpos($_SERVER['HTTP_USER_AGENT'],'Chrome')!=false) 
  				echo '<source src="'.$_POST['video'].'.webm" type="video/webm">'; 
  			else if(strpos($_SERVER['HTTP_USER_AGENT'],'Safari')!=false) 
  				echo '<source src="'.$_POST['video'].'.mp4" type="video/mp4">'; 
  			else
  				echo '<source src="'.$_POST['video'].'.webm" type="video/webm">';
  ?> 
  Your browser does not support the video tag.
</video>
</td>
<td><canvas id="myCanvas" width="500" height="500"></canvas>
</td></tr>
</table>
<p id="text"></p>

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
	
var flag = <?=$_POST['redo']?>+0;			
	


myVideo.onended = function(e) {


				insertRoutine();

				if(flag)
					window.location.href = 'panel.php?userID='+<?=$_POST['userID']?>;
				else			
					$.post('updatePercent.php',{percent:'0', completedVideos:<?=$_POST['completedVideos']+1?>,userID:<?=$_POST['userID']?>},function(data){ window.location.href = 'panel.php?userID='+<?=$_POST['userID']?>; });
				//,function( data ) { alert( "Data updated: " + data );}
				


    };	


function insertRoutine(){
					if(arousal.length>0)
			  		$.post('insert.php', {userID:<?=$_POST['userID']?>, 'data1[]': arousal, 'data2[]':valence, 'anotime[]':anotime, contTask:1 , video:"<?=$_POST['video']?>"});
 //,function( data ) { alert( "Data Loaded: " + data );}
				arousal.length = 0;
				valence.length = 0;
				anotime.length = 0;
	
	
}

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
	
	
	
	$.post('findLastAnnotation.php', {userID:<?=$_POST['userID']?>, continuous:1, videoName:"<?=$_POST['video']?>"},function( data ) { 
		
		var output = data.split(",");

		if ((output.length==3)&!flag){
			
			if(navigator.userAgent.indexOf("Chrome")!=-1){
				
				
				for	(index = 0; index < x.length; index++) {

					x[index]= centerX; 
					y[index]= centerY; 
				}
	
				currentA[0] = centerX;
				currentA[1] = centerY;
				loading = false;


			}else{

				prevVideoTime = Number(output[0]);
				var prevArousal = margin+(((1-Number(output[1]))/2)*(canvas.height-(2*margin)));
				var prevValence = margin+(((Number(output[2])+1)/2)*(canvas.width-(2*margin)));
			
			
				for	(index = 0; index < x.length; index++) {

					x[index]= prevValence; 
					y[index]= prevArousal; 
				}
	
				currentA[0] = prevValence;
				currentA[1] = prevArousal;
			
			
				if (myVideo.readyState==0){
					myVideo.addEventListener("loadedmetadata", function() {
     					this.currentTime = prevVideoTime;
     					loading = false;
     				//alert("loaded 1");
     				//alert( "Loaded 1: " + prevVideoTime +" "+myVideo.currentTime);
						}, false);
				}else{
					myVideo.addEventListener("canplay", function() {
						
     				this.currentTime = prevVideoTime;
     				loading = false;
     				//alert("loaded 1");
     				//alert( "Loaded 2: " + prevVideoTime +" "+myVideo.currentTime);
					}, false);
				

				}
			}
				
			
			
		}else{
			
				for	(index = 0; index < x.length; index++) {

					x[index]= centerX; 
					y[index]= centerY; 
				}
	
				currentA[0] = centerX;
				currentA[1] = centerY;
			
				if (myVideo.readyState==0){

					myVideo.addEventListener("loadedmetadata", function() { loading = false; }, false);			
				}else{
					loading = false;
				}		
		}

		});
	
	

	
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
			click[0] = ev.offsetX;
			click[1] = ev.offsetY; 
    	}else{
			click[0] = ev.layerX-offsetX;
			click[1] = ev.layerY-offsetY;    		
    	}
		
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
		   		
		   		insertRoutine();

				if(!flag)
					$.post('updatePercent.php',{percent:(myVideo.currentTime/myVideo.duration), completedVideos:<?=$_POST['completedVideos']?>,userID:<?=$_POST['userID']?>});
		   		//,function( data ) { alert( "Data Updated: " + data );}
		
		
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
	  		
	  		arousal.push(1-(  (   (currentA[1]-margin)/(canvas.width-(2*margin)))*2));
	  		valence.push((((currentA[0]-margin)/(canvas.height-(2*margin)))*2)-1);
	  		anotime.push(0+myVideo.currentTime);
	  		
	  		
	  		if(anotime.length>19){
		  		insertRoutine();
				
	  		}
	  		
	  		
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
