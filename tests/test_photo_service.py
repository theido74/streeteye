import tempfile
import unittest
from pathlib import Path
from unittest.mock import Mock

from src.service.photo_service import PhotoService


class PhotoServiceTest(unittest.TestCase):
    def test_delete_photos_older_than_removes_files_and_rows(self):
        with tempfile.TemporaryDirectory() as tmpdir:
            tmp_path = Path(tmpdir)
            old_photo = tmp_path / "old.jpg"
            old_photo.write_bytes(b"fake image")

            db = Mock()
            db.get_photos_older_than.return_value = [
                (1, str(old_photo)),
                (2, str(tmp_path / "missing.jpg")),
            ]
            service = PhotoService(db_manager=db, photo_dir=tmp_path)

            deleted = service.delete_photos_older_than(24)

            self.assertEqual(deleted, 2)
            self.assertFalse(old_photo.exists())
            db.get_photos_older_than.assert_called_once_with(24)
            self.assertEqual(db.delete_photo.call_count, 2)
            db.delete_photo.assert_any_call(1)
            db.delete_photo.assert_any_call(2)


if __name__ == "__main__":
    unittest.main()
