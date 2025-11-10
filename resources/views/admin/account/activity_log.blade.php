@extends('admin.master')

@section('content')
<div class="container-fluid">
	<h1 class="h3 mb-4 text-gray-800">Activity Log</h1>

	<div class="card shadow mb-4">
		<div class="card-body table-responsive">
			<table class="table table-bordered">
				<thead>
					<tr>
						<th>#</th>
						<th>Description</th>
						<th>Properties</th>
						<th>Date</th>
					</tr>
				</thead>
				<tbody>
				@forelse($logs as $log)
					<tr>
						<td>{{ $log->id }}</td>
						<td>{{ $log->description }}</td>
						<td><pre class="mb-0" style="white-space: pre-wrap;">{{ json_encode($log->properties, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE) }}</pre></td>
						<td>{{ $log->created_at }}</td>
					</tr>
				@empty
					<tr>
						<td colspan="4" class="text-center text-muted">No activity found</td>
					</tr>
				@endforelse
				</tbody>
			</table>
		</div>
		<div class="card-footer">
			{{ $logs->links() }}
		</div>
	</div>
</div>
@endsection


