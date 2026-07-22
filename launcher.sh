#!/bin/bash

# ============================================================
# StreetEye - Lanceur automatisé (adapté à ton projet)
# Ce script :
#   1. Crée la base de données (utilisateur, base, droits)
#   2. Installe les dépendances Python dans le venv streetEye/
#   3. Lance le test capture_test.py
# ============================================================

# === 1. VARIABLES ===
DB_NAME="streeteye"
DB_USER="streeteyeuser"
DB_PASS="streetEye"
PROJECT_DIR="$(pwd)"                  # Racine du projet
VENV_PYTHON="$PROJECT_DIR/streetEye/bin/python3"   # Python du venv
TEST_SCRIPT="tests/capture_test.py"   # Script à lancer

# === 2. VÉRIFICATION DES PRÉREQUIS ===
echo "🔍 Vérification des prérequis..."

# PostgreSQL
if ! command -v psql &> /dev/null; then
    echo "❌ PostgreSQL n'est pas installé."
    echo "   Installe-le avec : sudo apt install postgresql"
    exit 1
fi

# Python
if ! command -v python3 &> /dev/null; then
    echo "❌ Python3 n'est pas installé."
    echo "   Installe-le avec : sudo apt install python3 python3-venv"
    exit 1
fi

echo "✅ Prérequis OK"

# === 3. BASE DE DONNÉES ===
echo "🐘 Configuration de PostgreSQL..."

# Créer l'utilisateur s'il n'existe pas
USER_EXISTS=$(sudo -u postgres psql -tAc "SELECT 1 FROM pg_roles WHERE rolname='$DB_USER'")
if [ "$USER_EXISTS" = "1" ]; then
    echo "✅ L'utilisateur '$DB_USER' existe déjà."
else
    echo "👤 Création de l'utilisateur '$DB_USER'..."
    sudo -u postgres psql -c "CREATE USER $DB_USER WITH PASSWORD '$DB_PASS';"
    echo "✅ Utilisateur créé."
fi

# Créer la base si elle n'existe pas
DB_EXISTS=$(sudo -u postgres psql -tAc "SELECT 1 FROM pg_database WHERE datname='$DB_NAME'")
if [ "$DB_EXISTS" = "1" ]; then
    echo "✅ La base '$DB_NAME' existe déjà."
else
    echo "📁 Création de la base '$DB_NAME'..."
    sudo -u postgres psql -c "CREATE DATABASE $DB_NAME OWNER $DB_USER;"
    echo "✅ Base créée."
fi

# Donner les droits
echo "🔑 Attribution des droits..."
sudo -u postgres psql -d "$DB_NAME" -c "GRANT ALL PRIVILEGES ON DATABASE $DB_NAME TO $DB_USER;"
sudo -u postgres psql -d "$DB_NAME" -c "GRANT ALL PRIVILEGES ON ALL TABLES IN SCHEMA public TO $DB_USER;"
sudo -u postgres psql -d "$DB_NAME" -c "GRANT ALL PRIVILEGES ON ALL SEQUENCES IN SCHEMA public TO $DB_USER;"
sudo -u postgres psql -d "$DB_NAME" -c "ALTER DEFAULT PRIVILEGES IN SCHEMA public GRANT ALL PRIVILEGES ON TABLES TO $DB_USER;"
sudo -u postgres psql -d "$DB_NAME" -c "ALTER DEFAULT PRIVILEGES IN SCHEMA public GRANT ALL PRIVILEGES ON SEQUENCES TO $DB_USER;"

# Importer le schéma SQL s'il existe
SQL_FILE="$PROJECT_DIR/sql/CREATETABLE.sql"
if [ -f "$SQL_FILE" ]; then
    echo "📥 Import du schéma SQL..."
    sudo -u postgres psql -d "$DB_NAME" -f "$SQL_FILE"
    echo "✅ Schéma importé."
else
    echo "⚠️  Fichier SQL non trouvé ($SQL_FILE) – schéma non importé."
fi

echo "✅ Base de données prête."

# === 4. DÉPENDANCES PYTHON ===
echo "🐍 Installation des dépendances Python..."

# Vérifier que le venv existe
if [ ! -f "$VENV_PYTHON" ]; then
    echo "📦 Création de l'environnement virtuel dans streetEye/..."
    python3 -m venv streetEye
    echo "✅ Venv créé."
else
    echo "✅ Environnement virtuel déjà présent."
fi

# Installer les paquets depuis requirements.txt (ou de base)
REQ_FILE="$PROJECT_DIR/requirements.txt"
if [ -f "$REQ_FILE" ]; then
    echo "📦 Installation des paquets depuis requirements.txt..."
    "$VENV_PYTHON" -m pip install --upgrade pip
    "$VENV_PYTHON" -m pip install -r "$REQ_FILE"
    echo "✅ Dépendances installées."
else
    echo "⚠️  requirements.txt non trouvé – installation des paquets de base..."
    "$VENV_PYTHON" -m pip install psycopg2-binary opencv-python-headless python-dotenv numpy
    echo "✅ Paquets de base installés."
fi

# === 5. LANCEMENT DU SCRIPT DE TEST ===
echo "🚀 Lancement du script de capture..."

# La commande exacte : PYTHONPATH=. ./streetEye/bin/python3 tests/capture_test.py
cd "$PROJECT_DIR" || exit
PYTHONPATH=. "$VENV_PYTHON" "$TEST_SCRIPT"

# Si le script se termine, afficher un message
echo "⏹️  Le script s'est arrêté."
