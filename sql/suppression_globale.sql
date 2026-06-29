CREATE
OR REPLACE FUNCTION suppression_globale(
    p_camera_id INTEGER,
    p_vehicule_id INTEGER,
    p_photo_id INTEGER
) RETURNS VOID AS $$
BEGIN

UPDATE detection
SET deletedat = NOW()
WHERE camera_id = p_camera_id
  AND vehicule_id = p_vehicule_id
  AND photo_id = p_photo_id
  AND deletedat IS NULL;


UPDATE photo
SET deletedat = NOW()
WHERE id = p_photo_id
  AND deletedat IS NULL;


UPDATE vehicule
SET deletedat = NOW()
WHERE id = p_vehicule_id
  AND deletedat IS NULL;
END;
$$
LANGUAGE plpgsql;
