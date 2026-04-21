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

        $data = [
            'numero' => $quittance->numero_quittance,
            'date_emission' => $quittance->emise_le?->translatedFormat('d F Y') ?? now()->translatedFormat('d F Y'),
            'periode' => $quittance->labelPeriode(),
            'montant' => number_format((float) $paiement->montant, 0, ',', ' ').' FCFA',
            'proprietaire_nom' => $proprietaire->name,
            'proprietaire_email' => $proprietaire->email,
            'locataire_nom' => $locataire->nomComplet(),
            'locataire_email' => $locataire->email,
            'bien_nom' => $bien->nom,
            'bien_adresse' => $bien->adresse,
            'bien_ville_pays' => $bien->ville.', '.$bien->pays,
            'reference' => $paiement->reference,
            'mode_paiement' => $paiement->mode->label(),
            'date_generation' => now()->translatedFormat('d F Y'),
        ];

        $contentStream = $this->buildContentStream($data);

        return $this->buildPdfDocument($contentStream);
    }

    /**
     * @param  array<string, string>  $data
     */
    private function buildContentStream(array $data): string
    {
        $ops = [];

        // === BACKGROUNDS ===

        // Header band (dark navy)
        $ops[] = '0.102 0.204 0.4 rg';
        $ops[] = '0 778 595 64 re';
        $ops[] = 'f';

        // Info band (medium blue)
        $ops[] = '0.18 0.42 0.67 rg';
        $ops[] = '0 750 595 28 re';
        $ops[] = 'f';

        // Proprietaire box — header accent
        $ops[] = '0.88 0.93 0.98 rg';
        $ops[] = '50 716 235 22 re';
        $ops[] = 'f';

        // Proprietaire box — body
        $ops[] = '1 1 1 rg';
        $ops[] = '50 625 235 91 re';
        $ops[] = 'f';

        // Locataire box — header accent
        $ops[] = '0.88 0.93 0.98 rg';
        $ops[] = '310 716 235 22 re';
        $ops[] = 'f';

        // Locataire box — body
        $ops[] = '1 1 1 rg';
        $ops[] = '310 625 235 91 re';
        $ops[] = 'f';

        // Amount box
        $ops[] = '0.94 0.97 1.0 rg';
        $ops[] = '50 520 495 90 re';
        $ops[] = 'f';

        // Logement section rule
        $ops[] = '0.102 0.204 0.4 rg';
        $ops[] = '50 502 495 2 re';
        $ops[] = 'f';

        // Reference section rule
        $ops[] = '50 435 495 2 re';
        $ops[] = 'f';

        // Warning notice (amber)
        $ops[] = '0.99 0.86 0.34 rg';
        $ops[] = '50 155 495 60 re';
        $ops[] = 'f';

        // === BORDERS ===

        // Proprietaire box border
        $ops[] = '0.78 0.83 0.9 RG';
        $ops[] = '0.5 w';
        $ops[] = '50 625 235 113 re';
        $ops[] = 'S';

        // Locataire box border
        $ops[] = '310 625 235 113 re';
        $ops[] = 'S';

        // Amount box border
        $ops[] = '0.18 0.42 0.67 RG';
        $ops[] = '1 w';
        $ops[] = '50 520 495 90 re';
        $ops[] = 'S';

        // Warning notice border
        $ops[] = '0.75 0.6 0.1 RG';
        $ops[] = '0.5 w';
        $ops[] = '50 155 495 60 re';
        $ops[] = 'S';

        // === HEADER TEXT ===
        $ops[] = 'BT';
        $ops[] = '1 1 1 rg';
        $ops[] = '/F2 22 Tf';
        $ops[] = '1 0 0 1 50 805 Tm';
        $ops[] = '('.$this->e('QUITTANCE DE LOYER').') Tj';
        $ops[] = '/F1 9 Tf';
        $ops[] = '1 0 0 1 50 784 Tm';
        $ops[] = '('.$this->e('Gestion Locative').') Tj';
        $ops[] = 'ET';

        // === INFO BAND TEXT ===
        $ops[] = 'BT';
        $ops[] = '1 1 1 rg';
        $ops[] = '/F1 8 Tf';
        $ops[] = '1 0 0 1 50 760 Tm';
        $ops[] = '('.$this->e('N° '.$data['numero']).') Tj';
        $ops[] = '1 0 0 1 220 760 Tm';
        $ops[] = '('.$this->e('Émis le : '.$data['date_emission']).') Tj';
        $ops[] = '1 0 0 1 420 760 Tm';
        $ops[] = '('.$this->e('Période : '.$data['periode']).') Tj';
        $ops[] = 'ET';

        // === PROPRIETAIRE BOX ===
        $ops[] = 'BT';
        $ops[] = '0.102 0.204 0.4 rg';
        $ops[] = '/F2 8 Tf';
        $ops[] = '1 0 0 1 58 724 Tm';
        $ops[] = '('.$this->e('PROPRIÉTAIRE').') Tj';
        $ops[] = '0 0 0 rg';
        $ops[] = '/F2 9 Tf';
        $ops[] = '1 0 0 1 58 702 Tm';
        $ops[] = '('.$this->e($data['proprietaire_nom']).') Tj';
        $ops[] = '/F1 8 Tf';
        $ops[] = '1 0 0 1 58 688 Tm';
        $ops[] = '('.$this->e($data['proprietaire_email']).') Tj';
        $ops[] = 'ET';

        // === LOCATAIRE BOX ===
        $ops[] = 'BT';
        $ops[] = '0.102 0.204 0.4 rg';
        $ops[] = '/F2 8 Tf';
        $ops[] = '1 0 0 1 318 724 Tm';
        $ops[] = '('.$this->e('LOCATAIRE').') Tj';
        $ops[] = '0 0 0 rg';
        $ops[] = '/F2 9 Tf';
        $ops[] = '1 0 0 1 318 702 Tm';
        $ops[] = '('.$this->e($data['locataire_nom']).') Tj';
        $ops[] = '/F1 8 Tf';
        $ops[] = '1 0 0 1 318 688 Tm';
        $ops[] = '('.$this->e($data['locataire_email']).') Tj';
        $ops[] = 'ET';

        // === AMOUNT BOX ===
        $ops[] = 'BT';
        $ops[] = '0.102 0.204 0.4 rg';
        $ops[] = '/F1 9 Tf';
        $ops[] = '1 0 0 1 58 594 Tm';
        $ops[] = '('.$this->e('MONTANT RÉGLÉ').') Tj';
        $ops[] = '/F2 26 Tf';
        $ops[] = '1 0 0 1 58 553 Tm';
        $ops[] = '('.$this->e($data['montant']).') Tj';
        $ops[] = 'ET';

        // === LOGEMENT SECTION ===
        $ops[] = 'BT';
        $ops[] = '0.102 0.204 0.4 rg';
        $ops[] = '/F2 9 Tf';
        $ops[] = '1 0 0 1 50 509 Tm';
        $ops[] = '('.$this->e('LOGEMENT').') Tj';
        $ops[] = '0 0 0 rg';
        $ops[] = '/F2 10 Tf';
        $ops[] = '1 0 0 1 50 487 Tm';
        $ops[] = '('.$this->e($data['bien_nom']).') Tj';
        $ops[] = '/F1 9 Tf';
        $ops[] = '1 0 0 1 50 472 Tm';
        $ops[] = '('.$this->e($data['bien_adresse']).') Tj';
        $ops[] = '1 0 0 1 50 458 Tm';
        $ops[] = '('.$this->e($data['bien_ville_pays']).') Tj';
        $ops[] = 'ET';

        // === REFERENCE PAIEMENT ===
        $ops[] = 'BT';
        $ops[] = '0.102 0.204 0.4 rg';
        $ops[] = '/F2 9 Tf';
        $ops[] = '1 0 0 1 50 442 Tm';
        $ops[] = '('.$this->e('RÉFÉRENCE PAIEMENT').') Tj';
        $ops[] = '0 0 0 rg';
        $ops[] = '/F1 9 Tf';
        $ops[] = '1 0 0 1 50 424 Tm';
        $ops[] = '('.$this->e('Référence : '.$data['reference']).') Tj';
        $ops[] = '1 0 0 1 50 410 Tm';
        $ops[] = '('.$this->e('Mode de paiement : '.$data['mode_paiement']).') Tj';
        $ops[] = 'ET';

        // === WARNING NOTICE ===
        $ops[] = 'BT';
        $ops[] = '0.4 0.28 0.0 rg';
        $ops[] = '/F2 8 Tf';
        $ops[] = '1 0 0 1 62 202 Tm';
        $ops[] = '('.$this->e('! AVERTISSEMENT').') Tj';
        $ops[] = '/F1 8 Tf';
        $ops[] = '1 0 0 1 62 188 Tm';
        $ops[] = '('.$this->e("Paiement simulé - aucune transaction réelle n'a été effectuée.").') Tj';
        $ops[] = '1 0 0 1 62 174 Tm';
        $ops[] = '('.$this->e("Ce document est généré à titre d'illustration uniquement.").') Tj';
        $ops[] = 'ET';

        // === PAGE FOOTER ===
        $ops[] = 'BT';
        $ops[] = '0.6 0.6 0.6 rg';
        $ops[] = '/F1 7 Tf';
        $ops[] = '1 0 0 1 50 128 Tm';
        $ops[] = '('.$this->e('Document généré le '.$data['date_generation'].' - Application de Gestion Locative').') Tj';
        $ops[] = 'ET';

        return implode("\n", $ops);
    }

    private function buildPdfDocument(string $contentStream): string
    {
        $objets = [
            1 => '<< /Type /Catalog /Pages 2 0 R >>',
            2 => '<< /Type /Pages /Kids [3 0 R] /Count 1 >>',
            3 => '<< /Type /Page /Parent 2 0 R /MediaBox [0 0 595 842] /Resources << /Font << /F1 4 0 R /F2 5 0 R >> >> /Contents 6 0 R >>',
            4 => '<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica /Encoding /WinAnsiEncoding >>',
            5 => '<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica-Bold /Encoding /WinAnsiEncoding >>',
            6 => sprintf("<< /Length %d >>\nstream\n%s\nendstream", strlen($contentStream), $contentStream),
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

    private function e(string $text): string
    {
        return $this->pdfEscape($this->encode($text));
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
