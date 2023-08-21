@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
    <div class="row clearfix">
    <div class="col-lg-12">
        <div class="card chat-app">
            <div id="plist" class="people-list">
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text"><i class="fa fa-search"></i></span>
                    </div>
                    <input type="text" class="form-control" placeholder="Search...">
                </div>



                <ul class="list-unstyled chat-list mt-2 mb-0">



                     @foreach ($friends as $friend)
                     <a href="{{ route('home',$friend['id']) }}">
                    <li class="clearfix">
                        <img src="https://bootdey.com/img/Content/avatar/avatar7.png" alt="avatar">
                        <div class="about">
                            <div class="name">{{ $friend['name'] }}</div>
                            <div class="status">
                                <div class="status" id = "status_{{ $friend['id'] }}">
                                @if($friend['is_online']==1)
                                  <i class="fa fa-circle online"></i> online </div>
                                @else
                                 <i class="fa fa-circle offline"></i> offline </div>
                                 @endif
                        </div>
                    </li>
                </a>
                      @endforeach
                  
                   
                   
                </ul>
            </div>
              @if($id)
            <div class="chat">
                <div class="chat-header clearfix">
                    <div class="row">

                        <div class="col-lg-6">
                            <a href="javascript:void(0);" data-toggle="modal" data-target="#view_info">
                                <img src="https://bootdey.com/img/Content/avatar/avatar7.png/?name={{ $otherUser->name }}" alt="avatar">
                            </a>
                            <div class="chat-about">
                         
                                <h6 class="m-b-0">{{ $otherUser->name }}</h6>
                                @if($friend['unread_messages']>0)
                                <div class="badge bg-success float-right">5</div>
                                @endif
                                <small>Last seen: 2 hours ago</small>
                            </div>
                        </div>
                        <div class="col-lg-6 hidden-sm text-right">
                            <a href="javascript:void(0);" class="btn btn-outline-secondary"><i class="fa fa-camera"></i></a>
                            <a href="javascript:void(0);" class="btn btn-outline-primary"><i class="fa fa-image"></i></a>
                            <a href="javascript:void(0);" class="btn btn-outline-info"><i class="fa fa-cogs"></i></a>
                            <a href="javascript:void(0);" class="btn btn-outline-warning"><i class="fa fa-question"></i></a>
                        </div>

                    </div>
                </div>

                    <div class="chat-history">
                     @foreach ($messages as $message)
                        <ul class="m-b-0">

                         @if($message['user_id']==Auth::id())
                            <li class="clearfix">
                                <div class="message-data text-right">
                                    <span class="message-data-time">{{ date("h:i:A",strtotime($message['created_at'])) }}
                                    </span>
                                    <img src="https://bootdey.com/img/Content/avatar/avatar7.png/?name={{ Auth::user()->name }}" alt="avatar">
                                </div>
                                <div class="message other-message float-right">{{$message['message'] }} </div>
                            </li>
                            @elseif ($message['user_id']==$otherUser->id)
                            <li class="clearfix">
                                <div class="message-data text-left">
                                    <span class="message-data-time">{{ date("h:i:A",strtotime($message['created_at'])) }}
                                    </span>
                                    <img src="https://bootdey.com/img/Content/avatar/avatar7.png/?name={{ $otherUser->name }}" alt="avatar">
                                </div>
                                <div class="message other-message float-left">{{$message['message'] }} </div>
                            </li>
                           @endif
                        </ul>
                        @endforeach
                    </div>



                <div class="chat-message clearfix">
                    <form id=chat-form action="" method="get">
                        <div class="input-group mb-0">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="fa fa-send"></i></span>
                            </div>
                            <input type="text"  id="message_input" class="form-control" placeholder="Enter text here...">
                        </div>
                    </form>
                </div>
            </div>

             @endif
        </div>
    </div>
</div>
     
    </div>
</div>
@endsection
@section('script')
<script>
   $(function(){
    var user_id = '{{ Auth::id() }}';
    var other_user_id="{{($otherUser)?$otherUser->id:''}}";
    var otherUserName = "{{($otherUser)?$otherUser->name:''}}";
    var socket = io("http://localhost:3000",{query:{user_id:user_id}});

    $("#chat-form").on("submit",function(e){
        e.preventDefault();
        var message =$("#message_input").val();
        if(message.trim().length == 0){
            $("#message_input").focus();
        }else{
            var data = {
                user_id,
                other_user_id,
                message,
                otherUserName,
            }
            socket.emit('sent_message', data);
            console.log(data);
            console.log(user_id+ ' ' + other_user_id);
            $('#message_input').val('');
        }
    })

    socket.on('receive_message', function(data){
        //array_push($messages, data);
        dd(data);
    });

    socket.on('user_connected', function(data){
        $("#status_"+data).html('<i class="fa fa-circle online"></i> online');
    });
    socket.on('user_disconnected',function(data){
        $("#status_"+data).html('<i class="fa fa-circle offline"></i> offline');
    }) 
   })
</script>
@endsection
