



<style type="text/css">
  .table-responsive-stack tr {
  display: -webkit-box;
  display: -ms-flexbox;
  display: flex;
  -webkit-box-orient: horizontal;
  -webkit-box-direction: normal;
      -ms-flex-direction: row;
          flex-direction: row;
}


.table-responsive-stack td,
.table-responsive-stack th {
   display:block;
/*      
   flex-grow | flex-shrink | flex-basis   */
   -ms-flex: 1 1 auto;
    flex: 1 1 auto;
}

.table-responsive-stack .table-responsive-stack-thead {
   font-weight: bold;
}

@media screen and (max-width: 768px) {
   .table-responsive-stack tr {
      -webkit-box-orient: vertical;
      -webkit-box-direction: normal;
          -ms-flex-direction: column;
              flex-direction: column;
      border-bottom: 3px solid #ccc;
      display:block;
      
   }
   /*  IE9 FIX   */
   .table-responsive-stack td {
      float: left\9;
      width:100%;
   }
}
div:last-child {
    display: none;
}
</style>










  <html lang="en" >
<head>
  <meta charset="UTF-8">
  <title>Label</title>
  <script src="https://bootstrapcreative.com/wp-bc/wp-content/themes/wp-bootstrap/codepen/bootstrapcreative.js?v=5"></script><link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.0.0/css/bootstrap.css'><link rel="stylesheet" href="./style.css">

</head>
<body>
<!-- partial:index.partial.html -->
<div class="container">

   <h2>Label show</h2>
   <table class="table table-bordered table-striped table-responsive-stack" id="tableOne">
      <thead class="thead-dark">
         <tr>
                       <th >Sr.No</th>
                        <th >Name</th>
                        <th >Email</th>
                        <th >Amount</th>
                        <th>InsuredValue</th>
                        <th >Label Show</th>
                        <!-- <th>Delete</th> -->
         </tr>
      </thead>
      <tbody>
                       <?php $i=1; ?>
                        @forelse(@$label as $value)    
                          <tr>
                            <td >{{$i}}</td>
                            <td >{{$value->FullName}}</td>
                            <td  >{{$value->email}}</td>
                            <td >{{$value->Amount}}</td>
                            <td>{{$value->InsuredValue}}</td>
                            <td>  
                             <a href="{{$value->url}}" target="_black" ><img src="{{$value->url}}" width="100"></a>
                            </td>
                     <!--        <td>
                                <button value="DELETE" style="cursor: pointer;" onclick="del('{{$value->StampsTxID}}')">DELETE</button>
                            </td> -->
                          </tr>
                  <?php $i++; ?>
                        @empty
                        @endforelse
 
      </tbody>
   </table>
   
   
</div>
<!-- /.container -->
<!-- partial -->
  <script src='https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js'></script><script  src="./script.js"></script>


<script type="text/javascript">
  $(document).ready(function() {

   
   // inspired by http://jsfiddle.net/arunpjohny/564Lxosz/1/
   $('.table-responsive-stack').each(function (i) {
      var id = $(this).attr('id');
      //alert(id);
      $(this).find("th").each(function(i) {
         $('#'+id + ' td:nth-child(' + (i + 1) + ')').prepend('<span class="table-responsive-stack-thead">'+             $(this).text() + ':</span> ');
         $('.table-responsive-stack-thead').hide();
         
      });
      

      
   });

   
   
   
   
$( '.table-responsive-stack' ).each(function() {
  var thCount = $(this).find("th").length; 
   var rowGrow = 100 / thCount + '%';
   //console.log(rowGrow);
   $(this).find("th, td").css('flex-basis', rowGrow);   
});
   
   
   
   
function flexTable(){
   if ($(window).width() < 768) {
      
   $(".table-responsive-stack").each(function (i) {
      $(this).find(".table-responsive-stack-thead").show();
      $(this).find('thead').hide();
   });
      
    
   // window is less than 768px   
   } else {
      
      
   $(".table-responsive-stack").each(function (i) {
      $(this).find(".table-responsive-stack-thead").hide();
      $(this).find('thead').show();
   });
      
      

   }
// flextable   
}      
 
flexTable();
   
window.onresize = function(event) {
    flexTable();
};
   
   
   
   

  
// document ready  
});
</script>


<script type="text/javascript">
  function del(id)
  {
 
 // alert(id);

             var link="{{route('deletelabel')}}";
                                        var token="{{ csrf_token() }}";
                                          var data={'id':id,"_token":token};
                                          $.ajax({
                                            type:'POST',
                                            url:link,
                                            data :data,
                                            success:function(response){

                                                if (response == "Yes") 
                                                {
                                                  location.reload();
                                                }
                                                else
                                                {
                                                  alert("something wrong , please try again")
                                                }

                                            }

                                          });
  }
</script>
</body>
</html>





