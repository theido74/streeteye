from src.dataBase.db_connexion import DbConnexion

class DBManager:
    def __init__(self, db=None):
        self.db = db or DbConnexion()
        self.conn = self.db.getConnection() if hasattr(self.db, "getConnection") else self.db.conn
        self.cursor = self.conn.cursor()

    def save_photo(self, chemin: str) -> int:
        try:
            self.cursor.execute(
                """
                INSERT INTO photo(cheminStock, dateCapture)
                VALUES(%s, NOW())
                RETURNING id
                """,
                (chemin,)
            )
            photo_id = self.cursor.fetchone()[0]
            self.conn.commit()
            return photo_id

        except Exception:
            self.conn.rollback()
            raise

    def get_photos_older_than(self, hours: int = 24):
        self.cursor.execute(
            """
            SELECT id, cheminStock
            FROM photo
            WHERE dateCapture < NOW() - (%s * INTERVAL '1 hour')
            """,
            (hours,),
        )
        return self.cursor.fetchall()

    def delete_photo(self, photo_id: int):
        try:
            self.cursor.execute(
                "DELETE FROM detection WHERE photo_id = %s",
                (photo_id,),
            )
            self.cursor.execute(
                "DELETE FROM photo WHERE id = %s",
                (photo_id,),
            )
            self.conn.commit()
        except Exception:
            self.conn.rollback()
            raise

    def save_detection(self, camera_id, vehicule_id, photo_id, heure=None, tx_confiance=None, vitesse=None):
        try:
            self.cursor.execute(
                """
                INSERT INTO detection(
                    camera_id,
                    vehicule_id,
                    photo_id,
                    dateHeure,
                    txDeConfiance,
                    vitesse
                )
                VALUES(%s, %s, %s, COALESCE(%s, NOW()), %s, %s)
                """,
                (camera_id, vehicule_id, photo_id, heure, tx_confiance, vitesse)
            )
            self.conn.commit()

        except Exception:
            self.conn.rollback()
            raise


    def save_vehicule(self, type) -> int:
        plaque = "GE TEST"
        try:
            self.cursor.execute(
                """
                INSERT INTO vehicule(type,plaque)
                VALUES(%s,%s)
                RETURNING id
                """,
                (type,plaque)
            )
            vehicule_id = self.cursor.fetchone()[0]
            self.conn.commit()
            return vehicule_id

        except Exception:
            self.conn.rollback()
            raise

    def close(self):
        self.cursor.close()
        self.conn.close()
