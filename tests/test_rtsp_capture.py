import os
import unittest
from unittest.mock import Mock

from src.service import rtsp_capture


class RtspCaptureTest(unittest.TestCase):
    def test_configure_ffmpeg_rtsp_options_sets_default_once(self):
        original = os.environ.get("OPENCV_FFMPEG_CAPTURE_OPTIONS")
        try:
            os.environ.pop("OPENCV_FFMPEG_CAPTURE_OPTIONS", None)
            rtsp_capture.configure_ffmpeg_rtsp_options(5)
            self.assertEqual(
                os.environ["OPENCV_FFMPEG_CAPTURE_OPTIONS"],
                "rtsp_transport;tcp|stimeout;5000|rw_timeout;5000|max_delay;500000",
            )

            os.environ["OPENCV_FFMPEG_CAPTURE_OPTIONS"] = "existing"
            rtsp_capture.configure_ffmpeg_rtsp_options(10)
            self.assertEqual(os.environ["OPENCV_FFMPEG_CAPTURE_OPTIONS"], "existing")
        finally:
            if original is None:
                os.environ.pop("OPENCV_FFMPEG_CAPTURE_OPTIONS", None)
            else:
                os.environ["OPENCV_FFMPEG_CAPTURE_OPTIONS"] = original

    def test_reconnect_capture_releases_old_capture_and_opens_new_one(self):
        old_cap = Mock()
        new_cap = Mock()
        cv2_module = Mock()
        cv2_module.VideoCapture.return_value = new_cap
        cv2_module.CAP_FFMPEG = 1900
        cv2_module.CAP_PROP_BUFFERSIZE = 38
        cv2_module.CAP_PROP_OPEN_TIMEOUT_MSEC = 53
        cv2_module.CAP_PROP_READ_TIMEOUT_MSEC = 54

        result = rtsp_capture.reconnect_capture(old_cap, "rtsp://camera", cv2_module, read_timeout_ms=5000)

        old_cap.release.assert_called_once()
        cv2_module.VideoCapture.assert_called_once_with("rtsp://camera", 1900)
        self.assertIs(result, new_cap)
        new_cap.set.assert_any_call(38, 1)
        new_cap.set.assert_any_call(53, 5000)
        new_cap.set.assert_any_call(54, 5000)

    def test_read_frame_returns_none_on_failed_read(self):
        cap = Mock()
        cap.read.return_value = (False, None)

        self.assertIsNone(rtsp_capture.read_frame(cap))


if __name__ == "__main__":
    unittest.main()
