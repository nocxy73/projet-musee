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

// Récupération des filtres d'historique
$periode = isset($_GET['periode']) ? $_GET['periode'] : 'tout';
$exposition = isset($_GET['exposition']) ? (int)$_GET['exposition'] : 0;
$search = isset($_GET['search']) ? validateInput($_GET['search']) : '';
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$items_per_page = 15;

// Construction de la requête SQL en fonction des filtres
$where_clauses = ["v.h_depart IS NOT NULL"]; // Condition de base
$params = [];

// Filtre par période
switch ($periode) {
    case 'aujourdhui':
        $where_clauses[] = "DATE(v.h_depart) = CURDATE()";
        break;
    case 'semaine':
        $where_clauses[] = "v.h_depart >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)";
        break;
    case 'mois':
        $where_clauses[] = "v.h_depart >= DATE_SUB(CURDATE(), INTERVAL 1 MONTH)";
        break;
}

// Filtre par exposition
if ($exposition > 0) {
    $where_clauses[] = "vi.id_exposition = :exposition";
    $params['exposition'] = $exposition;
}

// Filtre par recherche
if (!empty($search)) {
    $where_clauses[] = "(v.nom LIKE :search OR v.prenom LIKE :search OR v.mail LIKE :search)";
    $params['search'] = "%$search%";
}

// Construction de la clause WHERE finale
$where_clause = implode(" AND ", $where_clauses);

// Requête pour compter le nombre total de résultats
$count_query = "
SELECT COUNT(DISTINCT v.id_visiteur) as total
FROM Visiteur v 
JOIN Visite vi ON v.id_visiteur = vi.id_visiteur
WHERE $where_clause";

$stmt_count = $pdo->prepare($count_query);
foreach ($params as $key => $value) {
    $stmt_count->bindValue(":$key", $value);
}
$stmt_count->execute();
$total_items = $stmt_count->fetch(PDO::FETCH_ASSOC)['total'];

// Calcul du total de pages
$total_pages = ceil($total_items / $items_per_page);
$page = max(1, min($page, $total_pages));
$offset = ($page - 1) * $items_per_page;

// Requête principale pour récupérer l'historique des visites avec pagination
$query = "
SELECT v.id_visiteur, v.nom, v.prenom, v.mail, v.age, v.tel, v.h_arrivee, v.h_depart, 
       GROUP_CONCAT(e.titre SEPARATOR ', ') as expositions,
       TIMESTAMPDIFF(MINUTE, v.h_arrivee, v.h_depart) as duree_visite
FROM Visiteur v 
JOIN Visite vi ON v.id_visiteur = vi.id_visiteur
JOIN Exposition e ON vi.id_exposition = e.id_exposition
WHERE $where_clause
GROUP BY v.id_visiteur
ORDER BY v.h_depart DESC
LIMIT :offset, :limit";

$stmt = $pdo->prepare($query);
foreach ($params as $key => $value) {
    $stmt->bindValue(":$key", $value);
}
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->bindValue(':limit', $items_per_page, PDO::PARAM_INT);
$stmt->execute();
$historique = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Récupération des expositions pour le filtre
$stmt_expos = $pdo->query("SELECT id_exposition, titre FROM Exposition ORDER BY titre");
$expositions = $stmt_expos->fetchAll(PDO::FETCH_ASSOC);

