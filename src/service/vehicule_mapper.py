class VehiculeMapper:
    @staticmethod
    def conversionClasseYolo(yolo_class):

        mapping = {
            0: "pieton",
            1: "cycliste",
            2: "voiture",
            3: "2 roues",
            5: "camion",
            7: "camion",
            18: "cheval",
            15: "chat",
            17: "chien",

        }
        return mapping.get(yolo_class,None)