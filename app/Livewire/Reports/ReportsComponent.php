<?php

namespace App\Livewire\Reports;

use App\Models\Client;
use App\Models\Product;
use App\Models\Sale;
use Livewire\Component;

class ReportsComponent extends Component
{
     protected string $layout = 'layouts.app';
    
    public $period = 'week';
    public $startDate = null;
    public $endDate = null;
    
    public function mount()
    {
        $this->updateDateRange();
    }
    
    public function updatedPeriod()
    {
        $this->updateDateRange();
    }
    
    private function updateDateRange()
    {
        match($this->period) {
            'today' => [
                $this->startDate = today(),
                $this->endDate = today()
            ],
            'week' => [
                $this->startDate = now()->subWeek(),
                $this->endDate = now()
            ],
            'month' => [
                $this->startDate = now()->subMonth(),
                $this->endDate = now()
            ],
            'quarter' => [
                $this->startDate = now()->subQuarter(),
                $this->endDate = now()
            ],
            'year' => [
                $this->startDate = now()->subYear(),
                $this->endDate = now()
            ],
            default => null
        };
    }
    
    public function applyCustomPeriod()
    {
        $this->validate([
            'startDate' => 'required|date',
            'endDate' => 'required|date|after_or_equal:startDate'
        ]);
        
        $this->period = 'custom';
    }
    
    private function getSalesQuery()
    {
        return Sale::byCompany(auth()->user()->company_id)
            ->completed()
            ->when($this->startDate && $this->endDate, function($query) {
                $query->whereBetween('completed_at', [
                    $this->startDate,
                    $this->endDate
                ]);
            });
    }
    
    public function render()
    {
          $salesQuery = $this->getSalesQuery();
        
        // Quick Stats
        $stats = [
            'total_revenue' => $salesQuery->sum('total'),
            'total_orders' => $salesQuery->count(),
            'unique_customers' => $salesQuery->distinct('client_id')->count('client_id'),
            'average_ticket' => $salesQuery->avg('total')
        ];
        
        // Daily Revenue Chart Data
        $dailyRevenue = $salesQuery->selectRaw('DATE(completed_at) as date, SUM(total) as revenue')
            ->groupBy('date')
            ->orderBy('date')
            ->get();
        
        // Top Products
        $topProducts = Product::byCompany(auth()->user()->company_id)
            ->withCount(['saleItems' => function($query) {
                $query->whereHas('sale', function($saleQuery) {
                    $saleQuery->byCompany(auth()->user()->company_id)
                        ->completed()
                        ->when($this->startDate && $this->endDate, function($q) {
                            $q->whereBetween('completed_at', [
                                $this->startDate,
                                $this->endDate
                            ]);
                        });
                });
            }])
            ->withSum(['saleItems' => function($query) {
                $query->whereHas('sale', function($saleQuery) {
                    $saleQuery->byCompany(auth()->user()->company_id)
                        ->completed()
                        ->when($this->startDate && $this->endDate, function($q) {
                            $q->whereBetween('completed_at', [
                                $this->startDate,
                                $this->endDate
                            ]);
                        });
                });
            }], 'total_price')
            ->orderByDesc('sale_items_sum_total_price')
            ->limit(10)
            ->get();
        
        // Top Customers
        $topCustomers = Client::byCompany(auth()->user()->company_id)
            ->withCount(['sales' => function($query) {
                $query->completed()
                    ->when($this->startDate && $this->endDate, function($q) {
                        $q->whereBetween('completed_at', [
                            $this->startDate,
                            $this->endDate
                        ]);
                    });
            }])
            ->withSum(['sales' => function($query) {
                $query->completed()
                    ->when($this->startDate && $this->endDate, function($q) {
                        $q->whereBetween('completed_at', [
                            $this->startDate,
                            $this->endDate
                        ]);
                    });
            }], 'total')
            ->orderByDesc('sales_sum_total')
            ->limit(10)
            ->get();

        return view('livewire.reports.reports-component', [
            'stats' => $stats,
            'dailyRevenue' => $dailyRevenue,
            'topProducts' => $topProducts,
            'topCustomers' => $topCustomers,
            'title' => 'Relatórios de Performance',
            'breadcrumb' => 'Dashboard > Relatórios'
        ]);
    }
}
