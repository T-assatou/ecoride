# EcoRide

🌱 **Présentation**  
EcoRide est une plateforme de covoiturage écologique développée dans le cadre du Titre Professionnel Développeur Web et Web Mobile (DWWM).  
L’objectif est de réduire l’impact environnemental des déplacements en facilitant les trajets partagés.

---

🚀 **Technologies utilisées**

- HTML5 / CSS3  
- JavaScript (fonctionnalités simples)  
- PHP (Back-end)  
- MySQL (Base de données relationnelle)  
- MongoDB (Base NoSQL - à titre démonstratif)  
- MAMP (environnement local sur Mac)  
- GitHub (gestion de version)

---

📁 **Structure du projet**

ecoride/  
├── index.php                => Page d’accueil  
├── assets/                  => Feuilles de style, images, JS  
├── controllers/             => Fichiers PHP de traitement (authentification, réservations…)  
├── includes/                => Fichiers réutilisables (nav, footer…)  
├── models/                  => Connexion à la base de données  
├── pages/                   => Pages principales (login, user-space, admin-control…)  
└── README.md                => Ce fichier

---

🧪 **Identifiants de test**

- **Utilisateur**  
  Email : `test@ecoride.fr`  
  Mot de passe : `123456`  

- **Employé**  
  Email : `employe@ecoride.fr`  
  Mot de passe : `employe123`  

- **Administrateur**  
  Email : `admin@ecoride.fr`  
  Mot de passe : `admin123`

---

⚙️ **Installation en local avec MAMP (Mac)**

1. Placer le dossier `ecoride` dans `/Applications/MAMP/htdocs/`
2. Démarrer MAMP (Apache + MySQL)
3. Accéder à l’application via : `http://localhost:8888/ecoride/`
4. Importer le fichier SQL dans phpMyAdmin (`localhost:8888/phpMyAdmin`)
5. Se connecter avec les identifiants de test ci-dessus

---

✅ **Fonctionnalités réalisées**

- Page d’accueil
- Authentification utilisateur
- Création de compte
- Espace personnel (choix du rôle chauffeur/passager)
- Ajout de véhicules
- Création de trajets
- Réservations
- Annulations
- Espace employé (validation d’avis, gestion litiges)
- Espace administrateur (statistiques, suspension de comptes)

---

🧠 **Auteur**

Projet réalisé par **T-assatou** dans le cadre du TP DWWM - Studi.