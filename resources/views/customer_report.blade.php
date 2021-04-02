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
	<link rel="stylesheet" type="text/css" href="{{ asset('css/style.css') }}">

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
	<input type="hidden" id="sdate" value="<?php echo $startDate; ?>">
    <input type="hidden" id="edate" value="<?php echo $endDate; ?>">
	<input type="hidden" value="{{ Url('/getTotalTags') }}" id="getTotal">
	<input type="hidden" value="{{ Url('/getMapData') }}" id="getMapData">
	<div class="main-wrapper-ljr">
		<?php if(Session::has('msg')){ ?>
			<div class="alert-msg" id="msg">
				<div class="success-msg"><?= Session::get('msg'); ?></div>
			</div>
		<?php } ?>
		
		<?php
         	$tab1 = 'active'; 
         	$tab2 = ''; 
         	if($tab == 2){
         		$tab1 = '';
         		$tab2 = 'active';
         	} 
        ?>
		
		<div class="row">
			<div class="col-md-12">
				<!-- tabs -->
				<ul class="nav nav-tabs" id="myTab" role="tablist">
					<li class="nav-item ">
						<a class="nav-link <?php echo $tab1; ?>" id="analytics-tab" data-toggle="tab" href="javascript:void(0);" role="tab" aria-controls="analytics" aria-selected="true" onclick="showContent(1);"><span>Dashboard</span></a>
					</li>
					<li class="nav-item">
						<a class="nav-link <?php echo $tab2; ?>" id="responses-tab" data-toggle="tab" href="javascript:void(0);" role="tab" aria-controls="responses" aria-selected="false" onclick="showContent(2);"><span>Customer Report</span></a>
					</li>
				</ul>
				<!-- End of tabs -->
				<!-- Tab Content -->
				<div class="tab-content" id="myTabContent"> 	
					<div class="tab-pane fade show <?php echo $tab1; ?>" id="analytics" role="tabpanel" aria-labelledby="analytics-tab">
						<div class="pt-content">
							<div class="filter-date">
								<h5>Filter By Date:</h5>
								<form action="{{ Url('/Filter') }}" method="POST">
									@csrf
									<div class="flex-search">
										<div class="search-control">
											<input type="text" class="form-control" name="daterange" id="daterange1" onchange="myFunction()"/>
											<input type="hidden" name="tab" value="1">
										</div>
										<div class="button-control">
											<input class="btn btn-info" type="submit" value="Submit">
										</div>
									</div>
								</form>
							</div>
							
							<div class="filter-tags">
								<h5>Filter By Tags:</h5>
			 					<div class="tags-fill"> 
								 	<?php //echo "<PRE>";print_r($allCustomerTags);die; 
										$alldata = array();
										for($i=0;$i<count($allCustomerTags);$i++){
											$fruits_ar = array();
											$fruits_ar = explode(',',$allCustomerTags[$i]->customer_tag);
											for($j=0;$j<count($fruits_ar);$j++){
												if(!empty($fruits_ar[$j])){
													array_push($alldata,trim($fruits_ar[$j]));
												}
											}
										}
										$alldata = array_unique($alldata);
										$allFTags = array_values($alldata);
										//echo "<PRE>";print_r(array_values($alldata));die;
									?>
									<?php $i=1; for($p=0;$p<count($allFTags);$p++){ ?>
										<button type="button" class="btn btn-light" onclick="getTotal('<?php echo $allFTags[$p]; ?>');">{{ $allFTags[$p] }}</button> 
									<?php }  ?> 
								</div>
							</div>
							<div class="button-control">
								<input class="btn btn-info" type="button" value="Reset" onclick="resetData();">
							</div>
							<div class="tag-box">
								<div id="addTags" class="row">
									<div class="col-xl-3 col-lg-4 col-md-6 col-tagbox">
										<div class="tags-view">
											<h3>Total Customer</h3>
											<div class="tag-preiod">
												<span class="old-period"> <b>{{ count($allCustomerOld) }}</b> <small>previous period</small> </span>
												<span class="cc-period"> <b>{{ count($allCustomer) }}</b> <small>Current period</small> </span>
											</div>
										</div>
									</div>
									<div class="col-xl-3 col-lg-4 col-md-6 col-tagbox">
										<div class="tags-view">
											<h3>Total DTC</h3>
											<div class="tag-preiod">
												<span class="old-period"> <b>{{ count($allDTCOld) }}</b> <small>previous period</small> </span>
												<span class="cc-period"> <b>{{ count($allDTC) }}</b> <small>Current period</small> </span>
											</div>
										</div>
									</div>
									<div class="col-xl-3 col-lg-4 col-md-6 col-tagbox">
										<div class="tags-view">
											<h3>Total Wholesale</h3>
											<div class="tag-preiod">
												<span class="old-period"> <b>{{ count($allWholesaleOld) }}</b> <small>previous period</small> </span>
												<span class="cc-period"> <b>{{ count($allWholesale) }}</b> <small>Current period</small> </span>
											</div>
										</div>
									</div>
								</div>
							</div>
							<div class="tag-chart">
								<div id="tagsChart" class="row">
									<div class="col-xl-6 col-chart">
										<figure class="highcharts-figure">
											<div id="TCustomer-chart"></div>
										</figure>
									</div>
									<div class="col-xl-6 col-chart">
										<figure class="highcharts-figure">
											<div id="TDTC-chart"></div>
										</figure>
									</div>
									<div class="col-xl-6 col-chart">
										<figure class="highcharts-figure">
											<div id="TWholesale-chart"></div>
										</figure>
									</div>
								</div>
							</div>
						</div>
					</div>
					<!-- tab content one -->
					<div class="tab-pane fade show <?php echo $tab2; ?>" id="responses" role="tabpanel" aria-labelledby="responses-tab">
						<div class="pt-content ">
							<div class="flex-datae">
								<div class="filter-date cr-left">
									<h5>Filter By Date</h5> 
									<form action="{{ Url('/Filter') }}" method="POST">
										@csrf
										<div class="flex-search">
											<div class="search-control">
												<input type="text" class="form-control" name="daterange" id="daterange" />
												<input type="hidden" name="tab" value="2">
											</div>
											<div class="button-control">
												<input class="btn btn-info" type="submit" value="Submit" >
											</div>
										</div>
									</form>
								</div>
								<div class="export-data">
									<a href="{{ url('/') }}/export/xlsx" class="btn btn-success">Export Data</a>
								</div>
							</div>
							<div class="table-re-box">
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
						</div>
					</div> 
					<!-- tab content two -->
				</div>
				<!-- End of Tab Content -->
			</div>
		</div>
		
		
	</div>
