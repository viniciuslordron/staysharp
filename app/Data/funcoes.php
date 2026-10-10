<?php 
//simular o auto incremento
    function proximoId(array $lista): int {
        $ids = array_column($lista, "id");
        if (count($ids) === 0) {
            return 1;
        }
        return max($ids) + 1;
    }
?>