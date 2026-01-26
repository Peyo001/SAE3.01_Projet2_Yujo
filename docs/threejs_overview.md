# Three.js dans la Room 3D

## Vue rapide
- La scène est rendue dans `room_threejs_new.twig` via `<canvas id="canvas">`.
- Données d'objets injectées par Twig (`objetsPlaces`, `objetsDisponibles`) depuis `ControllerRoom`.
- Chaque objet porte `modele3dPath` (GLB/OBJ), position, rotation, échelle.
- Les actions (ajout, sauvegarde, retrait) passent par des endpoints AJAX et sont persistées en base via `RoomDao` / `ObjetRoom`.

## Pipeline de rendu
1) **Scène / Caméra / Renderer**
   - `THREE.Scene` avec fond #0a0e27.
   - `PerspectiveCamera` (75°, near 0.1, far 1000), position (12,8,12), lookAt(0,0,0).
   - `WebGLRenderer` (antialias, shadowMap). Taille = viewport, pixelRatio = devicePixelRatio.

2) **Lumières**
   - `AmbientLight` (0xffffff, 0.6) pour un éclairage global doux.
   - `DirectionalLight` (0xffffff, 0.8) placé en (10,15,10), ombres activées (map 1024²).

3) **Géométrie statique**
   - Sol : `PlaneGeometry(10,10)`, rotation -90°, reçoit les ombres.
   - Murs : deux `BoxGeometry` pour le mur gauche et arrière, couleur 0x2a2a4e.

4) **Chargement des objets dynamiques** (`createObjet3D`)
   - Détermine l'extension :
     - `.glb/.gltf` → `GLTFLoader` (+ `DRACOLoader` si disponible).
     - `.obj` → `OBJLoader`.
     - Sinon : fallback cube.
   - Applique position, rotation, échelle (réduction par défaut x0.01), cast/receiveShadow.
   - Ajoute l'objet à `scene` et au tableau `objets` pour le raycasting.

5) **Boucle d'animation**
   - `requestAnimationFrame` : met à jour FPS, affiche position/rotation de l'objet sélectionné, gère zoom progressif, puis `renderer.render(scene,camera)`.

## Contrôles
- **Souris**
  - Clic : sélection via raycaster (inclut enfants GLB), highlight emissive.
  - Drag sans sélection : orbite caméra (angles clampés).
  - Drag avec sélection : move (translation) ou rotate (mode toggle).
  - Molette : zoom (distance caméra clampée 5–30).
- **Clavier**
  - `R` : bascule move/rotate.
- **UI**
  - Top bar : reset cam, toggle rotation, save, delete, retour.
  - Panneau gauche : objets achetés dispos + liste des objets placés.
  - Panneau droit : mode, compteur, FPS, position/rotation, aide.

## Cycle de vie des objets
- Chargement initial : `objetsPlaces` (Twig) → `createObjet3D` pour chaque.
- Ajout (`addObjetToRoom`) : POST vers `ajouterObjetDansRoom`, création dans la scène, puis reload pour rafraîchir les listes.
- Déplacement/Rotation : manipulé en front; l'état courant est dans `selected.position` et `selected.userData.rot*`.
- Sauvegarde (`saveRoom`) : POST vers `updateObjetPosition` avec pos/rot/scale.
- Retrait (`clearSelectedObject`) : POST vers `retirerObjetDeRoom`, suppression scène + reload.

## Chemins d'assets 3D
- Les fichiers sont servis depuis `public/3d/`. Exemple : `public/3d/gaming_chairs.glb` avec chemin stocké `/3d/gaming_chairs.glb`.
- Vérifier que le vhost pointe bien sur `public/`. Tester l'URL directe en cas de 404.

## Ajuster l'échelle
- Facteur par défaut dans `createObjet3D` : `finalScale = (size || 1.0) * 0.01`.
- Modèles trop grands/petits : ajuster `scale` en base pour l'objet ou modifier ce facteur dans le Twig.

## Débogage rapide
- Console : logs `[Loader]` montrent le chemin et les erreurs de chargement.
- 404 : vérifier présence du fichier dans `public/3d/` et le chemin en base.
- Draco : loader pointe sur `https://www.gstatic.com/draco/v1/decoders/`, rien à héberger localement.
