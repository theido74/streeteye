import os

import requests
from dotenv import load_dotenv
from src.service.photo_service import PhotoService
ps = PhotoService()

path = ps.photo_dir

load_dotenv()

BOT_TOKEN = os.getenv("BOT_TOKEN")
CHAT_ID = os.getenv("CHAT_ID")


class AlerteTelegram():

    def alerteTelegram(self, chemin, id_vehicule):
        url = f"https://api.telegram.org/bot{BOT_TOKEN}/sendPhoto"
        data = {"chat_id": CHAT_ID, "text": "Nouvelle infraction", "caption": f"vehicule_id : {id_vehicule}"}
        files = {"photo": open(chemin, "rb")}
        r = requests.post(url, data=data, files=files, timeout=20)
        print(r.status_code, r.text)
