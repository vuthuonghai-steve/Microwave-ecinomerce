<?php

namespace App\Services;

use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Facades\DB;

class ReportService
{
    public function revenue(string $range = 'monthly', ?string $from = null, ?string $to = null): array
    {
        [$fromDate, $toDate] = $this->resolveRange($range, $from, $to);

        if ($range === 'daily') {
            $labelFormat = 'Y-m-d';
            $groupExpr = DB::raw('DATE(orders.created_at) as period');
            $orderBy = 'period';
        } elseif ($range === 'weekly') {
            $labelFormat = 'o-\WW'; // ISO week
            $groupExpr = DB::raw("YEARWEEK(orders.created_at, 3) as period");
            $orderBy = 'period';
        } else { // monthly
            $labelFormat = 'Y-m';
            $groupExpr = DB::raw("DATE_FORMAT(orders.created_at, '%Y-%m') as period");
            $orderBy = 'period';
        }

        $rows = DB::table('orders')
            ->select($groupExpr, DB::raw('SUM(grand_total) as revenue'), DB::raw('COUNT(*) as orders'))
            ->where('status', 'delivered')
            ->whereBetween('created_at', [$fromDate->startOfDay(), $toDate->endOfDay()])
            ->groupBy('period')
            ->orderBy($orderBy)
            ->get();

        // Fill missing periods
        $labels = [];
        $rev = [];
        $ords = [];
        if ($range === 'daily') {
            $period = CarbonPeriod::create($fromDate->copy()->startOfDay(), '1 day', $toDate->copy()->startOfDay());
            $map = $rows->keyBy('period');
            foreach ($period as $d) {
                $key = $d->format('Y-m-d');
                $labels[] = $d->format($labelFormat);
                $rev[] = (float) ($map[$key]->revenue ?? 0);
                $ords[] = (int) ($map[$key]->orders ?? 0);
            }
        } elseif ($range === 'weekly') {
            // approximate weekly stepping
            $map = $rows->keyBy('period');
            $cursor = $fromDate->copy()->startOfWeek();
            $end = $toDate->copy()->startOfWeek();
            while ($cursor <= $end) {
                $yearWeek = (int) $cursor->format('oW');
                $labels[] = $cursor->format($labelFormat);
                $rev[] = (float) ($map[$yearWeek]->revenue ?? 0);
                $ords[] = (int) ($map[$yearWeek]->orders ?? 0);
                $cursor->addWeek();
            }
        } else { // monthly
            $map = $rows->keyBy('period');
            $cursor = $fromDate->copy()->startOfMonth();
            $end = $toDate->copy()->startOfMonth();
            while ($cursor <= $end) {
                $key = $cursor->format('Y-m');
                $labels[] = $cursor->format($labelFormat);
                $rev[] = (float) ($map[$key]->revenue ?? 0);
                $ords[] = (int) ($map[$key]->orders ?? 0);
                $cursor->addMonth();
            }
        }

        return [
            'labels' => $labels,
            'revenue' => $rev,
            'orders' => $ords,
        ];
    }

    public function bestSelling(string $range = 'monthly', ?string $from = null, ?string $to = null, int $limit = 10): array
    {
        [$fromDate, $toDate] = $this->resolveRange($range, $from, $to);

        $rows = DB::table('order_items')
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->join('products', 'products.id', '=', 'order_items.product_id')
            ->select('products.name', DB::raw('SUM(order_items.quantity) as sold'))
            ->where('orders.status', 'delivered')
            ->whereBetween('orders.created_at', [$fromDate->startOfDay(), $toDate->endOfDay()])
            ->groupBy('order_items.product_id', 'products.name')
            ->orderByDesc(DB::raw('sold'))
            ->limit($limit)
            ->get();

        return [
            'labels' => $rows->pluck('name')->all(),
            'sold' => $rows->pluck('sold')->map(fn($v) => (int)$v)->all(),
            'top_products' => $rows->map(fn($r) => ['name' => $r->name, 'sold' => (int) $r->sold])->all(),
        ];
    }

    public function exportCsv(string $type, array $data): string
    {
        $fh = fopen('php://temp', 'w+');
        if ($type === 'revenue') {
            fputcsv($fh, ['Label', 'Revenue', 'Orders']);
            foreach ($data['labels'] as $i => $label) {
                fputcsv($fh, [$label, $data['revenue'][$i] ?? 0, $data['orders'][$i] ?? 0]);
            }
        } elseif ($type === 'best-selling') {
            fputcsv($fh, ['Product', 'Sold']);
            foreach ($data['top_products'] as $p) {
                fputcsv($fh, [$p['name'], $p['sold']]);
            }
        }
        rewind($fh);
        return stream_get_contents($fh) ?: '';
    }

    private function resolveRange(string $range, ?string $from, ?string $to): array
    {
        $now = Carbon::now();
        if ($from && $to) {
            return [Carbon::parse($from), Carbon::parse($to)];
        }
        if ($range === 'daily') {
            return [$now->copy()->subDays(29), $now];
        } elseif ($range === 'weekly') {
            return [$now->copy()->subWeeks(11)->startOfWeek(), $now];
        }
        return [$now->copy()->subMonths(11)->startOfMonth(), $now];
    }
}

