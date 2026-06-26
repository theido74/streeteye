DROP TABLE IF EXISTS detection CASCADE;
DROP TABLE IF EXISTS camera CASCADE;
DROP TABLE IF EXISTS vehicule CASCADE;
DROP TABLE IF EXISTS photo CASCADE;

CREATE TABLE camera(
    id SERIAL PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    url VARCHAR(250) NOT NULL,
    location VARCHAR(250) NOT NULL,
    distanceReference NUMERIC(5,2)
);

CREATE TABLE vehicule(
    id SERIAL PRIMARY KEY,
    type VARCHAR(30),
    flash BOOL DEFAULT NULL

);


CREATE TABLE photo(
    id SERIAL PRIMARY KEY,
    cheminStock VARCHAR(100),
    dateCapture TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    deletedAt TIMESTAMP NULL
);

CREATE TABLE detection(
    camera_id INTEGER REFERENCES camera(id),
    vehicule_id INTEGER REFERENCES vehicule(id),
    photo_id INTEGER REFERENCES photo(id),
    dateHeure TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    txDeConfiance NUMERIC(5,2),
    vitesse NUMERIC(5,2),
    PRIMARY KEY (camera_id,vehicule_id,photo_id)
);
