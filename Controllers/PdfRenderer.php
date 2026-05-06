<?php
/**
 * PdfRenderer — générateur PDF 100% PHP natif, mise en page pro.
 *
 * Fonctionnalités :
 *   - Bandeau d'en-tête coloré (titre + sous-titre + marque)
 *   - Pied de page avec pagination "Page X / Y" et date
 *   - Sections avec barre verticale colorée
 *   - Blocs info (clé / valeur) en deux colonnes alternées
 *   - Blocs règles numérotés avec encart
 *   - Bloc signature
 *
 * Limites : police Helvetica WinAnsi (pas d'unicode complet — texte translit en latin-1).
 */
class PdfRenderer {
    /** @var array<int,array<int,string>> contenu de chaque page (opérateurs PDF) */
    private array $pages = [];
    private array $current = [];
    private float $y;

    private float $pageWidth    = 595;   // A4 pts
    private float $pageHeight   = 842;
    private float $marginLeft   = 50;
    private float $marginRight  = 50;
    private float $marginTop    = 110;   // espace réservé au header
    private float $marginBottom = 60;    // espace réservé au footer
    private float $contentWidth;
    private float $contentTop;           // y de départ d'une page

    // Charte graphique (violet sombre comme l'app)
    private array $colorBrand   = [0.42, 0.20, 0.78]; // violet
    private array $colorAccent  = [0.30, 0.85, 0.55]; // vert menthe
    private array $colorText    = [0.13, 0.13, 0.18];
    private array $colorMuted   = [0.45, 0.45, 0.55];
    private array $colorRule    = [0.85, 0.85, 0.92];
    private array $colorBg      = [0.97, 0.96, 1.00];

    private string $docTitle    = 'Document';
    private string $docSubtitle = '';
    private string $brand       = 'CreatorSpace';

    public function __construct() {
        $this->contentWidth = $this->pageWidth - $this->marginLeft - $this->marginRight;
        $this->contentTop   = $this->pageHeight - $this->marginTop;
        $this->y            = $this->contentTop;
    }

    /* ─────── Méta ─────── */

    public function setTitle(string $title, string $subtitle = ''): void {
        $this->docTitle    = $title;
        $this->docSubtitle = $subtitle;
    }

    public function setBrand(string $brand): void { $this->brand = $brand; }

    /* ─────── Layout primitives ─────── */

    private function newPage(): void {
        $this->pages[] = $this->current;
        $this->current = [];
        $this->y = $this->contentTop;
    }

    private function ensureSpace(float $needed): void {
        if ($this->y - $needed < $this->marginBottom + 30) $this->newPage();
    }

    private function op(string $s): void { $this->current[] = $s; }

    private function fillColor(array $c): string { return sprintf("%.3f %.3f %.3f rg ", $c[0], $c[1], $c[2]); }
    private function strokeColor(array $c): string { return sprintf("%.3f %.3f %.3f RG ", $c[0], $c[1], $c[2]); }

    private function rect(float $x, float $y, float $w, float $h, array $fill = null, array $stroke = null): string {
        $s = '';
        if ($fill)   $s .= $this->fillColor($fill);
        if ($stroke) $s .= $this->strokeColor($stroke);
        $s .= sprintf("%.2f %.2f %.2f %.2f re ", $x, $y, $w, $h);
        if ($fill && $stroke) $s .= "B\n";
        elseif ($fill)        $s .= "f\n";
        elseif ($stroke)      $s .= "S\n";
        return $s;
    }

    private function line(float $x1, float $y1, float $x2, float $y2, array $color, float $width = 0.6): string {
        return sprintf(
            "%s%.2f w %.2f %.2f m %.2f %.2f l S\n",
            $this->strokeColor($color), $width, $x1, $y1, $x2, $y2
        );
    }

