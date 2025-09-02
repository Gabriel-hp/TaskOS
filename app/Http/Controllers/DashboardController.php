<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Evento;
use App\Models\User;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = $this->getEventStats();
        $userStats = $this->getUserStats();
        $recentEvents = $this->getRecentEvents();

        return view('dashboard.index', compact('stats', 'userStats', 'recentEvents'));
    }

    private function getEventStats()
    {
        $now = Carbon::now();
        $thisMonth = $now->month;
        $thisYear = $now->year;
        $startOfWeek = $now->startOfWeek();
        $endOfWeek = $now->copy()->endOfWeek();

        return [
            'total' => Evento::count(),
            'agendados' => Evento::where('status', 'agendado')->count(),
            'feitos' => Evento::where('status', 'feito')->count(),
            'reagendados' => Evento::where('status', 'reagendado')->count(),
            'cancelados' => Evento::where('status', 'cancelado')->count(),
            'thisMonth' => Evento::whereMonth('start', $thisMonth)
                                ->whereYear('start', $thisYear)
                                ->count(),
            'thisWeek' => Evento::whereBetween('start', [$startOfWeek, $endOfWeek])
                               ->count(),
        ];
    }

   private function getUserStats()
{
    $users = User::all();

    return $users->map(function ($user) {
        $totalEvents = Evento::where('responsavel_id', $user->id)->count();
        $completedEvents = Evento::where('responsavel_id', $user->id)
                                 ->where('status', 'feito')
                                 ->count();

        return [
            'user' => $user,
            'totalEvents' => $totalEvents,
            'completedEvents' => $completedEvents,
            'completionRate' => $totalEvents > 0 ? round(($completedEvents / $totalEvents) * 100) : 0
        ];
    });
}


    private function getRecentEvents()
    {
        return Evento::with('responsavel')
                    ->orderBy('created_at', 'desc')
                    ->limit(5)
                    ->get();
    }
}
