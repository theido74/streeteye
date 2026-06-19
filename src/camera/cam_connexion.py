import cv2
import os

class CamConnexion:
    def __init__(self,url):
        self.url = url

    def connect(self):
        cap = cv2.VideoCapture(self.url)
        return cap