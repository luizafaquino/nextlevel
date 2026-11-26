<?php

session_name("nextlevel_session");
session_start();

unset($_SESSION['usuario_id']);
unset($_SESSION['usuario_nome']);
unset($_SESSION['usuario_tipo']);

session_regenerate_id(true);

header("Location: index.php");

exit;