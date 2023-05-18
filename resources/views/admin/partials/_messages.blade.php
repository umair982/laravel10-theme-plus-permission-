<div class="col-md-12 " style="margin-left: 3%">
    @if(Session::has('success_message'))
        <div class="alert alert-success col-md-6">
            {{ Session::get('success_message') }}
        </div>
    @endif
    @if(Session::has('error_message'))
        <div class="alert alert-danger col-md-6">
            {{ Session::get('error_message') }}
        </div>
    @endif
</div>

