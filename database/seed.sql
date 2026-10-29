INSERT INTO categorias (id, nome) VALUES
    (1, 'Camisas'),
    (2, 'Bonés'),
    (3, 'Adesivos');

INSERT INTO produtos (id, nome, descricao, preco, categoria_id, imagem, imagem_verso) VALUES
    (1, 'Camisa Staysharp Preta', 'Camisa de algodão com estampa frontal.', 89.90, 1, 'camisa-frente.jpg', 'camisa-costas.jpg'),
    (2, 'Camisa Staysharp Branca', 'Camisa de algodão com logo no peito.', 89.90, 1, 'camisa-frente.jpg', 'camisa-costas.jpg'),
    (3, 'Boné Trucker Staysharp', 'Boné trucker com tela atrás.', 79.90, 2, 'bone.jpg', ''),
    (4, 'Boné Dad Hat Staysharp', 'Boné aba curva com bordado.', 69.90, 2, 'bone.jpg', ''),
    (5, 'Adesivo Logo Staysharp', 'Adesivo de vinil resistente à água.', 9.90, 3, 'adesivo-2.jpg', 'adesivo-1.jpg'),
    (6, 'Kit de Adesivos Staysharp', 'Kit com 5 adesivos sortidos.', 24.90, 3, 'adesivo-2.jpg', 'adesivo-1.jpg');
-- Camisas 
INSERT INTO tamanhos (produto_id, tamanho, estoque) VALUES
(1, 'P', 10), (1, 'M', 15), (1, 'G', 8), (1, 'GG', 0),  -- GG esgotado
(2, 'P', 5),  (2, 'M', 12), (2, 'G', 7), (2, 'GG', 3);

--Boanés e adesivos
INSERT INTO tamanhos (produto_id, tamanho, estoque) VALUES
(3, 'Único', 20),
(4, 'Único', 50);

INSERT INTO usuarios (nome, email, senha) VALUES
('Admin', 'admin@teste.com', '$2y$10$tMWisLtlWndiXf0JkxID9udE6hz87uMcFvvc3jG9camtWjtvw61am');