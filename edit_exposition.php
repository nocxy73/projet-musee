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

// Vérifier si les colonnes titre, date_debut et date_fin existent
$check_columns = $pdo->query("SHOW COLUMNS FROM Exposition WHERE Field IN ('titre', 'date_debut', 'date_fin')");
$has_all_columns = $check_columns->rowCount() == 3;

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
$stmt = $pdo->prepare("SELECT titre, description, date_debut, date_fin" . ($has_image_column ? ", image" : "") . " FROM Exposition WHERE titre = 'Exposition Temporaire'");
$stmt->execute();
$exposition = $stmt->fetch(PDO::FETCH_ASSOC);

// Si aucun résultat n'est trouvé, initialiser avec des valeurs par défaut
if ($exposition === false) {
    $exposition = [
        'titre' => 'Exposition Temporaire',
        'description' => 'Une nouvelle exposition fascinante',
        'date_debut' => date('Y-m-d'),
        'date_fin' => date('Y-m-d', strtotime('+3 months')),
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
    try {
        $pdo->beginTransaction();
        
        // Validation des données
        $titre = validateInput($_POST['titre']);
        $description = validateInput($_POST['description']);
        $date_debut = $_POST['date_debut'];
        $date_fin = $_POST['date_fin'];
        
        // Validation des dates
        if (!empty($date_debut) && !empty($date_fin)) {
            $date_debut_obj = new DateTime($date_debut);
            $date_fin_obj = new DateTime($date_fin);
            
            if ($date_fin_obj < $date_debut_obj) {
                throw new Exception("La date de fin doit être ultérieure à la date de début");
            }
        }
        
        // Vérifier si une nouvelle image a été téléchargée
        if ($_FILES['image']['size'] > 0) {
            // Vérifier le type du fichier
            $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
            $file_type = $_FILES['image']['type'];
            
            if (!in_array($file_type, $allowed_types)) {
                throw new Exception("Format d'image non supporté. Veuillez utiliser JPG, PNG ou GIF.");
            }
            
            // Générer un nom de fichier unique
            $image_extension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
            $image_name = 'exposition_' . time() . '.' . $image_extension;
            
            // Créer le dossier images s'il n'existe pas
            $imageDirPath = "images/";
            if (!file_exists($imageDirPath)) {
                mkdir($imageDirPath, 0777, true);
            }
            
            // Déplacer l'image téléchargée
            if (!move_uploaded_file($_FILES['image']['tmp_name'], $imageDirPath . $image_name)) {
                throw new Exception("Erreur lors du téléchargement de l'image.");
            }
            
            // Supprimer l'ancienne image si elle est différente de l'image par défaut
            if ($exposition['image'] != 'musee temps.jpg' && file_exists($imageDirPath . $exposition['image'])) {
                unlink($imageDirPath . $exposition['image']);
            }
            
            $image = $image_name;
        } else {
            // Conserver l'image existante si aucune nouvelle image n'est fournie
            $image = $exposition['image'];
        }
        
        // Mettre à jour la base de données
        if ($has_image_column) {
            $stmt = $pdo->prepare("UPDATE Exposition SET titre = :titre, description = :description, date_debut = :date_debut, date_fin = :date_fin, image = :image WHERE titre = 'Exposition Temporaire'");
            $stmt->execute([
                'titre' => $titre,
                'description' => $description,
                'date_debut' => $date_debut,
                'date_fin' => $date_fin,
                'image' => $image
            ]);
        } else {
            $stmt = $pdo->prepare("UPDATE Exposition SET titre = :titre, description = :description, date_debut = :date_debut, date_fin = :date_fin WHERE titre = 'Exposition Temporaire'");
            $stmt->execute([
                'titre' => $titre,
                'description' => $description,
                'date_debut' => $date_debut,
                'date_fin' => $date_fin
            ]);
        }
        
        $pdo->commit();
        $success_message = "Exposition mise à jour avec succès !";
        
        // Rafraîchir les données de l'exposition après la mise à jour
        $stmt = $pdo->prepare("SELECT titre, description, date_debut, date_fin" . ($has_image_column ? ", image" : "") . " FROM Exposition WHERE titre = :titre");
        $stmt->execute(['titre' => $titre]);
        $exposition = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!isset($exposition['image'])) {
            $exposition['image'] = $image;
        }
        
    } catch (Exception $e) {
        $pdo->rollBack();
        $error_message = "Erreur : " . htmlspecialchars($e->getMessage());
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
            max-width: 800px;
            margin: 20px auto;
            background-color: #2a2a2a;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        
        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }
        
        .form-full-width {
            grid-column: span 2;
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
        
        input[type="text"],
        input[type="date"] {
            width: 100%;
            padding: 10px;
            border: 1px solid #444;
            border-radius: 4px;
            background-color: #333;
            color: #f1f5f9;
        }
        
        input[type="file"] {
            background-color: #333;
            padding: 10px;
            border-radius: 4px;
            width: 100%;
            color: #f1f5f9;
        }
        
        .preview-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin-top: 20px;
            background: rgba(0, 0, 0, 0.2);
            padding: 15px;
            border-radius: 8px;
        }
        
        .current-image {
            margin-top: 10px;
            max-width: 100%;
            height: auto;
            border-radius: 4px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
        }
        
        .image-info {
            margin-top: 10px;
            text-align: center;
            font-size: 0.9em;
            color: #ccc;
        }
        
        button {
            background-color: #6366f1;
            color: white;
            border: none;
            padding: 12px 20px;
            border-radius: 4px;
            cursor: pointer;
            font-weight: bold;
            font-size: 1em;
            transition: all 0.3s ease;
            width: 100%;
        }
        
        button:hover {
            background-color: #4f46e5;
            transform: translateY(-2px);
        }
        
        .success-message {
            background-color: #10b981;
            color: white;
            padding: 15px;
            border-radius: 4px;
            margin-bottom: 20px;
            text-align: center;
            animation: fadeIn 0.5s ease-out;
        }
        
        .error-message {
            background-color: #ef4444;
            color: white;
            padding: 15px;
            border-radius: 4px;
            margin-bottom: 20px;
            text-align: center;
            animation: fadeIn 0.5s ease-out;
        }
        
        .back-link {
            display: inline-flex;
            align-items: center;
            margin-top: 20px;
            color: #6366f1;
            text-decoration: none;
            font-weight: bold;
            transition: all 0.3s ease;
        }
        
        .back-link i {
            margin-right: 5px;
        }
        
        .back-link:hover {
            color: #4f46e5;
            transform: translateX(-5px);
        }
        
        .date-info {
            font-size: 0.85em;
            color: #aaa;
            margin-top: 5px;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        /* Styles pour prévisualiser l'exposition */
        .preview-box {
            background: rgba(255, 255, 255, 0.05);
            border-radius: 8px;
            padding: 20px;
            margin-top: 30px;
        }
        
        .preview-title {
            font-size: 1.3em;
            margin-bottom: 15px;
            color: #f1f5f9;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            padding-bottom: 10px;
        }
        
        .preview-dates {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
            font-size: 0.9em;
            color: #ccc;
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
        <?php if (isset($success_message)): ?>
            <div class="success-message">
                <i class="fas fa-check-circle"></i> <?php echo $success_message; ?>
            </div>
        <?php endif; ?>
        
        <?php if (isset($error_message)): ?>
            <div class="error-message">
                <i class="fas fa-exclamation-triangle"></i> <?php echo $error_message; ?>
            </div>
        <?php endif; ?>
        
        <h2>Modifier l'Exposition Temporaire</h2>
        
        <form method="POST" enctype="multipart/form-data">
            <div class="form-grid">
                <div class="form-group">
                    <label for="titre">Titre de l'exposition :</label>
                    <input type="text" name="titre" id="titre" value="<?php echo htmlspecialchars($exposition['titre'] ?? 'Exposition Temporaire'); ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="date_debut">Date de début :</label>
                    <input type="date" name="date_debut" id="date_debut" value="<?php echo htmlspecialchars($exposition['date_debut'] ?? date('Y-m-d')); ?>" required>
                    <p class="date-info">Date à laquelle l'exposition commence</p>
                </div>
                
                <div class="form-group form-full-width">
                    <label for="description">Description :</label>
                    <textarea name="description" id="description" required><?php echo htmlspecialchars($exposition['description'] ?? ''); ?></textarea>
                </div>
                
                <div class="form-group">
                    <label for="image">Nouvelle image :</label>
                    <input type="file" name="image" id="image" accept="image/*">
                    <p class="date-info">Laissez vide pour conserver l'image actuelle</p>
                </div>
                
                <div class="form-group">
                    <label for="date_fin">Date de fin :</label>
                    <input type="date" name="date_fin" id="date_fin" value="<?php echo htmlspecialchars($exposition['date_fin'] ?? date('Y-m-d', strtotime('+3 months'))); ?>" required>
                    <p class="date-info">Date à laquelle l'exposition se termine</p>
                </div>
            </div>
            
            <div class="preview-container">
                <h3>Image actuelle</h3>
                <?php if (!empty($exposition['image'])): ?>
                    <div>
                        <img src="images/<?php echo htmlspecialchars($exposition['image']); ?>" alt="Image actuelle de l'exposition" class="current-image">
                        <p class="image-info">Nom du fichier : <?php echo htmlspecialchars($exposition['image']); ?></p>
                    </div>
                <?php else: ?>
                    <p>Aucune image actuellement</p>
                <?php endif; ?>
            </div>
            
            <div class="preview-box">
                <h3 class="preview-title">Prévisualisation</h3>
                <div class="preview-dates">
                    <span>Du : <?php echo date('d/m/Y', strtotime($exposition['date_debut'])); ?></span>
                    <span>Au : <?php echo date('d/m/Y', strtotime($exposition['date_fin'])); ?></span>
                </div>
                <p><?php echo nl2br(htmlspecialchars($exposition['description'] ?? '')); ?></p>
            </div>
            
            <button type="submit">
                <i class="fas fa-save"></i> Mettre à jour l'Exposition
            </button>
        </form>
        
        <a href="index.php" class="back-link">
            <i class="fas fa-arrow-left"></i> Retour au tableau de bord
        </a>
    </div>
    
    <script>
    // Petit script pour prévisualiser la description et les dates en temps réel
    document.addEventListener('DOMContentLoaded', function() {
        const descriptionInput = document.getElementById('description');
        const titreInput = document.getElementById('titre');
        const dateDebutInput = document.getElementById('date_debut');
        const dateFinInput = document.getElementById('date_fin');
        
        const previewTitle = document.querySelector('.preview-title');
        const previewDates = document.querySelector('.preview-dates');
        const previewDescription = document.querySelector('.preview-box p');
        
        function updatePreview() {
            // Mise à jour du titre
            previewTitle.textContent = titreInput.value || 'Prévisualisation';
            
            // Mise à jour des dates
            const dateDebut = dateDebutInput.value ? new Date(dateDebutInput.value) : new Date();
            const dateFin = dateFinInput.value ? new Date(dateFinInput.value) : new Date();
            
            const formatDate = (date) => {
                return date.toLocaleDateString('fr-FR');
            };
            
            previewDates.innerHTML = `<span>Du : ${formatDate(dateDebut)}</span><span>Au : ${formatDate(dateFin)}</span>`;
            
            // Mise à jour de la description
            previewDescription.innerHTML = descriptionInput.value.replace(/\n/g, '<br>');
        }
        
        // Écouter les changements
        descriptionInput.addEventListener('input', updatePreview);
        titreInput.addEventListener('input', updatePreview);
        dateDebutInput.addEventListener('change', updatePreview);
        dateFinInput.addEventListener('change', updatePreview);
    });
    </script>
</body>
</html>
