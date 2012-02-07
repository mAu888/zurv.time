<?php
error_reporting(E_ALL);
ini_set('display_errors', true);

Zurv\Registry::getInstance()->db = new PDO('mysql:host=127.0.0.1;port=8889;dbname=timetracker', 'root', 'root');
Zurv\Registry::getInstance()->db->query('SET NAMES "utf8"');