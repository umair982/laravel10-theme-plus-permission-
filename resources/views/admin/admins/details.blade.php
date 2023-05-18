<div class="card-datatable table-responsive">
	<table id="clients" class="datatables-demo table table-striped table-bordered">
		<tbody>
		<tr>
			<td>Name</td>
			<td>{{$admin->name}}</td>
		</tr>
		<tr>
			<td>Email</td>
			<td>{{$admin->email}}</td>
		</tr>
		<tr>
			<td>Status</td>
			<td>
				@if($admin->active)
					<label class="badge py-3 px-4 fs-7 badge-light-success">Active</label>
				@else
					<label class="badge py-3 px-4 fs-7 badge-light-danger">Inactive</label>
				@endif
			</td>
		</tr>
		<tr>
			<td>Created at</td>
			<td>{{$admin->created_at}}</td>
		</tr>

		</tbody>
	</table>
</div>

