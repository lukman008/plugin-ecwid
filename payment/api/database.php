<?php

require('./rb.php');

R::setup('mysql:host=' . DBHOST . ';dbname=' . DBNAME . '', DBUSER, DBPASS);
R::freeze(true);
