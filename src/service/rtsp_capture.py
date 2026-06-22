import os


def configure_ffmpeg_rtsp_options(read_timeout_ms=5000):
    # FFmpeg attend les timeouts en microsecondes pour stimeout/rw_timeout.
    timeout_us = int(read_timeout_ms * 1000)
    options = f"rtsp_transport;tcp|stimeout;{timeout_us}|rw_timeout;{timeout_us}|max_delay;500000"
    os.environ.setdefault("OPENCV_FFMPEG_CAPTURE_OPTIONS", options)


def open_capture(url, cv2_module, read_timeout_ms=5000):
    cap = cv2_module.VideoCapture(url, getattr(cv2_module, "CAP_FFMPEG", 0))

    if hasattr(cv2_module, "CAP_PROP_BUFFERSIZE"):
        cap.set(cv2_module.CAP_PROP_BUFFERSIZE, 1)
    if hasattr(cv2_module, "CAP_PROP_OPEN_TIMEOUT_MSEC"):
        cap.set(cv2_module.CAP_PROP_OPEN_TIMEOUT_MSEC, read_timeout_ms)
    if hasattr(cv2_module, "CAP_PROP_READ_TIMEOUT_MSEC"):
        cap.set(cv2_module.CAP_PROP_READ_TIMEOUT_MSEC, read_timeout_ms)

    return cap


def reconnect_capture(cap, url, cv2_module, read_timeout_ms=5000):
    if cap is not None:
        try:
            cap.release()
        except Exception:
            pass

    return open_capture(url, cv2_module, read_timeout_ms=read_timeout_ms)


def read_frame(cap):
    ret, frame = cap.read()
    if not ret or frame is None:
        return None
    return frame
