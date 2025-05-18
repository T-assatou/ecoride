# EcoRide

**Présentation**  
EcoRide est une plateforme de covoiturage écologique développée dans le cadre du Titre Professionnel Développeur Web et Web Mobile (DWWM).  
L’objectif est de réduire l’impact environnemental des déplacements en facilitant les trajets partagés.

---

## 📄 Technologies utilisées
- HTML5 / CSS3  
- JavaScript (fonctionnalités simples)  
- PHP (Back-end)  
- MySQL (Base de données relationnelle)  
- MongoDB (Base NoSQL - à titre démonstratif)  
- MAMP (environnement local sur Mac)  
- GitHub (gestion de version)

---

## 🔺 Structure du projet
```
ecoride/  
├── index.php                => Page d’accueil  
├── assets/                  => Feuilles de style, images, JS  
├── controllers/             => Fichiers PHP de traitement (authentification, réservations…)  
├── includes/                => Fichiers réutilisables (nav, footer…)  
├── models/                  => Connexion à la base de données  
├── pages/                   => Pages principales (login, user-space, admin-control…)  
├── docs/                    => Livrables PDF (charte graphique, manuel, maquettes...)  
└── README.md                => Ce fichier
```

---

## 👤 Identifiants de test
| Rôle        | Email                  | Mot de passe |
|-------------|------------------------|--------------|
| Admin       | admin2@ecoride.fr      | admin123     |
| Employé     | employe@ecoride.fr     | employe123   |
| Utilisateur | test@ecoride.fr        | 123456       |

---

## ✨ Installation en local avec MAMP (Mac)
1. Placer le dossier `ecoride` dans `/Applications/MAMP/htdocs/`
2. Démarrer MAMP (Apache + MySQL)
3. Accéder à l’application via : `http://localhost:8888/ecoride/`
4. Importer le fichier SQL dans phpMyAdmin : `localhost:8888/phpMyAdmin`
5. Créer une base `ecoride` et importer le fichier `ecoride.sql`
6. Connectez-vous avec les identifiants fournis ci-dessus

---

## 🎯 Fonctionnalités principales
- Page d’accueil avec présentation + recherche
- Authentification / Création de compte utilisateur
- Espace personnel (choix du rôle, ajout de véhicules)
- Saisie de trajets par les chauffeurs
- Participation aux trajets pour les passagers
- Historique des trajets (avec annulation possible)
- Gestion des avis et litiges par les employés
- Dashboard admin (statistiques, crédits)
- Suspension/réactivation de comptes par l’admin

---

## 🔗 Liens importants
- 🌐 Site déployé : *à compléter*  
- 📚 GitHub public : [https://github.com/T-assatou/ecoride](https://github.com/T-assatou/ecoride)  
- 📊 Trello : https://trello.com/invite/b/682745285dafbdc8c849226f/ATTIdbc67b9e5a38aa0137a4dd34260df565F0E872EC/ecoride

---

## 📆 Stratégie Git utilisée
- `main` : branche stable (version finale)
- `develop` : branche d’intégration en cours de test
- `feature/*` : chaque fonctionnalité a sa propre branche
- ✅ Merge vers `develop` puis `main` une fois testée et validée

---

## 🚧 Justifications techniques
- **PHP/MySQL** : technologie demandée par le sujet + compatible avec MAMP
- **Pas de framework JS** pour rester dans le niveau débutant
- **MongoDB** : ajout démonstratif en complément relationnel
- **Charte graphique verte + pastel** pour cohérence écologique

---

## 📁 Contenu de la base de données (ecoride.sql)
- Création des tables `users`, `rides`, `vehicles`, `participants`, `avis`, `litiges`
- Contraintes d’intégrité + ON DELETE CASCADE
- Insertion de comptes de test (admin, employé, utilisateur)

---

## 📅 Livrables complémentaires (dans `/docs/`)
- `charte_graphique_ecoride.pdf`
- `manuel_utilisation_ecoride.pdf`
- `maquettes_ecoRide.pdf` (3 desktop + 3 mobile)
- `documentation_projet.pdf` (Trello, gestion Git)
- `documentation_technique.pdf` (diagrammes, configuration, sécurité)

---

## ✅ Notes utiles
- Tous les mots de passe sont hachés avec `password_hash()`
- Un mot de passe sécurisé est exigé à l'inscription : 9 caractères min, avec majuscule, minuscule et chiffre
- La plateforme prélève 2 crédits par covoiturage pour les frais de service

---

## 👨‍💻 Auteur
Projet réalisé par **T-assatou** dans le cadre du TP DWWM - Studi.  
Merci à toute l'équipe pédagogique !
