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

// Vérifier si la colonne image existe
$check_column = $pdo->query("SHOW COLUMNS FROM Exposition LIKE 'image'");
$has_image_column = $check_column->rowCount() > 0;

// Si la colonne n'existe pas, l'ajouter
if (!$has_image_column) {
    try {
        $pdo->exec("ALTER TABLE Exposition ADD COLUMN image VARCHAR(255)");
        $has_image_column = true;
    } catch (PDOException $e) {
        die("Erreur lors de l'ajout de la colonne image : " . $e->getMessage());
    }
}

// Vérifier si l'exposition temporaire existe
$check_expo = $pdo->prepare("SELECT COUNT(*) as count FROM Exposition WHERE titre = 'Exposition Temporaire'");
$check_expo->execute();
$result = $check_expo->fetch(PDO::FETCH_ASSOC);

if ($result['count'] == 0) {
    // L'exposition n'existe pas, l'ajouter
    $insert_expo = $pdo->prepare("INSERT INTO Exposition (titre, description, date_debut, date_fin, image) VALUES ('Exposition Temporaire', 'Échos du Temps', CURDATE(), DATE_ADD(CURDATE(), INTERVAL 3 MONTH), 'musee temps.jpg')");
    $insert_expo->execute();
}

// Récupérer les informations actuelles de l'exposition temporaire
$stmt = $pdo->prepare("SELECT description" . ($has_image_column ? ", image" : "") . " FROM Exposition WHERE titre = 'Exposition Temporaire'");
$stmt->execute();
$exposition = $stmt->fetch(PDO::FETCH_ASSOC);

// Si aucun résultat n'est trouvé, initialiser avec des valeurs par défaut
if ($exposition === false) {
    $exposition = [
        'description' => '',
        'image' => 'musee temps.jpg'
    ];
} else if (!isset($exposition['image']) && $has_image_column) {
    // Mettre à jour avec une image par défaut si la colonne existe mais est NULL
    $update = $pdo->prepare("UPDATE Exposition SET image = 'musee temps.jpg' WHERE titre = 'Exposition Temporaire'");
    $update->execute();
    $exposition['image'] = 'musee temps.jpg';
} else if (!isset($exposition['image'])) {
    // Si la colonne n'existe pas dans le résultat
    $exposition['image'] = 'musee temps.jpg';
}

// Gestion des actions de mise à jour
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $description = validateInput($_POST['description']);
    
    // Vérifier si une nouvelle image a été téléchargée
    if ($_FILES['image']['size'] > 0) {
        $image = $_FILES['image']['name'];
        
        // Créer le dossier images s'il n'existe pas
        $imageDirPath = "images/";
        if (!file_exists($imageDirPath)) {
            mkdir($imageDirPath, 0777, true);
        }
        
        // Déplacer l'image téléchargée
        move_uploaded_file($_FILES['image']['tmp_name'], $imageDirPath . $image);
    } else {
        // Conserver l'image existante si aucune nouvelle image n'est fournie
        $image = $exposition['image'];
    }
    
    // Mettre à jour la base de données avec ou sans la colonne image
    if ($has_image_column) {
        $stmt = $pdo->prepare("UPDATE Exposition SET description = :description, image = :image WHERE titre = 'Exposition Temporaire'");
        $stmt->execute(['description' => $description, 'image' => $image]);
    } else {
        $stmt = $pdo->prepare("UPDATE Exposition SET description = :description WHERE titre = 'Exposition Temporaire'");
        $stmt->execute(['description' => $description]);
    }
    
    echo "<div class='success-message'>Exposition mise à jour avec succès !</div>";
    
    // Rafraîchir les données de l'exposition après la mise à jour
    $stmt = $pdo->prepare("SELECT description" . ($has_image_column ? ", image" : "") . " FROM Exposition WHERE titre = 'Exposition Temporaire'");
    $stmt->execute();
    $exposition = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!isset($exposition['image'])) {
        $exposition['image'] = $image;
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier l'Exposition Temporaire</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="styles.css" rel="stylesheet">
    <style>
        .form-container {
            max-width: 600px;
            margin: 20px auto;
            background-color: #2a2a2a;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        
        .form-group {
            margin-bottom: 15px;
        }
        
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        
        textarea {
            width: 100%;
            min-height: 150px;
            padding: 10px;
            border: 1px solid #444;
            border-radius: 4px;
            background-color: #333;
            color: #f1f5f9;
            font-family: inherit;
        }
        
        input[type="file"] {
            background-color: #333;
            padding: 10px;
            border-radius: 4px;
            width: 100%;
            color: #f1f5f9;
        }
        
        .current-image {
            margin-top: 10px;
            max-width: 300px;
            border-radius: 4px;
        }
        
        button {
            background-color: #6366f1;
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 4px;
            cursor: pointer;
            font-weight: bold;
            margin-top: 10px;
        }
        
        button:hover {
            background-color: #4f46e5;
        }
        
        .success-message {
            background-color: #10b981;
            color: white;
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 15px;
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
    </style>
</head>
<body>
    <header class="header">
        <h1 class="main-title">
            <span class="icon">🏛️</span> Système de Gestion du Musée
        </h1>
        <p class="subtitle">Modifier l'Exposition Temporaire</p>
    </header>

    <div class="form-container">
        <h2>Modifier l'Exposition Temporaire</h2>
        
        <form method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="description">Description :</label>
                <textarea name="description" id="description" required><?php echo htmlspecialchars($exposition['description'] ?? ''); ?></textarea>
            </div>
            
            <div class="form-group">
                <label for="image">Image actuelle :</label>
                <?php if (!empty($exposition['image'])): ?>
                    <div>
                        <img src="images/<?php echo htmlspecialchars($exposition['image']); ?>" alt="Image actuelle de l'exposition" class="current-image">
                        <p>Nom du fichier : <?php echo htmlspecialchars($exposition['image']); ?></p>
                    </div>
                <?php else: ?>
                    <p>Aucune image actuellement</p>
                <?php endif; ?>
            </div>
            
            <div class="form-group">
                <label for="image">Nouvelle image (laisser vide pour conserver l'image actuelle) :</label>
                <input type="file" name="image" id="image" accept="image/*">
            </div>
            
            <button type="submit">Mettre à jour l'Exposition</button>
        </form>
        
        <a href="index.php" class="back-link">Retour au tableau de bord</a>
    </div>
</body>
</html>