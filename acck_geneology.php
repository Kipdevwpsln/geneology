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
//add jquery
function my_plugin_scripts()
{
    wp_enqueue_script('jquery');
    wp_enqueue_script('my-plugin-script', plugin_dir_url(__FILE__) . 'acck-jquery-script.js', array('jquery'));
}
add_action('wp_enqueue_scripts', 'my_plugin_scripts');
function my_plugin_enqueue_scripts()
{
    // Get the URL of the CSS file
    $css_url = plugins_url('kipdev_style.css', __FILE__);

    // Enqueue the CSS file
    wp_enqueue_style('my-plugin-style', $css_url);
}
add_action('wp_enqueue_scripts', 'my_plugin_enqueue_scripts');

function acck_genealogy()
{
    $content = '';
    //create tables if the do not exist table
    define('MY_PLUGIN_PATH', plugin_dir_path(__FILE__));
    include MY_PLUGIN_PATH . 'create_tables.php';

    //get the permerlink of the page in which the short code is put
    $permelink = get_permalink();

    $content .= '<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-GLhlTQ8iRABdZLl6O3oVMWSktQOp6b7In1Zl3/Jr59b6EGGoI1aFkw7cmDA6j6gD" crossorigin="anonymous"';
    //recover the variables from form
    $nom = isset($_POST['nom']) ? $_POST['nom'] : '';
    $prenom = isset($_POST['prenom']) ? $_POST['prenom'] : '';
    $nom_jeune_fille = isset($_POST['nom_jeune_fille']) ? $_POST['nom_jeune_fille'] : '';
    $observation = isset($_POST['observations']) ? sanitize_textarea_field($_POST['observations']) : '';
    $date_naisance = isset($_POST['date_naisance']) ? $_POST['date_naisance'] : '';
    $lieu_naissance = isset($_POST['lieu_naissance']) ? $_POST['lieu_naissance'] : '';
    $date_mariage = isset($_POST['date_mariage']) ? $_POST['date_mariage'] : '';
    $lieu_mariage = isset($_POST['lieu_mariage']) ? $_POST['lieu_mariage'] : '';
    $date_deces = isset($_POST['date_deces']) ? $_POST['date_deces'] : '';
    $lieu_dece = isset($_POST['lieu_dece']) ? $_POST['lieu_dece'] : '';
    $note = isset($_POST['notes']) ? $_POST['notes'] : '';

    //uploadimage
    $siteRrl= get_site_url();
    //echo $siteRrl;
    if (isset($_FILES) && !empty($_FILES)) {
        //echo "Uploading image";
        $photo = $_FILES['image'];
        $name_photo = $photo['name'];
        if ($photo['error'] === UPLOAD_ERR_OK) {
            $tmpName = $photo['tmp_name'];
            $sizephoto = $photo['size'];
            //remember to create folder descendants
            $destination = './wp-content/uploads/descendants/' . $name_photo;
            $extention = pathinfo($name_photo, PATHINFO_EXTENSION);
            if ($sizephoto <= 10000000) {
                if (in_array($extention, ['JPG', 'JPEG', 'jpeg', 'jpg', 'png', 'PNG'])) {
                    //a fun way to move the image to a place in the cloude
                    if (move_uploaded_file($tmpName, $destination)) {
                        //if we are here, it worked, so get the link
                        $lien_image = "$siteRrl/wp-content/uploads/descendants/$name_photo";
                        $content .= $lien_image;
                        //echo $lien_image;
                    } else {
                        echo '
                        <script>
                            alert("une erreur inconnue s\'est produite lors du téléchargement de votre photo. réessayez avec un fichier différent.");
                        </script>';
                    }
                } else {
                    echo '
                    <script>
                        alert("Le type d\'image que vous essayez de télécharger n\'est pas autorisé ; seuls les types JPG et PNG sont acceptés. .");
                    </script>';
                }
            } else {
                echo '
                <script>
                    alert("L\'image que vous essayez de télécharger est trop lourde.");
                </script>';
            }
        } else {
            $content .= "Il y a eu une erreur lors du téléchargement de votre photo";

        }
    }
    //create_person if noGeneologique is not set
    if (isset($_POST['submit']) && !isset($_GET['noGeneologique'])) {
        // prepare SQL statement

        $sql = "INSERT INTO `gen_personnes`(`nom`, `prenom`, `nom_jeune_fille`, `observation`, `date_naisance`,
         `lieu_naissance`, `date_mariage`, `lieu_mariage`, `date_deces`, `lieu_dece`, `note`, `lien_image`, `relation_id_relation`) 
        VALUES (:nom, :prenom, :nom_jeune_fille, :observation, :date_naisance, :lieu_naissance, :date_mariage, :lieu_mariage,:date_deces, :lieu_dece, :note, :lien_image, :relation_id_relation)";

        try {
            $conn = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET, DB_USER, DB_PASSWORD);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $stmt = $conn->prepare($sql);

            // bind parameters and execute
            $stmt->bindParam(':nom', $nom);
            $stmt->bindParam(':prenom', $prenom);
            $stmt->bindValue(':nom_jeune_fille', $nom_jeune_fille);
            $stmt->bindParam(':date_naisance', $date_naisance);
            $stmt->bindParam(':observation', $observation);
            $stmt->bindParam(':lieu_naissance', $lieu_naissance);
            $stmt->bindParam(':date_mariage', $date_mariage);
            $stmt->bindParam(':lieu_mariage', $lieu_mariage);
            $stmt->bindParam(':date_deces', $date_deces);
            $stmt->bindParam(':lieu_dece', $lieu_dece);
            $stmt->bindParam(':note', $note);
            $stmt->bindParam(':lien_image', $lien_image);
            $stmt->bindParam(':relation_id_relation', $relation_id_relation);
            $stmt->execute();
            $noGeneologique = $conn->lastInsertId();
            // redirect to success page
            header("Location: /ajout/?noGeneologique=$noGeneologique");
            exit();
        } catch (PDOException $e) {
            echo "there was an error inserting into bthe database" . $e;
        }
        $conn = null;
    }
    //The person exist already, We can update info
    elseif (isset($_GET['noGeneologique'])) {
        $id_person = $_GET['noGeneologique'];
        $form_title = '<h2>Mise à jour des informations pour ' . $nom . '</h2>';
        $btn_submit = '<input type="submit" class ="btn btn-warning"  name="modifier" value="Modifier descendant" />';
        $form_action = '/ajout/?noGeneologique=' . $id_person;

        //if submit button is clicked
        if (isset($_POST['modifier'])) {
            $sql_update = "UPDATE `gen_personnes` SET 
            `nom`=             :nom,
            `prenom`=          :prenom,
            `nom_jeune_fille`= :nom_jeune_fille,
            `observation`=     :observation,
            `date_naisance`=   :date_naisance,
            `lieu_naissance`=  :lieu_naissance,
            `date_mariage`=    :date_mariage,
            `lieu_mariage`=    :lieu_mariage,
            `date_deces`=      :date_deces,
            `lieu_dece`=       :lieu_dece,
            `note`=            :note,
            `lien_image`=      :lien_image,
            `relation_id_relation`= :relation_id_relation
             WHERE
            `noGeneologique` = :noGeneologique";
            try {
                $conn = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET, DB_USER, DB_PASSWORD);
                $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $stmt = $conn->prepare($sql_update);
                // bind parameters and execute
                //echo "connection established";

                $stmt->bindParam(':noGeneologique', $id_person);
                $stmt->bindParam(':nom', $nom);
                $stmt->bindParam(':prenom', $prenom);
                $stmt->bindParam(':nom_jeune_fille', $nom_jeune_fille);
                $stmt->bindParam(':observation', $observation);
                $stmt->bindParam(':date_naisance', $date_naisance);
                $stmt->bindParam(':date_mariage', $date_mariage);
                $stmt->bindParam(':lieu_mariage', $lieu_mariage);
                $stmt->bindParam(':date_deces', $date_deces);
                $stmt->bindParam(':lieu_naissance', $lieu_naissance);
                $stmt->bindParam(':lieu_dece', $lieu_dece);
                $stmt->bindParam(':note', $note);
                $stmt->bindParam(':lien_image', $lien_image);
                $stmt->bindParam(':relation_id_relation', $relation_id_relation);
                $stmt->execute();
                $noGeneologique = $conn->lastInsertId();
                

                //echo "mis a jour reusit";

            } catch (PDOException $e) {
                // echo '
                //  <script>
                // alert("la mise à jour des informations pour ' . $nom . ' a échoué");
                // </script>';
                echo "issues with update of descendant info" . $e;
            }
            $conn = null;
        }

        $id_person = $_GET['noGeneologique'];
        $sql_select = "SELECT * FROM gen_personnes WHERE noGeneologique= :noGeneologique";
        try {
            $conn = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET, DB_USER, DB_PASSWORD);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $stmt = $conn->prepare($sql_select);
            $stmt->bindValue(':noGeneologique', $id_person);
            $stmt->execute([':noGeneologique' => $id_person]);

            $person = $stmt->fetch(PDO::FETCH_ASSOC);

            $nom = $person['nom'];
            $prenom = $person['prenom'];
            $nom_jeune_fille = $person['nom_jeune_fille'];
            $observation = $person['observation'];
            $date_naisance = $person['date_naisance'];
            $lieu_naissance = $person['lieu_naissance'];
            $date_mariage = $person['date_mariage'];
            $lieu_mariage = $person['lieu_mariage'];
            $date_deces = $person['date_deces'];
            $lieu_dece = $person['lieu_dece'];
            $note = $person['note'];
            $lien_image = $person['lien_image'];
            $id_relation = $person['relation_id_relation'];

            //echo "selection of person";
            

        } catch (\Throwable $e) {
            echo "error while selecting peson" . $e;
        }
        $conn = null;

    } else {
        $btn_submit = '<input type="submit" class ="btn btn-primary"  name="submit" value="Ajouter un descendant"/>';
        $form_title = '<h4>Ajouter un nouveau descendant</h4>';
        $form_action = "/ajout/";
    }
    //echo "nom selectioner". $prenom;
    //Note: permerlink become form action
    $content .=' 
    <div class="container" id="addEditForm">
    <a href="/tout-les-descendants/"><button class= "btn btn-primary">Voir tout les descendants</button></a> <br>
    <a href=""><button class= "btn btn-info">Voir tout les descendants</button></a>
    ' . $form_title . '
    <form method="POST" action="' . $form_action . '" accept-charset="utf-8" enctype="multipart/form-data">
        <div class"row">
            <div class="col-md-4">
                <figure>
                    <img src ="' . $lien_image . '" alt ="PHOTO" class="img-fluid">
                    <legend>' . $nom . '  ' . $prenom . '</legend>
                </figure>
                <div class="form-group">
                   
                    <input type="file" class="form-control" id="image" name="image">
                </div>
            </div>
            <div class="col-md-8">
                <div class="row">
                    <div class="col-md">
                        <label for="nom">Nom de famille</label>
                        <input type="text" class="form-control" id="nom" name="nom" value="' . $nom . '" required>
                    </div>
                    <div class="col-md">
                        <label for="prenom">Prenom</label>
                        <input type="text" class="form-control" id="prenom" name="prenom" value="' . $prenom . '" sss>
                    </div>
                    <div class="col-md">
                        <label for="nom_jeune_fille">Nom de jeune fille </label>
                        <input type="text" class="form-control" name="nom_jeune_fille" id="nom_jeune_fille" value="' . $nom_jeune_fille . '">
                    </div>
                    <div class="row">
                        <div class="form-group">
                        <label for="observations">Observations</label>
                        <textarea class="form-control" id="observations" name ="observations" rows="3">'.$observation.'</textarea>
                      </div>
                      <div class="form-group">
                      <label for="notes">Notes</label>
                      <textarea class="form-control" id="notes" name = "notes" rows="3">'.$note.'</textarea>
                    </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md">
                <label for="date_naisance">Date de naissance</label>
                <input type="date" class="form-control" id="date_naisance" name="date_naisance" value="'.$date_naisance.'">
            </div>
            <div class="col-md">
                <label for="lieu_naissance">Lieu de naissance</label>
                <input type="text" class="form-control" id="lieu_naissance" name="lieu_naissance" value=" ' . $lieu_naissance . '" >
            </div>
        </div>
        <div class="row">
            <div class="col-md">
                <label for="date_mariage">Date Marriage</label>
                <input type="date" class="form-control" name="date_mariage" id="date_mariage" value="'.$date_mariage.'">
            </div>
            <div class="col-md">
                <label for="lieu_mariage">Liu Mariage</label>
                <input type="text" class="form-control" id="lieu_mariage" name="lieu_mariage" value=" ' . $lieu_mariage . '">
            </div>
        </div>

        <div class="row">
            <div class="col-md">
                <label for="date_deces">Date de décès</label>
                <input type="date" class="form-control" id="date_deces" name="date_deces" value="'.$date_deces .'">  
            </div>
            <div class="col-md">
                <label for="lieu_dece">Lieu de décès</label>
                <input type="text" class="form-control" id="lieu_dece" name="lieu_dece" value=" ' . $lieu_dece . '">
            </div>';
    $content .= '<br> <br>
        ' . $btn_submit . '
    </form> 
