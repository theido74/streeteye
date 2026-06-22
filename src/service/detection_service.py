from ultralytics import YOLO

import src.service.vehicule_mapper as vehicule_mapper
from src.dataBase.db_manager import DBManager
from src.service.tracker_service import TrackerService
from src.service.vehicule_service import Vehicule_service

vs = Vehicule_service()
ts = TrackerService()
class DetectionService:
    def __init__(self):
        self.db_manager = DBManager()
        self.model = YOLO("../../yolov8n.pt")
        self.vehicule_mapper = vehicule_mapper.VehiculeMapper()

    def detection_vehicule(self, frame):
        allowed_type = {"voiture", "2 roues", "camion", "pieton", "cheval", "chat", "cycliste"}
        vehicule = []

        # persist=True sert au suivi des vehicules, donc on utilise track().
        resultat = self.model.track(frame, verbose=False, persist=True)
        for r in resultat:
            for box in r.boxes:
                if box.id is None:
                    continue
                cls = int(box.cls[0])
                txConfiance = float(box.conf[0])
                vehicule_type = self.vehicule_mapper.conversionClasseYolo(cls)
                track_id = int(box.id[0])
                x1, y1, x2, y2 = map(int, box.xyxy[0])
                if vehicule_type is not None and vehicule_type in allowed_type:
                    vehicule.append({
                        "id": track_id,
                        "type": vehicule_type,
                        "txConfiance": txConfiance,
                        "bbox": (x1, y1, x2, y2),
                        "centre": ts.centreCoordonnees(x1, y1, x2, y2),
                    })

        return vehicule

    def create_detection(
            self,
            camera_id,
            vehicule_id,
            photo_id,
            heure=None,
            tx_confiance=None,
            vitesse=None,
    ):
        return self.db_manager.save_detection(
            camera_id,
            vehicule_id,
            photo_id,
            heure,
            tx_confiance,
            vitesse,
        )

    def close(self):
        self.db_manager.close()
