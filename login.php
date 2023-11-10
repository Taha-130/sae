<?php
session_start();
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

include("connexion.php"); // Include the connection file

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email']; // Retrieve the email from the form
    $motDePasse = $_POST['mot_de_passe']; // Retrieve the password from the form

    // Query to check if the email exists in the database
    $sql = "SELECT * FROM Users WHERE email = ?";
    $stmt = $cnx->prepare($sql);

    if ($stmt) {
        $stmt->bindParam(1, $email);

        if ($stmt->execute()) {
            $row = $stmt->fetch();
            if ($row && password_verify($motDePasse, $row['password'])) {
                // Login successful
                $_SESSION['email'] = $email;

                // Send a welcome email upon successful login
                $mail = new PHPMailer(true);
                try {
                    $mail->isSMTP();
                    $mail->Host = 'partage.u-pem.fr'; // Set your SMTP host
                    $mail->Port = 465; // Set the SMTP port
                    $mail->SMTPAuth = true;
                    $mail->CharSet = 'UTF-8';
                    $mail->SMTPSecure = 'ssl';
                    $mail->Username = 'taha.sefoudine@edu.univ-eiffel.fr';
                    $mail->Password = 'mdp';

                    $mail->From = "taha.sefoudine@edu.univ-eiffel.fr"; // Set your email as the sender
                    $mail->FromName = "Connexion Message"; // Set the sender's name

                    $mail->addAddress($email); // Send the email to the logged-in user
                    $mail->Subject = 'You are logged in'; // Set the email subject
                    $mail->isHTML(true); // Send as HTML

                    $mail->Body = "Vous êtes bien connecté !"; // Set the email body

                    $mail->send();
                    echo "Connexion réussie. Un mail de connexion a été envoyé.";
                    // You can redirect the user to their profile or another page here.
                } catch (Exception $e) {
                    echo "Error sending the welcome email: " . $mail->ErrorInfo;
                }
            } else {
                echo "Invalid email or password.";
            }
        } else {
            echo "Error during query execution.";
        }
    } else {
        echo "Error preparing the query.";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Connexion</title>
</head>
<body>
    <h1>Connexion</h1>
    <form method="POST" action="login.php">
        <label for="email">Adresse e-mail:</label>
        <input type="email" id="email" name="email" required><br><br>

        <label for="mot_de_passe">Mot de passe:</label>
        <input type="password" id="mot_de_passe" name="mot_de_passe" required><br><br>

        <input type="submit" value="Se connecter">
    </form>

    <p>Pas de compte ? <a href="register.php">S'inscrire</a></p>
</body>
</html>
