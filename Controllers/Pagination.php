<?php
/**
 * Pagination — utilitaire générique.
 * Usage:
 *   $p = new Pagination(count($items), 10);
 *   $page = $p->slice($items);
 *   echo $p->render(BASE_URL . '/contrats');
 */
class Pagination
{
    private int $total;
    private int $perPage;
    private int $current;
    private int $pages;

    public function __construct(int $total, int $perPage = 10, ?int $current = null)
    {
        $this->total   = max(0, $total);
        $this->perPage = max(1, $perPage);
        $this->pages   = (int) max(1, ceil($this->total / $this->perPage));
        $cur = $current ?? (int)($_GET['page'] ?? 1);
        $this->current = min(max(1, $cur), $this->pages);
    }

    public function offset(): int  { return ($this->current - 1) * $this->perPage; }
    public function limit(): int   { return $this->perPage; }
    public function current(): int { return $this->current; }
    public function pages(): int   { return $this->pages; }
    public function total(): int   { return $this->total; }

    /** Découpe un tableau déjà chargé en mémoire selon la page courante. */
    public function slice(array $items): array
    {
        return array_slice($items, $this->offset(), $this->perPage);
    }

    /** Génère un bloc HTML de navigation. */
    public function render(string $baseUrl, array $query = []): string
    {
        if ($this->pages <= 1) return '';
        $html = '<nav class="pagination">';
        for ($i = 1; $i <= $this->pages; $i++) {
            $q = array_merge($query, ['page' => $i]);
            $url = $baseUrl . '?' . http_build_query($q);
            $cls = $i === $this->current ? 'page active' : 'page';
            $html .= '<a class="' . $cls . '" href="' . htmlspecialchars($url) . '">' . $i . '</a>';
        }
        $html .= '</nav>';
        return $html;
    }
}
