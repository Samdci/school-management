@extends('layouts.backend')

@section('content')
<div class="container">
    @if(auth()->user()->role !== 'teacher')
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="box" style="min-width: 100vh">
                <div class="box-header">
                    @if (session('status'))
                    <div class="alert alert-success" role="alert">
                        {{ session('status') }}                      
                    </div>
                    @endif
                </div>

                <div class="row">
                    <div class="col-lg-4 col-xs-6">
                      <!-- small box -->
                      <div class="small-box bg-aqua">
                        <div class="inner">
                          <h3>{{ $totalUsers }}</h3>

                          <p>Total Users</p>
                        </div>
                        <div class="icon">
                          <i class="ion ion-person-add"></i>
                        </div>
                        <a href="{{ route('users.index') }}" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
                      </div>
                    </div>
                    <!-- ./col -->
                    <div class="col-lg-4 col-xs-6">
                      <!-- small box -->
                      <div class="small-box bg-green">
                        <div class="inner">
                          <h3>{{ $allStudents }}</h3>

                          <p>Total Students</p>
                        </div>
                        <div class="icon">
                          <i class="ion ion-person-add"></i>
                        </div>
                        <a href="{{ route('students.index') }}" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
                      </div>
                    </div>
                    <!-- ./col -->
                    <div class="col-lg-4 col-xs-6">
                      <!-- small box -->
                      <div class="small-box bg-yellow">
                        <div class="inner">
                          <h3>{{ $allTeachers }}</h3>

                          <p>Total Teachers</p>
                        </div>
                        <div class="icon">
                          <i class="ion ion-person-add"></i>
                        </div>
                        <a href="{{ route('teachers.index') }}" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
                      </div>
                    </div>

                  </div>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection
