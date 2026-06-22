from src.dataBase.db_manager import DBManager
class Vehicule_service:
    def __init__(self):
        self.db_manager = DBManager()

    def createVehicule(self, type,plaque=None):
        print("VEHICULE CREER")
        return self.db_manager.save_vehicule(type)

    def updateFlash(self, vitesse, id_vehicule):
        self.db_manager.update_flash(vitesse, id_vehicule)
