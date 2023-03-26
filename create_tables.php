<?/**
 * Summary.
 *
 * Description.
 *
 * @since Version 3 digits
 */
function createTables(){
    $sql_create='

    CREATE TABLE IF NOT EXISTS `gen_union` (
      `idunion` INT NOT NULL,
      `type_union` VARCHAR(45) NOT NULL,
      `date_debut` DATE NULL,
      `lieu_evenement` VARCHAR(45) NULL,
      `date_fin` DATE NULL,
      PRIMARY KEY (`idunion`))
    ENGINE = InnoDB;
    
    
    -- -----------------------------------------------------
    -- Table `relation`
    -- -----------------------------------------------------
    
    CREATE TABLE IF NOT EXISTS `gen_relation` (
      `id_relation` INT NOT NULL,
      `type_relation` VARCHAR(45) NULL,
      `id_parent` VARCHAR(45) NULL,
      `id_epouse` VARCHAR(45) NULL,
      `id_epous` VARCHAR(45) NULL,
      `persones_idpersones` INT NOT NULL,
      `persones_Source_idSource` INT NOT NULL,
      `union_idunion` INT NOT NULL,
      PRIMARY KEY (`id_relation`),
      INDEX `fk_relation_union1_idx` (`union_idunion` ASC) VISIBLE,
      CONSTRAINT `fk_relation_union1`
        FOREIGN KEY (`union_idunion`)
        REFERENCES .`union` (`idunion`)
        ON DELETE NO ACTION
        ON UPDATE NO ACTION)
    ENGINE = InnoDB;
    
    
    -- -----------------------------------------------------
    -- Table `personnes`
    -- -----------------------------------------------------
    
    CREATE TABLE IF NOT EXISTS `gen_personnes` (
      `noGeneologique` INT NOT NULL AUTO_INCREMENT,
      `nom` VARCHAR(45) NOT NULL,
      `prenom` VARCHAR(45) NOT NULL,
      `nom_jeune_fille` VARCHAR(45) NULL,
      `observation` VARCHAR(45) NULL,
      `date_naisance` VARCHAR(45) NOT NULL,
      `lieu_naissance` VARCHAR(45) NULL,
      `date_mariage` VARCHAR(45) NULL,
      `lieu_mariage` VARCHAR(45) NULL,
      `lieu_dece` VARCHAR(45) NULL,
      `note` VARCHAR(45) NULL,
      `lien_image` VARCHAR(45) NULL,
      `relation_id_relation` INT NOT NULL,
      PRIMARY KEY (`noGeneologique`),
      INDEX `fk_personnes_relation1_idx` (`relation_id_relation` ASC) VISIBLE,
      CONSTRAINT `fk_personnes_relation1`
        FOREIGN KEY (`relation_id_relation`)
        REFERENCES .`relation` (`id_relation`)
        ON DELETE NO ACTION
        ON UPDATE NO ACTION)
    ENGINE = InnoDB;
    
    
    SET SQL_MODE=@OLD_SQL_MODE;
    SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
    SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
    ';
        try {
            $conn = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET, DB_USER, DB_PASSWORD);
                $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $stmt = $conn->prepare($sql_create);
                $stmt->execute();
                $content .='<h2>tables created</h2>';//to be replaced
        } catch (PDOException $e) {
            echo 'Les tables n\'ont pas étaits crée '.$e;
        }
}