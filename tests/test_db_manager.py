import unittest
from unittest.mock import Mock, patch

from src.dataBase.db_connexion import DbConnexion
from src.dataBase.db_manager import DBManager


class DbConnexionTest(unittest.TestCase):
    @patch("src.dataBase.db_connexion.psycopg2.connect")
    def test_init_forwards_explicit_parameters(self, connect_mock):
        fake_connection = Mock()
        connect_mock.return_value = fake_connection

        db = DbConnexion(
            dbname="streeteye",
            user="streeteyeuser",
            password="secret",
            host="localhost",
            port=5432,
        )

        connect_mock.assert_called_once_with(
            dbname="streeteye",
            user="streeteyeuser",
            password="secret",
            host="localhost",
            port=5432,
        )
        self.assertIs(db.getConnection(), fake_connection)


class DBManagerTest(unittest.TestCase):
    def test_save_photo_commits_and_returns_new_id(self):
        cursor = Mock()
        cursor.fetchone.return_value = (42,)
        connection = Mock()
        connection.cursor.return_value = cursor
        db = Mock()
        db.getConnection.return_value = connection

        manager = DBManager(db)
        photo_id = manager.save_photo("photo/20240616120000.jpg")

        self.assertEqual(photo_id, 42)
        cursor.execute.assert_called_once_with(
            """
                INSERT INTO photo(cheminStock, dateCapture)
                VALUES(%s, NOW())
                RETURNING id
                """,
            ("photo/20240616120000.jpg",),
        )
        connection.commit.assert_called_once()
        connection.rollback.assert_not_called()

    def test_save_photo_rolls_back_on_error(self):
        cursor = Mock()
        cursor.execute.side_effect = RuntimeError("db error")
        connection = Mock()
        connection.cursor.return_value = cursor
        db = Mock()
        db.getConnection.return_value = connection

        manager = DBManager(db)

        with self.assertRaises(RuntimeError):
            manager.save_photo("photo/error.jpg")

        connection.rollback.assert_called_once()
        connection.commit.assert_not_called()

    def test_get_photos_older_than_returns_rows(self):
        cursor = Mock()
        cursor.fetchall.return_value = [(1, "photo/a.jpg"), (2, "photo/b.jpg")]
        connection = Mock()
        connection.cursor.return_value = cursor
        db = Mock()
        db.getConnection.return_value = connection

        manager = DBManager(db)
        rows = manager.get_photos_older_than(24)

        self.assertEqual(rows, [(1, "photo/a.jpg"), (2, "photo/b.jpg")])
        cursor.execute.assert_called_once()

    def test_delete_photo_deletes_detection_then_photo_and_commits(self):
        cursor = Mock()
        connection = Mock()
        connection.cursor.return_value = cursor
        db = Mock()
        db.getConnection.return_value = connection

        manager = DBManager(db)
        manager.delete_photo(7)

        cursor.execute.assert_called_once_with(
            """
                UPDATE photo
                SET deletedAt = NOW()
                WHERE id = %s
                  AND deletedAt IS NULL
                """,
            (7,),
        )
        connection.commit.assert_called_once()
        connection.rollback.assert_not_called()


if __name__ == "__main__":
    unittest.main()
