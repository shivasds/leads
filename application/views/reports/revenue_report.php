<?php 
    defined('BASEPATH') OR exit('No direct script access allowed');
    if($this->session->userdata("user_type") == "admin")
        $this->load->view('inc/admin_header');
    else
        $this->load->view('inc/header');
    setlocale(LC_MONETARY, 'en_IN');
function formatcurrency($floatcurr,$curr = 'INR')
{

$currencies = array('INR' => array('&#2352;',2,'.',',',0));
        
        //rupees weird format
        if ($curr == "INR")
            $number = formatinr($floatcurr);
        if ($currencies[$curr][0] === NULL)
            $number.= ' '.$curr;
        elseif ($currencies[$curr][4]===1)
            $number.= $currencies[$curr][0];
        //normally in front
        else
            $number = $currencies[$curr][0].$number;
        return $number;
}
   
function formatinr($input)
{
        //CUSTOM FUNCTION TO GENERATE ##,##,###.##
        $dec = "";
        $pos = strpos($input, ".");
        if ($pos === false){
            //no decimals   
        } else {
            //decimals
            $dec = substr(round(substr($input,$pos),2),1);
            $input = substr($input,0,$pos);
        }
        $num = substr($input,-3); //get the last 3 digits
        $input = substr($input,0, -3); //omit the last 3 digits already stored in $num
        while(strlen($input) > 0) //loop the process - further get digits 2 by 2
        {
            $num = substr($input,-2).",".$num;
            $input = substr($input,0,-2);
        }
        return $num . $dec;
}
?>
<style type="text/css">
	.display td {
	    border: 1px solid #aaa;
	    padding: 5px
	  }
</style>
</head>
<body>
	<div class="container">
		<div class="page-header">
		  <h1><?php echo $heading; ?></h1>
		</div>
		<div class="alert alert-success" style="display: none;">
			<strong>Success!</strong> Email Sent.
		</div>
		<div class="alert alert-danger" style="display: none;">
			<strong>Error!</strong> Email not Sent.
		</div>
		<form method="GET" action="<?php echo base_url()?>admin/generate_report">
			<div class="col-sm-3 form-group">
				<label for="emp_code">Dept:</label>
	            <select  class="form-control"  id="dept" name="dept">
	                <option value="">Select</option>
	                <?php $all_department=$this->common_model->all_active_departments();
	                foreach($all_department as $department){ ?>
	                    <option value="<?php echo $department->id; ?>" <?php if($department->id==$dept) echo 'selected'; ?>><?php echo $department->name; ?></option>
	                <?php }?>              
	            </select>
			</div>
			<div class="col-sm-3 form-group">
				<label for="emp_code">City:</label>
	            <select  class="form-control"  id="city" name="city" >
	                <option value="">Select</option>
	                <?php $cities= $this->common_model->all_active_cities(); 
	                foreach( $cities as $c){ ?>
	                    <option value="<?php echo $c->id; ?>" <?php if($c->id==$city) echo 'selected'; ?> ><?php echo $c->name ?></option>
	                <?php } ?>               
	            </select>
			</div>
			<div class="col-sm-3 form-group">
	            <button type="submit" id="Generate" class="btn btn-success btn-block">Filter</button>
	        </div>
	        <div class="col-sm-3 form-group">
	            <button id="email_this_report" class="btn btn-default btn-block">Email this report</button>
	        </div>
	    </form>
	    <br>
		<table class="display" cellspacing="0" width="100%">
			<thead>
				<tr>
					<th>Sl.No</th>
					<th>Booking Name</th>
					<th>User/Manager Name</th>
					<th>Project Name</th>
					<th>Lead Id</th>
					<th>Added Date</th>
					<th>Booking Month</th>
					<th>Commission</th>
					<th>Gross Revenue</th>
					<th>Cashback</th>
					<th>Sub broker Amount</th>
					<th>Net Revenue</th>
				</tr>
			</thead>
			<tbody>
				<?php if(count($revenue_datas)>0){
					$i = 1;
					$total_gr = 0;
					$total_cb = 0;
					$total_sba = 0;
					$total_nr = 0;
					foreach ($revenue_datas as $key => $value) { 
						$project_name=$this->common_model->get_project_name($value->project_id);
						$user_name=$this->user_model->get_user_fullname($value->user_id);
						$total_gr += $value->gross_revenue;
						$total_cb += $value->cash_back;
						$total_sba += $value->sub_broker_amo;
						$total_nr += $value->net_revenue;
						?> 
					 	<tr class="revenue_row" data-id="<?php echo $value->callback_id; ?>">
					 		<td><?php echo $i; ?></td>
					 		<td><?php echo $value->booking; ?></td>
					 		<td><?php echo $user_name; ?></td>
					 		<td><?php echo $project_name; ?></td>
					 		<td><?php echo $value->leadid; ?></td>
					 		<td><?php echo $value->date_added; ?></td>
					 		<td><?php echo $value->booking_month; ?></td>
					 		<td><?php echo formatcurrency($value->commission); ?></td>
					 		<td><?php echo formatcurrency($value->gross_revenue); ?></td>
					 		<td><?php echo formatcurrency($value->cash_back); ?></td>
					 		<td><?php echo formatcurrency($value->sub_broker_amo); ?></td>
					 		<td><strong><?php echo formatcurrency($value->net_revenue); ?></strong></td>
					 		
					 	</tr>
					<?php $i++; } ?>
					<tr style="color: blue;">
						<td colspan="8"><strong>Total</strong></td>
						<td><strong><?php echo formatcurrency( $total_gr); ?></strong></td>
						<td><strong><?php echo formatcurrency( $total_cb); ?></strong></td>
						<td><strong><?php echo formatcurrency($total_sba); ?></strong></td>
						<td><strong><?php echo formatcurrency( $total_nr); ?></strong></td>
					</tr>
				<?php } else { ?>
					<tr>
						<td colspan="12"> No entries </td>
					</tr>
				<?php } ?>
				
			</tbody>
		</table>
		<br>
		<br>
	</div>
</body>
<script type="text/javascript">
	jQuery(document).ready(function($) {
	    $(".revenue_row").click(function() {
	        window.document.location = "<?php echo base_url().'dashboard/view_revenue/'; ?>"+$(this).data("id");
	    });
	});
	$("#email_this_report").click(function(e){
		e.preventDefault();
		$(".alert-success").hide();
		$(".alert-danger").hide();
		$.get("<?php echo base_url().'admin/email_report?fromDate='.urlencode($fromDate).'&toDate='.urlencode($toDate).'&city='.urlencode($city).'&dept='.urlencode($dept).'&reportType='.urlencode($reportType); ?>", function(response){
			if(response == "Success")
				$(".alert-success").show();
			else
				$(".alert-danger").show();
		});
	});
</script>