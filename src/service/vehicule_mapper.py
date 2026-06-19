class VehiculeMapper:
    @staticmethod
    def conversionClasseYolo(yolo_class):

        mapping = {
            1: "cycliste",
            2: "Voiture",
            3: "2 roues",
            5: "camion",
            7: "camion"

        }
        return mapping.get(yolo_class,None)