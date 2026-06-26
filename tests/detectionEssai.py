import cv2

from src.service.detection_service import DetectionService
from src.service.photo_service import PhotoService
from src.service.vehicule_service import Vehicule_service

vs = Vehicule_service()
ps = PhotoService()
id_image = None
id_camera = 1

detecteur = DetectionService()
ps.delete(24)

if detecteur:
    print("detecteur connected")
image = cv2.imread("/home/leprechaun/Bureau/StreetEye/photo/test3.jpg")
if image is not None:
    print("image")
    chemin,id_image = ps.sauvegarde(image)
    print("CHEMIN" , chemin)
vehicule = detecteur.detection_vehicule(image)
print(vehicule)
if vehicule:
    id_vehicule = vs.createVehicule(vehicule[0]['type'])
    print("vehicule sauvargé")
    detecteur.create_detection(id_camera, id_vehicule, id_image,None,float(vehicule[0]['txConfiance']),None)
    print("DETECTION SAUVEGARDER")


