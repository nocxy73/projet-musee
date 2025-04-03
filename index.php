<?php
// Activer l'affichage des erreurs pour le débogage
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Connexion à la base de données
$host = 'localhost';
$dbname = 'musee_db';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}

// Fonction pour compter les visiteurs par type d'exposition
function getVisitorsCount($pdo, $expositionType) {
    $query = "SELECT COUNT(DISTINCT v.id_visiteur) as count 
              FROM Visiteur v 
              JOIN Acheter a ON v.id_visiteur = a.id_visiteur 
              JOIN Type_Ticket t ON a.id_ticket = t.id_ticket 
              WHERE v.h_depart IS NULL 
              AND (t.libelle = :type OR t.libelle = 'Les deux expositions')";
    
    try {
        $stmt = $pdo->prepare($query);
        $stmt->execute(['type' => $expositionType]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['count'] ?? 0;
    } catch (PDOException $e) {
        error_log("Erreur de comptage des visiteurs : " . $e->getMessage());
        return 0;
    }
}

// Récupérer le nombre de visiteurs pour chaque exposition
$visiteursPermanent = getVisitorsCount($pdo, 'Exposition Permanente');
$visiteursTemporaire = getVisitorsCount($pdo, 'Exposition Temporaire');

// Gestion des actions (ajout de visiteurs, suppression, etc.)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_visitor'])) {
        try {
            $pdo->beginTransaction();
            
            // Vérifier si le visiteur existe déjà
            $stmt = $pdo->prepare("SELECT id_visiteur FROM Visiteur WHERE mail = :mail AND h_depart IS NULL");
            $stmt->execute(['mail' => $_POST['mail']]);
            if ($stmt->fetch()) {
                throw new Exception("Un visiteur avec cet email est déjà présent dans le musée.");
            }
            
            // Ajouter le visiteur
            $stmt = $pdo->prepare("INSERT INTO Visiteur (nom, prenom, age, mail, tel, h_arrivee) VALUES (:nom, :prenom, :age, :mail, :tel, NOW())");
            $stmt->execute([
                'nom' => $_POST['nom'],
                'prenom' => $_POST['prenom'],
                'age' => $_POST['age'],
                'mail' => $_POST['mail'],
                'tel' => $_POST['tel']
            ]);
            
            $id_visiteur = $pdo->lastInsertId();
            
            // Associer le visiteur au type de billet
            $stmt = $pdo->prepare("INSERT INTO Acheter (id_visiteur, id_ticket) VALUES (:id_visiteur, :id_ticket)");
            $stmt->execute([
                'id_visiteur' => $id_visiteur,
                'id_ticket' => $_POST['ticket']
            ]);
            
            $pdo->commit();
            echo "<div class='success-message'>Visiteur ajouté avec succès !</div>";
            
            // Mettre à jour les compteurs
            $visiteursPermanent = getVisitorsCount($pdo, 'Exposition Permanente');
            $visiteursTemporaire = getVisitorsCount($pdo, 'Exposition Temporaire');
            
        } catch (Exception $e) {
            $pdo->rollBack();
            echo "<div class='error-message'>Erreur : " . htmlspecialchars($e->getMessage()) . "</div>";
        }
    } elseif (isset($_POST['delete_visitor'])) {
        try {
            $pdo->beginTransaction();
            $id_visiteur = $_POST['id_visiteur'];
            
            // Supprimer d'abord les enregistrements dans la table Acheter
            $stmt = $pdo->prepare("DELETE FROM Acheter WHERE id_visiteur = :id_visiteur");
            $stmt->execute(['id_visiteur' => $id_visiteur]);
            
            // Ensuite supprimer le visiteur
            $stmt = $pdo->prepare("DELETE FROM Visiteur WHERE id_visiteur = :id_visiteur");
            $stmt->execute(['id_visiteur' => $id_visiteur]);
            
            $pdo->commit();
            echo "<div class='success-message'>Visiteur supprimé avec succès !</div>";
            
            // Mettre à jour les compteurs
            $visiteursPermanent = getVisitorsCount($pdo, 'Exposition Permanente');
            $visiteursTemporaire = getVisitorsCount($pdo, 'Exposition Temporaire');
            
        } catch (Exception $e) {
            $pdo->rollBack();
            echo "<div class='error-message'>Erreur lors de la suppression : " . htmlspecialchars($e->getMessage()) . "</div>";
        }
    }
}

