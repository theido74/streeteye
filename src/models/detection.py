from datetime import datetime

class Detection:
    def __init__(self,idCamera,idVehicule=None,idPhoto=None,heure=None,txConfiance=None,vitesse=None):
        self.idVehicule = idVehicule
        self.idPhoto = idPhoto
        self.idCamera = idCamera
        self.heure = datetime.now()
        self.txConfiance = txConfiance
        self.vitesse = vitesse
