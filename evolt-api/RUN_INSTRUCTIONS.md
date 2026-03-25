# 🚀 How to Run EVolt API from Scratch

## Étape 1: Ouvrir le Terminal
- Ouvre PowerShell ou CMD dans Windows
- Navigue vers ton projet:
```powershell
cd C:\Users\marma\evolt\evolt-api
```

## Étape 2: Installer PHP (si pas déjà fait)
```powershell
# Vérifie si PHP est installé
php --version

# Si pas installé, télécharge PHP depuis: https://www.php.net/downloads.php
# Ou utilise Chocolatey:
# choco install php
```

## Étape 3: Installer Composer
```powershell
# Vérifie si Composer est installé
composer --version

# Si pas installé:
# Télécharge depuis: https://getcomposer.org/download/
```

## Étape 4: Installer les Dépendances
```powershell
# Install les packages PHP
composer install

# Install les packages Node.js
npm install
```

## Étape 5: Configurer l'Environnement
```powershell
# Copie le fichier .env
cp .env.example .env

# Génère la clé API
php artisan key:generate
```

## Étape 6: Setup la Base de Données
```powershell
# Crée les tables et insère les données de test
php artisan migrate:fresh --seed
```

## Étape 7: Démarrer le Serveur
```powershell
# Démarre le serveur Laravel
php artisan serve --host=127.0.0.1 --port=8000
```

## Étape 8: Tester l'API
- Ouvre ton navigateur: http://localhost:8000
- Importe `POSTMAN_COLLECTION.json` dans Postman
- Test les endpoints!

---

## 🎯 Commandes Rapides (Copy-Paste)

```powershell
# 1. Navigue vers le projet
cd C:\Users\marma\evolt\evolt-api

# 2. Install dépendances
composer install
npm install

# 3. Setup environnement
cp .env.example .env
php artisan key:generate

# 4. Setup base de données
php artisan migrate:fresh --seed

# 5. Démarre le serveur
php artisan serve --host=127.0.0.1 --port=8000
```

---

## 🔧 Si tu as des problèmes:

### "php n'est pas reconnu"
```powershell
# Ajoute PHP au PATH Windows
# Ou utilise le chemin complet:
C:\php\php.exe artisan serve
```

### "composer n'est pas reconnu"
```powershell
# Télécharge Composer depuis getcomposer.org
# Ou utilise Chocolatey:
choco install composer
```

### Problèmes de permissions
```powershell
# Exécute PowerShell en tant qu'administrateur
Set-ExecutionPolicy -ExecutionPolicy RemoteSigned -Scope CurrentUser
```

---

## 📊 Vérification

Quand le serveur démarre, tu devrais voir:
```
   INFO  Server running on [http://127.0.0.1:8000].  

  Press Ctrl+C to stop the server.
```

Test avec: http://localhost:8000/api/charging-stations

---

## 🧪 Test Rapide

```powershell
# Test avec curl (si disponible)
curl http://localhost:8000/api/charging-stations

# Ou test dans ton navigateur
# http://localhost:8000/api/charging-stations
```

---

## ✅ Si tout fonctionne bien:

1. ✅ Serveur démarré sur http://localhost:8000
2. ✅ Base de données avec 3 stations de recharge
3. ✅ 2 utilisateurs créés (admin + user)
4. ✅ Prêt à tester avec Postman!

---

## 🆘 Besoin d'aide?

Si tu as des erreurs, envoie-moi le message d'erreur et je t'aiderai!