</div>';
    $content .= ' <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js" integrity="sha384-w76AqPfDkMBDXo30jS1Sgez6pr3x5MlQ1ZAGC+nuZB+EYdgRZgiwxhTBTkF7CXvN" crossorigin="anonymous"></script>';

    return $content;
}
add_shortcode('add_person', 'acck_genealogy');

////////////////////////////////////////////////////////////////
/////////Afich list des descendat//////////////////////////////
////////////////////////////////////////////////////////////////
function showdescendants()
{
    $content = '';
    $content .= '
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-GLhlTQ8iRABdZLl6O3oVMWSktQOp6b7In1Zl3/Jr59b6EGGoI1aFkw7cmDA6j6gD" crossorigin="anonymous">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.3/font/bootstrap-icons.css">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha2/dist/js/bootstrap.bundle.min.js" integrity="sha384-qKXV1j0HvMUeCBQ+QVp7JcfGl760yU08IQ+GpUo5hlbpg51QRiuqHAJz8+BrxE/N" crossorigin="anonymous"></script>';
    $content .= '<h4>Les descendants </h4>';
    $content .= '
<form class="form">
<div class="row">
<div class="col-sm-8">
    <input class="form-control mr-sm-2" type="search" placeholder="Nom/prenon" aria-label="Search">
</div>
<div class="col-sm-4">
<button class="btn btn-outline-success my-2 my-sm-0" type="submit">Rechercher</button>
</div>
</div>
</form>';
    $content .= '<hr>';
    //select THE FIRST 20 PERSONS
    $sql = "SELECT * FROM `gen_personnes` ORDER by noGeneologique LIMIT 20";
    try {
        $conn = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET, DB_USER, DB_PASSWORD);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $list = $conn->query($sql);
        if ($list) {
            $content .= '
            <div class ="table-fixed-header">
            <table class="table table-striped">
            <thead class="table-dark">
            <tr>
                <th scope="col">N°Gén</th>
                <th scope="col">Nom</th>
                <th scope="col">Prenon</th>
                <th scope="col">modifier</th>
                <th scope="col">Voir </th>
                <th scope="col">Ajout relation</th>
                </tr>
            </thead>
            <tbody>';
            while ($row = $list->fetch()) {
                $num_geneologique= $row['noGeneologique'];
                $nom = $row['nom'];
                $prenom = $row['prenom'];
        
                $content .=
                    '<tr>
                    <td>' . $num_geneologique . '</td>
                    <td>' . $nom . '</td>
                    <td>' . $prenom. '</td>
                   
                    <td>
                    
                    <a href="/ajout/?noGeneologique=' . $num_geneologique . '#addEditForm" class= "addEditicon"><i class="bi bi-pencil"></i></a>
                    </td>  
                    <td>
                    <a href="/tout-les-descendants/?noGeneologique=' . $num_geneologique .'#info_descendant"><i class="bi bi-eye"></i></a>
                    
                    </td>
                    <td>
                    <i class="bi bi-node-plus"></i>
                    </td>
                    </tr>';
            }
            $content .= '</tbody>
        </table>
        </div>';
        } else {
            $content .= '<p>aucun descendant n\'a encore été ajouté</p>';
        }
    } catch (\Throwable $e) {
        echo "there was an error while selecting from gen_personnes" . $e;
    }
    $conn = null;
    $content .='<div class= "container" id ="info_descendant">';
    if (isset($_GET['noGeneologique'])){
        $id_person = $_GET['noGeneologique'];
        $sql_select = "SELECT * FROM gen_personnes WHERE noGeneologique= :noGeneologique";
        try {
            $conn = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET, DB_USER, DB_PASSWORD);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $stmt = $conn->prepare($sql_select);
            $stmt->bindValue(':noGeneologique', $id_person);
            $stmt->execute([':noGeneologique' => $id_person]);

            $person = $stmt->fetch(PDO::FETCH_ASSOC);

            $nom = $person['nom'];
            $prenom = $person['prenom'];
            $nom_jeune_fille = $person['nom_jeune_fille'];
            $observation = $person['observation'];
            $date_naisance = $person['date_naisance'];
            $lieu_naissance = $person['lieu_naissance'];
            $date_mariage = $person['date_mariage'];
            $lieu_mariage = $person['lieu_mariage'];
            $date_deces = $person['date_deces'];
            $lieu_dece = $person['lieu_dece'];
            $note = $person['note'];
            $lien_image = $person['lien_image'];
            $id_relation = $person['relation_id_relation'];

            $content .='<div class="row">
            <div class="col-md">
        
            <img src="'.$lien_image.'" alt="PHOTO"/ class="img-fluid" > 
            <legend class="legend">'.$nom.'  '.$prenom.'</legend>
            <p>Date de naissances: '.$date_naisance.'<p>
            <p>Lieu de naissances: '.$lieu_naissance.'<p>';
            if($date_deces){
                $content .='<p>Date de decé: '.$date_deces.'<p>'; 
                $content .='<p>liue  de decé: '.$lieu_dece.'<p>'; 
            }
            $content .= '
            </div>
            <div class="col-md">
            <h5>Relations</h5>
            <p>Effant de __'.$parent1.' et __'.$parent2.'</p>
            <p>Parent de __'.$nfant.',__</p> <br>

            <h5>Union.s</h5>
            <table class="table table">
            <thead>
            <tr>
            <th>Type d\'union</th>
            <th>Date debut</th>
            <th>Date fin</th>
            <th>Avec..</th>
            </tr>
            <thead>
            <tbody>
            </tbody>
            </table>

            <h5>Obsevations</h5>
            '.$observation.'
            <br>
            <h5>Notes</h5>
            '.$note.'
            <br>
           

            
            </div>
            </div>';
            

        } catch (\Throwable $e) {
            echo "error while selecting peson" . $e;
        }
        $conn = null;
    }
    
    $content .='</div>';
    return $content;
}
add_shortcode('showdescendants', 'showdescendants');

