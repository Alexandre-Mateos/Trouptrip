# TroupTrip

L'application collaborative qui simplifie l'organisation de vos voyages en groupe.

---

## Sommaire
1. [À propos](#À-propos)
2. [Fonctionnalités clés](#Fonctionnalités-clés)
3. [Stack Technique](#Stack-technique)
4. [Installation avec Docker](#Installation-avec-Docker)
5. [Documentation détaillée](#Documentation-détaillée)

---

## À propos

Organiser un séjour à plusieurs peut bite devenir un casse-tête organisationnelle et logistique:
- Qui vient ?
- Qui apporte quoi ?
- Qui s'occupe de réserver le gîte ?
- On fait quoi samedi après-midi ?
- Combien je dois à Alex ?

**TroupTrip** répond à toutes ces questions. En centralisant toutes les informations logistiques et organisationnelles 
de votre séjour, **TroupTrip** élimine la friction de l'organisation de vos séjours en groupe et réduit la charge mentale 
de ses participants.

---

## Fonctionnalités clés

### Logistique & Matériel
- **Inventaire partagé** : Listez le matériel nécessaire au séjour et aux activités (tentes, réchauds, nourriture).
- **Assignation** : Répartissez le matériel entre les membres du groupe.
- **Check List de départ** : Générez une checklist de départ pour vous assurer de ne rien oublier

### Organisation
- **Gestion des tâches** : Suivi des réservations et des préparatifs (système de statut Todo/Done).
- **Activités** : Planifiez au mieux votre séjour dans un planning partagé.

### Finances & Équilibrage
- **Gestion des dépenses** : Enregistrez vos frais et sélectionnez précisément les participants concernés.
- **Simplification des dettes** : Calcule de la balance globale pour minimiser le nombre de virements nécessaires.
- **Suivi des remboursements** : Marquez les dettes comme réglées une fois le virement effectué.

---

## Stack technique
- **Frontend** : Nuxt.js
- **Backend** : Symfony / API Platform
- **Base de données** : PostgreSQL
- **Déploiement** : Docker

---

## Installation avec Docker

Le projet est entièrement conteneurisé avec Docker pour faciliter le déploiement et garantir un environnement de développement stable.

### Pré-requis
- Docker Desktop
- Git
- WSL pour les systèmes fonctionnant sous Windows

### Lancement

1. Cloner le dépôt
git clone https://github.com/Alexandre-Mateos/Trouptrip.git
cd trouptrip

2. Configurer les variables d'environnement
cd .env

3. Lancer l'infrastructure complète
docker-compose up -d

4. l'application est disponible sur https://localhost/

## Documentation détaillée
La documentation technique détaillée de l'ensemble du projet (MLD, choix d'architecture) est disponible 
dans le dossier /docs