import cv2
import os
from dotenv import load_dotenv

from src.service.detection_service import DetectionService
from src.service.counter_service import CounterService

cs = CounterService()
dc = DetectionService()

load_dotenv()

url = os.getenv("RTSP_URL")
cap = cv2.VideoCapture(url)
all_id = set()


while True:
    ret, frame = cap.read()
    if not ret:
        continue

    vehicules = dc.detection_vehicule(frame)
    for vehicule in vehicules:
        x1, y1, x2, y2 = vehicule["bbox"]
        vehicule_id = vehicule["id"]
        label = f"ID: {vehicule_id}"
        vehicule_type = vehicule["type"].lower()

        cv2.rectangle(frame, (x1, y1), (x2, y2), (0, 255, 0), 2)
        cv2.putText(
            frame,
            f"{label} - {vehicule['type']}",
            (x1, y1 - 30),
            cv2.FONT_HERSHEY_SIMPLEX,
            0.6,
            (0, 0, 255),
            2,
        )

    vehicules_filtrees = [
        v for v in vehicules if v["type"] in {"Voiture", "2 roues", "camion","cycliste"} and v["txConfiance"] > 0.65
    ]
    if vehicules_filtrees:
        print(vehicules_filtrees)
        cs.compte(vehicule_id,vehicule_type)



        for vehicule in vehicules_filtrees:
            x1, y1, x2, y2 = vehicule["bbox"]
            all_id.add(vehicule["id"])

    cv2.imshow("frame", frame)
    if cv2.waitKey(1) & 0xFF == ord("q"):
        break

cap.release()
cv2.destroyAllWindows()
