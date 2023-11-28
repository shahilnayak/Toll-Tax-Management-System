
<?php if($_settings->chk_flashdata('success')): ?>
<script>
	alert_toast("<?php echo $_settings->flashdata('success') ?>",'success')
</script>
<?php endif;?>
<div class="card card-outline rounded-0 card-navy">
	<div class="card-header">
		<h3 class="card-title">List of Recipients</h3>
		<div class="card-tools">
			<a href="./?page=recipients/manage_recipient" id="create_new" class="btn btn-flat btn-primary"><span class="fas fa-plus"></span>  Create New</a>
		</div>
	</div>
	<div class="card-body">
        <div class="container-fluid">
			<div class="form-row mb-3">
				<div class="col-md-4">
					<label for="filter-category">Filter by Category:</label>
					<select class="form-control form-control-sm rounded-0" id="filter-category" name="filter-category">
						<option value="">All Categories</option>
						<?php 
							$qry = $conn->query("SELECT * FROM `category_list` where delete_flag = 0 and `status` = 1");
							while($row= $qry->fetch_assoc()):
						?>
							<option value="<?= $row['id'] ?>"><?= $row['name'] ?></option>
						<?php endwhile; ?>
					</select>
				</div>
				<div class="col-md-4">
					<label for="filter-date">Filter by Date:</label>
					<input type="text" class="form-control form-control-sm rounded-0 datepicker" id="filter-date" name="filter-date" placeholder="Select Date">
				</div>
				<div class="col-md-2">
					<label>&nbsp;</label>
					<button class="btn btn-primary btn-sm bg-gradient-primary rounded-0 form-control" id="apply-filter">Apply Filter</button>
				</div>
				<div class="col-md-2">
					<label>&nbsp;</label>
					<button class="btn btn-secondary btn-sm bg-gradient-secondary rounded-0 form-control" id="reset-filter">Reset Filter</button>
				</div>
			</div>
			<table class="table table-hover table-striped table-bordered" id="list">
				<colgroup>
					<col width="5%">
					<col width="15%">
					<col width="20%">
					<col width="20%">
					<col width="20%">
					<col width="10%">
					<col width="10%">
				</colgroup>
				<thead>
					<tr>
						<th>#</th>
						<th>Date Created</th>
						<th>Category</th>
						<th>Owner/Driver</th>
						<th>Toll Gate</th>
						<th>Cost</th>
						<th>Action</th>
					</tr>
				</thead>
				<tbody>
					<?php 
					$i = 1;
					$uwhere = "";
					if($_settings->userdata('type') != 1)
					$uwhere =" where r.user_id = '{$_settings->userdata('id')}' ";
					$qry = $conn->query("SELECT r.*, c.name as category, t.name as `toll` from `recipient_list` r inner join category_list c on r.category_id = c.id inner join `toll_list` t on r.toll_id = t.id {$uwhere} order by unix_timestamp(r.`date_created`) desc ");
						while($row = $qry->fetch_assoc()):
					?>
						<tr>
							<td class="text-center"><?php echo $i++; ?></td>
							<td><?php echo date("Y-m-d H:i",strtotime($row['date_created'])) ?></td>
							<td><?php echo $row['category'] ?></td>
							<td><?php echo $row['owner'] ?></td>
							<td><?php echo $row['toll'] ?></td>
							<td><?php echo $row['cost'] ?></td>
							<td align="center">
								 <button type="button" class="btn btn-flat p-1 btn-default btn-sm dropdown-toggle dropdown-icon" data-toggle="dropdown">
				                  		Action
				                    <span class="sr-only">Toggle Dropdown</span>
				                  </button>
				                  <div class="dropdown-menu" role="menu">
				                    <a class="dropdown-item view_data" href="./?page=recipients/view_recipient&id=<?php echo $row['id'] ?>"><span class="fa fa-eye text-dark"></span> View</a>
				                    <div class="dropdown-divider"></div>
				                    <a class="dropdown-item edit_data" href="./?page=recipients/manage_recipient&id=<?php echo $row['id'] ?>"><span class="fa fa-edit text-primary"></span> Edit</a>
				                    <div class="dropdown-divider"></div>
				                    <a class="dropdown-item delete_data" href="javascript:void(0)" data-id="<?php echo $row['id'] ?>"><span class="fa fa-trash text-danger"></span> Delete</a>
				                  </div>
							</td>
						</tr>
					<?php endwhile; ?>
				</tbody>
			</table>
			<p class="text-right">Total:</p>
			<p class="text-right" id="total-cost">0.00</p>
		</div>
	</div>
