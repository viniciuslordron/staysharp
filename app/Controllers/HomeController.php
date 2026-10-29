<?php 
    namespace App\Controllers;
    use App\Views\Render;
    use App\Models\CategoriaModel;
    use App\Models\ProdutoModel;

    class HomeController {
        private CategoriaModel $categoriaModel;
        private ProdutoModel $produtoModel;

        public function __construct() {
            $this->categoriaModel = new CategoriaModel();
            $this->produtoModel = new ProdutoModel();
        }

        public function index(): string {
            $titulo = "StaySharp";

            //busca as categorias no banco
            $categorias = $this->categoriaModel->listar();
            $nomesCategorias = array_column($categorias, "nome", "id");

            //mostra os 4 primeiros produtos do banco
            $destaques = array_slice($this->produtoModel->listar(), 0, 4);

            return (new Render()) -> render (
                'home/index',
                compact('titulo', 'categorias', 'nomesCategorias', 'destaques')
            );
        }
    }
?>
