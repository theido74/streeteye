from src.dataBase.db_connexion import DbConnexion

from dataBase.db_connexion import DbConnexion

class DBManager:
    def __init__(self):
        self.db = DbConnexion()
        self.conn = self.db.conn
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


