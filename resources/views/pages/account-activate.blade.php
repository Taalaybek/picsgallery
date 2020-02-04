@extends('app')
@section('content')
	<p>
		Account for {{ $user->email }} was successfully activated. 
		You will be redirected in 5 seconds to the login page.
	</p>
	<script>
		setTimeout(() => {
			window.location.href = '{{ env('FRONT_APP_URL') }}'
		}, 4000)
	</script>
@endsection