$date = (new DateTime())->format('d/m/Y');
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Système de Gestion du Musée</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="styles.css" rel="stylesheet">
</head>
<body>
    <header class="header">
        <h1 class="main-title">
            <span class="icon">🏛️</span> Système de Gestion du Musée
        </h1>
        <p class="subtitle">Une expérience culturelle unique</p>
    </header>

    <nav>
        <ul>
            <li><a href="#dashboard">Tableau de bord</a></li>
            <li><a href="#visitors">Visiteurs</a></li>
            <li><a href="#statistics">Statistiques</a></li>
        </ul>
    </nav>

    <section id="dashboard">
        <h2>🎨 Tableau de bord</h2>
        <div class="dashboard-container">
            <div class="card">
                <h3>🟣 Visiteurs actuels</h3>
                <?php
                $stmt = $pdo->query("SELECT COUNT(*) AS total_visiteurs FROM Visiteur WHERE h_depart IS NULL");
                $total_visiteurs = $stmt->fetch(PDO::FETCH_ASSOC)['total_visiteurs'];
                ?>
                <p class="visitor-count"><?php echo $total_visiteurs; ?> / 50</p>
                <div class="progress-bar">
                    <div class="progress" style="width: <?php echo ($total_visiteurs / 50) * 100; ?>%;"></div>
                </div>
            </div>

            <div class="card">
                <h3>🎨 Exposition Permanente</h3>
                <p class="visitor-count"><?php echo $visiteursPermanent; ?> / 50</p>
                <div class="progress-bar">
                    <div class="progress" style="width: <?php echo ($visiteursPermanent / 50) * 100; ?>%;"></div>
                </div>
            </div>

            <div class="card">
                <h3>✨ Exposition Temporaire</h3>
                <p class="visitor-count"><?php echo $visiteursTemporaire; ?> / 50</p>
                <div class="progress-bar">
                    <div class="progress" style="width: <?php echo ($visiteursTemporaire / 25) * 50; ?>%;"></div>
                </div>
            </div>
        </div>

        <div class="dashboard-summary">
            <div class="card">
                <h3>📋 Résumé des activités</h3>
                <?php
                $stmt = $pdo->query("SELECT COUNT(*) AS visiteurs_actuels FROM Visiteur WHERE h_depart IS NULL");
                $visiteurs_actuels = $stmt->fetch(PDO::FETCH_ASSOC)['visiteurs_actuels'];
                
                $stmt = $pdo->query("SELECT COUNT(*) AS visites_terminees FROM Visiteur WHERE DATE(h_depart) = CURDATE()");
                $visites_terminees = $stmt->fetch(PDO::FETCH_ASSOC)['visites_terminees'];
                ?>
                <p>📅 <strong>Aujourd'hui :</strong> <?php echo $date; ?></p>
                <p>👥 <strong>Visiteurs actuels :</strong> <?php echo $visiteurs_actuels; ?> / 50</p>
                <p>🏛️ <strong>Visites terminées aujourd'hui :</strong> <?php echo $visites_terminees; ?></p>
            </div>
        </div>
    </section>

    <section id="visitors">
        <h2>👥 Gestion des Visiteurs</h2>
        <form method="POST" class="add-visitor-form">
            <div class="form-group">
                <label for="nom">Nom :</label>
                <input type="text" name="nom" id="nom" required>
            </div>
            
            <div class="form-group">
                <label for="prenom">Prénom :</label>
                <input type="text" name="prenom" id="prenom" required>
            </div>
            
            <div class="form-group">
                <label for="age">Âge :</label>
                <input type="number" name="age" id="age" required min="0" max="120">
            </div>
            
            <div class="form-group">
                <label for="mail">Email :</label>
                <input type="email" name="mail" id="mail" required>
            </div>
            
            <div class="form-group">
                <label for="tel">Téléphone :</label>
                <input type="tel" name="tel" id="tel" required pattern="[0-9]{10}">
            </div>
            
            <div class="form-group">
                <label for="ticket">Type de billet :</label>
                <select name="ticket" id="ticket" required>
                    <option value="1">Exposition Permanente</option>
                    <option value="2">Exposition Temporaire</option>
                    <option value="3">Les deux expositions</option>
                </select>
            </div>
            
            <button type="submit" name="add_visitor" class="submit-btn">Ajouter un visiteur</button>
        </form>

        <h3>Liste des visiteurs actuels</h3>
        <div class="visitors-list">
            <?php
            $stmt = $pdo->query("
                SELECT v.id_visiteur, v.nom, v.prenom, v.age, v.mail, v.tel, v.h_arrivee, 
                       t.libelle AS type_billet
                FROM Visiteur v
                JOIN Acheter a ON v.id_visiteur = a.id_visiteur
                JOIN Type_Ticket t ON a.id_ticket = t.id_ticket
                WHERE v.h_depart IS NULL
                ORDER BY v.h_arrivee DESC
            ");

            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                echo "<div class='visitor-card'>";
                echo "<div class='visitor-info'>";
                echo "<p><strong>Nom :</strong> " . htmlspecialchars($row['nom']) . " " . htmlspecialchars($row['prenom']) . "</p>";
                echo "<p><strong>Âge :</strong> " . htmlspecialchars($row['age']) . " ans</p>";
                echo "<p><strong>Email :</strong> " . htmlspecialchars($row['mail']) . "</p>";
                echo "<p><strong>Téléphone :</strong> " . htmlspecialchars($row['tel']) . "</p>";
                echo "<p><strong>Type de billet :</strong> " . htmlspecialchars($row['type_billet']) . "</p>";
                echo "<p><strong>Heure d'arrivée :</strong> " . date('H:i', strtotime($row['h_arrivee'])) . "</p>";
                echo "</div>";
                echo "<form method='POST' class='delete-form'>";
                echo "<input type='hidden' name='id_visiteur' value='" . htmlspecialchars($row['id_visiteur']) . "'>";
                echo "<button type='submit' name='delete_visitor' class='delete-btn'>Supprimer</button>";
                echo "</form>";
                echo "</div>";
            }
            ?>
        </div>
    </section>

    <section id="statistics">
        <h2>📊 Statistiques</h2>
        <div class="stats-container">
            <?php
            // Calculer les statistiques
            $stmt = $pdo->query("
                SELECT t.libelle, COUNT(*) as count
                FROM Acheter a
                JOIN Type_Ticket t ON a.id_ticket = t.id_ticket
                JOIN Visiteur v ON a.id_visiteur = v.id_visiteur
                WHERE v.h_depart IS NULL
                GROUP BY t.libelle
            ");
            
            $total = 0;
            $stats = [];
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $stats[$row['libelle']] = $row['count'];
                $total += $row['count'];
            }
            
            foreach ($stats as $type => $count) {
                $percentage = $total > 0 ? round(($count / $total) * 100) : 0;
                echo "<div class='card'>";
                echo "<h3>" . htmlspecialchars($type) . "</h3>";
                echo "<p>" . $percentage . "%</p>";
                echo "</div>";
            }
            ?>
        </div>

        <h3>Fréquentation hebdomadaire</h3>
        <canvas id="weeklyChart"></canvas>
    </section>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const ctx = document.getElementById('weeklyChart').getContext('2d');
        const weeklyChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi', 'Dimanche'],
                datasets: [{
                    label: 'Nombre de visiteurs',
                    data: [0, 0, 0, 0, 0, 0, 0], // Données vides par défaut
                    backgroundColor: 'rgba(99, 102, 241, 0.7)',
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            color: '#f1f5f9'
                        },
                        grid: {
                            color: 'rgba(255, 255, 255, 0.1)'
                        }
                    },
                    x: {
                        ticks: {
                            color: '#f1f5f9'
                        },
                        grid: {
                            color: 'rgba(255, 255, 255, 0.1)'
                        }
                    }
                }
            }
        });
    </script>
</body>
</html>