    private function text(string $str, float $x, float $y, int $size = 11, bool $bold = false, array $color = null): string {
        $color = $color ?? $this->colorText;
        $font  = $bold ? '/F2' : '/F1';
        return sprintf(
            "%sBT %s %d Tf %.2f %.2f Td (%s) Tj ET\n",
            $this->fillColor($color), $font, $size, $x, $y, $this->escape($str)
        );
    }

    /* ─────── API publique ─────── */

    /** Compat ancienne API */
    public function title(string $t): void { $this->setTitle($t); }

    public function h2(string $text): void {
        $this->ensureSpace(34);
        $this->y -= 6;
        // barre verticale colorée
        $this->op($this->rect($this->marginLeft, $this->y - 4, 3, 18, $this->colorBrand));
        $this->op($this->text($text, $this->marginLeft + 12, $this->y, 13, true));
        $this->y -= 22;
        // ligne fine sous le titre
        $this->op($this->line(
            $this->marginLeft, $this->y + 4,
            $this->pageWidth - $this->marginRight, $this->y + 4,
            $this->colorRule, 0.5
        ));
        $this->y -= 6;
    }

    public function p(string $text, int $size = 11, bool $muted = false): void {
        $color = $muted ? $this->colorMuted : $this->colorText;
        foreach ($this->wrap($text, $size, $this->contentWidth) as $line) {
            $this->ensureSpace(16);
            $this->op($this->text($line, $this->marginLeft, $this->y, $size, false, $color));
            $this->y -= $size + 4;
        }
        $this->y -= 3;
    }

    public function li(string $text): void {
        $bullet = '• ';
        $indent = 14;
        $lines  = $this->wrap($text, 11, $this->contentWidth - $indent);
        foreach ($lines as $i => $line) {
            $this->ensureSpace(16);
            if ($i === 0) {
                $this->op($this->text($bullet, $this->marginLeft, $this->y, 11, false, $this->colorBrand));
            }
            $this->op($this->text($line, $this->marginLeft + $indent, $this->y, 11));
            $this->y -= 14;
        }
    }

    public function spacer(float $pts = 12): void {
        $this->y -= $pts;
        if ($this->y < $this->marginBottom + 30) $this->newPage();
    }

    /** Encart 2 colonnes : libellé / valeur — alternance de fond pour lisibilité */
    public function infoTable(array $rows): void {
        $rowH    = 22;
        $labelW  = 130;
        $alt     = false;
        $this->ensureSpace(count($rows) * $rowH + 10);

        $startY = $this->y;
        // Cadre arrondi simulé (rectangle simple)
        $totalH = count($rows) * $rowH;
        $this->op($this->rect(
            $this->marginLeft, $this->y - $totalH, $this->contentWidth, $totalH,
            null, $this->colorRule
        ));

        foreach ($rows as $label => $value) {
            $rowY = $this->y - $rowH;
            if ($alt) {
                $this->op($this->rect($this->marginLeft + 0.5, $rowY + 0.5, $this->contentWidth - 1, $rowH - 1, $this->colorBg));
            }
            $this->op($this->text((string)$label, $this->marginLeft + 10, $rowY + 7, 10, true, $this->colorMuted));
            $valLines = $this->wrap((string)$value, 11, $this->contentWidth - $labelW - 20);
            $this->op($this->text($valLines[0] ?? '—', $this->marginLeft + $labelW, $rowY + 7, 11, false, $this->colorText));
            $this->y -= $rowH;
            $alt = !$alt;
        }
        $this->y -= 8;
    }

