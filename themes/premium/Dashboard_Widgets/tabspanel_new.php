<?php
class tabs_new
{
    public function Alltabs_new()
    {  
$path_to_root = ".";
      echo"<section>";
      echo"<div class='col-xs-12'>";  
      echo "<div class=''>";
  
        echo'   <style>

#tabs_u {
overflow: hidden;
width: 100%;
margin: 0;
padding: 0;
list-style: none;
}
#tabs_u li {
float: left;
margin: 0 -15px 0 0;
}
#tabs_u a {
float: left;
position: relative;
padding: 0 40px;
height: 0;
line-height: 30px;
text-transform: uppercase;
text-decoration: none;
color: #fff;
border-right: 30px solid transparent;
border-bottom: 30px solid #3D3D3D;
border-bottom-color: #777\9;
opacity: .3;
filter: alpha(opacity=30);
}
#tabs_u a:hover,  #tabs_u a:focus {
border-bottom-color: #2ac7e1;
opacity: 1;
filter: alpha(opacity=100);
}

#tabs_u a:hover{
    border-bottom-color: #2ac7e1;
opacity: 1;
filter: alpha(opacity=100);
}
#tabs_u a:focus {
outline: 0;
}
#tabs_u #current {
z-index: 3;
border-bottom-color: #3d3d3d;
opacity: 1;
filter: alpha(opacity=100);
}
/* ----------- */
#content1 {
background: #fff;
border-top: 2px solid #3d3d3d;
padding: 2em;/*height: 220px;*/
}
#content1 h2,  #content1 h3,  #content1 p {
margin: 0 0 15px 0;
}

</style>';
//<li><a href="#" name="#tab6">HR</a></li>
//<li><a href="#" name="#tab7">Payroll</a></li>
echo'<div class="col-md-12 col-xs-12 col-sm-12">';
echo'<ul id="tabs_u" >
<li><a href="#" name="#tab_new">Cost Centers</a></li>


</ul>
<div id="content1" >


<div id="tab_new" style="backgroud-color:green">';


include_once("$path_to_root/themes/".user_theme(). "/Dashboard_Widgets/ChartsDonuts_new.php");                    
                            $_bank = new AllDonutCharts_new();
                            $_bank->customers_new();
echo'</div>



</div>
<script>
 function resetTabs1(){
        $("#content1 > div").hide(); //Hide all content
        $("#tabs_u a ").attr("id",""); //Reset ids      
    }

    var myUrl = window.location.href; //get URL
    var myUrlTab = myUrl.substring(myUrl.indexOf("#")); // For localhost/tabs.html#tab_new, myUrlTab = #tab_new    
    var myUrlTabName = myUrlTab.substring(0,4); // For the above example, myUrlTabName = #tab

    (function(){
        $("#content1 > div").hide(); // Initially hide all content
        $("#tabs_u li:first a").attr("id","current"); // Activate first tab
        $("#content1 > div:first").fadeIn(); // Show first tab content
        $("#tabs_u a").on("click",function(e) {
            e.preventDefault();
            if ($(this).attr("id") == "current"){ //detection for current tab
             return       
            }
            else{             
            resetTabs1();
            $(this).attr("id","current");';// Activate this
 echo"   $($(this).attr('name')).fadeIn(); // Show content for current tab
            }";
 echo'      });

 for (i = 1; i <= $("#tabs_u li").length; i++) {
          if (myUrlTab == myUrlTabName + i) {
              resetTabs1();
        $("a[name="+myUrlTab+"]").attr("id","current"); // Activate url tab
              $(myUrlTab).fadeIn(); // Show url tab content        
          }
        }
    })()
  </script> ';

      echo "</div>";
      echo "</div>";
      echo"</section>";
    }  
   
}

?>