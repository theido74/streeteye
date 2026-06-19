import math

class TrackerService:
    def __init__(self):
        self.next_id = 1
        self.tracked_vehicles = {}

    def centreCoordonnees(self, x1, y1, x2, y2):
        centreX = int((x1 + x2) / 2)
        centreY = int((y1 + y2) / 2)
        return centreX, centreY

    def calculerDistance(self, p1, p2):
        return math.sqrt((p1[0] - p2[0]) ** 2 + (p1[1] - p2[1]) ** 2)

    def donneIdVehicule(self,center):
        for vehicule_id,old_center in self.tracked_vehicles.items():
            distance = self.calculerDistance(center,old_center)
            if distance < 120:
                self.tracked_vehicles[vehicule_id] = center
                return vehicule_id

        vehicule_id = self.next_id

        self.next_id += 1

        self.tracked_vehicles[vehicule_id] = center
        return vehicule_id