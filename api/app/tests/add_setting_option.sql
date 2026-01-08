-- Insert the setting
INSERT INTO settings (name) VALUES ('marquee_banner');

INSERT INTO setting_options (setting_id, value, is_default, created_at, updated_at) 
VALUES (
    (SELECT id FROM settings WHERE name = 'marquee_banner'),
    'L''ÉTABLISSEMENT "ABDELATIF LAB" LABORATOIRE D''ANALYSES DE SANG CONVENTIONNÉ AVEC LE LABORATOIRE CERBA EN FRANCE VOUS SOUHAITE LA BIENVENUE, LE LABORATOIRE EST OUVERT DU SAMEDI AU JEUDI DE 7H30 à 16H30.',
    1,
    NOW(),
    NOW()
);


-- Insert another setting option
INSERT INTO settings (name) VALUES ('queue_video');

SET @setting_id = LAST_INSERT_ID();

INSERT INTO setting_options (setting_id, value, is_default, created_at, updated_at)
VALUES (
    @setting_id,
    '/videos/lab_video.mp4',
    1,
    NOW(),
    NOW()
);

-- Insert another setting option
INSERT INTO settings (name) VALUES ('logo');

SET @setting_id = LAST_INSERT_ID();

INSERT INTO setting_options (setting_id, value, is_default, created_at, updated_at)
VALUES (
    @setting_id,
    '/images/logo_lab.PNG',
    1,
    NOW(),
    NOW()
)

    -- photo update
SET SQL_SAFE_UPDATES = 0;

UPDATE profiles 
SET photo_url = SUBSTRING_INDEX(photo_url, '/', -1)
WHERE photo_url LIKE 'http://%' OR photo_url LIKE 'https://%';

SET SQL_SAFE_UPDATES = 1;

-- analysis status
ALTER TABLE analysis_catalog
ADD COLUMN is_active TINYINT(1) NOT NULL DEFAULT 1
AFTER price;
ALTER TABLE analysis_catalog MODIFY is_active BOOLEAN NOT NULL DEFAULT TRUE	
