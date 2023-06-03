<?php
require 'config/constants.php';
//destroy all sessions and redirect to home page
session_destroy();
header('location: ' . ROOT_URL);
die();