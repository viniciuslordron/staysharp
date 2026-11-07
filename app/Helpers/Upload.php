<?php
    namespace App\Helpers;

    class Upload {
        private const TIPOS = [
            "image/jpeg" => "jpg",
            "image/png" => "png",
            "image/webp" => "webp",
            "image/gif" => "gif"
        ];

        // salva uma imagem enviada em public/img/<subpasta>/ com nome aleatorio
        // devolve [caminho relativo a public/img/, erro]; um dos dois e sempre null
        public static function imagem(array $arquivo, string $subpasta): array {
            if (($arquivo["error"] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
                return [null, "Não foi possível enviar a imagem"];
            }
            if ($arquivo["size"] > 5 * 1024 * 1024) {
                return [null, "A imagem deve ter no máximo 5 MB"];
            }

            $info = @getimagesize($arquivo["tmp_name"]);
            if ($info === false || !isset(self::TIPOS[$info["mime"]])) {
                return [null, "Envie uma imagem JPG, PNG, WEBP ou GIF"];
            }

            $pasta = __DIR__ . "/../../public/img/" . $subpasta;
            if (!is_dir($pasta)) {
                mkdir($pasta, 0755, true);
            }

            $nome = bin2hex(random_bytes(8)) . "." . self::TIPOS[$info["mime"]];
            if (!move_uploaded_file($arquivo["tmp_name"], "$pasta/$nome")) {
                return [null, "Não foi possível salvar a imagem"];
            }

            return ["$subpasta/$nome", null];
        }

        // apaga uma imagem enviada antes, aceitando so o formato gerado por imagem()
        public static function apagar(string $caminho, string $subpasta): void {
            if (preg_match('#^' . preg_quote($subpasta, '#') . '/[a-f0-9]{16}\.(jpg|png|webp|gif)$#', $caminho)) {
                $arquivo = __DIR__ . "/../../public/img/" . $caminho;
                if (is_file($arquivo)) {
                    unlink($arquivo);
                }
            }
        }
    }
?>
