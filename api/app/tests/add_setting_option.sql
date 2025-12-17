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