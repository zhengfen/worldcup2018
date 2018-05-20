@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Admin</div>
                <div class="card-body">
                    <table class="table-bordered">
                        <thead>
                        <tr>
                            <th scope="col" class="text-center"><span title="Name">Nom affiché</span></th>
                            <th scope="col" class="text-center"><span title="Status">Status</span></th>
                            <th scope="col" class="text-center"><span title="Goal difference">Action</span></th>
                        </tr>
                        </thead>
                        @forelse ($users as $user)
                        <tr>
                        <td>{{ $user->name }} </td>  
                        <td id="{{ 'status_'.$user->id}}">{{ $user->status }}</td>
                        <td>  @if(Auth::id() == env('ADMIN_ID') )
                            <button class="btn btn-primary btn-sm" onclick='toggle_status({{$user->id}})'>Status Toggle</button>
                            @endif
                        </td>
                        </tr>
                        @empty
                        @endforelse
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    	
function toggle_status(user_id) {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    })
    var formData = {
        user_id : user_id,
    }
    $.ajax({
        type: "POST",
        url: window.App.baseurl+'/admin/status',
        data: formData,
        dataType: 'json',
        success: function (response) {
            $("#status_"+user_id).text(response.status);
            console.log(response.status);
        },
        error: function () {   
        }
    });
}  
</script>
@endsection