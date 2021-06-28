@extends('layouts.app')

@section('content')
<div class="container" style="margin:2em auto">
<div class="row">
    <div class="col-lg-6">
        <h1>Team Expenses</h1>
    </div>
    <div class="col-lg-6" style="text-align: right">
        <button class="btn btn-primary" type="button" data-toggle="collapse" data-target="#expense" aria-expanded="false" aria-controls="collapseExample">
            Add an Expense
        </button>
    </div>
</div>
@if ($message = Session::get('success'))
  <div class="alert alert-success alert-block">
      <button type="button" class="close" data-dismiss="alert">×</button>    
      <strong>{{ $message }}</strong>
  </div>
@endif
<div class="collapse @if (!$errors->isEmpty()) show @endif" id="expense" style="margin-top: 1em">
    <div class="card card-body">
        <form id="expense_form" action="{{route('expenses.store')}}" method="POST">
            @csrf
            <div class="row">
                <div class="col-lg-3">
                    <div class="form-group">
                        <label for="user_id">Select User</label>
                        <select class="form-control @error('user_id') is-invalid @enderror" name="user_id" id="user_id" aria-describedby="emailHelp" placeholder="Enter email">
                            @if(!empty($users))
                            <option value="" selected disabled>Select User</option>
                            @foreach($users as $user)
                                <option value="{{$user->id}}" @if(old('user_id') == $user->id) selected @endif>{{$user->first_name}} {{$user->last_name}} ({{$user->email}})</option>
                            @endforeach
                            @else
                                <option value="" selected disabled>User not found!</option>
                            @endif
                        </select>
                        @error('user_id')
                            <div class="alert alert-danger">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-lg-3">
                    <div class="form-group">
                        <label for="expense_date">Expense Date</label>
                        <input type="datetime-local" class="form-control @error('expense_date') is-invalid @enderror" id="expense_date" name="expense_date" placeholder="Date" value="{{ old('expense_date') }}">
                        @error('expense_date')
                            <div class="alert alert-danger">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-lg-3">
                    <div class="form-group">
                        <label for="amount">Expense Amount</label>
                        <input type="text" class="form-control touchspin @error('amount') is-invalid @enderror" id="amount" name="amount" placeholder="Amount" autocomplete="off" value="{{ old('amount') }}">
                        @error('amount')
                            <div class="alert alert-danger">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-lg-3">
                    <label for="exampleInputPassword1">&nbsp;</label>
                    <button type="submit" class="btn btn-primary btn-block">Submit</button>
                </div>
            </div>
        </form>
    </div>
</div>        
</div>
<div class="container">
    <div>
        Showing {{$expenses->firstItem()}} to {{$expenses->lastItem()}} of {{$expenses->total()}} records
    </div>
  <table class="table table-hover">
    <thead>
      <tr>
        <th scope="col">#</th>
        <th scope="col">Expense by</th>
        <th scope="col">Date</th>
        <th scope="col">Amount</th>
        <th scope="col">Actions</th>
      </tr>
    </thead>
    <tbody>
    @php
        $i=1;
    @endphp
    @if(count($expenses)>0)
        @foreach($expenses as $expense)
          <tr>
            <th scope="row">{{$i}}</th>
            <td>{{$expense->users->first_name}} {{$expense->users->last_name}}</td>
            <td>{{date("d/m/Y G:ia",strtotime($expense->expense_date))}}</td>
            <td>Rs.{{$expense->amount}}</td>
            <td><button type="button" class="btn btn-light edit-btn" data-id="{{$expense->id}}">Edit</button></td>
          </tr>
        @php
            $i++;
        @endphp
        @endforeach
    @else
    <tr>
        <td colspan="5" style="text-align:center;">No records found!</td>
    </tr>
    @endif
    </tbody>
  </table>
    <div>
        Showing {{$expenses->firstItem()}} to {{$expenses->lastItem()}} of {{$expenses->total()}} records
      </div>
      <div class="pagination">
        {{ $expenses->links() }}
      </div>
</div>
<!-- Edit Modal -->
<div class="modal" id="edit-modal" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Edit</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form id="expense_form" action="{{route('expenses.update')}}" method="POST">
            @csrf
            @method('PUT')
            <input type="hidden" name="id" id="edit_id" value="{{ old('id') }}">
            <div class="row">
                <div class="col-lg-3">
                    <div class="form-group">
                        <label for="user_id">Select User</label>
                        <select class="form-control @if($errors->edit->first('user_id')) is-invalid @endif" name="user_id" id="edit_user_id" aria-describedby="emailHelp" placeholder="Enter email" value="{{ old('user_id') }}">
                            @if(!empty($users))
                            <option value="" selected disabled>Select User</option>
                            @foreach($users as $user)
                                <option value="{{$user->id}}" @if(old('user_id') == $user->id) selected @endif>{{$user->first_name}} {{$user->last_name}} ({{$user->email}})</option>
                            @endforeach
                            @else
                                <option value="" selected disabled>User not found!</option>
                            @endif
                        </select>
                        @if($errors->edit->first('user_id'))
                            <div class="alert alert-danger">{{ $errors->edit->first('user_id') }}</div>
                        @endif
                    </div>
                </div>
                <div class="col-lg-3">
                    <div class="form-group">
                        <label for="expense_date">Expense Date</label>
                        <input type="datetime-local" class="form-control @if($errors->edit->first('expense_date')) is-invalid @endif" id="edit_expense_date" name="expense_date" value="{{ old('expense_date') }}" placeholder="Date">
                        @if($errors->edit->first('expense_date'))
                            <div class="alert alert-danger">{{ $errors->edit->first('expense_date') }}</div>
                        @endif
                    </div>
                </div>
                <div class="col-lg-3">
                    <div class="form-group">
                        <label for="amount">Expense Amount</label>
                        <input type="text" class="form-control touchspin @if($errors->edit->first('amount')) is-invalid @endif" id="edit_amount" name="amount" value="{{ old('amount') }}" placeholder="Amount" autocomplete="off">
                        @if($errors->edit->first('amount'))
                            <div class="alert alert-danger">{{ $errors->edit->first('amount') }}</div>
                        @endif
                    </div>
                </div>
                <div class="col-lg-3">
                    <label for="exampleInputPassword1">&nbsp;</label>
                    <button type="submit" class="btn btn-primary btn-block">Submit</button>
                </div>
            </div>
        </form>
      </div>
      <div class="modal-footer">
        
      </div>
    </div>
  </div>
</div>
@endsection

@section('script')
<script>
    $.ajaxSetup({
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $("input[name='amount']").TouchSpin({
        min: -1000000000,
        max: 1000000000,
        step: 0.1,
        decimals: 2,
        boostat: 5,
        maxboostedstep: 10000000
    });

    // edit a row
    @if($errors->edit->any())
    $('#edit-modal').modal('show');
    @endif
    $('.edit-btn').on('click', function(){
        $.ajax({
            type:"GET",
            url: "{{ url('/expenses-edit') }}/"+ ($(this).data('id')),
            data:'',
            success: function(data){
                if(data.status){
                    $('#edit_id').val(data.expenses.id);
                    $('#edit_user_id').val(data.expenses.user_id);
                    $('#edit_expense_date').val(data.expenses.expense_date);
                    $('#edit_amount').val(data.expenses.amount);
                    $('#edit-modal').modal('show');
                }
            }
        });
    });
</script>
@endsection