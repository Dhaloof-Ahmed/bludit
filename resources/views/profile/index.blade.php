@extends('layouts.app')

@section('content')

<h3>User Profile and Timeline</h3>


<div class="row">
    <div class="col-lg-5">
    @include('user.partials.userblock')
    <hr>

        @if (!$statuses->count())
            <p>{{ $user->getFirstNameOrUsername() }}hasn't posted anything, yet.</p>
        @else
	@foreach ($statuses as $status)
		<hr>
		<div class="media">
		    <a class="pull-left" href="{{ route('profile.index', ['username' => $status->user->username]) }}">
		        <img class="media-object" alt="{{ $status->user->getNameOrUsername() }}" src="{{ $status->user->getAvatarUrl() }}">
		    </a>
		    <div class="media-body">
		        <h4 class="media-heading"><a href="{{ route('profile.index', ['username' => $status->user->username]) }}">{{ $status->user->getNameOrUsername() }}</a></h4>
		        <p>{{ $status->body }}</p>
		        <ul class="list-inline">
		            <li>{{ $status->created_at->diffForHumans() }}</li>
                    @if ( $status->user_id !== Auth::user()->id )
		            	<li><a href="{{ route('status.like', $status->id) }}">Like</a></li>
	            	@endif
		            <li>10 likes</li>
		        </ul>
                
                
        @foreach ( $status->replies as $reply )
			        <div class="media">
			            <a class="pull-left" href="{{ route('profile.index', ['username' => $reply->user->username]) }}">
			                <img class="media-object" alt="{{ $reply->user->getNameOrUsername() }}" src="{{ $reply->user->getAvatarUrl() }}">
			            </a>
			            <div class="media-body">
			                <h5 class="media-heading"><a href="{{ route('profile.index', ['username' => $reply->user->username]) }}">{{ $reply->user->getNameOrUsername() }}</a></h5>
			                <p>{{ $reply->body }}</p>
			                <ul class="list-inline">
			                    <li>{{ $reply->created_at->diffForHumans() }}.</li>
			                    @if ( $reply->user_id !== Auth::user()->id )
			                    	<li><a href="{{ route('status.like', $reply->id) }}">Like</a></li>
			                    @endif
			                    <li>4 likes</li>
			                </ul>
			            </div>
			        </div>
		        @endforeach

		        <!-- show input textarea to reply to this status -->
		        @if ( ! isset($authUserIsFriend)  ||  Auth::user()->id===$status->user_id  ||  $authUserIsFriend  )
			        <form role="form" action="{{ route('status.reply', $status->id) }}" method="post">
			            <div class="form-group{{ $errors->has('reply-'.$status->id) ? ' has-error' : '' }}">
			                <textarea name="reply-{{ $status->id }}" class="form-control" rows="2" 
		                	 	 placeholder="Reply to this status"></textarea>
			                @if ($errors->has('reply-'.$status->id))
			                	<span class="help-block">{{ $errors->first('reply-'.$status->id) }}</span>
			                @endif
			            </div>
			            <input type="submit" value="Reply" class="btn btn-default btn-sm">
			            <input type="hidden" name="_token" value="{{ Session::token() }}">
			        </form>
		        @endif

		    </div>
		</div>
            @endforeach
        @endif

    </div>
    <div class="col-lg-4 col-lg-offset-1">

@if ( Auth::user()->hasFriendRequestPending($user) )
    <p>Waiting for {{ $user->getNameOrUsername() }} to accept your request</p>
@elseif ( Auth::user()->hasFriendRequestReceived($user) )
    <a href="{{ route('friend.accept', ['username' => $user->username]) }}" class="btn btn-primary">Accept friend request</a>
@elseif ( Auth::user()->isFriendsWith($user) )
    <p>You and {{ $user->getNameOrUsername() }} are friends</p>
@elseif ( Auth::user()->id !== $user->id) 
    <a href="{{ route('friend.add', ['username' => $user->username]) }}" class="btn btn-primary">Add as friend</a>
@endif

<h4>{{ $user->getFirstnameOrUsername() }}'s Friends</h4>

@if ( ! $user->friends()->count())
    <p>{{ $user->getFirstnameOrUsername() }} currently has no friends</p>
@else 
    @foreach ($user->friends() as $user)
        @include('user.partials.userblock')
    @endforeach
@endif

</div>
@endsection
