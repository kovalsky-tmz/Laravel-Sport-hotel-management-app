@if(count($errors)>0)

	
		<div class="col-md-8 alert alert-danger" style="margin-top: 1rem; margin-bottom:-15px;">
		  	
			  	@foreach($errors->all() as $error)
			  		<ul>
			  			<li style="list-style-type:none"><span class="fa fa-exclamation-circle" aria-hidden="true">&nbsp</span>{{ $error }}</li>
			  		</ul>
			  	@endforeach
			 
		 </div>

@endif