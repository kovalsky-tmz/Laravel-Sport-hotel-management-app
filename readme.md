<h2>Laravel app for sport hotel management</h2>

<b>Requirements:</b> Php, Laravel, Web server, MySQL
<p>
Firstly import database "szkoleniowy_osrodek_sportowy.sql", next create example admin to table users by command "php artisan db:seed".<br>
  <b>Admin account</b> - Email: admin@mail.com, password: admin <br>
  Finally run app by command "<code>php artisan serve</code>".<br>
  </p>
<h4>Simple Sample of Api:</h4>
POST /api/loginn -> get token, data={'email','password'} <br>
GET /api/user -> bearer token required
