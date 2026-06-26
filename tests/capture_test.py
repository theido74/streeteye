import os

import cv2
from dotenv import load_dotenv

from src.service.counter_service import CounterService
from src.service.detection_service import DetectionService
from src.service.photo_service import PhotoService
from src.service.telegram_service import AlerteTelegram
from src.service.vehicule_service import Vehicule_service
from src.service.vitesse_service import VitesseService

at = AlerteTelegram()
vhs = Vehicule_service()
ps = PhotoService()
vs = VitesseService()
cs = CounterService()
dc = DetectionService()
CAMERA_ID = 1
load_dotenv()
ps.delete()

url = os.getenv("RTSP_URL")
cap = cv2.VideoCapture(url)
all_id = []
vitesse = 0


while True:
    ret, frame = cap.read()
    if not ret:
        continue

    hauteur, largeur = frame.shape[:2]
    ligne_milieu = largeur // 2
    ligne_vitesse_1 = max(ligne_milieu - 500, 0)
    ligne_vitesse_2 = min(ligne_milieu + 10, largeur - 1)

    vehicules = dc.detection_vehicule(frame)
    for vehicule in vehicules:
        x1, y1, x2, y2 = vehicule["bbox"]
        vehicule_id = vehicule["id"]
        label = f"ID: {vehicule_id}"
        vehicule_type = vehicule["type"].lower()
        centre = vehicule["centre"]
        vitesse = vs.calculerVitesse(vehicule_id, centre)


        cv2.rectangle(frame, (x1, y1), (x2, y2), (0, 255, 0), 2)
        cv2.putText(
            frame,
            f"{label} - {vehicule['type']} - {vitesse}",
            (x1, y1 - 30),
            cv2.FONT_HERSHEY_SIMPLEX,
            0.6,
            (0, 0, 255),
            2,
        )

    # Lignes de repere pour voir quand la photo et la vitesse se declenchent.
    cv2.line(frame, (ligne_milieu, 0), (ligne_milieu, hauteur), (255, 255, 0), 2)
    cv2.line(frame, (ligne_vitesse_1, 0), (ligne_vitesse_1, hauteur), (0, 255, 255), 1)
    cv2.line(frame, (ligne_vitesse_2, 0), (ligne_vitesse_2, hauteur), (0, 255, 255), 1)

    vehicules_filtrees = [
        v for v in vehicules if
        v["type"] in {"voiture", "2 roues", "camion", "cycliste", "cheval", "chien", "chat", "pieton"} and v[
            "txConfiance"] > 0.80
    ]
    if vehicules_filtrees:
        chemin = None
        for vehicule in vehicules_filtrees:
            if vehicule["id"] not in all_id and vehicule["txConfiance"] > 0.65 and ps.validationCapture(all_id,
                                                                                                        vehicule["id"],
                                                                                                        vehicule[
                                                                                                            "centre"],
                                                                                                        ligne_milieu):
                chemin,photo_id = ps.sauvegarde(frame)
                print("DETECTION CREE")
                print("VEHICULE", vehicule)
                print("VITESSE", vitesse)
                v_id = vhs.createVehicule(vehicule["type"])
                print("ID DB = ", v_id)
                dc.create_detection(CAMERA_ID, v_id, photo_id,None, float(vehicule['txConfiance']),
                                    vitesse)
                all_id.append(vehicule["id"])
                cs.compte(vehicule["id"], vehicule["type"])
                cs.afficherStats()
                vhs.updateFlash(vitesse, v_id)
                if vitesse > 28:
                    at.alerteTelegram(chemin, v_id)
            else:
                continue

    cv2.imshow("frame", frame)
    if cv2.waitKey(1) & 0xFF == ord("q"):
        break

cap.release()
cv2.destroyAllWindows()
