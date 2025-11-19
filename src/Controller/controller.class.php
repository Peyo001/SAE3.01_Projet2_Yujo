<?php
class Controller
{
    /**
     * @brief Objet représentant la connexion à la base de données.
     */
    private PDO $pdo;

    /**
     * @brief Objet permettant de charger les templates Twig
     */
    private \Twig\Loader\FilesystemLoader $loader;

    /**
     * @brief Objet représentant l'environnement Twig
     */
    private \Twig\Environment $twig;

    /**
     * @brief Données récupérées via le protocole GET
     */
    private ?array $get = null;

    /**
     * @brief Données récupérées via le protocole POST
     */
    private ?array $post = null;


    /**
     * @brief Constructeur de la classe Controller.
     *
     * @param \Twig\Loader\FilesystemLoader $loader Chargeur des templates Twig.
     * @param \Twig\Environment $twig Environnement Twig.
     */
    public function __construct(\Twig\Loader\FilesystemLoader $loader, \Twig\Environment $twig)
    {
        $db = Database::getInstance();
        $this->pdo = $db->getConnection();

        $this->loader = $loader;
        $this->twig = $twig;

        if (isset($_GET) && !empty($_GET))
        {
            $this->get = $_GET;
        }

        if (isset($_POST) && !empty($_POST))
        {
            $this->post = $_POST;
        }
    }

    /**
     * @brief Appelle une méthode du contrôleur.
     *
     * @param string $methode Nom de la méthode à appeler.
     * @return mixed Résultat de l'appel de la méthode.
     * @throws Exception Si la méthode n'existe pas dans le contrôleur. 
     */
    public function call(string $methode): mixed
    {
        //teste si la methode existe
        if (!method_exists($this, $methode))
        {
            throw new Exception("La methode $methode n'existe pas dans la controleur __CLASS__");
        }

        return $this->$methode();
    }

    /**
     * @brief  Récupère la connexion PDO.
     *
     * @return PDO|null Connexion PDO.
     */
    public function getPdo(): ?PDO
    {
        return $this->pdo;
    }

    /**
     * @brief  Définit la connexion PDO.
     *
     * @param PDO|null $pdo Connexion PDO.
     * @return void
     */
    public function setPdo(PDO $pdo): void
    {
        $this->pdo = $pdo;
    }

    /**
     * @brief Récupère le chargeur de templates Twig.
     *
     * @return \Twig\Loader\FilesystemLoader Chargeur de templates Twig.
     */
    public function getLoader(): \Twig\Loader\FilesystemLoader
    {
        return $this->loader;
    }

    /**
     * @brief Définit le chargeur de templates Twig.
     *
     * @param \Twig\Loader\FilesystemLoader $loader Chargeur de templates Twig.
     * @return void
     */
    public function setLoader(\Twig\Loader\FilesystemLoader $loader): void
    {
        $this->loader = $loader;
    }

    /**
     * @brief Récupère l'environnement Twig.
     *
     * @return \Twig\Environment Environnement Twig.
     */
    public function getTwig(): \Twig\Environment
    {
        return $this->twig;
    }

    /**
     * @brief Définit l'environnement Twig.
     *
     * @param \Twig\Environment $twig Environnement Twig.
     * @return void
     */
    public function setTwig(\Twig\Environment $twig): void
    {
        $this->twig = $twig;
    }

    /**
    /**
     * @brief Retourne les données transmises via le protocole GET.
     *
     * @return array Données GET.
     */
    public function getGet(): array
    {
        return $this->get;
    }

    /**
     * @brief Mémorise les données transmises via le protocole GET.
     *
     * @param array $get Données GET.
     * @return void
     */
    public function setGet(array $get): void
    {
        $this->get = $get;
    }

    /**
     * @brief Retourne les données transmises via le protocole POST.
     *
     * @return array Données POST.
     */
    public function getPost(): array
    {
        return $this->post;
    }

    /**
     * @brief Mémorise les données transmises via le protocole POST.
     *
     * @param array $post Données POST.
     * @return void
     */
    public function setPost(array $post): void
    {
        $this->post = $post;
    }
}
