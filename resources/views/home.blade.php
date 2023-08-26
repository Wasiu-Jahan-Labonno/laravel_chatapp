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
                            <div class="status" id = "status_{{ $friend['id'] }}">
                                
                            @if($friend['is_online']==1)
                                <i class="fa fa-circle online"></i> online </div>
                            @else
                                <i class="fa fa-circle offline"></i> offline </div>
                            @endif
                        </div>
                        <div class="float-right" id="unread-count-{{$friend['id']}}">
                            @if($friend['unread_message']>0)
                                <span class="badge bg-success">{{$friend['unread_message']}}</span>
                            @endif
                        </div>
                        
                    </li>
                </a>
                      @endforeach
                  
                   
                   
                </ul>
            </div>
              @if($id)
            <div class="chat chatbox-custom">
                <div class="chat-header clearfix">
                    <div class="row">

                        <div class="col-lg-6">
                            <a href="javascript:void(0);" data-toggle="modal" data-target="#view_info">
                                <img src="{{asset('img/user.png')}}?name={{ $otherUser->name }}" alt="avatar">
                            </a>
                            <div class="chat-about">
                         
                                <h6 class="m-b-0">{{ $otherUser->name }}</h6>
                               
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
                     
                        <ul id="chatlog" class="m-b-0">
                        @foreach ($messages as $message)
                         @if($message['user_id']==Auth::id())
                            <li class="clearfix">
                                <div class="message-data text-right">
                               
                                    <span class="message-data-time">{{ $message['time'] }}
                                    </span>
                                    <img src="{{asset('img/user.png')}}?name={{ Auth::user()->name }}" alt="avatar">
                                   
                                </div>
                                <div class="message other-message float-right">{{$message['message'] }} </div>
                            </li>
                            @elseif ($message['user_id']==$otherUser->id)
                            <li class="clearfix">
                                <div class="message-data text-left">
                                
                                <img src="{{asset('img/user.png')}}?name={{ $otherUser->name }}" alt="avatar">
                                <span class="message-data-time">{{ $message['time'] }}
                                    </span>
                                  
                                </div>
                                <div class="message my-message float-left">{{$message['message'] }} </div>
                            </li>
                           @endif
                        @endforeach
                        </ul>
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
    $(document).ready(() => {
        $(".chat-history").animate({ scrollTop: $('.chat-history').prop("scrollHeight")}, 400);
    })
   $(function(){
    var user_id = '{{ Auth::id() }}';
    var other_user_id="{{($otherUser)?$otherUser->id:''}}";
    var otherUserName = "{{($otherUser)?$otherUser->name:''}}";
    var socket = io("http://192.168.1.125:3000",{query:{user_id:user_id}});

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
            $('#message_input').val('');
        }
    })

    socket.on('receive_message', function(data){
        var html;
        if((data.user_id == user_id && data.other_user_id==other_user_id) || (data.other_user_id==user_id && data.user_id == other_user_id)){
           if(data.user_id == user_id){
                html =`<li class="clearfix">
                                <div class="message-data text-right">
                                    <span class="message-data-time">${data.time}
                                    </span>
                                    <img src="{{asset('img/user.png')}}?name={{ Auth::user()->name }}" alt="{{ Auth::user()->name }}">
                                </div>
                                <div class="message other-message float-right">${data.message} </div>
                            </li>`
           }else{
            socket.emit('read_message', data.id);
             html = `<li class="clearfix">
                                <div class="message-data text-left">
                                    
                                    <img src="{{asset('img/user.png')}}?name=${data.otherUserName}" alt="${data.otherUserName}">
                                    <span class="message-data-time">${data.time}
                                    </span>
                                </div>
                                <div class="message my-message float-left">${data.message}</div>
                            </li>`;
           }
           $("#chatlog").append(html);
           $(".chat-history").animate({ scrollTop: $('.chat-history').prop("scrollHeight")}, 400);
          
        }else{
            $("#unread-count-"+data.user_id).html(`<span class="ml-5 badge bg-success fltrt">${data.unread_message}</span>`)
        }
    });

    socket.on('play_tune', () => {
        const notification_audio = new Audio('https://assets.mixkit.co/active_storage/sfx/951/951-preview.mp3');
        notification_audio.play();
    })

    socket.on('user_connected', function(data){
        $("#status_"+data).html('<i class="fa fa-circle online"></i> online');
    });
    socket.on('user_disconnected',function(data){
        $("#status_"+data).html('<i class="fa fa-circle offline"></i> offline');
    }) 
   })
</script>
@endsection
