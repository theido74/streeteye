import math
from fileinput import filename

import cv2
from datetime import datetime
from src.dataBase.db_manager import DBManager
db = DBManager()

class PhotoService:
    def sauvegarde(self,frame):
        chemin= ("photo/" + datetime.now().strftime("%Y%m%d%H%M%S") + ".jpg" + " 1")
        cv2.imwrite(chemin, frame)
        return db.save_photo(chemin)