function add_relations($noGeneoloqque)
{
    $content = '';
    $content .= '
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-GLhlTQ8iRABdZLl6O3oVMWSktQOp6b7In1Zl3/Jr59b6EGGoI1aFkw7cmDA6j6gD" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.3/font/bootstrap-icons.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha2/dist/js/bootstrap.bundle.min.js" integrity="sha384-qKXV1j0HvMUeCBQ+QVp7JcfGl760yU08IQ+GpUo5hlbpg51QRiuqHAJz8+BrxE/N" crossorigin="anonymous"></script>';
    $content .= '<div class="container">';
    $content .= '<form method="post" action="/ajout/">
    <div class="container">
        <div class="row">
            <div class="col-md">
                <h4>Sélectionner la première personne</h4>
                <select name="person1" class="form-select" size="5" aria-label="Sélectionner un descendant">';
    $sql = "SELECT * FROM `gen_personnes` ORDER by noGeneologique";
    try {
        $conn = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET, DB_USER, DB_PASSWORD);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $list = $conn->query($sql);
        if ($list) {
            while ($row = $list->fetch()) {

                $noGeneologique = $row['noGeneologique'];
                $nom = $row['nom'];
                $prenom = $row['prenom'];
                $person = "$noGeneologique  $nom $prenom";
                $content .= '<option value="' . $noGeneologique . '">' . $person . '</option>"<br />';
            }
        } else {
            $content .= "pas de personnes à sélectionner ";
        }
    } catch (\Throwable $e) {
        echo "there was an error while selecting from gen_personnes" . $e;
    }
    $conn = null;
    $content .= '
                </select>
            </div>
            <div class="col-md">
                <h4>Choisissez une relation à ajouter</h4>
                <div class="input-group mb-3">
                    <label class="input-group-text" for="inputGroupSelect01">Options</label>
                    <select name="relation" class="form-select" id="inputGroupSelect01">
                        <option selected>Choose...</option>
                        <option value="parent">Parent</option>
                        <option value="sibling">Frère ou sœur</option>
                        <option value="conjoint">Conjoint</option>
                        <option value="conjointe">Conjointe</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary" name ="ajout_relation">Ajout Rélation</button>
            </div>
            <div class="col-md">
                <h4>Sélectionner la deuxième personne</h4>
                <select name="person2" class="form-select" size="5" aria-label="Sélectionner un descendant">';

    $sql = "SELECT * FROM `gen_personnes` ORDER by noGeneologique";
    try {
        $conn = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET, DB_USER, DB_PASSWORD);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $list = $conn->query($sql);
        if ($list) {
            while ($row = $list->fetch()) {

                $noGeneologique = $row['noGeneologique'];
                $nom = $row['nom'];
                $prenom = $row['prenom'];
                $person = "$noGeneologique  $nom $prenom";
                $content .= '<option value="' . $noGeneologique . '">' . $person . '</option>"<br />';
            }
        } else {
            $content .= "pas de personnes à sélectionner ";
        }
    } catch (\Throwable $e) {
        echo "there was an error while selecting from gen_personnes" . $e;
    }
    $conn = null;
    $content .= '
                </select>
            </div>
            </div>
        </div>
        </form>';
    //verify that there is no relationship already

    $person1 = $_POST['person1'];
    $person2 = $_POST['person2'];
    $relation = $_POST['relation'];

    if ($relation == 'parent') {
        $id_parent = $person1;
    } elseif ($relation == 'conjointe') {
        $id_epouse = $person2;
    } elseif ($relation == 'conjoint') {
        $id_epous = $person1;
    }
    if (isset($_POST['ajout_relation'])) {
        try {
            // Establish a new PDO database connection
            $conn = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET, DB_USER, DB_PASSWORD);

            // Set PDO to throw exceptions on error
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // Prepare the SQL query with placeholders for the values
            $sql = "INSERT INTO `gen_relation` (`type_relation`, `id_parent`, `id_epouse`, `id_epous`, `persones_idpersones`) 
                        VALUES (:type_relation, :id_parent, :id_epouse, :id_epous, :persones_idpersones)";

            // Prepare the SQL statement
            $stmt = $conn->prepare($sql);

            // Bind the values to the placeholders
            $stmt->bindValue(':type_relation', $relation);
            $stmt->bindValue(':id_parent', $id_parent);
            $stmt->bindValue(':id_epouse', $id_epouse);
            $stmt->bindValue(':id_epous', $id_epous);
            $stmt->bindValue(':persones_idpersones', $person1);


            // Execute the statement
            $stmt->execute();
            $idrelation = $conn->lastInsertId();

            echo "Firts person ID =" . $person1;

            // Prepare the SQL query to update the gen_personnes table
            $sql2 = "UPDATE `gen_personnes` SET `relation_id_relation`= :relation_id_relation WHERE `noGeneologique` = :person1 OR `noGeneologique` = :person2";

            // Prepare the SQL statement
            $stmt2 = $conn->prepare($sql2);

            // Bind the values to the placeholders
            $stmt2->bindValue(':noGeneologique', $person1);
            $stmt2->bindValue(':noGeneologique', $person2);
            $stmt2->bindValue(':relation_id_relation', $idrelation);

            // Execute the statement
            $stmt2->execute();
            $conn = null;
        } catch (\Throwable $e) {
            // Return an error message with the exception details
            return "There was an error while adding the relation: " . $e->getMessage();
        }
    }



    return $content;
}
add_shortcode('add_relations', 'add_relations');

