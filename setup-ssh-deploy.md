# Configuration SSH pour le déploiement

## 1. Générer une clé SSH dédiée au déploiement

```bash
# Sur ta machine locale
ssh-keygen -t ed25519 -C "deploy-episciences-manager" -f ~/.ssh/deploy_episciences

# Cela crée 2 fichiers :
# ~/.ssh/deploy_episciences     (clé privée - pour GitHub)  
# ~/.ssh/deploy_episciences.pub (clé publique - pour le serveur)
```

## 2. Installer la clé publique sur le serveur

```bash
# Copier la clé publique sur le serveur
ssh-copy-id -i ~/.ssh/deploy_episciences.pub ton_user@epimanager.episciences.org

# OU manuellement :
cat ~/.ssh/deploy_episciences.pub
# Copier le contenu et l'ajouter dans ~/.ssh/authorized_keys sur le serveur
```

## 3. Tester la connexion SSH

```bash
ssh -i ~/.ssh/deploy_episciences ton_user@epimanager.episciences.org

# Si ça fonctionne, tu peux te connecter sans mot de passe !
```

## 4. Secrets GitHub à configurer

```bash
SSH_PRIVATE_KEY = contenu de ~/.ssh/deploy_episciences (clé privée complète)
SSH_USER = ton_user  
SSH_HOST = epimanager.episciences.org
DEPLOY_PATH = /var/www/episciences-manager (ou le bon chemin)
```

## 5. Test du déploiement

Une fois les secrets configurés, push sur la branche `main_creer_projet_symfony` pour déclencher le déploiement automatique !