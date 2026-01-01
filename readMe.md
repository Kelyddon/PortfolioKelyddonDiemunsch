installation des composant de symfony 
   composer install

création de la base de donnée
   php bin/console doctrine:database:create
   php bin/console doctrine:migrations:migrate

Mettre en place le compte admin
   php bin/console doctrine:fixtures:load

lancer le serveur
   symfony serve

Lien pour voir la page
https://127.0.0.1:8000/

Lien pour accéder à la page admin
https://127.0.0.1:8000/adminaccess

Code pour l'admin:
Email: admin@example.com
Mtp: ChangeMe!123
