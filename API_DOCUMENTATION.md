# Documentation API - Gestionnaire de Bibliothèque

## 🔐 Authentification

### Inscription
```http
POST /auth/register
Content-Type: application/json

{
    "email": "user@example.com",
    "password": "password123"
}
```

### Connexion
```http
POST /auth/login
Content-Type: application/json

{
    "email": "user@example.com",
    "password": "password123"
}
```

## 📚 Gestion des Livres

### Liste des livres
```http
GET /books
```

### Détail d'un livre
```http
GET /books/:id
```

### Ajouter un livre (Auth requis)
```http
POST /books
Authorization: Bearer <token>
Content-Type: application/json

{
    "title": "Le Petit Prince",
    "author_id": 1,
    "isbn": "978-2-07-040850-4",
    "published_year": 1943
}
```

### Modifier un livre (Auth requis)
```http
PUT /books/:id
Authorization: Bearer <token>
Content-Type: application/json

{
    "title": "Le Petit Prince - Édition collector"
}
```

### Supprimer un livre (Auth requis)
```http
DELETE /books/:id
Authorization: Bearer <token>
```

## 👥 Gestion des Auteurs

### Liste des auteurs
```http
GET /authors
```

### Détail d'un auteur
```http
GET /authors/:id
```

### Créer un auteur (Auth requis)
```http
POST /authors
Authorization: Bearer <token>
Content-Type: application/json

{
    "name": "Antoine de Saint-Exupéry",
    "biography": "Écrivain, poète et aviateur français"
}
```

## 📖 Gestion des Emprunts

### Emprunter un livre (Auth requis)
```http
POST /loans
Authorization: Bearer <token>
Content-Type: application/json

{
    "book_id": 1
}
```

### Mes emprunts en cours (Auth requis)
```http
GET /loans
Authorization: Bearer <token>
```

### Retourner un livre (Auth requis)
```http
POST /loans/:id/return
Authorization: Bearer <token>
```

## 🔍 Codes d'erreur

- `400`: Données invalides ou manquantes
- `401`: Non authentifié
- `403`: Non autorisé
- `404`: Ressource non trouvée
- `500`: Erreur serveur

## 🔒 Sécurité

- Toutes les routes protégées nécessitent un token JWT
- Le token est valide pendant 2 heures
- Il doit être envoyé dans le header `Authorization: Bearer <token>`