// Récupération des statistiques
$stmt_stats = $pdo->query("
    SELECT 
        COUNT(*) as total_visites,
        AVG(TIMESTAMPDIFF(MINUTE, h_arrivee, h_depart)) as duree_moyenne,
        MAX(TIMESTAMPDIFF(MINUTE, h_arrivee, h_depart)) as duree_max,
        MIN(TIMESTAMPDIFF(MINUTE, h_arrivee, h_depart)) as duree_min
    FROM Visiteur
    WHERE h_depart IS NOT NULL
");
$stats = $stmt_stats->fetch(PDO::FETCH_ASSOC);

// Statistiques par exposition
$stmt_expo_stats = $pdo->query("
    SELECT 
        e.titre,
        COUNT(DISTINCT v.id_visiteur) as nb_visiteurs,
        AVG(TIMESTAMPDIFF(MINUTE, v.h_arrivee, v.h_depart)) as duree_moyenne
    FROM Exposition e
    JOIN Visite vi ON e.id_exposition = vi.id_exposition
    JOIN Visiteur v ON vi.id_visiteur = v.id_visiteur
    WHERE v.h_depart IS NOT NULL
    GROUP BY e.id_exposition
    ORDER BY nb_visiteurs DESC
");
$expo_stats = $stmt_expo_stats->fetchAll(PDO::FETCH_ASSOC);

// Formatage des durées pour les statistiques
$duree_moy = round($stats['duree_moyenne'] ?? 0);
$duree_min = round($stats['duree_min'] ?? 0);
$duree_max = round($stats['duree_max'] ?? 0);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Historique des Visites - Musée</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Playfair+Display:wght@400;700&display=swap" rel="stylesheet">
    <link href="styles.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #6366f1;
            --secondary-color: #4f46e5;
            --accent-color: #ec4899;
            --background: #0f172a;
            --card-bg: #1e293b;
            --card-hover: #2a3b55;
            --text-primary: #f1f5f9;
            --text-secondary: #cbd5e1;
            --success-color: #10b981;
            --warning-color: #f59e0b;
            --danger-color: #ef4444;
            --gradient-primary: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            --shadow-md: 0 4px 6px rgba(0, 0, 0, 0.1);
            --border-radius-md: 12px;
        }
        
        body {
            background-color: var(--background);
            color: var(--text-primary);
            font-family: 'Poppins', sans-serif;
            background-image: 
                radial-gradient(at 80% 0%, hsla(225, 39%, 30%, 0.4) 0px, transparent 50%),
                radial-gradient(at 20% 100%, hsla(256, 39%, 30%, 0.4) 0px, transparent 50%);
            background-attachment: fixed;
        }
        
        .historique-container {
            background-color: var(--card-bg);
            border-radius: var(--border-radius-md);
            padding: 1.5rem;
            box-shadow: var(--shadow-md);
            margin-bottom: 1.5rem;
            border: 1px solid rgba(255, 255, 255, 0.05);
        }
        
        .filter-form {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-bottom: 1.5rem;
        }
        
        .filter-group {
            display: flex;
            flex-direction: column;
        }
        
        .filter-group label {
            margin-bottom: 0.5rem;
            color: var(--text-secondary);
            font-weight: 500;
            font-size: 0.9rem;
        }
        
        .filter-group select,
        .filter-group input {
            padding: 0.75rem;
            border-radius: 0.5rem;
            background-color: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: var(--text-primary);
            transition: all 0.3s ease;
        }
        
        .filter-group select:focus,
        .filter-group input:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 2px rgba(99, 102, 241, 0.2);
        }
        
        .filter-button {
            background: var(--gradient-primary);
            color: white;
            border: none;
            padding: 0.75rem 1.5rem;
            border-radius: 0.5rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-align: center;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }
        
        .filter-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }
        
        .table-responsive {
            overflow-x: auto;
            margin-top: 1rem;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 0.5rem;
        }
        
        thead {
            background-color: rgba(99, 102, 241, 0.1);
        }
        
        th, td {
            padding: 0.75rem 1rem;
            text-align: left;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        th {
            color: var(--text-primary);
            font-weight: 600;
            font-size: 0.9rem;
        }
        
        tbody tr {
            transition: background-color 0.2s ease;
        }
        
        tbody tr:hover {
            background-color: rgba(255, 255, 255, 0.05);
        }
        
        .pagination {
            display: flex;
            justify-content: center;
            gap: 0.5rem;
            margin-top: 1.5rem;
        }
        
        .pagination a, .pagination span {
            padding: 0.5rem 0.75rem;
            border-radius: 0.5rem;
            text-decoration: none;
            transition: all 0.3s ease;
            background: rgba(255, 255, 255, 0.05);
            color: var(--text-primary);
        }
        
        .pagination a:hover {
            background: rgba(99, 102, 241, 0.2);
        }
        
        .pagination .current {
            background: var(--primary-color);
            color: white;
        }
        
        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
            margin-top: 1rem;
        }
        
        .back-link:hover {
            color: var(--secondary-color);
            transform: translateX(-5px);
        }
        
        .no-results {
            padding: 2rem;
            text-align: center;
            color: var(--text-secondary);
            background: rgba(255, 255, 255, 0.02);
            border-radius: 0.5rem;
            font-style: italic;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 1rem;
            margin-bottom: 1.5rem;
        }
        
        .stat-card {
            background: rgba(255, 255, 255, 0.05);
            border-radius: 0.5rem;
            padding: 1.25rem;
            text-align: center;
            transition: all 0.3s ease;
            border: 1px solid rgba(255, 255, 255, 0.05);
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
            background: rgba(255, 255, 255, 0.1);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }
        
        .stat-value {
            font-size: 1.8rem;
            font-weight: 700;
            color: var(--primary-color);
            margin: 0.5rem 0;
        }
        
        .stat-label {
            color: var(--text-secondary);
            font-size: 0.9rem;
            font-weight: 500;
        }
        
        .stat-icon {
            font-size: 1.5rem;
            color: var(--accent-color);
            margin-bottom: 0.5rem;
        }
        
        .expo-stat-bar {
            height: 8px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 4px;
            overflow: hidden;
            margin-top: 0.5rem;
        }
        
        .expo-stat-fill {
            height: 100%;
            background: var(--primary-color);
            border-radius: 4px;
        }
        
        .expo-stat-item {
            margin-bottom: 1rem;
        }
        
        .expo-stat-title {
            display: flex;
            justify-content: space-between;
            margin-bottom: 0.25rem;
        }
        
        .expo-stat-name {
            font-weight: 500;
            color: var(--text-primary);
        }
        
        .expo-stat-value {
            color: var(--accent-color);
            font-weight: 600;
        }
        
        .detail-row {
            cursor: pointer;
        }
        
        .visitor-details {
            background: rgba(255, 255, 255, 0.03);
            padding: 1rem;
            border-radius: 0.5rem;
            margin: 0.5rem 0;
            border-left: 3px solid var(--primary-color);
            display: none;
        }
        
        .visitor-details.show {
            display: block;
            animation: fadeIn 0.3s ease;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .detail-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
        }
        
        .detail-item {
            display: flex;
            flex-direction: column;
        }
        
        .detail-label {
            font-size: 0.8rem;
            color: var(--text-secondary);
            margin-bottom: 0.25rem;
        }
        
        .detail-value {
            font-weight: 500;
            color: var(--text-primary);
        }
        
        .export-buttons {
            display: flex;
            justify-content: flex-end;
            gap: 0.5rem;
            margin-bottom: 1rem;
        }
        
        .export-button {
            background: rgba(255, 255, 255, 0.05);
            color: var(--text-primary);
            border: 1px solid rgba(255, 255, 255, 0.1);
            padding: 0.5rem 0.75rem;
            border-radius: 0.5rem;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.3s ease;
            font-size: 0.9rem;
        }
        
        .export-button:hover {
            background: rgba(255, 255, 255, 0.1);
            transform: translateY(-2px);
        }
        
        .export-button i {
            font-size: 1rem;
        }
        
        /* Responsive adjustments */
        @media (max-width: 768px) {
            .filter-form {
                grid-template-columns: 1fr;
            }
            
            .filter-group {
                margin-bottom: 0.5rem;
            }
            
            .stats-grid {
                grid-template-columns: 1fr;
            }
            
            th, td {
                padding: 0.5rem;
            }
            
            .table-responsive {
                margin-left: -1rem;
                margin-right: -1rem;
                padding: 0 1rem;
                width: calc(100% + 2rem);
            }
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
            <li><a href="index.php#dashboard"><i class="fas fa-chart-pie"></i> Tableau de bord</a></li>
            <li><a href="index.php#visitors"><i class="fas fa-users"></i> Visiteurs</a></li>
            <li><a href="index.php#statistics"><i class="fas fa-chart-bar"></i> Statistiques</a></li>
            <li><a href="historique.php" class="active"><i class="fas fa-history"></i> Historique</a></li>
        </ul>
    </nav>

    <section>
        <h2><i class="fas fa-history"></i> Historique des visites</h2>
        
        <div class="historique-container">
            <h3>Statistiques des visites</h3>
            
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon"><i class="fas fa-users"></i></div>
                    <div class="stat-value"><?= number_format($stats['total_visites'] ?? 0, 0, ',', ' ') ?></div>
                    <div class="stat-label">Visites terminées</div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon"><i class="fas fa-clock"></i></div>
                    <div class="stat-value"><?= $duree_moy ?> min</div>
                    <div class="stat-label">Durée moyenne</div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon"><i class="fas fa-hourglass-start"></i></div>
                    <div class="stat-value"><?= $duree_min ?> min</div>
                    <div class="stat-label">Durée minimum</div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon"><i class="fas fa-hourglass-end"></i></div>
                    <div class="stat-value"><?= $duree_max ?> min</div>
                    <div class="stat-label">Durée maximum</div>
                </div>
            </div>
            
            <div class="historique-container">
                <h3>Statistiques par exposition</h3>
                
                <?php if (count($expo_stats) > 0): ?>
                    <?php 
                    // Trouver le maximum de visiteurs pour calculer les pourcentages
                    $max_visiteurs = max(array_column($expo_stats, 'nb_visiteurs'));
                    ?>
                    
                    <?php foreach ($expo_stats as $expo): ?>
                        <div class="expo-stat-item">
                            <div class="expo-stat-title">
                                <div class="expo-stat-name"><?= htmlspecialchars($expo['titre']) ?></div>
                                <div class="expo-stat-value"><?= $expo['nb_visiteurs'] ?> visiteurs</div>
                            </div>
                            <div class="expo-stat-bar">
                                <div class="expo-stat-fill" style="width: <?= ($expo['nb_visiteurs'] / $max_visiteurs) * 100 ?>%"></div>
                            </div>
                            <div style="text-align: right; font-size: 0.8rem; color: var(--text-secondary); margin-top: 0.25rem;">
                                Durée moyenne: <?= round($expo['duree_moyenne']) ?> min
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="no-results">
                        <p>Aucune donnée disponible pour les expositions.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="historique-container">
            <div class="export-buttons">
                <a href="export_historique.php?format=csv<?= !empty($_SERVER['QUERY_STRING']) ? '&' . $_SERVER['QUERY_STRING'] : '' ?>" class="export-button">
                    <i class="fas fa-file-csv"></i> Exporter en CSV
                </a>
                <a href="export_historique.php?format=pdf<?= !empty($_SERVER['QUERY_STRING']) ? '&' . $_SERVER['QUERY_STRING'] : '' ?>" class="export-button">
                    <i class="fas fa-file-pdf"></i> Exporter en PDF
                </a>
            </div>

            <form action="" method="GET" class="filter-form">
                <div class="filter-group">
                    <label for="periode">Période :</label>
                    <select name="periode" id="periode">
                        <option value="tout" <?= $periode === 'tout' ? 'selected' : '' ?>>Toutes les périodes</option>
                        <option value="aujourdhui" <?= $periode === 'aujourdhui' ? 'selected' : '' ?>>Aujourd'hui</option>
                        <option value="semaine" <?= $periode === 'semaine' ? 'selected' : '' ?>>Cette semaine</option>
                        <option value="mois" <?= $periode === 'mois' ? 'selected' : '' ?>>Ce mois</option>
                    </select>
                </div>
                
                <div class="filter-group">
                    <label for="exposition">Exposition :</label>
                    <select name="exposition" id="exposition">
                        <option value="0">Toutes les expositions</option>
                        <?php foreach ($expositions as $expo): ?>
                            <option value="<?= $expo['id_exposition'] ?>" <?= $exposition === (int)$expo['id_exposition'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($expo['titre']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="filter-group">
                    <label for="search">Recherche :</label>
                    <input type="text" name="search" id="search" placeholder="Nom, prénom ou email" value="<?= htmlspecialchars($search) ?>">
                </div>
                
                <div class="filter-group" style="justify-content: flex-end;">
                    <button type="submit" class="filter-button">
                        <i class="fas fa-filter"></i> Filtrer
                    </button>
                </div>
            </form>
            
            <div class="table-responsive">
                <?php if (count($historique) > 0): ?>
                    <table>
                        <thead>
                            <tr>
                                <th>Visiteur</th>
                                <th>Email</th>
                                <th>Arrivée</th>
                                <th>Départ</th>
                                <th>Durée</th>
                                <th>Expositions</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($historique as $visite): ?>
                            <tr class="detail-row" data-id="<?= $visite['id_visiteur'] ?>">
                                <td><?= htmlspecialchars($visite['nom'] . ' ' . $visite['prenom']) ?></td>
                                <td><?= htmlspecialchars($visite['mail']) ?></td>
                                <td><?= date('d/m/Y H:i', strtotime($visite['h_arrivee'])) ?></td>
                                <td><?= date('d/m/Y H:i', strtotime($visite['h_depart'])) ?></td>
                                <td><?= $visite['duree_visite'] ?> min</td>
                                <td><?= htmlspecialchars($visite['expositions']) ?></td>
                                <td>
                                    <button class="toggle-details" data-id="<?= $visite['id_visiteur'] ?>">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="7" class="p-0">
                                    <div class="visitor-details" id="details-<?= $visite['id_visiteur'] ?>">
                                        <div class="detail-grid">
                                            <div class="detail-item">
                                                <div class="detail-label">Nom complet</div>
                                                <div class="detail-value"><?= htmlspecialchars($visite['nom'] . ' ' . $visite['prenom']) ?></div>
                                            </div>
                                            <div class="detail-item">
                                                <div class="detail-label">Âge</div>
                                                <div class="detail-value"><?= htmlspecialchars($visite['age']) ?> ans</div>
                                            </div>
                                            <div class="detail-item">
                                                <div class="detail-label">Email</div>
                                                <div class="detail-value"><?= htmlspecialchars($visite['mail']) ?></div>
                                            </div>
                                            <div class="detail-item">
                                                <div class="detail-label">Téléphone</div>
                                                <div class="detail-value"><?= htmlspecialchars($visite['tel'] ?? 'Non renseigné') ?></div>
                                            </div>
                                            <div class="detail-item">
                                                <div class="detail-label">Durée de visite</div>
                                                <div class="detail-value"><?= $visite['duree_visite'] ?> minutes</div>
                                            </div>
                                            <div class="detail-item">
                                                <div class="detail-label">Expositions visitées</div>
                                                <div class="detail-value"><?= htmlspecialchars($visite['expositions']) ?></div>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    
                    <?php if ($total_pages > 1): ?>
                        <div class="pagination">
                            <?php if ($page > 1): ?>
                                <a href="?page=<?= $page - 1 ?><?= !empty($periode) ? '&periode=' . $periode : '' ?><?= $exposition > 0 ? '&exposition=' . $exposition : '' ?><?= !empty($search) ? '&search=' . urlencode($search) : '' ?>">
                                    <i class="fas fa-chevron-left"></i> Précédent
                                </a>
                            <?php endif; ?>
                            
                            <?php
                            $start_page = max(1, $page - 2);
                            $end_page = min($total_pages, $page + 2);
                            
                            if ($start_page > 1) {
                                echo '<a href="?page=1' . (!empty($periode) ? '&periode=' . $periode : '') . ($exposition > 0 ? '&exposition=' . $exposition : '') . (!empty($search) ? '&search=' . urlencode($search) : '') . '">1</a>';
                                if ($start_page > 2) {
                                    echo '<span>...</span>';
                                }
                            }
                            
                            for ($i = $start_page; $i <= $end_page; $i++) {
                                if ($i == $page) {
                                    echo '<span class="current">' . $i . '</span>';
                                } else {
                                    echo '<a href="?page=' . $i . (!empty($periode) ? '&periode=' . $periode : '') . ($exposition > 0 ? '&exposition=' . $exposition : '') . (!empty($search) ? '&search=' . urlencode($search) : '') . '">' . $i . '</a>';
                                }
                            }