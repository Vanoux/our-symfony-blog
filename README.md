Avec Symfony, créez un blog sur lequel il sera possible de :
* créer/modifier/supprimer/commenter des entrées de blog,
* attribuer des étiquettes à chaque entrée de blog,
* n'afficher que les entrées de blog correspondant à une étiquette au choix. 

Bonus : 
* Mettez en place la gestion des utilisateurs sur le blog : https://github.com/FriendsOfSymfony/FOSUserBundle
* Seul l'administrateur et l'auteur d'une entrée de blog peuvent modifier et supprimer celle-ci.


posts
id PRIMARY KEY
title VARCHAR
author VARCHAR
content VARCHAR(2000)
created_at DATETIME
updated_at DATETIME

comments
id PRIMARY KEY
post_id FOREIGN KEY
author VARCHAR
content VARCHAR(500)
created_at DATETIME

tags
???
