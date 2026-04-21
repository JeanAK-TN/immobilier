<?php

namespace App;

use App\Models\Quittance;

class QuittancePdfBuilder
{
    public function build(Quittance $quittance): string
    {
        $quittance->loadMissing([
            'contrat.bien.proprietaire',
            'contrat.locataire.user',
            'paiement',
        ]);

        $proprietaire = $quittance->contrat->bien->proprietaire;
        $locataire = $quittance->contrat->locataire;
        $bien = $quittance->contrat->bien;
        $paiement = $quittance->paiement;

        $lignes = [
            'Quittance de loyer',
            sprintf('Numéro : %s', $quittance->numero_quittance),
            sprintf('Date d\'émission : %s', $quittance->emise_le?->translatedFormat('d F Y') ?? now()->translatedFormat('d F Y')),
            sprintf('Période : %s', $quittance->labelPeriode()),
            sprintf('Montant réglé : %s FCFA', number_format((float) $paiement->montant, 0, ',', ' ')),
            '',
            sprintf('Propriétaire : %s', $proprietaire->name),
            sprintf('Contact proprietaire : %s', $proprietaire->email),
            sprintf('Locataire : %s', $locataire->nomComplet()),
            sprintf('Contact locataire : %s', $locataire->email),
            sprintf('Bien : %s', $bien->nom),
            sprintf('Adresse : %s, %s, %s', $bien->adresse, $bien->ville, $bien->pays),
            sprintf('Référence paiement : %s', $paiement->reference),
            'Mention : Paiement simulé - aucune transaction réelle.',
        ];

        $flux = $this->buildContentStream($lignes);

        return $this->buildPdfDocument($flux);
    }

    /**
     * @param  array<int, string>  $lines
     */
    private function buildContentStream(array $lines): string
    {
        $contenu = ['BT', '/F1 18 Tf', '1 0 0 1 50 790 Tm', sprintf('(%s) Tj', $this->pdfEscape($this->encode('Quittance PDF'))), '/F1 11 Tf'];
        $positionY = 750;

        foreach ($lines as $ligne) {
            $segments = $ligne === '' ? [''] : explode("\n", wordwrap($ligne, 85));

            foreach ($segments as $segment) {
                $contenu[] = sprintf('1 0 0 1 50 %d Tm', $positionY);
                $contenu[] = sprintf('(%s) Tj', $this->pdfEscape($this->encode($segment)));
                $positionY -= 18;
            }

            $positionY -= 4;
        }

        $contenu[] = 'ET';

        return implode("\n", $contenu);
    }

    private function buildPdfDocument(string $contentStream): string
    {
        $objets = [
            1 => '<< /Type /Catalog /Pages 2 0 R >>',
            2 => '<< /Type /Pages /Kids [3 0 R] /Count 1 >>',
            3 => '<< /Type /Page /Parent 2 0 R /MediaBox [0 0 595 842] /Resources << /Font << /F1 4 0 R >> >> /Contents 5 0 R >>',
            4 => '<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica /Encoding /WinAnsiEncoding >>',
            5 => sprintf("<< /Length %d >>\nstream\n%s\nendstream", strlen($contentStream), $contentStream),
        ];

        $pdf = "%PDF-1.4\n";
        $offsets = [];

        foreach ($objets as $index => $objet) {
            $offsets[$index] = strlen($pdf);
            $pdf .= sprintf("%d 0 obj\n%s\nendobj\n", $index, $objet);
        }

        $xrefOffset = strlen($pdf);
        $pdf .= sprintf("xref\n0 %d\n", count($objets) + 1);
        $pdf .= "0000000000 65535 f \n";

        foreach ($objets as $index => $objet) {
            $pdf .= sprintf("%010d 00000 n \n", $offsets[$index]);
        }

        $pdf .= sprintf(
            "trailer\n<< /Size %d /Root 1 0 R >>\nstartxref\n%d\n%%%%EOF",
            count($objets) + 1,
            $xrefOffset
        );

        return $pdf;
    }

    private function encode(string $text): string
    {
        $encoded = iconv('UTF-8', 'Windows-1252//TRANSLIT//IGNORE', $text);

        return $encoded === false ? $text : $encoded;
    }

    private function pdfEscape(string $text): string
    {
        return str_replace(
            ['\\', '(', ')'],
            ['\\\\', '\(', '\)'],
            $text
        );
    }
}
