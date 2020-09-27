# Pokedex contexte

La liste des Pokémons est inventoriée dans ce repository :
https://gist.github.com/armgilles/194bcff35001e7eb53a2a8b441e8b2c6

Le code doit se baser sur les technologies suivantes.
    -   MySql
    -   Symfony
    -   FosRestBundle
    -   JMS Serializer

Le test sera noté sur la qualité du code, le respect des bonnes pratiques, la stabilité et
le bon usage des technologies mentionnées ci dessus.

Seule une API est attendue, il n’y a donc pas d’interface graphique à prévoir. 

Le code doit être livré sur un repo GIT et accompagné de sa procédure de déploiement.

# Généralités

    - L'API est basée sur la version de Symfony 4.18.x
    - L'API est sécurisée via le bundle JWT (https://github.com/lexik/LexikJWTAuthenticationBundle)
    - Les routes sont gérées par le bundle FosRestBundle (https://github.com/FriendsOfSymfony/FOSRestBundle)
    - Les résultats sont envoyés via JMS Serializer (https://github.com/schmittjoh/JMSSerializerBundle)

# Pré-requis

    - Serveur Http (Apache2, Nginx, ...)
    - Symfony CLI (https://symfony.com/download)
    - Php >= 7.1.3
    - MySql >= 8.0
    - composer (https://getcomposer.org/download/)
    - Postman (https://www.postman.com/downloads)
    - Git (https://git-scm.com/download/linux)

# Installation [LOCAL]

    - Cloner le projet depuis GIT 
    - Exécuter depuis le dossier pokedex
        -   composer install
    - Executer les fixtures depuis le dossier pokedex
        -   php bin/console doctrine:fixtures:load
    - Démarrer le serveur depuis le dossier pokedex
        -   symfony server:start
    - Par défaut, le port 8000 est utilisé par le serveur - Apache2 dans mon cas -, http://127.0.0.1:8000/ doit alors être accessible.

# Tester l'application [LOCAL]

Pour tester l'application dans les meilleurs conditions, il est préférable d'installer POSTMAN et d'y importer la collection ainsi que la configuration des variables d'environnement disponible dans le dossier 'sample' (pokedex.postman_collection.json & pokedex.postman_environment.json) notamment pour avoir les payloads attendus par l'API, pouvoir se baser dessus et aussi de profiter de l'authentification.

    - Importer les fichiers POSTMAN 
        -   sample/pokedex.postman_environment.json
        -   sample/pokedex.postman_collection.json
    - Générer un Token JWT
        -   POST http://127.0.0.1:8000/api/login_check 
        Ce dernier sera partagé aux autres endpoint de l'API selon sa date de validité. Pour le moment, il n'y a qu'un seul utilisateur (rcolboc.dev@gmail.com/rco), vous pouvez néanmoins modifier le fichier /sample/user.csv et y ajouter
        un utilisateur. Le TTL du token est de 3600s.
        Pour des questions évidentes de sécurité, le mot de passe attendu doit être crypté, SF propose cette fonctionnalitée:
        -   php bin/console security:encode-password 'MON_SUPER_MOT_DE_PASSE_CRYPTE'
        -   Ensuite il suffit de relancer le chargement des fixtures afin de les renseigner à SF
            -   php bin/console doctrine:fixtures:load
    - Lister les types de Pokemon
        -   GET http://127.0.0.1:8000/api/pokedex/types
    - Créer un nouveau Pokemon
        -   PUT http://127.0.0.1:8000/api/pokedex/pokemon
    - Modifier un Pokémon
        -   PATCH http://127.0.0.1:8000/api/pokedex/pokemon
    - Supprimer un Pokémon
        -   DELETE http://127.0.0.1:8000/api/pokedex/pokemon
    - Rechercher les informations d'un Pokémon
        -   GET http://127.0.0.1:8000/api/pokedex/pokemon
    - Rechercher une liste de Pokemons selon plusieurs filtres
        -   GET http://127.0.0.1:8000/api/pokedex/pokemon/search
            -   Paginer (60 items/page)
            -   Trier par Nom croissant et décroissant
            -   Filtrer via une recherche fulltext
            -   Filtrer par type de Pokémon (ice, water, dragon, steel...)

# Gestion des erreurs

L'API Pokedex gère les erreurs afin de ne pas polluer la BDD de données inutiles. L'utilisateur de l'API est alors notifié du problème et recevra un code HTTP ainsi qu'un rapport d'erreur - au format JSON - concernant les problèmes rencontrés.
