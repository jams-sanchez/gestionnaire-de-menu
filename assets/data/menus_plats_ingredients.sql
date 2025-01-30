INSERT INTO menu (nom, prix) VALUES
('Festin des Pirates', 27.99),
('Dîner Saiyan', 29.99),
('Festin de Kanto', 24.99);

INSERT INTO plat (nom, description, prix, id_categorie) VALUES
-- One Piece
('Salade du Sunny', 'Salade exotique avec fruits et noix de coco', 7.99, 1),
('Viande du Roi des Pirates', 'Gigot de viande avec sauce barbecue et épices', 18.99, 2),
('Bonbons du Chapeau de Paille', 'Perles sucrées à la noix de coco et gélatine', 6.99, 3),

-- Dragon Ball Z
('Boule de Cristal à la crevette', 'Beignets de crevettes croustillants', 8.99, 1),
('Steak Saiyan XXL', 'Steak de bœuf sauce piquante avec riz', 19.99, 2),
('Nuage Magique Flottant', 'Mousse à la mangue et lait de coco', 7.99, 3),

-- Pokémon
('Soupe Mystherbe', 'Velouté de légumes verts et basilic', 6.99, 1),
('Brochette Dracaufeu', 'Poulet grillé mariné au piment et miel', 18.99, 2),
('Pokéball Surprise', 'Mousse chocolat-vanille en forme de Pokéball', 7.99, 3);

INSERT INTO plat_menu (plat_id, menu_id) VALUES
-- Menu One Piece
(1, 1), (2, 1), (3, 1),
-- Menu Dragon Ball Z
(4, 2), (5, 2), (6, 2),
-- Menu Pokémon
(7, 3), (8, 3), (9, 3);


INSERT INTO ingredient (nom) VALUES
-- One Piece
('Fruits exotiques'), ('Noix de coco'), ('Menthe'),
('Gigot de viande'), ('Sauce barbecue'), ('Épices'),
('Lait de coco'), ('Gélatine'),

-- Dragon Ball Z
('Crevette'), ('Chapelure'), ('Ail'),
('Steak de bœuf'), ('Sauce piquante'), ('Riz'),
('Mangue'), ('Sucre'),

-- Pokémon
('Légumes verts'), ('Basilic'),
('Poulet'), ('Piment'), ('Miel'),
('Chocolat'), ('Vanille');


INSERT INTO ingredient_plat (ingredient_id, plat_id) VALUES
-- Salade du Sunny (One Piece)
(1, 1), (2, 1), (3, 1),
-- Viande du Roi des Pirates (One Piece)
(4, 2), (5, 2), (6, 2),
-- Bonbons du Chapeau de Paille (One Piece)
(7, 3), (8, 3),

-- Boule de Cristal à la crevette (Dragon Ball Z)
(9, 4), (10, 4), (11, 4),
-- Steak Saiyan XXL (Dragon Ball Z)
(12, 5), (13, 5), (14, 5),
-- Nuage Magique Flottant (Dragon Ball Z)
(15, 6), (16, 6),

-- Soupe Mystherbe (Pokémon)
(17, 7), (18, 7),
-- Brochette Dracaufeu (Pokémon)
(19, 8), (20, 8), (21, 8),
-- Pokéball Surprise (Pokémon)
(22, 9), (23, 9);

INSERT INTO categorie (nom) VALUES
('Entrée'),
('Plat'),
('Dessert');