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
    //get the permerlink in which the short code is put
    $permelink= get_permalink( post, leavename );

    $content = '';
    $content .= '<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-GLhlTQ8iRABdZLl6O3oVMWSktQOp6b7In1Zl3/Jr59b6EGGoI1aFkw7cmDA6j6gD" crossorigin="anonymous"';
    if (isset($_POST)) {
        //recover the variables from form

        $nom = $_POST['nom'];
        $prenom = $_POST['lien_image'];
        $date_naisance = $_POST['date_naisance'];
        $date_deces = $_POST['date_deces'];
        $lieu_naissance = $_POST['lieu_naissance'];
        $lieu_dece = $_POST['lieu_dece'];
        $numero_geneologic = $_POST['numero_geneologic'];

    }
    //uploadimage
    if(isset ($_FILES)){
        $photo = $_FILES['image'];
        $name_photo = $photo['name'];
        if ($photo['error'] === UPLOAD_ERR_OK){
            $tmpName = $photo['tmp_name'];
            $sizephoto = $photo['size'];
            //remember to create folder descendants
            $destination = './wp-content/uploads/descendants' . $name_photo;
            $extention = pathinfo($name_photo, PATHINFO_EXTENSION);
            if($sizephoto > 1000000){
                if (in_array($extention, ['JPG','JPEG', 'jpeg','jpg','png','PNG'])){
                    //a fun way to move the image to a place in the cloude
                    if(move_uploaded_file($tmpName, $destination)){
                        //if we are here, it worked, so get the link
                        $lien_image= $destination.$name_photo;
                    }
                    else{
                        echo '
                        <script>
                            alert("une erreur inconnue s\'est produite lors du téléchargement de votre photo. réessayez avec un fichier différent. ");
                        </script>';      
                    }
                }
                else{
                    echo '
                    <script>
                        alert("Le type d\'image que vous essayez de télécharger n\'est pas autorisé ; seuls les types JPG et PNG sont acceptés. . ");
                    </script>'; 
                }
            }
            else {
                echo '
                <script>
                    alert("L\'image que vous essayez de télécharger est trop lourde. ");
                </script>';  
            }
        }
        else{
            echo '
            <script>
                alert("Il y a eu une erreur lors du téléchargement de votre photo");
            </script>';
        ;
        }


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
        $stmt->bindParam(':Nome', $nom);
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
    //Note: permerlink become form action
    $content = '
    <form method="POST" action="'.$permelink.'" accept-charset="utf-8" enctype="multipart/form-data" >
  <div class="form-group">
    <label for="nom">Nom</label>
    <input type="text" class="form-control" id="nom" name="nom" value="'.$nom.'" required>
  </div>
  <div class="form-group">
    <label for="prenom">Prenom</label>
    <input type="text" class="form-control" id="prenom" name="prenom" value="'.$prenom.'" required>
  </div>
  <div class="form-group">
    <label for="date_naisance">Date de naissance</label>
    <input type="text" class="form-control" id="date_naisance" name="date_naisance" value="'.$date_naisance.'" required>
  </div>
  <div class="form-group">
    <label for="date_deces">Date de décès</label>
    <input type="date" class="form-control" id="date_deces" name="date_deces" value="'.$date_deces.'" required>
  </div>
  <div class="form-group">
    <label for="lieu_naissance">Lieu de naissance</label>
    <input type="text" class="form-control" id="lieu_naissance" name="lieu_naissance" value="'.$lieu_naissance.'" required>
  </div>
  <div class="form-group">
    <label for="lieu_dece">Lieu de décès</label>
    <input type="text" class="form-control" id="lieu_dece" name="lieu_dece" value="'.$lieu_dece.'" required>
  </div>
  <div class="form-group">
    <label for="numero_geneologic">Numéro généalogique</label>
    <input type="text" class="form-control" id="numero_geneologic" name="numero_geneologic" value="'.$numero_geneologic.'" required>
  </div>
  <div class="form-group">
    <label for="image">Photo</label>
    <input type="file" class="form-control" id="image" name="image" value="'.$lien_image.'" required>
  </div>
  <button type="submit" class="btn btn-primary">Ajouter une personne</button>
</form>
';

    $content .= ' <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js" integrity="sha384-w76AqPfDkMBDXo30jS1Sgez6pr3x5MlQ1ZAGC+nuZB+EYdgRZgiwxhTBTkF7CXvN" crossorigin="anonymous"></script>';

    return $content;

}
add_shortcode('add_person', 'createPerson');
