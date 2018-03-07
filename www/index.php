<?php
//on definit le chemin de notre application

define('APPLICATION_PATH', realpath(dirname(__FILE__). '/../'));

//on definit le chemin de notre librairie
$library=APPLICATION_PATH. '/library';
set_include_path($library. PATH_SEPARATOR. get_include_path());

//la classe zend_loader_autoloader inclut des methodes afin de nous aider a charger des fichiers automatiquement


require_once 'Zend/Loader/Autoloader.php';
$loader=Zend_Loader_Autoloader::getInstance();
$loader->registerNamespace('App_');

//on charge les classes a utiliser;
Zend_Loader::loadClass('Zend_Controller_Front');


/* on peut charger les classes a utiliser manuellement, dans ce cas on a pas besion de Zed_Loader_Autoloader*/

$configpath='../www/config/config.ini';

//creation d'un objet a partir du fichier ini
$config=new Zend_Config_Ini($configpath,'dev');


//construction d'un objet $db permettant d'utiliser la base de donnees

$db=Zend_Db::factory($config->database);

//on force l'adaptateur a se connecter au sgbd grace a la methode getConnection()
//on aura un objet pdo en cas de succes

$db->getConnection();

//on enregistre l'objet dans un registre pour qu'il soit utiliser partout dans l'application(variable globale)

Zend_Registry::set('db',$db);

//base de donnees par defaut

Zend_Db_Table::setDefaultAdapter($db);



try{
    //getInstance() est utilise pour recuperer une instance du controleur frontal
        $front=Zend_Controller_Front::getInstance();

        //le controleur frontal renvoie les exceptions qu'il a rencontrees
    //a l'objet de reponse, nous offrant une possibilite elegante de les gerer
        $front->throwExceptions(true);
        //setControllerDirectory() est utilise pour chercher les
    //fichiers de classes de controleurs
    // d'actions
    $front->setControllerDirectory(APPLICATION_PATH. '/application/controllers');

    //Dispatch lance notre apllication, fait le gros travail du controleur frontal
    //Il peut facultativement prendre un objet de requete et/ou un objet de reponse,
    //permettant ainsi au developpeur de fournir des objets personalises.
    $front->dispatch();
    //Triate les exceptions du controlleur (generalement 404)

}catch (Zend_Controller_Exception $e)
{
   echo  $e->getMessage();
    //traite les autres exceptions du controleur
}catch (Exception $e){
    include 'errors/500.phtml';
}

?>