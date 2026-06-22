import os

import cv2
from dotenv import load_dotenv
from ultralytics import YOLO

from src.dataBase.db_connexion import DbConnexion
from src.dataBase.db_manager import DBManager

load_dotenv()

def main():
    """Petit script manuel pour valider la DAO."""
    db = DbConnexion()
    db_manager = DBManager()

    try:
        print("Connexion DB réussie")
        load_dotenv()

        url = os.getenv("RTSP_URL")
        cap = cv2.VideoCapture(url)
        ret, frame = cap.read()

        if ret:
            cv2.imwrite("../../photo/test.jpg", frame)
            print("image capturée")

        cap.release()

        # On enregistre d'abord la photo, puis on récupère son ID pour la détection.
        id_photo = db_manager.save_photo("photo/test.jpg")
        print(id_photo, "INSERT OK")

        db_manager.save_detection(1, 1, id_photo)
        print("INSERT detection OK")

        model = YOLO("../../yolov8n.pt")

        print(model.names)



    finally:
        db_manager.close()


if __name__ == "__main__":
    main()
