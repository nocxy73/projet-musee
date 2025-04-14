<?php
// Activer l'affichage des erreurs pour le débogage
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Connexion à la base de données
$host = 'localhost';
$dbname = 'musee';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}

// Fonction pour valider et assainir les entrées utilisateur
function validateInput($data) {
    return htmlspecialchars(trim($data));
}

// Récupérer l'historique des visites
$stmt = $pdo->query("
SELECT v.id_visiteur, v.nom, v.prenom, v.mail, v.h_arrivee, v.h_depart, 
       GROUP_CONCAT(e.titre SEPARATOR ', ') as expositions,
       TIMESTAMPDIFF(MINUTE, v.h_arrivee, v.h_depart) as duree_visite
FROM Visiteur v 
JOIN Visite vi ON v.id_visiteur = vi.id_visiteur
JOIN Exposition e ON vi.id_exposition = e.id_exposition
WHERE v.h_depart IS NOT NULL
GROUP BY v.id_visiteur
ORDER BY v.h_depart DESC
LIMIT 100");

$historique = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Historique des Visites - Musée</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="styles.css" rel="stylesheet">
    <style>
        .historique-list {
            background-color: #2a2a2a;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            overflow-x: auto;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        
        th, td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #444;
        }
        
        th {
            background-color: #334155;
            color: #f1f5f9;
            font-weight: bold;
        }
        
        tr:hover {
            background-color: #333;
        }
        
        .back-link {
            display: inline-block;
            margin-top: 20px;
            color: #6366f1;
            text-decoration: none;
            font-weight: bold;
        }
        
        .back-link:hover {
            text-decoration: underline;
        }
        
        .no-results {
            padding: 20px;
            text-align: center;
            color: #cbd5e1;
        }
    </style>
</head>
<body>
    <header class="header">
        <h1 class="main-title">
            <span class="icon">🏛️</span> Système de Gestion du Musée
        </h1>
        <p class="subtitle">Historique des visites</p>
    </header>

    <nav>
        <ul>
            <li><a href="index.php#dashboard">Tableau de bord</a></li>
            <li><a href="index.php#visitors">Visiteurs</a></li>
            <li><a href="index.php#statistics">Statistiques</a></li>
            <li><a href="historique.php" class="active">Historique</a></li>
        </ul>
    </nav>

    <section>
        <h2>📜 Historique des visites</h2>
        
        <div class="historique-list">
            <?php if (count($historique) > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Visiteur</th>
                            <th>Email</th>
                            <th>Arrivée</th>
                            <th>Départ</th>
                            <th>Durée</th>
                            <th>Expositions visitées</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($historique as $visite): ?>
                        <tr>
                            <td><?= htmlspecialchars($visite['nom'] . ' ' . $visite['prenom']) ?></td>
                            <td><?= htmlspecialchars($visite['mail']) ?></td>
                            <td><?= date('d/m/Y H:i', strtotime($visite['h_arrivee'])) ?></td>
                            <td><?= date('d/m/Y H:i', strtotime($visite['h_depart'])) ?></td>
                            <td><?= $visite['duree_visite'] ?> min</td>
                            <td><?= htmlspecialchars($visite['expositions']) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="no-results">
                    <p>Aucune visite terminée n'a été enregistrée.</p>
                </div>
            <?php endif; ?>
        </div>
        
        <a href="index.php" class="back-link"><i class="fas fa-arrow-left"></i> Retour au tableau de bord</a>
    </section>

    <script>
        // Script pour le chargement dynamique des données si nécessaire
        document.addEventListener('DOMContentLoaded', function() {
            console.log('La page d\'historique est chargée');
        });
    </script>
</body>
</html>