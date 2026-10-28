<?php 
    namespace App\Controllers;
    use App\Views\Render;
    use App\Services\CategoriaService;
    use App\Services\ProdutoService;

    class HomeController {
        private CategoriaService $categoriaService;
        private ProdutoService $produtoService;

        public function __construct() {
            $this->categoriaService = new CategoriaService();
            $this->produtoService = new ProdutoService();
        }

        public function index(): string {
            $titulo = "StaySharp";

            //busca as categorias no banco
            $categorias = $this->categoriaService->listar();
            $nomesCategorias = array_column($categorias, "nome", "id");

            //mostra os 4 primeiros produtos do banco
            $destaques = array_slice($this->produtoService->listar(), 0, 4);

            return (new Render()) -> render (
                'home/index',
                compact('titulo', 'categorias', 'nomesCategorias', 'destaques')
            );
        }
    }
?>
