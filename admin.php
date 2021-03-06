<?php
function require_auth() {
  $AUTH_USER = 'admin';
  $AUTH_PASS = 'ils';
  header('Cache-Control: no-cache, must-revalidate, max-age=0');
  $has_supplied_credentials = !(empty($_SERVER['PHP_AUTH_USER']) && empty($_SERVER['PHP_AUTH_PW']));
  $is_not_authenticated = (
    !$has_supplied_credentials ||
    $_SERVER['PHP_AUTH_USER'] != $AUTH_USER ||
    $_SERVER['PHP_AUTH_PW']   != $AUTH_PASS
  );
  if ($is_not_authenticated) {
    header('HTTP/1.1 401 Authorization Required');
    header('WWW-Authenticate: Basic realm="Access denied"');
    exit;
  }
}
require_auth();
?>
<!DOCTYPE html>
<html>
<head>
<title></title>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.2.1/css/bootstrap.min.css">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.6/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.2.1/js/bootstrap.min.js"></script>
<script>
	function fillTable() {
		$.getJSON("api?action=get&id=all", function(data){
	        //console.log("Data: " + data + "\nStatus: " + status);
	        $.each( data, function( key, val ) {
	        	var text = val.ann_text.replace(/\n/g, "<br />");
       	     	 var row = "<tr class='tableTr'><td>" + val.id + "</td><td>" + 
	        	 			   "<button class='btn btn-info editbtn' id=" + val.id + " class='edit'>Edit</button></td><td nowrap>" +
	        	 			   //"<button class='editbtn' id='" + val.id + "' class='edit'>Edit</button></td><td>" +
	        	 			   val.ann_date + "</td><td>" + 
	        	               text + "</td><td>"+
	        	               (val.deleted == 1 ? "<span class='text-warning'>Yes</span>" : "No") + "</td></tr>";
	        	 //$("table tbody").append(row);
	        	  $("#tbodyId").append(row);

		   		 //console.log( "k='" + key + "', val=" + val.ann_text  );
		 	 });
         });
	}

	function clearForm(){
		$("#id").val(data.id);
	    $("#ann_date").val(data.ann_date);
		$("#ann_text").val(data.ann_text);
		$("#deleted").prop('checked', data.deleted == 1)
	}

	$(document).ready(function() {
		fillTable();
				
		$(document).on("click",".editbtn", function(event) { // any button
		  //console.log("editbtn clicked");
		  console.log(event.target.id);
		  $("#formArea").show(); 
		  $.getJSON("api?action=get&id="+event.target.id, function(data){
		  	  console.log( "Data Loaded: " + JSON.stringify(data) );
			  $("#id").val(data.id);
			  $("#ann_date").val(data.ann_date);
			  $("#ann_text").val(data.ann_text);
			  $("#deleted").prop('checked', data.deleted == 1) 
		  });
		});

		$("#clearTable").click(function(){
			$(".tableTr").remove(); 
		});

		$("#addNew").click(function(){
			$("#formArea").show(); 
		});

		$("form").submit(function(e){
			 e.preventDefault();
			 //console.log("submit form");
		//$( "#submit" ).click(function() {
		  var idVal = $("#id").val();
		  var dateVal = $("#ann_date").val();
		  var textVal = $("#ann_text").val();
		  var deletedVal = $("#deleted").prop('checked') ? 1 : 0;
		  var actionVal = idVal == "" ? 'add' : 'update';
		  var data = {
		  	id : idVal,
		  	ann_date : dateVal,
		  	ann_text : textVal,
		  	deleted : deletedVal,
		  	action : actionVal
		  };
		  $.ajax({
			  type: "POST",
			  url:"api/index.php",
			  data: data,
			  success: function( result ) {
					    console.log( "Data Loaded: " + JSON.stringify(result) );
					    var msg = "";
					    if(result == true) {
					    	msg = "<div class='alert alert-success'>Success: announcement is " 
					    	      + (actionVal=='add' ? "added" : "updated") + "</div>";
					    } else {
					      $("#msg").html();	
					      msg = "<div class='alert alert-danger'>Error: " + JSON.stringify(result) + "</div>";
					    }
					    $("#msg").replaceWith(msg);
					    
					    $("#tbodyId").empty();
					    fillTable();
					    $("#form")[0].reset();
					    $("#formArea").hide();
					  },
			  dataType: "json"
			});
		});
   });


	
</script>
</head>
<body>
<div class="container">



<div id='formArea' style="display: none">
	<h2>Announcement</h2>
	
	<form action='' method='post' id="form">
		<div class="form-group">
			<label for="id">N:</label>
			<input readonly class="form-control" type="text" name="id" id="id">
		</div>
		<div class="form-group">
			<label for="ann_date">Date:</label>
			<input required class="form-control" placeholder="Enter date" type="date" name="ann_date" id="ann_date"/>
		</div>
		<div class="form-group">
			<label for="ann_text">Text:</label>
			<textarea required rows="10" class="form-control" placeholder="Enter text" name="ann_text" id="ann_text"/></textarea>
		</div>
		<div class="form-group form-check">
			<label class="form-check-label">
				<input class="form-check-input" type="checkbox" name="deleted" id="deleted"/>Deleted
			</label>
		</div>
		<button id="submit" type="submit" class="btn btn-primary">Submit</button>

	</form>


</div>
<div id="msg"></div>
<p/>
	<!--<div><button id="clearTable">Clear Table</button> -->
		<h2>List of announcements <button type="button" class="btn btn-primary" id="addNew">New</button></h2>
		<table class='table' id='table'>
			<thead>			
				<tr>
					<th>N</th>
					<th>Action</th>
					<th>Date</th>
					<th>Text</th>
					<th>Is deleted</th>
				</tr>
			</thead>
			<tbody id="tbodyId">
			</tbody>
		</table>
	</div>
</div>

</body>
</html>