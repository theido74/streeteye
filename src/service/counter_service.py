class CounterService:
    def __init__(self):
        self.idCompte = set()
        self.total = 0
        self.vehiculeType = {
            "cycliste" : 0,
            "voiture": 0,
            "2 roues": 0,
            "camion": 0,
            "cheval": 0,
            "pieton": 0,
            "chien": 0,
            "chat": 0,
        }

    def estNouveau(self, track_id):
        return track_id not in self.idCompte

    def compte(self, track_id, vehicule_type):
        if track_id in self.idCompte:
            return False

        self.idCompte.add(track_id)
        self.total += 1

        vehicule_type = vehicule_type.lower()
        if vehicule_type in self.vehiculeType:
            self.vehiculeType[vehicule_type] += 1

        return True

    def getTotal(self):
        return self.total

    def getVehiculeType(self):
        return self.vehiculeType

    def afficherStats(self):
        print("===== STATS =====")
        print("Total :", self.total)
        for vehicule_type, count in self.vehiculeType.items():
            print(f"{vehicule_type} : {count}")

        print("===== FIN =====")
