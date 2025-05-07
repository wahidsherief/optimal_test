<?php

namespace App\Services;

use Barryvdh\Snappy\Facades\SnappyPdf as PDF;

class PDFService
{
    public function generatePDF(array $data, string $docType, int $templateId = null)
    {
        $template = $this->getTemplatePath($docType, $templateId);

        if (!view()->exists($template)) {
            abort(404, ucfirst($docType) . " template not found.");
        }

        $html = view($template, ['invoice' => (object) $data])->render();

        // Snappy PDF generation
        $pdf = PDF::loadHTML($html);
        $pdf->setPaper('a4')
            ->setOption('orientation', 'portrait')
            ->setOption('margin-bottom', '5mm')
            ->setOption('margin-top', '5mm')
            ->setOption('margin-right', '5mm')
            ->setOption('margin-left', '5mm');

        $directory = strtolower(str_replace(' ', '_', $docType));
        $baseFileName = strtolower(str_replace(' ', '_', 'qamla_' . $docType)) . '_' . $data['id'] ?? time();
        $directoryPath = storage_path('app/public/documents/' . $directory);

        // Ensure the directory exists
        if (!file_exists($directoryPath)) {
            mkdir($directoryPath, 0755, true);
        }

        // Check if the file already exists and generate a unique filename
        $postfix = 0;
        $fileName = $baseFileName . '.pdf';
        $storagePath = $directoryPath . '/' . $fileName;

        while (file_exists($storagePath)) {
            $postfix++;
            $fileName = $baseFileName . '_' . $postfix . '.pdf';
            $storagePath = $directoryPath . '/' . $fileName;
        }

        // Save the PDF
        $pdf->save($storagePath);

        $url = asset('storage/documents/' . $directory . '/' . $fileName);

        return $url;
    }

    protected function getTemplatePath(string $docType, int $templateId = null): string
    {
        $docType = strtolower(str_replace(' ', '_', $docType));
        $template = $templateId ? 'template_' . $templateId : $docType;
        return $docType . '/' .  $template;
    }
}
