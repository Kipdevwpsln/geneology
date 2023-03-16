<?php
/**
 * Plugin Name: acck_genealogy
 * Description: Family tree plugin for WordPress.
 * Version: 1.0
 * Author: acck, Dennis
 */

// Prevent direct file access
if (!defined('ABSPATH')) {
    exit;
}

function createPerson()
{
    //get the permerlink in which the 
    $content = '';
    $content .= '<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-GLhlTQ8iRABdZLl6O3oVMWSktQOp6b7In1Zl3/Jr59b6EGGoI1aFkw7cmDA6j6gD" crossorigin="anonymous"';
    if (isset($_POST)) {
        //recover the variables from form

        $nome = $_POST['nom'];
        $prenom = $_POST['lien_image'];
        $date_naisance = $_POST['date_naisance'];
        $date_deces = $_POST['date_deces'];
        $lieu_naissance = $_POST['lieu_naissance'];
        $lieu_dece = $_POST['lieu_dece'];
        $numero_geneologic = $_POST['numero_geneologic'];

    }
    //create_person if idperson is not set
    if (isset($_POST) && isset($_POST['btn_create']) && !isset($_POST['idperson'])) {
        // prepare SQL statement
        $sql = "INSERT INTO persons (Nome, prenom, date_naisance, date_deces, lieu_naissance, lieu_dece, numero_geneologic, lien_image)
        VALUES (:Nome, :prenom, :date_naisance, :date_deces, :lieu_naissance, :lieu_dece, :numero_geneologic, :lien_image)";

        try {
            $conn = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET, DB_USER, DB_PASSWORD);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $stmt = $conn->prepare($sql);

        // bind parameters and execute
        $stmt->bindParam(':Nome', $nome);
        $stmt->bindParam(':prenom', $prenom);
        $stmt->bindParam(':date_naisance', $date_naisance);
        $stmt->bindParam(':date_deces', $date_deces);
        $stmt->bindParam(':lieu_naissance', $lieu_naissance);
        $stmt->bindParam(':lieu_dece', $lieu_dece);
        $stmt->bindParam(':numero_geneologic', $numero_geneologic);
        $stmt->bindParam(':lien_image', $lien_image);
        $stmt->execute();

        // redirect to success page
        header('Location: success.php');
        exit();
        } catch (PDOException $e) {
            echo $e;
        }
        
    }
    $content = '
    <form method="POST" action="submit.php">
  <div class="form-group">
    <label for="nom">Nom</label>
    <input type="text" class="form-control" id="nom" name="nom" value="<?php echo $nome; ?>" required>
  </div>
  <div class="form-group">
    <label for="prenom">Prenom</label>
    <input type="text" class="form-control" id="prenom" name="prenom" value="<?php echo $prenom; ?>" required>
  </div>
  <div class="form-group">
    <label for="date_naisance">Date de naissance</label>
    <input type="text" class="form-control" id="date_naisance" name="date_naisance" value="<?php echo $date_naisance; ?>" required>
  </div>
  <div class="form-group">
    <label for="date_deces">Date de décès</label>
    <input type="date" class="form-control" id="date_deces" name="date_deces" value="<?php echo $date_deces; ?>" required>
  </div>
  <div class="form-group">
    <label for="lieu_naissance">Lieu de naissance</label>
    <input type="text" class="form-control" id="lieu_naissance" name="lieu_naissance" value="<?php echo $lieu_naissance; ?>" required>
  </div>
  <div class="form-group">
    <label for="lieu_dece">Lieu de décès</label>
    <input type="text" class="form-control" id="lieu_dece" name="lieu_dece" value="<?php echo $lieu_dece; ?>" required>
  </div>
  <div class="form-group">
    <label for="numero_geneologic">Numéro généalogique</label>
    <input type="text" class="form-control" id="numero_geneologic" name="numero_geneologic" value="<?php echo $numero_geneologic; ?>" required>
  </div>
  <div class="form-group">
    <label for="image">Photo</label>
    <input type="text" class="form-control" id="lien_image" name="lien_image" value="<?php echo $lien_image; ?>" required>
  </div>
  <button type="submit" class="btn btn-primary">Ajouter une personne</button>
</form>
';

    $content .= ' <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js" integrity="sha384-w76AqPfDkMBDXo30jS1Sgez6pr3x5MlQ1ZAGC+nuZB+EYdgRZgiwxhTBTkF7CXvN" crossorigin="anonymous"></script>';

    return $content;

}
add_shortcode('add_person', 'createPerson');
