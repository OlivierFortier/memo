<script>

    
    if(typeof window.history.pushState == 'function') {
        window.history.pushState({}, "Hide", '<?php echo $_SERVER['PHP_SELF'];?>');
    }

</script>

<?php

/*************************************************
 * 
 *   0) Récupérer les tâches du fichier de données
 * 
 * **********************************************/

$tachesFichier = "data/memo.json";
$tachesJSON = file_get_contents($tachesFichier);


$tachesArray = json_decode($tachesJSON, true);
$tachesFilter = $tachesArray;


 

/***********************
 *
 *  1) Ajouter une tâche
 * 
 * ********************/



if (isset($_POST["texteTache"])) {
    $texte = $_POST["texteTache"];

    
    $idTache = uniqid();
    

    
    $dateHeureTache = gmdate('Y-m-d\TH:i:s.v\Z');
   

    
    
    $tachesArray[$idTache] = [
        "texte" => $texte,
        "accomplie" => false,
        "dateAjout" => $dateHeureTache,
    ];

    

    
    $tachesJSON = json_encode($tachesArray);

    
    file_put_contents($tachesFichier, $tachesJSON);
}


/*************************************************************
 *
 *  2) Afficher les tâches : Voir ci-dessous dans le code HTML
 * 
 * **********************************************************/


/************************
 * 
 *  3) Filtrer les tâches
 * 
 * *********************/




if (isset($_GET["action"]) && $_GET["action"] == "filtrer") {
    
    
    
    
    

    
    if(isset($_GET["accomplie"]) && $_GET["accomplie"]==="1"){
       $tachesArray=array_filter($tachesArray,function($p){
           return ($p["accomplie"] == true);
       });
    }
    if(isset($_GET["accomplie"]) && $_GET["accomplie"]==="0"){
        $tachesArray=array_filter($tachesArray,function($p){
            return ($p["accomplie"] == false);
        });
     }
}

/*********************************
 *  
 *  4) Basculer l'état d'une tâche
 * 
 * ******************************/


if (isset($_GET["action"]) && $_GET["action"]=="basculer" && isset($_GET["id"])) {
    
    $tachesArray[$_GET["id"]]["accomplie"] = !$tachesArray[$_GET["id"]]["accomplie"];
    
    
    $tachesJSON = json_encode($tachesArray);

    
    file_put_contents($tachesFichier, $tachesJSON);
}

if (isset($_GET["action"]) && $_GET["action"]=="supprimer" && isset($_GET["id"])) {
    
    unset($tachesArray[$_GET["id"]]);
    
    
    $tachesJSON = json_encode($tachesArray);

    
    file_put_contents($tachesFichier, $tachesJSON);
}

?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>MEMO | Liste de tâches à compléter</title>
    <meta name="description" content="Application Web de gestion de tâches à compléter.">
    <link rel="stylesheet" href="ressources/css/styles.css">
</head>

<body>
    <div class="conteneur">
        <a href="index.php">
            <h1>MEMO</h1>
        </a>
        <form method="post" autocomplete="off">
            <input autofocus class="quoi-faire" type="text" name="texteTache" placeholder="Tâche à accomplir ...">
        </form>
        <div class="filtres">
            <!-- Les liens suivants permettent de filtrer les tâches -->
            <a href="index.php?action=filtrer&accomplie=1">Complétées</a>
            <a href="index.php?action=filtrer&accomplie=0">Non-complétées</a>
            <a href="index.php">Toutes</a>
        </div>
        <ul class="liste-taches">
            <!-- 
            Utilisez les éléments LI suivants comme gabarits pour l'affichage
            des "tâches".
            
            Remarquez la présence de la classe "accomplie" sur l'élément LI pour le montrer 
            biffé (complété) ou non (dépend de la valeur du champ "accomplie" dans le fichier JSON).
            -->

            <?php
            foreach ($tachesArray as $idTache => $infoTache) :

                ?>

                <li class="<?= ($infoTache["accomplie"] === true)?"accomplie":""; ?>">
                    <span class="coche done"><a href="?action=basculer&id=<?= $idTache; ?>" title="Cliquez pour faire basculer l'état de cette tâche."><img src="ressources/images/coche.svg" alt=""></a></span>
                    <span class="texte"><?= $infoTache["texte"]; ?></span>
                    <span class="ajout"><?= $infoTache["dateAjout"]; ?></span>
                    <span class="coche"><a href="?action=supprimer&id=<?= $idTache; ?>" title="Cliquez pour supprimer cette tâche."><img src="ressources/images/delete.svg" alt=""></a></span>
                </li>


            <?php endforeach; ?>

        </ul>
    </div>
</body>

</html>