<?php
    // dados iniciais do "banco fake"
    // troquem pelos produtos reais da loja (nome, preço e foto)

    $_SESSION["categorias"] = [
        ["id" => 1, "nome" => "Camisas"],
        ["id" => 2, "nome" => "Bonés"],
        ["id" => 3, "nome" => "Adesivos"]
    ];

    $_SESSION["produtos"] = [
        ["id" => 1, "nome" => "Camisa Staysharp Preta", "descricao" => "Camisa de algodão com estampa frontal.",
         "preco" => 89.90, "categoria_id" => 1, "imagem" => "/public/img/oversizedSP.jpeg"],
        ["id" => 2, "nome" => "Camisa Staysharp Branca", "descricao" => "Camisa de algodão com logo no peito.",
         "preco" => 89.90, "categoria_id" => 1, "imagem" => "/public/img/oversizedSP.jpeg"],
        ["id" => 3, "nome" => "Boné Trucker Staysharp", "descricao" => "Boné trucker com tela atrás.",
         "preco" => 79.90, "categoria_id" => 2, "imagem" => "/public/img/oversizedSP.jpeg"],
        ["id" => 4, "nome" => "Boné Dad Hat Staysharp", "descricao" => "Boné aba curva com bordado.",
         "preco" => 69.90, "categoria_id" => 2, "imagem" => "/public/img/oversizedSP.jpeg"],
        ["id" => 5, "nome" => "Adesivo Logo Staysharp", "descricao" => "Adesivo de vinil resistente à água.",
         "preco" => 9.90, "categoria_id" => 3, "imagem" => "/public/img/oversizedSP.jpeg"],
        ["id" => 6, "nome" => "Kit de Adesivos Staysharp", "descricao" => "Kit com 5 adesivos sortidos.",
         "preco" => 24.90, "categoria_id" => 3, "imagem" => "/public/img/oversizedSP.jpeg"]
    ];
?>