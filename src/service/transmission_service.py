#!/usr/bin/env python3
"""
Service de caméra pour StreetEye
Gère la capture d'images depuis les flux RTSP
"""

import os
import signal
import sys
import time
from datetime import datetime
from pathlib import Path

import cv2
from dotenv import load_dotenv

# Charger .env
load_dotenv()

# Chemins
PROJECT_ROOT = Path(__file__).parent.parent.parent
STREAM_DIR = PROJECT_ROOT / "frontend" / "stream"
PHOTO_DIR = PROJECT_ROOT / "photo"

# Créer les dossiers
STREAM_DIR.mkdir(parents=True, exist_ok=True)
PHOTO_DIR.mkdir(parents=True, exist_ok=True)

# Flag pour arrêter proprement
running = True


def signal_handler(sig, frame):
    global running
    print("\n🛑 Arrêt du service...")
    running = False


signal.signal(signal.SIGINT, signal_handler)
signal.signal(signal.SIGTERM, signal_handler)


class CameraService:
    """Service de capture pour les caméras RTSP"""

    def __init__(self, camera_id=1):
        self.camera_id = camera_id
        self.url = os.getenv("RTSP_URL")
        self.cap = None
        self.last_frame = None

        if not self.url:
            raise ValueError("❌ RTSP_URL non définie dans .env")

        print(f"📷 Caméra {camera_id} : {self.url}")

    def start(self):
        """Démarre la capture continue"""
        print(f"📁 Dossier de sortie : {STREAM_DIR}")

        self.cap = cv2.VideoCapture(self.url)
        if not self.cap.isOpened():
            raise RuntimeError("❌ Impossible d'ouvrir le flux RTSP")

        frame_count = 0
        while running:
            ret, frame = self.cap.read()
            if ret:
                self.last_frame = frame

                # 1. Sauvegarder pour le flux en direct
                stream_path = STREAM_DIR / "frame.jpg"
                cv2.imwrite(str(stream_path), frame)

                # 2. Sauvegarder une photo toutes les 30 secondes (optionnel)
                frame_count += 1
                if frame_count % 200 == 0:  # ~30 secondes à 6-7 fps
                    timestamp = datetime.now().strftime("%Y%m%d_%H%M%S")
                    photo_path = PHOTO_DIR / f"cam_{self.camera_id}_{timestamp}.jpg"
                    cv2.imwrite(str(photo_path), frame)
                    print(f"📸 Photo sauvegardée : {photo_path.name}")

                if frame_count % 100 == 0:
                    print(f"📸 {frame_count} images capturées")
            else:
                print("⚠️ Perte de flux, tentative de reconnexion...")
                self.reconnect()

            time.sleep(0.15)  # ~6-7 images par seconde

        self.stop()
        print(f"✅ Arrêté après {frame_count} images")

    def capture_one(self, output_path=None):
        """Capture une seule image"""
        if output_path is None:
            timestamp = datetime.now().strftime("%Y%m%d_%H%M%S")
            output_path = PHOTO_DIR / f"capture_{timestamp}.jpg"

        cap = cv2.VideoCapture(self.url)
        ret, frame = cap.read()
        cap.release()

        if ret:
            cv2.imwrite(str(output_path), frame)
            print(f"✅ Image capturée : {output_path}")
            return str(output_path)
        else:
            print("❌ Échec de la capture")
            return None

    def get_frame(self):
        """Retourne la dernière frame capturée"""
        return self.last_frame

    def reconnect(self):
        """Tente de reconnecter le flux"""
        if self.cap:
            self.cap.release()
        time.sleep(1)
        self.cap = cv2.VideoCapture(self.url)
        time.sleep(0.5)
        return self.cap.isOpened()

    def stop(self):
        """Arrête la capture"""
        if self.cap:
            self.cap.release()
        print("🔒 Flux libéré")


def main():
    """Point d'entrée principal"""
    # Vérifier que le .env est chargé
    print("🚀 Démarrage du service de caméra StreetEye")
    print(f"📁 Projet : {PROJECT_ROOT}")

    try:
        service = CameraService()
        service.start()
    except KeyboardInterrupt:
        print("\n⏹️  Interruption par l'utilisateur")
    except Exception as e:
        print(f"❌ Erreur : {e}")
        sys.exit(1)


if __name__ == "__main__":
    main()
