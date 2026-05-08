<?php
require_once ROOT . '/Controllers/BaseController.php';
require_once ROOT . '/Models/UserModel.php';
require_once ROOT . '/Models/RequestModel.php';
require_once ROOT . '/Models/HistoryModel.php';
require_once ROOT . '/Models/ContratModel.php';
require_once ROOT . '/Controllers/WeatherController.php';

class DashboardController extends Controller {

    /** Endpoint JSON consommé par Chart.js (contrats par mois). */
    public function chartData(): void {
        $this->requireAuth();
        $contrats = new ContratModel();
        $rows = $contrats->getAll();
        $byMonth = [];
        foreach ($rows as $r) {
            $m = substr($r['created_at'] ?? '', 0, 7);
            if ($m === '') continue;
            $byMonth[$m] = ($byMonth[$m] ?? 0) + 1;
        }
        ksort($byMonth);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode([
            'labels' => array_keys($byMonth),
            'data'   => array_values($byMonth),
        ]);
        exit;
    }

    public function index(): void {
        $this->requireAuth();
        $users    = new UserModel();
        $requests = new RequestModel();
        $history  = new HistoryModel();

        $stats = [
            'total_users'       => $users->countAll(),
            'active_users'      => $users->countByStatus('active'),
            'total_requests'    => $requests->countAll(),
            'pending_requests'  => $requests->countByStatus('pending'),
            'approved_requests' => $requests->countByStatus('approved'),
            'rejected_requests' => $requests->countByStatus('rejected'),
        ];

        $recent_logs     = $history->getAll(10);
        $recent_requests = $requests->getAll();
        $recent_requests = array_slice($recent_requests, 0, 5);

        $contratModel = new ContratModel();
        $contrats = $contratModel->getAll();

        // Contrats par mois (12 derniers mois)
                $contratParMois = $contratModel->getContratsParMoisLastYear();

        // Répartition par type
                $repartitionType = $contratModel->getRepartitionType();

        // Répartition par statut
                $repartitionStatut = $contratModel->getRepartitionStatut();

        // Rules par contrat (top 5)
                $topContrats = $contratModel->getTopContratsByRules(5);

        // Taux de complétion
        $totalContrats = count($contrats);
                $contratsAvecRules = $contratModel->countContratsAvecRules();
        $tauxCompletion = $totalContrats > 0 
            ? round(($contratsAvecRules / $totalContrats) * 100) 
            : 0;

        // Météo (API externe Open-Meteo) — non bloquant
        $weather = null;
        try {
            $w = new WeatherController();
            $ref = new ReflectionMethod($w, 'fetchWeather');
            $ref->setAccessible(true);
            $weather = $ref->invoke($w, WEATHER_CITY);
        } catch (\Throwable $e) { /* ignore */ }

        $this->render('dashboard/index', compact(
            'stats',
            'recent_logs',
            'recent_requests',
            'weather',
            'contratParMois',
            'repartitionType',
            'repartitionStatut',
            'topContrats',
            'totalContrats',
            'contratsAvecRules',
            'tauxCompletion'
        ));
    }
}

