# TroupTrip

L'application collaborative qui simplifie l'organisation de vos voyages en groupe.

---

## Sommaire
* [1 - À propos](#À-propos)
* [2 - Fonctionnalités clés](#Fonctionnalités-clés)
* [3 - Stack Technique](#Stack-technique)
* [4 - Installation avec Docker](#Installation-avec-Docker)
* [5 - Documentation détaillée](#Documentation-détaillée)

---

## 1 - À propos

Organiser un séjour à plusieurs peut vite devenir un casse-tête organisationnel et logistique:
- Qui vient ?
- Qui apporte quoi ?
- Qui s'occupe de réserver le gîte ?
- On fait quoi samedi après-midi ?
- Combien je dois à Alex ?

**TroupTrip** répond à toutes ces questions. En centralisant toutes les informations logistiques et organisationnelles 
de votre séjour, **TroupTrip** élimine la friction de l'organisation de vos séjours en groupe et réduit la charge mentale 
de ses participants.

---

## 2 - Fonctionnalités clés

### 2.1 - Logistique & Matériel
- **Inventaire partagé** : Listez le matériel nécessaire au séjour et aux activités (tentes, réchauds, nourriture).
- **Assignation** : Répartissez le matériel entre les membres du groupe.
- **Check List de départ** : Générez une checklist de départ pour vous assurer de ne rien oublier

### 2.2 - Organisation
- **Gestion des tâches** : Suivi des réservations et des préparatifs (système de statut Todo/Done).
- **Activités** : Organisez au mieux votre séjour dans un planning partagé.

### 2.3 - Finances & Équilibrage
- **Gestion des dépenses** : Enregistrez vos frais et sélectionnez précisément les participants concernés.
- **Simplification des dettes** : Calcule de la balance globale pour minimiser le nombre de virements nécessaires.
- **Suivi des remboursements** : Marquez les dettes comme réglées une fois le virement effectué.

---

## 3 - Stack Technique & Architecture

### 3.1 - Backend
- **Framework** : Symfony 7.4
- **API Engine** : API Platform 3.x
- **Runtime** : PHP 8.4-FPM
- **Base de données** : PostgreSQL 17
- **Outil de gestion BDD** : pgAdmin

### 3.2 - Frontend
- **Framework** : Nuxt 3
- **Modes de rendu** :
   - **Hybrid Rendering** : Utilisation du **SSR** (Server-Side Rendering) pour les routes spécifiques et du **SPA** (Single Page Application) pour les interfaces applicatives.
- **Communication** : Nuxt fait office de client pour l'API Symfony, gérant la récupération des données et le rendu final pour l'utilisateur.

---

## 4 - Installation avec Docker

Le projet est entièrement conteneurisé avec Docker pour faciliter le déploiement et garantir un environnement de développement stable.

### 4.1 - Pré-requis
- Docker Desktop
- Git
- WSL pour les systèmes fonctionnant sous Windows

### 4.2 - Lancement

1. Cloner le dépôt
git clone https://github.com/Alexandre-Mateos/Trouptrip.git
cd trouptrip

2. Configurer les variables d'environnement
cd .env

3. Lancer l'infrastructure complète:
```bash
docker compose up -d
```

4. l'application est disponible sur http://localhost:3000/ . Le swagger fournit par API platform est disponible sur
   http://localhost:8000/api . Pour consulter la base de donnée via l'inteface pgadmin est disponible sur http://localhost:8080

## 5 - Documentation détaillée
La documentation technique détaillée de l'ensemble du projet (MLD, choix d'architecture) est disponible 
dans le dossier /docs