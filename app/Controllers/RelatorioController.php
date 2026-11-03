<?php
    namespace App\Controllers;

    use App\Models\RelatorioModel;
    use App\Views\Render;

    class RelatorioController {
        private RelatorioModel $model;

        public function __construct() {
            $this->model = new RelatorioModel();
        }

        //aceita somente as opcoes de status do formulario
        private function pegarStatus(): string {
            $status = $_GET["status"] ?? "";
            return in_array($status, ["em_estoque", "esgotado"], true) ? $status : "";
        }

        public function index(): string {
            $titulo = "Relatório de estoque";
            $status = $this->pegarStatus();
            $categorias = $this->model->estoquePorCategoria();
            $produtos = $this->model->estoquePorProduto($status);
            $tamanhos = $this->model->estoquePorTamanho($status);

            return (new Render())->render(
                'relatorios/index',
                compact('titulo', 'status', 'categorias', 'produtos', 'tamanhos')
            );
        }

        public function exportar(): string {
            $status = $this->pegarStatus();
            //consulta antes de iniciar o download para poder tratar falhas do banco
            $tamanhos = $this->model->estoquePorTamanho($status);

            header("Content-Type: text/csv; charset=UTF-8");
            header('Content-Disposition: attachment; filename="estoque-staysharp.csv"');
            $arquivo = fopen("php://output", "w");
            //o BOM e o ponto e virgula permitem abrir os acentos e as colunas no Excel
            echo "\xEF\xBB\xBF";
            fputcsv($arquivo, ["Produto", "Categoria", "Tamanho", "Estoque", "Status"], ";", '"', "");

            foreach ($tamanhos as $t) {
                $linha = [];
                foreach ([$t["produto"], $t["categoria"], $t["tamanho"] ?? "Sem tamanho cadastrado"] as $campo) {
                    //evita que textos cadastrados sejam interpretados como formulas
                    if (in_array(substr(ltrim($campo), 0, 1), ["=", "+", "-", "@"], true)) {
                        $campo = "'" . $campo;
                    }
                    $linha[] = $campo;
                }
                $linha[] = $t["estoque"];
                $linha[] = $t["estoque"] > 0 ? "Em estoque" : "Esgotado";
                fputcsv($arquivo, $linha, ";", '"', "");
            }

            fclose($arquivo);
            return "";
        }
    }
?>
