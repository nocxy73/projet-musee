<?php
// config.php - Paramètres de connexion à la base de données

define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'evan');
define('DB_PASSWORD', 'admin');
define('DB_NAME', 'musee_db');

// Établir la connexion à la base de données
$conn = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

// Vérifier la connexion
if(!$conn) {
    die("ERREUR : Impossible de se connecter à la base de données. " . mysqli_connect_error());
}
?>