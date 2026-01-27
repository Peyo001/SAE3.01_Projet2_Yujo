<?php
/**
 * Classe ObjetRoom
 * 
 * Cette classe représente un objet 3D positionné dans une room.
 * Elle étend la classe Objet en ajoutant les propriétés de position, rotation et échelle.
 * 
 * Exemple d'utilisation :
 * $objetRoom = new ObjetRoom(1, 'Chaise en bois', '/models/chaise.obj', 100, 5, 2.5, 0.0, 1.0, 0, 45, 0, 1.2);
 * echo $objetRoom->getPositionX(); // Affiche 2.5
 */

class ObjetRoom extends Objet {
    // Position de l'objet dans l'espace 3D
    private float $positionX;
    private float $positionY;
    private float $positionZ;
    
    // Rotation de l'objet (en degrés)
    private float $rotationX;
    private float $rotationY;
    private float $rotationZ;
    
    // Échelle de l'objet
    private float $scale;
    
    // Date d'ajout dans la room
    private ?string $dateAjout;

    /**
     * Constructeur de la classe ObjetRoom.
     * 
     * @param ?int $idObjet Identifiant de l'objet
     * @param ?string $description Description de l'objet
     * @param ?string $modele3dPath Chemin vers le modèle 3D
     * @param ?int $prix Prix de l'objet
     * @param ?int $idRoom Identifiant de la room
     * @param float $positionX Position X (défaut: 0.0)
     * @param float $positionY Position Y (défaut: 0.0)
     * @param float $positionZ Position Z (défaut: 0.0)
     * @param float $rotationX Rotation X en degrés (défaut: 0.0)
     * @param float $rotationY Rotation Y en degrés (défaut: 0.0)
     * @param float $rotationZ Rotation Z en degrés (défaut: 0.0)
     * @param float $scale Échelle de l'objet (défaut: 1.0)
     * @param ?string $dateAjout Date d'ajout dans la room
     */
    public function __construct(
        ?int $idObjet,
        ?string $description,
        ?string $modele3dPath,
        ?int $prix,
        ?int $idRoom,
        ?string $image = null,
        float $positionX = 0.0,
        float $positionY = 0.0,
        float $positionZ = 0.0,
        float $rotationX = 0.0,
        float $rotationY = 0.0,
        float $rotationZ = 0.0,
        float $scale = 1.0,
        ?string $dateAjout = null
    ) {
        parent::__construct($idObjet, $description, $modele3dPath, $prix, $idRoom, $image);
        $this->positionX = $positionX;
        $this->positionY = $positionY;
        $this->positionZ = $positionZ;
        $this->rotationX = $rotationX;
        $this->rotationY = $rotationY;
        $this->rotationZ = $rotationZ;
        $this->scale = $scale;
        $this->dateAjout = $dateAjout;
    }

    // Getters pour la position
    public function getPositionX(): float { return $this->positionX; }
    public function getPositionY(): float { return $this->positionY; }
    public function getPositionZ(): float { return $this->positionZ; }
    
    /**
     * Récupère la position sous forme de tableau associatif.
     * 
     * @return array ['x' => float, 'y' => float, 'z' => float]
     */
    public function getPosition(): array {
        return [
            'x' => $this->positionX,
            'y' => $this->positionY,
            'z' => $this->positionZ
        ];
    }

    // Getters pour la rotation
    public function getRotationX(): float { return $this->rotationX; }
    public function getRotationY(): float { return $this->rotationY; }
    public function getRotationZ(): float { return $this->rotationZ; }
    
    /**
     * Récupère la rotation sous forme de tableau associatif.
     * 
     * @return array ['x' => float, 'y' => float, 'z' => float]
     */
    public function getRotation(): array {
        return [
            'x' => $this->rotationX,
            'y' => $this->rotationY,
            'z' => $this->rotationZ
        ];
    }

    // Getter/Setter pour l'échelle
    public function getScale(): float { return $this->scale; }
    public function setScale(float $scale): void { $this->scale = $scale; }

    // Getter/Setter pour la date d'ajout
    public function getDateAjout(): ?string { return $this->dateAjout; }
    public function setDateAjout(?string $dateAjout): void { $this->dateAjout = $dateAjout; }

    // Setters pour la position
    public function setPositionX(float $x): void { $this->positionX = $x; }
    public function setPositionY(float $y): void { $this->positionY = $y; }
    public function setPositionZ(float $z): void { $this->positionZ = $z; }
    
    /**
     * Définit la position complète de l'objet.
     * 
     * @param float $x Position X
     * @param float $y Position Y
     * @param float $z Position Z
     */
    public function setPosition(float $x, float $y, float $z): void {
        $this->positionX = $x;
        $this->positionY = $y;
        $this->positionZ = $z;
    }

    // Setters pour la rotation
    public function setRotationX(float $x): void { $this->rotationX = $x; }
    public function setRotationY(float $y): void { $this->rotationY = $y; }
    public function setRotationZ(float $z): void { $this->rotationZ = $z; }
    
    /**
     * Définit la rotation complète de l'objet.
     * 
     * @param float $x Rotation X en degrés
     * @param float $y Rotation Y en degrés
     * @param float $z Rotation Z en degrés
     */
    public function setRotation(float $x, float $y, float $z): void {
        $this->rotationX = $x;
        $this->rotationY = $y;
        $this->rotationZ = $z;
    }

    /**
     * Convertit l'objet en tableau associatif pour JSON.
     * Utile pour passer les données à JavaScript/Three.js.
     * 
     * @return array Données de l'objet au format tableau
     */
    public function toArray(): array {
        return [
            'idObjet' => $this->getIdObjet(),
            'description' => $this->getDescription(),
            'modele3dPath' => $this->getModele3dPath(),
            'prix' => $this->getPrix(),
            'position' => $this->getPosition(),
            'rotation' => $this->getRotation(),
            'scale' => $this->scale,
            'dateAjout' => $this->dateAjout
        ];
    }
}
?>
