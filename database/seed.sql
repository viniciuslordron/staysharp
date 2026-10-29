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
