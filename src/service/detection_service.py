from pathlib import Path

from ultralytics import YOLO

from src.service.vehicule_mapper import VehiculeMapper


class DetectionService:
    def __init__(self, model_path=None, vehicule_mapper=None):
        if model_path is None:
            model_path = Path(__file__).resolve().parents[2] / "yolov8n.pt"

        self.model = YOLO(str(model_path))
        self.vehicule_mapper = vehicule_mapper or VehiculeMapper()

    def detection_vehicule(self, frame):
        allowed_type = {"Voiture", "2 roues", "camion","cycliste"}
        vehicule = []

        resultats = self.model.track(frame, persist=True, verbose=False)

        for resultat in resultats:
            for box in resultat.boxes:
                if box.id is None:
                    continue

                cls = int(box.cls[0])
                tx_confiance = float(box.conf[0])
                vehicule_type = self.vehicule_mapper.conversionClasseYolo(cls)

                if vehicule_type is None or vehicule_type not in allowed_type:
                    continue

                x1, y1, x2, y2 = map(int, box.xyxy[0])
                vehicule.append(
                    {
                        "id": int(box.id[0]),
                        "type": vehicule_type,
                        "txConfiance": tx_confiance,
                        "bbox": (x1, y1, x2, y2),
                    }
                )

        return vehicule
