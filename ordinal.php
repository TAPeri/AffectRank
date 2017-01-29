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
<!--<li>Click on the central button to pause and resume the anotation-->
<!--<li>Click on the surrounding buttons to annotate a moment when arousal and/or valence has changed-->
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
  ?>   Your browser does not support the video tag.
</video>
</td>
<td>

<canvas id="myCanvas" width="500" height="500"></canvas>


</td></tr>
</table>
<p id="text"></p>

<script> 

var myVideo = document.getElementById("video1"); 
var canvas = document.getElementById('myCanvas');
var context = canvas.getContext('2d');  
      
var started = false;
var margin =100;
var radius = 4;

var arousal = [];
var valence = [];
var anotime = [];
		
	
var centerX = canvas.width / 2;
var centerY = canvas.height / 2;

var shift = Math.cos(Math.PI/4)*((canvas.width/2)-margin);//45 degrees

var x = [centerX-shift, centerX, centerX+shift,    margin, centerX, canvas.width-margin,   centerX-shift, centerX, centerX+shift];
var y = [centerY-shift,margin,centerY-shift,centerY,centerY,centerY,centerY+shift,canvas.height-margin,centerY+shift];

var flag = <?=$_POST['redo']?>+0;			


intermediate1x = [];
intermediate1y = [];
intermediate2x = [];
intermediate2y = [];



myVideo.onended = function(e) {

				insertRoutine();

				if(flag)
					window.location.href = 'panel.php?userID='+<?=$_POST['userID']?>;
				else
					$.post('updatePercent.php',{percent:'0', completedVideos:<?=$_POST['completedVideos']+1?>,userID:<?=$_POST['userID']?>},function(data){ window.location.href = 'panel.php?userID='+<?=$_POST['userID']?>; });

};	

var loading = true;


var offsetX = 0;
var offsetY = 0;

function init () {
	// Attach the mousemove event handler
	if(navigator.userAgent.indexOf("Firefox")!=-1){
        offsetX = canvas.getBoundingClientRect().left;
        offsetY = canvas.getBoundingClientRect().top;
    }
    
    	$.post('findLastAnnotation.php', {userID:<?=$_POST['userID']?>, continuous:0, videoName:"<?=$_POST['video']?>"},function( data ) { 
		
		var output = data.split(",");


		if ((output.length==3)&!flag){
			
			
			
			if(navigator.userAgent.indexOf("Chrome")!=-1){
					loading = false;


			}else{
			
			
				var prevVideoTime = Number(output[0]);
				
				
				
				if (myVideo.readyState==0){
					myVideo.addEventListener("loadedmetadata", function() {
     					this.currentTime = prevVideoTime;
     					loading = false;
     				//alert("loaded 1");
     				//alert( "Play: " + prevVideoTime +" "+myVideo.currentTime);
						}, false);
				}else{
				    this.currentTime = prevVideoTime;
     				loading = false;
				
				}
					
			}
					
							
		}else{
			
			if (myVideo.readyState==0){

				myVideo.addEventListener("loadedmetadata", function() { loading = false; }, false);			
			}else{
				loading = false;
			}
		}

	});

	
	
	canvas.addEventListener('mousedown', click, false);

	for(index=0;index<x.length;index++){
		
		var distX = x[index]-centerX;
		var distY = y[index]-centerY;
		
		intermediate1x.push( centerX+(distX/3));
		intermediate2x.push( centerX+(distX*2/3));
		intermediate1y.push( centerY+(distY/3));
		intermediate2y.push( centerY+(distY*2/3));		
		
	}

}


