
<?php if($_settings->chk_flashdata('success')): ?>
<script>
	alert_toast("<?php echo $_settings->flashdata('success') ?>",'success')
</script>
<?php endif;?>
<?php $date = isset($_GET['date']) ? $_GET['date'] : date("Y-m-d") ?>
<div class="card card-outline rounded-0 card-navy">
	<div class="card-header">
		<h3 class="card-title">Daily Passes History Report</h3>
	</div>
	<div class="card-body">
		<div class="contaner-fluid">
			<fieldset class="border mx-4 mb-3">
				<legend class="px-3 ml-4 w-auto">Filter Date</legend>
				<div class="container-fluid">
					<form action="" id="filter">
						<div class="row align-items-center">
							<div class="col-lg-4 col-md-6 col-sm-12 col-xs-12">
								<div class="form-group">
									<input type="date" max="<?= date("Y-m-d") ?>" name="date" value="<?= $date ?>" class="form-control form-control-sm rounded-0">
								</div>
							</div>
							<div class="col-lg-4 col-md-6 col-sm-12 col-xs-12">
								<div class="form-group">
									<button class="btn btn-sm btn-primary bg-gradient-primary rounded-0"><i class="fa fa-filter"></i> Filter</button>
									<button class="btn btn-sm btn-light bg-gradient-light border rounded-0" id="print" type="button"><i class="fa fa-print"></i> Print</button>
								</div>
							</div>
						</div>
					</form>
				</div>
			</fieldset>
		</div>
        <div class="container-fluid" id="printout">
			<table class="table table-hover table-striped table-bordered" id="list">
				<colgroup>
					<col width="5%">
					<col width="15%">
					<col width="15%">
					<col width="20%">
					<col width="20%">
					<col width="15%">
					<col width="10%">
				</colgroup>
				<thead>
					<tr>
						<th>#</th>
						<th>Date Created</th>
						<th>Pass Code</th>
						<th>Vehicle Details</th>
						<th>Owner/Toll Gate</th>
						<th>Processed By</th>
						<th>Balance</th>
					</tr>
				</thead>
				<tbody>
					<?php 
					$i = 1;
					$total = 0;
						$qry = $conn->query("SELECT ph.*, p.code, p.vehicle_name, p.owner, p.vehicle_registration, c.name as category, t.name as `toll` from `pass_history` ph inner join `pass_list` p on ph.pass_id = p.id inner join category_list c on p.category_id = c.id inner join `toll_list` t on ph.toll_id = t.id where date(ph.`date_created`) = '{$date}' order by unix_timestamp(ph.`date_created`) desc ");
						while($row = $qry->fetch_assoc()):
                            $user = $conn->query("SELECT concat(lastname, ', ', firstname, ' ', coalesce(middlename,'')) as `name` FROM `users` where id = '{$row['user_id']}'");
                            if($user->num_rows > 0){
                                $user_name = $user->fetch_array()['name'];
                            }
						$total += $row['cost'];
					?>
						<tr>
							<td class="text-center"><?php echo $i++; ?></td>
							<td><?php echo date("Y-m-d H:i",strtotime($row['date_created'])) ?></td>
							<td><?php echo $row['code'] ?></td>
							<td>
								<div style="line-height:1em">
									<div><?= $row['category'] ?></div>
									<div><?= $row['vehicle_name'] ?></div>
									<div><?= $row['vehicle_registration'] ?></div>
								</div>
							</td>
							<td>
								<div style="line-height:1em">
									<div><?= $row['owner'] ?></div>
									<div><?= $row['toll'] ?></div>
								</div>
							</td>
							<td><?php echo isset($user_name)? $user_name : '' ?></td>
							<td class="text-right"><?php echo number_format($row['cost']) ?></td>
						</tr>
					<?php endwhile; ?>
					<?php if($qry->num_rows <= 0): ?>
						<tr>
							<th colspan="7" class="text-center"><b>No Data</b></th>
						</tr>
					<?php endif; ?>
				</tbody>
				<tfoot>
					<tr>
						<th colspan="6" class="text-center"><b>Total</b></th>
						<th class="text-right"><b><?= number_format($total,2) ?></b></th>
					</tr>
				</tfoot>
			</table>
		</div>
	</div>
</div>
<noscript id="print-header">
	<div>
		<div class="d-flex w-100 align-items-center">
			<div class="col-2 text-center">
				<img src="<?= validate_image($_settings->info('logo'))?>" class="img-fluid img-thumbnail rounded-circle" style="height:5em;width:5em;object-fit:cover;object-position:center center" alt="">
			</div>
			<div class="col-8 text-center">
				<div style="line-height:.7em">
					<h4 class="text-center mb-0"><?= $_settings->info('name') ?></h4>
					<h3 class="text-center mb-0">Daily Passes History Report</h3>
					<div class="text-center m-1">as of</div>
					<div class="text-center"><?= date("F d, Y") ?></div>
				</div>
			</div>
		</div>
		<hr>
	</div>
</noscript>
<script>
	$(document).ready(function(){
		$('#list td,#list th').addClass('py-1 px-2 align-middle')
		$('#print').click(function(){
			var h = $('head').clone()
			var ph = $($('noscript#print-header').html()).clone()
			var p = $('#printout').clone()
			h.find('title').text("pass Details - Print View")
			// console.log(ph[0].outerHTML)
			start_loader()
			var nw = window.open('', '_blank','width=1000,height=800')
				nw.document.querySelector('head').innerHTML = h.html()
				nw.document.querySelector('body').innerHTML = ph[0].outerHTML + p[0].outerHTML
				nw.document.close()
				setTimeout(() => {
					nw.print()
					setTimeout(() => {
						nw.close()
						end_loader()
					}, 300);
				}, (300));
		})

		$('#filter').submit(function(e){
			e.preventDefault()
			location.href = "./?page=reports/pass_history&"+$(this).serialize()
		})
	})
</script>