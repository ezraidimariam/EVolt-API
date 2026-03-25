# 🔐 EVolt API - Version avec Sanctum Tokens!

## 🎉 **SANCTUM INTÉGRÉ AVEC SUCCÈS!**

J'ai ajouté Sanctum tokens à ton projet EVolt API simple!

---

## 🔐 **CE QUI A CHANGÉ:**

### **✅ AuthController avec Sanctum:**
- **register()** - Crée utilisateur + token Sanctum
- **login()** - Génère token Sanctum valide
- **logout()** - Supprime token actuel
- **me()** - Récupère utilisateur connecté

### **✅ Routes avec Middleware:**
- **Routes publiques:** register, login
- **Routes protégées:** logout, me, users, products
- **Middleware:** `auth:sanctum` pour sécuriser

---

## 📡 **ENDPOINTS AVEC SANCTUM:**

### **🔓 Routes Publiques (pas de token requis):**
- `POST /api/register` - Inscription avec token
- `POST /api/login` - Connexion avec token

### **🔒 Routes Protégées (token requis):**
- `POST /api/logout` - Déconnexion
- `GET /api/me` - Infos utilisateur connecté
- `GET /api/users` - Liste utilisateurs
- `POST /api/users` - Créer utilisateur
- `GET /api/users/{id}` - Voir utilisateur
- `GET /api/products` - Liste stations
- `POST /api/products` - Créer station
- `GET /api/products/{id}` - Voir station
- `PUT /api/products/{id}` - Modifier station
- `DELETE /api/products/{id}` - Supprimer station
- `GET /api/products/search/{name}` - Rechercher

---

## 🧪 **TESTS AVEC SANCTUM:**

### **1. Inscription:**
```bash
curl -X POST http://localhost:8000/api/register \
  -H "Content-Type: application/json" \
  -d '{"name":"John Doe","email":"john@example.com","password":"password123"}'
```

**Réponse:**
```json
{
  "success": true,
  "message": "User registered successfully",
  "data": {
    "user": {
      "id": 1,
      "name": "John Doe",
      "email": "john@example.com",
      "role": "user"
    },
    "access_token": "1|abc123...",
    "token_type": "Bearer"
  }
}
```

### **2. Connexion:**
```bash
curl -X POST http://localhost:8000/api/login \
  -H "Content-Type: application/json" \
  -d '{"email":"john@example.com","password":"password123"}'
```

### **3. Accès protégé (avec token):**
```bash
curl -H "Authorization: Bearer 1|abc123..." \
  http://localhost:8000/api/me
```

### **4. Lister les produits (avec token):**
```bash
curl -H "Authorization: Bearer 1|abc123..." \
  http://localhost:8000/api/products
```

---

## 🔧 **COMMENT ÇA MARCHE:**

### **1. Inscription:**
- ✅ Vérifie les inputs
- ✅ Crée l'utilisateur
- ✅ Génère token Sanctum
- ✅ Retourne user + token

### **2. Connexion:**
- ✅ Vérifie email/password
- ✅ Génère nouveau token
- ✅ Retourne user + token

### **3. Routes Protégées:**
- ✅ Middleware vérifie token
- ✅ `auth:sanctum` automatique
- ✅ `$request->user()` disponible

### **4. Déconnexion:**
- ✅ Récupère utilisateur du token
- ✅ Supprime token actuel
- ✅ Token invalide pour futur

---

## 🎯 **POUR DÉMARRER:**

### **1. Configure Sanctum:**
```bash
# Assure-toi que Sanctum est installé
composer require laravel/sanctum

# Publie les fichiers
php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"

# Lance les migrations
php artisan migrate
```

### **2. Démarre le serveur:**
```bash
cd C:\Users\marma\evolt\evolt-api
php artisan serve --port=8000
```

### **3. Teste:**
1. **Inscris un utilisateur**
2. **Connecte-toi** - récupère le token
3. **Utilise le token** pour les routes protégées

---

## 🏆 **AVANTAGES DE SANCTUM:**

✅ **Sécurité** - Tokens uniques  
✅ **Simple** - Pas de middleware complexe  
✅ **Flexible** - Plusieurs tokens par utilisateur  
✅ **Révocation** - Suppression facile  
✅ **Standard** - Bearer token HTTP  

---

## 🎓 **CE QUE TU APPRENDRAS:**

✅ **Authentification API** moderne  
✅ **Tokens Sanctum** et gestion  
✅ **Middleware d'authentification**  
✅ **Routes protégées** et publiques  
✅ **Gestion de session** API  
✅ **Sécurité REST**  

---

## 🚀 **TON PROJET EST PRÊT!**

Tu as maintenant une API EVolt avec:
- ✅ **Sanctum tokens** intégrés
- ✅ **Authentification** complète
- ✅ **Routes sécurisées** automatiquement
- ✅ **Code simple** niveau débutant
- ✅ **Documentation** claire

**Il te suffit de démarrer et tester!** 🎉

---

**🔐 BONNE DÉCOUVERTE DE SANCTUM!**
