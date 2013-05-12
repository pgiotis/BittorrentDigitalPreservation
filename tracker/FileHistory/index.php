<!--
To change this template, choose Tools | Templates
and open the template in the editor.
-->
<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <script src="js/bootstrap.min.js"></script>
        <script type="text/javascript" src="js/jquery-1.9.1.min.js" ></script>
        <link href="css/bootstrap.min.css" rel="stylesheet" media="screen">

        <title>Log files presenter</title>
    </head>
    <body>
        <h3 align="center" class="muted">Welcome to the statistics page. <br>
            Write the hashkey from the file that you want to see.</h3>

        <br></br>
        <p align="center">
            <label id="temp" style="display:none;" >Label name</label>
            <input type="text"  id="haskey" placeholder="Type HashKey here…" onkeyup="gethaskey()">
            <a href="logPresenter.php?id=" id="hrefKey" class="btn btn-primary">Submit</a>
        </p>



        <script>
            function gethaskey(){
                
               document.getElementById("temp").innerHTML=document.getElementById("haskey").value;
               
               document.getElementById("hrefKey").href="logPresenter.php?id="+document.getElementById("temp").innerHTML;
               
            }
            
        </script>

    </body>
</html>
