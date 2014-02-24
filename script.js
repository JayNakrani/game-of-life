var maxIteration=3;		// positive integer value which decides the number of iteration
var timeDelay=300;		// positive integer value (in millisecond) which decides delay between each iteration
var intervalRef=null;	// reference variable for interval functions
var generationNumber=0;	// number of current generation from root/first generation
neighbour=new Array();	// global array of neighbours
cellActionCode=[];		// 2D-Array to store cell action
for(r=0;r<=MAX_ROW;r++)	// initilizing the 2D-Array
{
	cellActionCode[r]=[];
	for(c=0;c<=MAX_COL;c++)
	{ 
		cellActionCode[r][c]=0;    
	}    
}
 
//to set the cell to black(alive) or white(dead)
function setCell(cell)
{
	if(cell.getAttribute("class")=="black")
	{
		//make it white
		cell.setAttribute("class","white");
		cell.setAttribute("alive","0");
	}
	else
	{
		//make it black
		cell.setAttribute("class","black");
		cell.setAttribute("alive","1");
	}
}

//returns number of alive neighbours : ip => Array of neighbours cell objects
function numberOfAliveNeighbours(neighbour)
{
	cnt=0;
	for(i=1;i<=8;i++)
	{
		if(parseInt(neighbour[i].attributes.alive.value)==1)
		{
			cnt++;
		}
	}
	return(cnt);
}

//start the game or run it
function runGame()
{
	intervalRef=setInterval("nextState();",timeDelay);
}

//to transit to next state
function nextState()
{
	liveFlag=false;	//flag indicating 'liveliness' of system
		
	//scan through each and every cell
	for(r=1;r<=MAX_ROW;r++)
	{
		cellActionCode[r][0]=0;
		for(c=1;c<=MAX_COL;c++)
		{
			liveNeighbours=0;
		//find the neighbourhood
			//self cell	=> neighbour[0]
				id=r+'_'+c;
				neighbour[0]=document.getElementById(id);
			//previous Left Cell => neighbour[1]
				id=(r-1)+'_'+(c-1);
				neighbour[1]=document.getElementById(id);
			//previous top cell => neighbour[2]
				id=(r-1)+'_'+c;
				neighbour[2]=document.getElementById(id);
			//previous right cell => neighbour[3]
				id=(r-1)+'_'+(c+1);
				neighbour[3]=document.getElementById(id);
			//left cell => neighbour[4]
				id=r+'_'+(c-1);
				neighbour[4]=document.getElementById(id);
			//right cell =>neighbour[5]
				id=r+'_'+(c+1);
				neighbour[5]=document.getElementById(id);
			//next left cell => neighbour[6]
				id=(r+1)+'_'+(c-1);
				neighbour[6]=document.getElementById(id);
			//next bottom cell => neighbour[7]
				id=(r+1)+'_'+(c);
				neighbour[7]=document.getElementById(id);
			//next right cell => neighbour[8]
				id=(r+1)+'_'+(c+1);
				neighbour[8]=document.getElementById(id);
			
			liveNeighbours=numberOfAliveNeighbours(neighbour);			
			if(liveNeighbours<2 || liveNeighbours>3)
			{
				//cell dies : Under population || Over population : code (-1)
				cellActionCode[r][c]=-1;
			}
			else
			{
				if(neighbour[0].attributes.alive.value=="0" && liveNeighbours==3)
				{
					//cell becomes alive : reproduction :  code(1)
					cellActionCode[r][c]=1;
				}
				else
				{
					//cell continues to live:Moderate Population : code(0)
					cellActionCode[r][c]=0;
				}
			}
			
			if(neighbour[0].attributes.alive.value=="1")
			{
				liveFlag=true;		//set the flag if there is any alive cell
			}
		}
	}
	//if system is not alive then stop execution
	if(liveFlag==false)
	{
		alert('System is dead at Generation Number:'+generationNumber+'...\nPausing the game...!!');
		pauseGame();
		return false;
	}
	generationNumber++;
	document.getElementById('logNum').innerHTML=generationNumber;
	//set the next state of each and every cell;
	for(r=1;r<=MAX_ROW;r++)
	{
		for(c=1;c<=MAX_COL;c++)
		{
			id=r+'_'+c;
			selfCell=document.getElementById(id);
			switch(cellActionCode[r][c])
			{
				case -1:
					//die
					selfCell.setAttribute("class","white");
					selfCell.setAttribute("alive","0");
					break;
				case 0:
					// no action:
					break;
				case 1:
					//reproduction
					selfCell.setAttribute("class","black");
					selfCell.setAttribute("alive","1");
					break;
				default:
					alert("Haha..something went wrong..!!");
					break;
			}
		}
	}
}

//to pause the game
function pauseGame()
{
	clearInterval(intervalRef);
}

//to clear the game
function clearAll()
{
	//reset each and every cell
	for(r=0;r<=MAX_ROW;r++)
	{
		for(c=0;c<=MAX_COL;c++)
		{
			id=r+'_'+c;
			document.getElementById(id).setAttribute("alive","0");
			document.getElementById(id).setAttribute("class","white");
		}
	}
	//reset variables and other display
	generationNumber=0;
	document.getElementById('logNum').innerHTML=generationNumber;
	clearInterval(intervalRef);
	alert('haha..Successfully cleared...!');
}

function adjustSpeed()
{
	speed=parseFloat(document.getElementById('userSpeed').value);
	if(isNaN(speed))
	{	
		alert('Non-number OR Text value given as speed..!!'); 
		return false;
	}
	timeDelay=(1000/speed);
	if(generationNumber>0)
	{
		pauseGame();
		runGame();
	}
}