function insertRoutine(){
	
				if(arousal.length>0)
			  		$.post('insert.php', {userID:<?=$_POST['userID']?>, 'data1[]': arousal, 'data2[]':valence, 'anotime[]':anotime, contTask:0 , video:"<?=$_POST['video']?>"});
 //,function( data ) { alert( "Data Loaded: " + data );}
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
//      	context.rect(margin, margin, canvas.width-(2*margin), canvas.height-(2*margin));
 		context.arc(centerX, centerY, (canvas.width/2)-margin, 0, 2 * Math.PI, false);

      	context.stroke();
	  	context.closePath();
	  
	  
	  	for	(index = x.length-1; index >=0; index--) {

      		if(index==4)
      			drawCirc(index,'green');
      		else{
      			drawCirc(index,'blue');
      			drawCircInterm1(index,'blue');
      			drawCircInterm2(index,'blue');
      			drawCircInterm1(index,'white');
      			drawCircInterm2(index,'white');
      		}
      		
	
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


	function click(ev){
		
		
		
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
		
		
	  	for	(index = x.length-1; index >=0; index--) {

			if(  (  ((click[0]-x[index])*(click[0]-x[index]))+ ((click[1]-y[index])*(click[1]-y[index]))  )  <  ((radius*5)*(radius*5))  ){
	
				if(index==4){//pause		
					pause();
				
				}else{	
					if (myVideo.paused)
						break;
						
					changeAV(  (((x[index]-margin)/(canvas.height-(2*margin)))*2)-1    ,   1-(  (   (y[index]-margin)/(canvas.width-(2*margin)))*2)  );
				
			
					drawCirc(index,'green');
				
				
				
				
					setTimeout( function(){drawCircInterm1(index,'blue');},150  );
					setTimeout( function(){drawCircInterm2(index,'blue');},300  );
					setTimeout( function(){drawCirc(index,'blue');},450  );
					setTimeout( function(){drawCircInterm1(index,'white');}, 450  );
					setTimeout( function(){drawCircInterm2(index,'white');}, 450  );					
			
				}
						
			break;
			}
		
	  	}
		
	}
	
	
		function drawCircInterm1(index,color){
		//var index = 1;
		context.beginPath();
      		context.arc(intermediate1x[index], intermediate1y[index], radius*2, 0, 2 * Math.PI, false);
      		context.fillStyle = color;
      		context.fill();
     		context.lineWidth = 3;
     		if (color=='white')
	      		context.strokeStyle = 'white';
    
     		else
	      		context.strokeStyle = '#003300';
      		context.stroke();
	 		context.closePath();		
		
	}
			function drawCircInterm2(index,color){
		//var index = 1;
		context.beginPath();
      		context.arc(intermediate2x[index], intermediate2y[index], radius*3, 0, 2 * Math.PI, false);
      		context.fillStyle = color;
      		context.fill();
     		context.lineWidth = 3;
     		if (color=='white')
	      		context.strokeStyle = 'white';
    
     		else
	      		context.strokeStyle = '#003300';      		context.stroke();
	 		context.closePath();		
		
	}
	
	
	

	function drawCirc(index,color){
		//var index = 1;
		context.beginPath();
      		context.arc(x[index], y[index], radius*5, 0, 2 * Math.PI, false);
      		context.fillStyle = color;
      		context.fill();
     		context.lineWidth = 3;
      		context.strokeStyle = '#003300';
      		context.stroke();
	 		context.closePath();		
		
	}







	function pause(){
	
		if(loading) return;
	
		if (myVideo.paused){
	    	    myVideo.play(); 
	    	    
	    	    drawCirc(4,'red');

	    	    
		}else{		
				pauseRoutine();
				drawCirc(4,'green');

		}
	}
	
	
	function pauseRoutine(){
				  myVideo.pause(); 
		   		
		   		arousal.push(0);
	  			valence.push(0);
	  			anotime.push(0+myVideo.currentTime);
	  		
			  	insertRoutine();
				if(!flag)			
					$.post('updatePercent.php',{percent:(myVideo.currentTime/myVideo.duration), completedVideos:<?=$_POST['completedVideos']?>,userID:<?=$_POST['userID']?>});
		   		//,function( data ) { alert( "Data Updated: " + data );}
		
		
		}

		
	function changeAV(val,ar) {
		

		if (myVideo.paused){
		
		
		}else{	
			  	  		
	  		arousal.push(ar);
	  		valence.push(val);
	  		anotime.push(myVideo.currentTime);
	  		
	  		
	  		if(anotime.length>19){
		  		insertRoutine();
				
	  		}
	  			  			  		
	  	}
	}

 init();
 redraw();


</script> 

</body>
</html>
