import math
from collections import deque
from datetime import datetime


class VitesseService:
    def __init__(self):
        self.pixelMetre = 0.02
        self.position = {}
        self.vitesseHisto = {}

    def calculerVitesse(self, position, track_id, centre):
        heure = datetime.now()
        if track_id not in self.position:
            self.position[track_id] = deque(maxlen=10)
            self.vitesseHisto[track_id] = deque(maxlen=5)

        self.position[track_id].append((centre, heure))

        historique = self.position[track_id]
        if len(historique) < 2:
            return 0

        old_center, old_heure = historique[-2]
        new_centre, new_heure = historique[-1]

        distance_pixel = math.sqrt(
            (new_centre[0] - old_center[0]) ** 2 + (new_centre[1] - old_center[1]) ** 2
        )
        distance_metre = distance_pixel * self.pixelMetre

        delta_temps = (new_heure - old_heure).total_seconds()
        if delta_temps <= 0:
            return 0

        vitesse = distance_metre / delta_temps * 3.6
        self.vitesseHisto[track_id].append(vitesse)
        moyenne_vitesse = sum(self.vitesseHisto[track_id]) / len(self.vitesseHisto[track_id])
        return round(moyenne_vitesse)
