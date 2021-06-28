@extends('layouts.app')
@section('style')

@endsection
@section('content')
<div class="container" style="margin: 2em auto">
  <div class="row">
    <div class="col-lg-6">
      <h1>Team</h1>
    </div>
    <div class="col-lg-6" style="text-align: right">
      <button class="btn btn-primary" type="button" data-toggle="collapse" data-target="#team" aria-expanded="false" aria-controls="collapseExample">
        Add Teammate
      </button>
    </div>
  </div>
  @if ($message = Session::get('success'))
  <div class="alert alert-success alert-block">
      <button type="button" class="close" data-dismiss="alert">×</button>    
      <strong>{{ $message }}</strong>
  </div>
  @endif
  <div class="collapse @if (!$errors->isEmpty()) show @endif" id="team" style="margin-top: 1em">
    <div class="card card-body">
        <form id="teammate_form" action="{{route('team.store')}}" method="POST">
            @csrf
            <div class="row">
                <div class="col-lg-3">
                    <div class="form-group">
                        <label for="first_name">First Name</label>
                        <input type="text" class="form-control @error('first_name') is-invalid @enderror" name="first_name" id="first_name" value="{{ old('first_name') }}" aria-describedby="emailHelp" >
                        @error('first_name')
                            <div class="alert alert-danger">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-lg-3">
                    <div class="form-group">
                        <label for="last_name">Last Name</label>
                        <input type="text" class="form-control" id="last_name" name="last_name" value="{{ old('last_name') }}" >
                    </div>
                </div>
                <div class="col-lg-3">
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}">
                        @error('email')
                            <div class="alert alert-danger">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-lg-3">
                    <label for="exampleInputPassword1">&nbsp;</label>
                    <button type="submit" class="btn btn-primary btn-block">Add</button>
                </div>
            </div>
        </form>
    </div>
  </div>
</div>

<div class="container">
  <div class="row">
    <div class="col-lg-12">
      <div>
        Showing {{$users->firstItem()}} to {{$users->lastItem()}} of {{$users->total()}} records
      </div>
      <div class="list-group">
        @if(count($users)>0)
          @foreach($users as $user)
          <?php
          $latestExpense = App\Models\Expense::where('user_id',$user->id)->latest()->first();
          ?>
            <a href="#" class="list-group-item list-group-item-action flex-column align-items-start">
              <div class="d-flex w-100 justify-content-between">
                <h5 class="mb-1">{{$user->first_name}} {{$user->last_name}}</h5>
                @if(!empty($latestExpense)>0)<small>last expense: {{App\Models\Expense::time_elapsed_string($latestExpense->expense_date)}} </small>@endif
              </div>
              <small>{{$user->first_name}} should receive Rs.400</small>
            </a>
          @endforeach
        @else
          <a href="#" class="list-group-item list-group-item-action flex-column align-items-start">
              <div class="d-flex w-100 justify-content-between">
                <h5 class="mb-1">No records found!</h5>
              </div>
            </a>
        @endif
      </div>
      <div>
        Showing {{$users->firstItem()}} to {{$users->lastItem()}} of {{$users->total()}} records
      </div>
      <div class="pagination">
        {{ $users->links() }}
      </div>
    </div>
  </div>
</div>

@endsection

@section('script')

@endsection