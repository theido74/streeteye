CREATE
OR REPLACE FUNCTION suppression_globale(
    p_photo_id INTEGER
) RETURNS VOID AS $$ --Dollard quoting, convention pgsql pour les guillements
BEGIN

UPDATE photo
SET deletedAt = NOW()
WHERE id = p_photo_id
  AND deletedAt IS NULL;

UPDATE detection
SET deletedAt = NOW()
WHERE photo_id = p_photo_id
  AND deletedAt IS NULL;

UPDATE vehicule
SET deletedAt = NOW()
WHERE id IN (SELECT DISTINCT vehicule_id
             FROM detection
             WHERE photo_id = p_photo_id)
  AND deletedAt IS NULL;

END;
$$
LANGUAGE plpgsql;
