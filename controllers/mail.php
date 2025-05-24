<?php
// ============================
// Fichier : controllers/mail.php
// Rôle : Envoi d'e-mail avec PHPMailer + Mailjet
// ============================

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// ✅ Chargement automatique avec Composer
require_once __DIR__ . '/../vendor/autoload.php';

/**
 * Envoie un e-mail via PHPMailer avec Mailjet
 *
 * @param string $destinataire Email du destinataire
 * @param string $sujet Sujet du message
 * @param string $contenuHTML Corps HTML du mail
 * @param string $contenuTexte Corps texte brut (optionnel)
 * @return bool
 */
function envoyerMail($destinataire, $sujet, $contenuHTML, $contenuTexte = '') {
    $mail = new PHPMailer(true);

    try {
        // Configuration du serveur SMTP Mailjet
        $mail->isSMTP();
        $mail->Host       = 'in-v3.***REMOVED***.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = '***REMOVED***'; // API publique Mailjet
        $mail->Password   = '***REMOVED***'; //  API privée Mailjet
        $mail->SMTPSecure = 'tls';
        $mail->Port       = 587;

        // Émetteur et destinataire
        $mail->setFrom('***REMOVED***', 'EcoRide');
        $mail->addAddress($destinataire);

        // Contenu
        $mail->isHTML(true);
        $mail->Subject = $sujet;
        $mail->Body    = $contenuHTML;
        $mail->AltBody = $contenuTexte ?: strip_tags($contenuHTML);

        $mail->send();
        return true;
    } catch (Exception $e) {
    echo "Mailer Error: " . $mail->ErrorInfo;
    return false;
}
}