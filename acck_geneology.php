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

function acck_genealogy()
{
    //get the permerlink in which the short code is put
    $permelink = get_permalink(post, leavename);

    $content = '';
    $content .= '<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-GLhlTQ8iRABdZLl6O3oVMWSktQOp6b7In1Zl3/Jr59b6EGGoI1aFkw7cmDA6j6gD" crossorigin="anonymous"';
    if (isset($_POST)) {
        //recover the variables from form

        $nom = $_POST['nom'];
        $prenom = $_POST['prenom'];
        $nom_jeune_fille = $_POST['nom_jeune_fille'];
        $observacion = $_POST['observacion'];
        $date_naisance = $_POST['date_naisance'];
        $lieu_naissance = $_POST['lieu_naissance'];
        $date_mariage = $_POST['date_mariage'];
        $lieu_mariage = $_POST['lieu_mariage'];
        $date_deces = $_POST['date_deces'];
        $lieu_dece = $_POST['lieu_dece'];
        $note = $_POST['note'];

    }
    //uploadimage
    if (isset($_FILES)) {
        $photo = $_FILES['image'];
        $name_photo = $photo['name'];
        if ($photo['error'] === UPLOAD_ERR_OK) {
            $tmpName = $photo['tmp_name'];
            $sizephoto = $photo['size'];
            //remember to create folder descendants
            $destination = './wp-content/uploads/descendants' . $name_photo;
            $extention = pathinfo($name_photo, PATHINFO_EXTENSION);
            if ($sizephoto > 1000000) {
                if (in_array($extention, ['JPG', 'JPEG', 'jpeg', 'jpg', 'png', 'PNG'])) {
                    //a fun way to move the image to a place in the cloude
                    if (move_uploaded_file($tmpName, $destination)) {
                        //if we are here, it worked, so get the link
                        $lien_image = $destination . $name_photo;
                    } else {
                        echo '
                        <script>
                            alert("une erreur inconnue s\'est produite lors du téléchargement de votre photo. réessayez avec un fichier différent. ");
                        </script>';
                    }
                } else {
                    echo '
                    <script>
                        alert("Le type d\'image que vous essayez de télécharger n\'est pas autorisé ; seuls les types JPG et PNG sont acceptés. . ");
                    </script>';
                }
            } else {
                echo '
                <script>
                    alert("L\'image que vous essayez de télécharger est trop lourde. ");
                </script>';
            }
        } else {
            echo '
            <script>
                alert("Il y a eu une erreur lors du téléchargement de votre photo");
            </script>';
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
            $stmt->bindParam(':nome', $nom);
            $stmt->bindParam(':prenom', $prenom);
            $stmt->bindValue(':nom_jeune_fille', $nom_jeune_fille);
            $stmt->bindParam(':date_naisance', $date_naisance);
            $stmt->bindParam(':lieu_naissance', $lieu_naissance);
            $stmt->bindParam(':date_mariage', $date_mariage);
            $stmt->bindParam(':lieu_mariage', $lieu_mariage);
            $stmt->bindParam(':date_deces', $date_deces);
            $stmt->bindParam(':lieu_dece', $lieu_dece);
            $stmt->bindParam(':note', $note);
            $stmt->bindParam(':lien_image', $lien_image);
            $stmt->execute();
            $id_person = $conn->lastInsertId();
            // redirect to success page
            header("Location: /?idperson='.$id_person.'");
            exit();
        } catch (PDOException $e) {
            echo $e;
        }
    }
    //The person exist already, We can update info
    elseif (isset($_GET['idpersorn'])) {
        $id_person = $_GET['idperson'];
        $form_title = '<h2>Mise à jour des informations pour ' . $nom . '</h2>';
        $value_btn_submit = "mise à jour";
        $btn_publish = '<button type="submit" name = "publish" class="btn btn-secondery">Rendre publique</button>';
        $form_action = $permelink . '/?idperson=' . $id_person;

        //if submit button is clicked
        if (isset($_POST['submit']) && !empty($_POST['nom'])) {
            $sql = "UPDATE persons SET
                nom = :nom,
                prenom = :prenom,
                nom_jeune_fille= :nom_jeune_fille,
                observation = :observation,
                date_naisance = :date_naisance,
                date_deces = :date_deces,
                lieu_naissance = :lieu_naissance,
                date_mariage=:date_mariage,
                lieu_mariage=:lieu_mariage,
                note = :note,
                lieu_dece = :lieu_dece,
                note = :note,
                lien_image = :lien_image
                WHERE idpersones = :id";
            try {
                $conn = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET, DB_USER, DB_PASSWORD);
                $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $stmt = $conn->prepare($sql);
                // bind parameters and execute
                $stmt->bindParam(':Nome', $nom);
                $stmt->bindParam(':prenom', $prenom);
                $stmt->bindParam('nom_jeune_fille', $nom_jeune_fille);
                $stmt->bindParam(':observacion', $observacion);
                $stmt->bindParam(':date_naisance', $date_naisance);
                $stmt->bindParam(':date_mariage', $date_mariage);
                $stmt->bindParam(':date_deces', $date_deces);
                $stmt->bindParam(':lieu_naissance', $lieu_naissance);
                $stmt->bindParam(':lieu_dece', $lieu_dece);
                $stmt->bindParam(':notes', $notes);
                $stmt->bindParam(':lien_image', $lien_image);

                $stmt->execute();
            } catch (PDOException $e) {
                echo '
                <script>
                alert("la mise à jour des informations pour ' . $nom . ' a échoué");
                </script>';
            }
        }
        if(isset($_POST['btn_publish'])){
            global $wpdb;
            $post_content= '
            <div class="container">
            <img src="'.$lien_image.'" alt="photo" />
            <div class="container">
            <h4>'.$nom.'  '.$prenom.'</h4>
            </div>
            </div>';
            
            $cpt = (array(
                'post_type' => 'desscendants',
                'post_title' => $nom. $prenom,
                'post_content' => $post_content,
                'post_status' => 'publish',
            ));
            $post_id = wp_insert_post($cpt);

            $id_cpt = get_post($post_id);

            $permerlien = get_permalink($id_cpt, $leavename = false);

            wp_redirect($permerlien);
            exit();
        // add postID to the to the person

        }
    }
    else{
        $value_btn_submit = 'Ajouter un descendant';
        $form_title = '<h4>Ajouter un nouveau descendant</h4>';
      }
    //Note: permerlink become form action
    $content = '
    ' . $form_title . '
    <br>
    <form method="POST" action="' . $form_action . '" accept-charset="utf-8" enctype="multipart/form-data" >
    <div class="row">
    <div class="col-md">
    <label for="nom">Nom de famille</label>
    <input type="text" class="form-control" id="nom" name="nom" value="' . $nom . '" required>
  </div>
  <div class="col-md">
    <label for="prenom">Prenom</label>
    <input type="text" class="form-control" id="prenom" name="prenom" value="' . $prenom . '" required>
  </div>
  </div>

  <div class="row">
    <div class="col-md">
    <label for="nom_jeune_fille">Nom de jeune fille </label>
    <input type="text" class="form-control" "name= nom_jeune_fille" id="nom_jeune_fille" value="' . $nom_jeune_fille . '" required>
  </div>
  <div class="col-md">
    <label for=observations">Observations</label>
    <input type="text" class="form-control" id="observations" name="observations" value="' . $observaion . '" required>
  </div>
  </div>

  <div class="row">
  <div class="col-md">
    <label for="date_naisance">Date de naissance</label>
    <input type="date" class="form-control" id="date_naisance" name="date_naisance" value="' . $date_naisance . '" required>
  </div>
  <div class="col-md">
  <label for="lieu_naissance">Lieu de naissance</label>
  <input type="text" class="form-control" id="lieu_naissance" name="lieu_naissance" value="' . $lieu_naissance . '" required>
  </div>
  </div>
  
  <div class="row">
  <div class="col-md">
  <label for="date_mariage">Date Marriage</label>
  <input type="date" class="form-control" "name= date_mariage" id="date_mariage" value="' . $date_mariage . '" required>
