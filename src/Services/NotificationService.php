<?php
// src/Services/NotificationService.php

class NotificationService {
    private $logFile;

    public function __construct() {
        $this->logFile = __DIR__ . '/../../logs/emails.log';
        $logDir = dirname($this->logFile);
        if (!is_dir($logDir)) {
            mkdir($logDir, 0777, true);
        }
    }

    /**
     * Envoie un email de confirmation de réservation (US11)
     */
    public function sendConfirmationEmail($recipientEmail, $recipientName, $reservationDetails) {
        $subject = "Confirmation de réservation - " . $reservationDetails['titre_evenement'];
        $body = $this->buildConfirmationTemplate($recipientName, $reservationDetails);
        
        return $this->sendEmail($recipientEmail, $subject, $body, 'confirmation');
    }

    /**
     * Envoie un email de refus de réservation (US12)
     */
    public function sendRefusalEmail($recipientEmail, $recipientName, $reservationDetails, $reason = null) {
        $subject = "Refus de demande de réservation - " . $reservationDetails['titre_evenement'];
        $body = $this->buildRefusalTemplate($recipientName, $reservationDetails, $reason);
        
        return $this->sendEmail($recipientEmail, $subject, $body, 'refusal');
    }

    /**
     * Envoie un email de notification lors d'un changement de statut (US13)
     */
    public function sendStatusChangeEmail($recipientEmail, $recipientName, $reservationDetails, $oldStatus, $newStatus) {
        $subject = "Mise à jour du statut de votre réservation #" . $reservationDetails['id'];
        $body = $this->buildStatusChangeTemplate($recipientName, $reservationDetails, $oldStatus, $newStatus);
        
        return $this->sendEmail($recipientEmail, $subject, $body, 'status_change');
    }

    /**
     * Méthode générique d'envoi d'email (mail PHP + simulation/logging pour dev)
     */
    private function sendEmail($to, $subject, $htmlBody, $type) {
        // En-têtes pour email HTML
        $headers = [
            'MIME-Version: 1.0',
            'Content-type: text/html; charset=utf-8',
            'From: Application Gestion Salles <no-reply@reservation-salles.univ.fr>',
            'Reply-To: support@reservation-salles.univ.fr',
            'X-Mailer: PHP/' . phpversion()
        ];

        // Tentative d'envoi via mail() natif (silencieux si le serveur SMTP local n'est pas configuré)
        $mailSent = @mail($to, $subject, $htmlBody, implode("\r\n", $headers));

        // Journalisation systématique des notifications (US11, US12, US13) dans les logs
        $logEntry = sprintf(
            "[%s] [TYPE: %s] TO: %s | SUBJECT: %s | STATUS: %s\n",
            date('Y-m-d H:i:s'),
            strtoupper($type),
            $to,
            $subject,
            $mailSent ? 'SENT' : 'LOGGED (DEV MODE)'
        );
        file_put_contents($this->logFile, $logEntry, FILE_APPEND);

        return true;
    }

