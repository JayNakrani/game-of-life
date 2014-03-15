<?php
// database entry
if(!isset($_GET['test']))
{
	include_once('../ext/lib/DataAccess.php');
	$dbObject=new db_class();
	$ip=mysql_real_escape_string($_SERVER['REMOTE_ADDR']);
	$userAgent=mysql_real_escape_string($_SERVER['HTTP_USER_AGENT']);
	$referer=mysql_real_escape_string($_SERVER['HTTP_REFERER']);
	
	date_default_timezone_set('Asia/Calcutta');
	$timeStamp=date('Y-m-d H:i:s');
	$maxRow=(isset($_GET['maxRow']))?($_GET['maxRow']):(-1);
	$maxCol=(isset($_GET['maxCol']))?($_GET['maxCol']):(-1);
	
	$query="INSERT INTO `life_log` (`srNo`, `ip`, `userAgent`, `referer`, `timeStamp`, `maxRow`, `maxCol`) VALUES ('', '".$ip."', '".$userAgent."', '".$referer."','".$timeStamp."','".$maxRow."','".$maxCol."');";
	$result=$dbObject->insertQuery($query);
	if(!$result)
	{
		die("Some db error!!");
	}
}


//for game of life page

define("BOARD_SIZE","500");
$alert=false;

if(isset($_GET['maxRow'],$_GET['maxCol']))
{
	if($_GET['maxRow']>150 || $_GET['maxRow']<2)
	{
		$_GET['maxRow']=10;
		$alert=true;		
	}
	
	if($_GET['maxCol']>150 || $_GET['maxCol']<2)
	{
		$_GET['maxCol']=10;
		$alert=true;
	}
	$maxRow=$_GET['maxRow'];
	$maxCol=$_GET['maxCol'];
	define("MAX_ROW",$maxRow);
	define("MAX_COL",$maxCol);
}
else
{
	$maxRow=10;
	$maxCol=10;
	define("MAX_ROW","10");
	define("MAX_COL","10");
}
$imgDimension=(BOARD_SIZE/$maxCol);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Conway's game of Life</title>
<link rel="shortcut icon" href="../ext/images/favicon.png" >
<link type="text/css" rel="stylesheet" href="style.css" />
<style type="text/css">
.emptyImg
{
	width:<?php echo $imgDimension."px"; ?>;
	height:<?php echo $imgDimension."px"; ?>;
}
</style>
<script type="text/javascript">
  /*var _gaq = _gaq || [];
  _gaq.push(['_setAccount', 'UA-32880711-1']);
  _gaq.push(['_trackPageview']);

  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();*/
</script>
<script type="text/javascript">
//definig constants for JavaScript
	<?php 
		echo "MAX_ROW=".MAX_ROW.";";
		echo "MAX_COL=".MAX_COL.";";
	?>
	window.onload=function(){
		if(typeof(MAX_ROW)=="undefined" || typeof(MAX_COL)=="undefined" )
		{
			alert("JavaScrpt variables are not set..!! Sorry, but you have to refresh the page!!");
		}
		<?php /*if($alert)	{ 	echo "alert(\"You can not give out of this range[50,150]!! Automatically resized to 100 X 100.\");"; }	*/?>
	}
</script>
<script type="text/javascript" src="script.js"></script>
</head>
<body>
<div id="mainWrap">
    <a href="https://github.com/dhananjay92/game-of-life" target="_blank">
        <img style="position: absolute; top: 0; left: 0; border: 0;-webkit-transform:rotate(-90deg); -moz-transform:rotate(-90deg);" src="https://s3.amazonaws.com/github/ribbons/forkme_right_red_aa0000.png" alt="Fork me on GitHub">
    </a>
    <div id="cntrlWrap">
            <div id="formWrap">
                <form name="form" id="form" onsubmit="return false;" style="padding-bottom:7px;">
                <!--<label for="userNum">Number:</label>
                <input name="userNum" type="text" id="userNum" />-->
                </form>
                <div id="btnWrap">
                    <button onclick="runGame()">Play</button>
                    <button onclick="pauseGame()" style="margin-left: 18px;">Pause</button>
                	<button onclick="clearAll()" style="margin-left: 18px;">Refresh</button>
                    <br /><br />
                    <button onclick="nextState()" style="">Next Step</button>
					<div style="text-align: center;">
                    	<a href="http://en.wikipedia.org/wiki/Conway's_Game_of_Life" target="_blank">Help !!</a>
                    </div>
                </div>
                
            </div>
            <div id="adjustWrap">
            	<fieldset>
                        <legend>Adjust grid</legend>
                        <form action="index.php" method="get">
                            <label for="maxRow">Rows</label>
                            <input type="text" name="maxRow" id="maxRow"/>
                            <label for="maxCol">Columns</label>
                            <input type="text" name="maxCol" id="maxCol"/>
                            <input type="submit" value="Submit!!"/>
                        </form>
                </fieldset>
                <fieldset>
                        <legend>Adjust Speed</legend>
                            <label for="userSpeed">Speed(in fps)</label>
                            <input type="text" name="userSpeed" value="" id="userSpeed"/>
                            <button onclick="adjustSpeed()">Adjust!!</button>
                        </form>
                </fieldset>
            </div>
    </div>
    <div id="tableWrap">
          <table id="mainTable" cellpadding="0" cellspacing="0" border="1">
              <?php
              for($r=0;$r<=(MAX_ROW+1);$r++)
              {
                  echo "<tr row=".$r.">";
                  for($c=0;$c<=(MAX_COL+1);$c++)
                  {
					  if($r*$c!=0 && $r<=MAX_ROW && $c<=MAX_COL)
					  {
						  echo "<td row=\"".$r."\" col=\"".$c."\" id=\"".$r."_".$c."\" class=\"\" style=\"height:".$imgDimension."px; width:".$imgDimension."px;\" alive=\"0\" onclick=\"setCell(this)\"><img src=\"imgs/empty.png\" class=\"emptyImg\" title=\"row:".$r." column:".$c."\"></td>";
					  }
					  else
					  {
						  echo "<td row=\"".$r."\" col=\"".$c."\" id=\"".$r."_".$c."\" class=\"\" style=\"display:none;\" alive=\"0\"></td>";
					  }
                  }
                  echo "</tr>";
              }
              ?>
          </table>
    </div>
    <div id="log">
    	Generation Number : <span id="logNum">0</span>    
    </div>
</div>
</body>
</html>
