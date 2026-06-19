import os
from dotenv import load_dotenv
import psycopg2


load_dotenv()

class DbConnexion:
    def __init__(
        self,
        connection=None,
        *,
        dbname=None,
        user=None,
        password=None,
        host=None,
        port=5432,
    ):
        if connection is not None:
            self.conn = connection
            return

        self.conn = psycopg2.connect(
            dbname=dbname or os.getenv("DB_NAME"),
            user=user or os.getenv("DB_USER"),
            password=password or os.getenv("DB_PASSWORD"),
            host=host or os.getenv("DB_HOST"),
            port=port,
        )

    def getConnection(self):
        return self.conn

    def close(self):
        if self.conn is not None:
            self.conn.close()
