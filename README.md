# 3IL Developpement initiation Symfony

Ce ReadME a pour but de vous aider à comprendre les principes de base du framework symfony.

Certains aspects seront volontairement ignorés ou survolés afin de comprendre l'éssentiel du framework, mais si certains souhaitent aller plus loins ou mieux comprendre certains aspects je serais ravis de les aiders en ce sens par la suite.

### Prérequis
##### Techniques
* PHP 7 ou + (accessible depuis le cmd ex: ``php -v``)
* Composer
* Wamp/Xamp ou autres pour la base de donnée.
##### Connaissances
* Des bases en POO PHP (similaire au JAVA).

### Sommaire

1. Introduction Symfony / Framework
1. Installation
1. Explications commandes
1. Arboresence et structure des fichiers
1. Configuration de base
1. Premier projet

## Introduction

* **Symfony c'est quoi ?**

  « Symfony is a set of PHP Components, a Web Application framework, a Philosophy, and a Community — all working together in harmony. » - Symfony
 
 * **Pourquoi devrais-je utiliser un framework?**
 
 Un framework n'est pas absolument nécessaire: il est «juste» un des outils disponibles pour vous aider à développer mieux et plus vite!
 
 Mieux, car un framework vous donne la certitude que vous développez une application en pleine conformité avec les règles métier, structurée, à la fois maintenable et évolutive.
 
 Plus rapide, car il permet aux développeurs de gagner du temps en réutilisant des modules génériques afin de se concentrer sur d'autres domaines. Sans toutefois être lié au cadre lui-même.
 
 
 ## Installation
 
 [Documentation Installation](https://symfony.com/doc/current/setup.html)
 
 Si vous avez déjà installé Symfony sur votre PC:
 `symfony new my_project_name --full`
 
 Sinon vous avez uniquement Composer sur votre PC:
 `composer create-project symfony/website-skeleton my_project_name`
 
 Pour tester si l'installation a fonctionnée:
 `cd my-project/`
 puis
 `symfony server:start`
 
Avec Symfony < v5.x `php bin/console server:run`

## Explication Commandes

Symfony est accompagné de plusieurs bundles permettant l'utilisation de commandes pour accélérer certaines tâches.

Ces commandes commence généralement par: `php bin/console`

et peuvent être suivis de différentes commandes: ``make / doctrine`` sont les plus utilisées en générale.

_Exemple:_ ``php bin/console make:entity`` permet de générer une entitée (objet) en respectant toutes les règles de Symfony.

[Listes des commandes utiles](https://tonypayet.com/symfony-4-listing-des-lignes-de-commandes-de-base/) (ces commandes sont toujours valables pour Symfony 5)


## Arborescence et structure des fichiers 

Les dossiers principaux de votre application sont les suivant:
* config
    * Contient tout les fichiers ``.yaml`` de configuration de l'application et des bundles.
* public
    * Contient tous les assets nécéssaire (accéssible publiquement) au fontionnement du site ex: css/jss/img/...
* src
    * C'est **LE** dossier qui va contenir la casi totalitée de votre code. Il contiendrat tous vos Controllers, Entitées, Formulaires, ... mais nous reviendront sur ces notions plus tard.
* templates
    * Trés important aussi, c'est ici que les "vues" HTML seront définies.
* tests
    * Permet de définir des test unitaires, mais nous ne traiterons pas cela ici.
* vendor
    * Contient toutes les dépendances/bundles de l'application (Vous n'aurez **JAMAIS** à l'éditer).


Enfin,
* le fichier ``.env`` ou ``.env.local``
    * C'est ici que seront définie les variables d'environnement. (Ex. La connexion a la base de données, serveur mail, ...)
    
## Configuration de base

Dans le ``.env`` nous allons configurer la connexion à une base de données. Il n'est pas nécéssaire que la base est été crée au préalable car Symfony peut se charger de cela pour vous.

``DATABASE_URL=mysql://username:password@127.0.0.1:3306/db_name?serverVersion=5.7`` 

Il suffit de remplacer les champs username, password et db_name par vos identifiants d'accés à la base donnée et le nom que vous souhaitez donner à votre DB.

## Premier projet

Ce mini projet a pour but de vous montrer les principes et fonctionnement de bases de Symfony. Nous allons mettre en place un simple site disposant d'un formulaire permettant de créer des articles, puis sur une autres pages nous afficherons la listes de ces articles.

Ensuite si le temps le permet nous verons comment gérer des utilisateur trés simplement (Inscription,Login) et comment limiter l'accés à certaines pages. 
1. #### Creation du premier Controller
    Pour cela nous allons donc utiliser la commande:
              
              ``php bin/console make:controller``
1. #### Creation de l'entitée Article
    Pour cela nous allons donc utiliser la commande:
          
          ``php bin/console make:entity``
          
    Cette commande vas vous demander un nom pour votre entité suivit de la liste des attributs que vous souhaitez lui associer.
    
    Nous ajouterons donc:
    * nom (string 255)
    * date (datetime)
    * content (text)
    
    L'entité a maintenant été générée par symfony et ce trouve dans: ``src/Entity`` nous allons légérement la modifier:
    
    ````php
   public function __construct()                          //On ajoute un constructeur pour 
       {                                                   //qu'à la création de l'article 
           $this->date = new \DateTime();                  //la date soit mise à jour.
       }
   
   public function setDate(\DateTimeInterface $date): self //De même pour le setter de date.
       {
           $this->date = new \DateTime();
   
           return $this;
       }

    ````
    
    Il faut donc l'ajouter à la base de donnée pour cela:
    
    `` php bin/console make:migration``
    
    ``php bin/console doctrine:migration:migrate``
    
1. #### Creation d'un formulaire
    Pour cela nous allons donc utiliser la commande:
              
              ``php bin/console make:form``
              
    Une fois le formulaire généré nous allons l'éditer dans :``src/Form/CreateArticleType.php``
    
    ````php
        public function buildForm(FormBuilderInterface $builder, array $options)
            {
                $builder
                    ->add('name')
                    ->add('content')
                    ->add('ajouter',SubmitType::class) //On ajoute un bouton pour soumettre le formulaire
                ;
            }
    ````
    On remarqueras que la ligne ``->add('date')`` a été enlevée car celle-ci serat automatiquement générée (sans saisie utilisateur).
    
    Dans notre controller nous allons maintenant appeller notre formulaire:
    
    ```php
        //src/Controller/ArticleController
        /**
         * @Route("/article/create", name="create_article")
         */
        public function create(Request $request)
        {
            $entitymanager = $this->getDoctrine()->getManager();            //Appel du manager d'entité
            $article = new Article();                                       //Création de l'article
    
            $form = $this->createForm(CreateArticleType::class,$article);   //Appel/Création du formulaire
            $form->handleRequest($request);                                 //Récupération du formulaire
            if($form->isSubmitted() && $form->isValid()){                   //Vérification de la validité du formulaire
                $article = $form->getData();                                // Récupération des informations saisies dans le formulaire
                $entitymanager->persist($article);                          //Envoie de l'article en base de donnée
                $entitymanager->flush();                                    //Confirmation de l'envoie
            }
    
    
            return $this->render('article/create.html.twig', [
                'form'=>$form->createView(),                                 //Envoie du formulaire à la vue
            ]);
        }
    ```
    
    Puis nous allons l'afficher dans la vue ( Twig):
    
    ```twig
        {# templates/article/create.html.twig #}
        {% extends 'base.html.twig' %}
        
        {% block title %}Hello ArticleController!{% endblock %}
        
        {% block body %}
        {{ form_start(form) }}
        
        {{ form_end(form) }}
        {% endblock %}
    ```
    A présent notre formulaire est prêt à fonctionner.

1. #### Affichage des articles

    Pour cela nous allons ajouter une fonction à notre Controlleur Article ``src/Controller/ArticleController``:
    
    ````php
        /**
         * @Route("/article/show", name="show_article")    // Route de la nouvelle page
         */
        public function show()
        {
            $repository = $this->getDoctrine()->getRepository(Article::class); // On appel le gestionnaire
            $articles = $repository->findAll();                                // On fait une requête pour récupérer tous les articles
    
            return $this->render('article/show.html.twig', [                   // Nouveau template
                'articles' => $articles,
            ]);
        }
    ````
    
    Il reste maintenant à créer la vue correspondante en l'occurence ``templates/article/show.html.twig``:
    
    ````twig
    {% extends 'base.html.twig' %}
    
    {% block title %}Hello ArticleController!{% endblock %}
    
    {% block body %}
        {%  for article in articles %}
            <h4>{{ article.name }} - {{ article.date |date("m/d/Y g:ia")  }}</h4>
            <p>{{ article.content }}</p>
            <br>
            <br>
        {% endfor %}
    {% endblock %}
    ````
   [Documentation pour les boucles Twig](https://twig.symfony.com/doc/2.x/tags/for.html)
   
   [Documentation pour les formats de date Twig](https://twig.symfony.com/doc/3.x/filters/date.html)
   
   Voila vous êtes maintenant capable de gérer un systeme d'articles avec Symfony. 