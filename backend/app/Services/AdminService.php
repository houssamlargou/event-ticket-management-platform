<?php 

namespace App\Services;

use App\Models\User;
use App\Models\Event;
use App\Models\Order;

class AdminService {
    public function getDashboard():array {
        $totalUsers = User::count();
        $totalEvents = Event::count();

        $totalTicketsSold = Order::where('payment_status', 'paid')
                            ->sum('quantity');
        
        $totalRevenue = Order::where('payment_status', 'paid')
                            ->sum('total_price');

        $approvedEvents = Event::where('status', 'approved')->count();

        $pendingEvents = Event::where('status', 'pending')->count();

        $rejectedEvents = Event::where('status', 'rejected')->count();

        return [
            'success' => true,
            'status' => 200,
            'data' => [
                'users' => $totalUsers,
                'events' => $totalEvents,
                'tickets_sold' => $totalTicketsSold,
                'revenue' => $totalRevenue,
                'events_status' => [
                    'approved' => $approvedEvents,
                    'pending' => $pendingEvents,
                    'rejected' => $rejectedEvents,
                ],
            ],
        ];
    }
}