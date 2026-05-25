<?php

session_start();
session_destroy();
header("Location: account.php?loggedout=1");
exit();
