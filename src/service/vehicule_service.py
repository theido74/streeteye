from dataBase import db_manager
from src.dataBase.db_manager import DBManager
class Vehicule_service:
    def __init__(self):
        self.db_manager = DBManager()

    def createVehicule(self, type,plaque=None):
        return self.db_manager.save_vehicule(type)