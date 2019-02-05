Laravel app for sport hotel management

Requirements:<br><ul>
  
<li>Php
<li>Laravel
<li>Web server 
<li>MySQL 

Firstly import database "szkoleniowy_osrodek_sportowy.sql", next create example admin to table users by command "php artisan db:seed".<br>
Finally run app by command "php artisan serve".<br>
<li>Simple Sample of Api</li>
POST /api/loginn -> token data={'email','password'} <br>
GET /api/user -> set auth bearer token
