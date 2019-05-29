<!DOCTYPE html>
<html>
<head>
	<link href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
	<link href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet" integrity="sha384-wvfXpqpZZVQGK6TAh5PVlGOfQNHSoD2xbE+QkPxCAFlNEevoEH3Sl0sibVcOQVnN" crossorigin="anonymous">	
	<title></title>
</head>
<body>
<div class="row">
    <div class="col-md-12 col-sm-12 col-xs-12">
        <div class="x_panel">
            <div class="x_title">
              <h2>Import<small></small></h2>
            </div>


			<form enctype="multipart/form-data" id='importForm' >
				<input type="hidden" name="_token" id="_token" value="{{csrf_token()}}">
				
				<div class="form-group">
					<div class="col-md-6 col-md-offset-3">
						<label for="file">Enter table name </label>
						<input type="text"  id="tableName" name="table_name" >
					</div>
					<div class="row">
		                <div class="col-md-8 col-md-offset-3">
		                 	<div class="id-file-error-div table_name commonError error"></div>
		                 </div>
		            </div>

				</div>
				<!-- <div>
					<label for="file">Enter columns name </label>
					<input type="text"  id="column-name" name="column-name" >
				</div> -->

				<div class="form-group">
					<div class="col-md-6 col-md-offset-3">
						<label for="file">Pick file</label>
						<input type="file"  id="file" name="file" accept=".csv">
					</div>
				</div>
				
				<div class="form-group">
					<div class="col-md-6 col-md-offset-3">
						<button type="submit" class="btn btn-primary" id="submit" data-loading-text="<i class='fa fa-circle-o-notch'></i> Loading...">Submit
						</button>
					</div>
				</div>
				<div class="form-group">
					<div class="col-md-6 col-md-offset-3">
						<div class="alert alert-success" id="successMessage" role="alert" style="display: none"></div>
						<div class="alert alert-danger" id="errorMessage" role="alert" style="display: none"></div>
					</div>
				</div>
			</form>
		</div>
	</div>
</div>
</body>
</html>


<script src="https://code.jquery.com/jquery-3.3.1.js" integrity="sha256-2Kok7MbOyxpgUVvAk/HJ2jigOSYS2auK4Pfzbm7uH60="
  crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous">	
</script>


<script type="text/javascript">
$(document).ready(function() {

	$('#importForm').submit(function(e){
		e.preventDefault();
		$('#submit').button('loading');
		
		var file_data = $('#file').prop('files')[0];
		var files = e.target.files;
		var form_data = new FormData();
		form_data.append('file', file_data);
		form_data.append('_token', $('input[name="_token"]').val());
		form_data.append('table_name', $('input[name="table_name"]').val());
		form_data.append('column_name', $('input[name="column-name"]').val());

		$.ajax({
			type: 'POST',
			url: '/import',
			processData: false,
			contentType: false,
			data:form_data,
			success: function(data){
				var dt = $.parseJSON(JSON.stringify(data));
			    $('#submit').button('reset');
			    if(data.result == 'success'){
			    	$('#errorMessage').hide();
			    	$('#successMessage').html('Data imported successfully.').show();    	
			    }
			    else if(data.result == 'failure'){
			    	$('#successMessage').hide();
			    	$('#errorMessage').html(data.messages).show();
			    }else if(data.result == 'validation-error'){
			            let x = data.messages;
			            $('.commonError').empty();
			            for (key in x) {
			                $('.'+key).text(x[key]);
			            }
			        } 
			},
			error: function(data){
				$('#submit').button('reset');
			}
		});
	});
});
</script>
