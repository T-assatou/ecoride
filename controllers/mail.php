<?php
// ============================
// Fichier : controllers/mail.php
// Rôle : Configuration PHPMailer avec Mailjet
// ============================

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// 📦 Inclure les classes PHPMailer (copiées dans /vendor/PHPMailer/)
require_once('../vendor/PHPMailer/PHPMailer.php');
require_once('../vendor/PHPMailer/SMTP.php');
require_once('../vendor/PHPMailer/Exception.php');

/**
 * Fonction générique pour envoyer un email via Mailjet
 *
 * @param string $destinataire L’adresse email du destinataire
 * @param string $sujet Sujet du message
 * @param string $contenuHTML Version HTML du message
 * @param string $contenuTexte Version texte du message (fallback)
 * @return bool
 */
function envoyerMail($destinataire, $sujet, $contenuHTML, $contenuTexte = '') {
    $mail = new PHPMailer(true);

    try {
        // Configuration Mailjet SMTP
        $mail->isSMTP();
        $mail->Host = 'in-v3.***REMOVED***.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'f7ad8c333031b5c79c362b5e11044b42'; //  Ton API key publique
        $mail->Password = 'f974446c2661bbb4a748199783e77e6a'; //  API key secrète
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;

        $mail->setFrom('***REMOVED***', 'EcoRide');
        $mail->addAddress($destinataire);

        $mail->isHTML(true);
        $mail->Subject = $sujet;
        $mail->Body    = $contenuHTML;
        $mail->AltBody = $contenuTexte ?: strip_tags($contenuHTML);

        $mail->send();
        return true;
    } catch (Exception $e) {
        // Tu peux logger l’erreur si besoin : $mail->ErrorInfo
        return false;
    }
}