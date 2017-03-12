@extends('layouts.app')

@section('content')

<script type="text/javascript">


    function getCookie(cname) {
        var cookies_id = null;
        var ca = document.cookie.split(';');
        console.log(ca);
        
        ca.forEach(function(element) {
            var cookies_array = element.split('=');
            cookies_array.forEach(function(val,index) {
                if(cname == val.trim()) {
                    cookies_id = cookies_array[1];
                    console.log(cookies_id);
                }     
            });
        });

        if(cookies_id == null) {
            actual_link = "<?php echo "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";?>" ; 
            alert("Please Re-Log in");
            window.location = actual_link;
        }
    }




</script>
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2" style="margin-bottom: 15px">
            <div class="panel panel-default">
                <div class="panel-heading">Dashboard</div>

                <div class="panel-body">
                    Hi {{  $user_name }}
                    <br>
                    You are logged in!
                </div>
            </div>
        </div>
        <div class="clearfix"> </div>
        <div style="margin-bottom: 10px;margin-left: 15px">
            <button class="btn btn-primary" onclick="adduser()"> Add User </button>
        </div>
        <div class="col-md-12">
            <div class="table-responsive">          
                  <table class="table table-hover table-bordered table-striped">
                    <thead>
                      <tr class="info">
                        <th class="text-center"> no </th>
                        <th class="text-center"> ID </th>
                        <th class="text-center"> Name </th>
                        <th class="text-center"> Email </th>
                        <th class="text-center"> Token </th>
                        <th class="text-center"> Last Activity </th>
                        <th class="text-center"> Action </th>
                      </tr>
                    </thead>
                    <tbody>
                        @if(count($data) > 0)
                            @foreach($data as $key=>$val)
                            <tr>
                                <td class="text-center" style="vertical-align: middle;"> 
                                    {{ $key+1 }} 
                                </td>
                                <td class="text-center" style="vertical-align: middle;">
                                    {{ $val->id }}
                                </td>
                                <td class="text-center" style="vertical-align: middle;">
                                    {{ $val->name }}
                                </td>
                                <td class="text-center" style="vertical-align: middle;">
                                    {{ $val->email }}
                                </td>
                                <td class="text-center" style="vertical-align: middle;">
                                    {{ $val->remember_token }}
                                </td>
                                <td class="text-center" style="vertical-align: middle;">
                                    <?php
                                    echo gmdate("Y-m-d H:i:s", $val->last_activity);
                                    ?>
                                </td>
                                <td>
                                    <div class="btn-group-vertical">
                                        <button type="button" class="btn btn-warning btn-outline" 
                                        onclick="editinfo('{{ $val->id }}','{{ $val->name }}','{{ $val->email }}')"> 
                                            Edit Info 
                                        </button>
                                        <button type="button" class="btn btn-danger btn-outline" 
                                        onclick="check('{{ $val->id }}')"> 
                                            Delete
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        @else
                        <tr>
                            <td colspan="12" style="text-align: center"> No User Data </td>
                        </tr>
                        @endif
                    </tbody>
                  </table>
            </div>
        </div>
        <div class="clearfix"> </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function() {
        getCookie("cookies_id");
    });
    function check(id) {
        var confirm_str = "Apakah anda yakin akan menghapus user id : " + id + " ?" ; 
        var r = confirm(confirm_str);
        if(r == true) {
            var data_request = {
                type : "delete_user",
                id   : id
            }
            $.ajaxSetup({
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
            });
            $.ajax({
                    dataType : "JSON",
                    type : "POST",
                    url : '{{ $url}}/delete',
                    data: data_request,
                    success: function( data_response ) {
                        alert(data_response.data);
                    }
                });

        } else {
            return false;
        }
    }
    function adduser() {
        $('#addModal').modal({backdrop: 'static', keyboard: false});
    }
    function editinfo(id,name,email) {
        $('#id_edit').val(id);
        $('#name_edit').val(name);
        $('#email_edit').val(email);  
        $('#editModal').modal({backdrop: 'static', keyboard: false});
    }
</script>
<div id="editModal" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-body">
                <div class="panel panel-primary">
                    <div class="panel-heading text-center"> Edit User </div>
                    <div class="panel-body">
                        <form class="form-horizontal" role="form" method="POST" action="{{ $url }}/edit">
                        {{ csrf_field() }}

                            <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
                                <label for="name" class="col-md-4 control-label"> ID </label>

                                <div class="col-md-6">
                                    <input id="id_edit" type="text" class="form-control" name="id" value="" autofocus readonly="">

                                    @if ($errors->has('name'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('name') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
                                <label for="name" class="col-md-4 control-label">Name</label>

                                <div class="col-md-6">
                                    <input id="name_edit" type="text" class="form-control" name="name" value="{{ old('name') }}" required autofocus>

                                    @if ($errors->has('name'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('name') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                                <label for="email" class="col-md-4 control-label">E-Mail Address</label>

                                <div class="col-md-6">
                                    <input id="email_edit" type="email" class="form-control" name="email" value="{{ old('email') }}" required>

                                    @if ($errors->has('email'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('email') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>

                    
                            <input id="password-confirm" type="hidden" class="form-control" name="url" 
                            value="<?php echo "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]"; ?>" required>

                            <div class="form-group">
                                <div class="col-md-6 col-md-offset-4">
                                    <button type="submit" class="btn btn-warning" name="type" value="edituser">
                                        Submit
                                    </button>
                                    <button type="submit" class="btn btn-danger" data-dismiss="modal">
                                        Cancel
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="panel-footer"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="addModal" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-body">
                <div class="panel panel-primary">
                    <div class="panel-heading text-center"> Input New User </div>
                    <div class="panel-body">
                        <form class="form-horizontal" role="form" method="POST" action="{{ $url }}/adduser">
                        {{ csrf_field() }}

                            <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
                                <label for="name" class="col-md-4 control-label">Name</label>

                                <div class="col-md-6">
                                    <input id="name" type="text" class="form-control" name="name" value="{{ old('name') }}" required autofocus>

                                    @if ($errors->has('name'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('name') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                                <label for="email" class="col-md-4 control-label">E-Mail Address</label>

                                <div class="col-md-6">
                                    <input id="email" type="email" class="form-control" name="email" value="{{ old('email') }}" required>

                                    @if ($errors->has('email'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('email') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                                <label for="password" class="col-md-4 control-label">Password</label>

                                <div class="col-md-6">
                                    <input id="password" type="password" class="form-control" name="password" required>

                                    @if ($errors->has('password'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('password') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="password-confirm" class="col-md-4 control-label">Confirm Password</label>

                                <div class="col-md-6">
                                    <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required>
                                </div>
                            </div>

                            <input id="password-confirm" type="hidden" class="form-control" name="url" 
                            value="<?php echo "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]"; ?>" required>

                            <div class="form-group">
                                <div class="col-md-6 col-md-offset-4">
                                    <button type="submit" class="btn btn-warning" name="type" value="adduser">
                                        Register
                                    </button>
                                    <button type="submit" class="btn btn-danger" data-dismiss="modal">
                                        Cancel
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="panel-footer"></div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
