import cv2
import os

os.environ["OPENCV_FFMPEG_CAPTURE_OPTIONS"] = "rtsp_transport;tcp"

# Toutes les URLs prometteuses trouvées par Nmap
urls = [
    # Format spécifique trouvé
    "rtsp://admin:L269B60C@192.168.0.27/user=admin_password=L269B60C_channel=1_stream=0.sdp?real_stream",
    "rtsp://admin:L269B60C@192.168.0.27/user=admin_password=L269B60C_channel=1_stream=1.sdp?real_stream",

    # Streaming/Channels (Hikvision style)
    "rtsp://admin:L269B60C@192.168.0.27/Streaming/Channels/101",
    "rtsp://admin:L269B60C@192.168.0.27/Streaming/Channels/102",
    "rtsp://admin:L269B60C@192.168.0.27/Streaming/Channels/103",
    "rtsp://admin:L269B60C@192.168.0.27/Streaming/Unicast/channels/101",

    # ONVIF
    "rtsp://admin:L269B60C@192.168.0.27/onvif1",
    "rtsp://admin:L269B60C@192.168.0.27/onvif2",

    # Live
    "rtsp://admin:L269B60C@192.168.0.27/live",
    "rtsp://admin:L269B60C@192.168.0.27/live.sdp",
    "rtsp://admin:L269B60C@192.168.0.27/live/h264",
    "rtsp://admin:L269B60C@192.168.0.27/live/mpeg4",

    # Video
    "rtsp://admin:L269B60C@192.168.0.27/video.h264",
    "rtsp://admin:L269B60C@192.168.0.27/video.mp4",
    "rtsp://admin:L269B60C@192.168.0.27/video1",
    "rtsp://admin:L269B60C@192.168.0.27/videoMain",

    # Streams
    "rtsp://admin:L269B60C@192.168.0.27/stream1",
    "rtsp://admin:L269B60C@192.168.0.27/stream2",
    "rtsp://admin:L269B60C@192.168.0.27/streaming/channels/1",
    "rtsp://admin:L269B60C@192.168.0.27/streaming/channels/2",

    # Simple
    "rtsp://admin:L269B60C@192.168.0.27/0",
    "rtsp://admin:L269B60C@192.168.0.27/1",
    "rtsp://admin:L269B60C@192.168.0.27/11",

    # H264
    "rtsp://admin:L269B60C@192.168.0.27/h264",
    "rtsp://admin:L269B60C@192.168.0.27/h264.sdp",

    # Cam
    "rtsp://admin:L269B60C@192.168.0.27/cam",
    "rtsp://admin:L269B60C@192.168.0.27/cam/realmonitor?channel=1&subtype=0",
    "rtsp://admin:L269B60C@192.168.0.27/cam/realmonitor?channel=1&subtype=1",

    # Media
    "rtsp://admin:L269B60C@192.168.0.27/media.amp",
    "rtsp://admin:L269B60C@192.168.0.27/media/video1",

    # RTSP avec port explicite
    "rtsp://admin:L269B60C@192.168.0.27:554/Streaming/Channels/101",
    "rtsp://admin:L269B60C@192.168.0.27:554/onvif1",
]

print(f"Test de {len(urls)} URLs...")
print("-" * 60)

found = False
for i, url in enumerate(urls):
    print(f"Test {i + 1}/{len(urls)}: {url[:80]}...")
    try:
        cap = cv2.VideoCapture(url)
        if cap.isOpened():
            ret, frame = cap.read()
            if ret and frame is not None and frame.size > 0:
                print(f"\n✅✅✅ SUCCÈS ! URL trouvée: \n{url}")
                os.makedirs("../photo", exist_ok=True)
                cv2.imwrite(f"photo/camera_found_{i + 1}.jpg", frame)
                found = True
                cap.release()
                break
        cap.release()
    except Exception as e:
        pass

if not found:
    print("\n❌ Aucune URL fonctionnelle trouvée")
    print("Essaie d'accéder à l'interface web: http://192.168.0.27")