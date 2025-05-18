<?php
// ============================
// Fichier : models/ride.php
// Rôle : Fonctions liées aux trajets (rides)
// ============================

// Récupère les trajets créés par un utilisateur
function getRidesByUser($pdo, $user_id) {
    $stmt = $pdo->prepare("SELECT * FROM rides WHERE user_id = :id ORDER BY date_depart");
    $stmt->execute([':id' => $user_id]);
    return $stmt->fetchAll();
}

// Récupère les participants d’un trajet
function getParticipantsByRide($pdo, $ride_id) {
    $stmt = $pdo->prepare("SELECT users.pseudo FROM participants 
                           INNER JOIN users ON participants.user_id = users.id 
                           WHERE participants.ride_id = :ride_id");
    $stmt->execute([':ride_id' => $ride_id]);
    return $stmt->fetchAll();
}

// Recherche un trajet
function searchRides($pdo, $depart, $arrivee, $date_depart) {
    $stmt = $pdo->prepare("SELECT rides.*, vehicles.marque, vehicles.modele 
                           FROM rides 
                           INNER JOIN vehicles ON rides.vehicle_id = vehicles.id 
                           WHERE rides.depart = :depart 
                           AND rides.arrivee = :arrivee 
                           AND DATE(rides.date_depart) = :date_depart");

    $stmt->execute([
        ':depart' => $depart,
        ':arrivee' => $arrivee,
        ':date_depart' => $date_depart
    ]);

    return $stmt->fetchAll();
}