</div>
<div class="col-md">
  <label for="lieu_mariage">Observations</label>
  <input type="text" class="form-control" id="lieu_mariage" name="lieu_mariage" value="' . $lieu_mariage . '" required>
</div>
</div>
  
  <div class="row">
  <div class="col-md">
  <label for="date_deces">Date de décès</label>
  <input type="date" class="form-control" id="date_deces" name="date_deces" value="' . $date_deces . '" required>  
  </div>
  <div class="col-md">
    <label for="lieu_dece">Lieu de décès</label>
    <input type="text" class="form-control" id="lieu_dece" name="lieu_dece" value="' . $lieu_dece . '" required>
  </div>
  </div>

  <div class="form-group">
    <label for="notes">Notes</label>
    <input type="text" class="form-control" id="notes" name="notes" value="' . $notes . '" required>
  </div>
  <div class="form-group">
    <label for="image">Photo</label>
    <input type="file" class="form-control" id="image" name="image" value="' . $lien_image . '" required>
  </div>
  <br>
  <button type="submit" class="btn btn-primary" name ="submit">'.$value_btn_submit.'</button>
  ' . $btn_publish . '
</form>
';

    $content .= ' <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js" integrity="sha384-w76AqPfDkMBDXo30jS1Sgez6pr3x5MlQ1ZAGC+nuZB+EYdgRZgiwxhTBTkF7CXvN" crossorigin="anonymous"></script>';

    return $content;

}
add_shortcode('add_person', 'acck_genealogy');

//show all people
function showdescendants() {
$content = '
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-GLhlTQ8iRABdZLl6O3oVMWSktQOp6b7In1Zl3/Jr59b6EGGoI1aFkw7cmDA6j6gD" crossorigin="anonymous"';
$content .= '<h4>Les descendant </h4>';
$content .= '
<form class="form-inline">
    <input class="form-control mr-sm-2" type="search" placeholder="Nom ou/et Prenom" aria-label="Search">
    <button class="btn btn-outline-success my-2 my-sm-0" type="submit">Recherche</button>
  </form>';
$content .= '</hr>';

return $content;
}
add_shortcode('showdescendants', 'showdescendants');