<?php

session_start();
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);


include("connexion.php"); // Inclure le fichier de connexion

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $pseudo = $_POST['pseudo'];
    $email = $_POST['email'];
    $motDePasse = $_POST['mot_de_passe'];
    $confirmationMotDePasse = $_POST['confirmation_mot_de_passe'];

    $_SESSION['email'] = $email; // Définissez l'adresse e-mail dans la session

    // Vérification que le mot de passe et la confirmation du mot de passe correspondent    
    if ($motDePasse !== $confirmationMotDePasse) {
        echo "Les mots de passe ne correspondent pas.";
    } elseif (strlen($motDePasse) >= 8 && preg_match('/[A-Z]/', $motDePasse) && preg_match('/[^a-zA-Z0-9]/', $motDePasse)) {
        // Hasheexit("ok");r le mot de passe
        $motDePasseHash = password_hash($motDePasse, PASSWORD_DEFAULT);

        $sql = "INSERT INTO Users (pseudo, email, password) VALUES (?, ?, ?)";
        $stmt = $cnx->prepare($sql);

        if ($stmt) {
            // Liaison des paramètres et exécution de la requête
            $stmt->bindParam(1, $pseudo);
            $stmt->bindParam(2, $email);
            $stmt->bindParam(3, $motDePasseHash);

            if ($stmt->execute()) {

                // Inscription réussie, maintenant envoyez l'e-mail
                $mail = new PHPMailer(true);

                try {
                    $mail->isSMTP();
                    $mail->Host = 'partage.u-pem.fr';
                    $mail->Port = 465;
                    $mail->SMTPAuth = 1;
                    $mail->CharSet = 'UTF-8';

                    if ($mail ->SMTPAuth) {
                    $mail->SMTPSecure = 'ssl';
                    $mail->Username = 'taha.sefoudine@edu.univ-eiffel.fr';
                    $mail->Password = 'aiwook1Uig';

                    }

                    $mail->From = "taha.sefoudine@edu.univ-eiffel.fr";
                    $mail->FromName = "Confirmateur création de compte";


                    $mail->addAddress($_SESSION['email']);

                    $mail->Subject = 'Inscription';
                    $mail->WordWrap = 50;
                    $mail->AltBody = "Bonjour $pseudo, \n Votre inscription a bien été prise en compte. \n Cordialement, \n L'équipe de l'Université Gustave Eiffel improvisée.";
                    $mail->Body = "Bonjour $pseudo, <br> Votre inscription a bien été prise en compte. <br> Cordialement, <br> L'équipe de l'Université Gustave Eiffel improvisée.";
                    $mail->isHTML(false);
                    $mail->MsgHTML("ceci est un test");

                    $mail->send();
                    echo 'Message has been sent';

                    echo "Inscription réussie ! Un e-mail de bienvenue a été envoyé.";
                } catch (Exception $e) {
                    echo "Erreur lors de l'envoi de l'e-mail : " . $mail->ErrorInfo;
                }
            } else {
                echo "Erreur lors de l'inscription.";
            }
            $stmt = null; // Fermeture du statement
        } else {
            echo "Erreur lors de la préparation de la requête.";
        }
    } else {
        echo "Le mot de passe doit contenir au moins 8 caractères, au moins une lettre majuscule et au moins un caractère spécial.";
    }
}

?>
<!DOCTYPE html>
<html>
<head>
    <title>Inscription</title>
</head>
<body>
    <h1>Inscription</h1>
    <form method="POST" action="register.php">
        <label for="pseudo">Pseudo:</label>
        <input type="text" id="pseudo" name="pseudo" required><br><br>

        <label for="email">Adresse e-mail:</label>
        <input type="email" id="email" name="email" required><br><br>

        <label for="mot_de_passe">Mot de passe:</label>
        <input type="password" id="mot_de_passe" name="mot_de_passe" required><br><br>

        <label for="confirmation_mot_de_passe">Confirmez le mot de passe:</label>
        <input type="password" id="confirmation_mot_de_passe" name="confirmation_mot_de_passe" required><br><br>

        <input type="submit" value="S'inscrire">
    </form>

    <p>Déjà inscrit ? <a href="login.php">Se connecter</a></p>
</body>
</html>
