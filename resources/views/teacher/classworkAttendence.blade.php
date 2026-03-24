@extends('layouts.teacher_analytics-master')



@section('content')

<!-- Flash Messages -->
@include('layouts.partials.flash-messages')

<div class="row">
    <div class="container-fluid mt-3">
        <!-- Page-Title -->
        <div class="row">
                <div class="col-lg-4">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="header-title mt-0 mb-3">Seen by Students</h4>
                            <div class="slimscroll crm-dash-activity">
                                <div class="activity">
                                    @php
                                        $i=1;
                                    @endphp
                                    @isset($readNotications)
                                    @foreach($readNotications as $readNotication)
                                    @if($readNotication->data['classworkId']==$id)
                                    <div class="activity-info">
                                        <div class="icon-info-activity">

                                            <span class="badge badge-primary badge-round">{{$i}}</span>
                                        </div>
                                        <div class="activity-info-text">
                                            <div class="d-flex justify-content-between align-items-center">
                                                @php
                                                    $usrs = $users->where('id',$readNotication->notifiable_id);
                                                @endphp
                                            <h6 class="m-0 w-75">@foreach($usrs as $usr){{$usr->name}} @endforeach</h6>
                                            </div>
                                            <div>
                                                <h7>{{$readNotication->read_at->format('h:ia d/M')}}</h7>
                                            </div>

                                        </div>
                                    </div>
                                    <hr>
                                    @php
                                        $i=$i+1
                                    @endphp
                                    @endif
                                    @endforeach
                                    @endisset
                                </div><!--end activity-->
                            </div><!--end crm-dash-activity-->
                        </div>  <!--end card-body-->
                    </div><!--end card-->
                </div><!--end col-->    <div class="col-lg-4">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="header-title mt-0 mb-3">Unseen by Students</h4>
                            <div class="slimscroll crm-dash-activity">
                                <div class="activity">
                                    @php
                                        $i=1;
                                    @endphp
                                    @isset($unreadNotications)
                                    @foreach($unreadNotications as $unreadNotication)
                                    @if($unreadNotication->data['classworkId']==$id)
                                    <div class="activity-info">
                                        <div class="icon-info-activity">

                                            <span class="badge badge-danger badge-round">{{$i}}</span>
                                        </div>
                                        <div class="activity-info-text">
                                            <div class="d-flex justify-content-between align-items-center">
                                                @php
                                                    $usrs = $users->where('id',$unreadNotication->notifiable_id);
                                                @endphp
                                            <h6 class="m-0 w-75">@foreach($usrs as $usr){{$usr->name}} @endforeach</h6>
                                            </div>

                                        </div>
                                    </div>
                                    <hr>
                                    @php
                                        $i=$i+1
                                    @endphp
                                    @endif
                                    @endforeach
                                    @endisset
                                </div><!--end activity-->
                            </div><!--end crm-dash-activity-->
                        </div>  <!--end card-body-->
                    </div><!--end card-->
                </div><!--end col-->

        </div>
    </div>
    @stop