</body>
</html>
<script src="https://code.highcharts.com/highcharts.js"></script>
<script src="https://code.highcharts.com/modules/series-label.js"></script>
<script src="https://code.highcharts.com/modules/exporting.js"></script>
<script src="https://code.highcharts.com/modules/export-data.js"></script>
<script src="https://code.highcharts.com/modules/accessibility.js"></script>
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

$(function() {
	//$('#daterange').val($('#date').val());
	  var dS = $('#sdate').val();
	  var dE = $('#edate').val();
	  $('#daterange').daterangepicker({
	   locale: {
	        format: 'MM/DD/YYYY'
	    },
	    opens: 'left',
	    startDate: dS,
    	endDate: dE,
    	maxDate: new Date()
	  });

	 var d1 = $('#sdate').val();
	    $('#daterange1').daterangepicker({
	    locale: {
	      format: 'MM/DD/YYYY'
	    },
	    opens: 'left',
	    startDate: dS,
    	endDate: dE,
    	maxDate: new Date()
	  }, function(start, end, label) {
	  });   
});

function showContent(type){
    if(type == 1){
        $('#responses').css('display','none');
        $('#analytics').css('display','block');
        $('#analytics').css('display','block');
        $('#responses-tab').removeClass('active');
        $('#analytics-tab').addClass('active');   
    }else if(type == 2){
        $('#analytics').css('display','none');
        $('#responses').css('display','block');
        $('#analytics-tab').removeClass('active');
        $('#responses-tab').addClass('active');
    }
}
function resetData() {
	$('.append').remove();
}
tagArr = [];
function getTotal(val){
	var url = $('#getTotal').val();
	var startDate = $('#sdate').val();
	var endDate = $('#edate').val();
	
	if(val !== 'Wholesale' && val !== 'DTC'){
		if ($.inArray(val, tagArr) != -1){
		}else{
			tagArr.push(val);
			$.ajax({
		        url: url,
		        type: "POST",
		        dataType: "JSON",
		        data: {tags:val,startDate:startDate,endDate:endDate,"_token": "{{ csrf_token() }}"},
		        success: function (data) {
		        	$('#addTags').append('<div class="col-xl-3 col-lg-4 col-md-6 col-tagbox append"><div class="tags-view"><h3>Total '+val+'</h3> <div class="tag-preiod"> <span class="old-period"><b>'+data.totalOld+'</b> <small>previous period</small></span> <span class="cc-period"><b>'+data.total+'</b> <small>Current period</small></span></div></div></div>');
		        	$('#tagsChart').append('<div class="col-xl-6 col-chart append"><figure class="highcharts-figure"><div id="'+data.id+'"></div></figure></div>');
		        	//alert(seriesDataCus);	
		        	startsChart1(data.seriesDataCus,data.tStarts,data.id,val);
		        }
		    });
		}
	}
}



function myFunction(){
    var url1 = $('#getMapData').val();
    var sdate = $('#sdate').val();
    var edate = $('#edate').val();
    var date = $('#daterange1').val();
     $.ajax({
        url: url1,
        type: "POST",
        dataType: "Json",
        data: { 
         _token: "{{ csrf_token() }}",
         date:date
       },
       cache: false,
       success: function(data){ 
       	//alert(data.seriesDataCus);
       	//alert(data.tStarts);
        startsChart(data.seriesDataCus,data.tStarts,data.seriesDataDTC,data.tStartsDTC,data.seriesDataWholesale,data.tStartsWholesale);
       }
     });             
}

function startsChart(dataS,max1,dataDTC,max2,dataWholesale,max3){
    var sdate = $('#sdate').val();
    var sdateArr = sdate.split("/");

    var edate = $('#edate').val();
    var edateArr = edate.split("/");
    console.log(sdateArr);
    var startDate = new Date(sdate);
    makeDate = new Date(startDate.setMonth(startDate.getMonth() - 1));
    var prevM = makeDate.getMonth()+1;

     var endDate = new Date(edate);
    makeDateE = new Date(endDate.setMonth(endDate.getMonth()));
    var prevME = makeDateE.getMonth();

    if(prevM == 12){
      sdateArr[2] = sdateArr[2]-1;
    }
    if(prevME == 12){
      edateArr[2] = edateArr[2]-1;
    }

    var dateE = new Date(edate);
    var day = dateE.getDay();   
    
    $('#TCustomer-chart').highcharts({
        chart: {
          type: 'spline'
        },
        tooltip: {
		    shared: true,
		    split: false,
		    enabled: true
		},
        title: {
          text: 'Number of Total Customer',
          align:'left'
        },
        subtitle: {
          text: ' '
        },
        xAxis: {
          type: 'datetime',
          labels: {
              format: '{value:%e %b }'
          },
          startOfWeek:day,
          startOnTick: true,
          min: Date.UTC(sdateArr[2], prevM, sdateArr[1]),
          max: Date.UTC(edateArr[2], prevME,edateArr[1]),
          tickInterval: 7 * 24 * 3600 * 1000 // interval of 1 day
        },
       yAxis: {
            title: {
                text: ' '
            },
            min:0,
            max:max1
        },
        credits: {
            enabled: false
        },
        exporting: { 
            enabled: false 
        },
        colors: ['#1E90FF', '#FF8C00','#FF8C00'],
        series: eval(dataS) 
    });

    $('#TDTC-chart').highcharts({
        chart: {
          type: 'spline'
        },
        tooltip: {
		    shared: true,
		    split: false,
		    enabled: true
		},
        title: {
          text: 'Number of DTC',
          align:'left'
        },
        subtitle: {
          text: ' '
        },
        xAxis: {
          type: 'datetime',
          labels: {
              format: '{value:%e %b }'
          },
          startOfWeek:day,
          startOnTick: true,
          min: Date.UTC(sdateArr[2], prevM, sdateArr[1]),
          max: Date.UTC(edateArr[2], prevME,edateArr[1]),
          tickInterval: 7 * 24 * 3600 * 1000 // interval of 1 day
        },
       yAxis: {
            title: {
                text: ' '
            },
            min:0,
            max:max2
        },
        credits: {
            enabled: false
        },
        exporting: { 
            enabled: false 
        },
        colors: ['#1E90FF', '#FF8C00','#FF8C00'],
        series: eval(dataDTC) 
    });

    $('#TWholesale-chart').highcharts({
        chart: {
          type: 'spline'
        },
        tooltip: {
		    shared: true,
		    split: false,
		    enabled: true
		},
        title: {
          text: 'Number of Wholesale',
          align:'left'
        },
        subtitle: {
          text: ' '
        },
        xAxis: {
          type: 'datetime',
          labels: {
              format: '{value:%e %b }'
          },
          startOfWeek:day,
          startOnTick: true,
          min: Date.UTC(sdateArr[2], prevM, sdateArr[1]),
          max: Date.UTC(edateArr[2], prevME,edateArr[1]),
          tickInterval: 7 * 24 * 3600 * 1000 // interval of 1 day
        },
       yAxis: {
            title: {
                text: ' '
            },
            min:0,
            max:max3
        },
        credits: {
            enabled: false
        },
        exporting: { 
            enabled: false 
        },
        colors: ['#1E90FF', '#FF8C00','#FF8C00'],
        series: eval(dataWholesale) 
    });
}

