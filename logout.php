<?php

require_once 'session_config.php';
session_destroy();
header("Location: account.php?loggedout=1");
exit();
?>