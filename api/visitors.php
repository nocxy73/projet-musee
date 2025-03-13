<?php
// api/visitors.php - API pour la gestion des visiteurs

// Inclure le fichier de configuration
require_once '../config.php';

// Définir le type de contenu comme JSON
header('Content-Type: application/json');

// Activer CORS pour permettre l'accès depuis votre front-end
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type');

// Fonction pour générer une réponse JSON
function sendResponse($status, $message, $data = null) {
    $response = [
        'status' => $status,
        'message' => $message
    ];
    
    if ($data !== null) {
        $response['data'] = $data;
    }
    
    echo json_encode($response);
    exit;
}

// Récupérer la méthode HTTP utilisée
$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        // Récupérer la liste des visiteurs actifs
        $sql = "SELECT v.*, vt.id_exposition, e.libelle as exposition_nom, t.libelle as ticket_nom, vt.h_arrivee, vt.h_depart
                FROM visiteur v
                JOIN visiter vt ON v.id_visiteur = vt.id_visiteur
                JOIN exposition e ON vt.id_exposition = e.id_exposition
                JOIN acheter a ON v.id_visiteur = a.id_visiteur
                JOIN type_ticket t ON a.id_ticket = t.id_ticket
                WHERE vt.h_depart IS NULL
                ORDER BY vt.h_arrivee DESC";
                
        $result = mysqli_query($conn, $sql);
        
        if ($result) {
            $visitors = [];
            while ($row = mysqli_fetch_assoc($result)) {
                $visitors[] = $row;
            }
            sendResponse('success', 'Liste des visiteurs récupérée avec succès', $visitors);
        } else {
            sendResponse('error', 'Erreur lors de la récupération des visiteurs: ' . mysqli_error($conn));
        }
        break;
        
    case 'POST':
        // Récupérer les données envoyées
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (!isset($data['ticketType'])) {
            sendResponse('error', 'Type de billet manquant');
        }
        
        // Commencer une transaction
        mysqli_begin_transaction($conn);
        
        try {
            // 1. Insérer le visiteur
            $sql = "INSERT INTO visiteur (nom, prenom, age, tel, mail) VALUES (?, ?, ?, ?, ?)";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "ssiss", 
                $data['nom'] ?? '', 
                $data['prenom'] ?? '', 
                $data['age'] ?? 0, 
                $data['tel'] ?? '', 
                $data['mail'] ?? ''
            );
            mysqli_stmt_execute($stmt);
            $visitorId = mysqli_insert_id($conn);
            
            // 2. Déterminer le type de billet
            $ticketType = $data['ticketType'];
            $ticketId = 0;
            
            switch ($ticketType) {
                case 'permanent':
                    $ticketId = 1;
                    $expositions = [1]; // Exposition permanente
                    break;
                case 'temporary':
                    $ticketId = 2;
                    $expositions = [2]; // Exposition temporaire
                    break;
                case 'both':
                    $ticketId = 3;
                    $expositions = [1, 2]; // Les deux expositions
                    break;
                default:
                    throw new Exception("Type de billet invalide");
            }
            
            // 3. Enregistrer l'achat du billet
            $now = date('Y-m-d H:i:s');
            $sql = "INSERT INTO acheter (id_visiteur, id_ticket, date_achat) VALUES (?, ?, ?)";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "iis", $visitorId, $ticketId, $now);
            mysqli_stmt_execute($stmt);
            
            // 4. Enregistrer les entrées pour chaque exposition
            foreach ($expositions as $expoId) {
                $sql = "INSERT INTO visiter (id_visiteur, id_exposition, h_arrivee) VALUES (?, ?, ?)";
                $stmt = mysqli_prepare($conn, $sql);
                mysqli_stmt_bind_param($stmt, "iis", $visitorId, $expoId, $now);
                mysqli_stmt_execute($stmt);
            }
            
            // Valider la transaction
            mysqli_commit($conn);
            
            // Envoyer la réponse
            sendResponse('success', 'Visiteur enregistré avec succès', [
                'id' => $visitorId,
                'customId' => 'V-' . $visitorId,
                'ticketType' => $ticketType,
                'entryTime' => $now
            ]);
            
        } catch (Exception $e) {
            // Annuler la transaction en cas d'erreur
            mysqli_rollback($conn);
            sendResponse('error', 'Erreur lors de l\'enregistrement: ' . $e->getMessage());
        }
        break;
        
    case 'PUT':
        // Enregistrer la sortie d'un visiteur
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (!isset($data['id'])) {
            sendResponse('error', 'ID du visiteur manquant');
        }
        
        $visitorId = $data['id'];
        $now = date('Y-m-d H:i:s');
        
        // Mettre à jour l'heure de sortie
        $sql = "UPDATE visiter SET h_depart = ? WHERE id_visiteur = ? AND h_depart IS NULL";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "si", $now, $visitorId);
        
        if (mysqli_stmt_execute($stmt)) {
            sendResponse('success', 'Sortie enregistrée avec succès');
        } else {
            sendResponse('error', 'Erreur lors de l\'enregistrement de la sortie: ' . mysqli_error($conn));
        }
        break;
        
    case 'DELETE':
        // Non implémenté
        sendResponse('error', 'Méthode non prise en charge');
        break;
        
    default:
        sendResponse('error', 'Méthode non prise en charge');
        break;
}