</div>
<script>
	$(document).ready(function(){
		$('.delete_data').click(function(){
			_conf("Are you sure to delete this recipient permanently?","delete_recipient",[$(this).attr('data-id')])
		})
		$('.table').dataTable({
			columnDefs: [
					{ orderable: false, targets: [4] }
			],
			order:[0,'asc']
		});
		$('.dataTable td,.dataTable th').addClass('py-1 px-2 align-middle')
		$('#list').on('draw.dt', function() {
            updateTotalCost();
        });

		$('.datepicker').datepicker({
            autoclose: true,
            format: 'yyyy-mm-dd',
        });

		// Apply filter button click event
		$('#apply-filter').on('click', function() {
			var category = $('#filter-category').val();
			var date = $('#filter-date').val();

			// Use DataTables API to perform filtering
			var table = $('#list').DataTable();
			table.search('').columns().search('').draw(); // Clear existing search filters

			// Apply new search filters based on category and date
			var categoryMapping = {
				1: '2 Wheeler',
				2: '4 Wheeler',
				3: '6 Wheeler',
				4: '10 Wheeler',
				5: '12Wheeler',
				6: 'sample',
				7: 'test123',
				8: 'testing',
				9: 'testing 2'
			};
			var categoryName = categoryMapping[category]
			if (category) {
				table.column(2).search(categoryName).draw();
			}
			if (date) {
				// Format the date input to match the date format in the table
				var formattedDate = formatDateForDataTable(date);
				table.columns(1).search(formattedDate).draw();
			}

			// Update the total cost after applying the filter
			updateTotalCost();
		});

		function formatDateForDataTable(date) {
			// Format the input date to match the date format in the table
			var parts = date.split('/');
			var dayPart = parts[1];
			var monthPart = parts[0];
			var yearPart = parts[2];
			return yearPart + '-' + monthPart + '-' + dayPart;
		}


        // Reset filter button click event
		$('#reset-filter').on('click', function() {
			$('#filter-category').val('');
			$('#filter-date').val('');

			// Use DataTables API to clear search filters and redraw the table
			var table = $('#list').DataTable();
			table.search('').columns().search('').draw();

			// Update the total cost after resetting the filter
			updateTotalCost();
		});

	})
	function updateTotalCost() {
		var totalCost = 0;
		$('#list tbody tr').each(function() {
			var costValue = $(this).find('td:eq(5)').text().replace(/[^0-9.]/g, ''); // Extract only numeric characters
			if (!isNaN(costValue) && costValue !== "") {
				totalCost += parseFloat(costValue);
			}
		});
		console.log(totalCost.toFixed(2));
		$('#total-cost').text(totalCost.toFixed(2));
	}
	function delete_recipient($id){
		start_loader();
		$.ajax({
			url:_base_url_+"classes/Master.php?f=delete_recipient",
			method:"recipient",
			data:{id: $id},
			dataType:"json",
			error:err=>{
				console.log(err)
				alert_toast("An error occured.",'error');
				end_loader();
			},
			success:function(resp){
				if(typeof resp== 'object' && resp.status == 'success'){
					location.reload();
				}else{
					alert_toast("An error occured.",'error');
					end_loader();
				}
			}
		})
	}
</script>