    /** Bloc règle numéroté avec encart */
    public function ruleBlock(int $num, string $titre, string $description = ''): void {
        $padX     = 12;
        $padY     = 10;
        $titleH   = 18;
        $descLines = $description !== '' ? $this->wrap($description, 10, $this->contentWidth - $padX * 2 - 30) : [];
        $blockH   = $titleH + $padY * 2 + count($descLines) * 13;

        $this->ensureSpace($blockH + 8);

        // Fond
        $this->op($this->rect(
            $this->marginLeft, $this->y - $blockH, $this->contentWidth, $blockH,
            $this->colorBg, $this->colorRule
        ));
        // Pastille numéro
        $badgeR = 11;
        $bx = $this->marginLeft + 18;
        $by = $this->y - $padY - 8;
        $this->op($this->rect($bx - $badgeR, $by - $badgeR + 4, $badgeR * 2, $badgeR * 2, $this->colorBrand));
        $this->op($this->text(str_pad((string)$num, 2, '0', STR_PAD_LEFT), $bx - 7, $by - 3, 10, true, [1,1,1]));

        // Titre
        $this->op($this->text($titre, $this->marginLeft + 44, $this->y - $padY - 4, 11, true));

        // Description
        $dy = $this->y - $padY - $titleH - 6;
        foreach ($descLines as $line) {
            $this->op($this->text($line, $this->marginLeft + 44, $dy, 10, false, $this->colorMuted));
            $dy -= 13;
        }
        $this->y -= $blockH + 8;
    }

    public function signatureBlock(string $nom, ?string $signature = null): void {
        $this->ensureSpace(90);
        $this->y -= 10;
        $boxW = 240;
        $boxH = 70;
        $x = $this->pageWidth - $this->marginRight - $boxW;
        $y = $this->y - $boxH;
        $this->op($this->rect($x, $y, $boxW, $boxH, null, $this->colorRule));
        $this->op($this->text('Signature', $x + 12, $y + $boxH - 16, 9, true, $this->colorMuted));
        if ($signature) {
            $this->op($this->text($signature, $x + 12, $y + 28, 14, true, $this->colorBrand));
        }
        $this->op($this->text($nom, $x + 12, $y + 10, 10, false, $this->colorText));
        $this->y -= $boxH + 10;
    }

    /* ─────── Wrapping ─────── */

    private function wrap(string $text, int $fontSize, float $maxWidth): array {
        $charWidth = $fontSize * 0.5;   // approximation Helvetica
        $maxChars  = max(20, (int) floor($maxWidth / $charWidth));
        $lines = [];
        foreach (preg_split('/\R/', $text) as $paragraph) {
            $words = explode(' ', $paragraph);
            $line = '';
            foreach ($words as $w) {
                if (mb_strlen($line . ' ' . $w) > $maxChars) {
                    if ($line !== '') $lines[] = $line;
                    $line = $w;
                } else {
                    $line = $line === '' ? $w : $line . ' ' . $w;
                }
            }
            if ($line !== '') $lines[] = $line;
        }
        return $lines !== [] ? $lines : [''];
    }

    private function escape(string $s): string {
        $s = @iconv('UTF-8', 'ISO-8859-1//TRANSLIT//IGNORE', $s) ?: $s;
        return str_replace(['\\', '(', ')'], ['\\\\', '\\(', '\\)'], $s);
    }

    /* ─────── Header / Footer (rendus à l'output) ─────── */

    private function renderHeader(int $pageIndex): array {
        $ops = [];
        $bandH = 70;
        $y = $this->pageHeight - $bandH;
        // Bandeau brand
        $ops[] = $this->rect(0, $y, $this->pageWidth, $bandH, $this->colorBrand);
        // Accent bar
        $ops[] = $this->rect(0, $y - 4, $this->pageWidth, 4, $this->colorAccent);
        // Brand
        $ops[] = $this->text($this->brand, $this->marginLeft, $this->pageHeight - 28, 11, true, [1,1,1]);
        // Titre doc
        $ops[] = $this->text($this->docTitle, $this->marginLeft, $this->pageHeight - 48, 16, true, [1,1,1]);
        if ($this->docSubtitle !== '') {
            $ops[] = $this->text($this->docSubtitle, $this->marginLeft, $this->pageHeight - 64, 10, false, [0.92,0.92,1]);
        }
        return $ops;
    }

