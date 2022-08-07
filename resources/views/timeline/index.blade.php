@extends('layouts.app')

@section('content')
    <h3>Timeline</h3>

    <div class="row">
	    <div class="col-lg-6">
            <form class="form-vertical" role="form" method="post" action="{{ route('status.post') }}">
            @csrf
                <div class="form-group{{ $errors->has('status') ? ' has-error' : '' }}">
                    <textarea name="status" placeholder="What's up, {{ Auth::user()->getFirstnameOrUsername() }}?" class="form-control" id="status" value="{{ old('status') }}"></textarea>


                <div class="form-group">
                    <button type="submit" class="btn btn-primary">Post Status</button>
                </div>

                <input type="hidden" name="_token" value="{{ Session::token() }}">

            </form>
	    </div>
	</div>

    <div class="row">
	    <div class="col-lg-6">
        @if (!$statuses->count())
	        <p>There is nothing in this timeline, yet.</p>
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
		            	<li><a href="{{ route('status.like', ['statusId' => $status->id]) }}">Like</a></li>
						<li> 10 likes </li>
	            	
					@endif
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
			                    	<li><a href="{{ route('status.like', ['statusId' => $reply->id]) }}">Like</a></li>
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

	{!! $statuses->render() !!}

@endif
	    </div>
	</div>

@endsection

