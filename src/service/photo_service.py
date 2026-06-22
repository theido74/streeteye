from datetime import datetime
from pathlib import Path

import cv2

from src.dataBase.db_manager import DBManager


class PhotoService:
    def __init__(self, db_manager=None, photo_dir="photo"):
        self.db = db_manager or DBManager()
        self.photo_dir = Path(photo_dir)
        self.idCapte = set()

    def sauvegarde(self, frame):
        if cv2 is None:
            raise ModuleNotFoundError("opencv-python is required to save photos")

        self.photo_dir.mkdir(exist_ok=True)
        chemin = self.photo_dir / f"{datetime.now().strftime('%Y%m%d%H%M%S')}.jpg"
        succes = cv2.imwrite(str(chemin), frame)
        if not succes:
            raise RuntimeError(f"Echec de l'ecriture de l'image: {chemin}")
        print(chemin)
        return self.db.save_photo(str(chemin))

    def validationCapture(self, id_liste, track_id, centre_x, ligne, tolerance=50):
        if isinstance(centre_x, (tuple, list)):
            centre_x = centre_x[0]

        if track_id not in id_liste:
            if abs(centre_x - ligne) < tolerance:
                self.idCapte.add(track_id)
                return True
        return False

    def delete(self, hours=24):
        return self.delete_photos_older_than(hours)

    def delete_photos_older_than(self, hours=24):
        photos = self.db.get_photos_older_than(hours)
        deleted = 0

        for photo_id, chemin in photos:
            chemin_photo = Path(chemin)
            try:
                chemin_photo.unlink()
            except FileNotFoundError:
                pass

            self.db.delete_photo(photo_id)
            deleted += 1

        return deleted
