<?php
// app/Traits/ImageUploadTrait.php

namespace App\Traits;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

trait ImageUploadTrait
{
    /**
     * Lida com o upload de um arquivo, redimensiona, converte para WebP usando a biblioteca GD nativa
     * e retorna o novo caminho.
     *
     * @param Request $request O objeto da requisição.
     * @param string $fieldName O nome do campo do arquivo no formulário.
     * @param string $directory O diretório de destino dentro de 'storage/app/public'.
     * @param int|null $maxWidth A largura máxima para redimensionamento (mantém a proporção).
     * @return string|null O novo caminho da imagem, ou null se falhar.
     */
    public function handleImageUpload(Request $request, string $fieldName, string $directory, ?int $maxWidth = null): ?string
    {
        if (!$request->hasFile($fieldName)) {
            return null;
        }

        if (!Storage::disk('public')->exists($directory)) {
            Storage::disk('public')->makeDirectory($directory);
        }

        $uploadedImage = $request->file($fieldName);
        $newImageName = Str::random(32) . '.webp';
        $imagePath = $directory . '/' . $newImageName;
        $storagePath = Storage::disk('public')->path($imagePath);

        try {
            // 1. Cria um recurso de imagem a partir do arquivo enviado
            $sourceImage = imagecreatefromstring(file_get_contents($uploadedImage->getRealPath()));

            if ($sourceImage === false) {
                // Não foi possível ler a imagem
                return null;
            }

            $originalWidth = imagesx($sourceImage);
            $originalHeight = imagesy($sourceImage);

            // 2. Calcula as novas dimensões se um $maxWidth for fornecido
            if ($maxWidth !== null && $originalWidth > $maxWidth) {
                $newWidth = $maxWidth;
                $newHeight = (int) (($originalHeight / $originalWidth) * $newWidth);
            } else {
                $newWidth = $originalWidth;
                $newHeight = $originalHeight;
            }

            // 3. Cria uma nova imagem (um "canvas" em branco) com as novas dimensões
            $resizedImage = imagecreatetruecolor($newWidth, $newHeight);
            
            // Adicional: Preserva a transparência para imagens PNG e GIF
            imagealphablending($resizedImage, false);
            imagesavealpha($resizedImage, true);
            $transparent = imagecolorallocatealpha($resizedImage, 255, 255, 255, 127);
            imagefilledrectangle($resizedImage, 0, 0, $newWidth, $newHeight, $transparent);


            // 4. Copia e redimensiona a imagem original para o novo canvas
            imagecopyresampled(
                $resizedImage,    // Imagem de destino (o canvas)
                $sourceImage,     // Imagem original
                0, 0,             // Coordenadas X,Y de destino
                0, 0,             // Coordenadas X,Y da original
                $newWidth,        // Nova largura
                $newHeight,       // Nova altura
                $originalWidth,   // Largura original
                $originalHeight   // Altura original
            );

            // 5. Salva a imagem redimensionada no formato WebP
            $quality = 80; // Qualidade de 0 a 100
            imagewebp($resizedImage, $storagePath, $quality);

            // 6. Libera a memória
            imagedestroy($sourceImage);
            imagedestroy($resizedImage);

            return $imagePath;

        } catch (\Exception $e) {
            // Em uma aplicação real, seria bom logar o erro:
            // Log::error('Falha na conversão de imagem com GD: ' . $e->getMessage());
            return null;
        }
    }
}
