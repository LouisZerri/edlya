# Edlya - Site Vitrine

## Contexte du projet

Ce projet est le **site vitrine de l'application mobile Edlya**, une application d'états des lieux immobiliers développée pour **GEST'IMMO** (entreprise de gestion immobilière à Bordeaux).

L'objectif est de présenter l'application, convaincre les prospects (bailleurs, agences immobilières, particuliers) et les diriger vers le téléchargement ou la prise de contact.

### L'application Edlya en bref

Edlya est une app mobile (iOS/Android) qui permet de :
- Créer des états des lieux d'entrée et de sortie complets
- Prendre des photos géolocalisées pour chaque élément
- Signer électroniquement (bailleur + locataire) sur place
- Analyser les dégradations par IA et estimer les retenues
- Importer des anciens EDL au format PDF
- Comparer automatiquement entrée/sortie
- Générer des PDF professionnels
- Travailler hors-ligne

L'app est disponible sur l'App Store : https://apps.apple.com/app/id6760767899

### Modèle commercial

- **Abonnement** : 49,99€/mois — EDL illimités
- **À l'unité** : 12€ HT (14,40€ TTC) par état des lieux
- Les abonnés peuvent "Demander mon état des lieux" (formulaire dédié)
- Paiement via Stripe/PayPal (intégration prévue plus tard)

---

## Charte graphique à respecter

### Couleurs

**Couleur principale (Indigo)** :
```
primary-50:  #eef2ff
primary-100: #e0e7ff
primary-200: #c7d2fe
primary-300: #a5b4fc
primary-400: #818cf8
primary-500: #6366f1
primary-600: #4f46e5  ← couleur principale (boutons, liens, accents)
primary-700: #4338ca
primary-800: #3730a3
primary-900: #312e81
```

**Couleurs sémantiques** :
- Vert (#10b981) : succès, fonctionnalités actives
- Bleu (#3b82f6) : état des lieux d'entrée
- Orange (#f97316) : état des lieux de sortie
- Amber (#f59e0b) : comparatif, avertissements
- Rouge (#ef4444) : erreurs, dégradations

**Neutres (Slate/Gray)** :
- Backgrounds : blanc, slate-50 (#f8fafc), slate-100 (#f1f5f9)
- Texte principal : slate-900 (#0f172a), slate-800 (#1e293b)
- Texte secondaire : slate-500 (#64748b), slate-400 (#94a3b8)
- Bordures : slate-200 (#e2e8f0)

### Typographie

- **Police** : Instrument Sans (avec fallback system-ui, sans-serif)
- Titres : font-bold, tailles 2xl à 4xl
- Sous-titres : font-semibold, tailles lg à xl
- Corps : font-normal, taille base (16px)
- Petits textes : font-normal, taille sm (14px)

### Style visuel

- Design **moderne et épuré**, professionnel
- Coins arrondis (8-12px)
- Ombres subtiles sur les cartes
- Gradients légers (primary-600 → primary-700) pour les CTA
- Responsive mobile-first
- Espacement généreux entre les sections

### Logo / Icône

- SVG d'une maison stylisée avec un oeil/scanner au centre, couleur #4f46e5
- Fichier de référence : `/home/louis/Documents/WEB/edlya/public/favicon.svg`
- Icône de l'app mobile : `/home/louis/Documents/WEB/edlya-mobile/edlya-app/assets/edlya-icon.png`

### Branding

- Nom : **Edlya**
- Mention : "Propulsé par GEST'IMMO" en footer
- Lien vers GEST'IMMO : https://gestimmo-presta.fr

---

## Structure du site vitrine

### Pages à créer

#### 1. Page d'accueil (Hero + présentation)
- **Hero section** : grande image/mockup de l'app + titre accrocheur + CTA "Découvrir" / "Demander un accès"
- Section "Pourquoi Edlya ?" : 3-4 points forts avec icônes
- Section fonctionnalités : grille avec les features clés (photos, signatures, IA, PDF, offline, comparatif)
- Section "Comment ça marche" : 3 étapes (Créer un EDL → Inspecter → Signer & Partager)
- Section témoignages : slider/grille de témoignages clients
- Section tarif : carte unique "Offre de lancement" avec prix + features incluses + CTA
- Footer : liens légaux, contact GEST'IMMO, réseaux sociaux

#### 2. Page Fonctionnalités (détaillée)
- Chaque fonctionnalité développée avec screenshot/mockup
- Gestion des logements
- Création d'EDL avec pièces et éléments
- Photos avec géolocalisation
- Signatures électroniques
- Analyse IA des dégradations
- Import PDF
- Comparatif entrée/sortie
- Estimations des retenues
- Mode hors-ligne
- Génération PDF

#### 3. Page Tarifs
- **Abonnement** : 49,99€/mois — EDL illimités, toutes fonctionnalités
- **À l'unité** : 12€ HT (14,40€ TTC) par état des lieux
- Comparatif des deux offres
- CTA vers contact / souscription

#### 4. Page Témoignages
- Grille de témoignages clients
- Note moyenne / étoiles

#### 5. Page Contact
- Formulaire de contact (nom, email, téléphone, message) → envoie un mail à **contact@edlya.fr**
- Coordonnées GEST'IMMO (30 rue Joseph Bonnet, 33100 Bordeaux)
- Mention "Demandez votre code d'activation"

#### 6. Pages légales
- Mentions légales
- Politique de confidentialité
- CGU

---

## Stack technique recommandée

Pour rester cohérent avec l'écosystème existant :
- **Astro** (template minimal, TypeScript strict)
- **Tailwind CSS 4** (avec la même palette de couleurs)
- **Vite** (intégré à Astro)
- Pas de base de données (site vitrine statique)
- Formulaire de contact → envoi mail à contact@edlya.fr
- Stripe/PayPal prévus plus tard pour le paiement des abonnements

---

## Captures d'écran de l'app

Des screenshots de l'application sont disponibles dans :
- `/home/louis/Documents/WEB/edlya-mobile/SCREENS/`

Utiliser ces screenshots comme mockups dans les sections fonctionnalités et hero.

---

## Liens utiles

- App web Edlya (charte graphique de référence) : `/home/louis/Documents/WEB/edlya/`
- API Edlya : `/home/louis/Documents/WEB/edlya-mobile/edlya-api/`
- App mobile Edlya : `/home/louis/Documents/WEB/edlya-mobile/edlya-app/`
- Site Gestimmo (client) : `/home/louis/Documents/WEB/gestimmo-presta/`
- App Store : https://apps.apple.com/app/id6760767899

---

## Ton et style rédactionnel

- **Professionnel** mais accessible
- Tutoiement évité, vouvoiement préféré
- Orienté bénéfices (gain de temps, simplicité, professionnalisme)
- Cible : professionnels de l'immobilier, bailleurs, agences
- Langue : Français uniquement
