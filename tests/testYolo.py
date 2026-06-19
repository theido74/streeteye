from unittest import result

from ultralytics import YOLO

model = YOLO("yolov8n.pt")
resultat = model("/home/leprechaun/Bureau/StreetEye/photo/test3.jpg")
print(model)
print(resultat)
for r in resultat:
    for b in r.boxes:
        print(b.cls)