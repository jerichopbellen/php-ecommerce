<?php
session_start();
session_destroy();
header("Location: /furnitures/index.php");