function startsChart1(dataS,max1,id,tag){
    var sdate = $('#sdate').val();
    var sdateArr = sdate.split("/");
  // alert(dataS);
    var edate = $('#edate').val();
    var edateArr = edate.split("/");
    console.log(sdateArr);
    var startDate = new Date(sdate);
    makeDate = new Date(startDate.setMonth(startDate.getMonth() - 1));
    var prevM = makeDate.getMonth()+1;

     var endDate = new Date(edate);
    makeDateE = new Date(endDate.setMonth(endDate.getMonth()));
    var prevME = makeDateE.getMonth();

    if(prevM == 12){
      sdateArr[2] = sdateArr[2]-1;
    }
    if(prevME == 12){
      edateArr[2] = edateArr[2]-1;
    }

    var dateE = new Date(edate);
    var day = dateE.getDay();   
    
    $('#'+id).highcharts({
        chart: {
          type: 'spline'
        },
        tooltip: {
		    shared: true,
		    split: false,
		    enabled: true
		},
        title: {
          text: 'Number of '+tag,
          align:'left'
        },
        subtitle: {
          text: ' '
        },
        xAxis: {
          type: 'datetime',
          labels: {
              format: '{value:%e %b }'
          },
          startOfWeek:day,
          startOnTick: true,
          min: Date.UTC(sdateArr[2], prevM, sdateArr[1]),
          max: Date.UTC(edateArr[2], prevME,edateArr[1]),
          tickInterval: 7 * 24 * 3600 * 1000 // interval of 1 day
        },
       yAxis: {
            title: {
                text: ' '
            },
            min:0,
            max:max1
        },
        credits: {
            enabled: false
        },
        exporting: { 
            enabled: false 
        },
        colors: ['#1E90FF', '#FF8C00','#FF8C00'],
        series: eval(dataS) 
    });
}
</script>
