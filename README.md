# 3IL Developpement initiation Symfony

Ce ReadME a pour but de vous aider à comprendre les principes de base du framework symfony.

Certains aspects seront volontairement ignorés ou survolés afin de comprendre l'essentiel du framework, mais si certains souhaitent aller plus loin ou mieux comprendre certains aspects je serai ravie de les aider en ce sens par la suite.

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
1. Sécurité, Utilisateurs et Administration

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
 
 Sinon vous avez uniquement [Composer](https://getcomposer.org/) sur votre PC:
 `composer create-project symfony/website-skeleton my_project_name`
 
 Pour tester si l'installation a fonctionné:
 `cd my-project/`
 puis
 `symfony server:start`
 
Avec Symfony < v5.x `php bin/console server:run`

Pour installer **CE** projet:
 * Télécharger le dossier depuis GitHub
 * Depuis le répertoire du projet lancer 
    ````shell script
    composer install
    php bin/console doctrine:schema:update --force
    ````
  

## Explication Commandes

Symfony est accompagné de plusieurs bundles permettant l'utilisation de commandes pour accélérer certaines tâches.

Ces commandes commence généralement par: `php bin/console`

et peuvent être suivis de différentes commandes: ``make / doctrine`` sont les plus utilisées en général.

_Exemple:_ ``php bin/console make:entity`` permet de générer une entitée (objet) en respectant toutes les règles de Symfony.

[Listes des commandes utiles](https://tonypayet.com/symfony-4-listing-des-lignes-de-commandes-de-base/) (ces commandes sont toujours valables pour Symfony 5)


## Arborescence et structure des fichiers 

Les dossiers principaux de votre application sont les suivant:
* config
    * Contient tout les fichiers ``.yaml`` de configuration de l'application et des bundles.
* public
    * Contient tous les assets nécéssaires (accéssible publiquement) au fontionnement du site ex: css/jss/img/...
* src
    * C'est **LE** dossier qui va contenir la quasi totalitée de votre code. Il contiendra tout vos Controllers, Entitées, Formulaires, ... mais nous reviendrons sur ces notions plus tard.
* templates
    * Trés important aussi, c'est ici que les "vues" HTML seront définies.
* tests
    * Permet de définir des test unitaires, mais nous ne traiterons pas cela ici.
* vendor
    * Contient toutes les dépendances/bundles de l'application (Vous n'aurez **JAMAIS** à l'éditer).


Enfin,
* le fichier ``.env`` ou ``.env.local``
    * C'est ici que seront définies les variables d'environnement. (Ex. La connexion a la base de données, serveur mail, ...)
    
## Configuration de base

Dans le ``.env`` nous allons configurer la connexion à une base de données. Il n'est pas nécéssaire que la base ait été crée au préalable car Symfony peut se charger de cela pour vous.

``DATABASE_URL=mysql://username:password@127.0.0.1:3306/db_name?serverVersion=5.7`` 

Il suffit de remplacer les champs username, password et db_name par vos identifiants d'accés à la base donnée et le nom que vous souhaitez donner à votre DB.


On génère ensuite la base de donnée (si cela n'est pas dejà fait manuellement): 

````shell script
php bin/console doctrine:database:create
````
## Premier projet

Ce mini projet a pour but de vous montrer les principes et fonctionnement de bases de Symfony. Nous allons mettre en place un simple site disposant d'un formulaire permettant de créer des articles, puis sur une autre page nous afficherons la liste de ces articles.

Ensuite si le temps le permet nous verrons comment gérer des utilisateurs très simplement (Inscription,Login) et comment limiter l'accés à certaines pages. 
1. #### Creation du premier Controller
    Pour cela nous allons donc utiliser la commande:
              
     ````shell script
     php bin/console make:controller
     ````
1. #### Creation de l'entitée Article
    Pour cela nous allons donc utiliser la commande:
          
    ````shell script
    php bin/console make:entity
   ````
          
    Cette commande va vous demander un nom pour votre entité suivi de la liste des attributs que vous souhaitez lui associer.
    
    Nous ajouterons donc:
    * nom (string 255)
    * date (datetime)
    * content (text)
    
    L'entité a maintenant été générée par symfony et se trouve dans: ``src/Entity`` nous allons légèrement la modifier:
    
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
              
   ````shell script
   php bin/console make:form
   ````
              
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
    On remarquera que la ligne ``->add('date')`` a été enlevée car celle-ci sera automatiquement générée (sans saisie utilisateur).
    
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
   
   Voila vous êtes maintenant capable de gérer un système d'articles avec Symfony. 
   
## Sécurité, Utilisateurs et Administration.

   Nous allons à présent ajouter à notre projet un systeme de gestion d'utilisateurs avec inscription, connexion, gestion des droits et restriction d'accès.
   Pour cela nous utiliserons directement le composant appelé "[Security](https://symfony.com/doc/current/security.html)" de Symfony.
   Ce composant a pour avantage d'être simple à utilisé, sécurisé, et maintenue par Symfony. Cependant il existe des bundles plus complet et plus avancés, tel que FOSUserBundle mais par conséquent il est un peut plus compliqué à déployer.
   Par la suite nous verons comment de façons très simple il nous est possible de créer un interface d'administration complète.
    
1. #### Création de la classe Utilisateur

    Pour cela on vas commencer par lancer la commande :
    `````shell script
         php bin/console make:user 
   `````
   Et enfaite... c'est tout. Voila vous avez généré votre classe utilisateur et vous pouvez lui ajouter autant de champs et méthodes que vous le souhaitez.
   Maintenant il ne reste plus qu'à envoyer tout ça en BDD.
    `````shell script
        php bin/console make:migration
        php bin/console doctrine:migrations:migrate
    `````
   Il est important de préciser que cette commande ressemble particulièrement à la commande `` make:entity ``. L'avantage ici est que Symfony reconnait que l'on souhaite utiliser le composant "Sécurité" pour gérer nos utilisateurs et vas donc faire le nécéssaire pour reconnaitre cette classe de la sorte.
   
1. #### Création du formulaire d'Inscription et de Connexion
    
    **[Formulaire de d'Inscription:](https://symfony.com/doc/4.1/doctrine/registration_form.html)**
    
    Pour générer le [formulaire d'inscription](https://symfony.com/doc/4.1/doctrine/registration_form.html) lancez la commande :
    `````shell script
       php bin/console make:registration-form
    `````
   Puis nous allons en suivant la documentation créer un Controller qui vas gérer l'inscription, voici sont code:
   `````php
   // App\Controller\RegistrationController.php
   
   namespace App\Controller;
   
   use App\Entity\User;
   use App\Form\RegistrationFormType;
   use App\Security\AppUserAuthenticator;
   use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
   use Symfony\Component\HttpFoundation\Request;
   use Symfony\Component\HttpFoundation\Response;
   use Symfony\Component\Routing\Annotation\Route;
   use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
   use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;
   
   class RegistrationController extends AbstractController
   {
       /**
        * @Route("/register", name="app_register")
        */
       public function register(Request $request, UserPasswordEncoderInterface $passwordEncoder, GuardAuthenticatorHandler $guardHandler, AppUserAuthenticator $authenticator): Response
       {
           $user = new User();
           $form = $this->createForm(RegistrationFormType::class, $user);
           $form->handleRequest($request);
   
           if ($form->isSubmitted() && $form->isValid()) {
               // encode the plain password
               $user->setPassword(
                   $passwordEncoder->encodePassword(
                       $user,
                       $form->get('plainPassword')->getData()
                   )
               );
   
               $entityManager = $this->getDoctrine()->getManager();
               $entityManager->persist($user);
               $entityManager->flush();
   
               // do anything else you need here, like send an email
   
               return $this->redirect('login');

           }
   
           return $this->render('registration/register.html.twig', [
               'registrationForm' => $form->createView(),
           ]);
       }
   }
   `````
   
   Il nous est à présent possible de s'inscrire sur notre site. (Il est possible qu'à ce point vous obteniez une erreur après l'inscription car nous avons pas encore créer la route vers laquelle le controller cherche à rediriger).
   
   **[Formulaire de Connexion:](https://symfony.com/doc/current/security/form_login_setup.html)**
   
   Pour cela on commance par lancer la commande:
   `````shell script
       php bin/console make:auth
   `````
   Cette commande a pour effet de générer le controller, le formulaireet la vue liés à la connexion (le controller ``SecurityController``).
   
   Un dossier Security a normalement du apparaitre dans le dossier src. Il est nécéssaire d'y éditer le fichier ``LoginFormAuthenticator.php``  afin d'y spécifier la route de redirèction après connexion. Pour cela il suffit de décommenter la ligne suivante:
   ````php
           return new RedirectResponse($this->urlGenerator->generate('some_route'));
    ````
   Puis de remplacer "``some_route``" par le nom de la route, dans notre cas "``home``".
  
1. #### Mise en place des rôles et gestion des accès

    [Documentation sur les rôles.](https://symfony.com/doc/current/security.html#denying-access-roles-and-other-authorization)

    Pour commencer nous allons distinguer deux catégories d'utilisateur:
        
    * Les utilisateurs anonymes:
    
        C'est à dire tous les visiteurs du site qui ne seraient pas connécté. Symfony leurs attribue le rôle "``IS_AUTHENTICATED_ANONIMOUSLY``".
    
    * Les utilisateurs (normaux):
    
        Tous ceux qui dispose d'un compte et qui ce sont connécté. Par defaut Symfony leurs attribue a tous le rôle "``ROLE_USER``".
    
    Il nous est ensuite possible de créer autant de rôle que nous le souhaitons comme par exemple le rôle "``ROLE_ADMIN``" qui comme sont nom l'indique permet de distinguer les administrateur.
    De plus un utilisteur peux avoir autant de rôle que nécéssaire.
    
    Il est possible de definir une hirarchie au sein des rôles que vous créez en suivant [cette documentation](https://symfony.com/doc/current/security.html#hierarchical-roles).
    
    Ces rôles sont à spécifier dans la table user de notre BDD et ce note par un tableau au format JSON.
    
    **Gestion des accés:**
    
    A partir de ces rôles il nous est a présent possible de restreindre l'accés a certaine partie ou fonctionnalité de notre site.
    
    Pour cela il suffit d'éditer le fichier de configuration ``App\config\packages\security.yaml``, c'est dans ce fichier que s'éffectue toute la configuration du package "Security" de Symfony.
    
    ````yaml
     access_control:
             - { path: ^/article/create, roles: ROLE_USER } 
   ````
   
   En dessous de ``acces_control:`` nous allons rajouter une ligne afin de spécifier que la route "/article/create" est uniquement accessible pour des utilisateurs connécté.
   
   A présent si vous essayez de joindre cette route sans être authentifié vous serez redirigé vers la page de connexion.
  
1. #### Création d'une interface administrateur complette

    Avec Symfony il existe une beaucoup de façon de créer une interface administrateur. 
    
    * Par exemple il est possible de créer des formulaires et des controllers dédié à chaque entité ou fonctionalité que l'on souhaite administrer. Cette méthode vous permet d'avoir un controle totale sur ce qui est réalisé, cependant elle peux s'avérer longue et fastidieuse si beaucoup de fonctionalités sont attendue.
    
    * Dans certains cas il serat beaucoup plus pratique d'utiliser des bundles dédié à l'administration. Il en existe des dixaines mais deux des plus abordable et complet sont "[EasyAdminBundle](https://symfony.com/doc/master/bundles/EasyAdminBundle/index.html)" et "[SonataBundle](https://sonata-project.org/bundles/admin/3-x/doc/index.html)".
   
   Dans notre cas nous préviligirons la deuxieme solution car plus facile a mettre en place et plus adapté à notre cas.
   
   Nous utiliserons donc "[EasyAdminBundle](https://symfony.com/doc/master/bundles/EasyAdminBundle/index.html)" car ce bundle a l'avantage d'être officiellement reconnue par Symfony et est donc documenté directement par Symfony.
   
   A partir d'ici la mise en place est extrèmement simple:
   
   - Installation du bundle:
        ````shell script
        composer require admin
        ````  
   - Configuration:
    
        Ici la configuration est simple, en installant le bundle un fichier dédié a été générer dans nos fichiers de configurations ``App\config\packages\easy_admin.yaml``.
        
        Il suffit donc à présent de spécifier qu'elles sont les entités que nous souhaitons administrer.
        Dans notre cas Article et User.
        ````yaml
        easy_admin:
            entities:
                # List the entity class name you want to manage
                - App\Entity\Article
                - App\Entity\User
        ````
      
      Voila notre interface admin est prète.
      
      En regardant de plus prêt on s'appercoie qu'un autre fichier de configuration a été créer dans ``App\config\routes\easy_admin.yaml`` c'est ici qu'il est spécifié la route d'accés a l'interface admin (Par défaut "``/admin``").
      
      On peux donc d'ors et déja la tester.
   
   **:warning: Attention :warning: A ce stade l'interface admin est accessible par n'importe quel utilisateurs même les utilisateurs anonymes, il faut donc faire le nécéssaire pour restreindre sont accès aux administrateurs seulement.**
   
## Bonus
#### :fr: Passer l'interface en Français
````yaml
#App\config\packages\translation.yaml
framework:
    default_locale: fr
    translator:
        default_path: '%kernel.project_dir%/translations'
        fallbacks:
            - fr
````
#### Customisation de l'interface EasyAdmin
   
   Avec Easy admin on peux facilement [customiser notre interface](https://symfony.com/doc/master/bundles/EasyAdminBundle/book/list-search-show-configuration.html) et les formulaires générés.
   
   ***
   <p align="center"> <b>Julien Morelli</b> 
   ![alt text][logo]
   <b>3IL Developpement</b> </p>
   
   [logo]: https://github.com/JulienMorelli/3ildeveloppement-initiation-symfony/blob/master/public/img/logo.png
   

       