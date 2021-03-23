<?php
//ini_set('max_execution_time', 0); 
use \App\Http\Controllers\GetController;
?>

<html>
	<head>
	<meta name="csrf-token" content="{{ csrf_token() }}">
	<!-- <link rel="stylesheet" type="text/css" href="//cdn.datatables.net/1.10.23/css/jquery.dataTables.min.css"> -->
	<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.5.2/css/bootstrap.css">
	<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.23/css/dataTables.bootstrap4.min.css">
	<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/responsive/2.2.7/css/responsive.dataTables.min.css">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
	<script src="//cdn.datatables.net/1.10.23/js/jquery.dataTables.min.js"></script>
	<script src="https://cdn.datatables.net/1.10.23/js/dataTables.bootstrap4.min.js"></script>
	<script src="https://cdn.datatables.net/responsive/2.2.7/js/dataTables.responsive.min.js"></script>
	<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
	<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
	<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
	<style>
		div.dataTables_wrapper div.dataTables_length select {
			width:60px;
		}
		.dataTables_wrapper .dataTables_paginate .paginate_button{
			padding:0px;
		}
		.page-link {
			color: rgba(51,51,51,0.8);
		}
		.page-item.active .page-link {
			color: #fff;
			background-color: hsl(0,55%,55%);
			border-color: hsl(0,55%,55%);
		}
		.dataTables_wrapper .dataTables_paginate .paginate_button:hover {
			border: none !important;
			background-color: transparent;
			background:none;
		}
		.pi-title {
			margin-bottom: 30px;
			position:relative;
		}
		.pi-title > h1 {
			font-size: 30px;
			padding-bottom: 15px;
			position: relative;
		}
		.pi-title > h1::after {
			content: "";
			position: absolute;
			width: 70px;
			height: 2px;
			background-color: hsl(0,55%,55%);
			left: 0;
			bottom: 0;
		}
		.pl-table a{
			color: rgba(51,51,51,0.8);
			text-decoration:none;
		}
		.pl-table a:hover,
		.pl-table a:focus{
			color: hsl(0,55%,55%);
		}
		.alert-msg{
			margin-bottom:20px;
		}
		.success-msg{
			background-color:#528c54;
			padding:10px 20px;
			color:#fff !important;
			text-align:center !important;
		}
		.success-msg > *{
			margin-bottom:0px !important;
			color:#fff !important;
		}
		.applyBtn {
			display: none;
		}
	</style>
</head>
<body>
	<div class="container-fluid" style="padding-top: 20px;">
		<h5>Filter By :</h5> 
		<form action="{{ Url('/Filter') }}" method="POST">
			@csrf
			<div class="row" style="padding-left: 10px;">
				<label>Customer created date:</label>
				<div class="col-md-2">
					<input type="text" class="form-control" name="daterange" id="daterange" />
				</div>
				<div class="col-md-2">
					<input class="btn btn-info" type="submit" value="Submit" >
				</div>
			</div>
		</form>
		<input type="hidden" id="date" value="<?php echo $date; ?>">
		<!-- <section id="page-title" class="alert-msg">
	        <div class="row">
	        	<?php if(Session::has('msg')){ ?>
		            <div class="col-sm-12" id="msg">
		                <div class="success-msg"><?= Session::get('msg'); ?></div>
		            </div>
		        <?php } ?>
	        </div>
	    </section> -->
	</div>
	<div  style="padding-left: 10px;padding-right: 40px;">
		<?php if(Session::has('msg')){ ?>
			<div class="alert-msg" id="msg">
				<div class="success-msg"><?= Session::get('msg'); ?></div>
			</div>
		<?php } ?>
		<div class="form-group">
		<a href="{{ url('/') }}/export/xlsx" class="btn btn-success">Export Data</a>
		
		</div>
		<div class="pi-title">
			<h1>Customer Report</h1>
		</div>
		<table id="example" class="table table-striped table-bordered" >
			<thead>
	            <tr>
	                <th>Customer Name</th>
	                <th>Address</th>
	                <th>Phone</th>
	                <th>Email</th>
	                <th>Customer created<br/>Date</th>
	                <th>Avg Order<br/>Amount</th>
	                <th>Total Orders</th>
	                 <th>Last Order<br/> Date</th>  
					
	            </tr>
	        </thead>
	        <tbody>
	        	<?php for($i=0;$i<count($customerData);$i++){ 

	        		
	        	?>
		            <tr>
						<td><?php if(!empty($customerData[$i]->first_name || $customerData[$i]->last_name)) { echo $customerData[$i]->first_name.' '.$customerData[$i]->last_name; } else { echo '-'; } ?></td>
						<td><?php if(!empty($customerData[$i]->address)) { echo $customerData[$i]->address; } else { echo '-'; } ?></td>
						<td><?php if(!empty($customerData[$i]->mobileno)) { echo $customerData[$i]->mobileno; } else { echo '-'; } ?></td>
						<td><?php if(!empty($customerData[$i]->email)) { echo $customerData[$i]->email; } else { echo '-'; } ?></td>
						<td><?php if(!empty($customerData[$i]->customer_created_at)) { echo date("m/d/Y", strtotime($customerData[$i]->customer_created_at)); } else { echo '-'; } ?></td>
						<td><?php if(!empty($customerData[$i]->average_amount)) { echo '$'.$customerData[$i]->average_amount; } else { echo '$0'; } ?></td>
						<td><?php if(!empty($customerData[$i]->total_order)) { echo $customerData[$i]->total_order; } else { echo '-'; } ?></td>
						<td><?php if(!empty($customerData[$i]->last_order_date)) { echo date("m/d/Y", strtotime($customerData[$i]->last_order_date)); } else { echo'-'; } ?></td>
						
						
					</tr>
				<?php } ?>
			</tbody>
		</table>
	</div>
</body>
</html>

<script type="text/javascript">
	$(document).ready(function() {
	    $('#example').DataTable({
	    	"aoColumnDefs": [
	    	{ "sWidth": "10%", "aTargets": [ 0 ] },
		      { "sWidth": "20%", "aTargets": [ 1 ] },
		      { "sWidth": "10%", "aTargets": [ 2 ] },
		      { "sWidth": "10%", "aTargets": [ 3 ] },
		      { "sWidth": "10%", "aTargets": [ 4 ] },
		      { "sWidth": "10%", "aTargets": [ 5 ] },
		      { "sWidth": "10%", "aTargets": [ 6 ] },
		      { "sWidth": "20%", "aTargets": [ 7 ] },

		    ],
			"pageLength": 50,
			bAutoWidth: false,
			responsive: true
		});
	} );
</script>
<script>
$(function() {
	//alert($('#date').val());
	$('#daterange').val($('#date').val());
  $('input[name="daterange"]').daterangepicker({
    opens: 'left',
     "locale": {
        format: 'MM/DD/YYYY'
    }
  });
});

</script>