    /**
     * Template HTML pour l'email de confirmation (US11)
     */
    private function buildConfirmationTemplate($name, $res) {
        $dateDebut = date('d/m/Y H:i', strtotime($res['date_debut']));
        $dateFin = date('d/m/Y H:i', strtotime($res['date_fin']));
        
        return "
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset='UTF-8'>
            <style>
                body { font-family: 'Segoe UI', Arial, sans-serif; background-color: #f8f9fa; margin: 0; padding: 20px; color: #333; }
                .card { max-width: 600px; margin: 0 auto; background: #ffffff; border-radius: 10px; overflow: hidden; box-shadow: 0 4px 10px rgba(0,0,0,0.05); }
                .header { background-color: #198754; color: white; padding: 25px; text-align: center; }
                .content { padding: 30px; }
                .details-box { background-color: #f0fdf4; border-left: 4px solid #198754; padding: 15px; margin: 20px 0; border-radius: 4px; }
                .footer { background-color: #f1f5f9; padding: 15px; text-align: center; font-size: 12px; color: #64748b; }
                .badge { display: inline-block; padding: 6px 12px; background-color: #198754; color: white; border-radius: 20px; font-weight: bold; font-size: 13px; }
            </style>
        </head>
        <body>
            <div class='card'>
                <div class='header'>
                    <h2>✓ Réservation Confirmée</h2>
                </div>
                <div class='content'>
                    <p>Bonjour <strong>" . htmlspecialchars($name) . "</strong>,</p>
                    <p>Votre demande de réservation a été <strong>validée avec succès</strong>. Le créneau vous a été attribué.</p>
                    
                    <div class='details-box'>
                        <h4 style='margin-top:0; color:#198754;'>Détails de la réservation #" . (int)$res['id'] . "</h4>
                        <p><strong>Motif / Titre :</strong> " . htmlspecialchars($res['titre_evenement']) . "</p>
                        <p><strong>Salle :</strong> " . htmlspecialchars($res['salle_nom'] ?? 'Salle réservée') . "</p>
                        <p><strong>Début :</strong> " . $dateDebut . "</p>
                        <p><strong>Fin :</strong> " . $dateFin . "</p>
                        <p><strong>Statut :</strong> <span class='badge'>Validée</span></p>
                    </div>

                    <p>Vous pouvez consulter à tout moment l'ensemble de vos réservations depuis votre espace personnel.</p>
                </div>
                <div class='footer'>
                    Application de Gestion et Réservation de Salles &copy; " . date('Y') . "
                </div>
            </div>
        </body>
        </html>";
    }

    /**
     * Template HTML pour l'email de refus (US12)
     */
    private function buildRefusalTemplate($name, $res, $reason) {
        $dateDebut = date('d/m/Y H:i', strtotime($res['date_debut']));
        $dateFin = date('d/m/Y H:i', strtotime($res['date_fin']));
        $reasonText = $reason ? htmlspecialchars($reason) : "Créneau indisponible ou contrainte logistique.";

        return "
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset='UTF-8'>
            <style>
                body { font-family: 'Segoe UI', Arial, sans-serif; background-color: #f8f9fa; margin: 0; padding: 20px; color: #333; }
                .card { max-width: 600px; margin: 0 auto; background: #ffffff; border-radius: 10px; overflow: hidden; box-shadow: 0 4px 10px rgba(0,0,0,0.05); }
                .header { background-color: #dc3545; color: white; padding: 25px; text-align: center; }
                .content { padding: 30px; }
                .details-box { background-color: #fef2f2; border-left: 4px solid #dc3545; padding: 15px; margin: 20px 0; border-radius: 4px; }
                .footer { background-color: #f1f5f9; padding: 15px; text-align: center; font-size: 12px; color: #64748b; }
                .badge { display: inline-block; padding: 6px 12px; background-color: #dc3545; color: white; border-radius: 20px; font-weight: bold; font-size: 13px; }
            </style>
        </head>
        <body>
            <div class='card'>
                <div class='header'>
                    <h2>✕ Réservation Non Accordée</h2>
                </div>
                <div class='content'>
                    <p>Bonjour <strong>" . htmlspecialchars($name) . "</strong>,</p>
                    <p>Nous sommes désolés de vous informer que votre demande de réservation n'a <strong>pas pu être acceptée</strong>.</p>
                    
                    <div class='details-box'>
                        <h4 style='margin-top:0; color:#dc3545;'>Rappel de la demande #" . (int)$res['id'] . "</h4>
                        <p><strong>Motif / Titre :</strong> " . htmlspecialchars($res['titre_evenement']) . "</p>
                        <p><strong>Salle :</strong> " . htmlspecialchars($res['salle_nom'] ?? 'Salle') . "</p>
                        <p><strong>Créneau :</strong> Du " . $dateDebut . " au " . $dateFin . "</p>
                        <p><strong>Statut :</strong> <span class='badge'>Refusée</span></p>
                        <p><strong>Motif du refus :</strong> " . $reasonText . "</p>
                    </div>

                    <p>N'hésitez pas à soumettre une nouvelle demande pour une autre salle ou un créneau différent.</p>
                </div>
                <div class='footer'>
                    Application de Gestion et Réservation de Salles &copy; " . date('Y') . "
                </div>
            </div>
        </body>
        </html>";
    }

    /**
     * Template HTML pour l'email de notification de changement de statut (US13)
     */
    private function buildStatusChangeTemplate($name, $res, $oldStatus, $newStatus) {
        $dateDebut = date('d/m/Y H:i', strtotime($res['date_debut']));
        $dateFin = date('d/m/Y H:i', strtotime($res['date_fin']));
        
        $statusLabels = [
            'en_attente' => 'En attente',
            'validee' => 'Validée',
            'refusee' => 'Refusée / Annulée'
        ];

        $oldLabel = $statusLabels[$oldStatus] ?? $oldStatus;
        $newLabel = $statusLabels[$newStatus] ?? $newStatus;

        return "
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset='UTF-8'>
            <style>
                body { font-family: 'Segoe UI', Arial, sans-serif; background-color: #f8f9fa; margin: 0; padding: 20px; color: #333; }
                .card { max-width: 600px; margin: 0 auto; background: #ffffff; border-radius: 10px; overflow: hidden; box-shadow: 0 4px 10px rgba(0,0,0,0.05); }
                .header { background-color: #0d6efd; color: white; padding: 25px; text-align: center; }
                .content { padding: 30px; }
                .details-box { background-color: #eff6ff; border-left: 4px solid #0d6efd; padding: 15px; margin: 20px 0; border-radius: 4px; }
                .footer { background-color: #f1f5f9; padding: 15px; text-align: center; font-size: 12px; color: #64748b; }
            </style>
        </head>
        <body>
            <div class='card'>
                <div class='header'>
                    <h2>ℹ Mise à Jour de Statut</h2>
                </div>
                <div class='content'>
                    <p>Bonjour <strong>" . htmlspecialchars($name) . "</strong>,</p>
                    <p>Le statut de votre réservation #" . (int)$res['id'] . " a été mis à jour.</p>
                    
                    <div class='details-box'>
                        <h4 style='margin-top:0; color:#0d6efd;'>Réservation #" . (int)$res['id'] . "</h4>
                        <p><strong>Événement :</strong> " . htmlspecialchars($res['titre_evenement']) . "</p>
                        <p><strong>Salle :</strong> " . htmlspecialchars($res['salle_nom'] ?? 'Salle') . "</p>
                        <p><strong>Créneau :</strong> Du " . $dateDebut . " au " . $dateFin . "</p>
                        <hr style='border:0; border-top:1px solid #cbd5e1; margin:10px 0;'>
                        <p><strong>Ancien statut :</strong> " . htmlspecialchars($oldLabel) . "</p>
                        <p><strong>Nouveau statut :</strong> <strong style='color:#0d6efd;'>" . htmlspecialchars($newLabel) . "</strong></p>
                    </div>

                    <p>Vous pouvez suivre le détail de vos réservations sur l'application web.</p>
                </div>
                <div class='footer'>
                    Application de Gestion et Réservation de Salles &copy; " . date('Y') . "
                </div>
            </div>
        </body>
        </html>";
    }
}
?>
