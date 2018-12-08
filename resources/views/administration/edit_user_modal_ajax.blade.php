<div class="modal fade" id="EditUserModal{{$user->user_id}}" tabindex="-1" role="dialog" aria-labelledby="EditUserModalLabel" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="EditUserModalLabel">Edycja Użytkownika</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
				<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<form method='POST' action='/users_list/edit/{{$user->user_id}}'>
		
					{{ csrf_field() }}

						<div class="row">
							<div class="col-md-6">
								<div class="form-group">
									<label for="email">Email:</label>
									<input type="email" class="form-control" required name="email" id="email" placeholder="Example@gmail.com" value={{$user->email }}>
								</div>

								<div class="form-group">
									<label for="password">Hasło:</label>
									<input type="password" class="form-control" name="password" id="password" placeholder="Minimum 6 znaków" >
								</div>

								<div class="form-group">
									<label for="password_confirmation">Potwierdzenie Hasła:</label>
									<input type="password" class="form-control" name="password_confirmation" id="password_confirmation" placeholder="Potwierdź hasło">
								</div>
							</div>

							<div class="col-md-6">

								<div class="form-group">
									<label for="first_name">Imię:</label>
									<input type="text" class="form-control" name="first_name" id="first_name" value={{ $user->first_name }}>
								</div>
								<div class="form-group">
									<label for="last_name">Nazwisko:</label>
									<input type="text" class="form-control" required name="last_name" id="last_name" value={{ $user->last_name }}>
								</div>
								<div class="form-group">
									<label for="phone">Telefon:</label>
									<input type="text" class="form-control" required name="phone" id="phone" value={{ $user->phone }}>
								</div>
								<div class="form-group">
									<label for="city">Miasto:</label>
									<input type="text" class="form-control" name="city" id="city" value={{ $user->city }}>
								</div>
								
							</div>

						</div>
					<div class="form-group" style="margin-left: 40%;margin-top: 1rem" >
							<button type="submit" class="btn btn-primary">Zapisz Zmiany!</button>
					</div>
				</form>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-dismiss="modal">Zamknij</button>
			</div>
		</div>
	</div>
</div>
