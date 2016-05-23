# Projet en équipe
Projet en équipe Webforce3

## Contributeurs :
Carine, Hugues, Thibaut, Alexandre, Brice

## Notes :

### Carine / Thibaut

* design général
* formulaires (parties client / admin)
* affichage des recettes (parties client / admin)
* affichage des utilisateurs (partie admin)
* affichage des messages de contact (partie admin)
* affichage des informations du restaurant dans un formulaire (partie admin)

### Brice / Hugues / Alexandre

* gestion des utilisateurs (Brice)
* gestion des recettes (Brice)
* gestion des informations du site (Hugues)
* gestion de la page de login / récupération de mot de passe (Hugues)
* gestion des messages de contact (Alexandre)
* gestion de la base de donnée (Alexandre)

### Installation / Utilisation 

* La base de données est directement installée au lancement du site si elle n'existe pas (un fichier .sql est fourni si besoin pour importer la base).

* le compte admin par défaut est : 
	* email : lorem@ipsum.fr
	* mot de passe : password

* le site ne possède aucune recette par défaut, il faut se connecter à la partie admin pour en ajouter.

* les fichiers :
	* admin/lost_password.php (ligne 160 à 201, décommenter et ajouter les identifiants pour l'envoi de mails)
	* admin/contact_message.php (ligne 52 à 58, ajouter les identifiants pour l'envoi de mails)