    private function renderFooter(int $pageIndex, int $pageCount): array {
        $ops = [];
        $y = 40;
        $ops[] = $this->line(
            $this->marginLeft, $y + 14,
            $this->pageWidth - $this->marginRight, $y + 14,
            $this->colorRule, 0.5
        );
        $left  = $this->brand . ' — ' . date('d/m/Y');
        $right = 'Page ' . ($pageIndex + 1) . ' / ' . $pageCount;
        $ops[] = $this->text($left, $this->marginLeft, $y, 9, false, $this->colorMuted);
        // align right approx
        $rw = strlen($right) * 5;
        $ops[] = $this->text($right, $this->pageWidth - $this->marginRight - $rw, $y, 9, false, $this->colorMuted);
        return $ops;
    }

    /* ─────── Sortie PDF ─────── */

    public function output(): string {
        if (!empty($this->current)) {
            $this->pages[] = $this->current;
            $this->current = [];
        }
        if (empty($this->pages)) $this->pages[] = [];

        $total = count($this->pages);
        // Injecter header + footer dans chaque page
        $finalPages = [];
        foreach ($this->pages as $i => $ops) {
            $hdr = $this->renderHeader($i);
            $ftr = $this->renderFooter($i, $total);
            $finalPages[] = array_merge($hdr, $ops, $ftr);
        }

        $objects = [];
        $offsets = [];
        $pageObjIds = [];
        $nextId = 5;
        $pageIdsList = [];

        foreach ($finalPages as $pageOps) {
            $pageObjId    = $nextId++;
            $contentObjId = $nextId++;
            $pageIdsList[] = $pageObjId;
            $stream = implode('', $pageOps);
            $objects[$contentObjId] = "<< /Length " . strlen($stream) . " >>\nstream\n" . $stream . "endstream";
            $objects[$pageObjId]    = "<< /Type /Page /Parent 2 0 R /MediaBox [0 0 {$this->pageWidth} {$this->pageHeight}] "
                                    . "/Resources << /Font << /F1 3 0 R /F2 4 0 R >> >> /Contents $contentObjId 0 R >>";
        }

        $kids = implode(' ', array_map(fn($id) => "$id 0 R", $pageIdsList));
        $objects[1] = "<< /Type /Catalog /Pages 2 0 R >>";
        $objects[2] = "<< /Type /Pages /Kids [$kids] /Count " . count($pageIdsList) . " >>";
        $objects[3] = "<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica /Encoding /WinAnsiEncoding >>";
        $objects[4] = "<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica-Bold /Encoding /WinAnsiEncoding >>";

        ksort($objects);

        $pdf = "%PDF-1.4\n%\xE2\xE3\xCF\xD3\n";
        foreach ($objects as $id => $body) {
            $offsets[$id] = strlen($pdf);
            $pdf .= "$id 0 obj\n$body\nendobj\n";
        }
        $xrefPos = strlen($pdf);
        $count = max(array_keys($objects)) + 1;
        $pdf .= "xref\n0 $count\n0000000000 65535 f \n";
        for ($i = 1; $i < $count; $i++) {
            $off = $offsets[$i] ?? 0;
            $pdf .= sprintf("%010d 00000 n \n", $off);
        }
        $pdf .= "trailer\n<< /Size $count /Root 1 0 R >>\nstartxref\n$xrefPos\n%%EOF";
        return $pdf;
    }

    public function stream(string $filename = 'document.pdf'): void {
        $bin = $this->output();
        header('Content-Type: application/pdf');
        header('Content-Length: ' . strlen($bin));
        header('Content-Disposition: inline; filename="' . $filename . '"');
        echo $bin;
    }

    public function download(string $filename = 'document.pdf'): void {
        $bin = $this->output();
        header('Content-Type: application/pdf');
        header('Content-Length: ' . strlen($bin));
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        echo $bin;
    }
}
