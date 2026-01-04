# Portfolio Symfony – Kelyddon Diemunsch

## Installation des composant de symfony 
    composer install

### Création de la base de donnée
   1. php bin/console doctrine:database:create
   2. php bin/console doctrine:migrations:migrate

### Mettre en place le compte admin
   php bin/console doctrine:fixtures:load

### Lancer le serveur
   symfony serve

### Lien pour voir la page
https://127.0.0.1:8000/

### Lien pour accéder à la page admin
https://127.0.0.1:8000/adminaccess

### Code pour l'admin:
Email: admin@example.com
Mtp: ChangeMe!123


### Pour modifier le texte de présentation:
   1. cliquer directement dans la case afin de le modifier
   2. Puis appuyer sur le bouton Confirmer les modifications

### Pour modifier les skills:
   1. Pareil que pour le texte de présentation cliquer dessus pour les modifier
   2. Attention: si la case est vide, cela supprimera le skill de la base de donnée
   3. Appuyer ensuite sur le bouton Confirmer les modifications
   4. Pour ajouter un skill, il faut écrire le skill dans le petit formulaire puis appuyer sur ajouter un hardskill

### Pour Ajouter un projet:
   1. Dans le navigateur, il y a un accés à la page ajouter un projet
   2. 2 cases sont obligatoire à remplir: le name et le Create At

### Pour editer/supprimer un projet:
   1. Dans la page des projets, il y'a un bouton editer et un supprimer
   2. Si vous appuyer sur editer, il y aura en bas un bouton update (pour confirmer les modifications), un bouton supprimer et un bouton pour retourner à la liste des projets