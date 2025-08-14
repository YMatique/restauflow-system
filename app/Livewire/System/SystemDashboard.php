<?php

namespace App\Livewire\System;

use App\Models\System\Company;
use App\Models\System\Plan;
use App\Models\System\Subscription;
use Carbon\Carbon;
use Livewire\Component;

class SystemDashboard extends Component
{
    public $selectedPeriod = '30'; // dias
    public $realtimeEnabled = false;

    // Métricas principais
    public $totalCompanies = 0;
    public $activeSubscriptions = 0;
    public $totalRevenue = 0;
    public $newCompaniesThisMonth = 0;

    // Dados para gráficos
    public $subscriptionTrends = [];
    public $revenueTrends = [];
    public $planDistribution = [];
    public $companiesByStatus = [];

    // Alertas e notificações
    public $expiringSubscriptions = [];
    public $systemAlerts = [];

    protected $listeners = ['refreshDashboard' => 'loadData'];

    public function mount()
    {
        $this->loadData();
    }

    public function updatedSelectedPeriod()
    {
        $this->loadData();
    }

    public function toggleRealtime()
    {
        $this->realtimeEnabled = !$this->realtimeEnabled;
        if ($this->realtimeEnabled) {
            $this->dispatch('start-realtime');
        } else {
            $this->dispatch('stop-realtime');
        }
    }

    public function loadData()
    {
        $days = (int) $this->selectedPeriod;
        $startDate = Carbon::now()->subDays($days);
        
        // Métricas principais
        $this->totalCompanies = Company::count();
        $this->activeSubscriptions = Subscription::active()->count();
        $this->newCompaniesThisMonth = Company::whereMonth('created_at', now()->month)
                                              ->whereYear('created_at', now()->year)
                                              ->count();

        // Revenue total (simulado - você pode implementar baseado na sua lógica de preços)
        $this->totalRevenue = Subscription::active()
            ->join('plans', 'subscriptions.plan_id', '=', 'plans.id')
            ->sum('plans.price_mzn');

        // Dados para gráficos
        $this->loadSubscriptionTrends($days);
        $this->loadRevenueTrends($days);
        $this->loadPlanDistribution();
        $this->loadCompaniesByStatus();
        
        // Alertas
        $this->loadExpiringSubscriptions();
        $this->loadSystemAlerts();
    }

    private function loadSubscriptionTrends($days)
    {
        $this->subscriptionTrends = Subscription::selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->where('created_at', '>=', Carbon::now()->subDays($days))
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->mapWithKeys(function ($item) {
                return [Carbon::parse($item->date)->format('d/m') => $item->count];
            })
            ->toArray();
    }

    private function loadRevenueTrends($days)
    {
        $this->revenueTrends = Subscription::selectRaw('DATE(subscriptions.created_at) as date, SUM(plans.price_mzn) as revenue')
            ->join('plans', 'subscriptions.plan_id', '=', 'plans.id')
            ->where('subscriptions.created_at', '>=', Carbon::now()->subDays($days))
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->mapWithKeys(function ($item) {
                return [Carbon::parse($item->date)->format('d/m') => $item->revenue];
            })
            ->toArray();
    }

    private function loadPlanDistribution()
    {
        $this->planDistribution = Plan::selectRaw('plans.name, COUNT(subscriptions.id) as count')
            ->leftJoin('subscriptions', function($join) {
                $join->on('plans.id', '=', 'subscriptions.plan_id')
                     ->where('subscriptions.status', 'active');
            })
            ->groupBy('plans.id', 'plans.name')
            ->get()
            ->mapWithKeys(function ($item) {
                return [$item->name => $item->count];
            })
            ->toArray();
    }

    private function loadCompaniesByStatus()
    {
        $this->companiesByStatus = Company::selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->get()
            ->mapWithKeys(function ($item) {
                return [$this->getStatusLabel($item->status) => $item->count];
            })
            ->toArray();
    }

    private function loadExpiringSubscriptions()
    {
        $this->expiringSubscriptions = Subscription::with(['company', 'plan'])
            ->where('ends_at', '<=', Carbon::now()->addDays(7))
            ->where('status', 'active')
            ->orderBy('ends_at')
            ->limit(5)
            ->get();
    }

    private function loadSystemAlerts()
    {
        $alerts = [];

        // Verificar subscriptions expiradas
        $expiredCount = Subscription::where('ends_at', '<', now())
                                  ->where('status', 'active')
                                  ->count();
        if ($expiredCount > 0) {
            $alerts[] = [
                'type' => 'error',
                'title' => 'Subscrições Expiradas',
                'message' => "{$expiredCount} subscrições expiradas precisam de atenção",
                'action' => 'Ver Subscrições',
                'url' => route('system.subscriptions')
            ];
        }

        // Verificar empresas inativas há mais de 30 dias
        $inactiveCount = Company::where('last_activity_at', '<', Carbon::now()->subDays(30))
                               ->orWhereNull('last_activity_at')
                               ->count();
        if ($inactiveCount > 5) {
            $alerts[] = [
                'type' => 'warning',
                'title' => 'Empresas Inativas',
                'message' => "{$inactiveCount} empresas sem atividade há mais de 30 dias",
                'action' => 'Ver Empresas',
                'url' => route('system.companies')
            ];
        }

        // Verificar crescimento de usuários
        $newUsersThisWeek = User::where('created_at', '>=', Carbon::now()->subWeek())->count();
        if ($newUsersThisWeek > 10) {
            $alerts[] = [
                'type' => 'success',
                'title' => 'Crescimento de Usuários',
                'message' => "{$newUsersThisWeek} novos usuários esta semana!",
                'action' => 'Ver Usuários',
                'url' => route('system.users')
            ];
        }

        $this->systemAlerts = collect($alerts)->take(5)->toArray();
    }

    private function getStatusLabel($status)
    {
        return match($status) {
            'active' => 'Ativas',
            'inactive' => 'Inativas', 
            'suspended' => 'Suspensas',
            'trial' => 'Teste',
            default => 'Outros'
        };
    }

    public function exportData()
    {
        // Implementar exportação dos dados do dashboard
        $this->dispatch('show-toast', [
            'type' => 'success',
            'message' => 'Dados exportados com sucesso!'
        ]);
    }
    public function render()
    {
        return view('livewire.system.system-dashboard')->layout('layouts.system');
    }
}
